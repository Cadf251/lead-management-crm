<?php

namespace App\adms\Models\traits;

use App\adms\Helpers\GenerateLog;
use DateTime;
use Exception;

trait DateHandler
{
  /**
   * Converte qualquer entrada válida para um objeto DateTime.
   */
  protected function castToDateTime(DateTime|string|null $valor): ?DateTime
  {
    if ($valor instanceof DateTime) {
      return $valor;
    }

    if (is_string($valor) && !empty($valor)) {
      try {
        return new DateTime($valor);
      } catch (Exception $e) {
        GenerateLog::log($e, GenerateLog::ERROR, ["valor" => $valor]);
        return null;
      }
    }

    return null;
  }
}
