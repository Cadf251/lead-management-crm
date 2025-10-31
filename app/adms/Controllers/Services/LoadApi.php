<?php

namespace App\adms\Controllers\Services;

use App\adms\Helpers\GenerateLog;

/**
 * Tem um funcionamento parecido com LoadPage e verifica as classes e métodos e se é válido.
 */
class LoadApi
{
  /** @var string $classLoad Controller a ser carregado */
  private string $classLoad;

  /** @var array $listClasses As Classes disponíveis */
  private array $listClasses = [
    "NovoLead"
  ];

  /**
   * Carrega o método e repassa os credenciais para conexão e os dados de POST
   * 
   * @param string $className A Class já tratada com slug
   * @param array $credenciais As credenciais do TOKEN validado 
   * @param array $post O Array sem tratamento. Cuidado para não receber valores indesejados.
   */
  public function loadApi(string $className, array $credenciais, array $post):void
  {
    // Verifica se está no array
    if(!in_array($className, $this->listClasses)){
      GenerateLog::generateLog("error", "Classe chamada no API não existe.", ["class" => $className]);
      echo json_encode(["sucesso" => false, "mensagem" => "A classe não existe."]);
      exit;
    }

    // Verifica se a class existe
    $this->classLoad = "\\App\\api\\Controllers\\$className";

    if (!class_exists($this->classLoad)){
      GenerateLog::generateLog("error", "Classe chamada no API não é uma classe.", ["class" => $this->classLoad]);
      echo json_encode(["sucesso" => false, "mensagem" => "A classe não existe."]);
      exit;
    }
    
    // Chama o método index.
    $metodo = new $this->classLoad();

    if (!method_exists($metodo, "index")){
      GenerateLog::generateLog("error", "O método index não existe.", ["class" => $this->classLoad]);
      echo json_encode(["sucesso" => false, "mensagem" => "O método não existe."]);
      exit;
    } else {
      $metodo->{"index"}($credenciais, $post);
    }
  }
}