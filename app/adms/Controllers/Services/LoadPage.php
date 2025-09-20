<?php

namespace App\adms\Controllers\Services;

use App\adms\Helpers\GenerateLog;

class LoadPage
{
  /** @var string $urlController Recebe da URL o nome da controller */
  private string $urlController = "";

  /** @var string $urlController Recebe da URL o parâmetro */
  private string $urlParameter = "";

  /** @var string $classLoad Controller a ser carregado */
  private string $classLoad;

  /** @var array $listDirectory Recebe a lista de diretórios com as controllers */
  private array $listPackages = ["adms"];

  /** @var array $listPgPublic Lista de páginas públicas */
  private array $listPgPublic = ["Login"];

  /** @var array $listDirectory Lista de diretório das classes */
  private array $listDirectory = ["login", "dashboard", "usuarios", "editar-usuario"];

  /** @var array $listPgPrivate Lista de páginas privadas */
  private array $listPgPrivate = ["Dashboard", "Usuarios", "EditarUsuario"];

    /**
   * Verificar se existe a página com o método checkPageExists
   * Verificar se e existe a classe com o método checkControllersExists
   * @param string $urlController Recebe da URL o nome da controller
   * @param string $urlParameter Recebe da URL o parâmetro
   * 
   * @return void
   */
  public function loadPage(string|null $urlController, string|null $urlParameter) :void
  {
    $this->urlController = $urlController;
    $this->urlParameter = $urlParameter;

    // Verificar se a página existe
    if (!$this->pageExists()){
      GenerateLog::generateLog("error", "Página não encontrada.", ["pagina" => $this->urlController, "parametro" => $this->urlParameter]);
      die("Página não encontrada!");
    }

    // Verifica se a class existe
    if (!$this->controllerExists()){
      GenerateLog::generateLog("error", "Classe não encontrada.", ["pagina" => $this->urlController, "parametro" => $this->urlParameter]);
      die("Classe não encontrada!");
    }

    // Chama o método
    $this->loadMetodo();
  }

  /**
   * Verificar se a página se existe em um array de páginas públicas
   * 
   * @return bool
   */
  private function pageExists() :bool
  {
    // Verifica se a página está no array de páginas públicas
    if (in_array($this->urlController, $this->listPgPublic))
      return true;
    
    // Verifica se a página está no array de páginas privadas
    if (in_array($this->urlController, $this->listPgPrivate))
      return true;
    
    return false;
  }

  /**
   * Verificar se a classe referente a página existe
   * 
   * @return bool
   */
  private function controllerExists() :bool
  {

    foreach ($this->listPackages as $pacote){

      foreach ($this->listDirectory as $directory){
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
  private function loadMetodo() :void
  {
    $classLoad = new $this->classLoad();

    if (method_exists($classLoad, "index"))
      $classLoad->{"index"}($this->urlParameter);
    else {
      GenerateLog::generateLog("error", "Método não encontrado.", ["pagina" => $this->urlController, "parametro" => $this->urlParameter]);
      die("Método não encontrado");
    }
  }
}