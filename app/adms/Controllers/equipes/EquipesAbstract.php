<?php

namespace App\adms\Controllers\equipes;

use App\adms\Repositories\EquipesRepository;
use App\adms\Database\DbConnectionClient;
use App\adms\Core\LoadView;
use App\adms\Models\teams\Equipe;
use App\adms\Presenters\EquipePresenter;
use App\adms\Services\EquipesService;

abstract class EquipesAbstract
{
  /** @var array $data Os dados que serão enviados para a VIEW */
  protected array $data = [
    "title" => "Listar Equipes"
  ];
  
  /** @var EquipesRepository $repo O repositório de equipes  */
  public EquipesRepository $repo;

  public EquipesService $service;

  /** Conecta com o banco de dados do cliente depois inicia o repositório de equipes */
  public function __construct(array|null $credenciais = null)
  {
    $conn = new DbConnectionClient($credenciais);
    $this->repo = new EquipesRepository($conn->conexao);
    $this->service = new EquipesService($conn->conexao);
  }

  /** 
   * Inclui no array $this->data valores adicionais que serão passados para o VIEW.
   */
  protected function setData(array $data): void
  {
    $this->data = array_merge($this->data, $data);
  }

  /**
   * Instancia e carrega a view.
   * 
   * @param string $viewPath O caminho relativo para a view
   */
  protected function render(string $viewPath): void
  {
    $loadView = new LoadView("adms/Views/equipes/$viewPath", $this->data);
    $loadView->loadView();
  }

  public function redirect($to = "listar-equipes"): void
  {
    header("Location: {$_ENV['HOST_BASE']}{$to}");
    exit;
  }

  /**
   * Faz a mágica acontencer. Renderiza o card final para a resposta do JSON
   */
  public function renderCard(Equipe $equipe){
    $equipes = EquipePresenter::present([$equipe]);
    $equipe = $equipes[0];
    return require APP_ROOT."/app/adms/Views/equipes/partials/equipe-card.php";
  }

}
