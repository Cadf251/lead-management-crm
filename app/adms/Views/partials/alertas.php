<?php if (isset($_SESSION["alerta"])): 
  $titulo = $_SESSION["alerta"][0];
  $mensagem = is_array($_SESSION["alerta"][1])
    ? implode("<br>", $_SESSION["alerta"][1])
    : $_SESSION["alerta"][1];
?>

<div 
  id="session-warning"
  data-warning-titulo="<?= htmlspecialchars($titulo) ?>"
  data-warning-mensagem="<?= htmlspecialchars($mensagem) ?>"
  data-warning-ask="false">
</div>

<?php unset($_SESSION["alerta"]); endif; ?>