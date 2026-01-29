<?php

namespace App\database\Respositories;

use App\adms\Repositories\RepositoryBase;

class SchemaRepository extends RepositoryBase
{
  public function getAppliedMigrations() {}

  public function hasMigrations() {}

  public function markAsApplied() {}
}