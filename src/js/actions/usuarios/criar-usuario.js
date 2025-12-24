import { postRequest } from "../../ajax/request";
import { openOverlay, overlayContent } from "../../ui/overlay";

export function criarUsuario() {
  postRequest("http://crm.local/criar-usuario/", "", (response) => {
    openOverlay();
    overlayContent(response.html);
  });
}