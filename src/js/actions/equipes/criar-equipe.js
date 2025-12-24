import { postRequest } from "../../ajax/request";
import { openOverlay, overlayContent } from "../../ui/overlay";

export function criarEquipe() {
  postRequest("http://crm.local/criar-equipe/", "", (response) => {
    openOverlay();
    overlayContent(response.html);
  });
}