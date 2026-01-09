<?php

namespace App\adms\Services;

use App\adms\Models\leads\Interaction;
use App\adms\Models\leads\Lead;

/**
 * Classe responsável por distrubuir as demandas pelo sistema
 * 
 * @goal Receber leads e interações e definir em qual etapa da jornada ele se encaixa
 */
class DistributorService
{

  public function findTeamByOffer(int $offerId)
  {
    
  }
}