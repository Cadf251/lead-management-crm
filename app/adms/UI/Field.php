<?php

namespace App\adms\UI;

class Field
{
  private string $label = '';
  private string $type = self::TYPE_TEXT;
  private string $name = '';
  private $value = '';
  private array $attrs = [];
  private string $optionsHtml = ''; // Para o Select
  private bool $inputOnly = false;
  private array $classes = ["input"];
  private array $radios = [];
  private bool $selectDefault = true;

  public const TYPE_TEXT = "text";
  public const TYPE_FILE = "file";
  public const TYPE_HIDDEN = "hidden";
  public const TYPE_SELECT = "select";
  public const TYPE_TEXTAREA = "textarea";
  public const TYPE_RADIO = "radio";

  public static function create(string $label, string $name): self
  {
    $instance = new self();
    $instance->label = $label;
    $instance->name = $name;
    return $instance;
  }

  public function type(string $type): self
  {
    $this->type = $type;
    return $this;
  }
  public function value($value): self
  {
    $this->value = $value;
    return $this;
  }
  public function placeholder(string $p): self
  {
    $this->attrs['placeholder'] = $p;
    return $this;
  }
  public function required(): self
  {
    $this->attrs['required'] = 'required';
    return $this;
  }

  public function maxLength(int $length):self
  {
    $this->attrs["maxlength"] = $length;
    return $this;
  }

  public function options(string $html): self
  {
    $this->optionsHtml .= $html;
    return $this;
  }

  public function inputOnly():self
  {
    $this->inputOnly = true;
    return $this;
  }

  public function addClass(string $class):self
  {
    $this->classes[] = $class;
    return $this;
  }

  public function addRadio(string $label, string $value)
  {
    $this->options(
      <<<HTML
      <label>
        <input type="radio" name="{$this->name}" value="$value">
        $label
      </label>
      HTML
    );
    return $this;
  }

  public function withoutDefaultOption()
  {
    $this->selectDefault = false;
    return $this;
  }

  public function render(): string
  {
    $attrStr = "";
    foreach ($this->attrs as $k => $v) $attrStr .= " $k=\"$v\"";

    $classes = implode(" ", $this->classes);

    if ($this->type === self::TYPE_HIDDEN) {
      return <<<HTML
      <input type="hidden" name="{$this->name}" value="{$this->value}">
      HTML;
    }

    // Template para File
    if ($this->type === self::TYPE_FILE) {
      return <<<HTML
      <div class="form__campo">
        <label class="input-file">
          {$this->label}
          <input class="$classes" type="file" name="{$this->name}" {$attrStr}>
        </label>
      </div>
      HTML;
    }

    // Template para Select
    if ($this->type === self::TYPE_SELECT) {
      if ($this->selectDefault) {
        $selectDefault = <<<HTML
        <option value="">Selecionar...</option>
        HTML;
      } else {
        $selectDefault = "";
      }
      if ($this->inputOnly) {
        return <<<HTML
        <select class="$classes" name="{$this->name}" {$attrStr}>
          $selectDefault
          {$this->optionsHtml}
        </select>
        HTML;
      } else {
        return <<<HTML
        <div class="form__campo">
          <label>{$this->label}</label>
          <select class="$classes" name="{$this->name}" {$attrStr}>
            $selectDefault
            {$this->optionsHtml}
          </select>
        </div>
        HTML;
      }
    }
    
    if ($this->type === self::TYPE_TEXTAREA) {
      return <<<HTML
      <div class="form__campo">
        <label>{$this->label}</label>
        <textarea
          class="$classes"
          rows="4"
          name="descricao"
          {$attrStr}
        >$this->value</textarea>
      </div>
      HTML;
    }

    if ($this->type === self::TYPE_RADIO) {
      return <<<HTML
      <div class="form__campo">
        <label>{$this->label}</label>
        {$this->optionsHtml}
      </div>
      HTML;
    }

    // Template padrão (text, email, tel, etc)
    return <<<HTML
    <div class="form__campo">
      <label>{$this->label}</label>
      <input class="$classes" type="{$this->type}" name="{$this->name}" value="{$this->value}" {$attrStr}>
    </div>
    HTML;
  }

  public function __toString()
  {
    return $this->render();
  }
}
