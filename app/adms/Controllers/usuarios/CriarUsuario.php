<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;

class CriarUsuario extends UsuariosAbstract
{
  public function index()
  {
    // Seleciona as opções no banco de dados
    $optionsArray = $this->repo->selecionarOpcoes("niveis_acesso");
    $optionsHTML = CreateOptions::criarOpcoes($optionsArray, null);

    $this->setData([
      "title" => "Criar Usuário",
      "usuario" => null,
      "form-options" => $optionsHTML
    ]);

    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = $_POST;

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_usuario", $this->data["form"]["csrf_token"])) {
      // Tenta criar o novo usuário
      $result = $this->criarUsuario();

      if ($result[0] === false){
        // Prepara o setWarning
        $_SESSION["alerta"] = [
          "Não foi possível criar o usuário!",
          $result[1]
        ];

        $this->redirect();
      }

      // Tenta enviar o email
      $mail = $this->emailConfirmacao();

      if ($mail[0] === false){
        $_SESSION["alerta"] = [
          "Usuário criado, com um porém!",
          "Não foi possível enviar o email de confirmação de senha!"
        ];
        $this->redirect();
      }

      // Verifica se deve enviar a foto
      if($_FILES["foto"]["tmp_name"] != ''){
        $resultado = $this->armazenarFoto();

        if ($resultado === false) {
          $_SESSION["alerta"] = [
            "Usuário criado, com um porém!",
            "A foto de perfil não foi armazenada."
          ];
        
          $this->redirect();
        }
      }

      // Mostrar mensagem de sucesso
      $_SESSION["alerta"] = [
        "Usuário criado com sucesso!",
        "Peça que ele verifique a caixa de e-mail para fazer o primeiro acesso."
      ];
      $this->redirect();
    }

    // Carregar a VIEW
    $this->render("adms/Views/usuarios/criar-usuario");
  }

  /**
   * Cria um novo usuário usando os dados recebidos de um form válido.
   * 
   * @return array 0 => true ou 0 => false, 1 => "mensagem de erro"
   */
  public function criarUsuario(): array
  {
    // Resume o array para facilitar
    $usuario = $this->data["form"];

    // Verifica se o email está sendo usado
    $existe = $this->repo->existe(
      "usuarios",
      "email = :email",
      [":email" => $usuario["email"]]
    );

    if ($existe)
      return [false, "O email está sendo usado por outro usuário"];
    
    $celular = CelularFormatter::paraInternaciona($usuario["celular"]);

    $params = [
      ":nome" => $usuario["nome"],
      ":email" => $usuario["email"],
      ":celular" => $celular,
      ":nivel_acesso_id" => (int)$usuario["nivel_acesso_id"],
      ":created" => date($_ENV['DATE_FORMAT'])
    ];

    $usuarioId = $this->repo->insertSQL("usuarios", $params);

    if ($usuarioId === false) {
      GenerateLog::generateLog("info", "Não foi possível cadastrar um usuário, verifique os logs de SQL.", ["form" => $this->data["form"]]);
      return [false, "Algo deu errado com o registro do novo usuário."];
    } else {
      // Seta os dados do usuário
      $this->id = $usuarioId;
      $this->nome = $usuario["nome"];
      $this->email = $usuario["email"];
      return [true];
    }
  }
}