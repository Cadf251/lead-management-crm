<?php

namespace App\adms\UI;

class InfoBox
{
  private int $type = self::TYPE_INFO;
  private string $title = "";
  private $message = "";

  private array $validTypes = [
    self::TYPE_ALERT,
    self::TYPE_INFO,
    self::TYPE_WARN
  ];

  const TYPE_INFO = 0;
  const TYPE_WARN = 1;
  const TYPE_ALERT = 2;

  public static function create(string $title, string $message):self
  {
    $instance = new self();
    $instance->title = $title;
    $instance->message = $message;
    return $instance;
  }

  public function setType(int $type):self
  {
    if(!in_array($type, $this->validTypes)) return $this;
    $this->type = $type;
    return $this;
  }

  public function render()
  {
    switch($this->type){
      case self::TYPE_ALERT:
        $color = "red";
        $icon = "x";
        break;
      case self::TYPE_INFO:
        $color = "silver";
        $icon = "circle-info";
        break;
      case self::TYPE_WARN:
        $color = "yellow";
        $icon = "triangle-exclamation";
        break;
    }

    return <<<HTML
    <div class="info-box info-box--{$color}">
      <h4 class="titulo titulo--3">{$this->title}</h4>
      <p><i class="fa-solid fa-{$icon}"></i> {$this->message}</p>
    </div>
    HTML;
  }

  public function __toString()
  {
    return $this->render();
  }
}