<!DOCTYPE html>
<html lang="pt-br">
<?php

use App\adms\Helpers\SafeEcho;
include_once "app/adms/Views/partials/head.php";
?>
<body>
  <div class="warning"></div>
  <main class="main--login">
    <div class="container">
      <header class="center w7">
        <img width="65%" src="<?php echo $_ENV['HOST_BASE'] ?>public/img/logo.webp" class="login-logo" alt="Logo">
      </header>
      <div class="card-main w5">
        <?php
        include_once $this->view;
        ?>
      </div>
    </div>
  </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?= $_ENV["HOST_BASE"] ?>public/js/main.min.js?<?= mt_rand(0, 1000) ?>"></script>
  <?php
  include_once "app/adms/Views/partials/alertas.php";
  ?>
</body>
</html>