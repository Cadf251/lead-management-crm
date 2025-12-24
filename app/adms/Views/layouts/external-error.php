<!DOCTYPE html>
<html lang="pt-br">
<?php

use App\adms\Helpers\SafeEcho;
use App\adms\Services\AuthUser;

include_once "./app/adms/Views/partials/head.php";
?>
<body class="js--body">
  <?php
  if (AuthUser::logado())
    include_once "./app/adms/Views/partials/nav.php";
  else
    $class = "main--external-error";
  ?>
  <main class="main <?php echo $class ?? "" ?> js--main">
    <?php
    include_once $this->view;
    ?>
  </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?= $_ENV["HOST_BASE"] ?>public/js/main.min.js?<?= mt_rand(0, 1000) ?>"></script>
</body>
</html>