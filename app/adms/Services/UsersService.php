<?php

namespace App\adms\Services;

use App\adms\Core\AppContainer;
use PDO;
use Exception;
use DomainException;
use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\PHPMailerHelper;
use App\adms\Repositories\UsersRepository;
use App\adms\Models\Token;
use App\adms\Models\users\User;
use DateTime;

/**
 * @complete V1
 * 
 * @author Cadu 
 */
class UsersService
{
  private OperationResult $result;
  private UsersRepository $repository;

  public function __construct(?PDO $conn = null)
  {
    $this->result = new OperationResult();
    $this->repository = new UsersRepository(
      $conn ?? AppContainer::getClientConn()
    );
  }

  /**
   * Cria um novo usuário, verifica se o email já estpa sendo usado, manda um email para criar senha, e armazena a foto se for enviada no formulário.
   * 
   * @log
   * 
   * @return OperationResult
   */
  public function create(string $name, string $email, string $phone, int $nivId): OperationResult
  {
    // Verifica se o usuário já existe
    if ($this->repository->exists($email)) {
      $this->result->warning("O email já está sendo usado por outro usuário.");
      return $this->result;
    }

    try {
      $usuario = User::new($name, $email, $phone, $nivId);
      $usuario->setId($this->repository->create($usuario));
      $this->result->addMessage("O usuário foi criado com sucesso.");
      $this->result->saveInstance("user", $usuario);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->failed("Não foi possível criar o usuário.");
      return $this->result;
    }

    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMessage("Peça que o usuário verifique o email $email para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->warning("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
    }

    if ($_FILES["foto"]["tmp_name"] != '') {
      try {
        $this->armazenarFoto($usuario);
        $this->repository->save($usuario);
      } catch (Exception $e) {
        GenerateLog::generateLog("error", "não foi possível armazenar um foto de usuário", ["error" => $e->getMessage()]);
        $this->result->addMessage("Não foi possível armazenar a foto.");
      }
    }

    return $this->result;
  }

  /**
   * Edita um usuário
   * 
   * @param User $usuario
   * @param array $dados Dados novos
   * 
   * @return OperationResult
   */
  public function edit(User $usuario, array $dados): OperationResult
  {
    try {
      $usuario->setName($dados["nome"]);
      $usuario->setPhone($dados["celular"]);
      $usuario->setNivel((int)$dados["nivel_acesso_id"]);
      if ($usuario->getEmail() !== $dados["email"]) {
        $this->mudarEmail($usuario, $dados["email"]);
      }
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->failed("Ocorreu algum erro.");
      return $this->result;
    }

    $substituida = false;

    if (!empty($_FILES["foto"]["name"])) {
      try {
        $this->trocarFoto($usuario);
        $this->result->addMessage("A foto foi armazenada com sucesso.");
        $substituida = true;
      } catch (Exception $e) {
        GenerateLog::generateLog("error", $e->getMessage(), [
          "code" => $e->getCode(),
          "file" => $e->getFile(),
          "line" => $e->getLine(),
        ]);
        $this->result->warning("Não foi possível armazenar a foto do usuário.");
      }
    }
    
    if ((!$substituida) && ($dados["apagar-foto"] === "on")) {
      try {
        $this->apagarFoto($usuario);
        $this->result->addMessage("A foto foi apagada com sucesso.");
      } catch (Exception $e) {
        GenerateLog::generateLog("error", $e->getMessage(), [
          "code" => $e->getCode(),
          "file" => $e->getFile(),
          "line" => $e->getLine(),
          "trace" => $e->getTrace(),
        ]);
        $this->result->warning("Não foi possível apagar a foto do usuário.");
      }
    }

    $this->repository->save($usuario);
    $this->result->saveInstance("user", $usuario);
    $this->result->addMessage("O usuário foi editado com sucesso.");
    return $this->result;
  }

  /**
   * Muda o email de um usuário
   * 
   * @param User $usuario
   * @param string $email
   * 
   * @log
   * 
   * @return void
   */
  private function mudarEmail(User $usuario, string $email):void 
  {
    $emailAntigo = $usuario->getEmail();

    $this->result->addMessage("O email é diferente do antigo, portanto, deve-se confirmar o novo email.");

    if ($this->repository->exists($email)) {
      $this->result->warning("O email não foi substituído porque já está sendo usado por outro usuário.");
      return;
    }

    try {
      $usuario->setEmail($email);
    } catch (Exception $e) {
      $this->result->warning("O novo email é inválido.");
      $usuario->setEmail($emailAntigo);
      return;
    }

    try {
      $usuario->resetPassword();
    } catch (Exception) {
      $this->result->warning("O email não foi substituído devido a um erro.");
      $usuario->setEmail($emailAntigo);
      return;
    }

    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMessage("Peça que o usuário verifique o email {$usuario->getEmail()} para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->warning("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
      $usuario->setEmail($emailAntigo);
    }
  }

  /**
   * Retiva o usuário e Reenvia o email de confirmação
   * 
   * @param User $usuario
   * 
   * @return OperationResult
   */
  public function reactivate(User $usuario): OperationResult
  {
    // Ativar o usuário
    try {
      $usuario->reactivate();

      $this->repository->save($usuario);

      $this->result->addMessage("Usuário foi reativado com sucesso.");
    } catch (DomainException $e) {
      GenerateLog::generateLog("error", "Não foi possível ativar um usuário.", [
        "error" => $e->getMessage()
      ]);

      $this->result->failed("Não foi possível reativar o usuário");
      return $this->result;
    }

    // Envia o email para nova senha
    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMessage("Peça que o usuário verifique o email {$usuario->getEmail()} para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->warning("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
    }

    return $this->result;
  }

  /**
   * Retiva o usuário e Reenvia o email de confirmação
   * 
   * @param User $usuario
   * 
   * @return OperationResult
   */
  public function resetPassword(User $usuario): OperationResult
  {
    // Ativar o usuário
    try {
      $usuario->resetPassword();

      $this->repository->save($usuario);

      $this->result->addMessage("A senha foi resetada com sucesso.");
    } catch (DomainException $e) {
      GenerateLog::generateLog("error", "Não foi possível resetar a senha de um usuário.", [
        "error" => $e->getMessage()
      ]);

      $this->result->failed("Não foi possível resetar a senha.");
      return $this->result;
    }

    // Envia o email para nova senha
    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMessage("Peça que o usuário verifique o email {$usuario->getEmail()} para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::generateLog("error", $e->getMessage(), [
        "code" => $e->getCode(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
      ]);
      $this->result->warning("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
    }

    return $this->result;
  }

  public function disable(User $usuario): ?OperationResult
  {
    try {
      $usuario->disable();

      $tokenService = new TokenService(AppContainer::getClientConn());
      $tokenService->disableUserTokens($usuario->getId());

      if ($usuario->getProfilePicture() !== null) {
        $this->apagarFoto($usuario);
      }

      $this->repository->save($usuario);
      $this->result->addMessage("O usuário {$usuario->getName()} foi desativado.");
    } catch (Exception) {
      $this->result->failed("Não foi possível desativar o usuário.");
    }
    return $this->result;
  }

  public function activate(User $usuario, $senhaHash): ?OperationResult
  {
    try {
      $usuario->activate($senhaHash);

      $this->repository->save($usuario);

      $this->result->addMessage("A senha foi criada com sucesso");
    } catch (Exception $e) {
      GenerateLog::generateLog("error", "Um usuário não pode ser ativado", [
        "error" => $e->getMessage()
      ]);
      $this->result->failed("A senha não foi criada com sucesso");

      return $this->result;
    }

    return $this->result;
  }

  public function resendMail(User $usuario): OperationResult
  {
    if (!$usuario->estaAguardandoConfirmacao()) {
      $this->result->failed("Esse usuário não tem nenhum token ativo.");
      return $this->result;
    }

    // Envia o email para nova senha
    try {
      $this->emailConfirmacao($usuario);
      $this->result->addMessage("Peça que o usuário verifique o email {$usuario->getEmail()} para criar uma senha.");
    } catch (Exception $e) {
      GenerateLog::generateLog("error", $e->getMessage(), [
        "code" => $e->getCode(),
        "file" => $e->getFile(),
        "line" => $e->getLine(),
      ]);
      $this->result->warning("O email de redefinição se senha não foi enviado, tente novamente ou entre em contato com o suporte.");
    }

    return $this->result;
  }

  /**
   * Armazena uma foto de perfil de um usuário. Não funciona se o $_FILES["foto"] não estiver setado.
   * 
   * @param User $usuario Precisa do ID para criar o arquivo. Modifica o atributo foto
   */
  private function armazenarFoto(User $usuario): void
  {
    ini_set('file_uploads', '1');

    $extensoes_permitidas = [
      'image/jpg' => '.jpg',
      'image/png'  => '.png',
      'image/jpeg' => '.jpeg'
    ];

    $tipo = mime_content_type($_FILES['foto']['tmp_name']);

    // Verifica se o tipo existe
    if (!array_key_exists($tipo, $extensoes_permitidas)) {
      throw new Exception("Formato de arquivo inválido na tentativa de upload para foto de perfil");
    }

    $arquivoNome = $usuario->getId() . $extensoes_permitidas[$tipo];
    $servidorId = AppContainer::getAuthUser()->getServerId();
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
      $usuario->setProfilePicture($tipoFormatado);
      
      // Se o usuário for o próprio, adiciona na SESSION
      if (AppContainer::getAuthUser()->getUserId() === $usuario->getId()) {
        $_SESSION["auth"]["profile_picture"] = $usuario->getProfilePicture();
      }
    } else {
      throw new Exception("Algo deu errado");
    }
  }

  /**
   * Apaga o arquivo da foto em uploads
   * 
   * @param User $usuario Precisa do ID para ler o arquivo. Modifica o atributo foto
   */
  private function apagarFoto(User $usuario): void
  {
    $servidorId = AppContainer::getAuthUser()->getServerId();
    $caminho = APP_ROOT . "files/uploads/{$servidorId}/fotos-perfil/{$usuario->getId()}";

    $arquivo = "$caminho.{$usuario->getProfilePicture()}";

    // Verifique se o arquivo existe
    if (file_exists($arquivo)) {
      unlink($arquivo);
    }

    $usuario->setProfilePicture(null);

    // Remove da $_SESSION caso seja o mesmo usuário
    if ($usuario->getId() === $_SESSION["auth"]["user_id"]) {
      $_SESSION["auth"]["profile_picture"] = null;
    }
  }

  /**
   * Troca a foto por uma nova, chamando a função apagarFoto e armazenarFoto
   * 
   * @return User $usuario
   */
  private function trocarFoto(User $usuario): void
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
   * @param User $usuario Lê os parâmetros de ID, NOME e EMAIL
   * 
   * @return bool Se funcionou ou não
   */
  private function emailConfirmacao(User $usuario): void
  {
    try {
      // CRIAR UM NOVO TOKEN
      $tokenService = new TokenService(AppContainer::getClientConn());

      $token = $tokenService->createForSystem($usuario->getId(), Token::CONTEXT_CONFIRMAR_EMAIL, new DateTime("+ 7 days"));

      // Cria o email de envio
      $mail = new PHPMailerHelper();

      $mail->destinatarios([$usuario->getEmail()]);

      $mail->imagens([["caminho" => APP_ROOT."public/img/logo.webp", "nome" => "logo"]]);

      $params = [
        "[NOME]" => $usuario->getName(),
        "[SERVIDOR_ID]" => AppContainer::getAuthUser()->getServerId() ?? $_SESSION["auth"]["server_id"],
        "[TOKEN]" => $token->getToken()
      ];

      // @todo criar um email mais interessante
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
