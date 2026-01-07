<?php

namespace App\adms\Models;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\traits\DateHandler;
use App\adms\Models\traits\StatusHandler;
use DateTime;
use Exception;
use InvalidArgumentException;

/**
 * language: EN
 */
class Token
{
  use DateHandler;
  use StatusHandler;

  private int $id;
  private string $token;
  private string $type;
  private string $context;
  private ?DateTime $deadend = null;
  private Status $status;
  private ?int $userId = null;
  private ?int $supportId = null;

  public const TYPE_SYSTEM = "system";
  public const TYPE_SUPPORT = "support";

  private const VALID_TYPES = [
    self::TYPE_SYSTEM,
    self::TYPE_SUPPORT 
  ];

  public const CONTEXT_CONFIRMAR_EMAIL = "confirm_email";

  private const VALID_CONTEXTS = [
    self::CONTEXT_CONFIRMAR_EMAIL
  ];

  public function __construct(
    string $token,
    string $type,
    string $context,
    DateTime|string|null $deadend = null,
    ?int $userId = null,
    ?int $supportId = null
  )
  {
    $this->setToken($token);
    $this->setType($type);
    $this->setContext($context);
    $this->setStatus(Status::STATUS_ATIVADO);
    $this->setDeadEnd($deadend);

    if ($userId == null && $supportId == null) {
      throw new Exception("O user e support ID não podem ser ambos null.");
    }

    $this->setUserId($userId);
    $this->setSupportId($supportId);
  }

  public static function new(
    string $type,
    string $context,
    DateTime|string|null $deadend = null,
    ?int $userId = null,
    ?int $supportId = null
  ):self
  {
    GenerateLog::generateLog("debug", "user", [$userId]);
    return new self(
      self::createToken(),
      $type,
      $context,
      $deadend,
      $userId,
      $supportId
    );
  }

  private static function createToken() :string
  {
    return bin2hex(random_bytes(16));
  }

  // |---------------|
  // |--- SETTERS ---|
  // |---------------|
  public function setId(int $id)
  {
    $this->id = $id;
  }

  public function setToken(string $token)
  {
    if (strlen($token) !== 32){
      throw new InvalidArgumentException("Invalid Token String: $token Length: " . strlen($token));
    }

    $this->token = $token;
  }

  public function setType(string $type) 
  {
    if (!in_array($type, self::VALID_TYPES)){
      throw new InvalidArgumentException("INvalid Token Type: $type");
    }

    $this->type = $type;
  }

  public function setContext(string $context)
  {
    if (!in_array($context, self::VALID_CONTEXTS)){
      throw new InvalidArgumentException("INvalid Token Type: $context");
    }
    
    $this->context = $context;
  }

  public function setDeadEnd(DateTime|string|null $deadend)
  {
    $this->deadend = $this->castToDateTime($deadend);
  }

  public function setUserId(?int $userId)
  {
    $this->userId = $userId;
  }

  public function setSupportId(?int $supportId)
  {
    $this->supportId = $supportId;
  }

  // |---------------|
  // |--- GETTERS ---|
  // |---------------|

  public function getId():int
  {
    return $this->id;
  }

  public function getToken():string
  {
    return $this->token;
  }

  public function getType(): string
  {
    return $this->type;
  }

  public function getContext():string
  {
    return $this->context;
  }

  public function getDeadEnd():?DateTime
  {
    return $this->deadend;
  }

  public function getUserId(): ?int
  {
    return $this->userId;
  }

  public function getSupportId():?int
  {
    return $this->supportId;
  }

  // |-----------------|
  // |--- VERIFIERS ---|
  // |-----------------|
  public function isValid()
  {
    return
      ($this->status->id === Status::STATUS_ATIVADO)
      && ($this->deadend === null || $this->deadend >= new DateTime("now"));
  }
}
