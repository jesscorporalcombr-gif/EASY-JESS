// salas/tabModSalaAbaServicos.js
// Carrega SOMENTE no 1º clique da aba "Serviços" e não dispara sem id de sala.

(function () {
  const modal = document.getElementById('modalCadSala');
  if (!modal) return;

  // evita empilhar listeners ao reabrir
  if (modal.__salaServCtl && typeof modal.__salaServCtl.abort === 'function') {
    try { modal.__salaServCtl.abort(); } catch(_) {}
  }
  const ctl = new AbortController();
  modal.__salaServCtl = ctl;
  const on = (el, ev, fn, opts) => { if (el) el.addEventListener(ev, fn, { ...(opts||{}), signal: ctl.signal }); };

  const pane = document.querySelector('#aba-servicos'); // conteúdo da aba
  if (!pane) return;

  const getSalaId = () =>
    (document.querySelector('#formCadSala input[name="id"]')?.value ||
     document.getElementById('frm-id')?.value || '').trim();

  // estilos (uma vez só)
  if (!document.getElementById('_serv_sort_css')) {
    const st = document.createElement('style');
    st.id = '_serv_sort_css';
    st.textContent = `
      th.sort-asc::after { content:" \\2191"; font-size:.9em; }
      th.sort-desc::after{ content:" \\2193"; font-size:.9em; }
      tr.row-dirty{ background:#fff8e1; }
    `;
    document.head.appendChild(st);
  }

  // ---------- módulo (roda apenas 1x por abertura/aba) ----------
  function initTabelaServicos() {
    const container = pane.querySelector('.table-containerServ');
    if (!container) return;

    const table  = container.querySelector('#tabelaAbaServ');
    const tbody  = table?.querySelector('tbody') || table?.createTBody();
    const search = container.querySelector('.searchBox');

    const idSala = getSalaId();
    if (!idSala) {
      if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Salve a sala para vincular serviços.</td></tr>';
      return;
    }

    // helpers
    const $all = (sel, ctx=document) => Array.from((ctx||document).querySelectorAll(sel));
    const normalize = (s)=> {
      s = (s ?? '') + '';
      try { return s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase(); }
      catch { return s.toLowerCase(); }
    };
    const markDirty = (tr, dirty)=>{
      if (!tr) return;
      if (dirty){ tr.classList.add('row-dirty'); tr.dataset.dirty='1'; }
      else { tr.classList.remove('row-dirty'); delete tr.dataset.dirty; }
    };
    const buildFotoSrcServico = (f)=>{
      if (!f) return '../img/sem-imagem.svg';
      if (f.startsWith('http') || f.startsWith('data:') || f.includes('/')) return f;
      const base = (typeof pastaFiles !== 'undefined' && pastaFiles)
        ? `../${pastaFiles}/img/servicos/` : '../img/servicos/';
      return base + f;
    };
    const makeHidden = (name, value)=>{
      const input = document.createElement('input');
      input.type = 'hidden'; input.name = name; input.value = value ?? ''; return input;
    };
    const makeCheckbox = ({ name, checked, value='1', onChange })=>{
      const input = document.createElement('input');
      input.type = 'checkbox'; input.name = name; input.value = value; input.checked = !!checked;
      if (onChange) on(input, 'change', onChange);
      return input;
    };

    // ajax save toggle (otimista)
    const SAVE_URL = 'salas/SModSalaSalvarServicos.php';
    const postJSON = (url, data)=>
      fetch(url, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data), signal: ctl.signal })
        .then(r=>r.json());

    function saveToggle(row, checked, cbEl, onDone) {
      const prev = Number(row.executa) === 1 ? 1 : 0;
      row.executa = checked ? 1 : 0;
      cbEl.disabled = true;

      postJSON(SAVE_URL, {
        id_sala: row.id_sala,
        id_servico: row.id_servico,
        executa: checked ? 1 : 0,
        id_link: row.id_link ?? null
      })
      .then(resp=>{
        if (!resp || resp.ok !== true) throw new Error(resp?.msg || 'Falha ao salvar');
        if (typeof resp.id_link !== 'undefined') row.id_link = resp.id_link;
      })
      .catch(err=>{
        row.executa = prev; cbEl.checked = !!prev;
        console.error('Erro ao salvar vínculo:', err);
      })
      .finally(()=>{
        cbEl.disabled = false;
        if (typeof onDone === 'function') onDone();
      });
    }

    // estado
    let allRows = [];
    let viewRows = [];
    let sortState = { colIdx:null, type:null, asc:true }; // 0 foto, 1 servico, 2 categoria, 3 executa

    const getComparable = (row, idx, type)=>{
      switch(idx){
        case 1: return normalize(row.servico ?? '');
        case 2: return normalize(row.categoria ?? '');
        case 3: return Number(row.executa) === 1 ? 1 : 0;
        default:
          if (type === 'num') return parseFloat(row[idx] ?? 0) || 0;
          return normalize(String(row[idx] ?? ''));
      }
    };
    const compareBy = (colIdx, type, asc)=>(a,b)=>{
      let va, vb;
      if (type === 'data'){ va = Date.parse(a[colIdx])||0; vb = Date.parse(b[colIdx])||0; }
      else if (type === 'num'){ va = +getComparable(a,colIdx,type)||0; vb = +getComparable(b,colIdx,type)||0; }
      else if (type === 'bool'){ va = getComparable(a,colIdx,type); vb = getComparable(b,colIdx,type); }
      else { va = getComparable(a,colIdx,type); vb = getComparable(b,colIdx,type); }
      if (va < vb) return asc ? -1 : 1;
      if (va > vb) return asc ? 1 : -1;
      return 0;
    };
    const defaultSort = (rows)=>
      rows.slice().sort((a,b)=>{
        const ex = (Number(b.executa) - Number(a.executa)); // executa=1 primeiro
        if (ex !== 0) return ex;
        return normalize(a.servico||'').localeCompare(normalize(b.servico||''), 'pt-BR', {sensitivity:'base'});
      });

    function applyCurrentSort(){
      if (sortState.colIdx == null) { viewRows = defaultSort(viewRows); return; }
      viewRows = viewRows.slice().sort(compareBy(sortState.colIdx, sortState.type, sortState.asc));
    }
    function clearSortIcons(){ $all('thead th', table).forEach(th=>th.classList.remove('sort-asc','sort-desc')); }
    function setSortIcon(th, asc){ th.classList.add(asc ? 'sort-asc':'sort-desc'); }

    function renderRows(rows){
      if (!tbody) return;
      tbody.innerHTML = '';
      rows.forEach((row, i)=>{
        const tr = document.createElement('tr');
        tr.dataset.index = String(i);

        // Foto
        const tdFoto = document.createElement('td');
        const img = document.createElement('img');
        img.src = buildFotoSrcServico(row.foto_servico);
        img.alt = 'Foto';
        Object.assign(img.style, { width:'40px', height:'40px', objectFit:'cover', borderRadius:'50%' });
        tdFoto.appendChild(img);

        // Serviço (+ hiddens)
        const tdServico = document.createElement('td');
        tdServico.textContent = row.servico ?? '';
        tdServico.appendChild(makeHidden(`rows[${i}][id_sala]`,    row.id_sala));
        tdServico.appendChild(makeHidden(`rows[${i}][id_servico]`, row.id_servico));
        tdServico.appendChild(makeHidden(`rows[${i}][id_link]`,    row.id_link ?? ''));

        // Categoria
        const tdCategoria = document.createElement('td');
        tdCategoria.textContent = row.categoria ?? '';

        // Executa
        const tdExecuta = document.createElement('td');
        tdExecuta.appendChild(makeHidden(`rows[${i}][executa]`, '0'));
        const cb = makeCheckbox({
          name: `rows[${i}][executa]`,
          checked: Number(row.executa) === 1,
          value: '1',
          onChange: (ev)=>{
            const checked = ev.target.checked;
            markDirty(tr, true);
            saveToggle(row, checked, ev.target, ()=>{
              markDirty(tr, false);
              refilterAndRender();
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

    function refilterAndRender(){
      const term = normalize(search ? search.value : '');
      viewRows = !term ? allRows.slice() : allRows.filter(r => normalize(r.servico).includes(term));
      applyCurrentSort();
      renderRows(viewRows);
    }

    function enableTableSorting(){
      const ths = $all('thead th', table);
      let initApplied = false;
      ths.forEach((th, idx)=>{
        const type = th.dataset.sort;
        if (!type) return;

        if (!initApplied && th.dataset.sortInit){
          initApplied = true;
          sortState.colIdx = idx;
          sortState.type   = type;
          sortState.asc    = th.dataset.sortInit.toUpperCase() === 'ASC';
        }
        th.style.cursor = 'pointer';
        on(th, 'click', ()=>{
          if (sortState.colIdx === idx) sortState.asc = !sortState.asc;
          else { sortState.colIdx = idx; sortState.type = type; sortState.asc = true; }
          applyCurrentSort(); renderRows(viewRows);
          clearSortIcons(); setSortIcon(th, sortState.asc);
        });
      });

      if (!initApplied && sortState.colIdx == null) {
        viewRows = defaultSort(viewRows); renderRows(viewRows); return;
      }
      if (sortState.colIdx != null) {
        applyCurrentSort(); renderRows(viewRows);
        clearSortIcons(); const thInit = ths[sortState.colIdx]; if (thInit) setSortIcon(thInit, sortState.asc);
      }
    }

    // fetch inicial (SÓ aqui, depois do clique)
    const fields = ['id_sala','id_servico','servico','foto_servico','categoria','id_link','executa'];
    const url = `salas/SModSalaTabServicos.php?id_sala=${encodeURIComponent(idSala)}&fields=${encodeURIComponent(fields.join(','))}`;

    fetch(url, { credentials: 'same-origin', cache: 'no-store', signal: ctl.signal })
      .then(r=>{ if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
      .then(data=>{
        allRows = Array.isArray(data?.rows) ? data.rows : [];
        viewRows = allRows.slice();
        enableTableSorting(); // já aplica sort init ou padrão
        on(search, 'input', refilterAndRender);
      })
      .catch(err=>{
        if (err.name === 'AbortError') return;
        console.error('Erro ao carregar serviços da sala:', err);
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Não foi possível carregar os serviços.</td></tr>';
      });
  }

  // --- inicializa somente na 1ª exibição da aba
  function armTabInitOnce(){
    const mark = 'sServInit';
    on(modal, 'shown.bs.tab', (e)=>{
      const target = e.target?.getAttribute('data-bs-target') || e.target?.getAttribute('href') || '';
      if (target === '#aba-servicos' || target.endsWith('#aba-servicos')) {
        if (pane.dataset[mark] === '1') return;
        pane.dataset[mark] = '1';
        initTabelaServicos();
      }
    });

    // se já estiver ativa ao carregar o script
    const tabLink = modal.querySelector('[data-bs-target="#aba-servicos"], a[href="#aba-servicos"]');
    if ((tabLink && tabLink.classList.contains('active')) || pane.classList.contains('show')) {
      if (pane.dataset[mark] !== '1') {
        pane.dataset[mark] = '1';
        initTabelaServicos();
      }
    }

    // cleanup ao fechar modal
    on(modal, 'hidden.bs.modal', ()=>{
      try { ctl.abort(); } catch(_) {}
      delete modal.__salaServCtl;
      delete pane.dataset[mark];
    }, { once:true });
  }

  armTabInitOnce();
})();
