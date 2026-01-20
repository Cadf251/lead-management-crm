// Função base para evitar repetição de código
async function baseRequest(url, config, then) {
  try {
    const response = await fetch(url, config);
    const data = await response.json();

    if (then && typeof then === "function") {
      then(data);
    }
    return data;
  } catch (error) {
    console.error("Erro na requisição AJAX:", error);
  }
}

export function getRequest(url, then = null) {
  return baseRequest(url, {
    method: 'GET',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  }, then);
}

export function postRequest(url, params = "", then = null) {
  return baseRequest(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: params
  }, then);
}