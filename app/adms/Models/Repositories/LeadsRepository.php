<?php

namespace App\adms\Models\Repositories;

use App\adms\Models\Services\DbOperations;

class LeadsRepository extends DbOperations
{
  private $tabela = "leads";

  /**
   * Cria um lead e retorna o ID criado ou false se falhar
   * 
   * @param string $nome
   * @param string $email
   * @param string $celular
   * 
   * @return int|false
   */
  public function criarLead(string $nome, string $email, string $celular):int|false
  {
    $params = [
      ":nome" => $nome,
      ":email" => $email,
      ":celular" => $celular,
      ":created" => date($_ENV['DATE_FORMAT'])
    ];

    return $this->insertSQL($this->tabela, $params);
  }

  public function insertUtm(int $leadId, array $utm)
  {
    $params = [
      ":origem" => $utm["origem"],
      ":meio" => $utm["meio"],
      ":campanha" => $utm["campanha"],
      ":conteudo" => $utm["conteudo"],
      ":termo" => $utm["termo"],
      ":palavra_chave" => $utm["palavra"],
      ":gclid" => $utm["gclid"],
      ":fbclid" => $utm["fbclid"],
      ":lead_id" => $leadId
    ];

    $this->insertSQL("lead_utm", $params);
  }

  /**
   * Retorna o ID do lead se ele existir ou false se não existir
   * 
   * @param string $email
   * @param string $celular
   * 
   * @return int|false
   */
  public function leadExiste(string $email, string $celular):int|false
  {
    $query = <<<SQL
    SELECT id
    FROM leads
    WHERE
      (email = :email)
      OR (celular = :celular)
    LIMIT 1
    SQL;

    $params = [
      ":email" => $email,
      ":celular" => $celular
    ];

    $result = $this->executeSQL($query, $params);

    if (empty($result) || ($result === false) || ($result[0]["id"] === 0))
      return false;
    else
      return $result[0]["id"];
  }
}