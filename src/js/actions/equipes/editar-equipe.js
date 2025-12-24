import { postRequest } from "../../ajax/request";
import { openOverlay, overlayContent } from "../../ui/overlay";

export function editarEquipe(btn, dataset) {
  var equipeId = dataset.equipeId;

  postRequest("http://crm.local/editar-equipe/"+equipeId, "", (response) => {
    openOverlay();
    overlayContent(response.html);
  });
}