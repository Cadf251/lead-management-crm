<?php
if (isset($_SESSION["alerta"])){
  // Se a mensagem for um array, empilha com quebras de linha.
  if (is_array($_SESSION['alerta'][1])){
    $_SESSION["alerta"][1] = implode("<br>", $_SESSION["alerta"][1]);
  }

  echo <<<HTML
    <script>
      setWarning("{$_SESSION['alerta'][0]}", "{$_SESSION['alerta'][1]}", false, () => {return false;});
    </script>
  HTML;

  unset($_SESSION["alerta"]);
}