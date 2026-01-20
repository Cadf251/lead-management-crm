<?php

use App\adms\Core\AppContainer;
use App\adms\UI\Header;
use App\adms\UI\InfoBox;

$nome = AppContainer::getAuthUser()->getUserName();

echo Header::create("Olá, {$nome}");

echo InfoBox::create("Dashboard", "Os dashboards serão implementados na versão 2.0")
  ->setType(InfoBox::TYPE_INFO);
?>
