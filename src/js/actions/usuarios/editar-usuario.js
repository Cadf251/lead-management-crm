import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { openOverlay, overlayContent } from "../../ui/overlay";

export function editarUsuario(btn, dataset) {
  var usuarioId = dataset.usuarioId;
  postRequest(hostBase+"editar-usuario/"+usuarioId, "", (response) => {
    openOverlay();
    overlayContent(response.html);
  });
}