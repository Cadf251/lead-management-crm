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

// function postRequest(task, usuarioId, equipeId, btn){
//   fetch('php/post-gerenciar-equipes.php',{
//     method: 'POST',
//     headers: {
//     'Content-Type': 'application/x-www-form-urlencoded'
//     },
//     body: `task=${task}&usuario_id=${usuarioId}&equipe_id=${equipeId}`
//   })
//   .then(res => res.json())
//   .then(data => {
//     const warning = document.querySelector('.warning');
//     if (data.sucesso) {
//       warning.innerHTML = `
//       <div style="text-align: center;">
//         <b>Sucesso!</b>
//         <p>`+data.mensagem+`</p>
//         <button class="small-btn small-btn--normal" onclick="recarregar(`+data.recarregar+`)">Ok!</button>
//       </div>`;
//       warning.classList.add('warning--show');
//       priorizarBtn = document.getElementById("priorizar-btn-"+usuarioId);
//       prejudicarBtn = document.getElementById("prejudicar-btn-"+usuarioId);
//     } else {
//       warning.innerHTML = `
//       <div style="text-align: center;">
//         <b>Algo deu errado.</b>
//         <p>`+data.mensagem+`</p>
//         <button class="small-btn small-btn--alerta" onclick="document.querySelector('.warning').classList.remove('warning--show')">Ok!</button>
//       </div>`;
//       warning.classList.add('warning--show');
//     }
//   });
// }

// function recarregar(a){
//   const warn = document.querySelector('.warning').classList.remove('warning--show');
//   if (a === true) window.location.reload();
// }

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

