<?php

if (isset($_SESSION["alerta"])){
  echo <<<HTML
    <script>
      setWarning("{$_SESSION['alerta'][0]}", "{$_SESSION['alerta'][1]}", false, () => {return false;});
    </script>
  HTML;

  unset($_SESSION["alerta"]);
}