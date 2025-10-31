<!DOCTYPE html>
<html lang="pt-br">
<?php

use App\adms\Helpers\SafeEcho;
include_once "app/adms/Views/partials/head.php";
?>
<body>
  <div class="warning"></div>
  <main class="main main--login js-main">
    <header class="centered">
      <img src="<?php echo $_ENV['HOST_BASE'] ?>public/adms/img/logo.png" class="login-logo" alt="Logo">
    </header>
    <?php
    include_once $this->view;
    ?>
  </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?php echo SafeEcho::safeEcho($_ENV["HOST_BASE"]) ?>public/adms/js/scripts.js?v=1"></script>
  <?php
  include_once "app/adms/Views/partials/alertas.php";
  ?>
</body>
</html>