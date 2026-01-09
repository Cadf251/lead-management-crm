<?php

namespace App\adms\Models;

use App\adms\Helpers\CreateOptions;
use Exception;
use InvalidArgumentException;

class NivelSistema
{
  private int $id;
  private string $nome;
  private ?string $descricao = null;

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
      throw new InvalidArgumentException('Nível inválido.');
    }

    $this->id = $id;
    $this->nome = self::MAP[$id]['nome'];
    $this->descricao = self::MAP[$id]['descricao'];
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getNome(): string
  {
    return $this->nome;
  }

  public function getDescricao(): ?string
  {
    return $this->descricao;
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
        "nome" => $instance->getNome()
      ];
    }

    return CreateOptions::criarOpcoes($array, $id);
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
