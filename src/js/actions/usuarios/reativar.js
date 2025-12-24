import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";
import { renderizar } from "../../ajax/render";

export function reativarUsuario(btn, dataset) {
  var usuarioId = dataset.usuarioId;
  var usuarioNome = dataset.usuarioNome;

  setWarning(
    "Deseja reativar o usuário "+usuarioNome+"?",
    "Ele terá acesso a praticamente tudo que tinha antes.",
    true,
    () => {
      postRequest(
        hostBase+"/reativar-usuario/"+usuarioId,
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