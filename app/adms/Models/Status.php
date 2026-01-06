<?php

namespace App\adms\Models;

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
    if ($id === 2) $nome = "Pausado";
    if ($id === 3) $nome = "Ativo";

    return new self($id, $nome);
  }
}
