let callbackConfirmar = null;

export function setWarning(mensagem, desc, ask = true, callback = null) {
  const warning = document.querySelector(".warning");

  callbackConfirmar = callback;

  warning.innerHTML = `
    <div>
      <b class="warning__titulo">${mensagem}</b>
      <p class="warning__descricao">${desc}</p>

      <div class="warning__buttons">
        ${
          ask
            ? `
              <button class="small-btn small-btn--blue" data-warning-confirm="true">Sim</button>
              <button class="small-btn small-btn--red" data-warning-confirm="false">Cancelar</button>
            `
            : `
              <button class="small-btn small-btn--blue" data-warning-confirm="true">Ok</button>
            `
        }
      </div>
    </div>
  `;

  warning.classList.add("warning--show");
}

export function initWarningDelegation() {
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-warning-confirm]");
    if (!btn) return;

    const confirmou = btn.dataset.warningConfirm === "true";

    document
      .querySelector(".warning")
      .classList.remove("warning--show");

    if (confirmou && typeof callbackConfirmar === "function") {
      callbackConfirmar();
    }

    callbackConfirmar = null;
  });

  document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("session-warning");
    if (!el) return;

    const titulo = el.dataset.warningTitulo;
    const mensagem = el.dataset.warningMensagem;
    const ask = el.dataset.warningAsk === "true";

    setWarning(titulo, mensagem, ask);
  });
}
