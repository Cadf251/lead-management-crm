<?php
return <<<HTML
<html>
  <body style="background-color: #bbb; font-family: helvetica, arial;">
    <div style="margin-left:auto;margin-right:auto;margin-top: 30px;margin-bottom:30px;min-width: 300px; max-width:600px;background-color:#f0f0f0">
      <table style="border-collapse:collapse;">
        <tr>
        <td style="background-color: #404040; margin: 0 auto"><img src="cid:logo" alt="Logo RD Mind" width="70%" style="padding: 50px 0; margin: 0 15%"></td>
        </tr>
      </table>
      <table style="border-collapse:collapse">
        <tr>
          <td style="width:30px;height:30px"></td>
          <td></td>
          <td style="width:30px;height:30px"></td>
        </tr>
        <tr>
          <td style="width:30px;height:30px"></td>
          <td><p style="color: #222; font-size: 18px;">Olá [NOME], confirme o seu email acessando nosso portal pela primeira vez e criando uma senha.<br>Use o código da empresa: [SERVIDOR_ID]</p></td>
          <td style="width:30px;height:30px"></td>
        </tr>
        <tr>
          <td style="width:30px;height:30px"></td>
          <td></td>
          <td style="width:30px;height:30px"></td>
        </tr>
        <tr>
          <td style="width:30px;height:30px"></td>
          <td style="text-align: center;"><a style="padding: 15px 20px;font-size: 20px; color: #f0f0f0;font-weight: bold; background-color: #0ebcc9;" href="{$_ENV['HOST_BASE']}criar-senha/[SERVIDOR_ID]-[TOKEN]">Acessar</a></td>
          <td style="width:30px;height:30px"></td>
        </tr>
        <tr>
          <td style="width:30px;height:30px"></td>
          <td></td>
          <td style="width:30px;height:30px"></td>
        </tr>
        <tr>
          <td style="width:30px;height:30px"></td>
          <td></td>
          <td style="width:30px;height:30px"></td>
        </tr>
      </table>
      <table style="border-collapse: collapse; background-color: #404040; color: #f0f0f0;">
        <tr><td style="width:30px;height:30px"></td><td></td><td style="width:30px;height:30px"></td></tr> 
        <tr>
          <td style="width:30px;"></td>
          <td><p style="font-size: 24px; font-weight: bold; margin: 16px 0 0 0;">Entenda o universo do Marketing Digital com o nosso Blog</p></td>
          <td style="width:30px;"></td>
        </tr>
        <tr>
          <td style="width:30px;"></td>
          <td><a style="color: #f0f0f0; margin: 8px 0; font-size: 18px;" href="https://blog.agenciardmind.com.br/trafego-pago/o-que-e-trafego-pago.php">O que é Tráfego Pago?</a><br><a style="color: #f0f0f0; margin: 8px 0; font-size: 18px;" href="https://blog.agenciardmind.com.br/sites/como-criar-um-site.php">Como criar um site?</a></td>
          <td style="width:30px;"></td>
        </tr>
        <tr>
          <td style="width:30px;"></td>
          <td><p style="font-size: 24px; font-weight: bold; margin: 16px 0">✅ Responda esse email</p></td>
          <td style="width:30px;"></td>
        </tr>
        <tr><td style="width:30px;height:30px"></td><td></td><td style="width:30px;height:30px"></td></tr> 
      </table>
    </div>
  </body>
</html>
HTML;