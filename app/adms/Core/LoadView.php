<?php

namespace App\adms\Core;

use App\adms\Helpers\GenerateLog;

/** 
 * Carrega as páginas da View
 */

class LoadView
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
    $this->view = APP_ROOT."app/{$this->nameView}.php";
    if (file_exists(APP_ROOT."app/" . $this->nameView . '.php')) {
      // Inclui o layout principal
      include APP_ROOT.'app/adms/Views/layouts/main.php';
    } else {
      GenerateLog::generateLog("error", "O arquivo não existe", ["arquivo" => './app/' . $this->nameView . '.php']);
      $this->falha();
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
      GenerateLog::generateLog("error", "O arquivo não existe", ["arquivo" => './app/' . $this->nameView . '.php']);
      $this->falha();
    }
  }

  /**
   * Carrega o layout de erro, que é igual ao main, porém sem o nav e com um link de volta para o dashboard
   * Não usar essa função para erros internos, apenas para quando o usuário não estiver logado
   */
  public function loadExternalError(): void
  {
    $this->view = APP_ROOT."app/{$this->nameView}.php";
    if (file_exists(APP_ROOT.'app/' . $this->nameView . '.php')) {
      // Inclui o layout principal
      include APP_ROOT.'app/adms/Views/layouts/external-error.php';
    } else {
      GenerateLog::generateLog("error", "O arquivo não existe", ["arquivo" => APP_ROOT.'app/' . $this->nameView . '.php']);
      $this->falha();
    }
  }

  /**
   * Carrega o layout do MASTER
   */
  public function loadViewMaster(): void
  {
    $this->view = "./app/{$this->nameView}.php";
    if (file_exists('./app/' . $this->nameView . '.php')) {
      // Inclui o layout principal
      include './app/database/Views/layouts/main.php';
    } else {
      
      $this->falha();
    }
  }

  /**
   * Direciona o cabra para a tela de falha e interrompe o código
   */
  private function falha(): void
  {
    GenerateLog::generateLog("error", "O arquivo não existe", ["arquivo" => './app/' . $this->nameView . '.php']);

    header("Location: {$_ENV['HOST_BASE']}erro/404");
    exit;
  }
}
