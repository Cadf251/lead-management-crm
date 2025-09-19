<?php

// Carregar o composer

use App\Controllers\Services\PageController;
use App\Models\Services\DbConnection;

require_once "../vendor/autoload.php";

// Instanciara a classe PageController, para tratar a URL
$url = new PageController();

$url->loadPage();

// $conn = new DbConnection(
//   "mysql.rercorretagemdeseguros.com.br",
//   "rercorretagemd",
//   "C01000100",
//   "rercorretagemd");

?>