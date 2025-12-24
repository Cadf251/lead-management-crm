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