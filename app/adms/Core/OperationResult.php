<?php

namespace App\adms\Core;

class OperationResult
{
  private bool $sucesso = true;
  private array $mensagens = [];

  public function addMensagem(string $msg): void
  {
    $this->mensagens[] = $msg;
  }

  public function falha(string $msg): void
  {
    $this->sucesso = false;
    $this->addMensagem($msg);
  }

  public function sucesso(): bool
  {
    return $this->sucesso;
  }

  public function mensagens(): array
  {
    return $this->mensagens;
  }
}
