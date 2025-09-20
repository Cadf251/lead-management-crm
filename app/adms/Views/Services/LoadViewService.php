<?php

namespace App\adms\Views\Services;

/** 
 * Carrega as páginas da View
 */

class LoadViewService
{
  /**
   * Receber o endereço da VIEW e os dados.
   * @param string $nameView Endereço da VIEW que deve ser carregada
   * @param array|string|null $data Dados que a VIEW deve receber.
   */
  public function __construct(private string $nameView, private array|string|null $data)
  {
      
  }

  /**
   * Carregar a VIEW.
   * Verificar se o arquivo existe, e carregar caso exista, não existindo para o carregamento
   * 
   * @return void
   */
  public function loadView(): void
  {
      if(file_exists('./app/' . $this->nameView . '.php')){
          include './app/' . $this->nameView . '.php';
      }else{
          die("O arquivo não existe");
      }
  }
}