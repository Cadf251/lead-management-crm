<?php

namespace App\adms\Models\base;

use App\adms\Helpers\Formatter;

abstract class Person
{
  private ?int $id;
  private string $name;
  private ?string $email;
  private ?string $phone;

  public function setId(int $id)
  {
    $this->id = $id;
  }

  public function setName(string $name)
  {
    $this->name = Formatter::name($name);
  }
}