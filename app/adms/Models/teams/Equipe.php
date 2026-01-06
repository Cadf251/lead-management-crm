<?php

namespace App\adms\Models\teams;

use App\adms\Models\Produto;
use App\adms\Models\Status;
use DomainException;
use Exception;

class Equipe
{
  private ?int $id;
  private string $nome;
  private ?string $descricao = null;
  private Status $status;
  private int $funcao;
  private array $ofertas;

  private array $colaboradores = [];

  public const STATUS_DESATIVADO = Status::STATUS_DESATIVADO;
  public const STATUS_PAUSADO = Status::STATUS_PAUSADO;
  public const STATUS_ATIVADO = Status::STATUS_ATIVADO;

  public static function novo(
    string $nome,
    ?string $descricao
  ): self {
    $equipe = new self();
    $equipe->setNome($nome);
    $equipe->setDescricao($descricao ?? null);
    $equipe->setStatus(self::STATUS_ATIVADO);
    return $equipe;
  }

  # | -------------------------|
  # | GETTERS                  |
  # | -------------------------|

  public function getId():?int
  {
    return $this->id;
  }

  public function getNome():string
  {
    return $this->nome;
  }

  public function getDescricao():?string
  {
    return $this->descricao;
  }

  public function getStatusId():int
  {
    return $this->status->id;
  }

  public function getStatusNome():string
  {
    return $this->status->nome;
  }

  public function getProdutoId():?int
  {
    return $this->produto->id ?? null;
  }

  public function getProdutoNome():?string
  {
    return "";
  }

  public function getProdutoDescricao():?string
  {
    return $this->produto->descricao ?? null;
  }

  public function getColaboradores():array
  {
    return $this->colaboradores ?? [];
  }

  public function getRecebemLeads(): array
  {
    $recebem = [];
    /** @var Colaborador $colaborador */
    foreach ($this->colaboradores as $colaborador) {
      if ($colaborador->podeReceberLeads()) {
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
    /** @var Colaborador $recebe */
    foreach ($recebem as $recebe) {
      $array[] = [
        "id" => $recebe->getId(),
        "vez" => $recebe->getVez()
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

  public function getColaboradorById(int $id): ?Colaborador
  {
    foreach ($this->colaboradores as $colaborador) {
      if ($colaborador->getId() === $id) {
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
      if ($vez < $colab->getVez()) {
        $vez = $colab->getVez();
      }
    }

    return $vez;
  }

  public function countColaboradores(): int
  {
    return count($this->colaboradores);
  }

  // --- CHANGERS ---

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
    if ($this->getStatusId() === self::STATUS_ATIVADO) {
      throw new DomainException("Essa equipe já está ativada.");
    }

    $this->setStatus(self::STATUS_ATIVADO);
  }

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
    return;
  }

  /**
   * Seta o Produto com um array associativo.
   * 
   * @param array $produto Chaves: int id, string nome, ?string descricao
   */
  public function setProdutoByArray(?array $produto): void
  {
    return;
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
    foreach ($this->colaboradores as $key => $colab) {
      if ($colab->getId() === $id) {
        unset($this->colaboradores[$key]);
      }
    }
  }
}
