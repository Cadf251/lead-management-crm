<?php

namespace App\adms\UI;

class Header
{
  private string $title;
  private string $badges = "";
  private string $buttons = "";
  private ?string $description = null;

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

  public function addBadge(string $badge)
  {
    $this->badges .= $badge;
    return $this;
  }

  public function withDescription(?string $description)
  {
    $this->description = $description;
    return $this;
  }

  public function render()
  {
    $desc = "";
    if ($this->description !== null) {
      $desc = <<<HTML
      <p class="task-header__main__description">
        {$this->description}
      </p>
      HTML;
    }
    
    return <<<HTML
    <div class="task-header">
      <div class="task-header__main">
        <div class="task-header__main__titulos">
          <h1 class="titulo titulo--2">{$this->title}</h1>
          {$this->badges}
        </div>
        $desc
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