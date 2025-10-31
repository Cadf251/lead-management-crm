<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;

class CriarEquipe extends EquipesAbstract
{
  public function index()
  {
    // Seleciona as opções do banco de dados
    $optionsArray = $this->repo->selecionarOpcoes("produtos");
    $this->setData([
      "title" => "Criar Equipe",
      "equipe" => null,
      "produtos-options" => CreateOptions::criarOpcoes($optionsArray)
    ]);

    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = $_POST;

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_equipe", $this->data["form"]["csrf_token"])) {
      // Formata os dados
      $this->data["form"]["descricao"] = 
        $this->data["form"]["descricao"] === ""
        ? null
        : $this->data["form"]["descricao"];
        
      // Tenta criar
      $criacao = $this->repo->criarEquipe(
        $this->data["form"]["nome"],
        (int)$this->data["form"]["produto_id"],
        $this->data["form"]["descricao"] ?? null,
      );

      if ($criacao)
        $_SESSION["alerta"] = [
          "Sucesso!",
          "✅ Equipe {$this->data['form']['nome']} criada com sucesso."
        ];
      else
        $_SESSION["alerta"] = [
          "Erro!",
          "❌ A equipe não foi criada."
        ];

      $this->redirect();
    }
    
    $this->render("criar-equipe"); 
  }
}