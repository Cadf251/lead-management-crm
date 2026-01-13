<?php

namespace App\adms\Core;

use App\adms\Helpers\GenerateLog;
use App\adms\Services\AuthUser;
use App\adms\UI\FloatingAction;

class LoadPage
{
  /** @var string $urlController Recebe da URL o nome da controller */
  private string $urlController = "";

  /** @var string $urlController Recebe da URL o parâmetro */
  private string $urlParameter = "";

  /** @var string $classLoad Controller a ser carregado */
  private string $classLoad;

  /** @var array $listDirectory Recebe a lista de diretórios com as controllers */
  private array $listPackages = ["adms", "database"];

  /** @var array $listDirectory Lista de diretório das classes */
  private array $listDirectory = ["login", "dashboard", "usuarios", "equipes", "atendimentos", "ofertas", "produtos", "floating", "erro", "master", "api"];

  /** @var array $listPgPublic Lista de páginas públicas */
  private array $listPgPublic = ["Login", "NovaSenha", "CriarSenha", "Erro", "Api"];

  /** @var array $listPgPrivate Lista de páginas privadas */
  private array $listPgPrivate = [
    "Deslogar",
    "Dashboard",
    "DashboardUsuarios",
    "DashboardEquipes",
    "ListarUsuarios",
    "ListarEquipes",
    "ListarColaboradores",
    "EmAtendimento",
    "ListarOfertas",
    "ListarProdutos"
  ];

  private array $listPost = [
    "CriarUsuario",
    "EditarUsuario",
    "DesativarUsuario",
    "ReativarUsuario",
    "ResetarSenha",
    "ReenviarEmail",
    "CriarEquipe",
    "EditarEquipe",
    "CongelarEquipe",
    "AtivarEquipe",
    "DesativarEquipe",
    "NovoColaborador",
    "AlterarFuncao",
    "AlterarRecebimento",
    "MudarVez",
    "RemoverColaborador",
    "CriarProduto",
    "EditarProduto",
    "DeletarProduto",
    "CriarOferta",
    "FloatingAction"
  ];

  /** @var array $listPgDev Páginas que só podem ser acessadas por DEVs */
  private array $listPgDev = [
    "ListarServidores",
    "InstalarServidor",
    "AtivarServidor",
    "AtualizarServidores"
  ];

  /**
   * Verificar se existe a página com o método checkPageExists
   * Verificar se e existe a classe com o método checkControllersExists
   * @param string $urlController Recebe da URL o nome da controller
   * @param string $urlParameter Recebe da URL o parâmetro
   * 
   * @return void
   */
  public function loadPage(string|null $urlController, string|null $urlParameter): void
  {
    $this->urlController = $urlController;
    $this->urlParameter = $urlParameter;

    // Verificar se a página existe
    $pageExists = $this->pageExists();

    if (!$pageExists[0])
      $this->falha("002. Página não encontrada.");
    else if ($pageExists[1] === "private") {
      // Requer login
      if (!AppContainer::getAuthUser()->estaLogado()) {
        header("Location: {$_ENV['HOST_BASE']}login");
        exit;
      }
    } else if ($pageExists[1] === "post") {
      // Permite só POST
      if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: {$_ENV['HOST_BASE']}dashboard");
        exit;
      }
    } else if (($pageExists[1] === "dev") && ($_SERVER['HTTP_HOST'] !== "crm.local")) {
      header("Location: {$_ENV['HOST_BASE']}login");
      exit;
    }

    // Verifica se a class existe
    if (!$this->controllerExists())
      $this->falha("002. Classe não encontrada.");

    // Chama o método
    $this->loadMetodo();
  }

  /**
   * Verificar se a página se existe e se a página é pública ou privada
   * 
   * @return array 0 => true ou false (se existe ou não); 1 => public ou private
   */
  private function pageExists(): array
  {
    // Verifica se a página está no array de páginas públicas
    if (in_array($this->urlController, $this->listPgPublic))
      return [true, "public"];

    // Verifica se a página está no array de páginas privadas
    if (in_array($this->urlController, $this->listPgPrivate))
      return [true, "private"];

    if (in_array($this->urlController, $this->listPost))
      return [true, "post"];

    // Verifica se a página está no array de devs
    if (in_array($this->urlController, $this->listPgDev))
      return [true, "dev"];

    return [false];
  }

  /**
   * Verificar se a classe referente a página existe
   * 
   * @return bool
   */
  private function controllerExists(): bool
  {

    foreach ($this->listPackages as $pacote) {

      foreach ($this->listDirectory as $directory) {
        $this->classLoad = "\\App\\$pacote\\Controllers\\$directory\\" . $this->urlController;

        if (class_exists($this->classLoad))
          return true;
      }
    }

    return false;
  }

  /**
   * Chama o método index se existir
   */
  private function loadMetodo(): void
  {
    $classLoad = new $this->classLoad();

    if (method_exists($classLoad, "index"))
      $classLoad->{"index"}($this->urlParameter);
    else {
      GenerateLog::generateLog("error", "Método não encontrado.", ["pagina" => $this->urlController, "parametro" => $this->urlParameter]);
      die("Método não encontrado");
    }
  }

  /**
   * Direciona o cabra para a tela de falha e interrompe o código
   * @param string $mensagem A mensagem do log de erro
   * @param string $location O local o usuário será enviado, pode ser login ou erro 404.
   */
  private function falha(string $mensagem): void
  {
    header("Location: {$_ENV['HOST_BASE']}erro/404");
    exit;
  }
}
