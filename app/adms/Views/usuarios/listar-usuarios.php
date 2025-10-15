<?php

use App\adms\Helpers\CelularFormatter;
use App\adms\Helpers\HTMLHelper;

if (!isset($this->data["usuarios"])) {
  echo "Nenhum usuário";
  die();
}

// Cria o botão do header
$href = "{$_ENV['HOST_BASE']}criar-usuario";
echo HTMLHelper::renderHeader("Editar Usuários", $href, "Criar um novo usuário", "plus");

// Lê o array de usuários e imprime cada um
foreach ($this->data["usuarios"] as $usuario) {
  // Formata o celular do usuário
  $usuario["u_celular"] = $usuario["u_celular"] === null
    ? "Telefone vazio ou inválido!"
    : CelularFormatter::paraPlaceholder($usuario["u_celular"]);

  // Normalizar local das fotos de perfil
  $foto = (isset($usuario["u_foto_perfil"]) || !empty($usuario["u_foto_perfil"]) || $usuario["u_foto_perfil"] !== null)
    ? "<img src='{$_ENV['HOST_BASE']}files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/{$usuario['u_id']}.{$usuario['u_foto_perfil']}' height='100%' width='100%'>"
    : "";

  $btns = "";

  $editar = HTMLHelper::renderButtonLink("{$_ENV['HOST_BASE']}editar-usuario/{$usuario['u_id']}", "pencil", "Editar usuário");

  $function = <<<JS
    setWarning(
      "Reenviar email de {$usuario['u_nome']}?",
      "Será reenviado o email de confirmação de senha.",
      true,
      () => {
        window.location.href = "{$_ENV['HOST_BASE']}reenviar-email/{$usuario['u_id']}";
      }
    )
  JS;

  $reenviarEmail = HTMLHelper::renderButtonAjax($function, "gray", "envelope", "Reenviar email de confirmação/redefinição de senha");
  
  $function = <<<JS
    setWarning(
      "Deseja desativar o {$usuario['u_nome']}?",
      "O usuário será desativado. A ação é reversível.",
      true,
      () => {
        window.location.href = "{$_ENV['HOST_BASE']}desativar-usuario/{$usuario['u_id']}";
      }
    )
  JS;

  $desativar = HTMLHelper::renderButtonAjax($function, "alerta", "trash-can", "Desativar Usuário");

  $function = <<<JS
    setWarning(
      "Deseja reativar o usuário {$usuario['u_nome']}?",
      "Ele terá acesso a praticamente tudo que tinha antes.",
      true,
      () => {
        window.location.href = "{$_ENV['HOST_BASE']}ativar-usuario/{$usuario['u_id']}";
      }
    )
  JS;

  $reativar = HTMLHelper::renderButtonAjax($function, "gray", "rotate", "Reativar Usuário");

  $function = <<<JS
    setWarning(
      "Deseja excluir o usuário {$usuario['u_nome']}?",
      "Esta ação não pode ser desfeita. Os leads e atendimentos que eram deste usuário ficarão sem dono.",
      true,
      () => {
        window.location.href = "{$_ENV['HOST_BASE']}excluir-usuario/{$usuario['u_id']}";
      }
    )
  JS;

  $deletar = HTMLHelper::renderButtonAjax($function, "alerta", "trash-can", "Apagar dados permanentemente");

  $function = <<<JS
    setWarning(
      "Deseja apagar a senha do {$usuario['u_nome']}?", 
      "Ao apagar a senha, será enviado o email para o usuário criar uma nova.", 
      true,
      () => {
        window.location.href = "{$_ENV['HOST_BASE']}recuperar-senha/{$usuario['u_id']}";
      }
    )
  JS;

  $alterarSenha = HTMLHelper::renderButtonAjax($function, "gray", "key", "Alterar senha do Usuário");

  switch ((int)$usuario["us_id"]){
    case 1:
      $btns .= $editar;
      $btns .= $reenviarEmail;
      $btns .= $desativar;
      break;
    case 2:
      $btns .= $reativar;
      $btns .= $deletar;
      break;
    case 3:
      $btns .= $editar;
      $btns .= $alterarSenha;
      $btns .= $desativar;
      break;
  }

  $content = <<<HTML
    <div class="centered">
      <div class="foto">$foto</div>
      <div class="usr-content">
        <p><b>{$usuario['u_nome']}</b></p>
        <p>{$usuario['u_email']}</p>
        <p>{$usuario['u_celular']}</p>
        <p>
          <span class="underline" title="{$usuario['niv_descricao']}">{$usuario['niv_nome']}</span><br>
          <span class="underline" title="{$usuario['us_descricao']}">{$usuario["us_nome"]}</span>
        </p>
      </div>
    </div>
    <div class="card__icons">
      $btns
    </div>
  HTML;

  echo HTMLHelper::renderCard($content, ["card-padrao--thinner"]);
}
?>