<?php

namespace App\database\Models;

class Database
{
  private string $name;
  private string $host;
  private string $user;
  private ?string $pass = null;
  private int $port = 3306;
  private bool $isIstalled = false;

  public function __construct(
    string $name,
    string $host,
    string $user,
    ?string $pass = null,
    bool $isIstalled = false
  ) {
    $this->setName($name);
    $this->setHost($host);
    $this->setUser($user);
    $this->setPass($pass);
    $this->isIstalled = $isIstalled;
  }

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function setHost(string $host): void
  {
    $this->host = $host;
  }

  public function setUser(string $user): void
  {
    $this->user = $user;
  }

  /**
   * Localizar a senha no ENV
   */
  public function setPass(?string $pass): void
  {
    $this->pass = $pass;
  }

  public function setPort(int $port): void
  {
    $this->port = $port;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getHost(): string
  {
    return $this->host;
  }

  public function getUser(): string
  {
    return $this->user;
  }

  public function getPass(): ?string
  {
    return $this->pass;
  }

  public function getPort(): int
  {
    return $this->port;
  }

  public function canConnect(): bool
  {
    if (!$this->isIstalled) {
      return false;
    }

    if ($this->pass === null) {
      return false;
    }

    return true;
  }
}
