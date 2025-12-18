<?php

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\HTMLHelper;

if (!isset($this->data["usuarios"])) {
  echo "Nenhum usuário";
  die();
}

// Cria o botão do header
$href = "{$_ENV['HOST_BASE']}criar-usuario";
echo HTMLHelper::renderHeader("Editar Usuários", $href, "Criar um novo usuário", "plus");

// Lê o array de usuários e imprime cada um
foreach ($this->data["usuarios"] as $usuario) {
  echo require APP_ROOT."app/adms/Views/usuarios/partials/usuario-card.php";
}
?>