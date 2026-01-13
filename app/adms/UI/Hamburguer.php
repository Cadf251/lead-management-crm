<?php

namespace App\adms\UI;

class Hamburguer
{
  private string $icons = "";

  public static function create(string $icons):self
  {
    $instance = new self();
    $instance->setIcons($icons);
    return $instance;
  }

  public function setIcons(string $icons)
  {
    $this->icons = $icons;
  }

  public function render()
  {
    return <<<HTML
    <div class="hamburguer">
      <div class="hamburguer__controller js--hamb-controll">
        <i class="fa-solid fa-ellipsis-vertical"></i>
      </div>
      <div class="hamburguer__content js--hamb-content">
        {$this->icons}
      </div>
    </div>
    HTML;
  }

  public function __toString()
  {
    return $this->render();
  }
}