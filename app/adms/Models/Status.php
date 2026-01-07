<?php

namespace App\adms\Models;

use Exception;

class Status
{
  public const STATUS_DESATIVADO = 1;
  public const STATUS_PAUSADO = 2;
  public const STATUS_ATIVADO = 3;

  public function __construct(
    public int $id,
    public string $nome
  ) {}

  public static function fromId(int $id): self
  {
    if ($id === 1) $nome = "Desativado";
    else if ($id === 2) $nome = "Pausado";
    else if ($id === 3) $nome = "Ativo";
    else {
      throw new Exception("Invalid Status ID: $id");
    }

    return new self($id, $nome);
  }
}
