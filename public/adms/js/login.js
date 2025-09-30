window.onload = function() {
  const codigoSalvo = localStorage.getItem('codigoEmpresa');
  if (codigoSalvo !== null) {
    document.getElementById('codigoEmpresa').value = codigoSalvo;
  }
};