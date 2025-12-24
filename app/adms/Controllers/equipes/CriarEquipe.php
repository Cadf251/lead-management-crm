<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;

class CriarEquipe extends EquipesAbstract
{
  public function index()
  {
    // Seleciona as opções do banco de dados
    $optionsArray = $this->repo->sql->selecionarOpcoes("produtos");
    $this->setData([
      "title" => "Criar Equipe",
      "equipe" => null,
      "produtos-options" => CreateOptions::criarOpcoes($optionsArray)
    ]);

    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = $_POST;

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_equipe", $this->data["form"]["csrf_token"])) {
      // Resume o array
      $equipe = $this->data["form"];

      $result = $this->service->criar(
        $equipe["nome"],
        $equipe["produto_id"],
        $equipe["descricao"]
      );

      // Mostrar mensagem de sucesso
      $_SESSION["alerta"] = [
        $result->getStatus(),
        $result->mensagens()
      ];
      $this->redirect();
    }
    // Retorna a VIEW
    $content = require APP_ROOT."app/adms/Views/equipes/criar-equipe.php";

    echo json_encode([
      "sucesso" => true,
      "html" => $content]);
    exit;
  }
}