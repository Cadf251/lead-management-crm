import { postRequest } from "../../ajax/request";
import { setWarning } from "../../ui/warning";

export function prejudicar(btn, dataset) {
  var colaboradorId = dataset.colaboradorId;
  var equipeId = dataset.equipeId;

  postRequest("http://crm.local/mudar-vez/" + colaboradorId,
    "task=prejudicar"
      + "&equipe_id="+ equipeId,
    (response) => {
      var infobox = document.querySelector(".js--infobox");
      infobox.innerHTML = response.fila;
    });
}