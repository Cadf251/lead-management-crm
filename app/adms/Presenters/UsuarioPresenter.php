<?php

namespace App\adms\Presenters;

use App\adms\Helpers\CelularFormatter;
use App\adms\Models\Usuario;
use App\adms\UI\Button;

class UsuarioPresenter
{
  public static function present(array $usuarios):array {
    $final = [];
    /** @var Usuario $usuario */
    foreach($usuarios as $usuario){
      $final[] = [
        "id" => $usuario->getId(),
        "nome" => $usuario->getNome(),
        "email" => $usuario->getEmail(),
        "celular" => self::normalizeCelular($usuario->getCelular()),
        "foto_perfil" => self::normalizeFoto($usuario->getId(), $usuario->getFoto()),
        "nivel_id" => $usuario->getNivelAcessoId(),
        "nivel_nome" => $usuario->getNivelAcessoNome(),
        "nivel_descricao" => $usuario->getNivelAcessoDescricao(),
        "status_id" => $usuario->getStatusId(),
        "status_nome" => $usuario->getStatusNome(),
        "status_descricao" => $usuario->getStatusDescricao(),
        "status_class" => self::getUsuarioStatusClass($usuario->getStatusId()),
        "button" => self::buttons($usuario->getStatusId(), $usuario->getId(), $usuario->getNome())
      ];
    }
    return $final;
  }

  private static function normalizeCelular(string $celular){
    if(empty($celular) || !isset($celular) || $celular === null){
      return "Telefone vazio ou inválido!";
    } else {
      return CelularFormatter::paraPlaceholder($celular);
    }
  }

  private static function normalizeFoto(int $id, ?string $fotoPerfil){
    if(empty($fotoPerfil) || !isset($fotoPerfil) || $fotoPerfil === null){
      return "";
    } else {
      return "<img src='{$_ENV['HOST_BASE']}files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/{$id}.{$fotoPerfil}'>";
    }
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
      1 => [
        $btns["editar"],
        $btns["reenviar-email"],
        $btns["desativar"]
      ],
      2 => [
        $btns["reativar"]
      ],
      3 => [
        $btns["editar"],
        $btns["resetar-senha"],
        $btns["desativar"]
      ]
    };
  }
}