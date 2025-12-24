export function initAnimations() {
  const reveals = document.querySelectorAll("[class*='animate--']");
  const hover = document.querySelectorAll("[data-simular-hover]");

  // === ANIMAÇÕES (usando IntersectionObserver)
  const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      const el = entry.target;
      if (entry.isIntersecting) {
        // Ativa a animação
        el.classList.forEach(cls => {
          if (cls.startsWith("animate--") && !cls.endsWith("--ativo")) {
            el.classList.add(cls + "--ativo");
          }
        });
        observer.unobserve(el); // só anima uma vez
      }
    });
  }, {
    root: null,              // viewport
    threshold: 0.1,          // ativa quando 10% visível
    rootMargin: "0px 0px -200px 0px" // começa um pouco antes
  });

  reveals.forEach(el => revealObserver.observe(el));

  // === FORÇAR HOVER NO MOBILE (também com Observer)
  const hoverObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      const el = entry.target;
      if (entry.isIntersecting) {
        el.classList.add("hover-simulado");
      } else {
        el.classList.remove("hover-simulado");
      }
    });
  }, {
    threshold: 0,
    rootMargin: "0px 0px -200px 0px"
  });

  hover.forEach(el => hoverObserver.observe(el));
}