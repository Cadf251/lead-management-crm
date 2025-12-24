<?php
use App\adms\Helpers\CSRFHelper;
?>
<h1 class="titulo titulo--1">Crie sua nova senha</h1>
<form method="post" class="form">
  <div class="form__campo">
    <label>Insira sua nova Senha</label>
    <div class="campo-senha">
      <input class="input" type="password" name="usuario_senha">
      <i class="pass-icon fa-solid fa-eye"></i>
    </div>
  </div>
  <input type="hidden" name="csrf_token" value="<?= CSRFHelper::generateCSRFToken("form_confirmar"); ?>">
    <a href="<?= $_ENV['HOST_BASE'] ?>login/">Voltar</a>
  <button type="submit" class="btn">Entrar</button>
</form>