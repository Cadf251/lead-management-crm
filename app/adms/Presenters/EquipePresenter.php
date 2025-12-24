<?php

namespace App\adms\Presenters;

use App\adms\Helpers\CreateOptions;
use App\adms\Models\Equipe;
use App\adms\Models\EquipeUsuario;
use App\adms\UI\Badge;
use App\adms\UI\Button;
use App\adms\UI\Field;
use App\adms\UI\InfoBox;

class EquipePresenter
{
  public static function present(array $equipes, array $funcoes = []): array
  {
    $final = [];
    foreach ($equipes as $equipe) {
      $final[] = [
        "id" => $equipe->id,
        "nome" => $equipe->nome,
        "descricao" => $equipe->descricao ?? "",
        "status_badge" => self::getStatusBadge($equipe),
        "produto_badge" => self::getProdutoBadge($equipe),
        "numero_badge" => self::getNumeroBadge($equipe),
        "buttons" => self::buttons($equipe->id, $equipe->nome, $equipe->status->id),
        "proximos" => self::proximos($equipe),
        "fila" => self::fila($equipe),
        "colaboradores" => self::colaboradores($equipe, $funcoes),
      ];
    }
    return $final;
  }

  private static function getStatusBadge(Equipe $equipe)
  {
    $class = UtilPresenter::getStatusClass($equipe->status->id);
    return Badge::create($equipe->status->nome, $class);
  }

  private static function getProdutoBadge(Equipe $equipe)
  {
    return Badge::create($equipe->produto->nome, "silver");
  }

  private static function getNumeroBadge(Equipe $equipe)
  {
    $count = $equipe->countColaboradores();

    return Badge::create(
      <<<HTML
      <i class="fa-solid fa-user"></i> $count
      HTML,
      "blue"
    );
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

    return $proximos;
  }

  private static function fila(Equipe $equipe): array
  {
    $recebem = $equipe->getRecebemLeads();

    if (empty($recebem)) {
      return [
        "badge" =>
        Badge::create("Fila inativa", "red")
          ->tooltip("Nenhum usuário pode receber leads nessa equipe."),

        "infobox" =>
        InfoBox::create("Fila inativa", "Nenhum usuário está habilitado a receber leads")
          ->setType(InfoBox::TYPE_ALERT)
      ];
    } else if (count($recebem) < 3) {
      return [
        "badge" =>
        Badge::create("Fila pequena", "yellow")
          ->tooltip("Poucou usuários podem receber leads nessa equipe."),

        "infobox" =>
        InfoBox::create("Fila pequena", "Poucos usuários habilitados a receber leads.")
          ->setType(InfoBox::TYPE_WARN)
      ];
    } else {
      return [
        "badge" =>
        Badge::create("Fila ativa", "green")
          ->tooltip("A fila está saudável."),
        "infobox" =>
        null
      ];
    }
  }

  private static function colaboradores(Equipe $equipe, $funcoes)
  {
    if (empty($equipe->colaboradores)) return [];

    $final = [];
    foreach ($equipe->colaboradores as $colaborador) {
      $final[] = [
        "id" => $colaborador->id,
        "usuario_id" => $colaborador->usuarioId,
        "usuario_nome" => $colaborador->usuarioNome,
        "recebe_leads_switch" => self::recebeLeads($colaborador),
        "funcao_select" => self::funcao($colaborador, $funcoes),
        "vez_buttons" => self::vez($colaborador),
        "remover_button" => self::remover($colaborador)
      ];
    }
    return $final;
  }
  
  private static function recebeLeads(EquipeUsuario $colab)
  {
    if ($colab->recebeLeads()) {
      $label = "Sim";
      $active = true;
    } else {
      $label = "Não";
      $active = false;
    }

    return Button::create($label)
      ->switch()
      ->data([
        "action" => "colaborador:recebimento",
        "colaborador-id" => $colab->id
      ])
      ->tooltip("Alterar se recebe leads")
      ->render();
  }

  private static function funcao(EquipeUsuario $colab, $funcoes)
  {
    $button = Button::create("")
      ->color("blue")
      ->data([
        "action" => "colaborador:alterar-funcao",
        "colaborador-id" => $colab->id
      ])
      ->tooltip("Salvar")
      ->withIcon("floppy-disk");

    $select = Field::create("", "funcao")
      ->type(Field::TYPE_SELECT)
      ->inputOnly()
      ->options(CreateOptions::criarOpcoes($funcoes, $colab->funcao->id));

    return <<<HTML
    <div class="cell-aligned">
      {$select->render()}
      {$button->render()}
    </div>
    HTML;
  }

  private static function vez(EquipeUsuario $colab)
  {
    $btn1 = Button::create()
      ->color("gray")
      ->data([
        "action" => "colaborador:prejudicar",
        "equipe-id" => $colab->id
      ])
      ->withIcon("minus");

    $btn2 = Button::create()
      ->color("silver")
      ->data([
        "action" => "colaborador:priorizar",
        "equipe-id" => $colab->id
      ])
      ->withIcon("plus");

    if (!$colab->recebeLeads()) {
      $btn1->setDisabled();
      $btn2->setDisabled();
    }

    return "{$btn1} {$btn2}";
  }

  private static function remover(EquipeUsuario $colab)
  {
    return Button::create("")
      ->color("red")
      ->data([
        "action" => "colaborador:remover",
        "equipe-id" => $colab->id
      ])
      ->withIcon("trash-can");
  }

  public static function presentNovoColaborador(array $colaboradores)
  {
    $final = [];
    foreach($colaboradores as $colaborador){
      $final[] = [
        "usuario_id" => $colaborador->usuarioId,
        "usuario_nome" => $colaborador->usuarioNome,
        "nivel_acesso_id" => $colaborador->nivelId
      ];
    }

    return $final;
  }
}
