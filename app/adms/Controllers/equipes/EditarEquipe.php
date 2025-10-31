<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;

class EditarEquipe extends EquipesAbstract
{
  public function index(string|null|int $equipeId)
  {
    // Seta o ID
    $this->setInfoById((int)$equipeId);

    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = $_POST;

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_equipe", $this->data["form"]["csrf_token"])) {
      // Formata os dados
      $this->data["form"]["descricao"] = 
        $this->data["form"]["descricao"] === ""
        ? null
        : $this->data["form"]["descricao"];
        
      // Tenta fazer upload
      $params = [
        ":nome" => $this->data["form"]["nome"],
        ":descricao" => $this->data["form"]["descricao"],
        ":produto_id" => $this->data["form"]["produto_id"]
      ];

      $update = $this->repo->updateEquipe($params, $this->id);

      if ($update)
        $_SESSION["alerta"] = [
          "Sucesso!",
          "✅ Equipe {$this->data['form']['nome']} atualizada com sucesso."
        ];
      else
        $_SESSION["alerta"] = [
          "Erro!",
          "❌ A equipe não foi atualizada."
        ];

      $this->redirect();
    }
    
    // Prepara um array simplificado para a VIEW
    $equipe = [
      "nome" => $this->nome,
      "descricao" => $this->descricao,
      "produto_id" => $this->produtoId
    ];

    $optionsArray = $this->repo->selecionarOpcoes("produtos");
    $this->setData([
      "title" => "Editar Equipe",
      "equipe" => $equipe,
      "produtos-options" => CreateOptions::criarOpcoes($optionsArray, $this->produtoId)
    ]);

    $this->render("editar-equipe");
  }
}