export function initFormUi() {

  document.addEventListener('change', function (event) {
    // 1. Verifica se o que disparou o evento é um input ou textarea
    const el = event.target;

    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === "SELECT") {
      if (el.closest('.js--form-edit')) {
        if (!el.classList.contains("input--changed"))
          el.classList.add('input--changed');
      }
    }
  });
}
