<?php

namespace App\adms\Core;

use App\adms\Helpers\GenerateLog;

/**
 * @complete V1
 */
class OperationResult
{
  private array $messages = [];
  private int $status = self::STATUS_SUCESSO;
  private bool $reported = false;
  private ?string $redirect = null;
  private array $updates = [];
  private array $customParams = [];

  private ?string $csrf_token = null;
  private bool $close_overlay = false;

  private array $savedInstance = [];

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
  public function getMessagesAsHtml(): ?string
  {
    if (empty($this->messages)) return null;
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
   * Salva uma instância na operação para uso posterior.
   * 
   */
  public function saveInstance(string $key, mixed $instance)
  {
    $this->savedInstance[$key] = $instance;
  }

  public function getInstance(string $key): mixed
  {
    return $this->savedInstance[$key] ?? null;
  }

  /**
   * Adiciona um destino caso a operação seja concluída com sucesso
   */
  public function redirect(string $to): void
  {
    $this->redirect = $to;
  }

  public function setChange(string $target, string $html): void
  {
    $this->updates[] = [
      "type" => "change",
      "target" => $target,
      "html" => $html
    ];
  }

  public function setUpdate(string $target, string $html): void
  {
    $this->updates[] = [
      "type" => "update",
      "target" => $target,
      "html" => $html
    ];
  }

  public function setCustomParam(string $key, $value): void
  {
    $this->customParams[$key] = $value;
  }

  /**
   * Adiciona um container para que um novo card seja criado
   */
  public function setAppend(string $target, string $html): void
  {
    // $this->append = $append;
    $this->updates[] = [
      "type" => "append",
      "target" => $target,
      "html" => $html,
    ];
  }

  public function setRemove(string $target)
  {
    // $this->remove = $remove;
    $this->updates[] = [
      "type" => "remove",
      "target" => $target
    ];
  }

  public function setOverlay(string $html): void
  {
    $this->updates[] = [
      "type" => "overlay",
      "html" => $html
    ];
  }

  public function setCsrfToken(string $token)
  {
    $this->csrf_token = $token;
  }

  public function closeOverlay()
  {
    $this->close_overlay = true;
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
   * retorna em PT|EN
   */
  public function getForAjax()
  {
    $response = [
      "success"   => $this->hadSucceded(),
      "redirect"  => $this->redirect ?? null,
      "csrf_token" => $this->csrf_token ?? null,
      "close_overlay" => $this->close_overlay ?? false,
    ];

    // Só adiciona os textos se NÃO foi reportado para a sessão
    if (!$this->reported) {
      $response["alert"]    = $this->getStatusAsString();
      $response["messages"] = $this->getMessagesAsHtml();
    }

    if (!empty($this->updates)) {
      $response["updates"] = $this->updates;
    }

    if (!empty($this->customParams)) {
      foreach ($this->customParams as $key => $value) {
        $response[$key] = $value;
      }
    }

    return $response;
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

    $this->reported = true;
  }
}
