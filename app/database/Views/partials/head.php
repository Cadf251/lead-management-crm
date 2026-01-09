<?php
use App\adms\Helpers\SafeEcho;
?>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="shortcut icon" href="<?= $_ENV["HOST_BASE"] ?> ?>public/adms/img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="<?= $_ENV["HOST_BASE"] ?> ?>public/adms/css/pallets/main-colors.css">
  <link rel="stylesheet" href="<?= $_ENV["HOST_BASE"] ?> ?>public/adms/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
  <title>
    <?php
    echo isset($this->data["title"]) ? $this->data["title"] : "CRM";
    ?>
  </title>
</head>