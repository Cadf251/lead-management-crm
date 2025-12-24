import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";
import { renderizar } from "../../ajax/render";

export function pausarEquipe(btn, dataset) {
  var equipeId = dataset.equipeId;
  var equipeNome = dataset.equipeNome;

  setWarning(
    "Deseja pausar a equipe "+equipeNome+"?",
    "Ela não receberá leads enquanto estiver pausada.",
    true,
    () => {
      postRequest(
        hostBase+"/congelar-equipe/"+equipeId,
        "",
        (response) => {
          renderizar(response.html, ".card--"+equipeId);
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