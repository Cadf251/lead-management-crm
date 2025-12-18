<?php

namespace App\adms\Models;

use App\adms\Repositories\UsuariosRepository;

class NivelAcesso
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
    $data = $repo->getNivel($id);

    return new self(
      $id,
      $data['nome'],
      $data['descricao']
    );
  }
}