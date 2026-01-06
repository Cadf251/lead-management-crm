<?php

use App\adms\Models\teams\Equipe;
use App\adms\Models\teams\EquipeUsuario;
use App\adms\UI\Field;

// Carregar o composer
require_once "vendor/autoload.php";

session_start();
ob_start();
ini_set("display_errors", 1);

// Instancia as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set($_ENV['TIME_ZONE']);

define('APP_ROOT', str_replace("\\", "/", __DIR__)."/");

if($_SERVER["HTTP_HOST"] === "crm.local"){
  $_ENV["HOST_BASE"] = "http://crm.local/";
}

$equipe = new Equipe();

$colaborador = new EquipeUsuario();
$colaborador->setId(0);
$colaborador->setRecebeLeads(true);
$colaborador->setVez(1);

$colaborador2 = new EquipeUsuario();
$colaborador2->setId(1);
$colaborador2->setRecebeLeads(true);
$colaborador2->setVez(0);

$colaborador3 = new EquipeUsuario();
$colaborador3->setId(2);
$colaborador3->setRecebeLeads(true);
$colaborador3->setVez(0);

$equipe->setColaboradores([
  $colaborador, $colaborador2, $colaborador3
]);

var_dump($equipe->getProximos());