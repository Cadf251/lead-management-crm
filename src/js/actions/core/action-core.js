import { finishLoading, removeCard, renderizar, setLoading } from "../../ajax/render";
import { postRequest, getRequest } from "../../ajax/request";
import { hostBase } from "../../core/host";
import { processResponse } from "../../core/process-response";
import { openOverlay, overlayContent } from "../../ui/overlay";
import { setWarning } from "../../ui/warning";

export const ActionHandler = {
  run(btn, dataset) {
    const url = dataset.url; // A URL agora vem do HTML

    // Se precisar de confirmação
    if (dataset.confirm) {
      setWarning(dataset.confirmTitle, dataset.confirmText, true, () => ActionHandler.execute(url, btn, dataset));
    } else {
      ActionHandler.execute(url, btn, dataset);
    }
  },

  execute(url, btn, dataset) {
    const body = new URLSearchParams();

    // Deixa o card com efeito de carregando
    if (dataset.target) {
      setLoading(dataset.target);
    }

    // 1. Coleta dados estáticos do próprio botão (data-param-*)
    Object.keys(dataset).forEach(key => {
      if (key.startsWith('param')) {
        // Ex: data-param-usuario-id -> usuario_id
        const paramName = key.replace('param', '')
          .replace(/([A-Z])/g, '_$1')
          .toLowerCase()
          .replace(/^_/, '');
        body.append(paramName, dataset[key]);
      }
    });

    // 2. Coleta dados dinâmicos de outros elementos (data-include=".classe")
    if (dataset.include) {
      document.querySelectorAll(dataset.include).forEach(el => {
        if (el.name && el.value !== undefined) {
          body.append(el.name, el.value);
        }
      });
    }

    const isView = dataset.actionType === "overlay";

    // 2. Callback unificado (o que fazer quando a resposta chegar)
    const callback = (response) => {
      // Se houver efeito de loading, paramos aqui antes de processar
      if (dataset.target) finishLoading(dataset.target);

      processResponse(response, btn);
    };

    // 3. Disparo baseado no tipo
    if (isView) {
      // GET para buscar views/forms (Sem CSRF)
      getRequest(hostBase + url, callback);
    } else {
      // POST para salvar/deletar/resetar (Com CSRF)
      const params = body.toString();
      postRequest(hostBase + url, params, callback);
    }
  }
};