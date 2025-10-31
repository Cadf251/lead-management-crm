<?php

use App\adms\Helpers\CSRFHelper;

$csrf = CSRFHelper::generateCSRFToken("form_equipe");

$equipe = $this->data["equipe"];

$content = <<<HTML
  <label>Qual é o nome da nova equipe?</label>
  <input class="form-padrao__input" placeholder="Insira o nome" maxlength="50" type="text" name="nome" value="{$equipe['nome']}" required>
  <label>Descrição da equipe</label>
  <textarea class="form-padrao__input" rows="4" placeholder="Insira uma descrição" maxlength="255" name="descricao">{$equipe['descricao']}</textarea>
  <label>Qual produto está associado a esta equipe?</label>
  <select class="form-padrao__input" name="produto_id" required>
    <option value="" required>Selecionar...</option>
    {$this->data["produtos-options"]}
  </select>
  <input type="hidden" name="csrf_token" value="{$csrf}">
HTML;