
export function hamburguerControll(controll, dataset) {
  console.log("Called");
  var content = controll.nextElementSibling;

  if (content.classList.contains("is-active")) {
    content.classList.remove("is-active");
    controll.classList.remove("is-active");
  } else {
    content.classList.add("is-active");
    controll.classList.add("is-active");
  }
}