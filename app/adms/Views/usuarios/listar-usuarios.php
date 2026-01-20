<?php

use App\adms\UI\Button;
use App\adms\UI\Header;

// Cria o botão do header
$button = Button::create("+ Criar")
  ->color("black")
  ->data([
    "action" => "action:core",
    "url" => "usuarios/criar/",
    "action-type" => "overlay"
  ]);

$header = Header::create("Gerenciar Usuários")
  ->addButton($button);

echo $header;

// Lê o array de usuários e imprime cada um
foreach ($this->data["usuarios"] as $user) {
  echo require APP_ROOT . "app/adms/Views/usuarios/partials/usuario-card.php";
}
