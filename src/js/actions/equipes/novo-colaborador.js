import { postRequest } from "../../ajax/request";
import { openOverlay, overlayContent } from "../../ui/overlay";
import { hostBase } from "../../core/host";
import { initColaboradorForm } from "../../ui/novo-colaborador-form";

export function novoColaborador(btn, dataset) {
  var equipeId = dataset.equipeId;
  
  postRequest(hostBase+"novo-colaborador/"+equipeId, "",
    (response) => {
      openOverlay();
      overlayContent(response.html);
      initColaboradorForm();
    }
  );

}