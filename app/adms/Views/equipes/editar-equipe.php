<?php

use App\adms\UI\Form;

$fields = require "partials/form-modelo.php";

return Form::create("equipes/editar/{$equipe["id"]}")
  ->addFields($fields)
  ->withTitle("Editar equipe")
  ->isAjax()
  ->render();