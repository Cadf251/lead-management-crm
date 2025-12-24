import { Actions } from "../actions/index.js";

export function initEventDelegation () {
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-action]");
    if (!btn) return;

    const action = btn.dataset.action;

    Actions[action]?.(btn, btn.dataset);
  });
}
