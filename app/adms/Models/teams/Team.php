<?php

namespace App\adms\Models\teams;

use App\adms\Models\Status;
use App\adms\Models\traits\ComumObject;
use App\adms\Models\traits\StatusHandler;
use DomainException;

class Team
{
  use ComumObject;
  use StatusHandler;

  private Status $status;
  private array $ofertas;

  private array $users = [];

  public const STATUS_DESATIVADO = Status::STATUS_DESATIVADO;
  public const STATUS_PAUSADO = Status::STATUS_PAUSADO;
  public const STATUS_ATIVADO = Status::STATUS_ATIVADO;

  public static function new(
    string $name,
    ?string $description
  ): self {
    $equipe = new self();
    $equipe->setName($name);
    $equipe->setDescription($description ?? null);
    $equipe->setStatus(self::STATUS_ATIVADO);
    return $equipe;
  }

  # | -------------------------|
  # | GETTERS                  |
  # | -------------------------|

  public function getUsers():array
  {
    return $this->users ?? [];
  }

  public function getAbleUsers(): array
  {
    $recebem = [];
    /** @var TeamUser $user */
    foreach ($this->users as $user) {
      if ($user->canReceiveLeads()) {
        $recebem[] = $user;
      }
    }
    return $recebem;
  }

  public function getNexts(int $quantidade = 3)
  {
    $recebem = $this->getAbleUsers();

    if (empty($recebem)) return [];

    $array = [];
    /** @var TeamUser $recebe */
    foreach ($recebem as $recebe) {
      $array[] = [
        "id" => $recebe->getId(),
        "vez" => $recebe->getTime()
      ];
    }

    for ($i = 0; $i < $quantidade; $i++) {
      // Ordena pelo menor VEZ e, em empate, menor ID
      usort($array, function ($a, $b) {
        if ($a["vez"] === $b["vez"]) {
          return $a["id"] <=> $b["id"];
        }
        return $a["vez"] <=> $b["vez"];
      });

      // Pega o OBJETO, não o array, do proximo
      $proximos[] = $this->getUserById($array[0]["id"]);

      // Incrementa a vez de quem foi escolhido
      $array[0]["vez"]++;
    }

    return $proximos ?? [];
  }

  public function getUserById(int $id): ?TeamUser
  {
    foreach ($this->users as $colaborador) {
      if ($colaborador->getId() === $id) {
        return $colaborador;
      }
    }
    return null;
  }

  public function getMinTime()
  {
    $vez = 0;

    if (empty($this->users)) return 0;

    foreach ($this->users as $colab) {
      if ($vez < $colab->getTime()) {
        $vez = $colab->getTime();
      }
    }

    return $vez;
  }

  public function countUsers(): int
  {
    return count($this->users);
  }

  // --- CHANGERS ---

  /**
   * Seta o ID para STATUS_DESATIVADO (1)
   * 
   * @throws DomainException
   */
  public function disable(): void
  {
    if ($this->status->getId() === self::STATUS_DESATIVADO) {
      throw new DomainException("Essa equipe já está desativada.");
    }

    $this->setStatus(self::STATUS_DESATIVADO);
  }

  /**
   * Pausa uma equipe STATUS_PAUSADO (2)
   * @throws DomainException
   */
  public function pause(): void
  {
    if ($this->status->getId() === self::STATUS_PAUSADO) {
      throw new DomainException("Essa equipe já está pausada.");
    }

    $this->setStatus(self::STATUS_PAUSADO);
  }

  /**
   * Ativa (ou despausa) uma equipe STATUS_ATIVADO (3)
   * 
   * @throws DomainException
   */
  public function activate(): void
  {
    if ($this->getStatusId() === self::STATUS_ATIVADO) {
      throw new DomainException("Essa equipe já está ativada.");
    }

    $this->setStatus(self::STATUS_ATIVADO);
  }

  # | -------------------------|
  # | SETTERS                  |
  # | -------------------------|

  /**
   * Um array de EquipesUsuario
   */
  public function setUsers(?array $colaboradores)
  {
    $this->users = $colaboradores ?? [];
  }

  public function setOneUser(TeamUser $user)
  {
    $this->users[] = $user;
  }

  public function removeUser(int $id)
  {
    /** @var EquipesUsuario $colab */
    foreach ($this->users as $key => $colab) {
      if ($colab->getId() === $id) {
        unset($this->users[$key]);
      }
    }
  }
}
