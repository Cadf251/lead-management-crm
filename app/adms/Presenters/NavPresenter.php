<?php

namespace App\adms\Presenters;

use App\adms\Core\AppContainer;
use App\adms\Models\NivelSistema;
use App\adms\Models\SystemLevel;
use App\adms\UI\NavLink;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class NavPresenter
{
  public static function present()
  {
    $servidorId = AppContainer::getAuthUser()->getServerId();
    $usuarioId = AppContainer::getAuthUser()->getUserId();
    $usuarioNome = AppContainer::getAuthUser()->getUserName();
    $usuarioFoto = AppContainer::getAuthUser()->getUserProfilePicture();
    $nivelAcessoNome = AppContainer::getAuthUser()->systemLevel->getName();

    return [
      "servidor_id" => $servidorId ?? "",
      "usuario_nome" => $usuarioNome ?? "Nome Inválido",
      "nivel_acesso_nome" => $nivelAcessoNome,
      "foto" => self::normalizeFoto(
        $servidorId,
        $usuarioId,
        $usuarioFoto
      ),
      "links" => self::links(AppContainer::getAuthUser()->systemLevel)
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

  private static function links(SystemLevel $nivel)
  {
    $links = NavLink::create("dashboard", "house", "Início");

    if ($nivel->podeEditarUsuarios()) {
      $links .= NavLink::create("usuarios", "user", "Editar Usuários");
    }

    if ($nivel->podeEditarEquipes()) {
      $links .= NavLink::create("equipes", "user-group", "Editar Equipes");
    }

    if ($nivel->canEditOffers()) {
      $links .= NavLink::create("ofertas", "tag", "Editar Ofertas");
      $links .= NavLink::create("produtos", "basket-shopping", "Editar Produtos");
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