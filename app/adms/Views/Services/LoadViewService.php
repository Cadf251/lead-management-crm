<?php

namespace App\adms\Views\Services;

/** 
 * Carrega as páginas da View
 */


class LoadViewService
{
  /** @var string $view Recebe o endereço da VIEW */
  private string $view;
  
  /**
   * Receber o endereço da VIEW e os dados.
   * @param string $nameView Endereço da VIEW que deve ser carregada
   * @param array|string|null $data Dados que a VIEW deve receber.
   */
  public function __construct(private string $nameView, private array|string|null $data)
  {
    // Inicializa os parâmetros
  }

  /**
   * Carregar a VIEW.
   * Verificar se o arquivo existe, e carregar caso exista, não existindo para o carregamento
   * 
   * @return void
   */
  public function loadView(): void
  {
    if (file_exists('./app/' . $this->nameView . '.php')) {
      include './app/' . $this->nameView . '.php';
    } else {
      die("O arquivo não existe");
    }
  }

  /**
   * Carrega o layout do login e chama view passando o $this->nameView.
   */
  public function loadViewLogin(): void
  {
    $this->view = "./app/{$this->nameView}.php";
    if (file_exists('./app/' . $this->nameView . '.php')) {
      // Inclui o layout principal
      include './app/adms/Views/layouts/login.php';
    } else {
      die("O arquivo não existe");
    }
  }
}
