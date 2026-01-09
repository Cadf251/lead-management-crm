<?php

namespace App\api\Service;

use Exception;

class ApiService
{
  public function cleanKeys(array $keys, array $data): array
  {
    return array_intersect_key($data, array_flip($keys));
  }

  public function checkKeys(array $min_keys, $data): void
  {
    foreach ($min_keys as $key){
      if (!isset($data[$key])) {
        throw new Exception("Está faltando uma chave mínima na requisição: $key");
      }
    }
  }
}