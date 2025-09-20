<?php

// Carregar o composer

use App\adms\Controllers\Services\PageController;

require_once "vendor/autoload.php";

// Instancia as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

// Instanciar a classe PageController, para tratar a URL
$url = new PageController();
$url->loadPage();

?>