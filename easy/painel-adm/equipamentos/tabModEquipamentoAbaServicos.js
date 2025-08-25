// equipamentos/tabModEquipamentoAbaServicos.js
// Aba "Serviços" do modal de Equipamentos
// - Lista serviços ativos (excluido <> 1) e marca os já vinculados à equipamento (executa=1)
// - Busca por nome do serviço
// - Ordenação por clique no TH via data-sort="num|a-z|data|bool" e data-sort-init="ASC|DESC"

(function () {
  // -------------------- helpers base --------------------

  const SAVE_URL = 'equipamentos/SModEquipamentoSalvarServicos.php';

  // helper POST JSON
  function postJSON(url, data) {
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    }).then(r => r.json());
  }

  // salva o toggle (otimista)
  function saveToggle(row, checked, cbEl, onDone) {
    const prev = Number(row.executa) === 1 ? 1 : 0;
    row.executa = checked ? 1 : 0; // otimista
    cbEl.disabled = true;

    postJSON(SAVE_URL, {
      id_equipamento: row.id_equipamento,
      id_servico: row.id_servico,
      executa: checked ? 1 : 0,
      id_link: row.id_link ?? null
    })
    .then(resp => {
      if (!resp || resp.ok !== true) {
        throw new Error(resp && resp.msg ? resp.msg : 'Falha ao salvar');
      }
      // atualiza id_link se veio
      if (typeof resp.id_link !== 'undefined') {
        row.id_link = resp.id_link;
      }
    })
    .catch(err => {
      // reverte em caso de erro
      row.executa = prev;
      cbEl.checked = !!prev;
      console.error('Erro ao salvar vínculo:', err);
      // opcional: toast/alert
      // alert('Não foi possível salvar. Tente novamente.');
    })
    .finally(() => {
      cbEl.disabled = false;
      if (typeof onDone === 'function') onDone();
    });
  }


  function $(sel, ctx = document) { return ctx.querySelector(sel); }
  function $all(sel, ctx = document) { return Array.from(ctx.querySelectorAll(sel)); }

  function normalize(str) {
    const s = (str ?? '') + '';
    try { return s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase(); }
    catch { return s.toLowerCase(); }
  }

  function markDirty(tr, dirty) {
    if (!tr) return;
    if (dirty) { tr.classList.add('row-dirty'); tr.dataset.dirty = '1'; }
    else { tr.classList.remove('row-dirty'); delete tr.dataset.dirty; }
  }

  function buildFotoSrcServico(f) {
    if (!f) return '../img/sem-imagem.svg';
    if (f.startsWith('http') || f.startsWith('data:') || f.includes('/')) return f;
    const base = (pastaFiles || pastaFiles)
      ? `../${(pastaFiles || pastaFiles)}/img/servicos/`
      : '../img/servicos/';
    return base + f;
  }

  function makeHidden(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value ?? '';
    return input;
  }
  function makeCheckbox({ name, checked, value = '1', onChange }) {
    const input = document.createElement('input');
    input.type = 'checkbox';
    input.name = name;
    input.value = value;
    input.checked = !!checked;
    if (onChange) input.addEventListener('change', onChange);
    return input;
  }

  // -------------------- módulo principal --------------------
  function TabelaModEquipamentoAbaServicos() {
    const container = document.querySelector('.table-containerServ');
    if (!container) return;

    const table = container.querySelector('#tabelaAbaServ');
    if (!table) return;

    const searchInput = container.querySelector('.searchBox');
    const tbody = table.querySelector('tbody') || table.createTBody();

    const idEquipamentoEl = document.getElementById('frm-id');
    const idEquipamento = idEquipamentoEl ? idEquipamentoEl.value : '';
    if (!idEquipamento) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Equipamento não informada.</td></tr>';
      return;
    }

    // estado
    let allRows = [];   // dataset completo vindo do back
    let viewRows = [];  // dataset filtrado
    let sortState = {   // estado de ordenação atual
      colIdx: null,     // índice do th
      type: null,       // "a-z" | "num" | "data" | "bool"
      asc: true         // true asc, false desc
    };

    // mapeamento fixo de colunas desta tabela
    // 0: foto, 1: servico, 2: categoria, 3: executa
    function getComparableValueFromRow(rowObj, colIdx, type) {
      switch (colIdx) {
        case 1: // servico
          return normalize(rowObj.servico ?? '');
        case 2: // categoria
          return normalize(rowObj.categoria ?? '');
        case 3: // executa (bool)
          return Number(rowObj.executa) === 1 ? 1 : 0;
        default: // foto ou outros
          if (type === 'num') return parseFloat(rowObj[colIdx] ?? 0) || 0;
          return normalize(String(rowObj[colIdx] ?? ''));
      }
    }

    function compareBy(colIdx, type, asc) {
      return (a, b) => {
        let va, vb;

        if (type === 'data') {
          // tenta parsear a partir de strings; se não tiver, fica 0
          va = Date.parse(a[colIdx]) || 0;
          vb = Date.parse(b[colIdx]) || 0;
        } else if (type === 'num') {
          va = parseFloat(getComparableValueFromRow(a, colIdx, type)) || 0;
          vb = parseFloat(getComparableValueFromRow(b, colIdx, type)) || 0;
        } else if (type === 'bool') {
          va = getComparableValueFromRow(a, colIdx, type); // 0/1
          vb = getComparableValueFromRow(b, colIdx, type);
        } else { // "a-z" | default
          va = getComparableValueFromRow(a, colIdx, type);
          vb = getComparableValueFromRow(b, colIdx, type);
        }

        if (va < vb) return asc ? -1 : 1;
        if (va > vb) return asc ? 1 : -1;
        return 0;
      };
    }

    function defaultSort(rows) {
      // fallback: executa DESC (checks primeiro) e, dentro, servico A-Z
      return rows.slice().sort((a, b) => {
        const ex = (Number(b.executa) - Number(a.executa)); // 1 antes de 0
        if (ex !== 0) return ex;
        const na = normalize(a.servico ?? '');
        const nb = normalize(b.servico ?? '');
        return na.localeCompare(nb, 'pt-BR', { sensitivity: 'base' });
      });
    }

    function applyCurrentSort() {
      if (sortState.colIdx == null) {
        viewRows = defaultSort(viewRows);
        return;
      }
      viewRows = viewRows.slice().sort(compareBy(sortState.colIdx, sortState.type, sortState.asc));
    }

    function clearSortIcons() {
      $all('thead th', table).forEach(th => th.classList.remove('sort-asc', 'sort-desc'));
    }
    function setSortIcon(th, asc) {
      th.classList.add(asc ? 'sort-asc' : 'sort-desc');
    }

    function renderRows(rows) {
      tbody.innerHTML = '';
      rows.forEach((row, i) => {
        const tr = document.createElement('tr');
        tr.dataset.index = String(i);

        // Foto
        const tdFoto = document.createElement('td');
        const img = document.createElement('img');
        img.src = buildFotoSrcServico(row.foto_servico);
        img.alt = 'Foto';
        Object.assign(img.style, { width: '40px', height: '40px', objectFit: 'cover', borderRadius: '50%' });
        tdFoto.appendChild(img);

        // Serviço + ids hidden
        const tdServico = document.createElement('td');
        tdServico.textContent = row.servico ?? '';
        tdServico.appendChild(makeHidden(`rows[${i}][id_equipamento]`, row.id_equipamento));
        tdServico.appendChild(makeHidden(`rows[${i}][id_servico]`, row.id_servico));
        tdServico.appendChild(makeHidden(`rows[${i}][id_link]`, row.id_link ?? ''));

        // Categoria (se usar; o TH pode estar oculto)
        const tdCategoria = document.createElement('td');
        tdCategoria.textContent = row.categoria ?? '';

        // Executa (usa esta equipamento)
        const tdExecuta = document.createElement('td');
        tdExecuta.appendChild(makeHidden(`rows[${i}][executa]`, '0'));
        
        
        
        const cb = makeCheckbox({
          name: `rows[${i}][executa]`,
          checked: Number(row.executa) === 1,
          value: '1',
          onChange: (ev) => {
            const checked = ev.target.checked;
            // marca sujo só para efeito visual (opcional)
            markDirty(tr, true);
            // salva e, ao terminar, refiltra/ordena e rerenderiza
            saveToggle(row, checked, ev.target, () => {
              markDirty(tr, false);
              refilterAndRender(); // mantém ordenação atual
            });
          }
        });
        tdExecuta.appendChild(cb);

        tr.appendChild(tdFoto);
        tr.appendChild(tdServico);
        tr.appendChild(tdCategoria);
        tr.appendChild(tdExecuta);
        tbody.appendChild(tr);
      });
    }

    function refilterAndRender() {
      const term = normalize(searchInput ? searchInput.value : '');
      viewRows = !term
        ? allRows.slice()
        : allRows.filter(r => normalize(r.servico).includes(term));

      applyCurrentSort();
      renderRows(viewRows);
    }

    // -------------------- sorting por TH --------------------
    function enableTableSorting() {
      const ths = $all('thead th', table);
      let initialApplied = false;

      ths.forEach((th, idx) => {
        const type = th.dataset.sort; // "a-z" | "num" | "data" | "bool"
        if (!type) return;

        // aplica sort inicial se este TH tiver data-sort-init e ainda não aplicou
        if (!initialApplied && th.dataset.sortInit) {
          initialApplied = true;
          sortState.colIdx = idx;
          sortState.type = type;
          sortState.asc = th.dataset.sortInit.toUpperCase() === 'ASC';
        }

        th.style.cursor = 'pointer';
        th.addEventListener('click', () => {
          // alterna se clicar na mesma coluna; se trocar de coluna, inicia ASC
          if (sortState.colIdx === idx) {
            sortState.asc = !sortState.asc;
          } else {
            sortState.colIdx = idx;
            sortState.type = type;
            sortState.asc = true;
          }
          // aplica, renderiza e ícones
          applyCurrentSort();
          renderRows(viewRows);
          clearSortIcons();
          setSortIcon(th, sortState.asc);
        });
      });

      // se nenhum TH definiu sort-init, usa ordenação padrão (executa DESC, serviço A-Z)
      if (!initialApplied && sortState.colIdx == null) {
        viewRows = defaultSort(viewRows);
        renderRows(viewRows);
        return;
      }

      // caso tenha sort-init, aplica e marca ícone
      if (sortState.colIdx != null) {
        applyCurrentSort();
        renderRows(viewRows);
        clearSortIcons();
        const thInit = ths[sortState.colIdx];
        if (thInit) setSortIcon(thInit, sortState.asc);
      }
    }

    // -------------------- listeners --------------------
    if (searchInput) searchInput.addEventListener('input', refilterAndRender);

    // -------------------- fetch inicial --------------------
    const fields = ['id_equipamento','id_servico','servico','foto_servico','categoria','id_link','executa'];
    const url = `equipamentos/SModEquipamentoTabServicos.php?id_equipamento=${encodeURIComponent(idEquipamento)}&fields=${encodeURIComponent(fields.join(','))}`;

    fetch(url)
      .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
      .then(data => {
        allRows = (data && Array.isArray(data.rows)) ? data.rows : [];
        viewRows = allRows.slice();
        // habilita ordenação e já aplica sort-init ou padrão
        enableTableSorting();
      })
      .catch(err => {
        console.error('Erro ao carregar serviços da equipamento:', err);
        tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Não foi possível carregar os serviços.</td></tr>';
      });
  }

  // -------------------- estilos visuais para ícones de sort --------------------
  (function injectSortStyles() {
    const css = `
      th.sort-asc::after { content: " \\2191"; font-size: .9em; }
      th.sort-desc::after { content: " \\2193"; font-size: .9em; }
    `;
    const st = document.createElement('style');
    st.textContent = css;
    document.head.appendChild(st);
  })();

  

  // exporta e auto-inicializa
  //window.TabelaModEquipamentoAbaServicos = TabelaModEquipamentoAbaServicos;
  let equipServicosLoaded = false;

  document.addEventListener('shown.bs.tab', (e) => {
    const target = e.target?.getAttribute('data-bs-target') || e.target?.getAttribute('href');
    if (target === '#aba-servicos') { // id da aba de serviços
      if (!equipServicosLoaded && tipo_cadastro!='novo') {
        equipServicosLoaded = true;
        TabelaModEquipamentoAbaServicos();
      }
    }
  });


})();
