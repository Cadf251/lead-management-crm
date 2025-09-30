document.addEventListener("DOMContentLoaded", function () {
  const equipeSelect = document.getElementById('equipeSelect');
  const usuarioSelect = document.getElementById('usuarioSelect');

  if (equipeSelect) {
    equipeSelect.addEventListener('change', function () {
      const equipeId = parseInt(this.value);

      usuarioSelect.innerHTML = '<option value="">Selecione...</option>';

      if (usuariosPorEquipe[equipeId]) {
        usuariosPorEquipe[equipeId].forEach(function (usuario) {
          const opt = document.createElement('option');
          opt.value = usuario.id;
          opt.textContent = usuario.nome;
          usuarioSelect.appendChild(opt);
        });
      }
    });
  }
});

function openLead(id){
  const params = new URLSearchParams(window.location.search);
  const selecionar = params.get("selecionar");

  window.location.href = "atendimento.php?atendimento_id="+id+"&selecionar="+selecionar;
}