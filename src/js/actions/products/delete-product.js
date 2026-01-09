import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";
import { removeCard } from "../../ajax/render";

export function deleteProduct(btn, dataset) {
  var productId = dataset.productId;
  var productName = dataset.productName;

  setWarning(
    "Deseja deletar o produto "+productName+"?",
    "Ele será completamente excluído para sempre.",
    true,
    () => {
      postRequest(
        hostBase+"/deletar-produto/"+productId,
        "",
        (response) => {
          removeCard(".card--"+productId);
          setWarning(
            response.alerta,
            response.mensagens,
            false
          );
        }
      )
    }
  )
}