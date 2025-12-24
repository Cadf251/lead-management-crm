import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";
import { renderizar } from "../../ajax/render";

export function resetarSenha(btn, dataset) {
  var usuarioId = dataset.usuarioId;
  var usuarioNome = dataset.usuarioNome;
  
  setWarning(
    "Deseja resetar a senha do "+usuarioNome+"?",
    "Ao resetar a senha, será enviado o email para o usuário criar uma nova.", 
    true,
    () => {
      postRequest(
        hostBase+"resetar-senha/"+usuarioId,
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