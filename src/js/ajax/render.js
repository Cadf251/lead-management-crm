export function renderizar(html, className) {
  var x = document.querySelector(className);
  x.innerHTML = html;
}

export function removeCard(className) {
  var x = document.querySelector(className);
  x.remove();
}