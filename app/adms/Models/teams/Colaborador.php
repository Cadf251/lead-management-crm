<?php

namespace App\adms\Models\teams;

use DomainException;

/**
 * O usuário em uma equipe
 */
class Colaborador
{
  private ?int $id = null;
  private int $usuarioId;
  private string $usuarioNome;
  private ?bool $recebeLeads = null;
  private ?ColaboradorFuncao $funcao = null;
  private ?int $nivelId;
  private ?int $vez = 0;

  public const FUNCAO_COLABORADOR = 1;
  public const FUNCAO_GERENTE = 2;

  /**
   * Instancia um usuário eleito a essa equipe
   */
  public static function novo(
    int $usuarioId,
    string $usuarioNome,
    int $nivelId
  ): self
  {
    $instance = new self();
    $instance->setUsuarioId($usuarioId);
    $instance->setUsuarioNome($usuarioNome);
    $instance->setNivelId($nivelId);
    return $instance;
  }

  public function getId():int
  {
    return $this->id;
  }

  public function getUsuarioId():int
  {
    return $this->usuarioId;
  }

  public function getUsuarioNome():string
  {
    return $this->usuarioNome;
  }

  public function getFuncaoId():int
  {
    return $this->funcao->id;
  }

  public function getFuncaoNome():string
  {
    return $this->funcao->nome;
  }

  public function getVez()
  {
    return $this->vez;
  }

  public function getNivelAcessoId():int
  {
    return $this->nivelId;
  }

  /**
   * Atenção, esse ID é do registro do colaborador na tabela equipes_usuarios, e não o ID do usuário.
   */
  public function setId(int $id)
  {
    $this->id = $id;
  }

  public function setUsuarioId(int $usuarioId)
  {
    $this->usuarioId = $usuarioId;
  }

  public function setUsuarioNome(string $nome)
  {
    $this->usuarioNome = $nome;
  }

  public function setRecebeLeads(bool $input)
  {
    $this->recebeLeads = $input;
  }

  public function setFuncao(int $id, ?string $nome = null, ?string $descricao = null)
  {
    if (!in_array($id, [
      self::FUNCAO_COLABORADOR,
      self::FUNCAO_GERENTE
    ])) {
      throw new DomainException("Função de usuário não existente");
    }

    $this->funcao = new ColaboradorFuncao($id, $nome, $descricao);
  }

  public function setVez(int $vez)
  {
    $this->vez = $vez;
  }

  public function setNivelId(int $nivelId)
  {
    $this->nivelId = $nivelId;
  }

  public function diminuirVez()
  {
    $this->vez--;
  }

  public function incrementarVez()
  {
    $this->vez++;
  }

  /**
   * Retorna se ele pode receber leads
   */ 
  public function podeReceberLeads():bool
  {
    return $this->recebeLeads;
  }

  public function podeSerGerente():bool
  {
    return true;
  }
}
