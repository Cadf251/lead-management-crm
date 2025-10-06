<h1 class="titulo-1">Instalar</h1>
<p>Escolha um servidor para instalar</p>
<?php

use App\adms\Helpers\HTMLHelper;

?>

<div class="w6">
<?php
foreach($this->data["servidores"] as $servidor){
  $status = $servidor["status"] == 1 ? "Ativo" : "Desativado";
  $created = $servidor["created"] != null ? date("d/m/Y H:i:s", strtotime($servidor["created"])) : "";
  $modified = $servidor["modifid"] != null ? date("d/m/Y H:i:s", strtotime($servidor["modified"])) : "";


  $content = <<<HTML
  <b>{$servidor["nome"]}</b>
  <ul>
    <li>ID: {$servidor['id']}</li>
    <li>Host: {$servidor['host']}</li>
    <li>User: {$servidor['user']}</li>
    <li>Db Name: {$servidor['db_name']}</li>
    <li>Versão: {$servidor['versao']}</li>
    <li>Status: {$status}</li>
    <li>Criado: {$created}</li>
    <li>Modificado: {$modified}</li>
  </ul>
  <a href="">Ativar</a>
  HTML;

  echo HTMLHelper::renderCard($content);
}
?>
</div>