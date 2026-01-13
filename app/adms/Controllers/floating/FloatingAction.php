<?php

namespace App\adms\Controllers\floating;

use App\adms\Helpers\GenerateLog;

class FloatingAction
{
  private array $map = [
    "teste" => "app/adms/Views/floating/teste.php",
    "offer-team" => "app/adms/Views/equipes/vincular-oferta.php"
  ];

  public function index(?string $floatingId)
  {
    $html = "";

    if (isset($this->map[$floatingId])) {
      $html = $this->render($this->map[$floatingId]);
    }

    echo json_encode(["html" => $html]);
    exit;
  }

  private function render(string $path): string
  {
    $final = APP_ROOT.$path;
    
    if (is_file($final)) {
      return require APP_ROOT.$path;
    } else {
      GenerateLog::generateLog(GenerateLog::ERROR, "Invalid FILE", ["path" => $path]);
      return "";
    }
  }
}