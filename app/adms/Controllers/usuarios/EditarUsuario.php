<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repositories\UsuariosRepository;
use App\adms\Views\Services\LoadViewService;
use App\adms\Controllers\usuarios\Usuarios;

class EditarUsuario
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  public function index(string|int $id)
  {
    // Inicia o repositório e pega os dados de um único usuário com base no ID recebido
    $usuarios = new UsuariosRepository();

    // Recupera os dados do usuário
    $usuarioArray = $usuarios->selecionar((int)$id);

    // Verifica se houve erro
    if (($usuarioArray === false) || (empty($usuarioArray))){
      GenerateLog::generateLog("error", "A consulta ao repositório retornou falso ou vazio", ["id" => $id]);

      // Prepara o setWarning
      $_SESSION["alerta"] = [
        "O usuário não existe!",
        "O usuário não existe ou foi excluído."
      ];
      
      // Dá um chute na bunda do cara e leva ele de volta
      header("Location: {$_ENV['HOST_BASE']}usuarios");
    }

    // Simplifica o array
    $usuario = $usuarioArray[0];

    $this->data = [
      "title" => "Editar Usuário",
      "css" => ["public/adms/css/usuarios.css"],
      "usuario" => $usuario,
      "task" => "editar"
    ];

    // Carregar a VIEW
    $loadView = new LoadViewService("adms/Views/usuarios/editar-usuario", $this->data);
    $loadView->loadView();
  }
}