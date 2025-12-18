<?php

namespace App\adms\Models;

use App\adms\Repositories\UsuariosRepository;

class UsuarioStatus
{
  public function __construct(
    public int $id,
    public string $nome,
    public string $descricao = ""
  )
  {
  }

  public static function fromId(
    int $id,
    UsuariosRepository $repo
  ): self {
    $data = $repo->getStatus($id);

    return new self(
      $id,
      $data['nome'],
      $data['descricao']
    );
  }
}