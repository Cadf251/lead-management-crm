import { closeOverlay, openOverlay, overlayContent } from "../ui/overlay";
import { setWarning } from "../ui/warning";
import { hostBase } from "./host";

export const processResponse = (response, btn = null) => {

  if (!response) return;

  // 1. Redirecionamento (Prioridade Total)
  if (response.redirect) {
    window.location.href = hostBase + response.redirect;
    return;
  }

  // 2. Processamento do Array de Updates
  if (response.updates && Array.isArray(response.updates)) {
    response.updates.forEach(instr => {
      const targetEl = instr.target ? document.querySelector(instr.target) : null;

      switch (instr.type) {
        case 'change':
          if (targetEl) targetEl.outerHTML = instr.html;
          break;
        case 'update': // Substitui o elemento (outerHTML)
          if (targetEl) targetEl.innerHTML = instr.html;
          break;
        case 'append':
          if (targetEl) targetEl.insertAdjacentHTML('beforeend', instr.html);
          break;
        case 'remove':
          if (targetEl) targetEl.remove();
          break;
        case 'overlay': // O HTML agora está amarrado aqui
          openOverlay();
          overlayContent(instr.html);
          break;
      }
    });
  }

  // 3. Comandos de UI e Segurança
  if (response.close_overlay) closeOverlay();

  if (!response.success && response.csrf_token && btn) {
    const form = btn.closest('form');
    const tokenInput = form?.querySelector("input[name='csrf_token']");
    if (tokenInput) tokenInput.value = response.csrf_token;
  }

  // 4. Alertas (Feedbacks)
  if (response.messages) {
    setWarning(response.alert, response.messages, false);
  }
};