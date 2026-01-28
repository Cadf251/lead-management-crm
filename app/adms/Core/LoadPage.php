<?php

namespace App\adms\Core;

use App\adms\Helpers\GenerateLog;

class LoadPage
{
  private string $urlController = "";
  private string $urlMethod = "";
  private string $urlParameter = "";

  /** @var array $routes Transforma uma URL amigável em class */
  private array $admsRoutes = [
    "login"         => ["login\LoginController", "index", self::ACCESS_PUBLIC],
    "deslogar"      => ["login\LoginController", "logout", self::ACCESS_PUBLIC],
    "esqueci-senha" => ["login\LoginController", "forgotPass", self::ACCESS_PUBLIC],
    "nova-senha"    => ["login\LoginController", "createPass", self::ACCESS_PUBLIC],

    "dashboard"     => ["dashboard\Dashboard", "index", self::ACCESS_PRIVATE],

    "usuarios"                => ["users\UsersController", "index", self::ACCESS_PRIVATE],
    "usuarios/listar"         => ["users\UsersController", "list", self::ACCESS_PRIVATE],
    "usuarios/editar"         => ["users\UsersController", "edit", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "usuarios/criar"          => ["users\UsersController", "create", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "usuarios/desativar"      => ["users\UsersController", "disable", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "usuarios/resetar-senha"  => ["users\UsersController", "resetPassword", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "usuarios/reativar"       => ["users\UsersController", "reactivate", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "usuarios/reenviar-email" => ["users\UsersController", "resendMail", self::ACCESS_PRIVATE, self::AJAX_ONLY],

    "erro" => ["erro\Erro", "index", self::ACCESS_PUBLIC],

    "equipes"           => ["teams\TeamsController", "index", self::ACCESS_PRIVATE],
    "equipes/criar"     => ["teams\TeamsController", "create", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "equipes/editar"    => ["teams\TeamsController", "edit", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "equipes/ativar"    => ["teams\TeamsController", "activate", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "equipes/pausar"    => ["teams\TeamsController", "pause", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "equipes/desativar" => ["teams\TeamsController", "disable", self::ACCESS_PRIVATE, self::AJAX_ONLY],

    "colaboradores"                     => ["teams\TeamUsersController", "index", self::ACCESS_PRIVATE],
    "colaboradores/novo"                => ["teams\TeamUsersController", "add", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "colaboradores/remover"             => ["teams\TeamUsersController", "remove", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "colaboradores/alterar-funcao"      => ["teams\TeamUsersController", "changeFunction", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "colaboradores/alterar-recebimento" => ["teams\TeamUsersController", "changeReceiving", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    "colaboradores/alterar-vez"         => ["teams\TeamUsersController", "changeTime", self::ACCESS_PRIVATE, self::AJAX_ONLY],
    
    "ofertas"   => ["Offer", self::ACCESS_PRIVATE],
    "produtos"  => ["Products", self::ACCESS_PRIVATE]
  ];

  private const ACCESS_PUBLIC = "public";
  private const ACCESS_PRIVATE = "private";
  private const ACCESS_DEV = "dev";
  private const AJAX_ONLY = true;

  private string $classLoad;

  /**
   * Verificar se existe a página com o método checkPageExists
   * Verificar se e existe a classe com o método checkControllersExists
   * @param string $urlController Recebe da URL o nome da controller
   * @param string $urlParameter Recebe da URL o parâmetro
   * 
   * @return void
   */
  public function loadPage(string|null $urlController, ?string $urlMethod, string|null $urlParameter): void
  {
    // 1. Monta a chave de busca (ex: "usuarios/editar" ou "usuarios")
    $routeKey = $urlMethod ? "$urlController/$urlMethod" : $urlController;
    
    // 2. Se não achou a combinação, tenta apenas o controller (fallback para index)
    if (!isset($this->admsRoutes[$routeKey])) {
      $routeKey = $urlController;
    }

    GenerateLog::generateLog("info", "route-key", [$this->admsRoutes[$routeKey]]);

    if (!isset($this->admsRoutes[$routeKey])) {
      $this->failed();
    }

    $routerInfo = $this->admsRoutes[$routeKey];
    $this->urlController = $routerInfo[0];
    $this->urlMethod     = $routerInfo[1];
    $accessLevel         = $routerInfo[2];

    // 2. Controller existe?
    if (!$this->controllerExists("adms")) {
      $this->failed();
    }

    // 3. Verificação de Segurança
    $this->checkSecurity($accessLevel);

    $this->urlParameter = $urlParameter ?? $urlMethod;

    $this->loadMethod();
  }

  private function controllerExists(string $package): bool
  {
    $this->classLoad = "\\App\\$package\\Controllers\\" . $this->urlController;
    return class_exists($this->classLoad);
  }

  private function loadMethod()
  {
    $classPath = $this->classLoad;
    $controller = new $classPath();

    // Se o método existe, chama. Se não, tenta o index (fallback antigo)
    if (method_exists($controller, $this->urlMethod)) {
      $controller->{$this->urlMethod}($this->urlParameter);
    } elseif (method_exists($controller, 'index')) {
      $controller->index($this->urlParameter);
    } else {
      $this->failed();
    }
  }

  private function checkSecurity(string $level)
  {
    if ($level == self::ACCESS_PRIVATE) {
      if (!AppContainer::getAuthUser()->isLoggedIn()) {
        $this->redirectLogin();
      }
    } else if ($level === self::ACCESS_DEV) {
      if ($_SERVER["HTTP_HOST"] !== "crm.local") {
        $this->failed();
      }
    }
  }

  private function failed(): void
  {
    GenerateLog::generateLog("debug", "failed for some reason", null);
    header("Location: {$_ENV['HOST_BASE']}erro/404");
    exit;
  }

  private function redirectLogin(): void
  {
    header("Location: {$_ENV['HOST_BASE']}login");
    exit;
  }
}
