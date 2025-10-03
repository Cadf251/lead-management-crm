<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Views\Services\LoadViewService;

class CriarUsuario extends UsuariosAbstract
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  public function index()
  {
    // Intancia o repositório
    // Não precisa instanciar, o repositório é criado em UsuariosAbstract

    // Seleciona as opções no banco de dados
    $optionsArray = $this->repo->selecionarOpcoes("niveis_acesso");
    $optionsHTML = CreateOptions::criarOpcoes($optionsArray, null);

    $this->data = [
      "title" => "Criar Usuário",
      "css" => ["public/adms/css/usuarios.css"],
      "usuarios" => null,
      "form-options" => $optionsHTML
    ];

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

        header("Location: {$_ENV['HOST_BASE']}criar-usuario");
        exit;
      }

      // Tenta enviar o email
      $mail = $this->enviarEmailConfirmacao();
      if ($mail[0] === false){
        $_SESSION["alerta"] = [
          "Usuário criado, com um porém!",
          $mail[1]
        ];
        header("Location: {$_ENV['HOST_BASE']}usuarios");
        exit;
      }

      // Verifica se deve enviar a foto
      if($_FILES["foto"]["tmp_name"] != ''){
        $resultado = $this->manipularFoto();

        if ($resultado === false) {
          $_SESSION["alerta"] = [
            "Usuário criado, com um porém!",
            "A foto de perfil não foi armazenada."
          ];
        
          header("Location: {$_ENV['HOST_BASE']}usuarios");
          exit;
        }
      }

      // Mostrar mensagem de sucesso
      $_SESSION["alerta"] = [
        "Usuário criado com sucesso!",
        ""
      ];
      header("Location: {$_ENV['HOST_BASE']}usuarios");
      exit;
    }

    // Carregar a VIEW
    $loadView = new LoadViewService("adms/Views/usuarios/criar-usuario", $this->data);
    $loadView->loadView();
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
      ":created" => date("Y-m-d H:i:s")
    ];

    $usuarioId = $this->repo->insertSQL("usuarios", $params);

    if ($usuarioId === false) {
      GenerateLog::generateLog("info", "Não foi possível cadastrar um usuário, verifique os logs de SQL.", ["form" => $this->data["form"]]);
      return [false, "Algo deu errado com o registro do novo usuário."];
    } else {
      // Seta o ID no form
      $this->data["form"]["usuario_id"] = $usuarioId;
      return [true];
    }
  }

  /**
   * É a continuação da função de criarUsuario().
   * 
   * Tenta enviar um email de confirmação para um nova senha.
   * Precisa usar os parametros do data["form"]
   * 
   * $this->data["form"]["usuario_id"],
   * $this->data["form"]["nome"],
   * $this->data["form"]["email"]
   * 
   * @return array 0 => true; 0 => false, 1 => "Mensagem de erro"
   */
  public function enviarEmailConfirmacao():array
  {
    $final = $this->emailConfirmacao(
      $this->data["form"]["usuario_id"],
      $this->data["form"]["nome"],
      $this->data["form"]["email"]
    );

    if ($final === true)
      return [true];
    else
      return [false, "Não foi possível enviar o email para criação de senha."];
  }

  /**
   * Tenta guardar a foto de perfil usando fo $_FILES
   * 
   * @return bool
   */
  public function manipularFoto():bool
  {
    GenerateLog::generateLog("error", "A função foi chamada.", ["form" => $this->data["form"], "files['foto']" => $_FILES["foto"]]);
    
    if (!isset($this->data["form"]["usuario_id"])){
      GenerateLog::generateLog("error", "A foto não foi armazenado porque o usuario_id não está setado.", ["form" => $this->data["form"]]);
      return false;
    }

    $resultado = $this->armazenarFoto($this->data["form"]["usuario_id"]);
    return $resultado;
  }
}