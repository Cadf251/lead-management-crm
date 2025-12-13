<?php

use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\HTMLHelper;

echo HTMLHelper::renderHeader("Gerenciar Equipes", "{$_ENV['HOST_BASE']}criar-equipe/", "Crie uma nova equipe", "plus");

foreach ($this->data["equipes"] as $equipe){
  echo require "partials/equipe-card.php";
}
?>