import { postRequest } from "../../ajax/request";
// import { setWarning } from "../../ui/warning";

export function remover(btn, dataset) {
  var colaboradorId = dataset.colaboradorId;
  var equipeId = dataset.equipeId;
  
  postRequest("http://crm.local/remover-colaborador/" + colaboradorId,
    "equipe_id="+ equipeId,
    (response) => {
      var infobox = document.querySelector(".js--infobox");
      infobox.innerHTML = response.fila;
      var tr = btn.closest("tr");
      tr.remove();
      var number = document.querySelector(".js--number-badge");
      number.innerHTML = response.numero;
    });
}