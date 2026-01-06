<?php

namespace App\adms\Services;

use App\adms\Models\NivelSistema;

/**
 * Regra de negócio criada para verificar se o usuário está logado ou não e direcionar para o login caso não esteja.
 */
class AuthUser
{
  private bool $logado = false;
  private array $usuario;
  private int $servidorId;
  private array $credenciais;
  public NivelSistema $nivelSistema;

  public static function create():self
  {
    $auth = $_SESSION["auth"];

    // Verifica se existe o array
    if (empty($auth) || !$auth || $auth === null) {
      return self::createFalse();
    }

    $instance = new self();
    $instance->logado = true;
    $instance->usuario = [
      "id" => (int)$auth["usuario_id"],
      "nome" => (string)$auth["usuario_nome"],
      "email" => (string)$auth["usuario_email"],
      "foto_perfil_tipo" => (string)$auth["foto_perfil_tipo"]
    ];
    $instance->servidorId = (int)$auth["servidor_id"];
    $instance->credenciais = (array)$auth["db_credenciais"];
    $instance->nivelSistema = new NivelSistema(2);
    return $instance;
  }

  public static function createFalse():self
  {
    $instance = new self();
    $instance->logado = false;
    return $instance;
  }

  public function estaLogado()
  {
    return $this->logado;
  }

  public function deslogar()
  {
    $_SESSION["auth"] = [];
    $this->logado = false;
    header("Location: {$_ENV['HOST_BASE']}login");
    exit;
  }

  // |---------------|
  // |--- GETTERS ---|
  // |---------------|

  public function getUsuarioId():?int
  {
    if (!$this->estaLogado()) {
      return null;
    }

    return $this->usuario["id"] ?? null;
  }

  public function getUsuarioNome():?string
  {
    if (!$this->estaLogado()) {
      return null;
    }

    return $this->usuario["nome"] ?? null;
  }

  public function getUsuarioEmail():?string
  {
    if (!$this->estaLogado()) {
      return null;
    }

    return $this->usuario["email"] ?? null;
  }

  public function getUsuarioFoto():?string
  {
    if (!$this->estaLogado()) {
      return null;
    }

    return $this->usuario["foto_perfil_tipo"] ?? null;
  }

  public function getCredenciais():?array
  {
    if (!$this->estaLogado()) {
      return null;
    }

    return $this->credenciais ?? null;
  }

  public function getServidorId():?int
  {
    if(!$this->estaLogado()) {
      return null;
    }

    return $this->servidorId ?? null;
  }

}