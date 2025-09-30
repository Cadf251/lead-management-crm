<!DOCTYPE html>
<html lang="pt-br">
<?php

use App\adms\Helpers\SafeEcho;

include_once "./app/adms/Views/partials/head.php";
?>
<body class="js--body">
  <main class="main main--external-error js--main">
    <script src="<?php echo SafeEcho::safeEcho($_ENV["HOST_BASE"]) ?>public/adms/js/nav.js"></script>
    <?php
    include_once $this->view;
    ?>
  </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?php echo SafeEcho::safeEcho($_ENV["HOST_BASE"]) ?>public/adms/js/scripts.js"></script>
</body>
</html>