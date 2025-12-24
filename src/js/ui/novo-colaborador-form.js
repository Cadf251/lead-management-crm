export function initColaboradorForm() {
  const usuarios = document.querySelector(".js--usuario-select");

  console.log("acessado");
  if (usuarios === null) return;
  
  console.log(usuarios);
  const funcao = document.querySelector(".js--usuario-funcao");
  console.log(funcao);
  const opcaoGerente = funcao.querySelector("option[value=\'2\']");
  console.log(opcaoGerente);

  usuarios.addEventListener("change", () => {
    const valor = usuarios.value;
    const partes = valor.split(",");
    const nivel = parseInt(partes[1]);

    console.log(nivel);

    if (nivel >= 3) {
      funcao.selectedIndex = 0;
      opcaoGerente.disabled = false;
    } else {
      funcao.selectedIndex = 0;
      opcaoGerente.disabled = true;
    }
  });}