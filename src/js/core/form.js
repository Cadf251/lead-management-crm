import { append, removeCard, renderizar } from "../ajax/render";
import { postRequest } from "../ajax/request";
import { closeOverlay } from "../ui/overlay";
import { setWarning } from "../ui/warning";
import { hostBase } from "./host";
import { processResponse } from "./process-response";

// Impede que qualquer formulário marcado para AJAX seja enviado tradicionalmente
export function initForm() {
  document.addEventListener('submit', (e) => {
    if (e.target.classList.contains('js--form-ajax')) {
      e.preventDefault();
    }
  });
}

export const FormHandler = {
  submit(btn) {
    const form = btn.closest(".js--form-ajax");

    if (!form) return false;

    // 1. Valida o form
    if (!FormValidator.validate(form)) return false;

    // 2. Coleta de Dados
    const formData = new FormData(form);
    const url = form.action || window.location.href;

    // 3. Converte o objeto FormData para o formato x-www-form-urlencoded
    const params = new URLSearchParams(formData).toString();

    // 4. Feedback visual (Opcional: desativa o botão para evitar cliques duplos)
    btn.disabled = true;
    const backupText = btn.innerHTML;
    btn.innerHTML = "Enviando...";

    // 5. Faz o request
    postRequest(url, params, (response) => {
      btn.disabled = false;
      btn.innerHTML = backupText;
      processResponse(response, btn);
    });
  }
};

export const FormValidator = {
  validate(form) {
    const inputs = form.querySelectorAll('input, select, textarea');
    let isValid = true;

    inputs.forEach(input => {
      this.clearError(input);

      // Regra: Campo Obrigatório
      if (input.hasAttribute('required') && !input.value.trim()) {
        this.setError(input, "Campo obrigatório");
        isValid = false;
      }

      // Regra: E-mail (ajustado para seu caso 'mail' ou 'email')
      if ((input.type === 'email' || input.type === 'mail') && input.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(input.value)) {
          this.setError(input, "E-mail inválido");
          isValid = false;
        }
      }
    });

    return isValid;
  },

  setError(input, msg) {
    input.classList.add('input--error'); // Sua classe de CSS
    // Opcional: injetar label de erro no DOM
    var label = input.closest(".form__campo").querySelector("label");
    label.innerHTML = label.innerHTML + msg;
  },

  clearError(input) {
    input.classList.remove('input--error');
  }
};