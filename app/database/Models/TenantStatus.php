<?php

namespace App\database\Models;

use App\adms\Models\StatusAbstract;
use InvalidArgumentException;

class TenantStatus extends StatusAbstract
{
  public const STATUS_DISABLED = 1;
  public const STATUS_ACTIVE = 2;

  protected function getMap(int $id): array
  {
    if (!in_array($id, [
      self::STATUS_DISABLED,
      self::STATUS_ACTIVE
    ])) {
      throw new InvalidArgumentException("Invalid Tenant Status: $id");
    }

    return match ($id) {
      self::STATUS_DISABLED => [
        "name" => "Ativo",
        "description" => null
      ],
      self::STATUS_ACTIVE => [
        "name" => "Desativado",
        "description" => null
      ],
    };
  }
}

// SENHA CARTÃO: 3217