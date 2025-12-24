<?php

namespace App\adms\UI;

class Header
{
  private string $title;
  private string $buttons = "";

  public static function create(string $title):self
  {
    $instance = new self();
    $instance->title = $title;
    return $instance;
  }

  public function addButton(string $button)
  {
    $this->buttons .= $button;
    return $this;
  }

  public function render()
  {
    return <<<HTML
    <div class="task-header">
      <div>
        <h1 class="titulo titulo--2">{$this->title}</h1>
      </div>
      <div class="task-header__buttons">
        {$this->buttons}
      </div>
    </div>
    HTML;
  }

  public function __toString(): string
  {
    return $this->render();
  }
}