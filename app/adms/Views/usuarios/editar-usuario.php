<?php

use App\adms\UI\Form;

$usuario = $this->data["usuarios"][0];

$semFoto = "";

if ($usuario['foto_perfil'] === "") $semFoto = " sem--foto";

$content = <<<HTML
<div class="editar-usuario col-center">
  <label class="js--delete-foto">
    <input type="radio" name="apagar-foto">
    <div class="foto foto--editar{$semFoto}">
      {$usuario['foto_perfil']}
      <div class="foto--delete"><i class='fa-solid fa-trash-can'></i></div>
    </div>
  </label>
  <h2 class="titulo titulo--2">{$usuario["nome"]}</h2>
</div>
HTML;

$fields = require "partials/form-modelo.php";

return Form::create("usuarios/editar/{$usuario['id']}")
  ->addFields($fields)
  ->withFiles()
  ->isAjax()
  ->withContent($content)
  ->render();