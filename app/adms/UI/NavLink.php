<?php

namespace App\adms\UI;

class NavLink
{
  private string $href;
  private string $icon;
  private string $label;

  public static function create(
    string $href,
    string $icon,
    string $label
  ):self
  {
    $inst = new self();
    $inst->href = $href;
    $inst->icon = $icon;
    $inst->label = $label;
    return $inst;
  }

  public function render():string
  {
    return <<<HTML
    <a href="{$_ENV['HOST_BASE']}{$this->href}" class="nav__link"><i class="fa-solid fa-{$this->icon}"></i> <span class="nav__texto">{$this->label}</span></a>
    HTML;
  }

  public function __toString():string
  {
    return $this->render();
  }
}