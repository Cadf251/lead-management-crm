<?php

namespace App\adms\Models\supports;

use App\adms\Models\leads\Lead;
use App\adms\Models\teams\Colaborador;
use App\adms\Models\traits\ComumDescription;
use App\adms\Models\traits\ComumId;
use DateTime;

class Support
{
  use ComumId;
  use ComumDescription;

  private ?DateTime $fistContact = null;
  private bool $status;
  private int $contatoStatus;
  // private $jornada;
  public Colaborador $colaborador;
  
  // @todo V2 Trocar por jornada
  public Lead $lead;
  
  /** @var array([Retornos]) $retornos */
  public array $retornos;
}
