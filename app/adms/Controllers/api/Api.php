<?php

namespace App\adms\Controllers\api;

use App\adms\Controllers\Services\LoadApi;
use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\SlugController;
use App\adms\Models\Services\DbConnectionGlobal;
use App\database\Models\DatabaseRepository;

/** Essa classe é publica e recebe as requisições de POST, faz as devidas confirmações e chama o loadApi. */
class Api
{
  /**
   * Recebe o $_POST. Verifica se há um api_token válido e instancia o LoadApi. 
   * Trata os erros em JSON: {"sucesso":bool,"mensagem":string}
   * 
   * @param string|null $task Recebe uma task que será transformada em Class
   * 
   * @return void
   */
  public function index(string|null $task):void
  {
    // Recebe o input
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Permite só POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
      GenerateLog::generateLog("warning", "Uma requisição ao API não veio por POST.", ["post" => $post]);
      echo json_encode(["sucesso" => false, "mensagem" => "A requisição não é POST."]);
      exit;
    }

    // Precisa de um TOKEN para conectar ao banco de dados
    if (($post["api_token"] === "") || (!isset($post["api_token"]))){
      GenerateLog::generateLog("warning", "Uma requisição ao API não tem TOKEN de acesso.", ["post" => $post]);
      echo json_encode(["sucesso" => false, "mensagem" => "TOKEN não informado."]);
      exit;
    }

    $server = new DbConnectionGlobal();
    $repository = new DatabaseRepository($server->conexao);

    $servidor = $repository->verificarTokenApi($post["api_token"]);

    if ($servidor === false){
      GenerateLog::generateLog("warning", "O TOKEN é inválido para a requisição POST.", ["post" => $post]);
      echo json_encode(["sucesso" => false, "TOKEN inválido."]);
      exit;
    }

    // Tranforma a task em class
    $class = SlugController::slugController($task);

    // Tenta chamar a App\api\Controllers\Class->index().
    $loadApi = new LoadApi();
    $loadApi->loadApi($class, $servidor, $post);
  }
}