<?php

namespace App\api\Controllers;

use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\NotificarErro;
use Exception;

class AtualizarPerfil
{
  private string $table = "atendimento_perfil";
  private array $credenciais;
  private array $data;
  private array $chaves = ["atendimento_id", "tipo", "contexto", "dados"];
  private array $result;
  private array $errorData = [];

  public function index(array $credenciais, array $post): void
  {
    $this->credenciais = $credenciais;
    $this->data = array_intersect_key($post, array_flip($this->chaves));
    unset($post);

    try {
      $this->tratarJson();
    } catch (Exception $e) {
      if($e->getMessage() === "JSON contém chaves proibidas") {
        $this->notificar($e->getMessage(), ["JSON_INPUT" => $this->data["dados"]]);
        GenerateLog::generateLog("alert", $e->getMessage(), [
          $this->data["dados"]
        ]);
      } else {
        GenerateLog::generateLog("error", $e->getMessage(), [
          $this->data["dados"]
        ]);
      }

      echo json_encode([
        "sucesso" => false,
        "mensagem" => "Input JSON inválido.",
        "error" => $e->getMessage()
      ]);
      exit;
    }
  }

  public function mesclarPerfil(){
    
  }

  /**
   * O CRM aceita apenas JSON válido, com tipo raiz objeto,
   * profundidade limitada, tamanho máximo definido
   * e bloqueio de chaves potencialmente perigosas.
   * O conteúdo do JSON não é interpretado pelo sistema.
   */
  private function tratarJson()
  {
    $input = $this->data["dados"];

    if (strlen($input) > 65535) {
      throw new Exception('Payload muito grande');
    }

    $dadosArray = json_decode($input, true, 20, JSON_THROW_ON_ERROR);

    if (!is_array($dadosArray) || array_is_list($dadosArray)) {
      throw new Exception('JSON deve ser um objeto');
    }

    $chavesPerigosas = [
      '__proto__',
      'prototype',
      'constructor',
      'constructor.prototype',
      '__defineGetter__',
      '__defineSetter__',
      '__lookupGetter__',
      '__lookupSetter__'
    ];

    if (contemChavesPerigosas($dadosArray, $chavesPerigosas)) {
      throw new Exception('JSON contém chaves proibidas');
    }

    function contemChavesPerigosas(array $data, array $blacklist): bool
    {
      foreach ($data as $key => $value) {
        if (is_string($key) && in_array($key, $blacklist, true)) {
          return true;
        }

        if (is_array($value)) {
          if (contemChavesPerigosas($value, $blacklist)) {
            return true;
          }
        }
      }

      return false;
    }
  }

  private function notificar(string $descricao, array $info)
  {
    NotificarErro::notificar($descricao, $info);
  }
}
