<?php

namespace App\adms\Controllers\ofertas;

class CriarOferta
{
  public function index()
  {

    $content = require APP_ROOT."app/adms/Views/ofertas/criar-oferta.php";

    echo json_encode([
      "success" => true,
      "html" => $content
    ]);
  }
}