<?php

namespace App\adms\Helpers;

use App\adms\Views\Services\LoadViewService;

class ErrorHandler
{

  /**
   * Direciona para a tela de erro fatal e cria o log
   * 
   * deadEnd que dizer fim da linha. Essa função foi criada apenas para erros fundamentais, como na conexão com o banco de dados, que é crítico e faz com que todo o sistema fique comprometido.
   * 
   * @param string $h1 É o título da VIEW
   * @param string $descricao A descrição da VIEW
   * @param string $level O level do erro – consultar GenerateLog
   * @param string $mensagem A mensagem salva no log
   * @param array $errorInfo O array do erro
   * 
   * @return void
   */
  public static function deadEndError($h1, $descricao, string $level, string $mensagem, array $errorInfo) :void
  {
    GenerateLog::generateLog($level, $mensagem, $errorInfo);

    $dataView = [
      "title" => "Erro",
      "h1" => $h1,
      "descricao" => $descricao
    ];

    $loadError = new LoadViewService("adms/Views/erro/fatal", $dataView);
    $loadError->loadView();
  }
}