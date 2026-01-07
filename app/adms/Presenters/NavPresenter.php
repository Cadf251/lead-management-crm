<?php

namespace App\adms\Presenters;

use App\adms\Core\AppContainer;
use App\adms\Models\NivelSistema;
use App\adms\UI\NavLink;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class NavPresenter
{
  public static function present()
  {
    $servidorId = AppContainer::getAuthUser()->getServidorId();
    $usuarioId = AppContainer::getAuthUser()->getUsuarioId();
    $usuarioNome = AppContainer::getAuthUser()->getUsuarioNome();
    $usuarioFoto = AppContainer::getAuthUser()->getUsuarioFoto();
    $nivelAcessoNome = AppContainer::getAuthUser()->nivelSistema->getNome();

    return [
      "servidor_id" => $servidorId ?? "",
      "usuario_nome" => $usuarioNome ?? "Nome Inválido",
      "nivel_acesso_nome" => $nivelAcessoNome,
      "foto" => self::normalizeFoto(
        $servidorId,
        $usuarioId,
        $usuarioFoto
      ),
      "links" => self::links(AppContainer::getAuthUser()->nivelSistema)
    ];
  }

  private static function normalizeFoto(int $servidorId, int $usuarioId, ?string $usuarioFoto):string
  {
    if ($usuarioFoto !== null) {
      return <<<HTML
      <img src="{$_ENV['HOST_BASE']}files/uploads/$servidorId/fotos-perfil/$usuarioId.$usuarioFoto" height="100%" width="100%">
      HTML;
    } else {
      return "";
    }
  }

  private static function links(NivelSistema $nivel)
  {
    $links = NavLink::create("dashboard", "house", "Início");

    if ($nivel->podeEditarUsuarios()) {
      $links .= NavLink::create("listar-usuarios", "user", "Editar Usuários");
    }

    if ($nivel->podeEditarEquipes()) {
      $links .= NavLink::create("listar-equipes", "user-group", "Editar Equipes");
    }

    $links .= NavLink::create("em-atendimento", "list", "Gerenciar Leads");

    /*
        echo HTMLHelper::renderNavLink("em-atendimento", "list", "Gerenciar Leads");
    echo HTMLHelper::renderNavLink("recursos", "toolbox", "Meus Recursos");
    echo HTMLHelper::renderNavLink("carteira-clientes", "wallet", "Carteira de Clientes");
    echo HTMLHelper::renderNavLink("publicos", "chart-simple", "Públicos");
    echo HTMLHelper::renderNavLink("holerites", "file-invoice-dollar", "Holerites");
    echo HTMLHelper::renderNavLink("configuracao", "gear", "Configurações");
    */
    $links .= NavLink::create("deslogar", "power-off", "Sair");

    return $links;
  }
}