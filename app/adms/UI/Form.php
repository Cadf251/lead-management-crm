<?php

namespace App\adms\UI;

use App\adms\UI\Field;

class Form
{
  private ?string $title = null;
  private array $fields = [];
  private string $action = '';
  private string $content = "";
  private string $cta = 'Enviar';
  private bool $hasFiles = false;

  public static function create(string $action = ''): self
  {
    $instance = new self();
    $instance->action = $_ENV['HOST_BASE'] . $action;
    return $instance;
  }

  public function withContent(string $content):self
  {
    $this->content = $content;
    return $this;
  }

  public function withTitle(string $title):self
  {
    $this->title = $title;
    return $this;
  }

  public function addField(Field $field): self
  {
    $this->fields[] = $field;
    return $this;
  }

  /**
   * Adiciona vários campos de uma vez
   * @param Field[] $fields
   */
  public function addFields(array $fields): self
  {
    foreach ($fields as $field) {
      $this->addField($field);
    }
    return $this;
  }

  public function withFiles(): self
  {
    $this->hasFiles = true;
    return $this;
  }
  public function buttonLabel(string $label): self
  {
    $this->cta = $label;
    return $this;
  }

  public function render(): string
  {
    $enctype = $this->hasFiles ? ' enctype="multipart/form-data"' : '';
    $fieldsHtml = implode("\n", $this->fields);

    $title = "";
    if ($this->title !== null) {
      $title = <<<HTML
      <h2 class="titulo titulo--2">{$this->title}</h2>
      HTML;
    }

    return <<<HTML
    <form class="form" method="post" action="{$this->action}"{$enctype}>
      $title
      {$this->content}
      {$fieldsHtml}
      <button type="submit" class="small-btn small-btn--blue">{$this->cta}</button>
    </form>
    HTML;
  }

  public function __toString()
  {
    return $this->render();
  }
}
