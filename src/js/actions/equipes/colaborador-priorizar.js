import { postRequest } from "../../ajax/request";

export function priorizar(btn, dataset) {
  var colaboradorId = dataset.colaboradorId;
  var equipeId = dataset.equipeId;
  
  postRequest("http://crm.local/mudar-vez/" + colaboradorId,
    "task=priorizar"
      + "&equipe_id="+ equipeId,
    (response) => {
      var infobox = document.querySelector(".js--infobox");
      infobox.innerHTML = response.fila;
    });
}