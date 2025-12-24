<?php

use App\adms\UI\Badge;
use App\adms\UI\Card;

$btns = "";

foreach($equipe["buttons"] as $button){
  $btns .= $button->render();
}

$badge3 = $equipe["fila"]["badge"];

$content = <<<HTML
<div class="card__header center">
  <div class="card__header__info">
    <strong>{$equipe['nome']}</strong>
    <div class="subinfo">
      <span>{$equipe['descricao']}</span>
    </div>
  </div>
</div>
<div class="card__inline-items">
  {$equipe["numero_badge"]}
  {$equipe["produto_badge"]}
  {$equipe["status_badge"]}
  $badge3
</div>
<div class="card__inline-items">
  $btns
</div>
HTML;

$final = Card::create($content);

return <<<HTML
<div class="card--{$equipe['id']}">
  $final
</div>
HTML;

//   foreach($usuarios as $usuario){
//
//       $funcoesResult = CreateOptions::criarOpcoes($this->data["funcoes"], $usuario["usuario_id"]);
//      
//       $funcao = <<<HTML
//         <select name="funcao" class="input js--select">$funcoesResult</select>
//         <button onclick="
//           setWarning(
//             'Deseja alterar a função do usuário {$usuario['nome']}?',
//             'Altera a função do usuário na equipe.',
//             true,
//             () => {
//               postRequest('{$_ENV['HOST_BASE']}equipes-ajax/alterar-funcao', 'equipe_id={$equipe['equipe_id']}&usuario_id={$usuario['usuario_id']}&set='+ document.querySelector('.js--select').value,
//               (response) => {
//                 renderizar(response.html, '.card--{$equipe['equipe_id']}');
//               })
//             }
//           )"
//           class="small-btn small-btn--gray js--salvar" disabled>
//           <i class="fa-solid fa-floppy-disk"></i>
//         </button>
//       HTML;
//     } else {
//       $funcao = $usuario["funcao_nome"];
//     }

//     // Tratar botão de pode receber leads
//     switch ((int)$usuario["pode_receber_leads"]){
//       case 1:
//         $recebeLeads = "Sim";
//         $class = "ativado";
//         $set = 0;
//         $prioriClass = ["small-btn--normal", "small-btn--alerta"];
//         $disabled = "";
//         break;
//       case 0:
//         $recebeLeads = "Não";
//         $class = "desativado";
//         $set = 1;
//         $prioriClass = ["small-btn--gray", "small-btn--gray"];
//         $disabled = "disabled";
//         break;
//     }

//     $recebimento = <<<HTML
//       <button onclick="postRequest(
//           '{$_ENV['HOST_BASE']}equipes-ajax/alterar-recebimento',
//           'equipe_id={$equipe['equipe_id']}&usuario_id={$usuario['usuario_id']}&set={$set}',
//           (response) => {
//             renderizar(response.html, '.card--{$equipe['equipe_id']}');
//           }
//         )"
//         class="switch switch--{$class}">
//         $recebeLeads
//       </button>
//     HTML;

//     // Verifica se é o próximo
//     $medals = "";
//     if($usuario["usuario_id"] === $proximos[0]) $medals .= "🥇";
//     if($usuario["usuario_id"] === $proximos[1]) $medals .= "🥈";
//     if($usuario["usuario_id"] === $proximos[2]) $medals .= "🥉";

//     // Tratar botões de prejudicar, priorizar
//     $prioriJudicar = <<<HTML
//     <button 
//       class="small-btn {$prioriClass[0]}" id="priorizar-btn-{$pessoa['usuario_id']}"
//       onclick="setWarning(
//         'Deseja priorizar a vez do usuário {$pessoa['nome']}?',
//         'Ele ficará uma posição a frente na fila de leads.',
//         true,
//         () => {
//           postRequest(
//             '{$_ENV['HOST_BASE']}equipes-ajax/priorizar',
//             'usuario_id={$usuario['usuario_id']}&equipe_id={$equipe['equipe_id']}',
//             (response) => {
//               renderizar(response.html, '.card--{$equipe['equipe_id']}');
//             }
//           )
//         }
//       )"
//       $disabled>
//       <i class="fa-solid fa-up-long" title="O usuário será colocado uma posição a frente na fila de recebimento de leads"></i>
//     </button>
//     <button class="small-btn {$prioriClass[1]}" id="prejudicar-btn-{$pessoa['usuario_id']}"
//       onclick="setWarning(
//         'Deseja prejudicar a vez do usuário {$pessoa['nome']}?',
//         'Ele ficará uma posição para trás na fila de leads.',
//         true,
//         () => {
//           postRequest(
//             '{$_ENV['HOST_BASE']}equipes-ajax/prejudicar',
//             'usuario_id={$usuario['usuario_id']}&equipe_id={$equipe['equipe_id']}',
//             (response) => {
//               renderizar(response.html, '.card--{$equipe['equipe_id']}');
//             }
//           )
//         }
//       )"
//       $disabled>
//       <i class="fa-solid fa-down-long" title="O usuário será colocado uma posição para trás na fila de recebimento de leads"></i>
//     </button>
//     HTML;
    
//     // Tratar botão de remover da equipe
//     $remover = <<<HTML
//     <button class="small-btn small-btn--alerta" onclick="setWarning(
//         'Deseja excluir da equipe o usuário {$pessoa['nome']}?',
//         'Ele não perderá o acesso aos leads que são atribuidos a ele.',
//         true,
//         () => {
//           postRequest(
//             '{$_ENV['HOST_BASE']}equipes-ajax/remover-usuario',
//             'usuario_id={$usuario['usuario_id']}&equipe_id={$equipe['equipe_id']}',
//             (response) => {
//               renderizar(response.html, '.card--{$equipe['equipe_id']}');
//             }
//           )
//         }
//       )">
//       <i class="fa-solid fa-minus" title="Retirar da Equipe"></i>
//     </button>
//     HTML;
//     $rows .= <<<HTML
//     <tr>
//       <td>{$usuario["nome"]}</td>
//       <td>{$funcao}</td>
//       <td class="text-center">{$recebimento}</td>
//       <td class="text-center">{$medals}</td>
//       <td class="text-center">$prioriJudicar</td>
//       <td class="text-center">$remover</td>
//     </tr>
//     HTML;
//   }
// }

// if ($equipe["equipe_status_id"] === 1)
//   $addBtn = "";
// else
//   $addBtn = HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}adicionar-usuario/{$equipe['equipe_id']}", "plus", "Incluir usuário na equipe");

// $rows .= <<<HTML
// <tr>
//   <td colspace="6">$addBtn</td>
// </tr>
// HTML;

// // Finaliza a tabela e o card
// $finalTable = HTMLHelper::renderTable("", $tableHeader, $rows);
// $content .= $finalTable;
// $final = HTMLHelper::renderCardComplete($title, $content, $equipe["equipe_descricao"] ?? "", $icons);

// return <<<HTML
// <div class="card--{$equipe['equipe_id']}">
//   $final
// </div>
// HTML;