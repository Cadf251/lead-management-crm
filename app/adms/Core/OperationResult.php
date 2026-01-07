<?php

namespace App\adms\Core;

use App\adms\Helpers\GenerateLog;

/**
 * @todo remover atributo status
 */
class OperationResult
{
  private bool $sucesso = true;
  private array $mensagens = [];
  private int $status = self::STATUS_SUCESSO;

  const STATUS_SUCESSO = 3;
  const STATUS_AVISO = 2;
  const STATUS_ERRO = 1;

  public function addMensagem(string $msg): void
  {
    $this->mensagens[] = $msg;
  }

  public function warn(string $msg):void
  {
    $this->status = self::STATUS_AVISO;
    $this->addMensagem($msg);
  }

  /**
   * @todo Remover a linha sucesso
   */
  public function falha(string $msg): void
  {
    $this->sucesso = false;
    $this->status = self::STATUS_ERRO;
    $this->addMensagem($msg);
  }

  /**
   * Retorna o status em forma de mensagem para o aviso.
   */
  public function getStatus():string
  {
    if ($this->status === 1) return "❌ Erro!";
    else if ($this->status === 2) return "ℹ️ Atenção!";
    else if ($this->status === 3) return "✅ Sucesso!";
    else return "ℹ️ Atenção!";
  }

  /**
   * Formatado para $_SESSION["alerta"]
   */
  public function getAlerta():array
  {
    return [
      $this->getStatus(),
      $this->mensagens()
    ];
  }

  public function report()
  {
    $_SESSION["alerta"] = $this->getAlerta();
  }

  public function getMensagens(){
    return implode("<br>", $this->mensagens());
  }

  public function sucesso(): bool
  {
    return $this->status !== 1;
  }

  /**
   * Retorna as mensagens em forma de array.
   */
  public function mensagens(): array
  {
    return $this->mensagens;
  }
}
