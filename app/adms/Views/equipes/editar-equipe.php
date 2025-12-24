<?php

use App\adms\UI\Form;

$fields = require "partials/form-modelo.php";

return Form::create("editar-equipe/{$equipe["id"]}")
  ->addFields($fields)
  ->withTitle("Editar equipe")
  ->render();