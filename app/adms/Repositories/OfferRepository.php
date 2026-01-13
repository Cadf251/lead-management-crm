<?php

namespace App\adms\Repositories;

use App\adms\Models\sales\Offer;
use Exception;

class OfferRepository extends RepositoryBase
{
  private string $table = "ofertas";

  public function queryBase(): string
  {
    return <<<SQL
    SELECT 
      o.id AS oferta_id, o.nome AS oferta_nome, o.descricao AS oferta_descricao, o.status_id AS oferta_status_id,
      p.id AS produto_id, p.nome AS produto_nome, p.descricao AS produto_descricao
    FROM {$this->table} o
    INNER JOIN produtos p ON o.produto_id = p.id
    SQL;
  }

  /**
   * @return ?array<Offer>
   */
  public function list(): ?array
  {
    try {
      return $this->sql->selectMultiple(
        $this->queryBase(),
        fn(array $row) => $this->hydrateOffer($row)
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function select(int $id): ?Offer
  {
    $query = <<<SQL
    {$this->queryBase()}
    WHERE o.id = :oferta_id
    SQL;

    $params = [
      "oferta_id" => $id
    ];

    try {
      return $this->sql->selectOne(
        $query,
        fn(array $row) => $this->hydrateOffer($row),
        $params
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  private function hydrateOffer(array $row):Offer
  {
    $offer = new Offer();
    $offer->setId($row["oferta_id"]);
    $offer->setName($row["oferta_nome"]);
    $offer->setDescription($row["oferta_descricao"]);
    $offer->setStatus($row["oferta_status_id"]);
    $offer->setProduct(
      $row["produto_id"],
      $row["produto_nome"],
      $row["produto_descricao"],
      null
    );
    return $offer;
  }

  public function create(Offer $offer): int
  {
    $params = [
      "nome" => $offer->getName(),
      "descricao" => $offer->getDescription(),
      "status_id" => $offer->getStatusId(),
      "produto_id" => $offer->getProductId()
    ];

    try {
      return $this->sql->insert($this->table, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function save(Offer $offer): void
  {
    $params = [
      "nome" => $offer->getName(),
      "descricao" => $offer->getDescription(),
      "status_id" => $offer->getStatusId(),
      "produto_id" => $offer->getProductId()    
    ];

    try {
      $this->sql->updateById($this->table, $params, $offer->getId());
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}