document.addEventListener('DOMContentLoaded', function () {
  // Máscara do input
  document.querySelectorAll('.phone').forEach(function (el) {
    el.addEventListener('input', function () {
      let text = this.value;
      text = text.replace(/\D/g, '');
      if (text.length > 0) text = text.replace(/.{0}/, '$&(');
      if (text.length > 3) text = text.replace(/.{3}/, '$&)');
      if (text.length > 9) text = text.replace(/.{9}/, '$&-');
      this.value = text;
    });
  });
});

function postRequest(task, usuarioId, usuarioNome){
  fetch('php/post-gerenciar-usuarios.php',{
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `task=${task}&usuario_id=${usuarioId}&usuario_nome=${usuarioNome}`
  })
  .then(res => res.json())
  .then(data => {
    const warning = document.querySelector('.warning');
    if (data.sucesso) {
      warning.innerHTML = `
      <div style="text-align: center;">
        <b>Sucesso!</b>
        <p>`+data.mensagem+`</p>
        <button class="small-btn small-btn--normal" onclick="recarregar(`+data.recarregar+`)">Ok!</button>
      </div>`;
      warning.classList.add('warning--show');
    } else {
      warning.innerHTML = `
      <div style="text-align: center;">
        <b>Algo deu errado.</b>
        <p>`+data.mensagem+`</p>
        <button class="small-btn small-btn--alerta" onclick="document.querySelector('.warning').classList.remove('warning--show')">Ok!</button>
      </div>`;
      warning.classList.add('warning--show');
    }
  });
}

function recarregar(a){
  const warn = document.querySelector('.warning').classList.remove('warning--show');
  if (a === true) window.location.reload();
}