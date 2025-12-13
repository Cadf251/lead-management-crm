<?php

namespace App\adms\Controllers\equipes;

use App\adms\Models\Repositories\EquipesRepository;
use App\adms\Models\Services\DbConnectionClient;
use App\adms\Views\Services\LoadViewService;

abstract class EquipesAbstract
{
  /** @var array $data Os dados que serão enviados para a VIEW */
  protected array $data = [
    "title" => "Listar Equipes"
  ];

  /** @var int $id O ID da equipe */
  public int $id = 0;

  /** @var string $nome O nome da equipe */
  public string $nome;

  /** @var string $descricao A descrição da equipe */
  public ?string $descricao;

  /** @var int $produtoId O ID do produto da equipe */
  public int $produtoId;

  public const STATUS_DESATIVADO = 1;
  public const STATUS_PAUSADO = 2;
  public const STATUS_ATIVADO = 3;

  /** @var int $statusId O ID do status da equipe */
  public int $statusId = 0;

  /** @var EquipesRepository $repo O repositório de equipes  */
  public EquipesRepository $repo;

  /** Conecta com o banco de dados do cliente depois inicia o repositório de equipes */
  public function __construct(array|null $credenciais = null)
  {
    $conn = new DbConnectionClient($credenciais);
    $this->repo = new EquipesRepository($conn->conexao);
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
    $loadView = new LoadViewService("adms/Views/equipes/$viewPath", $this->data);
    $loadView->loadView();
  }

  public function redirect(): void
  {
    header("Location: {$_ENV['HOST_BASE']}listar-equipes");
    exit;
  }

  /**
   * Seta as informações da equipe pelo ID e trata erro se a equipe não existir
   * 
   * @param int $equipeId O ID da equipe
   */
  public function setInfoById(int $equipeId): void
  {
    $equipe = $this->repo->selecionarEquipe($equipeId);

    if (empty($equipe)) {
      $_SESSION["alerta"] = [
        "Aviso!",
        ["❌ Essa equipe não existe ou você não tem acesso a ela."]
      ];
      $this->redirect();
    }
   
    $this->id = $equipe["equipe_id"];
    $this->nome = $equipe["equipe_nome"];
    $this->descricao = $equipe["equipe_descricao"];
    $this->produtoId = $equipe["produto_id"];
    $this->statusId = $equipe["equipe_status_id"];
  }
}
