<?php

namespace App\adms\Core;

use App\adms\Helpers\ClearUrl;
use App\adms\Helpers\SlugController;

class PageController
{
  private string $url = "";
  private array $urlArray = [];
  private string $urlController = "";
  private string $urlMethod = "";
  private ?string $urlParameter = null;

  /**
   * Lembrando que o PageController não sabe inglês. Ele tem que reparar a url em pt.
   */
  public function __construct()
  {
    $url = filter_input(INPUT_GET, "url", FILTER_DEFAULT);

    if (!empty($url)) {
      $this->url = ClearUrl::clearUrl($url);
      $this->urlArray = explode("/", $this->url);

      // 1. Define o Controller
      $this->urlController = $this->urlArray[0] ?? "login";

      // 2. Define o Método (Ação) - Se não existir, deixamos vazio
      if (isset($this->urlArray[1])) {
        $this->urlMethod = $this->urlArray[1];
      }

      // 3. Define o Parâmetro (ID)
      $this->urlParameter = isset($this->urlArray[2])
        ? $this->urlArray[2]
        : null;
    } else {
      $this->urlController = "login";
    }
  }

  public function loadPage(): void
  {
    $loadPage = new LoadPage();

    // Agora passamos os 3 argumentos para o LoadPage
    // Se o seu LoadPage ainda recebe apenas 2, precisaremos ajustar a assinatura dele também!
    $loadPage->loadPage($this->urlController, $this->urlMethod, $this->urlParameter);
  }
}