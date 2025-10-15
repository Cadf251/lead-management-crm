<?php

use App\adms\Helpers\HTMLHelper;

echo HTMLHelper::renderHeader("Gerenciar Equipes", "{$_ENV['HOST_BASE']}criar-equipe/", "Crie uma nova equipe", "plus");
var_dump($this->data["equipes"]);
/*
array (size=9)
  'equipe_id' => int 3
  'equipe_nome' => string 'Vendas' (length=6)
  'equipe_descricao' => null
  'equipe_created' => string '2025-10-06 17:41:07' (length=19)
  'equipe_modified' => null
  'produto_nome' => string 'Sites' (length=5)
  'produto_descricao' => null
  'equipe_status_id' => int 3
  'equipe_status_nome' => string 'Ativado' (length=7)
*/
foreach ($this->data["equipes"] as $equipe){
  // Cria o cabeçalho
  switch ($equipe["equipe_status_id"]){
    case 3:
      $emogi = "✅";
      $href = "congelar-equipe";
      $icon  = "pause";
      $mouseover = "Pausar Equipe"; 
      break;
    case 2:
      $emogi = "⏸️";
      $href = "descongelar-equipe";
      $icon  = "play";
      $mouseover = "Despausar Equipe";
      break;
  }

  $icons = "";
  $icons .= HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}editar-equipe/{$equipe['equipe_id']}", "pencil", "Editar equipe");
  $icons .= HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}{$href}/{$equipe['equipe_id']}", $icon, $mouseover, "gray");
  $icons .= HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}apagar-equipe/{$equipe['equipe_id']}", "trash-can", "Excluir equipe", "alerta");

  $title = "Equipe {$equipe['equipe_nome']}";

  $status = HTMLHelper::renderStatusCard($emogi, $equipe["equipe_status_nome"]);

  $produto = HTMLHelper::renderStatusCard("", $equipe["produto_nome"]);

  $content = <<<HTML
    $status
    $produto
  HTML;

  // Começa a criar a tabela de usuários
  $tableHeader = <<<HTML
    <tr>
      <th>Usuário</th>
      <th>Função</th>
      <th>Recebe leads?</th>
      <th>Próximos leads</th>
      <th>Priorizar/prejudicar</th>
      <th>Remover da equipe</th>
    </tr>
  HTML;

  // Insere os usuários

  // Finaliza a tabela e o card
  $finalTable = HTMLHelper::renderTable("", $tableHeader, "");
  $content .= $finalTable;
  echo HTMLHelper::renderCardComplete($title, $content, $equipe["equipe_descricao"] ?? "", $icons);
}
?>