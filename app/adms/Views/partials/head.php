<?php
use App\adms\Helpers\SafeEcho;
$base = SafeEcho::safeEcho($_ENV['HOST_BASE']);
?>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="shortcut icon" href="<?php echo $base ?>public/adms/img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="<?php echo $base ?>public/adms/css/pallets/main-colors.css">
  <link rel="stylesheet" href="<?php echo $base ?>public/adms/css/styles.css">
  <?php

  use App\adms\Helpers\GenerateLog;

  if(isset($this->data["css"])){
    if(is_array($this->data["css"])){
      foreach ($this->data["css"] as $cssLink){
        echo <<<HTML
          <link rel="stylesheet" href="{$base}{$cssLink}">\n
        HTML;
      }
    } else {
      GenerateLog::generateLog("warning", "O CSS adicional não está sendo passado como array.", ["css" => $this->data["css"]]);
    }
  }
  ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
  <title>
    <?php
    echo isset($this->data["title"]) ? $this->data["title"] : "CRM";
    ?>
  </title>
</head>