<?php

namespace App\adms\UI;

class Card
{
  private string $content = '';
  private string $title = '';
  private string $type = 'object'; // Valor padrão conforme seu código original
  private array $extraClasses = [];

  /**
   * Inicia a criação do Card com o conteúdo principal
   */
  public static function create(string $content = ''): self
  {
    $instance = new self();
    $instance->content = $content;
    return $instance;
  }

  /**
   * Define o título do Card (renderiza o h3 automaticamente)
   */
  public function withTitle(string $title): self
  {
    $this->title = $title;
    return $this;
  }

  /**
   * Define o modificador de tipo (ex: object, info, alert)
   * Resulta na classe CSS: card--{$type}
   */
  public function type(string $type): self
  {
    $this->type = $type;
    return $this;
  }

  /**
   * Adiciona classes CSS extras manualmente
   */
  public function addClass(string $className): self
  {
    $this->extraClasses[] = $className;
    return $this;
  }

  /**
   * Renderiza o HTML final
   */
  public function render(): string
  {
    $titleHtml = $this->title ? "<h3 class='titulo titulo--3'>{$this->title}</h3>" : "";
    $classes = implode(' ', $this->extraClasses);

    // Montamos o card com o modificador de tipo e classes extras
    return <<<HTML
    <div class="card--{$this->type} {$classes}">
        {$titleHtml}
        {$this->content}
    </div>
    HTML;
  }

  public function __toString(): string
  {
    return $this->render();
  }
}
