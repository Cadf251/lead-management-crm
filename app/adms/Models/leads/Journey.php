<?php

namespace App\adms\Models\leads;

use App\adms\Models\products\Offer;
use App\api\Controllers\Oferta;
use Exception;

class Journey
{
  private int $id;
  private int $leadId;
  private JourneyStatus $status;
  private Offer $offer;
  private array $interactions;

  public static function new(
    int $leadId,
    int $statusId,
    int $ofertaId,
    ?Interaction $interaction = null
  ): self
  {
    $instance = new self();
    $instance->setLeadId($leadId);
    $instance->setStatus($statusId);
    $instance->setOferta($ofertaId);
    $instance->setInteraction($interaction);
    return $instance;
  }

  // |---------------|
  // |--- SETTERS ---|
  // |---------------|

  public function setId(int $id)
  {
    $this->id = $id;
  }

  public function setLeadId(int $id)
  {
    $this->leadId = $id;
  }

  /**
   * @throws Exception
   */
  public function setStatus(int $id)
  {
    try {
      $this->status = new JourneyStatus($id);
    } catch (Exception $e) {
      throw new Exception("Invalid Lead Jorney Status.");
    }
  }

  public function setOferta()
  {
    $this->offer = new Offer();
  }

  public function setInteraction(Interaction $interaction)
  {
    $this->interactions[] = $interaction;
  }


  // |---------------|
  // |--- GETTERS ---|
  // |---------------|

  public function getId():int
  {
    return $this->id;
  }

  public function getLeadId():int
  {
    return $this->leadId;
  }

  public function getStatusId():int
  {
    return $this->status->getId();
  }

  public function getStatusName():string
  {
    return $this->status->getName();
  }

  public function getStatusDescription():string
  {
    return $this->status->getDescription();
  }

  public function getOferta():Offer
  {
    return $this->offer;
  }

  public function getInteractions():array
  {
    return $this->interactions;
  }

  // |----------------|
  // |--- CHANGERS ---|
  // |----------------|

  public function descard()
  {
    $this->status = new JourneyStatus(JourneyStatus::STATTUS_DISCARDED);
  }

  public function feeding()
  {
    $this->status = new JourneyStatus(JourneyStatus::STATUS_FEEDING);
  }

  public function ready()
  {
    $this->status = new JourneyStatus(JourneyStatus::STATUS_READY);
  }
}