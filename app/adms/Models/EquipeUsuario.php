<?php

namespace App\adms\Models;

use DomainException;
use Exception;

class EquipeUsuario
{
  public ?int $id = null;
  public int $usuarioId;
  public string $usuarioNome;
  public ?bool $recebeLeads = null;
  public ?EquipeFuncao $funcao = null;
  public ?int $nivelId;
  public ?string $funcaoNome = null;
  public ?int $vez = 0;

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

    $this->funcao = new EquipeFuncao($id, $nome, $descricao);
  }

  public function setVez(int $vez)
  {
    $this->vez = $vez;
  }

  public function setNivelId(int $nivelId)
  {
    if(!in_array($nivelId, Usuario::VALIDS_NIVEIS)) {
      throw new Exception("nivel de acesso inválido");
    }

    $this->nivelId = $nivelId;
  }

  public function diminuirVez()
  {
    if ($this->vez > 0) {
      $this->vez--;
    }
  }

  public function incrementarVez()
  {
    $this->vez++;
  }

  /**
   * Retorna se ele pode receber leads
   */ 
  public function recebeLeads():bool
  {
    return $this->recebeLeads;
  }

  public function podeSerGerente():bool
  {
    return $this->nivelId >= Usuario::NIVEL_GERENTE;
  }
}
