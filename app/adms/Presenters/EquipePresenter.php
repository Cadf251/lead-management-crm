<?php

namespace App\adms\Presenters;

use App\adms\Helpers\CreateOptions;
use App\adms\Models\teams\Team;
use App\adms\Models\teams\TeamUser;
use App\adms\Models\teams\TeamUserFunction;
use App\adms\UI\Badge;
use App\adms\UI\Button;
use App\adms\UI\Field;
use App\adms\UI\InfoBox;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class EquipePresenter
{
  public static function present(array $equipes, array $funcoes = []): array
  {
    $final = [];
    /** @var Team $equipe */
    foreach ($equipes as $equipe) {
      $final[] = [
        "id" => $equipe->getId() ?? "",
        "nome" => $equipe->getName() ?? "Sem nome",
        "descricao" => $equipe->getDescription() ?? "",
        "status_badge" => self::getStatusBadge($equipe),
        "numero_badge" => self::getNumeroBadge($equipe),
        "buttons" => self::buttons($equipe->getId(), $equipe->getName(), $equipe->getStatusId()),
        "proximos" => self::proximos($equipe),
        "fila" => self::fila($equipe),
        "colaboradores" => self::colaboradores($equipe),
      ];
    }
    
    return $final;
  }

  private static function getStatusBadge(Team $equipe)
  {
    $class = UtilPresenter::getStatusClass($equipe->getStatusId());
    return Badge::create($equipe->getStatusName(), $class);
  }

  private static function getNumeroBadge(Team $equipe)
  {
    $count = $equipe->countUsers();

    return Badge::create(
      <<<HTML
      <i class="fa-solid fa-user"></i> <span class="js--number-badge">$count</span>
      HTML,
      "blue");
  }

  private static function buttons(int $equipeId, string $equipeNome, int $statusId)
  {
    $btns = [
      "colaboradores" =>
      Button::create()
        ->color("green")
        ->withIcon("user")
        ->link("colaboradores/$equipeId")
        ->tooltip("Listar Colaboradores"),

      "editar" =>
      Button::create("")
        ->color("blue")
        ->data([
          "action" => "action:core",
          "url" => "equipes/editar/$equipeId",
          "action-type" => "overlay"
        ])
        ->tooltip("Editar equipe")
        ->withIcon("pencil"),

      "pausar" =>
      Button::create("")
        ->color("gray")
        ->data([
          "action" => "action:core",
          "url" => "equipes/pausar/$equipeId",
          "target" => ".card--$equipeId"
        ])
        ->tooltip("Pausar equipe")
        ->withIcon("pause"),

      "ativar" =>
      Button::create("")
        ->color("green")
        ->data([
          "action" => "action:core",
          "url" => "equipes/ativar/$equipeId",
          "target" => ".card--$equipeId"
        ])
        ->tooltip("Ativar equipe")
        ->withIcon("play"),

      "desativar" =>
      Button::create("")
        ->color("red")
        ->data([
          "action" => "action:core",
          "url" => "equipes/desativar/$equipeId",
          "remove" => ".card--$equipeId",
          "confirm" => true,
          "confirm-title" => "Deseja excluir a equipe $equipeNome?",
          "confirm-text" => "Essa ação é irreversível.",
        ])
        ->tooltip("Desativar equipe")
        ->withIcon("trash-can"),

      "nova-oferta" => 
        Button::create("")
          ->color("green")
          ->data([
            "action" => "floating:offer-team"
          ])
          ->tooltip("Atribuir oferta")
          ->withIcon("tag")
    ];

    return match ($statusId) {
      1 => [
        []
      ],
      2 => [
        $btns["colaboradores"],
        $btns["nova-oferta"],
        $btns["editar"],
        $btns["ativar"],
        $btns["desativar"],
      ],
      3 => [
        $btns["colaboradores"],
        $btns["nova-oferta"],
        $btns["editar"],
        $btns["pausar"],
        $btns["desativar"],
      ]
    };
  }

  private static function proximos(Team $equipe)
  {
    $proximos = $equipe->getNexts();

    if (empty($proximos)) return null;

    $simplified = [];

    /** @var TeamUser $proximo */
    foreach ($proximos as $proximo) {
      $simplified[] = [
        "id" => $proximo->getId(),
        "nome" => $proximo->getUserName(),
        "vez" => $proximo->getTime()
      ];
    }

    return $simplified;
  }

  private static function proximosResumo(?array $simplified)
  {
    if ($simplified === null){
      return null;
    }
    $mapa = [];

    foreach ($simplified as $index => $item) {
      $id = $item['id'];

      $mapa[$id]['nome'] ??= $item['nome'];
      $mapa[$id]['posicoes'][] = $index + 1;
    }

    $frases = [];
    foreach ($mapa as $pessoa) {
      $nome = $pessoa['nome'];
      $pos = $pessoa['posicoes'];

      if ($pos === [1, 2, 3]) {
        $frases[] = "$nome (Recebe os 3 próximos)";
        continue;
      }

      if (count($pos) === 1) {
        $frases[] = "$nome (Recebe o " . self::ordinal($pos[0]) . ")";
        continue;
      }

      $ultimo = array_pop($pos);

      $frase = "$nome (Recebe o ";
      $m = [];

      foreach($pos as $row) {
        $m[] = self::ordinal($row);
      }
      $frase .= implode(", ", $m);

      $frase .= " e " . self::ordinal($ultimo) . ")";
      
      $frases[] = $frase;
    }

    return $frases;
  }

  private static function ordinal(int $pos)
  {
    return ['primeiro', 'segundo', 'terceiro'][$pos - 1];
  }

  public static function fila(Team $equipe): array
  {
    $recebem = $equipe->getAbleUsers();

    $proximos = self::proximos($equipe);

    $frases = self::proximosResumo($proximos);

    if ($frases === null) {
      $content = null;
    } else {
      $content = implode(", ", $frases);
    }

    if (empty($recebem)) {
      return [
        "badge" =>
        Badge::create("Fila inativa", "red")
          ->tooltip("Nenhum usuário pode receber leads nessa equipe."),

        "infobox" =>
        InfoBox::create("Fila inativa", "Nenhum usuário está habilitado a receber leads")
          ->setType(InfoBox::TYPE_ALERT)
          ->setContent($content)
      ];
    } else if (count($recebem) < 3) {
      return [
        "badge" =>
        Badge::create("Fila pequena", "yellow")
          ->tooltip("Poucou usuários podem receber leads nessa equipe."),

        "infobox" =>
        InfoBox::create("Fila pequena", "Poucos usuários habilitados a receber leads.")
          ->setType(InfoBox::TYPE_WARN)
          ->setContent($content)
      ];
    } else {
      return [
        "badge" =>
        Badge::create("Fila ativa", "green")
          ->tooltip("A fila está saudável."),
        "infobox" =>
        InfoBox::create("Fila saudável", "A fila funciona corretamente e tem usuários suficientes.")
          ->setType(InfoBox::TYPE_INFO)
          ->setContent($content)
      ];
    }
  }

  public static function colaboradores(Team $equipe)
  {
    if (empty($equipe->getUsers())) return [];

    $final = [];
    /** @var TeamUser $colaborador */
    foreach ($equipe->getUsers() as $colaborador) {
      $final[] = self::presentOneColaborador($equipe, $colaborador);
    }
    return $final;
  }

  public static function presentOneColaborador(Team $equipe, TeamUser $colaborador)
  {
    return [
      "id" => $colaborador->getId() ?? "",
      "usuario_id" => $colaborador->getUserId() ?? "",
      "usuario_nome" => $colaborador->getUserName() ?? "",
      "recebe_leads_switch" => self::recebeLeads($colaborador, $equipe->getId()),
      "funcao_select" => self::funcao($colaborador, TeamUserFunction::getSelectOptions()),
      "vez_buttons" => self::vez($colaborador, $equipe->getId()),
      "remover_button" => self::remover($colaborador, $equipe->getId())
    ];
  }

  private static function recebeLeads(TeamUser $colab, $equipeId)
  {
    if ($colab->canReceiveLeads()) {
      $label = "Sim";
      $active = true;
    } else {
      $label = "Não";
      $active = false;
    }

    return Button::create($label)
      ->switch()
      ->setSwitch($active)
      ->data([
        "action" => "teamuser:receiving",
        "url" => "colaboradores/alterar-recebimento/{$colab->getId()}",
        "equipe-id" => $equipeId,
      ])
      ->tooltip("Alterar se recebe leads")
      ->render();
  }

  private static function funcao(TeamUser $colab, $funcoes)
  {
    $button = Button::create("")
      ->color("blue")
      ->data([
        "action" => "teamuser:alter-function",
        "url" => "colaboradores/alterar-funcao/{$colab->getId()}",
        "original-value" => $colab->getFunctionId()
      ])
      ->setDisabled()
      ->tooltip("Salvar")
      ->withIcon("floppy-disk");

    $select = Field::create("", "funcao")
      ->type(Field::TYPE_SELECT)
      ->addClass("js--usuario-funcao")
      ->inputOnly()
      ->withoutDefaultOption()
      ->options(CreateOptions::criarOpcoes($funcoes, $colab->getFunctionId()));

    return <<<HTML
    <div class="cell-aligned">
      {$select->render()}
      {$button->render()}
    </div>
    HTML;

  }

  private static function vez(TeamUser $colab, $equipeId)
  {
    $btn1 = Button::create()
      ->color("gray")
      ->data([
        "action" => "teamuser:changetime",
        "url" => "colaboradores/alterar-vez/{$colab->getId()}",
        "equipe-id" => $equipeId,
        "set" => "harm"
      ])
      ->withIcon("minus");

    $btn2 = Button::create()
      ->color("silver")
      ->data([
        "action" => "teamuser:changetime",
        "url" => "colaboradores/alterar-vez/{$colab->getId()}",
        "equipe-id" => $equipeId,
        "set" => "prioritize"
      ])
      ->withIcon("plus");

    if (!$colab->canReceiveLeads()) {
      $btn1->setDisabled();
      $btn2->setDisabled();
    }

    return "{$btn1} {$btn2}";
  }

  private static function remover(TeamUser $colab, $equipeId)
  {
    return Button::create("")
      ->color("red")
      ->data([
        "action" => "teamuser:remove",
        "equipe-id" => $equipeId,
        "url" => "colaboradores/remover/{$colab->getId()}"
      ])
      ->withIcon("trash-can");
  }

  public static function presentNovoColaborador(array $colaboradores)
  {
    $final = [];
    /** @var TeamUser $colaborador */
    foreach ($colaboradores as $colaborador) {
      $final[] = [
        "usuario_id" => $colaborador->getUserId(),
        "usuario_nome" => $colaborador->getUserName(),
        "nivel_acesso_id" => $colaborador->getLevelId()
      ];
    }

    return $final;
  }
}
