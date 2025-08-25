// salas/tabModSalaAbaDocumentos.js
// Guia "Documentos": upload <= 2MB, título + descrição, add/cancel/save, editar (2 cliques ou botão),
// excluir, listar+buscar+ordenar. Carrega só no 1º clique da aba; reload apenas em insert/delete.

(function () {
  
  const ENDPOINT_LIST   = 'salas/SModSalaDocListar.php';
  const ENDPOINT_SAVE   = 'salas/SModSalaDocSalvar.php';
  const ENDPOINT_DELETE = 'salas/SModSalaDocExcluir.php';

  const limiteMB = 20;
  let BYTES_Max_MB     = limiteMB * 1024 * 1024;





  const $    = (sel, ctx = document) => ctx.querySelector(sel);
  const $all = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const normalize = (s) => {
    try { return (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase(); }
    catch { return (s||'').toLowerCase(); }
  };
  const humanSize = (bytes) => {
    if (bytes == null) return '';
    const b = Number(bytes);
    if (isNaN(b)) return '';
    if (b < 1024) return `${b} B`;
    if (b < 1024*1024) return `${(b/1024).toFixed(1)} KB`;
    return `${(b/1024/1024).toFixed(2)} MB`;
  };
  const formatDate = (iso) => {
    if (!iso) return '';
    const d = new Date(iso);
    if (isNaN(d)) return iso;
    const dd = String(d.getDate()).padStart(2,'0');
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const yy = d.getFullYear();
    return `${dd}/${mm}/${yy}`;
  };
  function injectSortStyles(){
    if ($('#_doc_sort_css')) return;
    const st = document.createElement('style');
    st.id = '_doc_sort_css';
    st.textContent = `
      th.sort-asc::after  { content:" \\2191"; font-size:.9em; }
      th.sort-desc::after { content:" \\2193"; font-size:.9em; }
      tr.row-dirty { background: #fff8e1; }
      #tabela-documentos td.td-desc { max-width: 480px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    `;
    document.head.appendChild(st);
  }

  // id da sala sempre fresco
  const getSalaId = () =>
  Number(
    document.querySelector('#doc-id_sala')?.value ||
    document.querySelector('#formCadSala input[name="id"]')?.value ||
    0
  );

  // pega o pane e modal
  const pane  = $('#aba-documentos');
  if (!pane) return;
  const modal = pane.closest('.modal') || document;

  // zera instância anterior desta aba
  if (modal.__salaDocsCtl && typeof modal.__salaDocsCtl.abort === 'function') {
    try { modal.__salaDocsCtl.abort(); } catch(_) {}
  }
  const ctl = new AbortController();
  modal.__salaDocsCtl = ctl;

  const on = (el, ev, fn, opts) => { if (el) el.addEventListener(ev, fn, { ...(opts||{}), signal: ctl.signal }); };

  // sorting helper: colunas = 0 ícone, 1 título, 2 descrição, 3 tamanho (num), 4 data (data)
  function enableSorting(table, getRows, render){
    const ths = $all('thead th', table);
    const state = { colIdx:null, asc:true };

    const clearIcons = () => ths.forEach(th => th.classList.remove('sort-asc','sort-desc'));
    const setIcon = (th, asc) => th.classList.add(asc ? 'sort-asc':'sort-desc');

    function getComparable(row, idx){
      switch (idx) {
        case 1: return normalize(row.titulo);
        case 2: return normalize(row.descricao || '');
        case 3: return Number(row.tamanho_bytes) || 0;
        case 4: return Date.parse(row.data_upload || row.created_at || row.data_arquivo || '') || 0;
        default: return '';
      }
    }
    function sortRows(rows){
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
        state.asc    = th.dataset.sortInit.toUpperCase() === 'ASC';
      }
      th.style.cursor = 'pointer';
      on(th, 'click', ()=>{
        if (state.colIdx === idx) state.asc = !state.asc;
        else { state.colIdx = idx; state.asc = true; }
        clearIcons(); setIcon(th, state.asc);
        render(sortRows(getRows()));
      });
    });

    if (appliedInit){
      clearIcons();
      const th = ths[state.colIdx]; if (th) setIcon(th, state.asc);
      return rows => sortRows(rows);
    }
    return rows => rows;
  }

  function SalaDocsModule(){
    injectSortStyles();

    // UI
    const form      = $('#form-doc', pane);
    const btnNovo   = $('#btn-novo-doc', pane);
    const btnSalvar = $('#doc-salvar', pane);
    const btnCanc   = $('#doc-cancelar', pane);

    const inpId     = $('#doc-id', pane);
    const inpTitulo = $('#doc-titulo', pane);
    const inpDesc   = $('#doc-desc', pane);
    const inpFile   = $('#doc-file', pane);
    const btnFile   = $('#doc-file-btn', pane);
    const txtFile   = $('#doc-file-name', pane);
    const curWrap   = $('#doc-file-current', pane);
    const curLink   = $('#doc-file-link', pane);

    const table     = $('#tabela-documentos', pane);
    const tbody     = table?.querySelector('tbody');
    const search    = pane.querySelector('.searchBox');

    if (!table || !tbody) return;

    let allRows = [];
    let viewRows = [];
    let loadedOnce = false; // carrega apenas na 1ª exibição da aba

    function resetForm(){
      inpId.value = '';
      inpTitulo.value = '';
      inpDesc.value = '';
      if (inpFile) inpFile.value = '';
      if (txtFile) txtFile.value = '';
      if (curWrap) curWrap.style.display = 'none';
      if (curLink) { curLink.removeAttribute('href'); curLink.textContent = ''; }
    }
    const showForm = (show) => { if (form) form.style.display = show ? '' : 'none'; };
    const pickFile = () => inpFile?.click();
    function onFileChange(){
      const f = inpFile?.files && inpFile.files[0];
      if (!f){ if (txtFile) txtFile.value = ''; return; }
      if (f.size > BYTES_Max_MB){
        alert('Arquivo excede 2 MB.');
        inpFile.value = '';
        if (txtFile) txtFile.value = '';
        return;
      }
      if (txtFile) txtFile.value = `${f.name} (${humanSize(f.size)})`;
    }

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
        const ext = (typeof window.extrairExtensao === 'function')
          ? window.extrairExtensao(r.arquivo)
          : (r.arquivo || '').split('.').pop()?.toLowerCase() || '';

        const ico = (window.FileIconRegistry && window.FileIconRegistry.get(ext))
          || { icon:'bi-file-earmark-fill', color:'text-muted' };

        const iconHtml = `<i class="bi ${ico.icon} ${ico.color} me-2" title="${ext || 'arquivo'}"></i>`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="td-icon text-center">${iconHtml}</td>
          <td class="td-titulo">${r.titulo ?? ''}</td>
          <td class="td-desc" title="${(r.descricao||'').replace(/"/g,'&quot;')}">${(r.descricao||'')}</td>
          <td class="td-size">${humanSize(r.tamanho_bytes)}</td>
          <td class="td-date">${formatDate(r.data_upload || r.data_arquivo)}</td>
          <td class="text-center">${rowActionsHTML(r)}</td>
        `;

        // clique na linha -> abre arquivo (se houver)
        tr.style.cursor = 'pointer';
        on(tr, 'click', (ev)=>{
          if (ev.target.closest('.act-edit, .act-del')) return;
          if (r.url) window.open(r.url, '_blank', 'noopener');
        });

        // duplo clique -> editar
        on(tr, 'dblclick', ()=> startEdit(r.id));

        tbody.appendChild(tr);
      });

      // ações (delegado com signal)
      $all('.act-edit', tbody).forEach(btn => on(btn, 'click', ()=> startEdit(Number(btn.dataset.id))));
      $all('.act-del',  tbody).forEach(btn => on(btn, 'click', ()=> doDelete(Number(btn.dataset.id))));
    }

    function applyFilter(){
      const term = normalize(search ? search.value : '');
      viewRows = !term ? allRows.slice()
                       : allRows.filter(r =>
                           normalize(r.titulo).includes(term) ||
                           normalize(r.descricao || '').includes(term) ||
                           normalize(r.arquivo || '').includes(term)
                         );
      render(viewRowsSorter(viewRows));
    }

    function loadList(){
      const idSala = getSalaId();
      if (!idSala) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Salve a sala para anexar documentos.</td></tr>';
        return;
      }
      const url = `${ENDPOINT_LIST}?id_sala=${encodeURIComponent(idSala)}`;
      fetch(url, { credentials:'same-origin', cache:'no-store', signal: ctl.signal })
        .then(r => r.json())
        .then(j => {
          allRows = Array.isArray(j.rows) ? j.rows : [];
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
      if (!getSalaId()) { alert('Salve a sala antes de adicionar documentos.'); return; }
      resetForm(); showForm(true);
    }

    function startEdit(id){
      const row = allRows.find(r => Number(r.id) === Number(id));
      if (!row) return;
      resetForm();
      inpId.value     = row.id;
      inpTitulo.value = row.titulo || '';
      inpDesc.value   = row.descricao || '';
      if (txtFile) txtFile.value = '';
      if (row.url) {
        curWrap.style.display = '';
        curLink.href = row.url;
        curLink.textContent = row.arquivo || 'arquivo';
      }
      showForm(true);
    }

    function doDelete(id){
      if (!confirm('Excluir este documento?')) return;
      const fd = new FormData(); fd.append('id', id);
      fetch(ENDPOINT_DELETE, { method:'POST', body: fd, credentials:'same-origin', signal: ctl.signal })
        .then(r => r.json())
        .then(j => {
          if (!j.ok) throw new Error(j.msg || 'Falha ao excluir');
          // reload SOMENTE aqui (delete)
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
      const isEdit = !!inpId.value;
      const f = inpFile?.files && inpFile.files[0];

      if (!isEdit) {
        if (!f) { alert('Selecione um arquivo (máx. 2 MB).'); return; }
        if (f.size > BYTES_Max_MB) { alert('Arquivo excede 2 MB.'); return; }
      } else if (f && f.size > BYTES_Max_MB) {
        alert('Arquivo excede 2 MB.');
        return;
      }

      const idSala = getSalaId();
      if (!idSala) { alert('Salve a sala antes de anexar documentos.'); return; }

      const fd = new FormData();
      fd.append('id', inpId.value);
      fd.append('id_sala', idSala);
      fd.append('titulo', inpTitulo.value.trim());
      fd.append('descricao', inpDesc.value.trim());
      if (f) fd.append('arquivo', f);

      btnSalvar.disabled = true;
      fetch(ENDPOINT_SAVE, { method:'POST', body: fd, credentials:'same-origin', signal: ctl.signal })
        .then(r => r.json())
        .then(j => {
          if (!j.ok) throw new Error(j.msg || 'Falha ao salvar');
          showForm(false);
          resetForm();
          // reload SOMENTE aqui (insert/update)
          loadList();
        })
        .catch(err=>{
          if (err.name === 'AbortError') return;
          console.error(err);
          alert('Não foi possível salvar o documento.');
        })
        .finally(()=> btnSalvar.disabled = false);
    }

    // Binds (com signal)
    on(btnNovo,   'click', startNew);
    on(btnFile,   'click', pickFile);
    on(inpFile,   'change', onFileChange);
    on(btnCanc,   'click', ()=>{ showForm(false); resetForm(); });
    on(btnSalvar, 'click', doSave);
    if (search) on(search, 'input', applyFilter);

    // Sorting (usa função que lê viewRows no momento)
    const viewRowsSorter = enableSorting(table, ()=>viewRows, rows => render(rows));

    // Init apenas na 1ª vez que a aba for exibida
    on(modal, 'shown.bs.tab', (e) => {
      const target = e.target?.getAttribute('data-bs-target') || e.target?.getAttribute('href') || '';
      if (target === '#aba-documentos' || target.endsWith('#aba-documentos')) {
        if (!loadedOnce) { loadedOnce = true; loadList(); }
      }
    });

    // Se a aba já estiver ativa quando o script carregar
    const tabLink = modal.querySelector('[data-bs-target="#aba-documentos"], a[href="#aba-documentos"]');
    if ((tabLink && tabLink.classList.contains('active')) || pane.classList.contains('show')) {
      if (!loadedOnce) { loadedOnce = true; loadList(); }
    }
  }

  // boot do módulo (sem carregar lista agora)
  SalaDocsModule();

  // cleanup total ao fechar o modal
  on(modal, 'hidden.bs.modal', () => {
    try { ctl.abort(); } catch(_) {}
    delete modal.__salaDocsCtl;
  }, { once:true });
})();
