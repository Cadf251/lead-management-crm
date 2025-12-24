<?php
use App\adms\Helpers\CSRFHelper;
?>
<h1 class="titulo titulo--1">Bem-vindo!</h1>
<form method="post" class="form">
  <div class="form__campo">
    <label for="servidor_id">Código da empresa</label>
    <input class="input" type="number" name="servidor_id" id="servidor_id" value="<?= isset($_COOKIE["codigo_empresa"]) ? $_COOKIE["codigo_empresa"] : "" ?>" required>
  </div>
  <div class="form__campo">
    <label>Email</label>
    <input class="input" type="mail" name="usuario_email">
  </div>
  <div class="form__campo">
    <label>Senha</label>
    <div class="campo-senha">
      <input class="input" type="password" name="usuario_senha">
      <i class="pass-icon fa-solid fa-eye"></i>
    </div>
  </div>
  <input type="hidden" name="csrf_token" value="<?= CSRFHelper::generateCSRFToken("form_login"); ?>">
  <a href="<?= $_ENV['HOST_BASE'] ?>nova-senha/">Não tenho a senha</a>
  <button type="submit" class="btn">Entrar</button>
</form>
