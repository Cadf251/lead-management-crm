<?php
use App\adms\Helpers\CSRFHelper;
?>
<form method="post" class="form-padrao form--login">
  <h1 class="titulo-2">Bem-vindo<br>Crie uma nova senha</h1>
  <label>Qual é o código da empresa?</label>
  <input class="form-padrao__input input--login" type="number" name="servidor_id" id="codigoEmpresa" value="">
  <label>Qual é o seu email?</label>
  <input class="form-padrao__input input--login" type="mail" name="usuario_email">
  <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken("form_nova_senha"); ?>">
  <a href="<?php echo $_ENV['HOST_BASE'] ?>login/">Voltar ao login</a>
  <button type="submit" class="btn">Recuperar Senha</button>
</form>