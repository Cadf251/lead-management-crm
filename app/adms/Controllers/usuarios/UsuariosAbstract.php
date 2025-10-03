<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\PHPMailerHelper;
use App\adms\Models\Repositories\TokenRepository;
use App\adms\Models\Repositories\UsuariosRepository;
use App\adms\Models\Services\DbConnectionClient;

/** Define funções universais para as classes de usuários. Tem o objetivo de ser herdado. Instancia o repositório automaticamente. */
abstract class UsuariosAbstract 
{
  /** @var protected $repo O repositório de usuários  */
  protected $repo;
  /** @var protected $tokenRepo O repositório de TOKENs  */
  protected $tokenRepo;

  /** Conecta com o banco de dados do cliente depois inicia o repositório de usuários e de tokens */
  public function __construct()
  {
    $conn = new DbConnectionClient(null);
    $this->repo = new UsuariosRepository($conn->conexao);
    $this->tokenRepo = new TokenRepository($conn->conexao);
  }

  /**
   * Armazena uma foto de perfil de um usuário. Não funciona se o $_FILES["foto"] não estiver setado.
   * 
   * @param int $usuarioId O ID do usuário
   * 
   * @return bool Se o upload falhou ou não
   */
  public function armazenarFoto(int $usuarioId) :bool
  {
    ini_set('file_uploads', '1');

    $extensoes_permitidas = [
      'image/jpeg' => '.jpg',
      'image/png'  => '.png',
      'image/jpeg' => '.jpeg'
    ];

    $tipo = mime_content_type($_FILES['foto']['tmp_name']);

    // Verifica se o tipo existe
    if (!array_key_exists($tipo, $extensoes_permitidas)){
      GenerateLog::generateLog("error", "Formato de arquivo inválido na tentativa de upload para foto de perfil.", ["tipo" => $tipo, "usuario_id" => $usuarioId, "filearray" => $_FILES["foto"]]);
      return false;
    }

    $arquivoNome = $usuarioId.$extensoes_permitidas[$tipo];
    $caminho = "files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/";  

    // Tenta criar o diretório se não existir
    if (!is_dir($caminho)){
      $novaPasta = mkdir($caminho, 0777, true);
      if (!$novaPasta) {
        GenerateLog::generateLog("error", "Não foi possível criar a pasta.", ["pasta" => $caminho, "tipo" => $tipo, "usuario_id" => $usuarioId, "filearray" => $_FILES["foto"]]);
        return false;
      }
    }

    $tmp = $_FILES["foto"]["tmp_name"];
    $imagem = getimagesize($tmp);

    // Verifica se é uma imagem
    if (!$imagem){
      GenerateLog::generateLog("error", "O arquivo não é uma imagem.", ["tipo" => $tipo, "usuario_id" => $usuarioId, "filearray" => $_FILES["foto"]]);
      return false;
    }

    $final = $caminho.$arquivoNome;

    // Verifica se o arquivo existe
    if (file_exists($final)){
      GenerateLog::generateLog("error", "O arquivo já existe. Fique atento a IDs duplicados.", ["tipo" => $tipo, "usuario_id" => $usuarioId, "filearray" => $_FILES["foto"]]);
      return false;
    }

    // Tenta criar os uploads
    if (move_uploaded_file($tmp, $final)){
      // Se o usuário for o próprio, adiciona na SESSION
      if ($_SESSION["usuario_id"] === $usuarioId)
        $_SESSION["foto_perfil"] = $final;

      // Atualiza o banco de dados
      $tipoFormatado = str_replace(".", "", $extensoes_permitidas[$tipo]);
      $params = [
        ":foto_perfil" => $tipoFormatado
      ];
      $this->repo->updateUsuario($params, $usuarioId);
      return true;
    }

    GenerateLog::generateLog("error", "Não pôde armazenar por algum motivo.", ["tipo" => $tipo, "usuario_id" => $usuarioId, "filearray" => $_FILES["foto"], "caminho" => $caminho, "final" => $final]);
    return false;
  }

  /**
   * Apaga o arquivo da foto em uploads
   * 
   * @param int $usuarioId O ID do usuário
   * 
   * @return bool Se apagou ou não
   */
  public function apagarFoto(int $usuarioId):bool
  {
    $caminho = "files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/$usuarioId";

    $extensoes_permitidas = ['.jpg','.png','.jpeg'];

    $ok = false;

    // Verica o arquivo para cada extensão
    foreach ($extensoes_permitidas as $tipo){
      $arquivo = $caminho.$tipo;

      // Verifique se o arquivo existe
      if (!empty($arquivo) && file_exists($arquivo)){
        unlink($arquivo);
        $ok = true;
      }
    }

    if ($ok === false)
      GenerateLog::generateLog("error", "A foto não foi apagada.", ["id" => $usuarioId]);
    else {
      // Atualiza o banco de dados
      $params = [
        ":foto_perfil" => null
      ];
      $this->repo->updateUsuario($params, $usuarioId);
    }

    return $ok;
  }

  /**
   * Troca a foto por uma nova, chamando a função apagarFoto e armazenarFoto
   * 
   * @param int $usuarioId O ID do usuário
   * 
   * @return bool Se funcionou ou não
   */
  public function trocarFoto(int $usuarioId):bool
  {
    $apagar = $this->apagarFoto($usuarioId);

    if ($apagar === false)
      return false;

    return $this->armazenarFoto($usuarioId);
  }

    /**
   * Enviar um email de confirmação
   * 
   * @param int $usuarioId O ID do usuário.
   * @param string $usuarioNome O nome do usuário.
   * @param string $usuarioEmail O email do usuário.
   * 
   * @return bool Se falhou ou não
   */
  public function emailConfirmacao(int $usuarioId, string $usuarioNome, string $usuarioEmail):bool
  {    
    // Cria devidamente o TOKEN
    $token = $this->tokenRepo->armazenarToken("sistema", "confirmar_email_senha", $usuarioId);

    // Cria o email de envio
    $mail = new PHPMailerHelper;

    $mail->destinatarios([$usuarioEmail]);

    $mail->imagens([["caminho" => "public/adms/img/logo.png", "nome" => "logo"]]);

    $params = [
      "[NOME]" => $usuarioNome,
      "[SERVIDOR_ID]" => $_SESSION["servidor_id"],
      "[TOKEN]" => $token
    ];

    $body = <<<HTML
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
              <td style="text-align: center;"><a style="padding: 15px 20px;font-size: 20px; color: #f0f0f0;font-weight: bold; background-color: #0ebcc9;" href="{$_ENV['HOST_BASE']}nova-senha/[SERVIDOR_ID]-[TOKEN]">Acessar</a></td>
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

    if ($body === false)
      return false;

    $body = $mail->parameters($body, $params);

    $title = "RD Mind | Confirme seu E-mail e Crie sua Senha";
    $mail->setarConteudo($title, $body);
    return $mail->enviar();
  }
}