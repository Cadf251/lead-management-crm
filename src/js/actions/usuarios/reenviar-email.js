import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";
import { renderizar } from "../../ajax/render";

export function reenviarEmail(btn, dataset) {
  var usuarioId = dataset.usuarioId;
  var usuarioNome = dataset.usuarioNome;

  setWarning(
    "Deseja reenviar o email do "+usuarioNome+"?",
    "O email de redefinição de senha senha enviado.", 
    true,
    () => {
      postRequest(
        hostBase+"/reenviar-email/"+usuarioId,
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
  );
}
  