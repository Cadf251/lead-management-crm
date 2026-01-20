<?php

namespace App\adms\Models\traits;

use App\adms\Models\Status;
use Exception;

/**
 * Usar com o Status genérico apenas. Para uso de outros status, usar StatusAbstract
 */
trait StatusHandler
{
  // |---------------|
  // |--- GETTERS ---|
  // |---------------|
  public function getStatusId():int
  {
    return $this->status->getId();
  }

  public function getStatusName(): string
  {
    return $this->status->getName();
  }

  public function getStatusDescription(): string
  {
    return $this->status->getDescription();
  }

  // |---------------|
  // |--- SETTERS ---|
  // |---------------|
  public function setStatus(int $id)
  {
    try {
      $this->status = new Status($id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}
