<?php

namespace App\adms\Services;

use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\PHPMailerHelper;
use App\adms\Models\Usuario;
use App\adms\Repositories\TokenRepository;
use App\adms\Repositories\UsuariosRepository;
use DomainException;
use Exception;
use PDO;

class UsuariosService
{
  private ?PDO $conexao;
  private OperationResult $result;
  private UsuariosRepository $repository;

  public function __construct(PDO $conexao)
  {
    $this->conexao = $conexao;
    $this->result = new OperationResult();
    $this->repository = new UsuariosRepository($this->conexao);
  }

  /**
   * Retiva o usuário e Reenvia o email de confirmação
   * 
   * @param Usuario $usuario
   * 
   * @return OperationResult
   */
  public function reativar(Usuario $usuario):OperationResult
  {
    // Ativar o usuário
    try {
      $usuario->reativar($this->repository);

      $this->repository->salvar($usuario);

      $this->result->addMensagem("Usuário foi ativado com sucesso.");
    } catch (DomainException $e){
      GenerateLog::generateLog("error", "Não foi possível ativar um usuário.", [
        "error" => $e->getMessage()
      ]);

      $this->result->falha("Não foi possível ativar o usuário");
    }

    // Envia o email para nova senha
    $this->emailConfirmacao($usuario);

    return $this->result;
  }

  /**
   * Retiva o usuário e Reenvia o email de confirmação
   * 
   * @param Usuario $usuario
   * 
   * @return OperationResult
   */
  public function resetarSenha(Usuario $usuario):OperationResult
  {
    // Ativar o usuário
    try {
      $usuario->resetarSenha($this->repository);

      $this->repository->salvar($usuario);

      $this->result->addMensagem("A senha foi resetada com sucesso.");
    } catch (DomainException $e){
      GenerateLog::generateLog("error", "Não foi possível resetar a senha de um usuário.", [
        "error" => $e->getMessage()
      ]);

      $this->result->falha("Não foi possível resetar a senha.");
    }

    // Envia o email para nova senha
    $this->emailConfirmacao($usuario);

    return $this->result;
  }

  public function desativar(Usuario $usuario){
    // Instancia o OperationResult
    $result = new OperationResult();

    // Instancia o repositório
    $repositorio = new UsuariosRepository($this->conexao);

    $tokenRepo = new TokenRepository($this->conexao);

    $usuario->desativar($repositorio);

    if ($usuario->foto !== null){
      $this->apagarFoto($usuario);
    }

    $repositorio->salvar($usuario);
  }

  public function ativar(Usuario $usuario, $senhaHash):?OperationResult
  {
    try {
      $usuario->ativar($this->repository, $senhaHash);

      $this->repository->salvar($usuario);

      $this->result->addMensagem("A senha foi criada com sucesso");
    } catch (Exception $e) {
      GenerateLog::generateLog("error", "Um usuário não pode ser ativado", [
        "error" => $e->getMessage()
      ]);
      $this->result->falha("A senha não foi criada com sucesso");
    }

    return $this->result;
  }
    
  /**
   * Armazena uma foto de perfil de um usuário. Não funciona se o $_FILES["foto"] não estiver setado.
   * 
   * @param Usuario $usuario
   * 
   * @return Usuario $usuario
   */
  private function armazenarFoto(Usuario $usuario):?Usuario
  {
    // Verifica se o id está setado
    if (!isset($usuario->id)){
      throw new Exception("O Id do usuário não está setado");
    }

    ini_set('file_uploads', '1');

    $extensoes_permitidas = [
      'image/jpeg' => '.jpg',
      'image/png'  => '.png',
      'image/jpeg' => '.jpeg'
    ];

    $tipo = mime_content_type($_FILES['foto']['tmp_name']);

    // Verifica se o tipo existe
    if (!array_key_exists($tipo, $extensoes_permitidas)) {
      throw new Exception("Formato de arquivo inválido na tentativa de upload para foto de perfil");
    }

    $arquivoNome = $usuario->id . $extensoes_permitidas[$tipo];
    $caminho = APP_ROOT."files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/";

    // Tenta criar o diretório se não existir
    if (!is_dir($caminho)) {
      $novaPasta = mkdir($caminho, 0777, true);
      if (!$novaPasta) {
        throw new Exception("Não foi possível criar a pasta.");
      }
    }

    $tmp = $_FILES["foto"]["tmp_name"];
    $imagem = getimagesize($tmp);

    // Verifica se é uma imagem
    if (!$imagem) {
      throw new Exception("O arquivo não é uma imagem.");
    }

    $final = $caminho . $arquivoNome;

    // Verifica se o arquivo existe
    if (file_exists($final)) {
      throw new Exception("O arquivo já existe");
    }

    // Tenta criar os uploads
    if (move_uploaded_file($tmp, $final)) {
      // Se o usuário for o próprio, adiciona na SESSION
      if ($_SESSION["usuario_id"] === $usuario->id){
        $_SESSION["foto_perfil"] = $final;
      }

      // Atualiza o banco de dados
      $tipoFormatado = str_replace(".", "", $extensoes_permitidas[$tipo]);
      $usuario->setFoto($tipoFormatado);
      return $usuario;
    } else {
      throw new Exception("Algo deu errado");
    }
  }

  /**
   * Apaga o arquivo da foto em uploads
   * 
   * @param Usuario $usuario
   * 
   * @return Usuario $usuario
   */
  private function apagarFoto(Usuario $usuario): ?Usuario
  {
    // Verifica se o id está setado
    // Verifica se o id está setado
    if (!isset($usuario->id)){
      throw new Exception("O Id do usuário não está setado");
    }

    $caminho = APP_ROOT."files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/{$usuario->id}";

    $arquivo = "$caminho.{$usuario->foto}";

    // Verifique se o arquivo existe
    if (!empty($arquivo) && file_exists($arquivo)) {
      unlink($arquivo);
    }

    $usuario->setFoto(null);

    // Remove da $_SESSION caso seja o mesmo usuário
    if ($usuario->id === $_SESSION["usuario_id"]) {
      $_SESSION["foto_perfil"] = null;
    }

    return $usuario;
  }

  /**
   * Troca a foto por uma nova, chamando a função apagarFoto e armazenarFoto
   * 
   * @param Usuario $usuario
   * 
   * @return Usuario $usuario
   */
  private function trocarFoto(Usuario $usuario): ?Usuario
  {
    $usuario = $this->apagarFoto($usuario);
    return $this->armazenarFoto($usuario);
  }

  /**
   * Enviar um email de confirmação
   * 
   * Precisa dos parâmetros setados: id, nome, email
   * 
   * @return bool Se funcionou ou não
   */
  private function emailConfirmacao(Usuario $usuario): void
  {
    try {
      // Verifica se os parâmetros estão devidamente setados
      if (($usuario->id === 0) || ($usuario->nome === "") || ($usuario->email === "")) {
        throw new Exception("Não foi possível enviar o email porque há parâmetros faltando.");
      }

      // Cria devidamente o TOKEN
      $tokenRepo = new TokenRepository($this->conexao);

      // Prazo de 7 dias
      $prazo = date($_ENV['DATE_FORMAT'], strtotime('+7 days'));
      $token = $tokenRepo->armazenarToken("sistema", "confirmar_email_senha", $prazo, $usuario->id);

      // Cria o email de envio
      $mail = new PHPMailerHelper();

      $mail->destinatarios([$usuario->email]);

      $mail->imagens([["caminho" => "public/adms/img/logo.png", "nome" => "logo"]]);

      $params = [
        "[NOME]" => $usuario->nome,
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

      if ($body === false){
        throw new Exception("Body está vazio");
      }

      $body = $mail->parameters($body, $params);

      $title = "RD Mind | Confirme seu E-mail e Crie sua Senha";
      $mail->setarConteudo($title, $body);
      $mail->enviar();
      $this->result->addMensagem("O email para gerar nova senha foi enviado.");
    } catch (Exception $e){
      GenerateLog::generateLog("error", "Não foi possível enviar um email.", [
        "error" => $e->getMessage()
      ]);
      $this->result->falha("Não foi possível enviar o email para criar nova senha.");
    }
  }
}