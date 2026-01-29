<?php

namespace App\database\Models;

use App\adms\Models\traits\ComumId;
use App\adms\Models\traits\ComumName;
use Exception;

class Tenant
{
  use ComumId;
  use ComumName;

  private Database $database;
  private string $contactEmail;
  private ?string $apiToken = null;
  private TenantStatus $status;

  public function setContactEmail(string $email): void
  {
    $this->contactEmail = $email;
  }

  public function setApiToken(?string $token): void
  {
    $this->apiToken = $token;
  }

  public function setDatabase(
    string $name,
    string $host,
    string $user,
    ?string $pass = null,
    ?string $version = null,
  ): void
  {
    $this->database = new Database(
      $name,
      $host,
      $user,
      $pass,
      $version,
    );
  }

  public function getContactEmail(): string
  {
    return $this->contactEmail;
  }

  public function getDatabase(): Database
  {
    return $this->database;
  }

  public function getApiToken(): ?string
  {
    return $this->apiToken;
  }

  public function getStatusId():int
  {
    return $this->status->getId();
  }

  public function getStatusName(): string
  {
    return $this->status->getName();
  }

  public function getStatusDescription(): string
  {
    return $this->status->getDescription();
  }

  public function setStatus(int $id)
  {
    try {
      $this->status = new TenantStatus($id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}
