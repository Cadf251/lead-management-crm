<?php

namespace App\adms\Core;

use App\adms\Models\SystemLevel;
use App\database\Models\Tenant;

/**
 * Regra de negócio criada para verificar se o usuário está logado ou não e direcionar para o login caso não esteja.
 */
class AuthUser
{
  private bool $loggedIn = false;
  private array $user;
  private Tenant $tenant;
  public SystemLevel $systemLevel;

  public static function create(): self
  {
    $auth = $_SESSION["auth"];

    // Verifica se existe o array
    if (empty($auth) || !$auth || $auth === null) {
      return self::createFalse();
    }

    $instance = new self();
    $instance->loggedIn = true;
    $instance->user = [
      "id" => (int)$auth["user_id"],
      "name" => (string)$auth["user_nome"],
      "email" => (string)$auth["user_email"],
      "profile_picture" => (string)$auth["profile_picture"]
    ];
    $instance->systemLevel = new SystemLevel((int)$auth["system_level_id"]);

    $tenant = new Tenant();
    $tenant->setId((int)$_SESSION["tenant_id"]);
    $tenant->setContactEmail($_SESSION["tenant_email"]);
    $tenant->setStatus(2);
    // $tenant->setDatabase()
    $instance->tenant = $tenant;
    return $instance;
  }

  public static function createFalse(): self
  {
    $instance = new self();
    $instance->loggedIn = false;
    return $instance;
  }


  public function isLoggedIn(): bool
  {
    return $this->loggedIn;
  }

  public function logout(): void
  {
    $_SESSION["auth"] = [];
    $this->loggedIn = false;
    header("Location: {$_ENV['HOST_BASE']}login");
    exit;
  }

  // |---------------|
  // |--- GETTERS ---|
  // |---------------|

  public function getUserId(): ?int
  {
    if (!$this->isLoggedIn()) {
      return null;
    }

    return $this->user["id"] ?? null;
  }

  public function getUserName(): ?string
  {
    if (!$this->isLoggedIn()) {
      return null;
    }

    return $this->user["name"] ?? null;
  }

  public function getUserEmail(): ?string
  {
    if (!$this->isLoggedIn()) {
      return null;
    }

    return $this->user["email"] ?? null;
  }

  public function getUserProfilePicture(): ?string
  {
    if (!$this->isLoggedIn()) {
      return null;
    }

    return $this->user["profile_picture"] ?? null;
  }

  public function getCredentials(): ?array
  {
    if (!$this->isLoggedIn()) {
      return null;
    }

    return $this->credentials ?? null;
  }

  public function getServerId(): ?int
  {
    if (!$this->isLoggedIn()) {
      return null;
    }

    return $this->serverId ?? null;
  }
}
