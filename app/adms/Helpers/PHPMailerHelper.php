<?php

namespace App\adms\Helpers;

use Exception;
use Generator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class PHPMailerHelper
{
  public ?PHPMailer $mail = null;

  public function __construct()
  {
    $this->mail = new PHPMailer(true);
    $this->mail->SMTPDebug  = SMTP::DEBUG_OFF;
    $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $this->mail->isSMTP();
    $this->mail->SMTPAuth   = true;
    $this->mail->Host       = $_ENV["SMPT_HOST"];
    $this->mail->Username   = $_ENV["SMPT_USER"];
    $this->mail->Password   = $_ENV["SMPT_PASS"];
    $this->mail->Port       = $_ENV["SMPT_PORT"];
    $this->mail->isHTML(true);
    $this->mail->CharSet = 'UTF-8';
    $this->mail->setFrom('contato@agenciardmind.com.br', 'RD Mind Ads');
  }

  /**
   * Adiciona destinatários de um array
   * 
   * @param array $destinatarios Array simples com strings dos destinatários
   * 
   * @return void
   */
  public function destinatarios(array $destinatarios):void
  {
    foreach($destinatarios as $dest){
      $this->mail->addAddress($dest);
    }
  }

  /**
   * Adiciona imagens de um array
   * 
   * @param array $imagens Array associativo com o caminho e nome:
   * 0 => ["caminho" => "", "nome" => ""], 1=> [...]
   * 
   * @return void
   */
  public function imagens(array $imagens):void
  {
    foreach($imagens as $idx => $img){
      if (
          !isset($img["caminho"], $img["nome"]) ||
          !is_string($img["caminho"]) ||
          !is_string($img["nome"])
      )
        GenerateLog::generateLog("error", "Formato inválido para imagem", ["indice" => $idx, "esperado" => "['caminho' => string, 'nome' => string]"]);

      if (!file_exists($img["caminho"]))
        GenerateLog::generateLog("error", "Arquivo de imagem não encontrado", ["indice" => $idx, "caminho" => $img["caminho"]]);

      $this->mail->AddEmbeddedImage($img["caminho"], $img["nome"]);
    }
  }

  /**
   * Subtitui os parâmetros na string para o email
   * 
   * @param string $body O Corpo do email sendo tratado
   * @param array $params Os parâmetros no formato [PARAM] => "var"
   * 
   * @return string O $body atualizado
   */
  public function parameters(string $body, array $params):string
  {
    foreach ($params as $key => $value){
      if (strpos($body, $key) !== false){
        $body = str_replace($key, $value, $body);
      }
    }
    return $body;
  }

  /**
   * Seta o subject e o body do e-mail.
   * 
   * @param string $titulo O subject
   * @param string $body Body HTML
   * 
   * @return void
   */
  public function setarConteudo(string $titulo, string $body):void
  {
    $this->mail->Subject = $titulo;
    $this->mail->Body = $body;
  }

  /**
   * Enviar o email
   * 
   * Cria um log se falhar.
   * 
   * @return bool Se falhou ou não
   */
  public function enviar():bool
  {
    try {
      // Validar Subject
      if (empty($this->mail->Subject)) {
        throw new Exception("O campo 'Subject' não foi definido.");
      }

      // Validar Body
      if (empty($this->mail->Body)) {
        throw new Exception("O campo 'Body' não foi definido.");
      }

      // Validar From
      $from = $this->mail->From ?? null;
      $fromName = $this->mail->FromName ?? null;
      if (empty($from) || empty($fromName)) {
        throw new Exception("O campo 'From' não foi definido corretamente.");
      }

      // Validar se existe ao menos 1 destinatário
      if (empty($this->mail->getToAddresses())) {
        throw new Exception("Nenhum destinatário foi definido.");
      }
      
      $this->mail->send();
      return true;
    } catch (Exception $e){
      GenerateLog::generateLog("error", "Um email não pôde ser enviado", ["erro" => $e->getMessage()]);
      return false;
    }
  }
}