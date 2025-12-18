<?php

namespace App\adms\Presenters;

use App\adms\Helpers\CelularFormatter;
use App\adms\Models\Usuario;

class UsuarioPresenter
{
  public static function present(array $usuarios):array {
    $final = [];
    foreach($usuarios as $usuario){
      $final[] = [
        "id" => $usuario->id,
        "nome" => $usuario->nome,
        "email" => $usuario->email,
        "celular" => self::normalizeCelular($usuario->celular),
        "foto_perfil" => self::normalizeFoto($usuario->id, $usuario->foto),
        "nivel_id" => $usuario->nivel->id,
        "nivel_nome" => $usuario->nivel->nome,
        "nivel_descricao" => $usuario->nivel->descricao,
        "status_id" => $usuario->status->id,
        "status_nome" => $usuario->status->nome,
        "status_descricao" => $usuario->status->descricao,
        "button" => self::buttons($usuario->status->id, $usuario->id, $usuario->nome)
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
      return "{$_ENV['HOST_BASE']}files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/{$id}.{$fotoPerfil}";
    }
  }

  private static function buttons(int $statusId, int $id, string $nome){
    $btns = [
      "editar" => [
        "type" => "link",
        "href" => "{$_ENV['HOST_BASE']}editar-usuario/{$id}",
        "icon" => "pencil",
        "title" => "Editar Usuário",
        "color" => "normal"
      ],
      "reenviar-email" => [
        "type" => "ajax",
        "function" => <<<JS
          setWarning(
            "Reenviar email de {$nome}?",
            "Será reenviado o email de confirmação de senha.",
            true,
            () => {
              window.location.href = "{$_ENV['HOST_BASE']}reenviar-email/{$id}";
            }
          )
        JS,
        "color" => "gray",
        "icon" => "evelope",
        "title" => "Reenviar email de confirmação/redefinição de senha"
      ],
      "desativar" => [
        "type" => "ajax",
        "function" => <<<JS
          setWarning(
            "Deseja desativar o {$nome}?",
            "O usuário será desativado. A ação é reversível.",
            true,
            () => {
              window.location.href = "{$_ENV['HOST_BASE']}desativar-usuario/{$id}";
            }
          )
        JS,
        "color" => "alerta",
        "icon" => "trash-can",
        "title" => "Desativar Usuário"
      ],
      "reativar" => [
        "type" => "ajax",
        "function" => <<<JS
          setWarning(
            "Deseja reativar o usuário {$nome}?",
            "Ele terá acesso a praticamente tudo que tinha antes.",
            true,
            () => {
              postRequest(
                "{$_ENV['HOST_BASE']}ativar-usuario/{$id}",
                "",
                (response) => {
                  renderizar(response.html, ".card--{$id}");
                }
              )
            }
          )
        JS,
        "color" => "gray",
        "icon" => "rotate",
        "title" => "Reativar Usuário"
      ],
      "alterar-senha" => [
        "type" => "ajax",
        "function" => <<<JS
          setWarning(
            "Deseja apagar a senha do {$nome}?", 
            "Ao apagar a senha, será enviado o email para o usuário criar uma nova.", 
            true,
            () => {
              window.location.href = "{$_ENV['HOST_BASE']}recuperar-senha/{$id}";
            }
          )
        JS,
        "color" => "gray",
        "icon" => "key",
        "title" => "Alterar senha do Usuário"
      ]
    ];

    return match($statusId){
      1 => [
        $btns["editar"],
        $btns["reenviar-email"],
        $btns["desativar"]
      ],
      2 => [
        $btns["reativar"],
        $btns["desativar"],
      ],
      3 => [
        $btns["editar"],
        $btns["alterar-senha"],
        $btns["desativar"]
      ]
    };
  }
}