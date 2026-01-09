<?php

namespace App\api\Controllers;

use App\adms\Core\OperationResult;
use App\adms\Services\LeadService;
use App\api\Models\ApiClient;
use App\api\Service\ApiService;
use Exception;

/** Recebe requisições de POST para inserir leads no banco de dados */
class NovoLead
{
  private ApiClient $client;
  private array $data;
  private OperationResult $result;
  private ApiService $service;

  public function __construct()
  {
    $this->result = new OperationResult();
    $this->service = new ApiService();
  }

  /**
   * Da sequência a requisição API
   * 
   * @param ApiClient $client
   * @param array $post
   * 
   * @return void echo JSON
   */
  public function index(ApiClient $client, array $post): void
  {
    $min_keys = [
      "visitor"
    ];

    $max_keys = array_merge($min_keys, [
      "offer_id", "interaction"
    ]);

    // Inicializa o cliente globalmente
    $this->client = $client;

    // Limpa chaves não permitidas
    $this->data = $this->service->cleanKeys($max_keys, $post);
    unset($post);

    // Verifica se há os dados mínimos
    try {
      $this->service->checkKeys($min_keys, $this->data);
    } catch (Exception $e) {
      $this->result->failed($e->getMessage());
      echo json_encode($this->result->getForApi());
      exit;
    }

    try {
      $leadResult = $this->tratarLead();
      $this->result->addMessage("Lead recebido.");
    } catch (Exception $e) {
      $this->result->failed($e->getMessage());
      echo json_encode($this->result->getForApi());
      exit;
    }

    // @todo Continuar lógica de distribuição

    echo json_encode([
      ...$this->result->getForApi(),
      "lead" => $leadResult
    ]);

    // try {
    //   $this->atendimentoResult = $this->tratarAtendimento();
    // } catch (Exception $e) {
    //   // Erro não fatal
    //   GenerateLog::generateLog("error", $e->getMessage(), $this->errorData);
    //   $this->atendimentoResult = [
    //     "recebido" => false,
    //     "error" => $e->getMessage()
    //   ];
    //   $this->notificar($e->getMessage(), [
    //     "post" => $this->post,
    //     "cliente" => $this->credenciais,
    //     "atendimento" => $this->atendimentoResult
    //   ]);
    // }

    // try {
    //   $this->enviarDados();
    // } catch (Exception $e) {
    //   GenerateLog::generateLog("info", "One error ocurred", [$e->getMessage()]);
    // }

    // // Resposta final
    // echo json_encode(
    //   [
    //     "sucesso" => true,
    //     "mensagem" => "O processo correu até o final",
    //     "lead" => $this->leadResult,
    //     "atendimento" => $this->atendimentoResult
    //   ]
    // );
    // exit;
  }

  /**
   * Trata o lead completamente.
   * 
   * @throws Exception
   * 
   * @return array
   */
  public function tratarLead(): array
  {
    $min_keys = ["nome", "email", "celular"];

    $visitor = $this->data["visitor"];
    
    try {
      $this->service->checkKeys($min_keys, $visitor);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    
    $leadService = new LeadService($this->client->getConn());

    $lead = $leadService->createLead(
      $visitor["nome"],
      $visitor["email"],
      $visitor["celular"]
    );

    if($lead === null) {
      throw new Exception("Não foi possível criar o lead devido a um erro interno.");
    }
    
    return [
      "id" => $lead->getId(),
      "nome" => $lead->getNome(),
      "email" => $lead->getEmail(),
      "celular" => $lead->getCelular()
    ];
  }

  public function distributeLead()
  {

  }

  // /**
  //  * Trata o atendimento completamente.
  //  * 
  //  * @throws Exception
  //  * 
  //  * @return array
  //  */
  // public function tratarAtendimento(): array
  // {
  //   // Verifica se há equipe_id, se não, não gera o atendimento mas deixa o lead no limbo
  //   foreach ($this->chavesAtendimento as $chave) {
  //     if (!isset($this->data[$chave]) || empty($this->data[$chave])) {
  //       $this->errorData = ["dado" => $chave];
  //       throw new Exception("Há um dado faltando.");
  //     }
  //   }

  //   // Processa o atendimento
  //   $atendimento = new CriarAtendimento($this->credenciais);
  //   $atendimento->leadId = $this->leadResult["id"];
  //   $atendimento->equipeId = (int)$this->data["equipe_id"];

  //   $novoAtendimento = false;
  //   if ($this->leadResult["lead_novo"])
  //     $novoAtendimento = true;
  //   else {
  //     $recente = $atendimento->atendimentoExiste();

  //     if ($recente === false) {
  //       $novoAtendimento = true;
  //     } else {
  //       $agora = new DateTime();
  //       $atendimentoData = new DateTime($recente['atendimento_created']);
  //       $atendimentoDias = $agora->diff($atendimentoData);

  //       if ($atendimentoDias->days >= 30)
  //         $novoAtendimento = true;
  //       else {
  //         $atendimento->id = $recente["atendimento_id"];
  //         $atendimento->usuarioId = $recente["usuario_id"];
  //         $atendimento->usuarioNome = $recente["nome"];
  //         $atendimento->usuarioEmail = $recente["email"];
  //         $atendimento->usuarioCelular = $recente["celular"];
  //       }
  //     }
  //   }

  //   if ($novoAtendimento) {
  //     try {
  //       $dados = $atendimento->novoAtendimento();
  //     } catch (Exception $e) {
  //       throw new Exception($e->getMessage());
  //     }

  //     $atendimento->id = $dados["atendimento_id"];
  //     $atendimento->usuarioId = $dados["usuario_id"];
  //     $atendimento->usuarioNome = $dados["usuario_nome"];
  //     $atendimento->usuarioEmail = $dados["usuario_email"];
  //     $atendimento->usuarioCelular = $dados["usuario_celular"];
  //   }

  //   return [
  //     "recebido" => true,
  //     "id" => $atendimento->id,
  //     "atendente" => [
  //       "id" => $atendimento->usuarioId,
  //       "nome" => $atendimento->usuarioNome,
  //       "email" => $atendimento->usuarioEmail,
  //       "celular" => $atendimento->usuarioCelular
  //     ],
  //     "atendimento_novo" => $novoAtendimento
  //   ];
  // }

  // public function enviarDados()
  // {
  //   $mail = new PHPMailerHelper();
  //   if (!isset($this->atendimentoResult["atendente"]["email"]) || empty($this->atendimentoResult["atendente"]["email"]) || $this->atendimentoResult["atendente"]["email"] === null) {
  //     // Email do atendente não existe, verifica se existe email de fallback configurado
  //     if (!isset($this->credenciais["email_master"]) || empty($this->credenciais["email_master"]) || $this->credenciais["email_master"] === null) {
  //       throw new Exception("nenhum email para envio.");
  //     } else {
  //       $to = $this->credenciais["email_master"];
  //     }
  //   } else {
  //     $to = $this->atendimentoResult["atendente"]["email"];
  //   }


  //   $mail->destinatarios([$to]);

  //   $titulo = "Você tem um novo lead para atender";
  //   $leadRows = [
  //     "ID" => $this->leadResult["id"],
  //     "Nome" => $this->leadResult["nome"],
  //     "Email" => $this->leadResult["email"],
  //     "Celular" => $this->leadResult["celular"],
  //   ];
  //   $leadHtml = "";
  //   foreach ($leadRows as $key => $row) {
  //     $leadHtml .= <<<HTML
  //     <tr>
  //       <td style="min-width:100px">$key</td>
  //       <td>$row</td>
  //     </tr>
  //     HTML;
  //   }
  //   if ($this->atendimentoResult["recebido"]) {
  //     $attRows = [
  //       "Recebido" => "Sim",
  //       "Atendente" => $this->atendimentoResult["atendente"]["nome"],
  //       "Email" => $this->atendimentoResult["atendente"]["email"],
  //       "Celular" => $this->atendimentoResult["atendente"]["celular"],
  //     ];
  //   } else {
  //     $attRows = [
  //       "Recebido" => "Não",
  //       "Motivo" => $this->atendimentoResult["error"]
  //     ];
  //   }

  //   $attHtml = "";
  //   foreach ($attRows as $key => $row) {
  //     $attHtml .= <<<HTML
  //     <tr>
  //       <td style="min-width:100px">$key</td>
  //       <td>$row</td>
  //     </tr>
  //     HTML;
  //   }
  //   $body = <<<HTML
  //   <html>
  //   <body style="font-family:verdana, arial; background-color: #efefef; margin:0">
  //     <div style="font-family:verdana, arial; background-color: #efefef; width: 100%; padding-top: 50px; padding-bottom: 50px">
  //       <div style="background-color: #fefefe;border-radius: 8px;padding-top: 16px;padding-bottom: 16px;padding-left: 32px;padding-right:32px; max-width: 500px; margin-left: auto; margin-right: auto; margin-top: 30px">
  //         <h1 style="text-align: center;">Dados do lead</h1>
  //         <table>
  //           {$leadHtml}
  //         </table>
  //         <hr>
  //         <table>
  //           {$attHtml}
  //         </table>
  //         <div style="margin: 32px auto; width: 100%;text-align: center;">
  //         <a style="margin: auto; padding: 12px; border-radius: 5px; background-color: #0ebcc9;color: #efefef;font-weight: bold;text-decoration: none;" href="">Atender lead</a></div>
  //       </div>
  //     </div>
  //   </body>
  //   </html>
  //   HTML;
  //   $mail->setarConteudo($titulo, $body);
  //   $mail->enviar();
  // }

  // private function notificar(string $descricao, array $info)
  // {
  //   NotificarErro::notificar($descricao, $info);
  // }
}
