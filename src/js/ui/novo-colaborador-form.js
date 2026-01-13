export function initColaboradorForm() {
  const usuarios = document.querySelector(".js--usuario-select");

  if (usuarios === null) return;
  
  const funcao = document.querySelector(".js--usuario-funcao");
  const opcaoGerente = funcao.querySelector("option[value=\'2\']");

  usuarios.addEventListener("change", () => {
    const valor = usuarios.value;
    const partes = valor.split(",");
    const nivel = parseInt(partes[1]);

    if (nivel >= 3) {
      funcao.selectedIndex = 0;
      opcaoGerente.disabled = false;
    } else {
      funcao.selectedIndex = 0;
      opcaoGerente.disabled = true;
    }
  });}