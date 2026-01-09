<?php

namespace App\adms\Models\traits;

trait ComumDescription
{
  private ?string $description = null;

  public function setDescription(?string $description): void
  {
    if ($description === "") $description = null;
    $this->description = $description;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }
}