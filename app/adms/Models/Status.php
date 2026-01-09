<?php

namespace App\adms\Models;

use Exception;
use InvalidArgumentException;

class Status extends StatusAbstract
{
  public const STATUS_DESATIVADO = 1;
  public const STATUS_PAUSADO = 2;
  public const STATUS_ATIVADO = 3;

  protected function getMap(int $id): array
  {
    if (!in_array($id, [
      self::STATUS_ATIVADO,
      self::STATUS_PAUSADO,
      self::STATUS_DESATIVADO
    ])) {
      throw new InvalidArgumentException("Invalid Status: $id");
    }

    return match ($id) {
      self::STATUS_ATIVADO => [
        'name' => "Ativo",
        "description" => null
      ],
      self::STATUS_PAUSADO => [
        'name' => "Pausado",
        "description" => null
      ],
      self::STATUS_DESATIVADO => [
        'name' => "Desativado",
        'description' => null
      ]
    };
  }
}
