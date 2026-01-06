<?php

namespace App\adms\Models\leads;

class Perfil
{
  private int $id;
  private int $leadId;
  private string $dados;

  public function __construct(
    int $id,
    int $leadId,
    string $dados
  )
  {
    $this->setId($id);
    $this->setLeadId($leadId);
    $this->setDados($dados);
  }

  public function setId(int $id):void
  {
    $this->id = $id;
  }

  public function setLeadId(int $id):void
  {
    $this->leadId = $id;
  }

  public function setDados(string $json):void
  {
    $this->dados = $json;
  }
}

class PerfilStatus
{

}