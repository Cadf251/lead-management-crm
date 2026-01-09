<?php

namespace App\adms\Models\traits;

trait ComumName
{
  private string $name;

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function getName(): string
  {
    return $this->name;
  }

}