import { postRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { setWarning } from "../../ui/warning";
import { renderizar, removeCard } from "../../ajax/render";

export function desativarEquipe(btn, dataset) {
  var equipeId = dataset.equipeId;
  var equipeNome = dataset.equipeNome;

  setWarning(
    "Deseja desativar a equipe "+equipeNome+"?",
    "Ela será completamente excluída.",
    true,
    () => {
      postRequest(
        hostBase+"/desativar-equipe/"+equipeId,
        "",
        (response) => {
          removeCard(".card--"+equipeId);
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