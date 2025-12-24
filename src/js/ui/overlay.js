import { setWarning } from "./warning";

export function initOverlay() {
  const overlay = document.querySelector(".js--overlay");
  const body = document.querySelector(".js--body");
  const content = document.querySelector(".js--overlay-content");

  const close = document.querySelector(".js--overlay-close");

  if (close === null) return;

  close.addEventListener("click", () => {
    if (!overlay.classList.contains("overlay--oculto")) {
      setWarning("Deseja fechar?", "Todo progresso será descartado.", true, () => {
        overlay.classList.add("overlay--oculto");
        body.classList.remove("congelado");
        content.innerHTML = "";
      });
    }
  });
}

export function openOverlay() {
  const overlay = document.querySelector(".js--overlay");
  const body = document.querySelector(".js--body");

  if (overlay.classList.contains("overlay--oculto")) {
    overlay.classList.remove("overlay--oculto");
    body.classList.add("congelado");
  }
}

export function overlayContent(content) {
  const overlay = document.querySelector(".js--overlay-content");

  overlay.innerHTML = content;
}