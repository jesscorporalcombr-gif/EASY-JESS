// js/chamaCliente.js

class ClientSearch {
  constructor(container, minChars = 4) {
    this.container      = container;
    this.searchEndpoint = './endPoints/clientes-busca.php';
    this.input          = container.querySelector('.client-search__input');
    this.hiddenId       = container.querySelector('.client-search__id');
    this.list           = container.querySelector('.client-search__list');
    this.icon           = container.querySelector('.client-search__icon');
    this.timer          = null;
    this.minChars       = minChars;   // agora 4
    this.results        = [];
    this.selectedIndex  = -1;
    this._bindEvents();
  }

  _bindEvents() {
    this.input.addEventListener('input', e => {
      clearTimeout(this.timer);
      this.timer = setTimeout(() => this._onInput(e.target.value), 300);
    });
    this.input.addEventListener('keydown', e => this._onKeydown(e));
  }

  _onInput(raw) {
    const term = raw.trim();
    this.hiddenId.value = '';
    this.hiddenId.dispatchEvent(new Event('input', { bubbles: true }));
    this.icon.className = 'bi client-search__icon bi-person-plus';

    if (term.length < this.minChars) {
      return this._clearList();
    }

    this._fetchMatches(term);
  }

  async _fetchMatches(term) {
    try {
      const res = await fetch(`${this.searchEndpoint}?q=${encodeURIComponent(term)}`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      this.results = await res.json();
      this.selectedIndex = -1;
      this._renderList();
    } catch {
      console.error('Erro ao buscar clientes');
      this._clearList();
    }
  }

  _renderList() {
    this.list.innerHTML = '';
    this.results.forEach((cli, i) => {
      const li = document.createElement('li');
      li.textContent = cli.nome;
      li.addEventListener('click', () => this._choose(cli));
      this.list.appendChild(li);
    });
    this.list.style.display = this.results.length ? 'block' : 'none';
  }

  _onKeydown(e) {
    const items = Array.from(this.list.querySelectorAll('li'));
    if (!items.length) return;
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      this.selectedIndex = e.key === 'ArrowDown'
        ? Math.min(this.selectedIndex + 1, items.length - 1)
        : Math.max(this.selectedIndex - 1, 0);
      this._highlight(items);
      e.preventDefault();
    }
    if (e.key === 'Enter') {
      if (this.selectedIndex >= 0) {
        this._choose(this.results[this.selectedIndex]);
      } else if (this.results.length === 1) {
        this._choose(this.results[0]);
      }
      e.preventDefault();
    }
  }

  _highlight(items) {
    items.forEach((li, i) => li.classList.toggle('selecionado', i === this.selectedIndex));
    items[this.selectedIndex]?.scrollIntoView({ block: 'nearest' });
  }

  _choose(cli) {
    this.input.value    = cli.nome;
    this.hiddenId.value = cli.id;
    this.hiddenId.dispatchEvent(new Event('input', { bubbles: true }));
    this.icon.className = 'bi client-search__icon bi-eye';
    this._clearList();
    this.hiddenId.dispatchEvent(new CustomEvent('clienteSelecionado', {
      detail: cli,
      bubbles: true
    }));
  }

  _clearList() {
    this.list.innerHTML = '';
    this.list.style.display = 'none';
  }
}

// controle de instâncias
const instances = new WeakMap();

function initClientSearch(container) {
  if (!instances.has(container)) {
    instances.set(container, new ClientSearch(container));
  }
}

// Setup: inicializa existentes e futuros via focus
function setupClientSearch() {
  // init de quem já existe
  document.querySelectorAll('.client-search').forEach(initClientSearch);

  // quando qualquer input for focado dentro de .client-search, inicializa se ainda não tiver
  document.addEventListener('focusin', event => {
    const container = event.target.closest('.client-search');
    if (container) initClientSearch(container);
  });
}

document.addEventListener('DOMContentLoaded', setupClientSearch);

