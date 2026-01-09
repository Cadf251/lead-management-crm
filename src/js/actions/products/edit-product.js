import { postRequest } from "../../ajax/request";
import { openOverlay, overlayContent } from "../../ui/overlay";

export function editProduct(btn, dataset) {
  var productId = dataset.productId;

  postRequest("http://crm.local/editar-produto/"+productId,
    "",
    (response) => {
      openOverlay();
      overlayContent(response.html);
    });
}