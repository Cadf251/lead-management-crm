const equipes = document.querySelectorAll(".js--equipe-box");
const equipesControl = document.querySelector(".js--equipes-control");
const btns = equipesControl.querySelectorAll("button");
eqIndex = -1;

nextEquipe(eqIndex, "next");

function nextEquipe(idx, direction){
  if (idx !== -1){
    if (equipes[idx].classList.contains("equipe-box--ativo"))
    equipes[idx].classList.remove("equipe-box--ativo");
  }

  if (direction === "next"){
    if ((idx + 1) === equipes.length){
      idx = 0;
    } else {
      idx++;
    }
  } else if (direction === "previous"){
    if (idx === 0){
      idx = equipes.length - 1;
    } else {
      idx--;
    }
  }
  
  equipes[idx].classList.add("equipe-box--ativo");

  btns[0].setAttribute("onclick", "nextEquipe("+idx+", 'previous')");
  btns[1].setAttribute("onclick", "nextEquipe("+idx+", 'next')");
  return;
}

const selects = document.querySelectorAll(".js--origem-input");

selects.forEach(el => {
  el.addEventListener("change", () => {
    var parent = el.parentElement;
    var grandParent = parent.parentElement;
    var graficos = grandParent.querySelectorAll(".js--graficos");

    graficos.forEach(el => {
      if (!el.classList.contains("display-none"))
        el.classList.add("display-none");
    });
    var target = graficos[el.value];

    target.classList.remove("display-none");
  });
});