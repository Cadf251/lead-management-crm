<?php

namespace App\adms\Controllers\dashboard;

use App\adms\Helpers\CSRFHelper;
use App\adms\Repositories\DashboardRepository;
use App\adms\Database\DbConnectionClient;
use App\adms\Core\LoadView;
use DateTime;

class DashboardEquipes
{
  /** @var array|string|null $data Valores enviados para a VIEW */
  private array|string|null $data = null;

  /** @var object|null $conexao A conexão com o banco do cliente */
  private object|null $conexao = null;

  public function index()
  {
    $this->data["title"] = "Dashboard Equipes";

    // Carrega a VIEW
    $loadView = new LoadView("adms/Views/dashboard/dashboard-equipes/index", $this->data);
    $loadView->loadView();
  }
}