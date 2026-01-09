<?php

namespace App\adms\Core;

/**
 * @complete V1
 */
class OperationResult
{
  private array $messages = [];
  private int $status = self::STATUS_SUCESSO;

  const STATUS_SUCESSO = 3;
  const STATUS_AVISO = 2;
  const STATUS_ERRO = 1;

  /**
   * Inclui uma nova mensagem
   */
  public function addMessage(string $msg): void
  {
    $this->messages[] = $msg;
  }

  /**
   * Coloca a operação como warning e inclui uma mensagem
   */
  public function warning(string $msg): void
  {
    $this->status = self::STATUS_AVISO;
    $this->addMessage($msg);
  }

  /**
   * Atualiza o status e adiciona uma mensagem
   */
  public function failed(string $msg): void
  {
    $this->status = self::STATUS_ERRO;
    $this->addMessage($msg);
  }

  /**
   * Retorna as mensagens
   */
  public function getMessages(): array
  {
    return $this->messages;
  }

  /**
   * Retorna como HTML com separador <br>
   */
  public function getMessagesAsHtml(): string
  {
    return implode("<br>", $this->messages);
  }

  /**
   * Retorna como string para uso em alertas
   */
  public function getStatusAsString(): string
  {
    $status = [
      1 => "❌ Erro!",
      2 => "ℹ️ Atenção!",
      3 => "✅ Sucesso!"
    ];

    return isset($status[$this->status])
      ? $status[$this->status]
      : "ℹ️ Atenção!";
  }

  /**
   * Retorna se falhou ou não
   */
  public function hadFailed(): bool
  {
    return $this->status === self::STATUS_ERRO;
  }

  /**
   * Retorna se teve sucesso. Warning também é considerado sucesso.
   */
  public function hadSucceded(): bool
  {
    return !$this->hadFailed();
  }

  /**
   * Pega o alerta no formado para a session
   */
  public function getAlert(): array
  {
    return [
      $this->getStatusAsString(),
      $this->getMessages()
    ];
  }

  /**
   * Retorna o status no formato acetável para AJAX
   * 
   * retorna em PT
   */
  public function getForAjax()
  {
    return [
      "sucesso" => $this->hadSucceded(),
      "alerta" => $this->getStatusAsString(),
      "mensagens" => $this->getMessagesAsHtml()
    ];
  }

  /**
   * Retorna o status no formato para API
   * 
   * retorna em EN
   */
  public function getForApi()
  {
    return [
      "success" => $this->hadSucceded(),
      "messages" => $this->getMessages()
    ];
  }

  /**
   * Reporta o alerta para a session
   */
  public function report()
  {
    $_SESSION["alerta"] = $this->getAlert();
  }
}

