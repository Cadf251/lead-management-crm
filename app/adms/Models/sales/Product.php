<?php

namespace App\adms\Models\sales;

use App\adms\Models\traits\ComumObject;

class Product
{
  use ComumObject;
  
  private ?string $icon = null;

  public function __construct(
    ?int $id,
    string $name,
    ?string $description = null,
    ?string $icon = null
  )
  {
    $this->setId($id);
    $this->setName($name);
    $this->setDescription($description);
    $this->setIcon($icon);
  }

  public static function new(
    string $name,
    ?string $description = null
  ): self
  {
    return new self(
      null,
      $name,
      $description
    );
  }

  public function getIcon(): ?string
  {
    return $this->icon;
  }

  public function setIcon(?string $icon): void
  {
    $this->icon = $icon;
  }
}