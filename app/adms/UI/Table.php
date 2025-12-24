<?php

namespace App\adms\UI;

class Table
{
  private ?string $title = null;
  private string $header;
  private string $rows;
  private string $class = "";

  public static function create(string $header, string $class):self
  {
    $instance = new self();
    $instance->header = $header;
    $instance->class = $class;
    return $instance;
  }

  public function withTitle(string $title)
  {
    $this->title = $title;
    return $this;
  }

  public function addRows(string $rows)
  {
    $this->rows = $rows;
    return $this;
  }

  public function render()
  {
    if ($this->title !== null) {
      $title = "<h2 class='titulo titulo--3'>{$this->title}</h2>";
    } else {
      $title = "";
    }

    return <<<HTML
    {$this->title}
    <table class="table {$this->class}">
      {$this->header}
      {$this->rows}
    </table>
    HTML;
  }

  public function __toString()
  {
    return $this->render();
  }
}