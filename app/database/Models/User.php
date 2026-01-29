<?php

namespace App\database\Models;

use App\adms\Models\traits\ComumId;
use InvalidArgumentException;

class User
{
  use ComumId;

  private string $email;
  private ?string $passWordHash;

  /** @var array<Tenant> $tenants */
  private array $tenants;

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getPassWordHash():?string
  {
    return $this->passWordHash;
  }

  public function setEmail(string $email): void
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException("Invalid E-mail: $email");
    }

    $this->email = $email;
  }

  public function setPass(?string $passWordHash): void
  {
    if (empty($passWordHash)) $passWordHash = null;
    $this->passWordHash = $passWordHash;
  }
}