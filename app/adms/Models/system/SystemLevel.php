<?php

namespace App\adms\Models\system;

use App\adms\Helpers\CreateOptions;
use App\adms\Models\traits\ComumObject;
use InvalidArgumentException;

class SystemLevel
{
  use ComumObject;

  public const NIVEL_COMUM = 1;
  public const NIVEL_ADM = 2;

  private const MAP = [
    self::NIVEL_COMUM => [
      'name' => 'Colaborador',
      'description' => 'Acesso padrão destinado a colaboradores, funcionários operacionais e membros da equipe comercial.',
    ],
    self::NIVEL_ADM => [
      'name' => 'Administrador',
      'description' => 'Possui acesso completo a todas as funcionalidades e configurações do sistema.',
    ],
  ];

  public function __construct(int $id)
  {
    $this->setById($id);
  }

  public function setById(int $id)
  {
    if (!isset(self::MAP[$id])) {
      throw new InvalidArgumentException("Nível inválido: $id");
    }

    $this->id = $id;
    $this->name = self::MAP[$id]['name'];
    $this->description = self::MAP[$id]['description'];
  }

  /**
   * Retorna as opções para um <select> em HTML.
   * 
   */
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
        "name" => $instance->getName()
      ];
    }

    return CreateOptions::create($array, $id);
  }

  public function canEditUsers(): bool
  {
    return $this->id === 2;
  }

  public function canEditTeams(): bool
  {
    return $this->id === 2;
  }

  public function canEditOffers(): bool
  {
    return $this->id === self::NIVEL_ADM;
  }
}
