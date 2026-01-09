<?php

namespace App\api\Models;

use PDO;

class ApiClient
{
  private int $id;
  private string $email;
  private string $apiToken;
  private PDO $conn;

  public function __construct(
    int $id,
    string $email,
    string $apiToken,
    PDO $conn
  )
  {
    $this->id = $id;
    $this->email = $email;
    $this->apiToken = $apiToken;
    $this->conn = $conn;
  }

  public function getData(): array
  {
    return [];
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getApiToken(): string
  {
    return $this->apiToken;
  }

  public function getConn(): PDO
  {
    return $this->conn;
  }
}