<?php

if (!isset($offer)) return "";

$content = <<<HTML
<div class="card__header">
  <div>
    <strong>{$offer["name"]}</strong>
    <div class="subinfo">
      <span>{$offer["description"]}</span>
    </div>
  </div>
</div>
<div class="card__inline-items">

</div>
HTML;