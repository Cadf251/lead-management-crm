<?php

namespace App\adms\Models\traits;

/**
 * Um objeto comum com id, nome e descricao.
 */
trait ComumObject
{
  private ?int $id;
  private string $name;
  private ?string $description = null;

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function setDescription(string $description): void
  {
    $this->description = $description;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }
}