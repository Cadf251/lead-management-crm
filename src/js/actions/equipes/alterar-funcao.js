import { postRequest } from "../../ajax/request";
import { setWarning } from "../../ui/warning";

export function alterarFuncao(btn, dataset) {
  var colaboradorId = dataset.colaboradorId;
  var select = btn.previousElementSibling;
  var novaFuncao = select.value;

  postRequest("http://crm.local/alterar-funcao/"+colaboradorId,
    "funcao_id="+novaFuncao, 
    (response) => {
      if (response.sucesso === true || response.sucesso !== undefined) {
        btn.dataset.originalValue = novaFuncao;
        btn.disabled = true;
      } else {
        setWarning(
          sucesso.alerta,
          sucesso.mensagens,
          false
        );
      }
  });
}