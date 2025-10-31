<?php

use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\HTMLHelper;

echo HTMLHelper::renderHeader("Gerenciar Equipes", "{$_ENV['HOST_BASE']}criar-equipe/", "Crie uma nova equipe", "plus");

foreach ($this->data["equipes"] as $equipe){
  // Cria o cabeçalho
  $editarIcon = HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}editar-equipe/{$equipe['equipe_id']}", "pencil", "Editar equipe");
  $congelarIcon = HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}congelar-equipe/{$equipe['equipe_id']}", "pause",  "Pausar Equipe", "gray");
  $despausarIcon = HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}ativar-equipe/{$equipe['equipe_id']}", "play",  "Despausar Equipe", "gray");
  $ativarIcon = HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}ativar-equipe/{$equipe['equipe_id']}", "rotate",  "Reativar Equipe", "gray");
  $desativarIcon = HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}desativar-equipe/{$equipe['equipe_id']}", "trash-can", "Desativar equipe", "alerta");
  switch ($equipe["equipe_status_id"]){
    case 3:
      $emogi = "✅";
      $icons = "
        $editarIcon
        $congelarIcon
        $desativarIcon";
      break;
    case 2:
      $emogi = "⏸️";
      $icons = "
        $editarIcon
        $despausarIcon
        $desativarIcon";
      break;
    case 1:
      $emogi = "❌";
      $icons = $ativarIcon;
      break;
  }

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
  $usuarios = $this->data["usuarios"][$equipe["equipe_id"]];
  $proximos = $this->data["proximos"][$equipe["equipe_id"]];

  $rows = "";
  if (empty($usuarios)){
    $rows .= <<<HTML
    <tr>
      <td colspace="6">Nenhum usuário</td>
    </tr>
    HTML;
  } else {
    foreach($usuarios as $usuario){
      // Tratar botão de função
      // Se ele tiver o nível de acesso menor que 3, é colaborador e não pode ser Gerente
      if ($usuario["nivel_acesso_id"] >= 3){
        $function = <<<JS
          setWarning(
            "Deseja alterar a função do usuário {$usuario['nome']}?",
            "Altera a função do usuário na equipe.",
            true,
            () => {
              postRequest("alterar_funcao", "equipe_id={$equipe['equipe_id']}&usuario_id={$usuario['usuario_id']}&set="+ document.querySelector(".js--select").value)
            })
        JS;

        $funcoesResult = CreateOptions::criarOpcoes($this->data["funcoes"], $usuario["usuario_id"]);
        
        $funcao = <<<HTML
          <select name="funcao" class="input js--select">$funcoesResult</select>
          <button onclick="
            setWarning(
              'Deseja alterar a função do usuário {$usuario['nome']}?',
              'Altera a função do usuário na equipe.',
              true,
              () => {
                postRequest('{$_ENV['HOST_BASE']}equipes-ajax/alterar-funcao', 'equipe_id={$equipe['equipe_id']}&usuario_id={$usuario['usuario_id']}&set='+ document.querySelector('.js--select').value)
              }
            )"
            class="small-btn small-btn--gray js--salvar" disabled>
            <i class="fa-solid fa-floppy-disk"></i>
          </button>
        HTML;
      } else {
        $funcao = $usuario["funcao_nome"];
      }

      // Tratar botão de pode receber leads
      switch ((int)$usuario["pode_receber_leads"]){
        case 1:
          $recebeLeads = "Sim";
          $class = "ativado";
          $set = 0;
          $prioriClass = ["small-btn--normal", "small-btn--alerta"];
          $disabled = "";
          break;
        case 0:
          $recebeLeads = "Não";
          $class = "desativado";
          $set = 1;
          $prioriClass = ["small-btn--gray", "small-btn--gray"];
          $disabled = "disabled";
          break;
      }

      $recebimento = <<<HTML
        <button onclick="postRequest(
            '{$_ENV['HOST_BASE']}equipes-ajax/alterar-recebimento',
            'equipe_id={$equipe['equipe_id']}&usuario_id={$usuario['usuario_id']}&set={$set}'
          )"
          class="switch switch--{$class}">
          $recebeLeads
        </button>
      HTML;

      // Tratar quem é o próximo a receber leads
      $arrayResult = [];
      
      // Tratar botões de prejudicar, priorizar
      $prioriJudicar = <<<HTML
      <button 
        class="small-btn {$prioriClass[0]}" id="priorizar-btn-{$pessoa['usuario_id']}"
        onclick="setWarning(
          'Deseja priorizar a vez do usuário {$pessoa['nome']}?',
          'Ele ficará uma posição a frente na fila de leads.',
          true,
          () => {
            postRequest(
              '{$_ENV['HOST_BASE']}equipes-ajax/priorizar',
              'usuario_id={$usuario['usuario_id']}&equipe_id={$equipe['equipe_id']}'
            )
          }
        )"
        $disabled>
        <i class="fa-solid fa-up-long" title="O usuário será colocado uma posição a frente na fila de recebimento de leads"></i>
      </button>
      <button class="small-btn {$prioriClass[1]}" id="prejudicar-btn-{$pessoa['usuario_id']}"
        onclick="setWarning(
          'Deseja prejudicar a vez do usuário {$pessoa['nome']}?',
          'Ele ficará uma posição para trás na fila de leads.',
          true,
          () => {
            postRequest(
              '{$_ENV['HOST_BASE']}equipes-ajax/prejudicar',
              'usuario_id={$usuario['usuario_id']}&equipe_id={$equipe['equipe_id']}'
            )
          }
        )"
        $disabled>
        <i class="fa-solid fa-down-long" title="O usuário será colocado uma posição para trás na fila de recebimento de leads"></i>
      </button>
      HTML;
      
      // Tratar botão de remover da equipe
      $remover = <<<HTML
      <button class="small-btn small-btn--alerta" onclick="setWarning(
          'Deseja excluir da equipe o usuário {$pessoa['nome']}?',
          'Ele não perderá o acesso aos leads que são atribuidos a ele.',
          true,
          () => {
            postRequest(
              '{$_ENV['HOST_BASE']}equipes-ajax/remover-usuario',
              'usuario_id={$usuario['usuario_id']}&equipe_id={$equipe['equipe_id']}'
            )
          }
        )">
        <i class="fa-solid fa-minus" title="Retirar da Equipe"></i>
      </button>
      HTML;
      $rows .= <<<HTML
      <tr>
        <td>{$usuario["nome"]}</td>
        <td>{$funcao}</td>
        <td class="text-center">{$recebimento}</td>
        <td class="text-center"></td>
        <td class="text-center">$prioriJudicar</td>
        <td class="text-center">$remover</td>
      </tr>
      HTML;
    }
  }

  if ($equipe["equipe_status_id"] === 1)
    $addBtn = "";
  else
    $addBtn = HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}adicionar-usuario/{$equipe['equipe_id']}", "plus", "Incluir usuário na equipe");
  
  $rows .= <<<HTML
  <tr>
    <td colspace="6">$addBtn</td>
  </tr>
  HTML;

  // Finaliza a tabela e o card
  $finalTable = HTMLHelper::renderTable("", $tableHeader, $rows);
  $content .= $finalTable;
  echo HTMLHelper::renderCardComplete($title, $content, $equipe["equipe_descricao"] ?? "", $icons);
}
?>