<?php

use App\adms\Controllers\Services\PageController;

// Carregar o composer
require_once "vendor/autoload.php";

session_start();
ob_start();
ini_set("display_errors", 0);

date_default_timezone_set($_ENV['TIME_ZONE']);

// Instancia as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

// Instanciar a classe PageController, para tratar a URL
$url = new PageController();
$url->loadPage();
?>