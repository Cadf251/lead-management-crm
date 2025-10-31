<?php

use App\adms\Helpers\HTMLHelper;

echo HTMLHelper::renderHeader("Editar Equipe", "{$_ENV['HOST_BASE']}listar-equipes/", "Voltar", "left-long");

include_once "partials/form-modelo.php";

echo HTMLHelper::thinnerForm("Insira os dados", $content, "Criar", true);