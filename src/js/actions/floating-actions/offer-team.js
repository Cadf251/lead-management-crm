import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { FloatingAction } from "../../ui/floating-action";

export function offerTeam(btn, dataset) {
  const trigger = btn;

  // Chama o componente UI passando as coordenadas do mouse e o ID
  FloatingAction.open(trigger, "offer-team");

  postRequest(
    hostBase+"floating-action/offer-team",
    "",
    (response) => {
      FloatingAction.fill(response.html)
    }
  )
}