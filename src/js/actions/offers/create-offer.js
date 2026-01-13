import { postRequest } from "../../ajax/request";
import { openOverlay, overlayContent } from "../../ui/overlay";

export function createOffer() {
  postRequest("http://crm.local/criar-oferta/", "", (response) => {
    openOverlay();
    overlayContent(response.html);
  });
}