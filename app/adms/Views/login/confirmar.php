<?php
use App\adms\Helpers\CSRFHelper;
?>
<form method="post" class="form-padrao form--login">
  <h1 class="titulo-2">Bem-vindo<br>Crie sua nova senha</h1>
  <label>Insira sua nova Senha</label>
  <div class="show-password">
    <input class="form-padrao__input input--login" type="password" name="usuario_senha" id="input">
    <i onclick="showPassword('input', 'icon')" id="icon" class="pass-icon fa-solid fa-eye"></i>
  </div>
  <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken("form_confirmar"); ?>">
  <a href="<?php echo $_ENV['HOST_BASE'] ?>login/">Voltar</a>
  <button type="submit" class="btn">Fazer login</button>
</form>