<?php

namespace App\adms\Models\leads;

use App\adms\Models\Pessoa;
use App\adms\Models\traits\DateHandler;

class Lead extends Pessoa
{
  use DateHandler;

  private float $score = 0;
  // private Profile $profile;
  // private array $journeys;

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
    return new self(
      $nome,
      $email,
      $celular
    );
  }

  // public function setJourney(Journey $journey)
  // {
  //   $this->journeys[] = $journey;
  // }

  // public function setProfile(Profile $profile)
  // {
  //   $this->profile = $profile;
  // }
}