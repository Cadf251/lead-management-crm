<?php

namespace App\adms\Services;

use App\adms\Core\AppContainer;
use App\adms\Core\OperationResult;
use App\adms\Database\DbConnectionClient;
use App\adms\Database\GlobalConn;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Token;
use App\adms\Models\users\User;
use App\adms\Repositories\LoginRepository;
use App\adms\Repositories\UsersRepository;
use Exception;
use PDO;

class LoginService
{
  private PDO $globalConn;
  private DbConnectionClient $clientConn;

  private OperationResult $result;

  public LoginRepository $repo;

  private array $credentials;
  private User $user;

  public function __construct()
  {
    $this->result = new OperationResult();
    $this->globalConn = GlobalConn::get();
    $this->repo = new LoginRepository($this->globalConn);
  }

  public function verifyLogin(array $data): OperationResult
  {
    if (!$this->validateForm($data)) {
      return $this->failed();
    }

    if (!$this->user->canLoggin()) {
      return $this->failed("Usuário não tem senha válida.");
    }

    if (!password_verify(
      $data["password"],
      $this->user->getPassWordHash()
    )) {
      $this->result->failed("Senha inválida.");
      return $this->result;
    }

    $this->setSessionUser(
      $data["server_id"],
      $this->credentials,
      $this->user
    );

    return $this->result;
  }

  public function forgotPass(array $data)
  {
    if (!$this->validateForm($data)) {
      return $this->failed();
    }

    $userService = new UsersService($this->clientConn->conexao);

    $_SESSION["auth"]["server_id"] = (int)$data["server_id"];

    AppContainer::setClientConn($this->clientConn);

    $result = $userService->resetPassword($this->user);

    unset($_SESSION["auth"]["server_id"]);

    AppContainer::unsetClientConn();
    
    return $result;
  }

  private function validateForm(array $data): bool
  {
    $connected = $this->connectClient((int)$data["server_id"]);

    if ($connected === null) {
      return false;
    }

    $this->credentials = $connected;

    $user = $this->selectUser($data["email"]);

    if ($user === null) {
      return false;
    }

    $this->user = $user;

    return true;
  }

  public function validateToken(int $serverId, string $token): ?Token
  {
    $connected = $this->connectClient($serverId);

    if ($connected === null) {
      return null;
    }

    $this->credentials = $connected;

    $tokenService = new TokenService($this->clientConn->conexao);
    
    try {
      $valido = $tokenService->validate($token, Token::TYPE_SYSTEM, Token::CONTEXT_CONFIRMAR_EMAIL);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      return null;
    }

    if ($valido === false) {
      return null;
    }

    return $valido;
  }

  public function createPass(int $serverId, array $data, Token $token)
  {
    $senhaHash = password_hash($data["password"], PASSWORD_BCRYPT);
    
    try {
      $repo = new UsersRepository($this->clientConn->conexao);
      $this->user = $repo->select($token->getUserId());

      $service = new UsersService($this->clientConn->conexao);
      $service->activate($this->user, $senhaHash);

      $tokenService = new TokenService($this->clientConn->conexao);
      $tokenService->disable($token);
      
      $this->setSessionUser($serverId, $this->credentials, $this->user);
      
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->failed("Algo deu errado.");
    }

    return $this->result;
  }

  public function setSessionUser(
    int $serverId,
    array $server,
    User $user
  ) {
    $_SESSION["auth"] = [
      "user_id" => $user->getId(),
      "user_nome" => $user->getName(),
      "user_email" => $user->getEmail(),
      "profile_picture" => $user->getProfilePicture(),
      "system_level_id" => (int)$user->getSystemLevelId(),
      "server_id" => $serverId,
      "db_credentials" => $server
    ];
  }

  private function connectClient(int $serverId): array|null
  {
    $creds = $this->repo->verificarServidor($serverId);

    if ($creds === false) {
      return null;
    }

    $conn = new DbConnectionClient($creds);
    $this->clientConn = $conn;
    return $creds;
  }

  private function selectUser(string $email): ?User
  {
    $repo = new UsersRepository($this->clientConn->conexao);
    $user = $repo->selectByEmail($email);
    return $user;
  }

  private function failed($msg = "Usuário inválido."): OperationResult
  {
    $this->result->failed($msg);
    return $this->result;
  }
}
