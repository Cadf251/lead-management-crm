// ui/floatingaction.js
export const FloatingAction = {
  container: null,

  init() {
    if (!this.container) {
      const html = `
        <div class="floating-action js--floating-container">
          <div class="floating-action__content js--floating-content"></div>
        </div>`;
      document.body.insertAdjacentHTML('beforeend', html);
      this.container = document.querySelector('.js--floating-container');

      // Mantemos apenas os eventos de fechar (clique fora e ESC)
      this.bindCloseEvents();
    }
  },

  bindCloseEvents() {
    document.addEventListener('mousedown', (e) => {
      if (this.container?.classList.contains('is-active') &&
        !this.container.contains(e.target)) {
        this.close();
      }
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') this.close();
    });
  },

  // Agora o 'open' recebe as coordenadas e o ID para o teste
  open(trigger, floatingId) {
    this.init(); // Garante que o container existe

    const rect = trigger.getBoundingClientRect();
        
    // Coordenadas baseadas no botão
    // Podemos usar o topo/esquerda do botão ou o centro dele.
    // Vou usar o X inicial e o Y final do botão para ele aparecer logo abaixo.
    const x = rect.left;
    const y = rect.top + rect.height; // Aparece logo abaixo do botão
    
    console.log("Floating Action disparado!");
    console.log("ID solicitado:", floatingId);
    console.log(`Coordenadas: X:${x} Y:${y}`);

    const content = this.container.querySelector('.js--floating-content');

    // Simulação do seu loading cinza
    content.innerHTML = '<div class="floating-loader">Simulando Loading...</div>';
    this.container.classList.add("is-loading");
    this.container.classList.add('is-active');
    
    this.setPosition(x, y);
  },

  fill(html) {
    var content = this.container.querySelector(".js--floating-content");
    this.container.classList.remove("is-loading");
    content.innerHTML = html;
  },

  setPosition(x, y) {
    const offset = 10;
    const viewportWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    const half = viewportWidth / 2;

    if (x < half) {
      this.container.style.left = `${x + offset}px`;
    } else {
      console.log(this.container.offsetWidth);
      this.container.style.left = `${x - this.container.offsetWidth - offset}px`;
    }
    this.container.style.top = `${y - offset}px`;
  },

  close() {
    if (this.container) {
      this.container.classList.remove('is-active');
      this.container.querySelector('.js--floating-content').innerHTML = '';
    }
  }
};