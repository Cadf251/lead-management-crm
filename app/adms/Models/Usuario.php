<?php

namespace App\adms\Models;

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\NameFormatter;
use App\adms\Repositories\UsuariosRepository;
use DomainException;
use Exception;
use InvalidArgumentException;

/**
 * Modelo de USUÁRIO para ser instanciado e usado.
 */
class Usuario
{
  public ?int $id;
  public string $nome;
  public string $email;
  public string $celular;
  public ?string $foto;
  public ?string $senhaHash;
  public UsuarioStatus $status;
  public NivelAcesso $nivel;

  public const STATUS_CONFIRMACAO = 1;
  public const STATUS_DESATIVADO = 2;
  public const STATUS_ATIVADO = 3;

  public const NIVEL_COLABORADOR = 1;
  public const NIVEL_FINANCEIRO = 2;
  public const NIVEL_GERENTE = 3;
  public const NIVEL_ADMIN = 4;

  public static function novo(
    string $nome,
    string $email,
    string $celular,
    int $nivelId,
    $repo
  ): self {
    $usuario = new self();
    $usuario->setNome($nome);
    $usuario->setEmail($email);
    $usuario->setCelular($celular);
    $usuario->setSenha(null);
    $usuario->setFoto(null);
    $usuario->setStatusById(self::STATUS_CONFIRMACAO, $repo);
    $usuario->setNivelById($nivelId, $repo);
    return $usuario;
  }

  /**
   * Reativa um usuário DESATIVADO
   * 
   * @param UsuariosRepository $repo
   */
  public function reativar(UsuariosRepository $repo)
  {
    if ($this->status->id !== self::STATUS_DESATIVADO) {
      throw new DomainException("DOMAIN ERROR: Usuário deve estar desativado.");
    }

    $this->setStatusById(self::STATUS_CONFIRMACAO, $repo);
  }

  /**
   * Retorna um usuário ativo ao status de aguardando confirmação
   * 
   * @param UsuariosRepository $repo
   */
  public function resetarSenha(UsuariosRepository $repo)
  {
    if ($this->status->id !== self::STATUS_ATIVADO) {
      throw new DomainException("DOMAIN ERROR: Usuário deve estar ativado.");
    }

    $this->setStatusById(self::STATUS_CONFIRMACAO, $repo);
    $this->setSenha(null);
  }

  /**
   * Ativa um usuário que está aguardando confirmação
   * 
   * @param UsuariosRepository $repo
   */
  public function ativar(UsuariosRepository $repo, $novaSenhaHash): void
  {
    if ($this->status->id !== self::STATUS_CONFIRMACAO) {
      throw new DomainException("Usuário deve estar em estágio de confirmação.");
    }

    $this->setStatusById(self::STATUS_ATIVADO, $repo);
    $this->setSenha($novaSenhaHash);
  }

  public function podeLogar()
  {
    return (($this->senhaHash !== null) && ($this->status->id === self::STATUS_ATIVADO));
  }

  /**
   * Desativa qualquer usuário
   * 
   * @param UsuariosRepository $repo
   */
  public function desativar(UsuariosRepository $repo): void
  {
    if ($this->status->id === self::STATUS_DESATIVADO) {
      throw new DomainException("Usuário já está desativado.");
    }

    $this->setSenha(null);
    $this->setStatusById(self::STATUS_DESATIVADO, $repo);
  }

  // |--------------------|
  // |  SETTERS           |
  // |--------------------|

  public function setId(int $id): void
  {
    if (isset($this->id)) {
      throw new DomainException("Usuário já possui ID");
    }

    $this->id = $id;
  }

  public function setNome(string $nome): void
  {
    $nome = NameFormatter::formatarNome($nome);
    $this->nome = $nome;
  }

  public function setEmail(string $email): void
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException("E-mail inválido");
    }

    $this->email = $email;
  }

  public function setCelular(string $celular): void
  {
    if(!CelularFormatter::esInternacional($celular)){
      $celular = CelularFormatter::paraInternacional($celular);
    }
    $this->celular = $celular;
  }

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

  public function setNivel(int $nivId, string $nivNome, string $descricao = ""): void
  {
    if (!in_array($nivId, [
      self::NIVEL_COLABORADOR,
      self::NIVEL_FINANCEIRO,
      self::NIVEL_GERENTE,
      self::NIVEL_ADMIN
    ])) {
      throw new InvalidArgumentException(("Nível de acesso inválido."));
    }

    $this->nivel = new NivelAcesso($nivId, $nivNome, $descricao);
  }

  public function setNivelById(int $id, UsuariosRepository $repo)
  {
    if (!in_array($id, [
      self::NIVEL_COLABORADOR,
      self::NIVEL_FINANCEIRO,
      self::NIVEL_GERENTE,
      self::NIVEL_ADMIN
    ])) {
      throw new InvalidArgumentException("Nível de acesso inválido.");
    }

    $this->nivel = NivelAcesso::fromId($id, $repo);
  }

  public function setStatus(int $statusId, string $statusNome, string $descricao = "")
  {
    if (!in_array($statusId, [
      self::STATUS_CONFIRMACAO,
      self::STATUS_DESATIVADO,
      self::STATUS_ATIVADO
    ])) {
      throw new InvalidArgumentException("Status inválido.");
    }

    $this->status = new UsuarioStatus($statusId, $statusNome, $descricao);
  }

  public function setStatusById(int $id, UsuariosRepository $repo)
  {
    if (!in_array($id, [
      self::STATUS_CONFIRMACAO,
      self::STATUS_DESATIVADO,
      self::STATUS_ATIVADO
    ])) {
      throw new InvalidArgumentException("Status inválido.");
    }

    $this->status = UsuarioStatus::fromId($id, $repo);
  }
}
