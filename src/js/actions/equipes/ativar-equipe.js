import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";
import { renderizar } from "../../ajax/render";

export function ativarEquipe(btn, dataset) {
  var equipeId = dataset.equipeId;
  var equipeNome = dataset.equipeNome;

  setWarning(
    "Deseja despausar a equipe "+equipeNome+"?",
    "Ela voltará a receber leads normalmente.",
    true,
    () => {
      postRequest(
        hostBase+"/ativar-equipe/"+equipeId,
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