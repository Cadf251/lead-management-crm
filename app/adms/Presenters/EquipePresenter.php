<?php

namespace App\adms\Presenters;

use App\adms\Helpers\CreateOptions;
use App\adms\Models\teams\Equipe;
use App\adms\Models\teams\Colaborador;
use App\adms\UI\Badge;
use App\adms\UI\Button;
use App\adms\UI\Field;
use App\adms\UI\InfoBox;

class EquipePresenter
{
  public static function present(array $equipes, array $funcoes = []): array
  {
    $final = [];
    /** @var Equipe $equipe */
    foreach ($equipes as $equipe) {
      $final[] = [
        "id" => $equipe->getId() ?? "",
        "nome" => $equipe->getNome() ?? "Sem nome",
        "descricao" => $equipe->getDescricao() ?? "",
        "status_badge" => self::getStatusBadge($equipe),
        "produto_badge" => self::getProdutoBadge($equipe),
        "numero_badge" => self::getNumeroBadge($equipe),
        "buttons" => self::buttons($equipe->getId(), $equipe->getNome(), $equipe->getStatusId()),
        "proximos" => self::proximos($equipe),
        "fila" => self::fila($equipe),
        "colaboradores" => self::colaboradores($equipe, $funcoes),
      ];
    }
    return $final;
  }

  private static function getStatusBadge(Equipe $equipe)
  {
    $class = UtilPresenter::getStatusClass($equipe->getStatusId());
    return Badge::create($equipe->getStatusNome(), $class);
  }

  private static function getProdutoBadge(Equipe $equipe)
  {
    return Badge::create($equipe->getProdutoNome() ?? "Produto inválido.", "silver");
  }

  private static function getNumeroBadge(Equipe $equipe)
  {
    $count = $equipe->countColaboradores();

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
        ->link("listar-colaboradores/$equipeId")
        ->tooltip("Listar Colaboradores"),

      "editar" =>
      Button::create("")
        ->color("blue")
        ->data([
          "action" => "equipe:editar",
          "equipe-id" => $equipeId
        ])
        ->tooltip("Editar equipe")
        ->withIcon("pencil"),

      "pausar" =>
      Button::create("")
        ->color("gray")
        ->data([
          "action" => "equipe:pausar",
          "equipe-id" => $equipeId
        ])
        ->tooltip("Pausar equipe")
        ->withIcon("pause"),

      "ativar" =>
      Button::create("")
        ->color("green")
        ->data([
          "action" => "equipe:ativar",
          "equipe-id" => $equipeId,
          "equipe-nome" => $equipeNome
        ])
        ->tooltip("Ativar equipe")
        ->withIcon("play"),

      "desativar" =>
      Button::create("")
        ->color("red")
        ->data([
          "action" => "equipe:desativar",
          "equipe-id" => $equipeId,
          "equipe-nome" => $equipeNome
        ])
        ->tooltip("Desativar equipe")
        ->withIcon("trash-can")
    ];

    return match ($statusId) {
      1 => [
        []
      ],
      2 => [
        $btns["colaboradores"],
        $btns["editar"],
        $btns["ativar"],
        $btns["desativar"]
      ],
      3 => [
        $btns["colaboradores"],
        $btns["editar"],
        $btns["pausar"],
        $btns["desativar"]
      ]
    };
  }

  private static function proximos(Equipe $equipe)
  {
    $proximos = $equipe->getProximos();

    if (empty($proximos)) return null;

    $simplified = [];

    /** @var Colaborador $proximo */
    foreach ($proximos as $proximo) {
      $simplified[] = [
        "id" => $proximo->getId(),
        "nome" => $proximo->getUsuarioNome(),
        "vez" => $proximo->getVez()
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

  public static function fila(Equipe $equipe): array
  {
    $recebem = $equipe->getRecebemLeads();

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

  private static function colaboradores(Equipe $equipe, $funcoes)
  {
    if (empty($equipe->getColaboradores())) return [];

    $final = [];
    /** @var Colaborador $colaborador */
    foreach ($equipe->getColaboradores() as $colaborador) {
      $final[] = [
        "id" => $colaborador->getId() ?? "",
        "usuario_id" => $colaborador->getUsuarioId() ?? "",
        "usuario_nome" => $colaborador->getUsuarioNome() ?? "",
        "recebe_leads_switch" => self::recebeLeads($colaborador, $equipe->getId()),
        "funcao_select" => self::funcao($colaborador, $funcoes),
        "vez_buttons" => self::vez($colaborador, $equipe->getId()),
        "remover_button" => self::remover($colaborador, $equipe->getId())
      ];
    }
    return $final;
  }

  private static function recebeLeads(Colaborador $colab, $equipeId)
  {
    if ($colab->podeReceberLeads()) {
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
        "action" => "colaborador:recebimento",
        "colaborador-id" => $colab->getId(),
        "equipe-id" => $equipeId
      ])
      ->tooltip("Alterar se recebe leads")
      ->render();
  }

  private static function funcao(Colaborador $colab, $funcoes)
  {
    if ($colab->podeSerGerente()) {
      $button = Button::create("")
        ->color("blue")
        ->data([
          "action" => "colaborador:alterar-funcao",
          "colaborador-id" => $colab->getId(),
          "original-value" => $colab->getFuncaoId()
        ])
        ->setDisabled()
        ->tooltip("Salvar")
        ->withIcon("floppy-disk");

      $select = Field::create("", "funcao")
        ->type(Field::TYPE_SELECT)
        ->addClass("js--usuario-funcao")
        ->inputOnly()
        ->withoutDefaultOption()
        ->options(CreateOptions::criarOpcoes($funcoes, $colab->getFuncaoId()));

      return <<<HTML
      <div class="cell-aligned">
        {$select->render()}
        {$button->render()}
      </div>
      HTML;
    } else {
      return <<<HTML
      <div class="cell-aligned">
        {$colab->getFuncaoNome()}
      </div>
      HTML;
    }
  }

  private static function vez(Colaborador $colab, $equipeId)
  {
    $btn1 = Button::create()
      ->color("gray")
      ->data([
        "action" => "colaborador:prejudicar",
        "colaborador-id" => $colab->getId(),
        "equipe-id" => $equipeId,
      ])
      ->withIcon("minus");

    $btn2 = Button::create()
      ->color("silver")
      ->data([
        "action" => "colaborador:priorizar",
        "colaborador-id" => $colab->getId(),
        "equipe-id" => $equipeId,
      ])
      ->withIcon("plus");

    if (!$colab->podeReceberLeads()) {
      $btn1->setDisabled();
      $btn2->setDisabled();
    }

    return "{$btn1} {$btn2}";
  }

  private static function remover(Colaborador $colab, $equipeId)
  {
    return Button::create("")
      ->color("red")
      ->data([
        "action" => "colaborador:remover",
        "colaborador-id" => $colab->getId(),
        "equipe-id" => $equipeId,
      ])
      ->withIcon("trash-can");
  }

  public static function presentNovoColaborador(array $colaboradores)
  {
    $final = [];
    /** @var Colaborador $colaborador */
    foreach ($colaboradores as $colaborador) {
      $final[] = [
        "usuario_id" => $colaborador->getUsuarioId(),
        "usuario_nome" => $colaborador->getUsuarioNome(),
        "nivel_acesso_id" => $colaborador->getNivelAcessoId()
      ];
    }

    return $final;
  }
}
