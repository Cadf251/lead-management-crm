<?php

namespace App\adms\Helpers;

use App\adms\Core\AppContainer;
use App\adms\Services\AuthUser;
use Exception;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Gerar log
 * 
 *  - DEBUG (100): Informação de depuração.
 *  - INFO (200): Eventos interessantes. Por exemplo: um usuário realizou o login ou logs de SQL.
 *  - NOTICE (250): Eventos normais, mas significantes.
 *  - WARNING (300): Ocorrências excepcionais, mas que não são erros. Por exemplo: Uso de APIs descontinuadas, uso inadequado de uma API. Em geral coisas que não estão erradas mas precisam de atenção.
 *  - ERROR (400): Erros de tempo de execução que não requerem ação imediata, mas que devem ser logados e monitorados.
 *  - CRITICAL (500): Condições criticas. Por exemplo: Um componente da aplicação não está disponível, uma exceção não esperada ocorreu.
 *  - ALERT (550): Uma ação imediata deve ser tomada. Exemplo: O sistema caiu, o banco de dados está indisponível , etc. Deve disparar um alerta para o responsável tomar providencia o mais rápido possível.
 *  - EMERGENCY (600): Emergência: O sistema está inutilizável.
 * @author Cadu <cadu.devmarketing@gmail.com>
 */
class GenerateLog
{

  const DEBUG = "debug";
  const INFO = "info";
  const NOTICE = "notice";
  const WARNING = "warning";
  const ERROR = "error";
  const CRITICAL = "critical";
  const ALERT = "alert";
  const EMERGENCY = "emergency";

  /**
   * Método static que pode ser chamado sem criar a instância
   * Códigos de erro personalizados:
   * Cria um LOG
   * 
   */
  public static function generateLog(string $level, string $message, array|null $content): void
  {
    $log = new Logger("name");

    $nameFileLog = date("dmY") . ".log";

    $pasta = AppContainer::getAuthUser()->getServidorId() ?? "limbo";

    // Cria o diretório
    $path = APP_ROOT . "files/logs/$pasta";

    // Cria a pasta se não existir
    if (!is_dir($path)) {
      $novaPasta = mkdir($path, 0777, true);
      if (!$novaPasta) $path = "files/logs/limbo";
    }

    // Criar o caminho do log
    $filePath = "$path/$nameFileLog";

    // Verifica se o arquivo existe
    if (!file_exists($filePath)) {

      // Abre o arquivo
      $fileOpen = fopen($filePath, "w");

      // Fecha o arquivo
      fclose($fileOpen);
    }

    $log->pushHandler(new StreamHandler($filePath), Level::Debug);

    if (AppContainer::getAuthUser()->estaLogado()) {
      $content["sessao"] = [
        "usuario_id" =>  AppContainer::getAuthUser()->getUsuarioId() ?? null,
        "servidor_id" => AppContainer::getAuthUser()->getServidorId() ?? null
      ];
    }

    // Salvar o log no arquivo
    $log->$level($message, $content);
  }

  // MÉTODO NOVO: Recebe o objeto de erro diretamente
  public static function log(\Throwable $e, $level = self::ERROR, array $info = [])
  {
    // Extrai os dados automaticamente
    $dadosDoErro = [
      'mensagem' => $e->getMessage(),
      'arquivo'  => $e->getFile(),
      'linha'    => $e->getLine(),
    ];

    // Chama o método antigo internamente para salvar, 
    // assim você não repete a lógica de salvar no arquivo/banco
    self::generateLog($level, $e->getMessage(), array_merge($dadosDoErro, $info));
  }
}
