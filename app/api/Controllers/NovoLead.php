<?php

namespace App\api\Controllers;

use App\adms\Controllers\atendimentos\CriarAtendimento;
use App\adms\Controllers\leads\CriarLead;
use App\adms\Helpers\GenerateLog;
use DateTime;
use Exception;

/** Recebe requisições de POST para inserir leads no banco de dados */
class NovoLead
{
  private array $credenciais;
  private array $data;
  private array $chavesLead = ["nome", "email", "celular"];
  private array $chavesAtendimento = ["equipe_id"];
  private array $chavesTotal;
  private array $chavesUtm = ["utm_source", "utm_medium", "utm_term", "utm_content", "palavra", "gclid", "fbclid"];

  private array|false $leadResult;
  private array|false $atendimentoResult;

  private array $errorData = [];

  /**
   * Da sequência a requisição API
   * 
   * @param array $credenciais
   * @param array $post
   * 
   * @return void echo JSON
   */
  public function index(array $credenciais, array $post):void
  {
    $this->credenciais = $credenciais;
    $this->chavesTotal = array_merge($this->chavesLead, $this->chavesAtendimento);
    $this->data = array_intersect_key($post, array_flip($this->chavesTotal));
    $this->data["utm"] = array_intersect_key($post, array_flip($this->chavesUtm));
    unset($post);

    try {
      $this->leadResult = $this->tratarLead();
    } catch (Exception $e){
      GenerateLog::generateLog("error", $e->getMessage(), $this->errorData);
      echo json_encode(["sucesso" => false, "mensagem" => $e->getMessage()]);
      exit;
    }

    try {
      $this->atendimentoResult = $this->tratarAtendimento();
    } catch (Exception $e){
      GenerateLog::generateLog("error", $e->getMessage(), $this->errorData);
      echo json_encode(["sucesso" => false, "mensagem" => $e->getMessage()]);
      exit;
    }

    // Resposta final
    echo json_encode(
      [
        "sucesso" => true,
        "mensagem" => "O processo correu até o final",
        "lead" => $this->leadResult,
        "atendimento" => $this->atendimentoResult
      ]
    );
    exit;
  }

  /**
   * Trata o lead completamente.
   * 
   * @return array|false
   */
  public function tratarLead():array|false
  {
    foreach ($this->chavesLead as $chave){
      if (!isset($this->data[$chave]) || empty($this->data[$chave])){
        throw new \Exception("Há um dado faltando.");
        return false;
      }
    }

    $lead = new CriarLead($this->credenciais);
    $lead->setarDados($this->data["nome"], $this->data["email"], $this->data["celular"], $this->data["utm"]);

    $leadExiste = $lead->leadExiste();
    
    if (is_int($leadExiste)){
      $leadNovo = false;
      $lead->id = $leadExiste;
    } else {
      $leadNovo = true;
      $criar = $lead->criarLead();
      
      if (($criar !== false) || ($criar !== 0)){
        $lead->id = $criar;
      } else {
        $this->errorData = ["O método criarLead() falhou."];
        throw new \Exception("Não foi possível criar o lead");
        return false;
      }
    }

    return [
      "id" => $lead->id,
      "nome" => $lead->nome,
      "email" => $lead->email,
      "celular" => $lead->celular,
      "lead_novo" => $leadNovo
    ];
  }

  /**
   * Trata o atendimento completamente.
   * 
   * @return array|false
   */
  public function tratarAtendimento():array|false
  {
    // Verifica se há equipe_id, se não, não gera o atendimento mas deixa o lead no limbo
    foreach($this->chavesAtendimento as $chave){
      if (!isset($this->data[$chave]) || empty($this->data[$chave])){
        $this->errorData = ["dado" => $chave];
        throw new \Exception("Há um dado faltando.");
        return false;
      }
    }

    // Processa o atendimento
    $atendimento = new CriarAtendimento($this->credenciais);
    $atendimento->leadId = $this->leadResult["id"];
    $atendimento->equipeId = (int)$this->data["equipe_id"];

    $novoAtendimento = false;
    if ($this->leadResult["lead_novo"])
      $novoAtendimento = true;
    else {
      $recente = $atendimento->atendimentoExiste();

      if($recente === false){
        $novoAtendimento = true;
      } else {
        $agora = new DateTime();
        $atendimentoData = new DateTime($recente['atendimento_created']);
        $atendimentoDias = $agora->diff($atendimentoData);

        if ($atendimentoDias >= 30)
          $novoAtendimento = true;
        else {
          $atendimento->id = $recente["atendimento_id"];
          $atendimento->usuarioId = $recente["usuario_id"];
          $atendimento->usuarioNome = $recente["nome"];
          $atendimento->usuarioEmail = $recente["email"];
          $atendimento->usuarioCelular = $recente["celular"];
        }
      }
    }

    if($novoAtendimento){
      $dados = $atendimento->novoAtendimento();

      if ($dados === false){
        $this->errorData = ["O método novoAtendimento() falhou."];
        throw new \Exception("Não foi possível criar o atendimento.");
        return false;
      }

      $atendimento->id = $dados["atendimento_id"];
      $atendimento->usuarioId = $dados["usuario_id"];
      $atendimento->usuarioNome = $dados["usuario_nome"];
      $atendimento->usuarioEmail = $dados["usuario_email"];
      $atendimento->usuarioCelular = $dados["usuario_celular"];
    }

    return [
      "id" => $atendimento->id,
      "atendente" => [
        "id" => $atendimento->usuarioId,
        "nome" => $atendimento->usuarioNome,
        "email" => $atendimento->usuarioEmail,
        "celular" => $atendimento->usuarioCelular
      ],
      "atendimento_novo" => $novoAtendimento
    ];
  }
}