import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";

const infobox = document.querySelector(".js--infobox");

export function alterReceiving(btn, dataset) {
  var equipeId = dataset.equipeId;

  var ativado = "switch-btn--ativado";
  var desativado = "switch-btn--desativado";

  var set = !btn.classList.contains(ativado);

  var td = btn.closest("td");
  var next = td.nextElementSibling;
  var buttons = next.querySelectorAll("button");
  
  postRequest(hostBase + dataset.url,
    "recebe_leads=" + set
    + "&equipe_id=" + equipeId,
    (response) => {
      if (response.success === true) {
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

        infobox.innerHTML = response.info_box_html;

      } else {
        setWarning(
          response.alert,
          response.messages,
          false
        );
      }
    });
}