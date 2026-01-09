<?php

namespace App\adms\Models\traits;

trait ComumId
{
  private ?int $id;

  public function setId(?int $id): void
  {
    $this->id = $id;
  }

  public function getId(): int
  {
    return $this->id;
  }
}
