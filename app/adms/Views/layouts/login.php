<!DOCTYPE html>
<html lang="pt-br">
<?php
include_once "app/adms/Views/partials/head.php";
?>
<body>
  <div class="warning"></div>
  <?php
  include_once $this->view;
  ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="js/scripts.js"></script>
  <script src="js/login.js"></script>
</body>
</html>