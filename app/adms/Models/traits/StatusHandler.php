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
    return $this->status->id;
  }

  public function getStatusName(): string
  {
    return $this->status->nome;
  }

  // |---------------|
  // |--- SETTERS ---|
  // |---------------|
  public function setStatus(int $id)
  {
    try {
      $this->status = Status::fromId($id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  // |----------------|
  // |--- CHANGERS ---|
  // |----------------|
  public function disable()
  {
    $this->setStatus(Status::STATUS_DESATIVADO);
  }

  public function pause()
  {
    $this->setStatus(Status::STATUS_PAUSADO);
  }

  public function activate()
  {
    $this->setStatus(Status::STATUS_ATIVADO);
  }

  // |-----------------|
  // |--- VERIFIERS ---|
  // |-----------------|
  public function isActive(): bool
  {
    return $this->status->id === Status::STATUS_ATIVADO;
  }
}
