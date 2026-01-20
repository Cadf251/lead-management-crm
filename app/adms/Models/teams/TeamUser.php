<?php

namespace App\adms\Models\teams;

use App\adms\Models\traits\ComumId;
use DomainException;
use Exception;

/**
 * O usuário em uma equipe
 */
class TeamUser
{
  use ComumId;

  private int $userId;
  private string $userName;
  private ?bool $receive = false;
  private ?TeamUserFunction $function = null;
  private ?int $levelId;
  private int $time = 0;

  public const FUNCAO_COLABORADOR = TeamUserFunction::FUNCAO_COLABORADOR;
  public const FUNCAO_GERENTE = TeamUserFunction::FUNCAO_GERENTE;

  /**
   * Instancia um usuário eleito a essa equipe
   */
  public static function new(
    int $userId,
    string $userName,
    int $levelId
  ): self
  {
    $instance = new self();
    $instance->setUserId($userId);
    $instance->setUserName($userName);
    $instance->setLevelId($levelId);
    return $instance;
  }

  public function getUserId():int
  {
    return $this->userId;
  }

  public function getUserName():string
  {
    return $this->userName;
  }

  public function getFunctionId():int
  {
    return $this->function->getId();
  }

  public function getFuncionNome():string
  {
    return $this->function->getName();
  }

  public function getTime()
  {
    return $this->time;
  }

  public function getLevelId():int
  {
    return $this->levelId;
  }

  public function setUserId(int $usuarioId)
  {
    $this->userId = $usuarioId;
  }

  public function setUserName(string $nome)
  {
    $this->userName = $nome;
  }

  public function setReceiveLeads(bool $input)
  {
    $this->receive = $input;
  }

  public function setFunction(int $id)
  {
    try {
      $this->function = new TeamUserFunction($id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function setTime(int $vez)
  {
    $this->time = $vez;
  }

  public function setLevelId(int $levelId)
  {
    $this->levelId = $levelId;
  }

  public function dimishTime()
  {
    $this->time--;
  }

  public function increaseTime()
  {
    $this->time++;
  }

  public function canReceiveLeads():bool
  {
    return $this->receive;
  }
}
