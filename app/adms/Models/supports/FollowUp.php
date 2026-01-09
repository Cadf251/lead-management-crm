<?php

namespace App\adms\Models\supports;

use App\adms\Models\traits\ComumId;
use DateTime;

class FollowUp
{
  use ComumId;
  /** @var int $supportId for back-tracing */
  private int $supportId;

  private string $method;
  private DateTime $schedule;
  private int $status;
}