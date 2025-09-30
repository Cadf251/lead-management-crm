const nav = document.querySelector(".js--nav");
const main = document.querySelector(".js--main");

var navClass = nav.classList;
var mainClass = main.classList;

function resizeNav(){
  const estaExpandido = localStorage.getItem('navExpandida') === 'true';

  changeClasses(!estaExpandido, navClass, mainClass);
  localStorage.setItem("navExpandida", !estaExpandido);
}

function ajustNav(){
  const estaExpandido = localStorage.getItem('navExpandida') === 'true';

  changeClasses(estaExpandido, navClass, mainClass);

  if (navClass.contains("nav--preload"))
    navClass.remove("nav--preload");
}

function changeClasses(boolean, navClass, mainClass){
  if (boolean) {
    navClass.remove("nav--recolhido");
    navClass.add("nav--extendido");
    mainClass.remove("main--recolhido");
    mainClass.add("main--extendido");
  } else {
    navClass.remove("nav--extendido");
    navClass.add("nav--recolhido");
    mainClass.remove("main--extendido");
    mainClass.add("main--recolhido");
  }
}

ajustNav();