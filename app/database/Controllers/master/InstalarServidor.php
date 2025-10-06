<?php

namespace App\database\Controllers\master;

use App\adms\Models\Services\DbConnectionClient;
use App\adms\Models\Services\DbConnectionGlobal;
use App\database\Models\DatabaseRepository;

class InstalarServidor
{
  private int $id;
  private string $sql;

  public function index(string|null|int $id)
  {
    // Pega o ID
    $this->id = (int)$id;

    // Pega a última versão do Install
    $this->ultimaVersao();

    // Verifica se está disponível para instalar.
    $conexao = new DbConnectionGlobal();
    $repo = new DatabaseRepository($conexao->conexao);

    $servidor = $repo->selecionar($this->id);
    $array = [
      "host" => $servidor["host"],
      "db_name" => $servidor["db_name"],
      "user" => $servidor["user"],
      "pass" => $servidor["pass"]
    ];

    if (($servidor["status"] === 1) && ($servidor["versao"] === "0")){
      $this->instalarServidor($array);
    } else {
      die("Não está qualificado para instalar.");
    }

    header("Location: {$_ENV['HOST_BASE']}listar-servidores");
    exit;
  }

  public function ultimaVersao()
  {
    $files = scandir("app/database/sql/install");
    $count = count($files);
    $last = $files[$count - 1];
    $lastContent = file_get_contents("app/database/sql/install/$last");
    $this->sql = $lastContent;
  }

  public function instalarServidor(array $credenciais)
  {
    // Conecta no servidor do cliente
    $conn = new DbConnectionClient($credenciais);
    $repo = new DatabaseRepository($conn->conexao);

    // Roda as queries
    $queries = explode(";", $this->sql);

    foreach ($queries as $query){
      // Limpa a query antes de rodar
      $limpa = trim($query);
      if (!empty($limpa)){
        $repo->executeSQL($query, [], false);
      }
    }

    // Atualiza com o version name
  }
}