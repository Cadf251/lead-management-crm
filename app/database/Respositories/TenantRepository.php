<?php

namespace App\database\Respositories;

use App\adms\Repositories\RepositoryBase;
use App\database\Models\Tenant;
use Exception;

class TenantRepository extends RepositoryBase
{
  public function list()
  {
    $query = <<<SQL
    SELECT
      t.id tenant_id, t.name tenant_name, t.contact_email tenant_email, t.api_token tenant_api_token, t.status_id tenant_status_id,
      d.name `db_name`, d.host `db_host`, d.user `db_user`, d.version `db_version`
    FROM tenants t
    INNER JOIN `database` d ON d.tenant_id = t.id
    SQL;

    return $this->sql->selectMultiple(
      $query,
      fn(array $row) => $this->hydrate($row));
  }

  public function hydrate(array $row): Tenant
  {
    extract($row);

    $tenant = new Tenant();
    $tenant->setId($tenant_id);
    $tenant->setName($tenant_name);
    $tenant->setContactEmail($tenant_email);
    $tenant->setApiToken($tenant_api_token);
    $tenant->setStatus($tenant_status_id);
    $tenant->setDatabase(
      $db_name,
      $db_host,
      $db_user,
      null,
      $db_version
    );

    return $tenant;
  }

  public function createTenant(Tenant $tenant): int
  {
    $paramsTenant = [
      "name" => $tenant->getName(),
      "contact_email" => $tenant->getContactEmail(),
      "api_token" => $tenant->getApiToken(),
      "status_id" => $tenant->getStatusId()
    ];

    $db = $tenant->getDatabase();

    try {
      $id = $this->sql->insert("tenants", $paramsTenant);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    $paramsDb = [
      "name" => $db->getName(),
      "host" => $db->getHost(),
      "user" => $db->getUser(),
      "version" => null,
      "tenant_id" => $id
    ];

    try {
      $this->sql->insert("database", $paramsDb);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    return $id;
  }
}