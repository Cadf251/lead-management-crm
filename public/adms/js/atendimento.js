$('.money').on('input', function() {
  let text=$(this).val()
  text = text.replace(/[^\d,]/g, '');
  //proíbe mais que dois dígitos depois da vírgula
  text = text.replace(/,(\d{0,2})\d*/g, ',$1');
  if(text.length>0) text=text.replace(/.{0}/,'$&R') 
  if(text.length>0) text=text.replace(/.{1}/,'$&$')
    $(this).val(text);
});

function createInput(val){
  var input = document.createElement("input");
  input.type="text";
  input.className="input";
  input.value=val;
  input.style.minWidth = "300px";
  input.style.fontSize = "1em";
  return input;
}

function ehCelular(valor) {
  const numero = valor.replace(/\D/g, '');
  if (numero.length !== 11) return false;
  if (numero[2] !== '9') return false;
  return true;
}

function changeButton(btn, idx, val){
  var icon = btn.querySelector("i");
  if (idx === 0){
    btn.classList.remove("small-btn--normal");
    btn.classList.add("small-btn--alerta");
    btn.setAttribute("title", "Cancelar");
    btn.setAttribute("onclick", "cancelar(this, '"+val+"')");

    icon.classList.remove("fa-pencil");
    icon.classList.add("fa-xmark");
    icon.style.width = icon.offsetHeight + 'px';
  } else if (idx === 1){
    btn.classList.remove("small-btn--alerta");
    btn.classList.add("small-btn--normal");
    btn.setAttribute("title", "Editar");
    btn.setAttribute("onclick", "turnToInput(this)")

    icon.classList.remove("fa-xmark");
    icon.classList.add("fa-pencil");
  }
  btn.style.margin = 0;
}

function changeSaveBtn(saveBtn, idx){
  if (idx === 0){
    saveBtn.disabled = true;
    saveBtn.classList.remove("small-btn--normal");
    saveBtn.classList.add("small-btn--gray");
  } else if (idx === 1){
    saveBtn.disabled = false;
    saveBtn.classList.remove("small-btn--gray");
    saveBtn.classList.add("small-btn--normal");
  }
}

function createSpan(val){
  var span = document.createElement("span");
  span.innerHTML = val;
  span.className="input-like";
  return span;
}

function turnToInput(button, type){
  var span = button.nextElementSibling;
  var saveBtn = span.nextElementSibling;
  var val = span.innerHTML;

  var input = createInput(val);

  span.replaceWith(input);
  input.focus();

  switch(type){
    case "texto":
      break;
    case "email":
      break;
    case "celular":
      input.classList.add("phone");
      input.setAttribute("maxlength", 14);
      $('.phone').on('input', function() {
        let text=$(this).val()
        text=text.replace(/\D/g,'')
        if(text.length>0) text=text.replace(/.{0}/,'$&(') 
        if(text.length>3) text=text.replace(/.{3}/,'$&)')
        if(text.length>9) text=text.replace(/.{9}/,'$&-')
          $(this).val(text);
      });
      break;
  }

  changeSaveBtn(saveBtn, 0);
  changeButton(button, 0, val);

  saveBtn.classList.remove("display-none");

  input.addEventListener("input", function () {
    if (input.value == val){
      changeSaveBtn(saveBtn, 0);
    } else {
      changeSaveBtn(saveBtn, 1);
    }
  });
}

function cancelar(button, oldValue){
  var input = button.nextElementSibling;
  var saveBtn = input.nextElementSibling;
  
  var span = createSpan(oldValue);

  input.replaceWith(span);

  changeButton(button, 1, oldValue);

  changeSaveBtn(saveBtn, 1);

  saveBtn.classList.add("display-none");
}

function saveText(saveBtn, tabela, campo, leadId){
  var input = saveBtn.previousElementSibling;
  var button = input.previousElementSibling;
  var novoValor = input.value.trim();
  console.log(tabela);
  var task = "atualizar_nome";
  fetch('php/post-atendimento.php',{
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `task=${task}&tabela=${tabela}&lead_id=${leadId}&campo=${campo}&novo_valor=${novoValor}`
  })
  .then(res => res.json())
  .then(data => {
    console.log(data.mensagem);
    if(data.sucesso){
      changeSaveBtn(saveBtn,1);
      saveBtn.classList.add("display-none");
      var span = createSpan(novoValor);
      input.replaceWith(span);
      changeButton(button, 1, novoValor);
    }
  });
}

function postAtt(task, leadId, attId){
  fetch('php/post-atendimento.php',{
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `task=${task}&lead_id=${leadId}&att_id=${attId}`
  })
  .then(res => res.json())
  .then(data => {

    if (data.sucesso)
      var title = "Sucesso!";
    else 
      var title = "Algo deu errado!";

    if (data.voltar){
      var depois = () => {
      var voltarBtn = document.getElementById("voltar");
      voltarBtn.click()};
    } else if (data.recarregar){
      var depois = () => {
        window.location.href = window.location.href;
      }
    } else {
      var depois = null;
    }

    setWarning(title, data.mensagem, false, depois);
  });
}

const salvar = document.querySelector(".js--salvar-contato");
const whats = document.querySelector(".js--whatsapp");
const telefone = document.querySelector(".js--telefone");
const email = document.querySelector(".js--email");

const contatoConfig = document.querySelector(".js--contato-config");
const contatoConfigInputs = contatoConfig.querySelectorAll("input");

const emailConfig = document.querySelector(".js--email-config");
const emailConfigInputs = emailConfig.querySelectorAll("input");

const msg = document.querySelector(".js--mensagem-box");
const msgInputs = msg.querySelectorAll("input, select, textarea");

const select = document.querySelector(".js--select-message-type");
const content = document.querySelector(".js--display-selected");
const messages = content.querySelectorAll(".form-padrao");

// ATIVA A PERSONALIZAÇÃO DO CONTATO
salvar.addEventListener("change", () => {
  if (contato.checked === true) {
    contatoConfig.classList.remove("disabled");
    for (i=0;i<contatoConfigInputs.length;i++){
      contatoConfigInputs[i].disabled = false;
    }
  } else {
    contatoConfig.classList.add("disabled");
    for (i=0;i<contatoConfigInputs.length;i++){
      contatoConfigInputs[i].disabled = true;
    }
  }
});

// ATIVA A PERSONALIZAÇÃO DO EMAIL
email.addEventListener("change", () => {
  if (email.checked === true) {
    emailConfig.classList.remove("disabled");
    for (i=0;i<emailConfigInputs.length;i++){
      emailConfigInputs[i].disabled = false;
    }
  } else {
    emailConfig.classList.add("disabled");
    for (i=0;i<emailConfigInputs.length;i++){
      emailConfigInputs[i].disabled = true;
    }
  }

  if((whats.checked === true)||(email.checked === true)) {
    msg.classList.remove("disabled");
    for (i=0;i<msgInputs.length;i++){
      msgInputs[i].disabled = false;
    }
  } else {
    msg.classList.add("disabled");
    msg.disabled = true;
    for (i=0;i<msgInputs.length;i++){
      msgInputs[i].disabled = true;
    }
  }
});

whats.addEventListener("change", () => {
  if((whats.checked === true)||(email.checked === true)) {
    msg.classList.remove("disabled");
    for (i=0;i<msgInputs.length;i++){
      msgInputs[i].disabled = false;
    }
  } else {
    msg.classList.add("disabled");
    msg.disabled = true;
    for (i=0;i<msgInputs.length;i++){
      msgInputs[i].disabled = true;
    }
  }
});

select.addEventListener("change", () => {
  for (i = 0; i<3; i++){
    messages[i].classList.add("display-none");
    var input = messages[i].querySelector("textarea, select");
    input.required = false;
    input.value = null;
  }
  messages[select.value].classList.remove("display-none");
  var inputSelected = messages[select.value].querySelector("textarea, select");
  inputSelected.required = true;

  if (select.value == 2){
    whats.disabled = true;
  } else {
    whats.disabled = false;
  }
});

function limparInputContato(){
  var x = document.querySelector(".js--whatsapp");
  x.checked = false;
  x.dispatchEvent(new Event("change"));
  var y = document.querySelector(".js--telefone");
  y.checked = false;
  y.dispatchEvent(new Event("change"));
}

const mensagemSalvaSelect = document.querySelector(".js--mensagem-salva-select");
const mensagemSalvaDisplay = document.querySelector(".js--mensagem-salva-display");

mensagemSalvaSelect.addEventListener("change", () => {
  mensagemSalvaDisplay.value = mensagemSalvaSelect.value;
})

const statusSelect = document.querySelector(".js--status-select");
const statusDisplay = document.querySelector(".js--status-descricao");
var optionSelecionado = statusSelect.options[statusSelect.selectedIndex];

statusDisplay.innerHTML = optionSelecionado.dataset.descricao;

statusSelect.addEventListener("change", () => {
  var optionSelecionado = statusSelect.options[statusSelect.selectedIndex];
  statusDisplay.innerHTML = optionSelecionado.dataset.descricao;
});