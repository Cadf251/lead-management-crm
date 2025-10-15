<?php

namespace App\database\Controllers\master;

use App\adms\Models\Services\DbConnectionClient;
use App\adms\Models\Services\DbConnectionGlobal;
use App\database\Models\DatabaseRepository;

class AtualizarServidores
{
  private string $sql;
  private array $versions;
  private string $lastVersion;
  private string $updateVersion;

  public function index()
  {
    // Pega qual versão deve ser instalada
    $this->getVersoes();

    $options = "";
    foreach ($this->versions as $versao) {
      if ($versao === "1.0.0") continue;
      $options .= "<option value='$versao'>$versao</option>";
    }
    // Mostra um formulário com as versões disponíveis
    echo <<<HTML
      <b>Qual versão será baixada?</b>
      <form method="post">
        <select name="versao" required>
          <option value="">Selecione...</option>
          $options
        </select><br><br>
        <button type="submit">Enviar</button>
      </form>
    HTML;

    if (isset($_POST["versao"])) {
      $position = array_search($_POST["versao"], $this->versions);
      $this->updateVersion = $_POST["versao"];
      $this->lastVersion = $this->versions[$position + 1];

      $this->getSqlContent();
      $this->updateAll();
    }
  }

  public function getVersoes()
  {
    $dir = "app/database/sql/updates/sql";
    $files = scandir($dir);

    $array = array_diff($files, [".", ".."]);

    $arrayReverse = array_reverse($array);

    foreach ($arrayReverse as $item) {
      $this->versions[] = str_replace(["update-", ".sql"], "", $item);
    }
  }

  public function getSqlContent()
  {
    $this->sql = file_get_contents("app/database/sql/updates/sql/update-{$this->updateVersion}.sql");
  }

  public function updateAll()
  {
    // Recupera todos os bancos de dados
    $conexao = new DbConnectionGlobal();
    $repo = new DatabaseRepository($conexao->conexao);

    $servidores = $repo->listarClientes();

    // Tenta instalar um por um verificando se a versão a ser instalada está apta
    foreach ($servidores as $servidor) {
      if (($servidor["status"] === 1) && ($servidor["versao"] === $this->lastVersion)) {
        $serverArray = [
          "host" => $servidor["host"],
          "db_name" => $servidor["db_name"],
          "user" => $servidor["user"],
          "pass" => $servidor["pass"]
        ];

        $this->updateOne($serverArray);

        // Atualiza a version name
        $params = [
          ":versao" => $this->updateVersion
        ];

        $repo->updateSQL("servidores", $params, $servidor["id"]);
        echo "✅ Servidor att com sucesso:";
        var_dump($servidor);
      } else {
        echo "Este não está qualificado para a atualização:";
        var_dump($servidor);
      }
    }
  }

  public function updateOne(array $credenciais)
  {
    // Conecta no servidor do cliente
    $conn = new DbConnectionClient($credenciais);
    $repo = new DatabaseRepository($conn->conexao);

    // Roda as queries
    $queries = explode(";", $this->sql);

    if (empty($this->sql))
      die("Nenhum query encontrada nesta versão");
    
    foreach ($queries as $query){
      // Limpa a query antes de rodar
      $limpa = trim($query);
      if (!empty($limpa)){
        $repo->executeSQL($query, [], false);
      }
    }
  }
}
