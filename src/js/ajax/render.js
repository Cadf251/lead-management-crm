export function renderizar(html, className) {
  var x = document.querySelector(className);
  if (x !== null ) x.outerHTML = html;
}

export function removeCard(className) {
  var x = document.querySelector(className);
  if (x !== null ) x.remove();
}

export function append(html, className) {
  var x = document.querySelector(className);
  x.insertAdjacentHTML('beforeend', html);
}

export function setLoading(className) {
  var x = document.querySelector(className);
  if (x !== null ) x.classList.add("is-loading");
}

export function finishLoading(className) {
  var x = document.querySelector(className);
  if (x !== null ) x.classList.remove("is-loading");
}