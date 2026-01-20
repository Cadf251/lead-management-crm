<?php

namespace App\adms\Models\users;

use App\adms\Models\base\Person;
use App\adms\Models\SystemLevel;
use App\adms\Models\traits\StatusHandler;
use DomainException;
use Exception;
use App\adms\Models\UserStatus;

/**
 * Language: MIX
 * Modelo de USUÁRIO para ser instanciado e usado.
 */
class User extends Person
{
  use StatusHandler;

  private ?string $profilePicture;
  private ?string $passWordHash;
  private UserStatus $status;
  private SystemLevel $level;

  public static function new(
    string $name,
    string $email,
    string $phone,
    int $nivelId
  ): self {
    $usuario = new self();
    $usuario->setName($name);
    $usuario->setEmail($email);
    $usuario->setPhone($phone);
    $usuario->setPass(null);
    $usuario->setProfilePicture(null);
    $usuario->setStatus(UserStatus::STATUS_CONFIRMACAO);
    $usuario->setNivel($nivelId);
    return $usuario;
  }

  // |--------------------|
  // |  SETTERS           |
  // |--------------------|

  public function setPass(?string $passWordHash): void
  {
    if (empty($passWordHash)) $passWordHash = null;
    $this->passWordHash = $passWordHash;
  }

  public function setProfilePicture(?string $fotoType): void
  {
    if (!in_array($fotoType, [null, "png", "jpg", "jpeg"])) {
      throw new Exception("Formato não permitido");
    }

    $this->profilePicture = $fotoType;
  }

  public function setNivel(int $id)
  {
    try {
      $this->level = new SystemLevel($id);
    } catch (Exception $e) {
      throw new DomainException("Nivel inválido.", $e->getCode(), $e);
    }
  }

  public function setStatus(int $statusId)
  {
    try {
      $this->status = new UserStatus($statusId);
    } catch (Exception $e){
      throw new Exception("Invalid user status $statusId", $e->getCode(), $e);
    }
  }

  // |--------------------|
  // |  GETTERS           |
  // |--------------------|

  public function getProfilePicture():?string
  {
    return $this->profilePicture;
  }

  public function getPassWordHash():?string
  {
    return $this->passWordHash;
  }

  public function getSystemLevelId(): int
  {
    return $this->level->getId();
  }

  public function getSystemLevelName():string
  {
    return $this->level->getName();
  }

  public function getSystemLevelDescription():string
  {
    return $this->level->getDescription();
  }

  // --- STATUS CHANGERS ---

  /**
   * Reativa um usuário DESATIVADO
   */
  public function reactivate()
  {
    if ($this->status->getId() !== UserStatus::STATUS_DESATIVADO) {
      throw new DomainException("DOMAIN ERROR: Usuário deve estar desativado.");
    }

    $this->setStatus(UserStatus::STATUS_CONFIRMACAO);
  }

  /**
   * Retorna um usuário ativo ao status de aguardando confirmação
   */
  public function resetPassword()
  {
    if ($this->status->getId() !== UserStatus::STATUS_ATIVADO) {
      throw new DomainException("DOMAIN ERROR: Usuário deve estar ativado.");
    }

    $this->setStatus(UserStatus::STATUS_CONFIRMACAO);
    $this->setPass(null);
  }

  /**
   * Desativa qualquer usuário
   */
  public function disable(): void
  {
    if ($this->status->getId() === UserStatus::STATUS_DESATIVADO) {
      throw new DomainException("Usuário já está desativado.");
    }

    $this->setPass(null);
    $this->setStatus(UserStatus::STATUS_DESATIVADO);
  }

  /**
   * Ativa um usuário que está aguardando confirmação 
   */
  public function activate($novaSenhaHash): void
  {
    if ($this->status->getId() !== UserStatus::STATUS_CONFIRMACAO) {
      throw new DomainException("Usuário deve estar em estágio de confirmação.");
    }

    $this->setStatus(UserStatus::STATUS_ATIVADO);
    $this->setPass($novaSenhaHash);
  }

  // --- VERIFIERS ---
  /**
   * 
   * @return bool
   */
  public function canLoggin():bool
  {
    return (($this->passWordHash !== null) && ($this->status->getId() === UserStatus::STATUS_ATIVADO));
  }

  public function estaAguardandoConfirmacao():bool
  {
    return $this->status->getId() === UserStatus::STATUS_CONFIRMACAO;
  }
}
