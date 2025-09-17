<?php

// Carregar o composer

use App\Controllers\Services\PageController;

require_once "../vendor/autoload.php";

// Instanciara a classe PageController, para tratar a URL
$url = new PageController();

$url->loadPage();

?>