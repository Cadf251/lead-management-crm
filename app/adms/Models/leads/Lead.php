<?php

namespace App\adms\Models\leads;

use App\adms\Models\Pessoa;
use DateTime;

class Lead extends Pessoa
{
  private float $score = 0;
  private DateTime $created;
  private array $journeys;
  private Perfil $perfil;

  public function __construct(
    string $nome,
    ?string $email,
    ?string $celular
  )
  {
    $this->setNome($nome);
    $this->setEmail($email);
    $this->setCelular($celular);
  }

  public static function new(
    string $nome,
    string $email,
    string $celular
  ){
    $instance = new self(
      $nome,
      $email,
      $celular
    );
    $instance->setCreatedAsNow();
  }

  public function setCreated(DateTime $time)
  { 
    $this->created = $time;
  }

  public function setCreatedAsNow()
  {
    return $this->setCreated(new DateTime("now"));
  }

  public function setJourney(Journey $journey)
  {
    $this->journeys[] = $journey;
  }
}