function iniciarTabelaPagAtendimentos () {
  // Suporta várias tabelas; cada uma deve estar dentro de .table-container
  const tableContainers = document.querySelectorAll('.table-container');

  tableContainers.forEach(container => {
    const dataTableElement   = container.querySelector('.tablePagAtendimentos');
    if (!dataTableElement) return;

    const searchInput        = container.querySelector('.searchBox');
    const rowsPerPageSelect  = container.querySelector('.rowsPerPage');
    const paginationDiv      = container.querySelector('.pagination');
    const infoRangeElement   = container.querySelector('.info-range');
    const inputIni           = container.querySelector('#dataInicial');
    const inputFim           = container.querySelector('#dataFinal');
    const filterButtons      = container.querySelectorAll('.filter-button');

    // Estado
    let activeConditions = {};
    let allData = [];
    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect?.value, 10) || 10;
    let currentSort = { field: 'atendimento_id', order: 'ASC' }; // seguro por padrão

    // --- Helpers de texto/data ---
    function normalizeText(text) {
      const str = (text == null) ? '' : String(text);
      try {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
      } catch {
        return str.toLowerCase();
      }
    }

    function parseDateString(dateStr) {
      if (!dateStr) return 0;
      const isoTs = Date.parse(dateStr);
      if (!isNaN(isoTs)) return isoTs;
      const parts = String(dateStr).split('/');
      if (parts.length === 3) {
        const [d, m, y] = parts;
        const reformatted = `${y}-${String(m).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const ts = Date.parse(reformatted);
        return isNaN(ts) ? 0 : ts;
      }
      return 0;
    }

    function daysUntilNextBirthday(dateStr) {
      if (!dateStr) return Infinity;
      const parts = dateStr.split('-').map(Number);
      if (parts.length !== 3) return Infinity;
      const [, month, day] = parts;
      const today = new Date();
      const thisYear = today.getFullYear();
      let next = new Date(thisYear, month - 1, day);
      if (next < today) next = new Date(thisYear + 1, month - 1, day);
      const diffMs = next - today;
      return Math.ceil(diffMs / (1000 * 60 * 60 * 24));
    }

    // fallback simples se não houver global
    function formatarDataBrSafe(iso) {
      if (typeof formatarDataBr === 'function') return formatarDataBr(iso);
      if (!iso) return '';
      const ts = parseDateString(iso);
      if (!ts) return iso;
      const d = new Date(ts);
      const dd = String(d.getDate()).padStart(2, '0');
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      const yy = d.getFullYear();
      return `${dd}/${mm}/${yy}`;
    }

    // --- Filtros do atributo data-filtro ---
    function updateFiltersFromAttribute() {
      const dataFiltroAttr = dataTableElement.getAttribute('data-filtro');
      if (!dataFiltroAttr) { activeConditions = {}; return; }
      try {
        activeConditions = JSON.parse(dataFiltroAttr);
      } catch (e) {
        console.error('Erro ao parsear data-filtro:', e);
        activeConditions = {};
      }
    }
    updateFiltersFromAttribute();

    // --- Datas ---
    function getDefaultRange() {
      const hoje = new Date();
      const fim = hoje.toISOString().slice(0, 10);
      const iniDate = new Date(hoje);
      iniDate.setDate(iniDate.getDate() - 10);
      const ini = iniDate.toISOString().slice(0, 10);
      return { ini, fim };
    }

    function getDateRange() {
      const { ini: defIni, fim: defFim } = getDefaultRange();
      const ini = (inputIni?.value || '').trim() || defIni;
      const fim = (inputFim?.value || '').trim() || defFim;

      const rx = /^\d{4}-\d{2}-\d{2}$/;
      const iniOk = rx.test(ini) && !isNaN(Date.parse(ini));
      const fimOk = rx.test(fim) && !isNaN(Date.parse(fim));
      if (!iniOk || !fimOk) return { ini: defIni, fim: defFim };

      if (Date.parse(ini) > Date.parse(fim)) return { ini: fim, fim: ini };
      return { ini, fim };
    }

    // --- URL e carga remota ---
    function buildUrl() {
      const thElements = dataTableElement.querySelectorAll('thead th[data-field]');
      const fieldsArray = Array.from(thElements).map(th => th.getAttribute('data-field'));
      const fields = fieldsArray.join(',');

      let url = `tecnico/searchModAtendimentos.php?table=${encodeURIComponent(dataTableElement.getAttribute('data-table'))}&fields=${encodeURIComponent(fields)}&all=true`;

      const { ini, fim } = getDateRange();
      url += `&data_inicial=${encodeURIComponent(ini)}&data_final=${encodeURIComponent(fim)}`;

      Object.entries(activeConditions).forEach(([key, value]) => {
        url += `&${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
      });

      return url;
    }

    function loadRemote() {
      const url = buildUrl();
      return fetch(url)
        .then(resp => {
          if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
          return resp.json();
        })
        .then(data => {
          allData = Array.isArray(data.rows) ? data.rows : [];
          return allData;
        })
        .catch(err => {
          console.error('Erro carregando do backend:', err);
          allData = [];
          return allData;
        });
    }

    // Expor para debug (opcional)
    container._loadRemoteAtend = loadRemote;

    // --- Renderização ---
    function displayRows(rows) {
      const tbody = dataTableElement.querySelector('tbody');
      tbody.innerHTML = '';

      rows.forEach(row => {
        const tr = document.createElement('tr');
        const ths = dataTableElement.querySelectorAll('thead th');

        ths.forEach(th => {
          const td = document.createElement('td');

          const tdClasses = th.getAttribute('data-classe-td');
          if (tdClasses) tdClasses.split(" ").forEach(cls => td.classList.add(cls));

          const trClasses = th.getAttribute('data-classe-tr');
          if (trClasses) trClasses.split(" ").forEach(cls => tr.classList.add(cls));

          const field = th.getAttribute('data-field');
          const sortAttr = th.getAttribute('data-sort');

          let cellValue = '';
          if (field) cellValue = (row[field] !== undefined && row[field] !== null) ? row[field] : '';

          // Modal
          if (th.hasAttribute('data-modal')) {
            td.addEventListener('click', () => {
              const modalName = th.getAttribute('data-modal');
              // dataTipo não é usado aqui; adapte se necessário
              abrirModal(modalName, row['atendimento_id'], undefined);
            });
          }

          // Foto
          if (th.hasAttribute('data-foto')) {
            const foto = document.createElement('img');
            let basePath, imgPath;
            if (cellValue !== '') {
              basePath = `../${pastaFiles}/img/clientes/`;
              imgPath = cellValue;
            } else {
              basePath = '../img/';
              imgPath = 'sem-foto.svg';
            }
            foto.src = basePath + imgPath;
            foto.style.width = '40px';
            foto.style.height = '40px';
            foto.style.objectFit = 'cover';
            foto.style.borderRadius = '50%';
            td.appendChild(foto);

          // Status
          } else if (field === 'status' && cellValue) {
            const status = String(cellValue);
            const span = document.createElement('span');
            let cls = 'status-label';
            switch (status) {
              case 'Em Atendimento':
                cls += ' status-atendimento';
                break;
              case 'Finalizado':
                cls += ' status-finalizado';
                break;
              case 'Atendimento Concluido':
                cls += ' status-concluido';
                break;



              default:
                cls += ' status-outro';
            }
            span.className = cls;
            span.textContent = status;
            td.appendChild(span);

          // Data
          } else if (sortAttr === 'data') {
            td.textContent = formatarDataBrSafe(cellValue);

          // Padrão
          } else {
            td.textContent = cellValue;
          }

          if (th.hasAttribute('hidden')) td.style.display = 'none';

          if (td.classList.contains('numero')) {
            td.setAttribute('data-raw-value', cellValue);
          }
          if (td.classList.contains('posNeg')) {
            td.classList.add((parseFloat(cellValue) || 0) < 0 ? 'num-negativo' : 'num-positivo');
          }

          tr.appendChild(td);
        });

        tbody.appendChild(tr);
      });
    }

    // --- Ordenação ---
    function compareNumbers(a, b, order) {
      const numA = parseFloat(a) || 0;
      const numB = parseFloat(b) || 0;
      return order === 'ASC' ? numA - numB : numB - numA;
    }

    function compareStrings(a, b, order) {
      return order === 'ASC' ? a.localeCompare(b) : b.localeCompare(a);
    }

    function compareDates(a, b, order) {
      const tA = parseDateString(a);
      const tB = parseDateString(b);
      return order === 'ASC' ? tA - tB : tB - tA;
    }

    function compareAniversario(a, b, order) {
      const daysA = daysUntilNextBirthday(a);
      const daysB = daysUntilNextBirthday(b);
      return order === 'ASC' ? daysA - daysB : daysB - daysA;
    }

    const thWithInitSort = dataTableElement.querySelector('thead th[data-sort-init]');
    if (thWithInitSort) {
      currentSort.field = thWithInitSort.getAttribute('data-field');
      currentSort.order = thWithInitSort.getAttribute('data-sort-init').toUpperCase();
    } else {
      const firstTh = dataTableElement.querySelector('thead th[data-field]');
      if (firstTh) currentSort.field = firstTh.getAttribute('data-field');
    }

    dataTableElement.querySelectorAll('thead th[data-field][data-sort]').forEach(th => {
      th.addEventListener('click', () => {
        const field = th.dataset.field;
        if (currentSort.field === field) {
          currentSort.order = currentSort.order === 'ASC' ? 'DESC' : 'ASC';
        } else {
          currentSort.field = field;
          currentSort.order = 'ASC';
        }
        fetchData();
      });
    });

    // --- Busca/Paginação ---
    if (searchInput) {
      searchInput.addEventListener('input', () => { currentPage = 1; fetchData(); });
    }
    if (rowsPerPageSelect) {
      rowsPerPageSelect.addEventListener('change', () => {
        rowsPerPage = parseInt(rowsPerPageSelect.value, 10) || 10;
        currentPage = 1;
        fetchData();
      });
    }

    if (filterButtons.length) {
      filterButtons.forEach(button => {
        if (button.classList.contains('active')) {
          const field = button.getAttribute('data-field-cond');
          const value = button.getAttribute('data-cond-search');
          activeConditions[field] = value;
        }
        button.addEventListener('click', function() {
          const field = this.getAttribute('data-field-cond');
          const value = this.getAttribute('data-cond-search');
          if (this.classList.contains('active')) {
            delete activeConditions[field];
            this.classList.remove('active');
          } else {
            activeConditions[field] = value;
            this.classList.add('active');
          }
          // Ao mudar filtro, recarrega do backend
          currentPage = 1;
          loadRemote().then(() => fetchData());
        });
      });
    }

    function createPaginationButton(label, handler, disabled) {
      const btn = document.createElement('button');
      btn.textContent = label;
      btn.disabled = !!disabled;
      btn.classList.add('page-nav-button');
      btn.onclick = handler;
      return btn;
    }

    function setupPagination(totalRecords) {
      paginationDiv.innerHTML = '';
      const totalPages = Math.max(1, Math.ceil(totalRecords / rowsPerPage));

      // << (primeira)
      paginationDiv.appendChild(
        createPaginationButton('<<', () => { if (currentPage !== 1) { currentPage = 1; fetchData(); } }, currentPage === 1)
      );
      // < (anterior)
      paginationDiv.appendChild(
        createPaginationButton('<', () => { if (currentPage > 1) { currentPage -= 1; fetchData(); } }, currentPage === 1)
      );

      let startPage = Math.max(1, currentPage - 2);
      let endPage = Math.min(totalPages, startPage + 4);
      for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.textContent = String(i);
        btn.classList.add('page-number-button');
        if (i === currentPage) btn.classList.add('active-page');
        btn.onclick = () => { currentPage = i; fetchData(); };
        paginationDiv.appendChild(btn);
      }

      // > (próxima)
      paginationDiv.appendChild(
        createPaginationButton('>', () => { if (currentPage < totalPages) { currentPage += 1; fetchData(); } }, currentPage >= totalPages)
      );
      // >> (última)
      paginationDiv.appendChild(
        createPaginationButton('>>', () => { if (currentPage !== totalPages) { currentPage = totalPages; fetchData(); } }, currentPage >= totalPages)
      );
    }

    function updateInfoRange(currentPage, rowsPerPage, totalRows) {
      if (!infoRangeElement) return;
      const startIndex = totalRows === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
      const endIndex = Math.min(startIndex + rowsPerPage - 1, totalRows);
      infoRangeElement.textContent = `Exibindo de ${startIndex} a ${endIndex} de um total de ${totalRows} registros`;
    }

    function fetchData() {
      const searchTerm = normalizeText(searchInput?.value || '');

      const fieldsToSearch = Array.from(
        dataTableElement.querySelectorAll('thead th[data-field]')
      ).map(th => th.getAttribute('data-field'));

      let filteredData = allData.filter(row => {
        return fieldsToSearch.some(field => {
          const value = row[field] == null ? '' : String(row[field]);
          return normalizeText(value).includes(searchTerm);
        });
      });

      const thSort = dataTableElement.querySelector(`thead th[data-field="${currentSort.field}"]`);
      const sortType = thSort ? thSort.getAttribute('data-sort') : 'a-z';

      filteredData.sort((a, b) => {
        const valA = a[currentSort.field];
        const valB = b[currentSort.field];
        if (sortType === 'num')   return compareNumbers(valA, valB, currentSort.order);
        if (sortType === 'data')  return compareDates(valA, valB, currentSort.order);
        if (sortType === 'aniversario') return compareAniversario(valA, valB, currentSort.order);
        return compareStrings(
          normalizeText(valA == null ? '' : String(valA)),
          normalizeText(valB == null ? '' : String(valB)),
          currentSort.order
        );
      });

      const totalRecords = filteredData.length;
      const startIdx = (currentPage - 1) * rowsPerPage;
      const paginatedData = filteredData.slice(startIdx, startIdx + rowsPerPage);

      displayRows(paginatedData);
      setupPagination(totalRecords);
      updateInfoRange(currentPage, rowsPerPage, totalRecords);
    }

    // --- Observer para mudanças no data-filtro ---
    const observer = new MutationObserver(mutations => {
      for (const mutation of mutations) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'data-filtro') {
          updateFiltersFromAttribute();
          currentPage = 1;
          loadRemote().then(() => fetchData());
        }
      }
    });
    observer.observe(dataTableElement, { attributes: true, attributeFilter: ['data-filtro'] });

    // --- Mudança das datas ---
    function onDateChange() {
      currentPage = 1;
      loadRemote().then(() => fetchData());
    }
    if (inputIni) inputIni.addEventListener('change', onDateChange);
    if (inputFim) inputFim.addEventListener('change', onDateChange);

    // --- Carga inicial ---
    loadRemote()
      .then(() => fetchData())
      .catch(error => console.error('Error loading initial data:', error));
  });
}

iniciarTabelaPagAtendimentos();
