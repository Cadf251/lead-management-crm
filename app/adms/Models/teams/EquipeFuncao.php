<?php

namespace App\adms\Models\teams;

class EquipeFuncao
{
  public function __construct(
    public int $id,
    public ?string $nome,
    public ?string $descricao
  )
  {
  }
}