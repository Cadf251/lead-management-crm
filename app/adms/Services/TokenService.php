<?php

namespace App\adms\Services;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Token;
use App\adms\Repositories\TokenRepository;
use DateTime;
use Exception;
use PDO;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class TokenService
{
  private TokenRepository $repository;

  public function __construct(PDO $conn)
  {
    $this->repository = new TokenRepository($conn);
  }

  public function createForSystem(
    int $userId,
    string $context,
    ?DateTime $deadend
  ):Token
  {
    $token = Token::new(
      Token::TYPE_SYSTEM,
      $context,
      $deadend,
      $userId
    );

    try {
      $this->repository->create($token);
      return $token;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function createForSupport(
    int $supportId,
    string $context,
    ?DateTime $deadend
  ):Token
  {
    $token = Token::new(
      Token::TYPE_SUPPORT,
      $context,
      $deadend,
      null,
      $supportId
    );

    try {
      $this->repository->create($token);
      return $token;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function validate(
    string $token,
    string $type,
    string $context
  ):Token|false
  {
    try {
      $token = $this->repository->recover($token, $type, $context);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
    
    if ($token === null) {
      return false;
    }

    if (!$token->isValid()) {
      return false;
    }

    return $token;
  }

  public function disable(Token $token)
  {
    try {
      $token->disable();
      $this->repository->saveStatus($token);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function disableUserTokens(int $userId)
  {
    try {
      $this->repository->disableUserTokens($userId);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}