<?php

use App\adms\Controllers\erro\Erro;
use App\adms\Controllers\Services\PageController;
use App\adms\Helpers\GenerateLog;

// Carregar o composer
require_once "vendor/autoload.php";

session_start();
ob_start();
ini_set("display_errors", 0);

// Instancia as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set($_ENV['TIME_ZONE']);

define('APP_ROOT', str_replace("\\", "/", __DIR__)."/");

// Observa erros fatais
register_shutdown_function( function () {
  $error = error_get_last();
  if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
    GenerateLog::generateLog(
      'critical',
      $error["message"], 
      [
        'arquivo' => $error['file'],
        'linha' => $error['line'],
      ]
    );
    $er = new Erro();
    $er->index("500");
    exit;
  }
});

// Instanciar a classe PageController, para tratar a URL
$url = new PageController();
$url->loadPage();
