import { postRequest } from "../../ajax/request";
import { setWarning } from "../../ui/warning";

export function alterarRecebimento(btn, dataset) {
  var ativado = "switch-btn--ativado";
  var desativado = "switch-btn--desativado";

  var set = !btn.classList.contains(ativado);
  var colaboradorId = dataset.colaboradorId;
  var equipeId = dataset.equipeId;
  var td = btn.closest("td");
  var next = td.nextElementSibling;
  var buttons = next.querySelectorAll("button");

  postRequest("http://crm.local/alterar-recebimento/" + colaboradorId,
    "recebe_leads=" + set
    +"&equipe_id="+equipeId,
    (response) => {
      if (response.sucesso === true) {
        if (set) {
          btn.innerHTML = "Sim";
          btn.classList.remove(desativado);
          btn.classList.add(ativado);

          buttons.forEach(button => {
            button.disabled = false;
          });
        } else {
          btn.innerHTML = "Não";
          btn.classList.remove(ativado);
          btn.classList.add(desativado);

          buttons.forEach(button => {
            button.disabled = true;
          });
        }
        var infobox = document.querySelector(".js--infobox");
        infobox.innerHTML = response.fila;
      } else {
        setWarning(
          response.alerta,
          response.mensagens,
          false
        );
      }
    });
}