<?php

namespace App\adms\Presenters;

use App\adms\Core\AppContainer;
use App\adms\Helpers\CelularFormatter;
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
    /** @var Usuario $usuario */
    foreach($usuarios as $usuario){
      $final[] = [
        "id" => $usuario->getId() ?? "",
        "nome" => $usuario->getNome() ?? "Nome inválido.",
        "email" => $usuario->getEmail() ?? "Email inválido.",
        "celular" => self::normalizeCelular($usuario->getCelular()),
        "foto_perfil" => self::normalizeFoto($usuario->getId(), $usuario->getFoto()),
        "nivel_badge" => self::nivelBadge($usuario),
        "status_badge" => self::statusBadge($usuario),
        "button" => self::buttons(
          $usuario->getStatusId(),
          $usuario->getId(),
          $usuario->getNome())
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
      $servidorId = AppContainer::getAuthUser()->getServidorId();
      return "<img src='{$_ENV['HOST_BASE']}files/uploads/{$servidorId}/fotos-perfil/{$id}.{$fotoPerfil}'>";
    }
  }

  private static function nivelBadge(Usuario $usuario):Badge
  {
    return Badge::create($usuario->getNivelAcessoNome() ?? "Nível Inválido", "silver")
      ->tooltip($usuario->getNivelAcessoDescricao() ?? "");
  }

  private static function statusBadge(Usuario $usuario):Badge
  {
    return Badge::create(
      $usuario->getStatusNome() ?? "Status inválido",
      self::getUsuarioStatusClass($usuario->getStatusId())
      )
      ->tooltip($usuario->getStatusDescricao() ?? "");
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
            "action" => "usuario:editar",
            "usuario-id" => $id
          ])
          ->tooltip("Editar Usuário")
          ->withIcon("pencil"),

      "reenviar-email" =>
        Button::create("")
          ->color("gray")
          ->data([
            "action" => "usuario:reenviar-email",
            "usuario-id" => $id,
            "usuario-nome" => $nome
          ])
          ->tooltip("Reenviar email de confirmação/redefinição de senha")
          ->withIcon("envelope"),

      "desativar" => 
        Button::create("")
          ->color("red")
          ->data([
            "action" => "usuario:desativar",
            "usuario-id" => $id,
            "usuario-nome" => $nome
          ])
          ->tooltip("Desativar Usuário")
          ->withIcon("trash-can"),

      "reativar" => 
        Button::create("")
          ->color("green")
          ->data([
            "action" => "usuario:reativar",
            "usuario-id" => $id,
            "usuario-nome" => $nome
          ])
          ->tooltip("Reativar Usuário")
          ->withIcon("rotate"),

      "resetar-senha" => 
        Button::create("")
          ->color("gray")
          ->data([
            "action" => "usuario:resetar-senha",
            "usuario-id" => $id,
            "usuario-nome" => $nome
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