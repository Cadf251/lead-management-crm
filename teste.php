<?php

use App\adms\Models\Status;
use App\adms\Models\teams\Equipe;
use App\adms\Models\teams\EquipeUsuario;
use App\adms\UI\Field;
use Dom\Text;

// Carregar o composer
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

var_dump($_SESSION);
