import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";

const infobox = document.querySelector(".js--infobox");
const numberBadge = document.querySelector(".js--number-badge");

export function remove(btn, dataset) {
  var equipeId = dataset.equipeId;

  var td = btn.closest("tr");
  
  postRequest(hostBase + dataset.url,
    "equipe_id=" + equipeId,
    (response) => {
      console.log(response);
      if (response.success === true) {
        console.log(response.info_box_html);
        console.log(response.number_badge_html);
        console.log(infobox);
        console.log(numberBadge);
        infobox.innerHTML = response.info_box_html;
        numberBadge.innerHTML = response.number_badge_html;
        td.remove();
      } else {
        setWarning(
          response.alert,
          response.messages,
          false
        );
      }
    });
}