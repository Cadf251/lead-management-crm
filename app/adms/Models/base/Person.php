<?php

namespace App\adms\Models\base;

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\NameFormatter;
use App\adms\Models\traits\ComumId;
use App\adms\Models\traits\ComumName;
use InvalidArgumentException;

abstract class Person
{
  use ComumId;
  private string $name;
  private ?string $email;
  private ?string $phone;

  public function getName(): string
  {
    return $this->name;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getPhone(): string
  {
    return $this->phone;
  }

  public function setName(string $name): void
  {
    $name = NameFormatter::formatarNome($name);
    $this->name = $name;
  }

  public function setEmail(string $email): void
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException("E-mail inválido");
    }

    $this->email = $email;
  }

  public function setPhone(string $phone): void
  {
    if (!CelularFormatter::esInternacional($phone)) {
      $phone = CelularFormatter::paraInternacional($phone);
    }

    $this->phone = $phone;
  }
}
