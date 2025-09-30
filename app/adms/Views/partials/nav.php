<nav class="nav nav--preload js--nav">
  <button type="button" class="nav__button" onclick="resizeNav()">
    <i class="fa-solid fa-bars"></i>
  </button>
  <div class="nav__userdata">
    <div class="foto">
      <?php
      // if (!empty($_SESSION['foto_perfil']) || $_SESSION["foto_perfil"] !== null)
      //   echo <<<HTML
      //     <img height="100%" width="100%" src="{$dominio}{$_SESSION['foto_perfil']}">
      //   HTML;

      use App\adms\Helpers\HTMLHelper;

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
      echo HTMLHelper::renderNavLink("usuarios", "user", "Editar Usuários");

    if (in_array(2, $_SESSION["permissoes"]) || in_array(4, $_SESSION["permissoes"]))
      echo HTMLHelper::renderNavLink("equipes", "user-group", "Editar Equipes");

    echo HTMLHelper::renderNavLink("leads", "list", "Gerenciar Leads");
    echo HTMLHelper::renderNavLink("recursos", "toolbox", "Meus Recursos");
    echo HTMLHelper::renderNavLink("carteira-clientes", "wallet", "Carteira de Clientes");
    echo HTMLHelper::renderNavLink("publicos", "chart-simple", "Públicos");
    echo HTMLHelper::renderNavLink("holerites", "file-invoice-dollar", "Holerites");
    echo HTMLHelper::renderNavLink("configuracao", "gear", "Configurações");
    ?>
    <form action="login" method="post">
      <input type="hidden" name="task" value="deslogar">
      <button class="nav__link nav__link--button" type="submit">
        <i class="fa-solid fa-power-off"></i>
        <span class="nav__texto"> Sair</span>
      </button>
    </form>
  </div>
</nav>