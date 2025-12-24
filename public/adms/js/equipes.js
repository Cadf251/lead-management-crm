window.onload = () => {
  const selectElement = document.querySelectorAll('.js--select');
  const saveElement = document.querySelectorAll('.js--salvar');
  
  const selectArray = Array.from(selectElement);
  
  selectArray.forEach(select => {
    const originalValue = select.value;
    select.addEventListener('change', () => {
      const i = selectArray.indexOf(select);
      console.log(i);
      if (select.value !== originalValue) {
        saveElement[i].classList.add('small-btn--normal');
        saveElement[i].classList.remove('small-btn--gray');
        saveElement[i].disabled = false;
      } else {
        saveElement[i].classList.add('small-btn--gray');
        saveElement[i].classList.remove('small-btn--normal'); 
        saveElement[i].disabled = true;
      }
    });
  });
};