import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";
import { renderizar } from "../../ajax/render";

export function desativar(btn, dataset) {
  var usuarioId = dataset.usuarioId;
  var usuarioNome = dataset.usuarioNome;

  setWarning(
    "Deseja desativar o usuário "+usuarioNome+"?",
    "Ele perderá o acesso, mas seus dados continuarão no histórico do sistema.",
    true,
    () => {
      postRequest(
        hostBase+"desativar-usuario/"+usuarioId,
        "",
        (response) => {
          renderizar(response.html, ".card--"+usuarioId);
          setWarning(
            response.alerta,
            response.mensagens,
            false
          );
        }
      )
    }
  )
}