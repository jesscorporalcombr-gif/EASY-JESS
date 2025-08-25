// equipamentos/tabModEquipamentoAbaDocumentos.js
// Guia "Documentos": título + tipo + data_arquivo + descrição + arquivo (sem limite de 2MB no front),
// listar/buscar/ordenar, adicionar/editar/remover, abrir arquivo ao clicar na linha.
(function () {
  // -------------------- Config --------------------
  const ENDPOINT_LIST   = 'equipamentos/SModEquipamentoDocListar.php';
  const ENDPOINT_SAVE   = 'equipamentos/SModEquipamentoDocSalvar.php';
  const ENDPOINT_DELETE = 'equipamentos/SModEquipamentoDocExcluir.php';

  // -------------------- Utils --------------------
  const $    = (sel, ctx = document) => ctx.querySelector(sel);
  const $all = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  function normalize(s){
    try { return (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase(); }
    catch { return (s||'').toLowerCase(); }
  }
  function truncate(s, n=100){
    s = s || '';
    return s.length > n ? s.slice(0, n - 1) + '…' : s;
  }
  function humanSize(bytes){
    if (bytes == null) return '';
    const b = Number(bytes);
    if (isNaN(b)) return '';
    if (b < 1024) return `${b} B`;
    if (b < 1024*1024) return `${(b/1024).toFixed(1)} KB`;
    return `${(b/1024/1024).toFixed(2)} MB`;
  }
  function formatDate(iso){
    if (!iso) return '';
    const d = new Date(iso);
    if (isNaN(d.getTime())) return iso; // fallback
    const dd = String(d.getDate()).padStart(2,'0');
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const yyyy = d.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
  }
  function injectSortStyles(){
    if ($('#_doc_sort_css')) return;
    const st = document.createElement('style');
    st.id = '_doc_sort_css';
    st.textContent = `
      th.sort-asc::after  { content:" \\2191"; font-size:.9em; }
      th.sort-desc::after { content:" \\2193"; font-size:.9em; }
      tr.row-dirty { background:#fff8e1; }
      #tabela-documentos td.td-desc { max-width: 420px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    `;
    document.head.appendChild(st);
  }

  function extrairExtensao(nome){
    if (!nome) return '';
    const i = nome.lastIndexOf('.');
    return i >= 0 ? nome.slice(i + 1).toLowerCase() : '';
  }

  // Lê o ID SEM cache (sempre fresco)
  function getEquipId() {
    return Number($('#doc-id_equipamento')?.value || $('#frm-id')?.value || 0);
  }

  // -------------------- Abort controller (zera tudo ao fechar modal) --------------------
  const pane  = $('#aba-documentos');
  if (!pane) return;

  const modal = pane.closest('.modal') || document;
  if (modal.__docCtl && typeof modal.__docCtl.abort === 'function') {
    try { modal.__docCtl.abort(); } catch(_) {}
  }
  const ctl = new AbortController();
  modal.__docCtl = ctl;

  function on(el, ev, fn, opts){
    if (!el) return;
    el.addEventListener(ev, fn, { ...(opts||{}), signal: ctl.signal });
  }

  // -------------------- Ordenação --------------------
  function enableSorting(table, getRows, render){
    const ths = $all('thead th', table);
    const state = { colIdx:null, type:null, asc:true };

    function clearIcons(){ ths.forEach(th => th.classList.remove('sort-asc','sort-desc')); }
    function setIcon(th, asc){ th.classList.add(asc ? 'sort-asc':'sort-desc'); }

    function getComparable(row, idx){
      // 0 Título, 1 Tipo, 2 Descrição, 3 Data, 4 Tamanho, 5 Ações
      switch(idx){
        case 0: return normalize(row.titulo);
        case 1: return normalize(row.tipo);
        case 2: return normalize(row.descricao);
        case 3: return Date.parse(row.data_arquivo || row.data_upload || '') || 0;
        case 4: return Number(row.tamanho_bytes) || 0;
        default: return '';
      }
    }

    function sort(rows){
      if (state.colIdx == null) return rows;
      const { colIdx, asc } = state;
      return rows.slice().sort((a,b)=>{
        const va = getComparable(a,colIdx);
        const vb = getComparable(b,colIdx);
        if (va < vb) return asc ? -1 : 1;
        if (va > vb) return asc ? 1 : -1;
        return 0;
      });
    }

    let appliedInit = false;
    ths.forEach((th, idx)=>{
      const type = th.dataset.sort;
      if (!type) return;

      if (!appliedInit && th.dataset.sortInit){
        appliedInit = true;
        state.colIdx = idx;
        state.type   = type;
        state.asc    = th.dataset.sortInit.toUpperCase() === 'ASC';
      }

      th.style.cursor = 'pointer';
      on(th, 'click', ()=>{
        if (state.colIdx === idx) state.asc = !state.asc;
        else { state.colIdx = idx; state.type = type; state.asc = true; }
        clearIcons();
        setIcon(th, state.asc);
        render(sort(getRows()));
      });
    });

    if (appliedInit){
      clearIcons();
      const th = ths[state.colIdx];
      if (th) setIcon(th, state.asc);
      return rows => sort(rows);
    }
    return rows => rows;
  }

  // -------------------- Módulo da Aba Documentos --------------------
  function DocModule(){
    injectSortStyles();

    // UI
    const form      = $('#form-doc');
    const btnNovo   = $('#btn-novo-doc');
    const btnSalvar = $('#doc-salvar');
    const btnCanc   = $('#doc-cancelar');

    const inpId     = $('#doc-id');
    const inpTitulo = $('#doc-titulo');
    const inpDesc   = $('#doc-desc');
    const inpTipo   = $('#doc-tipo');
    const inpData   = $('#doc-data-arquivo');

    const inpFile   = $('#doc-file');
    const btnFile   = $('#doc-file-btn');
    const txtFile   = $('#doc-file-name');
    const curWrap   = $('#doc-file-current');
    const curLink   = $('#doc-file-link');

    const table     = $('#tabela-documentos');
    const tbody     = table?.querySelector('tbody');
    const search    = pane.querySelector('.searchBox');

    if (!table || !tbody) return;

    let allRows = [];
    let viewRows = [];
    let loadedOnce = false; // <- só carrega a lista na *primeira* vez que a aba for mostrada

    // ---- helpers de form ----
    function resetForm(){
      inpId.value = '';
      inpTitulo.value = '';
      inpDesc.value = '';
      inpTipo.value = '';
      inpData.value = '';
      if (inpFile) inpFile.value = '';
      if (txtFile) txtFile.value = '';
      if (curWrap) curWrap.style.display = 'none';
      if (curLink) { curLink.removeAttribute('href'); curLink.textContent = ''; }
    }
    function showForm(show){ if (form) form.style.display = show ? '' : 'none'; }
    function pickFile(){ inpFile?.click(); }
    function onFileChange(){
      const f = inpFile?.files && inpFile.files[0];
      if (txtFile) txtFile.value = f ? `${f.name} (${humanSize(f.size)})` : '';
    }

    // ---- renderização ----
    function rowActionsHTML(row){
      return `
        <div class="d-flex justify-content-center gap-2">
          <button type="button" class="btn btn-sm me-1 btn-outline-primary btn-acoes-tabelas-modal act-edit" data-id="${row.id}">
            <i class="bi bi-pencil ico-act-tab-mod"></i>
          </button>
          <button type="button" class="btn btn-sm me-1 btn-outline-danger btn-acoes-tabelas-modal act-del" data-id="${row.id}">
            <i class="bi bi-trash ico-act-tab-mod"></i>
          </button>
        </div>
      `;
    }

    function render(rows){
      tbody.innerHTML = '';
      rows.forEach(r=>{
        const e = extrairExtensao(r.arquivo);
        const ico = (window.FileIconRegistry && window.FileIconRegistry.get(e)) || { icon:'bi-file-earmark-fill', color:'text-muted' };
        const iconHtml = `<i class="bi ${ico.icon} ${ico.color} me-2" title="${(e||'arquivo')}"></i>`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="td-icon">${iconHtml ?? ''}</td>
          <td class="td-titulo">${r.titulo ?? ''}</td>
          <td class="td-tipo">${r.tipo ?? ''}</td>
          <td class="td-desc" title="${(r.descricao||'').replace(/"/g,'&quot;')}">${truncate(r.descricao, 100)}</td>
          <td>${formatDate(r.data_arquivo) || formatDate(r.data_upload)}</td>
          <td>${humanSize(r.tamanho_bytes)}</td>
          <td class="text-center">${rowActionsHTML(r)}</td>
        `;

        // clique na linha -> abre arquivo (evita clique nos botões)
        tr.style.cursor = 'pointer';
        on(tr, 'click', (ev)=>{
          if (ev.target.closest('.act-edit, .act-del')) return;
          if (r.url) window.open(r.url, '_blank');
        });

        // duplo clique também edita
        on(tr, 'dblclick', ()=> startEdit(r.id));

        tbody.appendChild(tr);
      });

      // delegação simples (bound com signal) para os botões
      $all('.act-edit', tbody).forEach(btn=>{
        on(btn, 'click', ()=> startEdit(Number(btn.dataset.id)));
      });
      $all('.act-del',  tbody).forEach(btn=>{
        on(btn, 'click', ()=> doDelete(Number(btn.dataset.id)));
      });
    }

    function applyFilter(){
      const term = normalize(search ? search.value : '');
      viewRows = !term
        ? allRows.slice()
        : allRows.filter(r =>
            normalize(r.titulo).includes(term) ||
            normalize(r.tipo).includes(term) ||
            normalize(r.descricao).includes(term) ||
            normalize(r.arquivo || '').includes(term)
          );
      render(viewRowsSorter(viewRows));
    }

    // ---- Data I/O ----
    function loadList(){
      const idEquip = getEquipId();
      if (!idEquip) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Salve o cadastro do equipamento para anexar documentos.</td></tr>';
        return;
      }
      const url = `${ENDPOINT_LIST}?id_equipamento=${encodeURIComponent(idEquip)}`;
      fetch(url, { credentials:'same-origin', cache:'no-store', signal: ctl.signal })
        .then(r => r.json())
        .then(j => {
          const rows = Array.isArray(j?.rows) ? j.rows : [];
          allRows = rows;
          viewRows = allRows.slice();
          render(viewRowsSorter(viewRows)); // aplica sort-init
        })
        .catch(err=>{
          if (err.name === 'AbortError') return;
          console.error('Erro ao listar documentos:', err);
          tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Não foi possível carregar os documentos.</td></tr>';
        });
    }

    function startNew(){
      if (!getEquipId()) {
        alert('Salve o cadastro do equipamento antes de adicionar documentos.');
        return;
      }
      resetForm();
      showForm(true);
    }

    function startEdit(id){
      const row = allRows.find(r => Number(r.id) === Number(id));
      if (!row) return;
      resetForm();
      inpId.value     = row.id;
      inpTitulo.value = row.titulo || '';
      inpDesc.value   = row.descricao || '';
      inpTipo.value   = row.tipo || '';
      inpData.value   = (row.data_arquivo || '').slice(0,10) || '';
      if (txtFile) txtFile.value   = '';
      if (row.url){
        curWrap.style.display = '';
        curLink.href = row.url;
        curLink.textContent = row.arquivo || 'arquivo';
      }
      showForm(true);
    }

    function doDelete(id){
      if (!confirm('Excluir este documento?')) return;
      const fd = new FormData();
      fd.append('id', id);
      fetch(ENDPOINT_DELETE, { method:'POST', body: fd, credentials:'same-origin', signal: ctl.signal })
        .then(r => r.json())
        .then(j => {
          if (j?.ok !== true) throw new Error(j?.msg || 'Falha ao excluir');
          // **Só aqui** recarrega a lista
          loadList();
        })
        .catch(err=>{
          if (err.name === 'AbortError') return;
          console.error(err);
          alert('Não foi possível excluir.');
        });
    }

    function doSave(){
      if (!inpTitulo.value.trim()){
        alert('Informe o título.');
        return;
      }
      const idEquip = getEquipId();
      if (!idEquip) {
        alert('Você precisa salvar o cadastro do equipamento antes de anexar documentos.');
        return;
      }

      const fd = new FormData();
      fd.append('id', inpId.value);
      fd.append('id_equipamento', idEquip);
      fd.append('titulo', inpTitulo.value.trim());
      fd.append('descricao', inpDesc.value.trim());
      fd.append('tipo', inpTipo.value.trim());
      fd.append('data_arquivo', inpData.value || '');
      if (inpFile?.files && inpFile.files[0]) fd.append('arquivo', inpFile.files[0]);

      btnSalvar.disabled = true;
      fetch(ENDPOINT_SAVE, { method:'POST', body: fd, credentials:'same-origin', signal: ctl.signal })
        .then(r => r.json())
        .then(j => {
          if (j?.ok !== true) throw new Error(j?.msg || 'Falha ao salvar');
          showForm(false);
          resetForm();
          // **Só aqui** recarrega a lista (insert/update)
          loadList();
        })
        .catch(err=>{
          if (err.name === 'AbortError') return;
          console.error(err);
          alert('Não foi possível salvar o documento.');
        })
        .finally(()=> btnSalvar.disabled = false);
    }

    // ---- Binds (todos com signal: ctl.signal) ----
    on(btnNovo,   'click', startNew);
    on(btnFile,   'click', pickFile);
    on(inpFile,   'change', onFileChange);
    on(btnCanc,   'click', ()=>{ showForm(false); resetForm(); });
    on(btnSalvar, 'click', doSave);
    if (search) on(search, 'input', applyFilter);

    // sorting (instala com função que lê viewRows no momento da renderização)
    const viewRowsSorter = enableSorting(table, ()=>viewRows, rows => render(rows));

    // ---- Carregar apenas na primeira vez que a aba for exibida ----
    on(modal, 'shown.bs.tab', (e)=>{
      const target = e.target?.getAttribute('data-bs-target') || e.target?.getAttribute('href') || '';
      if (target === '#aba-documentos' || target.endsWith('#aba-documentos')) {
        if (!loadedOnce) {
          loadedOnce = true;
          loadList(); // primeira e ÚNICA carga automática por abertura do modal
        }
      }
    });

    // Se a aba já estiver ativa quando este script rodar
    const tabLink = modal.querySelector('[data-bs-target="#aba-documentos"], a[href="#aba-documentos"]');
    if ((tabLink && tabLink.classList.contains('active')) || pane.classList.contains('show')) {
      if (!loadedOnce) {
        loadedOnce = true;
        loadList();
      }
    }
  }

  // -------------------- Boot do módulo --------------------
  DocModule();

  // Depois do INSERT do equipamento:
  // Apenas atualiza o hidden. NÃO recarrega a lista aqui (vai carregar na 1ª abertura da aba).
  on(document, 'equipamento:salvo', (e) => {
    const id = e.detail?.id;
    if (!id) return;
    const hid = document.getElementById('doc-id_equipamento');
    if (hid) hid.value = id;
  });

  // Cleanup ao fechar o modal: remove TODOS listeners/fetch desta instância
  on(modal, 'hidden.bs.modal', () => {
    try { ctl.abort(); } catch(_) {}
    delete modal.__docCtl;
  }, { once:true });
})();
