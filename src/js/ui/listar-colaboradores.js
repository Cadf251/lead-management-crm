export function initColaboradores() {
  const selects = document.querySelectorAll(".js--usuario-funcao");

  if (!selects) return;

  selects.forEach(select => {
    const btn = select.nextElementSibling;
    const originalValue = select.value;
    select.addEventListener("change", () => {
      btn.disabled = btn.dataset.originalValue === select.value;
    })
  });
}