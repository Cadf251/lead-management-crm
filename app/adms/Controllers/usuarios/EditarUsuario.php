<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\GenerateLog;
use App\adms\Views\Services\LoadViewService;
use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;

class EditarUsuario extends UsuariosAbstract
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  public function index(string|int $id)
  {
    // Recupera os dados do usuário
    $usuarioArray = $this->repo->selecionar((int)$id);

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
      exit;
    }

    // Simplifica o array
    $usuario = $usuarioArray[0];

    // Seleciona as opções no banco de dados
    $optionsArray = $this->repo->selecionarOpcoes("niveis_acesso");
    $optionsHTML = CreateOptions::criarOpcoes($optionsArray, $usuario["niv_id"] ?? null);

    $this->data = [
      "title" => "Editar Usuário",
      "css" => ["public/adms/css/usuarios.css"],
      "usuario" => $usuario,
      "form-options" => $optionsHTML
    ];

    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = $_POST;

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_usuario", $this->data["form"]["csrf_token"])) {
      $result = $this->apurarEdicao($usuario);

      if ($result === false)
        $_SESSION["alerta"] = [
          "O usuário não foi editado!",
          "Ocorreu algum erro, tente novamente mais tarde."
        ];
      else 
        $_SESSION["alerta"] = [
          "O usuário foi editado com sucesso!",
          ""
        ];
      header("Location: {$_ENV['HOST_BASE']}usuarios");
      exit;
    }

    // Carregar a VIEW
    $loadView = new LoadViewService("adms/Views/usuarios/editar-usuario", $this->data);
    $loadView->loadView();
  }

  /** 
   * Apura a edição do usuário e faz as alterações necessárias
   * 
   * @param array $usuario Os dados do usuário
   * 
   * @return bool
   */
  public function apurarEdicao(array $usuario):bool
  {
    // Monta o array para fazer um update
    $this->data["form"]["celular"] = CelularFormatter::paraInternaciona($this->data["form"]["celular"]);
    $params = [
      ":nome" => $this->data["form"]["nome"],
      ":email" => $this->data["form"]["email"],
      ":celular" => $this->data["form"]["celular"],
      ":nivel_acesso_id" => (int)$this->data["form"]["nivel_acesso_id"]
    ];

    // Verifica se o email é diferente e necessita confirmação
    if ($this->data["form"]["email"] !== $usuario["u_email"]){
      $params[":usuario_status_id"] = 1;
      $params[":senha"] = null;

      $this->emailConfirmacao($usuario["u_id"], $this->data["form"]["nome"], $this->data["form"]["email"]);
    }

    // Verifica o que se deve fazer com a foto de perfil
    if((int)$this->data["form"]["foto_existe"] === 1){
      // Foto existe, deve-se trocar ou apagar?
      if ($this->data["form"]["editar_foto"] === "apagar"){
        $tempt = $this->apagarFoto($usuario["u_id"]);
      } else if ($this->data["form"]["editar_foto"] === "trocar"){
        $tempt = $this->trocarFoto($usuario["u_id"]);

        GenerateLog::generateLog("info", "Resultado do trocar foto", [$tempt]);
      }
    } else if (!empty($_FILES["foto"]["name"])){
      // Se a foto existir, e foi enviado uma nova foto no form, armazena esta foto
      $tempt = $this->armazenarFoto($usuario["u_id"]);
    }

    // Efetiva as edições no banco de dados
    return $this->repo->updateUsuario($params, $usuario["u_id"]);
  }
}