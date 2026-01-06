<?php

use App\adms\Presenters\NavPresenter;

// interpretate user data
$nav = NavPresenter::present();

?>
<nav class="nav nav--preload js--nav">
  <button type="button" class="nav__button js--nav-button">
    <i class="fa-solid fa-bars"></i>
  </button>
  <div class="nav__userdata">
    <div class="foto">
      <?= $nav["foto"] ?>
    </div>
    <div class="nav__texto">
      <?= $nav["usuario_nome"] ?>
      <br>
      <?= $nav["nivel_acesso_nome"] ?>
    </div>
  </div>
  <div class="nav__icons">
    <?= $nav["links"] ?>
  </div>
</nav>