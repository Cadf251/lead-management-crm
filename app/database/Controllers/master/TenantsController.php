<?php

namespace App\database\Controllers\master;

use App\adms\Core\LoadView;
use App\database\Presenters\TenantPresenter;
use App\database\Services\TenantService;

class TenantsController
{
  private array $data = [
    "title" => "Master"
  ];

  private TenantService $service;

  public function __construct()
  {
    $this->service = new TenantService();
  }

  public function index()
  {
    $list = $this->service->list();
    $this->data["tenants"] = TenantPresenter::present($list);
    $view = new LoadView("database/Views/master/list-tenants", $this->data);
    $view->loadViewMaster();
  }

  public function create()
  {
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (isset($post)) {
      $result = $this->service->createTenant(
        $post["tenant_name"],
        $post["tenant_email"],
        $post["host"],
        $post["db_user"],
        $post["db_name"]
      );

      $this->data["result"] = $result;
    }

    $view = new LoadView("database/Views/master/create-tenant", $this->data);
    $view->loadViewMaster();
  }
}