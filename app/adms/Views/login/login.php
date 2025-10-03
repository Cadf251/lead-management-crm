<?php
use App\adms\Helpers\CSRFHelper;
?>
<form method="post" class="form-padrao form--login">
  <h1 class="titulo-2">Bem-vindo<br>Faça seu login</h1>
  <label>Qual é o código da empresa?</label>
  <input class="form-padrao__input input--login" type="number" name="servidor_id" id="codigoEmpresa" value="">
  <label>Qual é o seu email?</label>
  <input class="form-padrao__input input--login" type="mail" name="usuario_email">
  <label>Qual é a sua senha?</label>
  <div class="show-password">
    <input class="form-padrao__input input--login" type="password" name="usuario_senha" id="input">
    <i onclick="showPassword('input', 'icon')" id="icon" class="pass-icon fa-solid fa-eye"></i>
  </div>
  <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken("form_login"); ?>">
  <a href="<?php echo $_ENV['HOST_BASE'] ?>nova-senha/">Não tenho a senha</a>
  <button type="submit" class="btn">Fazer login</button>
</form>