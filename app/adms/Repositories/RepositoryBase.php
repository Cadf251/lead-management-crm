<?php

namespace App\adms\Repositories;

use App\adms\Database\DbOperationsRefactored as DbOperations;
use PDO;

abstract class RepositoryBase
{
  public DbOperations $sql;

  public function __construct(PDO $conn)
  {
    $this->sql = new DbOperations($conn);
  }
}