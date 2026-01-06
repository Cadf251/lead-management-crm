<?php

namespace App\adms\Services;

use App\adms\Core\AppContainer;
use PDO;
use Exception;
use DomainException;
use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\PHPMailerHelper;
use App\adms\Models\Usuario;
use App\adms\Repositories\TokenRepository;
use App\adms\Repositories\UsuariosRepository;

/**
 * 
 * @author Cadu 
 */
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
   * Cria um novo usuário, verifica se o email já estpa sendo usado, manda um email para criar senha, e armazena a foto se for enviada no formulário.
   * 
   * @param string $nome
   * @param string $email
   * @param string $celular Em qualquer formato
   * @param int $nivId
   * 
   * @log
   * 
   * @return OperationResult
   */
  public function criar(string $nome, string $email, string $celular, int $nivId): OperationResult
  {
    // Verifica se o usuário já existe
    if ($this->repository->existe($email)) {
      $this->result->warn("O email já está sendo usado por outro usuário.");
      return $this->result;
    }

    try {
      $usuario = Usuario::novo($nome, $email, $celular, $nivId);
      $usuario->setId($this->repository->criar($usuario));
      $this->result->addMensagem("O usuário foi criado com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->falha("Não foi possível criar o usuário.");
      return $this->result;
    }

    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMensagem("Peça que o usuário verifique o email $email para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->warn("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
    }

    if ($_FILES["foto"]["tmp_name"] != '') {
      try {
        $this->armazenarFoto($usuario);
        $this->repository->salvar($usuario);
      } catch (Exception $e) {
        GenerateLog::generateLog("error", "não foi possível armazenar um foto de usuário", ["error" => $e->getMessage()]);
        $this->result->addMensagem("Não foi possível armazenar a foto.");
      }
    }

    return $this->result;
  }

  /**
   * Edita um usuário
   * 
   * @param Usuario $usuario
   * @param array $dados Dados novos
   * 
   * @return OperationResult
   */
  public function editar(Usuario $usuario, array $dados): OperationResult
  {
    $usuario->setNome($dados["nome"]);
    $usuario->setCelular($dados["celular"]);
    $usuario->setNivel($dados["nivel_acesso_id"]);
    if ($usuario->getEmail() !== $dados["email"]) {
      $this->mudarEmail($usuario, $dados["email"]);
    }

    $substituida = false;

    if (!empty($_FILES["foto"]["name"])) {
      try {
        $this->trocarFoto($usuario);
        $this->result->addMensagem("A foto foi armazenada com sucesso.");
        $substituida = true;
      } catch (Exception $e) {
        GenerateLog::generateLog("error", $e->getMessage(), [
          "code" => $e->getCode(),
          "file" => $e->getFile(),
          "line" => $e->getLine(),
          "trace" => $e->getTrace(),
        ]);
        $this->result->warn("Não foi possível armazenar a foto do usuário.");
      }
    }
    
    if ((!$substituida) && ($dados["apagar-foto"] === "on")) {
      try {
        $this->apagarFoto($usuario);
        $this->result->addMensagem("A foto foi apagada com sucesso.");
      } catch (Exception $e) {
        GenerateLog::generateLog("error", $e->getMessage(), [
          "code" => $e->getCode(),
          "file" => $e->getFile(),
          "line" => $e->getLine(),
          "trace" => $e->getTrace(),
        ]);
        $this->result->warn("Não foi possível apagar a foto do usuário.");
      }
    }

    $this->repository->salvar($usuario);
    $this->result->addMensagem("O usuário foi editado com sucesso.");
    return $this->result;
  }

  /**
   * Muda o email de um usuário
   * 
   * @param Usuario $usuario
   * @param string $email
   * 
   * @log
   * 
   * @return void
   */
  private function mudarEmail(Usuario $usuario, string $email):void 
  {
    $emailAntigo = $usuario->getEmail();

    $this->result->addMensagem("O email é diferente do antigo, portanto, deve-se confirmar o novo email.");

    if ($this->repository->existe($email)) {
      $this->result->warn("O email não foi substituído porque já está sendo usado por outro usuário.");
      return;
    }

    try {
      $usuario->setEmail($email);
    } catch (Exception $e) {
      $this->result->warn("O novo email é inválido.");
      $usuario->setEmail($emailAntigo);
      return;
    }

    try {
      $usuario->resetarSenha();
    } catch (Exception) {
      $this->result->warn("O email não foi substituído devido a um erro.");
      $usuario->setEmail($emailAntigo);
      return;
    }

    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMensagem("Peça que o usuário verifique o email {$usuario->getEmail()} para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->warn("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
      $usuario->setEmail($emailAntigo);
    }
  }

  /**
   * Retiva o usuário e Reenvia o email de confirmação
   * 
   * @param Usuario $usuario
   * 
   * @return OperationResult
   */
  public function reativar(Usuario $usuario): OperationResult
  {
    // Ativar o usuário
    try {
      $usuario->reativar();

      $this->repository->salvar($usuario);

      $this->result->addMensagem("Usuário foi reativado com sucesso.");
    } catch (DomainException $e) {
      GenerateLog::generateLog("error", "Não foi possível ativar um usuário.", [
        "error" => $e->getMessage()
      ]);

      $this->result->falha("Não foi possível reativar o usuário");
      return $this->result;
    }

    // Envia o email para nova senha
    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMensagem("Peça que o usuário verifique o email {$usuario->getEmail()} para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->warn("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
    }

    return $this->result;
  }

  /**
   * Retiva o usuário e Reenvia o email de confirmação
   * 
   * @param Usuario $usuario
   * 
   * @return OperationResult
   */
  public function resetarSenha(Usuario $usuario): OperationResult
  {
    // Ativar o usuário
    try {
      $usuario->resetarSenha();

      $this->repository->salvar($usuario);

      $this->result->addMensagem("A senha foi resetada com sucesso.");
    } catch (DomainException $e) {
      GenerateLog::generateLog("error", "Não foi possível resetar a senha de um usuário.", [
        "error" => $e->getMessage()
      ]);

      $this->result->falha("Não foi possível resetar a senha.");
      return $this->result;
    }

    // Envia o email para nova senha
    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMensagem("Peça que o usuário verifique o email {$usuario->getEmail()} para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::generateLog("error", $e->getMessage(), [
        "code" => $e->getCode(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
      ]);
      $this->result->warn("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
    }

    return $this->result;
  }

  public function desativar(Usuario $usuario): ?OperationResult
  {
    try {
      $usuario->desativar();

      $tokenRepo = new TokenRepository($this->conexao);
      $tokenRepo->desativarDeUsuario($usuario->getId());

      if ($usuario->getFoto() !== null) {
        $this->apagarFoto($usuario);
      }

      $this->repository->salvar($usuario);
      $this->result->addMensagem("O usuário {$usuario->getNome()} foi desativado.");
    } catch (Exception) {
      $this->result->falha("Não foi possível desativar o usuário.");
    }
    return $this->result;
  }

  public function ativar(Usuario $usuario, $senhaHash): ?OperationResult
  {
    try {
      $usuario->ativar($senhaHash);

      $this->repository->salvar($usuario);

      $this->result->addMensagem("A senha foi criada com sucesso");
    } catch (Exception $e) {
      GenerateLog::generateLog("error", "Um usuário não pode ser ativado", [
        "error" => $e->getMessage()
      ]);
      $this->result->falha("A senha não foi criada com sucesso");

      return $this->result;
    }

    return $this->result;
  }

  public function reenviarEmail(Usuario $usuario): OperationResult
  {
    if (!$usuario->estaAguardandoConfirmacao()) {
      $this->result->falha("Esse usuário não tem nenhum token ativo.");
      return $this->result;
    }

    // Envia o email para nova senha
    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMensagem("Peça que o usuário verifique o email {$usuario->getEmail()} para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::generateLog("error", $e->getMessage(), [
        "code" => $e->getCode(),
        "file" => $e->getFile(),
        "line" => $e->getLine(),
        "trace" => $e->getTrace(),
      ]);
      $this->result->warn("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
    }

    return $this->result;
  }

  /**
   * Armazena uma foto de perfil de um usuário. Não funciona se o $_FILES["foto"] não estiver setado.
   * 
   * @param Usuario $usuario Precisa do ID para criar o arquivo. Modifica o atributo foto
   */
  private function armazenarFoto(Usuario $usuario): void
  {
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

    $arquivoNome = $usuario->getId() . $extensoes_permitidas[$tipo];
    $servidorId = AppContainer::getAuthUser()->getServidorId();
    $caminho = APP_ROOT . "files/uploads/{$servidorId}/fotos-perfil/";

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
      // Atualiza o banco de dados
      $tipoFormatado = str_replace(".", "", $extensoes_permitidas[$tipo]);
      $usuario->setFoto($tipoFormatado);
      
      // Se o usuário for o próprio, adiciona na SESSION
      if (AppContainer::getAuthUser()->getUsuarioId() === $usuario->getId()) {
        $_SESSION["auth"]["foto_perfil_tipo"] = $usuario->getFoto();
      }
    } else {
      throw new Exception("Algo deu errado");
    }
  }

  /**
   * Apaga o arquivo da foto em uploads
   * 
   * @param Usuario $usuario Precisa do ID para ler o arquivo. Modifica o atributo foto
   */
  private function apagarFoto(Usuario $usuario): void
  {
    $servidorId = AppContainer::getAuthUser()->getServidorId();
    $caminho = APP_ROOT . "files/uploads/{$servidorId}/fotos-perfil/{$usuario->getId()}";

    $arquivo = "$caminho.{$usuario->getFoto()}";

    // Verifique se o arquivo existe
    if (!empty($arquivo) && file_exists($arquivo)) {
      unlink($arquivo);
    }

    $usuario->setFoto(null);

    // Remove da $_SESSION caso seja o mesmo usuário
    if ($usuario->getId() === $_SESSION["auth"]["usuario_id"]) {
      $_SESSION["auth"]["foto_perfil_tipo"] = null;
    }
  }

  /**
   * Troca a foto por uma nova, chamando a função apagarFoto e armazenarFoto
   * 
   * @param Usuario $usuario Lê ID, modifica FOTO
   * 
   * @return Usuario $usuario
   */
  private function trocarFoto(Usuario $usuario): void
  {
    $this->apagarFoto($usuario);
    $this->armazenarFoto($usuario);
  }

  /**
   * Enviar um email de confirmação
   * 
   * @see GenerateLog Gera Log
   * @see OperationResult Atribui operarion em $this->result
   * 
   * @param Usuario $usuario Lê os parâmetros de ID, NOME e EMAIL
   * 
   * @return bool Se funcionou ou não
   */
  private function emailConfirmacao(Usuario $usuario): void
  {
    try {
      // Verifica se os parâmetros estão devidamente setados
      if (($usuario->getId() === 0) || ($usuario->getNome() === "") || ($usuario->getEmail() === "")) {
        throw new Exception("Não foi possível enviar o email porque há parâmetros faltando.");
      }

      // Cria devidamente o TOKEN
      $tokenRepo = new TokenRepository($this->conexao);

      // Prazo de 7 dias
      $prazo = date($_ENV['DATE_FORMAT'], strtotime('+7 days'));
      $token = $tokenRepo->armazenarToken("sistema", "confirmar_email_senha", $prazo, $usuario->getId());

      // Cria o email de envio
      $mail = new PHPMailerHelper();

      $mail->destinatarios([$usuario->getEmail()]);

      $mail->imagens([["caminho" => APP_ROOT."public/img/logo.png", "nome" => "logo"]]);

      $params = [
        "[NOME]" => $usuario->getNome(),
        "[SERVIDOR_ID]" => AppContainer::getAuthUser()->getServidorId(),
        "[TOKEN]" => $token
      ];

      $body = require APP_ROOT . "public/adms/emails/confirmacao.php";

      if ($body === false) {
        throw new Exception("Body está vazio");
      }

      $body = $mail->parameters($body, $params);

      $title = "RD Mind | Confirme seu E-mail e Crie sua Senha";
      $mail->setarConteudo($title, $body);
      $mail->enviar();
    } catch (Exception $e) {
      throw new Exception("Não foi possível enviar um email: " . $e->getMessage(), $e->getCode(), $e);
    }
  }
}
