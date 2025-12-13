<!DOCTYPE html>
<html lang="pt-br">
<?php

use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\SafeEcho;

include_once "./app/adms/Views/partials/head.php";
?>
<body class="js--body">
  <div class="warning"></div>
  <?php
  include_once "./app/adms/Views/partials/nav.php";
  ?>
  <main class="main js--main">
    <script src="<?php echo SafeEcho::safeEcho($_ENV["HOST_BASE"]) ?>public/adms/js/nav.js"></script>
    <?php
    include_once $this->view;
    ?>
  </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?php echo SafeEcho::safeEcho($_ENV["HOST_BASE"]) ?>public/adms/js/scripts.js?v=12902190"></script>
  <?php
  if(isset($this->data["js"])){
    if(is_array($this->data["js"])){
      foreach ($this->data["js"] as $jsLink){
        echo <<<HTML
          <script src="{$base}{$jsLink}"></script>
        HTML;
      }
    } else {
      GenerateLog::generateLog("warning", "O CSS adicional não está sendo passado como array.", ["css" => $this->data["css"]]);
    }
  }
  include_once "./app/adms/Views/partials/alertas.php";
  ?>
</body>
</html>