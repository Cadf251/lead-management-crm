<?php

namespace App\adms\Models\Repositories;

use App\adms\Helpers\TokenHelper;
use App\adms\Models\Services\DbOperations;

/**
 * Repositório de Tokens
 */
class TokenRepository extends DbOperations
{
  /**
   * Verifica se um token já existe, independente do contexto
   * 
   * @param string $token O token a ser procurado
   * 
   * @return bool Se existe ou não
   */
  public function tokenExiste(string $token): bool
  {
    $tokenQ = <<<SQL
    SELECT token
    FROM tokens
    WHERE 
      ((prazo >= NOW()) OR (prazo IS NULL))
      AND (token = :token)
      AND (token_status_id = 3)
    SQL;

    $params = [
      [":token" => $token]
    ];

    $tokenArray = $this->executeSQL($tokenQ, $params, true);

    if (empty($tokenArray))
      return false;
    else
      return true;
  }

  /**
   * Desativa um TOKEN
   * 
   * Ele muda o token_status_id para 1 (desativado)
   * 
   * @param string $token O token a ser desativado
   * 
   * @return void
   */
  public function desativarToken(string $token): void
  {
    $tokenQ = <<<SQL
    UPDATE tokens
    SET token_status_id = 1
    WHERE (token = :token)
    LIMIT 1
    SQL;

    $params = [
      [":token" => $token]
    ];

    $this->executeSQL($tokenQ, $params, false);
  }

  /**
   * Recupera um TOKEN ativo usando os valores passados.
   * 
   * @param string $tipo O tipo do TOKEN
   * @param string $contexto O contexto do TOKEN
   * @param int|null $usuarioId O ID do usuário
   * @param int|null $atendimentoId O ID do atendimento
   * 
   * @return string|false String se existir, false se não existir
   */
  public function recuperarToken(string $tipo, string $contexto, int|null $usuarioId = null, int|null $atendimentoId = null):string|false
  {
    $base = <<<SQL
    SELECT token
    FROM tokens
    WHERE 
      ((prazo >= NOW()) OR (prazo IS NULL))
      AND ((tipo LIKE :tipo) AND (contexto LIKE :contexto))
      AND (token_status_id = 3)
    SQL;

    $conds = [];

    if ($atendimentoId !== null)
      $conds[] = <<<SQL
        (atendimento_id = :atendimento_id)
      SQL;

    if ($usuarioId !== null)
      $conds[] = <<<SQL
        (usuario_id = :usuario_id)
      SQL;

    if (!empty($conds)){
      $imploded = implode(" OR ", $conds);
      $base .= <<<SQL
         AND ($imploded)
      SQL;
    }

    $query = $base.<<<SQL
      LIMIT 1
    SQL;

    $params = [
      ":atendimento_id" => $atendimentoId,
      ":tipo" => $tipo,
      ":contexto" => $contexto,
      ":usuario_id" => $usuarioId
    ];

    $token = $this->executeSQL($query, $params);

    if (($token !== false) && (!empty($token)))
      return $token[0]["token"];
    else 
      return false;
  }

  /**
   * Cria um TOKEN e armazena no banco de dados caso ele não exista.
   * 
   * Se já existir um com o mesmo $tipo e $contexto, ele recupera e retorna.
   * 
   * @param string $tipo O tipo do TOKEN
   * @param string $contexto O contexto do TOKEN
   * @param int|null $usuarioId O ID do usuário
   * @param int|null $atendimentoId O ID do atendimento
   * 
   * @return string O Token criado
   */
  public function armazenarToken(string $tipo, string $contexto, int|null $usuarioId = null, int|null $atendimentoId = null):string
  {
    $recuperar = $this->recuperarToken($tipo, $contexto, $usuarioId, $atendimentoId);

    if($recuperar !== false)
      return $recuperar;

    $token = TokenHelper::criarToken();

    $query = <<<SQL
    INSERT INTO tokens (token, tipo, contexto, usuario_id, atendimento_id, token_status_id)
    VALUES (:token, :tipo, :contexto, :usuario_id, :atendimento_id, 3)
    SQL;

    $params = [
      ":token" => $token,
      ":atendimento_id" => $atendimentoId,
      ":tipo" => $tipo,
      ":contexto" => $contexto,
      ":usuario_id" => $usuarioId
    ];

    $this->executeSQL($query, $params, false);
    return $token;
  }
}
