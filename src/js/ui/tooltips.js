export function initTooltip(){
  const tooltip = document.createElement("div");
  const viewportWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
  const half = viewportWidth / 2;
  tooltip.className = "tooltip";

  function applyTooltip(el) {
    const titleText = el.getAttribute("title");

    if (!titleText) return;

    // Atualiza o texto do tooltip mesmo que já tenha aplicado antes
    el.dataset.tooltipText = titleText;

    if (!el.hasAttribute("data-tooltip-applied")) {
      el.removeAttribute("title");
      el.setAttribute("data-tooltip-applied", "true");

      el.addEventListener("mouseenter", () => {
        tooltip.textContent = el.dataset.tooltipText;
        tooltip.style.opacity = 1;
      });

      el.addEventListener("mousemove", e => {
        const offset = 12;

        tooltip.style.top = `${e.pageY + offset}px`;

        if (e.pageX < half) {
          tooltip.style.left = `${e.pageX + offset}px`;
        } else {
          tooltip.style.left = `${e.pageX - tooltip.offsetWidth - offset}px`;
        }
      });

      el.addEventListener("mouseleave", () => {
        tooltip.style.opacity = 0;
      });
    }
  }

  // Aplicar nos elementos existentes
  window.onload = () => {
    document.body.appendChild(tooltip);
    document.querySelectorAll("[title]").forEach(applyTooltip);
    observer.observe(document.body, {
      attributes: true,
      subtree: true,
      attributeFilter: ["title"]
    });
  };

  // Observar mudanças de atributo 'title'
  const observer = new MutationObserver(mutations => {
    mutations.forEach(mutation => {
      if (mutation.type === "attributes" && mutation.attributeName === "title") {
        applyTooltip(mutation.target);
      }
    });
  });
}