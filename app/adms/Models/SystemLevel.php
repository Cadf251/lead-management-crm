<?php

namespace App\adms\Models;

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
      'nome' => 'Colaborador',
      'descricao' => 'Acesso padrão destinado a colaboradores, funcionários operacionais e membros da equipe comercial.',
    ],
    self::NIVEL_ADM => [
      'nome' => 'Administrador',
      'descricao' => 'Possui acesso completo a todas as funcionalidades e configurações do sistema.',
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
    $this->name = self::MAP[$id]['nome'];
    $this->description = self::MAP[$id]['descricao'];
  }

  /**
   * Retorna as opções para um <select> em HTML.
   * 
   * @fix A descrição não aparece na VIEW. Isso pode ser um problema
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

  public function podeEditarUsuarios(): bool
  {
    return $this->id === 2;
  }

  public function podeEditarEquipes(): bool
  {
    return $this->id === 2;
  }

  public function podeEditarOfertas(): bool
  {
    return $this->id === 2;
  }

  public function canEditOffers(): bool
  {
    return $this->id === self::NIVEL_ADM;
  }
}
