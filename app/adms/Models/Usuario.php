<?php

namespace App\adms\Models;

use DomainException;
use Exception;
use App\adms\Models\UserStatus;

/**
 * Language: MIX
 * Modelo de USUÁRIO para ser instanciado e usado.
 */
class Usuario extends Pessoa
{
  private ?string $foto;
  private ?string $senhaHash;
  private UserStatus $status;
  private NivelSistema $nivel;

  public static function novo(
    string $nome,
    string $email,
    string $celular,
    int $nivelId
  ): self {
    $usuario = new self();
    $usuario->setNome($nome);
    $usuario->setEmail($email);
    $usuario->setCelular($celular);
    $usuario->setSenha(null);
    $usuario->setFoto(null);
    $usuario->setStatus(UserStatus::STATUS_CONFIRMACAO);
    $usuario->setNivel($nivelId);
    return $usuario;
  }

  // |--------------------|
  // |  SETTERS           |
  // |--------------------|

  public function setSenha(?string $senhaHash): void
  {
    if (empty($senhaHash)) $senhaHash = null;
    $this->senhaHash = $senhaHash;
  }

  public function setFoto(?string $fotoType): void
  {
    if (!in_array($fotoType, [null, "png", "jpg", "jpeg"])) {
      throw new Exception("Formato não permitido");
    }

    $this->foto = $fotoType;
  }

  public function setNivel(int $id)
  {
    try {
      $this->nivel = new NivelSistema($id);
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

  public function getFoto():?string
  {
    return $this->foto;
  }

  public function getSenhaHash():?string
  {
    return $this->senhaHash;
  }

  public function getStatusNome():string
  {
    return $this->status->getName();
  }

  public function getStatusDescricao():string
  {
    return $this->status->getDescription();
  }

  public function getStatusId():int
  {
    return $this->status->getId();
  }

  public function getNivelAcessoId():int
  {
    return $this->nivel->getId();
  }

  public function getNivelAcessoNome():string
  {
    return $this->nivel->getNome();
  }

  public function getNivelAcessoDescricao():string
  {
    return $this->nivel->getDescricao();
  }

  // --- STATUS CHANGERS ---

  /**
   * Reativa um usuário DESATIVADO
   */
  public function reativar()
  {
    if ($this->status->getId() !== UserStatus::STATUS_DESATIVADO) {
      throw new DomainException("DOMAIN ERROR: Usuário deve estar desativado.");
    }

    $this->setStatus(UserStatus::STATUS_CONFIRMACAO);
  }

  /**
   * Retorna um usuário ativo ao status de aguardando confirmação
   */
  public function resetarSenha()
  {
    if ($this->status->getId() !== UserStatus::STATUS_ATIVADO) {
      throw new DomainException("DOMAIN ERROR: Usuário deve estar ativado.");
    }

    $this->setStatus(UserStatus::STATUS_CONFIRMACAO);
    $this->setSenha(null);
  }

  /**
   * Desativa qualquer usuário
   */
  public function desativar(): void
  {
    if ($this->status->getId() === UserStatus::STATUS_DESATIVADO) {
      throw new DomainException("Usuário já está desativado.");
    }

    $this->setSenha(null);
    $this->setStatus(UserStatus::STATUS_DESATIVADO);
  }

  /**
   * Ativa um usuário que está aguardando confirmação 
   */
  public function ativar($novaSenhaHash): void
  {
    if ($this->status->getId() !== UserStatus::STATUS_CONFIRMACAO) {
      throw new DomainException("Usuário deve estar em estágio de confirmação.");
    }

    $this->setStatus(UserStatus::STATUS_ATIVADO);
    $this->setSenha($novaSenhaHash);
  }

  // --- VERIFIERS ---
  /**
   * 
   * @return bool
   */
  public function podeLogar():bool
  {
    return (($this->senhaHash !== null) && ($this->status->getId() === UserStatus::STATUS_ATIVADO));
  }

  public function estaAguardandoConfirmacao():bool
  {
    return $this->status->getId() === UserStatus::STATUS_CONFIRMACAO;
  }
}
