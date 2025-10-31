<?php
use App\adms\Helpers\HTMLHelper;
?>
<nav class="nav nav--preload js--nav">
  <button type="button" class="nav__button" onclick="resizeNav()">
    <i class="fa-solid fa-bars"></i>
  </button>
  <div class="nav__userdata">
    <div class="foto">
      <?php
      if (!empty($_SESSION['foto_perfil']) || $_SESSION["foto_perfil"] !== null)
        echo <<<HTML
          <img src="{$_ENV['HOST_BASE']}files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/{$_SESSION['usuario_id']}.{$_SESSION['foto_perfil']}" height="100%" width="100%">
        HTML;
      ?>
    </div>
    <div class="nav__texto">
      <?php
      echo <<<HTML
        {$_SESSION['usuario_nome']}<br> 
        {$_SESSION["nivel_acesso_nome"]}
      HTML;
      ?>
    </div>
  </div>
  <div class="nav__icons">
    <?php
    echo HTMLHelper::renderNavLink("dashboard", "house", "Início");

    if (in_array(1, $_SESSION["permissoes"]))
      echo HTMLHelper::renderNavLink("listar-usuarios", "user", "Editar Usuários");

    if (in_array(2, $_SESSION["permissoes"]) || in_array(4, $_SESSION["permissoes"]))
      echo HTMLHelper::renderNavLink("listar-equipes", "user-group", "Editar Equipes");

    echo HTMLHelper::renderNavLink("leads", "list", "Gerenciar Leads");
    echo HTMLHelper::renderNavLink("recursos", "toolbox", "Meus Recursos");
    echo HTMLHelper::renderNavLink("carteira-clientes", "wallet", "Carteira de Clientes");
    echo HTMLHelper::renderNavLink("publicos", "chart-simple", "Públicos");
    echo HTMLHelper::renderNavLink("holerites", "file-invoice-dollar", "Holerites");
    echo HTMLHelper::renderNavLink("configuracao", "gear", "Configurações");
    echo HTMLHelper::renderNavLink("deslogar", "power-off", "Sair");
    ?>
  </div>
</nav>