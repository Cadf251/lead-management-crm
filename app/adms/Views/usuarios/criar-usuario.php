<?php

use App\adms\UI\Form;

// Forma um $content com o formulário
$fields = require "partials/form-modelo.php";

return Form::create("usuarios/criar")
  ->addFields($fields)
  ->withFiles()
  ->withTitle("Criar usuário")
  ->isAjax()
  ->render();