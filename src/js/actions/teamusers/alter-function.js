import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";

export function alterFunction(btn, dataset) {
  var select = btn.previousElementSibling;
  var novaFuncao = select.value;

  postRequest(hostBase+dataset.url,
    "funcao_id="+novaFuncao, 
    (response) => {
      if (response.success) {
        btn.dataset.originalValue = novaFuncao;
        btn.disabled = true;
      } else {
        setWarning(
          success.alert,
          success.messages,
          false
        );
      }
  });
}