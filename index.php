<?php

use App\adms\Controllers\Services\PageController;
use App\adms\Helpers\GenerateLog;

// Carregar o composer
require_once "vendor/autoload.php";

session_start();
ob_start();
ini_set("display_errors", 0);

date_default_timezone_set($_ENV['TIME_ZONE']);

// Observa erros fatais
register_shutdown_function(function () {
  $error = error_get_last();
  if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
    GenerateLog::generateLog('emergency', $error['message'], [
      'arquivo' => $error['file'],
      'linha' => $error['line'],
    ]);
  }
});


// Instancia as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

// Instanciar a classe PageController, para tratar a URL
$url = new PageController();
$url->loadPage();