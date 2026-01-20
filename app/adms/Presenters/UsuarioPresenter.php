<?php

namespace App\adms\Presenters;

use App\adms\Core\AppContainer;
use App\adms\Helpers\CelularFormatter;
use App\adms\Models\users\User;
use App\adms\Models\Usuario;
use App\adms\UI\Badge;
use App\adms\UI\Button;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class UsuarioPresenter
{
  public static function present(array $usuarios):array {
    $final = [];
    /** @var User $usuario */
    foreach($usuarios as $usuario){
      $final[] = [
        "id" => $usuario->getId() ?? "",
        "nome" => $usuario->getName() ?? "Nome inválido.",
        "email" => $usuario->getEmail() ?? "Email inválido.",
        "celular" => self::normalizeCelular($usuario->getPhone()),
        "foto_perfil" => self::normalizeFoto($usuario->getId(), $usuario->getProfilePicture()),
        "nivel_badge" => self::nivelBadge($usuario),
        "status_badge" => self::statusBadge($usuario),
        "button" => self::buttons(
          $usuario->getStatusId(),
          $usuario->getId(),
          $usuario->getName())
      ];
    }
    return $final;
  }

  private static function normalizeCelular(string $celular){
    if(empty($celular)){
      return "Telefone vazio ou inválido!";
    } else {
      return CelularFormatter::paraPlaceholder($celular);
    }
  }

  private static function normalizeFoto(int $id, ?string $fotoPerfil){
    if(empty($fotoPerfil)){
      return "";
    } else {
      $servidorId = AppContainer::getAuthUser()->getServerId();
      return "<img src='{$_ENV['HOST_BASE']}files/uploads/{$servidorId}/fotos-perfil/{$id}.{$fotoPerfil}'>";
    }
  }

  private static function nivelBadge(User $usuario):Badge
  {
    return Badge::create($usuario->getSystemLevelName() ?? "Nível Inválido", "silver")
      ->tooltip($usuario->getSystemLevelDescription() ?? "");
  }

  private static function statusBadge(User $usuario):Badge
  {
    return Badge::create(
      $usuario->getStatusName() ?? "Status inválido",
      self::getUsuarioStatusClass($usuario->getStatusId())
      )
      ->tooltip($usuario->getStatusDescription() ?? "");
  }

  /** Diferente da UtilPresenter::getStatusClass */
  public static function getUsuarioStatusClass(int $statusId)
  {
    $classes = [
      1 => "blue",
      2 => "red",
      3 => "green",
    ];

    return $classes[$statusId] ?? "gray";
  }

  private static function buttons(int $statusId, int $id, string $nome){
    $btns = [
      "editar" =>
        Button::create("")
          ->color("blue")
          ->data([
            "action" => "action:core",
            "url" => "usuarios/editar/$id",
            "action-type" => "overlay"
          ])
          ->tooltip("Editar Usuário")
          ->withIcon("pencil"),

      "reenviar-email" =>
        Button::create("")
          ->color("gray")
          ->data([
            "action" => "action:core",
            "url" => "usuarios/reenviar-email/$id",
            "confirm" => true,
            "confirm-title" => "Deseja reenviar o email do $nome?",
            "confirm-text" => "O email de redefinição de senha senha enviado.",
          ])
          ->tooltip("Reenviar email de confirmação/redefinição de senha")
          ->withIcon("envelope"),

      "desativar" => 
        Button::create("")
          ->color("red")
          ->data([
            "action" => "action:core",
            "url" => "usuarios/desativar/$id",
            "confirm" => true,
            "confirm-title" => "Deseja desativar o usuário $nome?",
            "confirm-text" => "Ele perderá o acesso, mas seus dados continuarão no histórico do sistema.",
            "target" => ".card--$id"
          ])
          ->tooltip("Desativar Usuário")
          ->withIcon("trash-can"),

      "reativar" => 
        Button::create("")
          ->color("green")
          ->data([
            "action" => "action:core",
            "url" => "usuarios/reativar/$id",
            "confirm" => true,
            "confirm-title" => "Deseja reativar o usuário $nome?",
            "confirm-text" => "Ele terá acesso a praticamente tudo que tinha antes.",
            "target" => ".card--$id"
          ])
          ->tooltip("Reativar Usuário")
          ->withIcon("rotate"),

      "resetar-senha" => 
        Button::create("")
          ->color("gray")
          ->data([
            "action" => "action:core",
            "url" => "usuarios/resetar-senha/$id",
            "confirm" => true,
            "confirm-title" => "Deseja resetar a senha do usuário $nome?",
            "confirm-text" => "Ao resetar a senha, será enviado o email para o usuário criar uma nova.",
            "target" => ".card--$id"
          ])
          ->tooltip("Resetar senha do Usuário")
          ->withIcon("key"),
    ];

    return match($statusId){
      1 => <<<HTML
        {$btns["editar"]}
        {$btns["reenviar-email"]}
        {$btns["desativar"]}
      HTML,
      2 => <<<HTML
        {$btns["reativar"]}
      HTML,
      3 => <<<HTML
        {$btns["editar"]}
        {$btns["resetar-senha"]}
        {$btns["desativar"]}
      HTML
    };
  }
}