<?php

namespace App\adms\Helpers;

class NotificarErro
{
  public static function notificar(string $descricao, array $info)
  {
    $mailer = new PHPMailerHelper();
    $mailer->destinatarios([
      "cadu.devmarketing@gmail.com"
    ]);

    ob_start();
    var_dump($info);
    $contextDump = ob_get_clean();

    $body = <<<HTML
    <html>
      <h1>Atenção!</h1>
      <p>$descricao</p>
      $contextDump
    </html>
    HTML;

    $mailer->setarConteudo("❌ Erro fatal no CRM", $body);
    $mailer->enviar();
  }
}