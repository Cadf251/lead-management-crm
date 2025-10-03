<?php
use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\CSRFHelper;

$celular = CelularFormatter::paraPlaceholder($this->data['usuario']['u_celular'] ?? "");

$csrf = CSRFHelper::generateCSRFToken("form_usuario");

$content = <<<HTML
  <label>Qual é o nome do novo usúario?</label>
  <input class="form-padrao__input" type="text" name="nome" value="{$this->data['usuario']['u_nome']}" required>
  <label>Qual é o email do novo usuário?</label>
  <input class="form-padrao__input" type="text" name="email" value="{$this->data['usuario']['u_email']}" required>
  <label>Qual é o celular do novo usuário?</label>
  <input class="form-padrao__input phone" type="text" name="celular" value="$celular" maxlength="14" placeholder="(xx)9xxxx-xxxx" class="phone" required>
  $foto
  <label>Qual será o nível de acesso do usuário?</label>
  <select class="form-padrao__input" name="nivel_acesso_id" required>
    <option value="">Selecionar...</option>
    {$this->data["form-options"]}
  </select>
  <input type="hidden" name="csrf_token" value="{$csrf}">
  $inputs
HTML;