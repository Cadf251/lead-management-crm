import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";

const infobox = document.querySelector(".js--infobox");

export function alterTime(btn, dataset) {
  var equipeId = dataset.equipeId;
  var set = dataset.set;

  postRequest(hostBase + dataset.url,
    "set=" + set
    + "&equipe_id=" + equipeId,
    (response) => {
      console.log(response);

      if (response.success === true) {
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