<?php

use App\adms\UI\Badge;
use App\adms\UI\Button;
use App\adms\UI\Card;
use App\adms\UI\Header;
use App\database\Models\Tenant;


echo Header::create("Listar servidores")
  ->addButton(Button::create("+ Criar")
  ->color("black")
  ->link("master/criar-servidor"));

foreach ($this->data["tenants"] as $tenant) {

  $content = <<<HTML
  <div>
    <h2 class="titulo titulo--3">{$tenant["name"]}</h2>
    Contato: {$tenant["contact"]}<br>
    Api Token: {$tenant["api_token"]}
  </div>
  <div class="card__header__info">
    <strong>Database info</strong>
    Host: {$tenant["host"]}<br>
    DB Name: {$tenant["db_name"]}<br>
    User Name: {$tenant["db_user"]}
  </div>
  <div class="card__inline-items">
    {$tenant["status_badge"]}
    {$tenant["buttons"]}
  </div>
  <!-- Falta botão de instalar -->
  HTML;

  echo Card::create($content);
}