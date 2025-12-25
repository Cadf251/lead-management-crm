<?php

namespace App\adms\UI;

class Button
{
  private string $label = '';
  private ?string $icon = null;
  private string $color = 'blue';
  private string $tooltip = '';
  private string $href = '';
  private array $dataset = [];
  private bool $switch = false;
  private bool $disabled = false;
  private bool $switchAtivo = true;

  public static function create(string $label = ''): self
  {
    $instance = new self();
    $instance->label = $label;
    return $instance;
  }

  public function withIcon(string $icon): self
  {
    $this->icon = $icon;
    return $this;
  }

  public function color(string $color): self
  {
    $this->color = $color;
    return $this;
  }

  public function tooltip(string $text): self
  {
    $this->tooltip = $text;
    return $this;
  }

  /**
   * Se definido, renderiza como <a>. Caso contrário, como <button>.
   */
  public function link(string $href): self
  {
    $this->href = $_ENV["HOST_BASE"].$href;
    return $this;
  }

  /**
   * Adiciona atributos data-*
   */
  public function data(array $data): self
  {
    $this->dataset = array_merge($this->dataset, $data);
    return $this;
  }

  public function switch()
  {
    $this->switch = true;
    return $this;
  }

  public function setSwitch(bool $set)
  {
    $this->switchAtivo = $set;
    return $this;
  }

  public function setDisabled()
  {
    $this->disabled = true;
    return $this;
  }

  public function render(): string
  {
    if ($this->disabled) {
      $disabled = "disabled";
    } else {
      $disabled = "";
    }

    // Monta o dataset
    $dataAttrs = "";
    foreach ($this->dataset as $key => $value) {
      $dataAttrs .= " data-{$key}='{$value}'";
    }
  
    if($this->icon !== null) {
      $icon = <<<HTML
      <i class="fa-solid fa-{$this->icon}"></i>
      HTML;
    }

    // Se tiver href, é um link. Se não, é um botão
    if ($this->href) {
      return <<<HTML
      <a href="{$this->href}" class="small-btn small-btn--{$this->color}" title="{$this->tooltip}">
        $icon
        {$this->label}
      </a>
      HTML;
    }
    
    if($this->switch){
      if($this->switchAtivo) {
        $class= "ativado";
      } else {
        $class="desativado";
      }
      return <<<HTML
      <button
        type="button"
        class="switch-btn switch-btn--{$this->color} switch-btn--{$class}"
        title="{$this->tooltip}"
        {$dataAttrs}
        {$disabled}
      >{$icon} $this->label</button>
      HTML;
    }

    return <<<HTML
    <button
      type="button"
      class="small-btn small-btn--{$this->color}"
      title="{$this->tooltip}"
      {$dataAttrs}
      {$disabled}
    >{$icon} $this->label</button>
    HTML;
  }

  public function __toString(): string
  {
    return $this->render();
  }
}
