export function postRequest(url, params = "", then = null) {
  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: params
  })
    .then(res => res.json())
    .then(data => {
      console.log(data);
      if ((then !== null) && (typeof then === "function")) {
        then(data);
      }
      return data;
    });
}

