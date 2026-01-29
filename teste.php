<?php

// Carregar o composer

use App\adms\Core\AppContainer;
use App\adms\Services\AuthUser;
use App\adms\Services\TeamsService;
use App\database\Infraestructure\MigrationRunner;

require_once "vendor/autoload.php";

session_start();
ob_start();
ini_set("display_errors", 1);

// Instancia as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set($_ENV['TIME_ZONE']);

define('APP_ROOT', str_replace("\\", "/", __DIR__) . "/");

if ($_SERVER["HTTP_HOST"] === "crm.local") {
  $_ENV["HOST_BASE"] = "http://crm.local/";
}

// Instancia o AuthUser
$auth = AuthUser::create();
AppContainer::setAuthUser($auth);


$migrate = new MigrationRunner();
$migrate->getMigrations("client");
