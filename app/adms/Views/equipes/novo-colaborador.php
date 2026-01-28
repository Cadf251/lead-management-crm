<?php

use App\adms\Helpers\CSRFHelper;
use App\adms\UI\Field;
use App\adms\UI\Form;

$usuarios = $this->data["usuarios"];
$funcoes = $this->data["funcoes"];
$csrf = CSRFHelper::generateCSRFToken("add_usuario");

$usuariosOpt = "";
foreach ($usuarios as $usuario) {
  $usuariosOpt .= <<<HTML
  <option value="{$usuario['usuario_id']}">{$usuario['usuario_nome']}</option>
  HTML;
}

$fields = [
  Field::create("Novo usuário", "usuario_id")
    ->type(Field::TYPE_SELECT)
    ->options($usuariosOpt)
    ->required()
    ->addClass("js--usuario-select"),

  Field::create("Função do usuário na equipe", "funcao_id")
    ->type(Field::TYPE_SELECT)
    ->required()
    ->options($funcoes ?? "")
    ->addClass("js--usuario-funcao"),
  
  Field::create("Poderá receber leads?", "pode_receber_leads")
    ->type(Field::TYPE_RADIO)
    ->required()
    ->addRadio("Sim", 1)
    ->addRadio("Não", 0),

  Field::create("", "csrf_token")
    ->type(Field::TYPE_HIDDEN)
    ->value($csrf)
];

return Form::create("novo/{$this->data["equipe_id"]}")
  ->addFields($fields)
  ->isAjax()
  ->withTitle("Novo colaborador")
  ->render();