<?php

use App\adms\UI\Form;

?>
<h1 class="titulo titulo--1">Bem-vindo!</h1>
<?php

$csrf = "form_login";

$fields = require "partials/fields.php";

echo Form::create("")
  ->addFields($fields)
  ->addExtraContent(<<<HTML
  <a href="{$_ENV['HOST_BASE']}esqueci-senha/">Não tenho a senha</a>
  HTML)
  ->isAjax();
?>