<!DOCTYPE html>
<html lang="pt-br">
<?php
include_once "./app/adms/Views/partials/head.php";
?>
<body class="js--body">
  <div class="warning"></div>
  <?php
  include_once "./app/adms/Views/partials/nav.php";
  ?>
  <main class="main js--main">
    <?php
    include_once $this->view;
    ?>
  </main>
  <?php
  include_once "./app/adms/Views/partials/alertas.php";
  include_once "app/adms/Views/partials/overlay.php";
  ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?= $_ENV["HOST_BASE"] ?>public/js/main.min.js?<?= mt_rand(0, 1000) ?>"></script>
</body>
</html>