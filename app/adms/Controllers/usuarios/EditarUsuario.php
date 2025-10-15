<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;

class EditarUsuario extends UsuariosAbstract
{
  public function index(string|int $id)
  {
    $this->setInfoById((int)$id);

    // Seleciona as opções no banco de dados
    $optionsArray = $this->repo->selecionarOpcoes("niveis_acesso");
    $optionsHTML = CreateOptions::criarOpcoes($optionsArray, $this->nivId ?? null);

    $this->setData([
      "usuario" => $this->data["usuario"],
      "form-options" => $optionsHTML
    ]);

    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = $_POST;

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_usuario", $this->data["form"]["csrf_token"])) {
      $result = $this->apurarEdicao();

      if ($result[0] === false)
        $_SESSION["alerta"] = [
          "O usuário não foi editado!",
          $result[1]
        ];
      else
        $_SESSION["alerta"] = [
          "O usuário foi editado com sucesso!",
          $result[1]
        ];
      $this->redirect();
    }

    // Carregar a VIEW
    $this->render("adms/Views/usuarios/editar-usuario");
  }

  /** 
   * Apura a edição do usuário e faz as alterações necessárias
   * 
   * Setar: id, email
   * 
   * @return array => [bool sucesso, string info]
   */
  public function apurarEdicao(): array
  {
    // Monta o array para fazer um update
    $this->data["form"]["celular"] = CelularFormatter::paraInternaciona($this->data["form"]["celular"]);
    $params = [
      ":nome" => $this->data["form"]["nome"],
      ":celular" => $this->data["form"]["celular"],
      ":nivel_acesso_id" => (int)$this->data["form"]["nivel_acesso_id"]
    ];

    // Seta o novo nome
    $this->nome = $this->data["form"]["nome"];

    // Verifica se o email é diferente e necessita confirmação
    if ($this->data["form"]["email"] !== $this->email) {
      $params[":usuario_status_id"] = 1;
      $params[":senha"] = null;

      // Verifica se o e-mail já está sendo usado
      $existe = $this->repo->existe(
        "usuarios",
        "email = :email",
        [":email" => $this->data["form"]["email"]]
      );

      if ($existe === true)
        $info[] = "O e-mail está sendo usado por outro usuário.";
      else {
        // Subtitui o email pelo novo para não enviar para o email antigo
        $this->email = $this->data["form"]["email"];

        $params[":email"] = $this->email;
        
        $confimacao = $this->emailConfirmacao();

        if ($confimacao === false)
          $info[] = "Não foi possível enviar o email de confirmação de senha.";
        else
          $info[] = "E-mail de confirmação enviado com sucesso.";
      }
    }

    // Verifica o que se deve fazer com a foto de perfil
    if ((int)$this->data["form"]["foto_existe"] === 1) {
      // Foto existe, deve-se trocar ou apagar?
      if ($this->data["form"]["editar_foto"] === "apagar") {
        $result = $this->apagarFoto();
      } else if ($this->data["form"]["editar_foto"] === "trocar") {
        $result = $this->trocarFoto();
      }

      if ($result === false)
        $info[] = "Não foi possível {$this->data['form']['editar_foto']} a foto.";
      else {
        $passado = str_replace("ar", "ado", $this->data["form"]["editar_foto"]);
        $info[] = "A foto foi $passado com sucesso.";
      }
    } else if (!empty($_FILES["foto"]["name"])) {
      // Se a foto existir, e foi enviado uma nova foto no form, armazena esta foto
      $result = $this->armazenarFoto($this->id);

      if($result === false)
        $info[] = "Não foi possível guardar a foto do usuário.";
      else 
        $info[] = "A foto do usuário com guardada com sucesso.";
    }

    if (empty($info))
      $mensagem = "Nenhum erro durante o processo.";
    else
      $mensagem = implode("<br>", $info);

    // Efetiva as edições no banco de dados
    return [$this->repo->updateUsuario($params, $this->id), $mensagem];
  }
}
