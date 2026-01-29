<?php

namespace App\database\Infraestructure;

use App\adms\Database\DbOperations;
use App\database\Models\Database;
use App\database\Models\Tenant;
use App\database\Respositories\SchemaRepository;
use App\database\Respositories\TenantRepository;
use PDO;

class MigrationRunner
{
  private PDO $conn;
  private string $module;
  private TenantRepository $tenantRepo;
  private SchemaRepository $schemaRepo;

  public function __construct(
    PDO $conn,
    string $module
  )
  {
    $this->conn = $conn;
    $this->module = $module;
    $this->tenantRepo = new TenantRepository($conn);
    $this->schemaRepo = new SchemaRepository($conn);
  }

  public function install()
  {
    // List migrations
    $migrations = $this->getMigrations();

    if (empty($migrations)) return;

    // Foreach migrations, runn it
    foreach ($migrations as $name => $sql) {
      $this->runMigration($name, $sql);
    }
  }

  public function update()
  {
    // verify which migrations it doesnt have
  }

  public function runMigration(string $name, string $sql)
  {
    // run migration

    // save on repository the shema
  }

  public function getMigrations(): array
  {
    $types = [
      "install", "migrations"
    ];

    $queries = [];
    foreach ($types as $type) {
      $path = APP_ROOT."app/database/sql/{$this->module}/$type/";
      if (!is_dir($path)) continue;

      $files = scandir($path);

      foreach ($files as $file) {
        if ($file !== "." && $file !== "..") {
          $queries[$file] = file_get_contents("$path/$file");
        }
      }
    }

    ksort($queries);
    return $queries;
  }
}