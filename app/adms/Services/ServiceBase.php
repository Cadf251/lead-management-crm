<?php

namespace App\adms\Services;

use App\adms\Core\OperationResult;

abstract class ServiceBase
{
  private OperationResult $result;

  public function __construct()
  {
    $this->result = new OperationResult();
  }
}