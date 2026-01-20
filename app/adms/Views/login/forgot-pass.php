<?php

use App\adms\Helpers\CSRFHelper;
use App\adms\UI\Form;

?>
<h1 class="titulo titulo--1">Redefina a sua senha</h1>
<?php

$csrf = "forgot_pass";

$allFields = require "partials/fields.php";

$fields = [
  $allFields[0],
  $allFields[1],
  $allFields[3],
];

echo Form::create("")
  ->addFields($fields)
  ->addExtraContent(<<<HTML
  <a href="{$_ENV['HOST_BASE']}login/">Voltar</a>
  HTML)
  ->isAjax();