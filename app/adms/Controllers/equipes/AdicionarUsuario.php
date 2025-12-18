<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\CSRFHelper;
use App\adms\Repositories\UsuariosRepository;
use App\adms\Database\DbConnectionClient;

class AdicionarUsuario extends EquipesAbstract
{
  public function index(string|int|null $equipeId)
  {
    $this->setInfoById((int)$equipeId);

    if ($this->statusId === 1){
      $_SESSION["alerta"] = [
        "Aviso!",
        ["❌ Essa equipe não existe, não está ativada ou você não tem acesso a ela."]
      ];
      $this->redirect();
    }

    // Verifica se há POST
    $this->data["form"] = filter_input_array(INPUT_POST);

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("add_usuario", $this->data["form"]["csrf_token"])){
      $usuarioInfo = explode(",", $this->data["form"]["usuario_id"]);
      $usuarioId = (int)trim($usuarioInfo[0]);

      // Instancia o repositório do usuário e seleciona o usuário
      $conn = new DbConnectionClient(null);
      $usuario = new UsuariosRepository($conn->conexao);
      $usuarios = $usuario->selecionar($usuarioId);

      if(empty($usuarios)){
        $_SESSION["alerta"] = [
          "Aviso!",
          ["❌ Esse usuário não existe."]
        ];
        $this->redirect();
      }

      $usuarioSelect = $usuarios[0];
      
      // Verifica se o nível de acesso é condizente com a função
      if ($usuarioSelect["niv_id"] < 3)
        $this->data["form"]["funcao_id"] = 1;
      
      // Verifica qual a vez que o usuário deve ter
      $vez = $this->repo->minVez($this->id);

      $params = [
        ":vez" => $vez,
        ":pode_receber_leads" => (int)$this->data["form"]["recebe_leads"],
        ":equipe_usuario_funcao_id" => (int)$this->data["form"]["funcao_id"],
        ":equipe_id" => $this->id,
        ":usuario_id" => $usuarioId
      ];

      if ($this->repo->adicionarUsuario($params)){
        $_SESSION["alerta"] = [
          "Sucesso!",
          "✅ Usuário adicionado com sucesso."
        ];
      } else {
        $_SESSION["alerta"] = [
          "Erro!",
          "❌ Algo deu errado."
        ];
      }

      $this->redirect();
    }

    // Insere o novo usuário
    $usuarios = $this->repo->eleitosAEquipe($this->id);
    $funcoes = $this->repo->selecionarOpcoes("equipes_usuarios_funcoes");

    $this->setData([
      "title" => "Adicionar Usuário",
      "nome" => $this->nome,
      "usuarios" => $usuarios,
      "funcoes" => $funcoes,
      "js" => ["public/adms/js/equipes.js"]
    ]);

    $this->render("adicionar-usuario");
  }
}