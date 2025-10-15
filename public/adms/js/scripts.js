function showPassword(a, b){
  var x = document.getElementById(a);
  var y = document.getElementById(b);
  if (y.classList.contains('fa-eye')){
    x.type = "text";
    y.classList.remove('fa-eye');
    y.classList.add('fa-lock');
  } else if (y.classList.contains('fa-lock')){
    x.type = "password";
    y.classList.remove('fa-lock');
    y.classList.add('fa-eye');
  }
}

function submitForm(){
  document.getElementById("form").submit()
}

function limparInput(array){
  for (let i = 0; i < array.length; i++){
    var a = document.querySelector(array[i]);
    if (a.checked === true) a.checked = false;
  }
}

const tooltip = document.createElement("div");
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
      tooltip.style.left = `${e.pageX + 10}px`;
      tooltip.style.top = `${e.pageY + 10}px`;
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

function salvarContato(nome,email,celular){
  fetch(`php/post-salvar-contato.php`,{
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `nome=${encodeURIComponent(nome)}&celular=${encodeURIComponent(celular)}&email=${encodeURIComponent(email)}`
  })
  .then(response => response.blob())
  .then(blob => {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'contato.vcf';
    document.body.appendChild(a);
    a.click();
    a.remove();
    window.URL.revokeObjectURL(url);
  });
}

function setWarning(mensagem, desc, ask, func) {
  const warning = document.querySelector('.warning');

  if (ask){
    var input = 
    `<button class="small-btn small-btn--normal" onclick="confirmarAviso(true)">Sim</button>
    <button class="small-btn small-btn--alerta" onclick="confirmarAviso(false)">Cancelar</button>`;
  } else {
    var input = `<button class="small-btn small-btn--normal" onclick="confirmarAviso(true)">Ok</button>`;
  }

  console.log(desc);

  warning.innerHTML = `
    <div>
      <b class='warning__titulo'>${mensagem}</b>
      <p class='warning__descricao'>${desc}</p>
      <div class='warning__buttons'>${input}</div>
    </div>
  `;
  warning.classList.add('warning--show');

  window._callbackConfirmar = func;
}

function confirmarAviso(confirmou) {
  document.querySelector('.warning').classList.remove('warning--show');
  if (confirmou && typeof window._callbackConfirmar === 'function') {
    window._callbackConfirmar();
  } else {
    document.querySelector('.warning').innerHTML = "Não é uma função";
  }
}

function applyThinnerForm(){
  document.querySelector(".all").classList.add("all--centered");
}