<?php

namespace App\adms\Models;

class Produto
{
  public function __construct(
    public int $id,
    public string $nome,
    public ?string $descricao
  )
  {
  }
}