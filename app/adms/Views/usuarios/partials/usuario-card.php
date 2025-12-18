<?php

use App\adms\Helpers\HTMLHelper;

$btns = "";

foreach($usuario["button"] as $button){
  if($button["type"] === "link"){
    $btns .= HTMLHelper::renderButtonLink($button["href"], $button["icon"], $button["title"], $button["color"]);
  } else {
    $btns .= HTMLHelper::renderButtonAjax($button["function"], $button["color"], $button["icon"], $button["title"]);
  }
}

$content = <<<HTML
  <div class="centered">
    <div class="foto"><img src='{$usuario["foto_perfil"]}' height='100%' width='100%'></div>
    <div class="usr-content">
      <p><b>{$usuario['nome']}</b></p>
      <p>{$usuario['email']}</p>
      <p>{$usuario['celular']}</p>
      <p>
        <span class="underline" title="{$usuario['nivel_descricao']}">{$usuario['nivel_nome']}</span><br>
        <span class="underline" title="{$usuario['status_descricao']}">{$usuario["status_nome"]}</span>
      </p>
    </div>
  </div>
  <div class="card__icons">
    $btns
  </div>
HTML;

$final = HTMLHelper::renderCard($content, ["card-padrao--thinner"]);

return <<<HTML
<div class="card--{$usuario['id']}">
  $final
</div>
HTML;