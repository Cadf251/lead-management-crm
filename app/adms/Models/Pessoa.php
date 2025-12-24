<?php

namespace App\adms\Models;

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\NameFormatter;
use DomainException;
use InvalidArgumentException;

abstract class Pessoa
{
  public ?int $id;
  public string $nome;
  public string $email;
  public string $celular;

  public function setId(int $id): void
  {
    if (isset($this->id)) {
      throw new DomainException("Usuário já possui ID");
    }

    $this->id = $id;
  }

  public function setNome(string $nome): void
  {
    $nome = NameFormatter::formatarNome($nome);
    $this->nome = $nome;
  }

  public function setEmail(string $email): void
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException("E-mail inválido");
    }

    $this->email = $email;
  }

  public function setCelular(string $celular): void
  {
    if(!CelularFormatter::esInternacional($celular)){
      $celular = CelularFormatter::paraInternacional($celular);
    }
    $this->celular = $celular;
  }
}