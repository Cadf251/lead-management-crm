<?php

use App\adms\Helpers\HTMLHelper;

echo HTMLHelper::renderHeader("Criar Usuário", "{$_ENV['HOST_BASE']}usuarios/", "Voltar", "left-long");

$foto = <<<HTML
  <label>Faça o upload da foto do usuário</label>
  <input class="form-padrao__input" type="file" name="foto">
HTML;

// Forma um $content com o formulário
include_once "form-modelo.php";

echo HTMLHelper::thinnerForm("Insira os dados", $content, "Criar");