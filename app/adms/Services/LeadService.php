<?php

namespace App\adms\Services;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\leads\Lead;
use App\adms\Repositories\LeadsRepository;
use Exception;
use PDO;

class LeadService
{
  private LeadsRepository $repository;

  public function __construct(PDO $conn)
  {
    $this->repository = new LeadsRepository($conn);
  }

  // Se preocupa em criar novo lead apenas.
  public function createLead(
    string $name,
    string $email,
    string $phone
  ):?Lead
  {
    try {
      $lead = $this->repository->selectByEmail($email);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      return null;
    }

    // Se o lead não existir, criar
    if ($lead === null) {
      $lead = Lead::new($name, $email, $phone);
      $leadId = $this->repository->create($lead);
      $lead->setId($leadId);
    }

    return $lead;
  }
}
