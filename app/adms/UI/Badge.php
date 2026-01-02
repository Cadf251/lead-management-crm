<?php

namespace App\adms\UI;

class Badge
{
  private string $label;
  private string $color;
  private string $tooltip = "";
  private string $class = "";

  public static function create(string $label, string $color):self
  {
    $instance = new self();
    $instance->label = $label;
    $instance->color = $color;
    return $instance;
  }

  public function tooltip(string $tooltip):self
  {
    $this->tooltip = $tooltip;
    return $this;
  }

  public function addClass(string $class)
  {
    $this->class = $class;
    return $this;
  }

  public function render()
  {
    return <<<HTML
    <span 
    class="small-badge small-badge--{$this->color} {$this->class}"
    title="{$this->tooltip}">
      {$this->label}
    </span>
    HTML;
  }

  public function __toString()
  {
    return $this->render();
  }
}