<?php

use App\adms\Helpers\HTMLHelper;

echo HTMLHelper::renderHeader("Editar Usuário", "{$_ENV['HOST_BASE']}usuarios/", "Voltar", "left-long");

if (empty($usuario["u_foto_perfil"]) || $usuario["u_foto_perfil"] === null)
  $foto = <<<HTML
    <label>Faça o upload da foto do usuário</label>
    <input class="form-padrao__input form-padrao__input--thinner" type="file" name="foto">
    <input type="hidden" name="foto_existe" value="0">
  HTML;
else 
  $foto = <<<HTML
    <div class="centered w12">
    <div class="foto margin-0">
      <img src="{$usuario['u_foto_perfil']}" height="100%" width="100%">
    </div>
    <div class="foto-input">
      <div class="foto-input__container">
        <input type="radio" name="editar_foto" id="foto-a" value="trocar" class="trigger">
        <label for="foto-a">Trocar foto de perfil atual</label>
        <input class="form-padrao__input foto-input__input-file form-padrao__input--thinner" type="file" name="foto">
      </div>
      <div>
        <input type="radio" name="editar_foto" value="apagar" id="foto-b"><label for="foto-b">Apagar foto de perfil atual</label></div>
        <button class="normal-link" onclick="limparInput(['#foto-a', '#foto-b'])" type="button">Limpar</button>
        <input type="hidden" name="foto_existe" value=1>
      </div>
    </div>
  HTML;

// Forma um $content com o formulário
include_once "form-modelo.php";

echo HTMLHelper::thinnerForm("Insira os dados", $content, "Editar");