<?php

use App\adms\UI\Form;

// Forma um $content com o formulário
$fields = require "partials/form-modelo.php";

return Form::create("criar-usuario")
  ->addFields($fields)
  ->withFiles()
  ->withTitle("Criar usuário")
  ->render();