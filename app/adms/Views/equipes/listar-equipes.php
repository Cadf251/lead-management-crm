<?php

use App\adms\UI\Button;
use App\adms\UI\Header;

// Cria o botão do header
$button = Button::create("+ Criar")
  ->color("black")
  ->data(["action" => "equipe:criar"]);

$header = Header::create("Gerenciar Equipes")
  ->addButton($button);

echo $header;

foreach ($this->data["equipes"] as $equipe){
  echo require "partials/equipe-card.php";
}
?>