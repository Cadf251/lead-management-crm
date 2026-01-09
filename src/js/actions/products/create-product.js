import { postRequest } from "../../ajax/request";
import { openOverlay, overlayContent } from "../../ui/overlay";

export function createProduct() {
  postRequest("http://crm.local/criar-produto/", "", (response) => {
    openOverlay();
    overlayContent(response.html);
  });
}