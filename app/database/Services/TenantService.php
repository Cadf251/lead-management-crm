<?php

namespace App\database\Services;

use App\adms\Core\OperationResult;
use App\adms\Database\GlobalConn;
use App\adms\Helpers\GenerateLog;
use App\database\Infraestructure\MigrationRunner;
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
    $globalConn = GlobalConn::get();
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