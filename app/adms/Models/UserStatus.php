<?php

namespace App\adms\Models;

use InvalidArgumentException;

/**
 * language: EN
 */
class UserStatus extends StatusAbstract
{
  public const STATUS_CONFIRMACAO = 1;
  public const STATUS_DESATIVADO = 2;
  public const STATUS_ATIVADO = 3;

  protected function getMap(int $id): array
  {
    if (!in_array($id, [
      self::STATUS_ATIVADO,
      self::STATUS_CONFIRMACAO,
      self::STATUS_DESATIVADO
    ])) {
      throw new InvalidArgumentException("Invalid User Status");
    }

    return match ($id) {
      self::STATUS_CONFIRMACAO => [
        'name' => "Aguardando Confirmação",
        "description" => "O usuário precisa acessar o e-mail cadastrado para confirmar sua conta e definir uma senha."
      ],
      self::STATUS_DESATIVADO => [
        'name' => "Desligado",
        "description" => "O acesso do usuário foi desativado. Seus dados permanecem armazenados, mas ele não poderá mais acessar o sistema."
      ],
      self::STATUS_ATIVADO => [
        'name' => "Ativo",
        "description" => "O usuário possui acesso completo e está autorizado a utilizar o sistema."
      ]
    };
  }
}
