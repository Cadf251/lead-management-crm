<?php

namespace App\adms\Repositories;

use App\adms\Database\DbOperations;
use App\adms\Models\Usuario;

/** Manipula os dados de usuários no banco de dados */
class UsuariosRepository extends DbOperations
{
  /** @var string $tabela é o nome da tabela no banco de dados */
  public string $tabela = "usuarios";

  /**
   * Retorna a base de SQL para consulta de usuários
   * Retorna um array no padrão:
   * tabela_campo;
   * u_id (usuario.id)
   * niv_nome (nivel_acesso.nome)
   * us_id (usuario_status.id)
   * 
   * @return string A query SELECT
   */
  private function queryBase() :string
  {
    return <<<SQL
      SELECT 
        u.id u_id, u.nome u_nome, email u_email, u.celular u_celular, u.senha u_senha, u.foto_perfil u_foto_perfil,
        niv.id niv_id, niv.nome niv_nome, niv.descricao niv_descricao,
        us.id us_id, us.nome us_nome, us.descricao us_descricao
      FROM {$this->tabela} u
      INNER JOIN niveis_acesso niv ON niv.id = u.nivel_acesso_id
      INNER JOIN usuario_status us ON u.usuario_status_id = us.id
    SQL;
  }

  /**
   * Lista todos os usuários sem distinção de status usando a $this->queryBase().
   * 
   * @return array Usuario
   */
  public function listar() :array
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      ORDER BY
        CASE WHEN us_id = 2 THEN 1 ELSE 0 END, us_id DESC, u.id
    SQL;

    $array = $this->executeSQL($query);

    $final = [];
    
    foreach($array as $row){
      $final[] = $this->hydrateUsuario($row);
    }

    return $final;
  }

  /**
   * Seleciona um único usuário com base no ID
   * 
   * @param int $id O ID do usuário a ser recuperado
   * 
   * @return Usuario
   */
  public function selecionar(int $id):?Usuario
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      WHERE 
        u.id = :usuario_id
      LIMIT 1
    SQL;

    $params = [
      ":usuario_id" => $id
    ];

    $array = $this->executeSQL($query, $params);

    if (empty($array)) {
      return null;
    }

    return $this->hydrateUsuario($array[0]);
  }

  public function selecioarByEmail(string $email):?Usuario
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      WHERE 
        u.email = :email
      LIMIT 1
    SQL;

    $params = [
      ":email" => $email
    ];

    $array = $this->executeSQL($query, $params);

    if (empty($array)) {
      return null;
    }

    return $this->hydrateUsuario($array[0]);
  }

  /**
   * Transforma um usuário array em Usuario
   * 
   * @param array $row O row vindo do queryBase
   * 
   * @return ?Usuario
   */
  public function hydrateUsuario(array $row):?Usuario{
    $usuario = new Usuario();
    $usuario->setId($row["u_id"]);
    $usuario->setNome($row["u_nome"]);
    $usuario->setEmail($row["u_email"]);
    $usuario->setCelular($row["u_celular"]);
    $usuario->setSenha($row["u_senha"]);
    $usuario->setFoto($row["u_foto_perfil"]);
    $usuario->setNivel($row["niv_id"], $row["niv_nome"], $row["niv_descricao"]);
    $usuario->setStatus($row["us_id"], $row["us_nome"], $row["us_descricao"]);
    return $usuario;
  }

  /**
   * Salva o objeto no banco de dados
   * 
   * @param Usuario $usuario
   * 
   */
  public function salvar(Usuario $usuario):void {
    $params = [
      ":nome" => $usuario->nome,
      ":email" => $usuario->email,
      ":celular" => $usuario->celular,
      ":senha" => $usuario->senhaHash,
      ":foto_perfil" => $usuario->foto,
      ":usuario_status_id" => $usuario->status->id,
      ":nivel_acesso_id" => $usuario->nivel->id,
      ":modified" => date($_ENV["DATE_FORMAT"])
    ];

    $this->updateSQL($this->tabela, $params, $usuario->id);
  }

  public function getNivel(int $id){
    return $this->getStatusOuNivel($id, "nivel_acesso")[0];
  }

  public function getStatus(int $id){
    return $this->getStatusOuNivel($id, "usuario_status")[0];
  }

  private function getStatusOuNivel(int $id, string $tabela){
    $query = <<<SQL
    SELECT nome, descricao
    FROM $tabela
    WHERE id = :id
    SQL;

    $params = [
      ":id" => $id
    ];

    return $this->executeSQL($query, $params);
  }

// public function resetarSenha(int $id){
//   $params = [
//     ":senha" => null,
//     ":modified" => date($_ENV["DATE_FORMAT"])
//   ];

//   return $this->updateSQL($this->tabela, $params, $id);
// }

// /**
//  * Atualiza os usuários, e já incluir o modified no $params
//  * 
//  * @param array $params Os parâmetros que devem ser ataulizados, no formato: [":campo_literal" => "var"]
//  * @param int $id O id do usuário que será atualizado
//  * 
//  * @return bool
//  */
// public function updateUsuario(array $params, int $id)
// {
//   $modified = date($_ENV["DATE_FORMAT"]);
//   $params[":modified"] = $modified;
//   return $this->updateSQL($this->tabela, $params, $id);
// }
  
//   /**
//    * Seta o status do usuário como aguardando confirmação
//    * 
//    * @param int $usuarioId O ID do usuário
//    * 
//    * @return bool
//    */
//   public function ativar(int $usuarioId) :bool
//   {
//     $params = [
//       ":usuario_status_id" => 1
//     ];

//     return $this->updateUsuario($params, $usuarioId);
//   }

//   /**
//  * Seta o status do usuário como aguardando confirmação e apaga a senha
//  * 
//  * @param int $usuarioId O ID do usuário
//  * 
//  * @return bool
//   */
//   public function resetarSenha(int $usuarioId) :bool
//   {
//     $params = [
//       ":usuario_status_id" => 1,
//       ":senha" => null
//     ];

//     return $this->updateUsuario($params, $usuarioId);
//   }

//   /**
//    * Seta o status do usuário como desativado
//    * 
//    * @param int $usuarioId O ID do usuário
//    * 
//    * @return bool
//    */
//   public function desativar(int $usuarioId) :bool
//   {
//     $params = [
//       ":usuario_status_id" => 2,
//       ":senha" => null
//     ];

//     return $this->updateUsuario($params, $usuarioId);
//   }

//   /** 
//    * Dá um tiro de misericórdia no usuário.
//    * 
//    * Evitar essa funcionalidade.
//    * 
//    * @param int $usuarioId O Usuário que será excluído
//    * 
//    * @return bool
//    */
//   public function excluir(int $usuarioId):bool
//   {
//     return $this->deleteByIdSQL($this->tabela, $usuarioId);
//   }
}