<?php

namespace App\adms\Models\leads;

use App\adms\Models\StatusAbstract;
use InvalidArgumentException;

/**
 * language: EN
 */
class JourneyStatus extends StatusAbstract
{
  public const STATUS_READY = 1;
  public const STATUS_FEEDING = 2;
  public const STATTUS_DISCARDED = 3;

  protected function getMap(int $id): array
  {
    if ($id < 1 || $id > 3){
      throw new InvalidArgumentException("Invalid LeadJorney Status.");
    }

    return match ($id) {
      self::STATTUS_DISCARDED => [
        'name' => "Descartado",
        "description" => "O lead não é qualificado para o produto ou perdeu o interesse."
      ],
      self::STATUS_FEEDING => [
        'name' => "Em Nutrição",
        "description" => "O lead ainda não tem os dados necessários para o produto."
      ],
      self::STATUS_READY => [
        'name' => "Pronto",
        "description" => "O lead está pronto para ser atendido."
      ]
    };
  }
}
