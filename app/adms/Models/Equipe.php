<?php

namespace App\adms\Models;

use DomainException;
use Exception;

class Equipe
{
  public ?int $id;
  public string $nome;
  public ?string $descricao;
  public Status $status;
  public ?Produto $produto;

  public array $colaboradores = [];

  public const STATUS_DESATIVADO = 1;
  public const STATUS_PAUSADO = 2;
  public const STATUS_ATIVADO = 3;

  public static function novo(
    string $nome,
    Produto $produto,
    ?string $descricao
  ): self {
    $equipe = new self();
    $equipe->setNome($nome);
    $equipe->setProduto($produto);
    $equipe->setDescricao($descricao ?? null);
    $equipe->setStatus(self::STATUS_ATIVADO);
    return $equipe;
  }

  /**
   * Seta o ID para STATUS_DESATIVADO (1)
   * 
   * @throws DomainException
   */
  public function desativar(): void
  {
    if ($this->status->id === self::STATUS_DESATIVADO) {
      throw new DomainException("Essa equipe já está desativada.");
    }

    $this->setStatus(self::STATUS_DESATIVADO);
  }

  /**
   * Pausa uma equipe STATUS_PAUSADO (2)
   * @throws DomainException
   */
  public function pausar(): void
  {
    if ($this->status->id === self::STATUS_PAUSADO) {
      throw new DomainException("Essa equipe já está pausada.");
    }

    $this->setStatus(self::STATUS_PAUSADO);
  }

  /**
   * Ativa (ou despausa) uma equipe STATUS_ATIVADO (3)
   * 
   * @throws DomainException
   */
  public function ativar(): void
  {
    if ($this->status->id === self::STATUS_ATIVADO) {
      throw new DomainException("Essa equipe já está ativada.");
    }

    $this->setStatus(self::STATUS_ATIVADO);
  }
  # | -------------------------|
  # | GETTERS                  |
  # | -------------------------|

  public function getRecebemLeads(): array
  {
    $recebem = [];
    foreach ($this->colaboradores as $colaborador) {
      if ($colaborador->recebeLeads()) {
        $recebem[] = $colaborador;
      }
    }
    return $recebem;
  }

  public function getProximos(int $quantidade = 3)
  {
    $recebem = $this->getRecebemLeads();

    if (empty($recebem)) return [];

    $array = [];
    foreach ($recebem as $recebe) {
      $array[] = [
        "id" => $recebe->id,
        "vez" => $recebe->vez
      ];
    }

    for ($i = 0; $i < $quantidade; $i++) {
      // Ordena pelo menor VEZ e, em empate, menor ID
      usort($array, function ($a, $b) {
        if ($a["vez"] === $b["vez"]) {
          return $a["id"] <=> $b["id"];
        }
        return $a["vez"] <=> $b["vez"];
      });

      // Pega o OBJETO, não o array, do proximo
      $proximos[] = $this->getColaboradorById($array[0]["id"]);

      // Incrementa a vez de quem foi escolhido
      $array[0]["vez"]++;
    }

    return $proximos;
  }

  public function getColaboradorById(int $id): ?EquipeUsuario
  {
    foreach ($this->colaboradores as $colaborador) {
      if ($colaborador->id === $id) {
        return $colaborador;
      }
    }
    return null;
  }

  public function getVezMinima()
  {
    $vez = 0;

    if (empty($this->colaboradores)) return 0;

    foreach ($this->colaboradores as $colab) {
      if ($vez < $colab->vez) {
        $vez = $colab->vez;
      }
    }

    return $vez;
  }

  public function countColaboradores(): int
  {
    return count($this->colaboradores);
  }

  public function detailColaborador() {}

  # | -------------------------|
  # | SETTERS                  |
  # | -------------------------|

  /**
   * O ID da equipe
   * 
   * @param int $id
   */
  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function setNome(string $nome)
  {
    $this->nome = $nome;
  }

  public function setDescricao(?string $descricao)
  {
    if (empty($descricao)) $descricao = null;
    $this->descricao = $descricao;
  }

  public function setStatus(int $id)
  {
    if (!in_array($id, [
      self::STATUS_PAUSADO,
      self::STATUS_DESATIVADO,
      self::STATUS_ATIVADO
    ])) {
      throw new Exception("Status inválido.");
    }

    $this->status = Status::fromId($id);
  }

  public function setProduto(?Produto $produto): void
  {
    $this->produto = $produto;
  }

  /**
   * Seta o Produto com um array associativo.
   * 
   * @param array $produto Chaves: int id, string nome, ?string descricao
   */
  public function setProdutoByArray(?array $produto): void
  {
    if ((!isset($produto["id"]) && (!is_int($produto["id"])))
      || (!isset($produto["nome"]) && (!is_string($produto["nome"])))
    ) {
      throw new Exception("Produto inválido");
    }

    $this->produto = new Produto($produto["id"], $produto["nome"], $produto["descricao"] ?? null);
  }

  /**
   * Um array de EquipesUsuario
   */
  public function setColaboradores(array $colaboradores)
  {
    $this->colaboradores = $colaboradores;
  }

  public function removerColaborador(int $id)
  {
    /** @var EquipesUsuario $colab */
    foreach ($this->colaboradores as $key => $colab){
      if ($colab->getId() === $id) {
        unset($this->colaboradores[$key]);
      }
    }
  }
}
