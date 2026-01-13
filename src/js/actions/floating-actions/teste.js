import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { FloatingAction } from "../../ui/floating-action";

export function teste(btn, dataset) {
  const trigger = btn;
  const floatingId = dataset.floatingId;

  // Chama o componente UI passando as coordenadas do mouse e o ID
  FloatingAction.open(trigger, floatingId);

  postRequest(
    hostBase+"floating-action/"+floatingId,
    "",
    (response) => {
      FloatingAction.fill(response.html)
    }
  )
}