<?php

use App\adms\UI\Field;
use App\adms\UI\Form;

$field = Field::create("Selecione uma oferta", "offer_id")
  ->type(Field::TYPE_SELECT)
  ->options(<<<HTML
  <option>Não seja gay</option>
  HTML);

return Form::create("Atribuir")
  ->addField($field);