<?php

use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\HTMLHelper;

echo HTMLHelper::renderHeader("Equipe {$this->data['nome']}", "{$_ENV['HOST_BASE']}listar-equipes", "Voltar", "left-long");

$usuariosOpt = "";

foreach ($this->data["usuarios"] as $usuario){
  $usuariosOpt .= <<<HTML
    <option value="{$usuario['id']}, {$usuario['nivel_acesso_id']}">{$usuario['nome']}</option>
  HTML;
}

$funcoes = CreateOptions::criarOpcoes($this->data["funcoes"]);

$csrf = CSRFHelper::generateCSRFToken("add_usuario");

$content = <<<HTML
  <label>Qual é o novo usuário?</label>
  <select class="form-padrao__input js--usuario-select" name="usuario_id">
    <option value="">Selecione...</option>
    $usuariosOpt
  </select>
  <label>Qual é a função do novo usuário?</label>
  <select class="form-padrao__input js--usuario-funcao" name="funcao_id">
    <option value="1">Selecione...</option>
    $funcoes
  </select>
  <label>Pode receber leads?</label>
  <div>
    <input type="radio" name="recebe_leads" id="recebe-1" value="1">
    <label for="recebe-1">Sim</label> | 
    <input type="radio" name="recebe_leads" id="recebe-0" value="0">
    <label for="recebe-0">Não</label>
  </div>
  <input type="hidden" name="csrf_token" value="{$csrf}">
HTML;

echo HTMLHelper::thinnerForm("Adicionar usuário", $content, "Adicionar");