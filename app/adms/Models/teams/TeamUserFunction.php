<?php

namespace App\adms\Models\teams;

use App\adms\Helpers\CreateOptions;
use App\adms\Models\traits\ComumObject;
use App\adms\Models\StatusAbstract as ObjectAbstract;
use InvalidArgumentException;

class TeamUserFunction extends ObjectAbstract
{
  public const FUNCAO_COLABORADOR = 1;
  public const FUNCAO_GERENTE = 2;

  protected function getMap(int $id): array
  {
    if (!in_array($id, [
      self::FUNCAO_COLABORADOR,
      self::FUNCAO_GERENTE
    ])) {
      throw new InvalidArgumentException("Invalid TeamUser Function: $id");
    }

    return match($id) {
      self::FUNCAO_COLABORADOR => [
        "name" => "Colaborador",
        "description" => "Visualiza apenas os próprios leads e recursos atribuidos."
      ],
      self::FUNCAO_GERENTE => [
        "name" => "Gerente",
        "description" => "Visualiza os leads, recursos e desempenho de todos os usuários da equipe"
      ]
    };
  }

  public static function getSelectOptions(?int $id = null)
  {
    $instances = [
      new self(1),
      new self(2)
    ];

    $array = [];

    /** @var self $instance */
    foreach ($instances as $instance) {
      $array[] = [
        "id" => $instance->getId(),
        "nome" => $instance->getName()
      ];
    }

    return $array;
  }
}