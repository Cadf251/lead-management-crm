<?php

namespace App\adms\Models\leads;

use DateTime;

class Interaction
{
  private int $id;
  private float $peso;
  private int $jornadaId;
  private string $tipo;
  private string $contexto;
  /** @var int $tempoEngajamento Em segundos */
  private int $tempoEngajamento;
}