<?php

namespace App\adms\Models;

use Exception;
use InvalidArgumentException;

/**
 * language: EN
 */
abstract class StatusAbstract
{
  abstract protected function getMap(int $id):array;

  private int $id;
  private string $name;
  private string $description;

  public function __construct(int $id)
  {
    try {
      $this->id = $id;
      $info = $this->getMap($id);
      $this->name = $info["name"];
      $this->description = $info["description"];
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getDescription(): string
  {
    return $this->description;
  }
}