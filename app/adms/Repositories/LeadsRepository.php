<?php

namespace App\adms\Repositories;

use App\adms\Database\DbOperations;
use App\adms\Database\DbOperationsRefactored;
use App\adms\Models\leads\Lead;
use Exception;

class LeadsRepository extends DbOperations
{
  private $tabela = "leads";
  private DbOperationsRefactored $sql;

  public function __construct($conexao)
  {
    $this->sql = new DbOperationsRefactored($conexao);
    parent::__construct($conexao);
  }

  public function create(Lead $lead): int
  {
    $params = [
      "nome" => $lead->getNome(),
      "email" => $lead->getEmail(),
      "celular" => $lead->getCelular(),
      "created" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      return $this->sql->insert($this->tabela, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function select(int $id)
  {
    $query = <<<SQL
    SELECT id, nome, email, celular
    FROM leads
    WHERE id = :id
    SQL;

    $params = [
      "id" => $id
    ];

    try {
      $result = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($result)) {
      return null;
    }

    return $this->hydrate($result[0]);
  }

  public function selectByEmail(string $email):?Lead
  {
    $query = <<<SQL
    SELECT id, nome, email, celular
    FROM leads
    WHERE email = :email
    SQL;

    $params = [
      "email" => $email
    ];

    try {
      $result = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($result)) {
      return null;
    }

    return $this->hydrate($result[0]);
  }

  private function hydrate(array $row):Lead
  {
    $lead = new Lead(
      $row["nome"],
      $row["email"],
      $row["celular"]
    );
    $lead->setId($row["id"]);
    return $lead;
  }

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