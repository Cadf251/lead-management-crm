<?php

namespace App\database\Services;

use App\adms\Core\AppContainer;
use App\adms\Core\OperationResult;
use App\adms\Database\DbConnectionClient;
use App\adms\Database\DbConnectionGlobal;
use App\adms\Helpers\GenerateLog;
use App\database\Models\Database;
use App\database\Models\Tenant;
use App\database\Models\TenantStatus;
use App\database\Respositories\TenantRepository;
use Exception;

class TenantService
{
  private OperationResult $result;
  private TenantRepository $repository;

  public function __construct()
  {
    $globalConn = AppContainer::getGlobalConn();
    $this->result = new OperationResult();
    $this->repository = new TenantRepository($globalConn);
  }

  public function list()
  {
    return $this->repository->list();
  }

  public function createTenant(
    string $name,
    string $contactEmail,
    string $host,
    string $dbUser,
    string $dbName,
  )
  {
    $tenant = new Tenant();

    $tenant->setName($name);
    $tenant->setContactEmail($contactEmail);
    $tenant->setStatus(TenantStatus::STATUS_DISABLED);
    $tenant->setDatabase(
      $dbName,
      $host,
      $dbUser
    );

    try {
      $id = $this->repository->createTenant($tenant);
      $this->result->addMessage("Salvo com sucesso no id: $id");
    } catch (Exception $e) {
      $this->result->failed("Não foi possível salvar o objeto");
      GenerateLog::log($e, GenerateLog::ERROR);
    }

    return $this->result;
  }

  public function installTenantDatabase(
    Tenant $tenant
  )
  {
    // Try to connect to database
    $db = $tenant->getDatabase();

    // If pass is not set, try to set it
    try {
      $this->setDbPass($db, $tenant->getId());
    } catch (Exception $e) {
      var_dump($e);
    }

    try {
      $conn = new DbConnectionClient([
        "host" => $db->getHost(),
        "db_name" => $db->getName(),
        "user" => $db->getUser(),
        "pass" => $db->getPass()
      ]);
    } catch (Exception $e) {
      var_dump($e);
    }

    // Get last SQL version available and install
    $files = scandir(APP_ROOT."app/database/sql/client/install");
    $count = count($files);
    $last = $files[$count - 1];
    $sql = file_get_contents(APP_ROOT."app/database/sql/client/install/$last");

    $queries = explode(";", $sql);

    if (empty($queries)) {
      var_dump("No queries found in $last file");
    }
  }

  /**
   * Verify if the DB pass is set. If it is not, set it
   * 
   * @throws Exception If pass could not be found in ENV file
   */
  private function setDbPass(Database $db, $tenantId): void
  {
    if ($db->getPass() === null) {
      if (!isset($_ENV["TENANT_DB_PASS_{$tenantId}"])) {
        throw new Exception("Unknow tenant password in ENV: {$tenantId}");
      }

      $pass = $_ENV["TENANT_DB_PASS_{$tenantId}"];
      $db->setPass($pass);
    }
  }
}