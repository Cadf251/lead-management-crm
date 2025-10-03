<?php

namespace App\adms\Controllers\dashboard;

use App\adms\Helpers\CSRFHelper;
use App\adms\Models\Repositories\DashboardRepository;
use App\adms\Models\Services\DbConnectionClient;
use App\adms\Views\Services\LoadViewService;
use DateTime;

class DashboardUsuarios
{
  /** @var array|string|null $data Valores enviados para a VIEW */
  private array|string|null $data = null;

  /** @var object|null $conexao A conexão com o banco do cliente */
  private object|null $conexao = null;

  public function index()
  {
    $this->data["title"] = "Dashboard Usuários";

    // Carrega a VIEW
    $loadView = new LoadViewService("adms/Views/dashboard/dashboard-usuarios/index", $this->data);
    $loadView->loadView();
  }
}