<?php

namespace App\adms\Controllers\Services;

use App\adms\Helpers\ClearUrl;
use App\adms\Helpers\SlugController;

/**
 * Recebe a URL e direciona para outros Controllers
 * 
 * @author Cadu Prado cadu.devmarketing@gmail.com
 */
class PageController
{
  /** @var string $url Recebe a URL do .htaccess */
  private string $url;

  /** @var array $urlArray Recebe a URL convertida em array */
  private array $urlArray = [];

  /** @var string $urlController Recebe da URL o nome da controller */
  private string $urlController = "";

  /** @var string $urlController Recebe da URL o parâmetro */
  private string $urlParameter = "";

  /** Recebe a URL do .htaccess */
  public function __construct()
  {
    // Verifica se tem valor da $url
    $url = filter_input(INPUT_GET, "url", FILTER_DEFAULT);

    if (!empty($url)){
      // Recebe o valor
      $this->url = $url;

      // Chama o helper para limpar a URL
      $this->url = ClearUrl::clearUrl($this->url);

      // Converte a url em array
      $this->urlArray = explode("/", $this->url);

      // Verifica se existe a controller da URL
      if (isset($this->urlArray[0])){
        $this->urlController = SlugController::slugController($this->urlArray[0]);
      } else {
        $this->urlController = SlugController::slugController("Login");
      }

      // Verifique se existe o parâmetro da URL
      if (isset($this->urlArray[1])){
        $this->urlParameter = $this->urlArray[1];
      }

    } else {
      $this->urlController = SlugController::slugController("Login");
    }
  }

  /**
   * Carregar página/controller 
   * Instanciar a classe para validar e carregar página/controller 
   *
   * @return void
   */
  public function loadPage() :void
  {
    $loadPage = new LoadPage();
    
    $loadPage->loadPage($this->urlController, $this->urlParameter);
  }
}