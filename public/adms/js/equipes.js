const selectUsuario = document.querySelector(".js--usuario-select");
if ((selectUsuario !== null) && (selectUsuario !== undefined)){
  const selectFuncao = document.querySelector(".js--usuario-funcao");
  const opcaoGerente = selectFuncao.querySelector("option[value=\'2\']");

  selectUsuario.addEventListener("change", () => {
    const valor = selectUsuario.value;
    const partes = valor.split(",");
    const nivel = parseInt(partes[1]);

    if (nivel >= 3) {
      selectFuncao.selectedIndex = 0;
      opcaoGerente.disabled = false;
    } else {
      selectFuncao.selectedIndex = 0;
      opcaoGerente.disabled = true;
    }
  });
}

window.onload = () => {
  const selectElement = document.querySelectorAll('.js--select');
  const saveElement = document.querySelectorAll('.js--salvar');
  
  const selectArray = Array.from(selectElement);
  
  selectArray.forEach(select => {
    const originalValue = select.value;
    select.addEventListener('change', () => {
      const i = selectArray.indexOf(select);
      console.log(i);
      if (select.value !== originalValue) {
        saveElement[i].classList.add('small-btn--normal');
        saveElement[i].classList.remove('small-btn--gray');
        saveElement[i].disabled = false;
      } else {
        saveElement[i].classList.add('small-btn--gray');
        saveElement[i].classList.remove('small-btn--normal'); 
        saveElement[i].disabled = true;
      }
    });
  });
};

function renderizar(html, className){
  var x = document.querySelector(className);
  x.innerHTML = html;
}