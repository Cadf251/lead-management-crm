<?php

namespace App\adms\Database;

use App\database\Models\Database;

class GlobalConn
{
  use ConnBase;

  protected static function getDb(): ?Database
  {
    return new Database(
      $_ENV["DB_NAME"],
      $_ENV["DB_HOST"],
      $_ENV["DB_USER"],
      $_ENV["DB_PASS"],
      true
    );
  }
}