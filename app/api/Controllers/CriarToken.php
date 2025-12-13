<?php

namespace App\api\Controllers;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repositories\TokenRepository;
use App\adms\Models\Services\DbConnectionClient;
use Exception;

/** Cria TOKENS com funções específicas */
class CriarToken
{
  private array $credenciais;
  private array $data;
  private array $chavesPermitidas = ["atendimento_id", "tipo", "contexto"];
  private array $tiposPermitidos = ["chatbot", "formulario"];

  /** Pega o array $post e processa a requisição */
  public function index(array $credenciais, array $post):void
  {
    $this->credenciais = $credenciais;

    // Pega o $post
    $this->data = array_intersect_key($post, array_flip($this->chavesPermitidas));
    unset($post);

    try {
      $token = $this->tratarToken();
    } catch (Exception $e){
      GenerateLog::generateLog("error", "Não foi possível criar o TOKEN por API", ["erro" => $e->getMessage()]);
      echo json_encode(["sucesso" => false, "mensagem" => $e->getMessage()]);
      exit;
    }

    // Resposta final
    echo json_encode([
      "sucesso" => true,
      "mensagem" => "",
      "token" => $token
    ]);
    exit;
  }

  /**
   * Trata o token completamente
   * 
   * @throws Exception
   * 
   * @return string
   */
  public function tratarToken():string
  {
    // Verifica se há dados mínimos
    foreach($this->chavesPermitidas as $chave){
      if (!isset($this->data[$chave]) || empty($this->data[$chave])){
        throw new Exception("Há um dado faltando.");
      }
    }

    // Verifica se o tipo do token é inválido
    if (!in_array($this->data["tipo"], $this->tiposPermitidos))
      throw new Exception("O tipo de token não é permitido.");

    $conexao = new DbConnectionClient($this->credenciais);
    $repositorio = new TokenRepository($conexao->conexao);

    // Ele não tem nenhum token, criar um novo
    $token = $repositorio->armazenarToken(
      $this->data["tipo"],
      $this->data["contexto"],
      null,
      null,
      $this->data["atendimento_id"]
    );
    
    return $token;
  }
}