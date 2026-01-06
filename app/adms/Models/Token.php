<?php

namespace App\adms\Models;

use DateTime;

/**
 * language: EN
 */
abstract class Token
{
  private int $id;
  private string $token;
  private string $type;
  private string $context;
  private ?DateTime $prazo;
  private Status $status;
  private int $userId;

  public const TYPE_SYSTEM = "system";

  public const CONTEXT_CONFIRMAR_EMAIL = "confirm_email";

  /**
   * Cria um novo TOKEN
   */
  public static function new(
    int $userId,
    string $type,
    ?string $context = null,
    ?DateTime $prazo = null,
  ): self {
    $instance = new self();
    $instance->token = bin2hex(random_bytes(16));
    $instance->userId = $userId;
    $instance->type = $type;
    $instance->context = $context;
    $instance->prazo = $prazo;
    return $instance;
  }
}
