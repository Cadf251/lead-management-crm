<?php

// namespace App\adms\Controllers\api;

// use App\adms\Core\LoadApi;
// use App\adms\Core\OperationResult;
// use App\adms\Helpers\GenerateLog;
// use App\adms\Helpers\SlugController;
// use App\adms\Database\DbConnectionGlobal;
// use App\database\Models\DatabaseRepository;

// /** Essa classe é publica e recebe as requisições de POST, faz as devidas confirmações e chama o loadApi. */
// class Api
// {
//   /**
//    * Recebe o $_POST. Verifica se há um api_token válido e instancia o LoadApi. 
//    * Trata os erros em JSON: {"sucesso":bool,"mensagem":string}
//    * 
//    * @param string|null $task Recebe uma task que será transformada em Class
//    * 
//    * @return void
//    */
//   public function index(string|null $task): void
//   {
//     $result = new OperationResult();

//     // Permite só POST
//     if ($_SERVER["REQUEST_METHOD"] !== "POST") {
//       $result->failed("A requisição é inválida.");
//       echo json_encode($result->getForApi());
//       exit;
//     }

//     $post = $this->getData();

//     $token = $this->getApiToken();

//     if ($token === null) {
//       $result->failed("O TOKEN é inválido.");
//       echo json_encode($result->getForApi());
//       exit;
//     }

//     // $server = new DbConnectionGlobal();
//     // $repository = new DatabaseRepository($server->conexao);

//     // $client = $repository->selectClientByApiToken($token);

//     // if ($client === null) {
//     //   $result->failed("O TOKEN é inválido.");
//     //   echo json_encode($result->getForApi());
//     //   exit;
//     // }

//     // Tenta chamar a App\api\Controllers\Class->index().
//     // $loadApi = new LoadApi();
//     // $loadApi->loadApi($class, $client, $post);
//   }

//   /**
//    * Retorna um ARRAY do post ou do json input
//    */
//   private function getData(): array
//   {
//     // Primeiro tenta POST tradicional
//     $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//     if (!empty($data)) {
//       return $data;
//     }

//     // Fallback para JSON
//     $raw = file_get_contents("php://input");

//     if (!$raw) {
//       return [];
//     }

//     $json = json_decode($raw, true);

//     return is_array($json) ? $json : [];
//   }

//   /**
//    * Pega o API TOKEN e retorna null se não existir
//    */
//   private function getApiToken(): ?string
//   {
//     $headers = getallheaders();

//     // Bearer token
//     if (!empty($headers['Authorization'])) {
//       if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
//         return $matches[1];
//       }
//     }

//     // Fallback: X-API-KEY
//     if (!empty($headers['X-API-KEY'])) {
//       return $headers['X-API-KEY'];
//     }

//     return null;
//   }
// }
