<?php
use App\adms\Helpers\CSRFHelper;
?>
<h1 class="titulo titulo--1">Redefina a sua senha</h1>
<form method="post" class="form">
  <div class="form__campo">
    <label>Código da empresa</label>
    <input class="input" type="number" name="servidor_id" id="codigoEmpresa" value="" required>
  </div>
  <div class="form__campo">
    <label>Email</label>
    <input class="input" type="mail" name="usuario_email">
  </div>
  <input type="hidden" name="csrf_token" value="<?= CSRFHelper::generateCSRFToken("form_nova_senha"); ?>">
    <a href="<?= $_ENV['HOST_BASE'] ?>login/">Voltar</a>
  <button type="submit" class="btn">Entrar</button>
</form>