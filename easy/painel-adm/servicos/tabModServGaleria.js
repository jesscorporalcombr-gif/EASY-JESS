// servicos/tabModServGaleria.js (drop-in sem mudar UI/fluxo)
(function () {
  // pega o modal deste instance
  const modal = document.getElementById('modalCadServico');
  if (!modal) return;

  // se já tinha uma instância, destrói antes de criar outra (evita pilha)
  if (typeof modal.__galeriaCleanup === 'function') {
    try { modal.__galeriaCleanup(); } catch(_) {}
  }

  // -------- estado local desta abertura --------
  let G_rows = [];
  let G_editId = null;

  // refs de listeners pra conseguir remover no cleanup
  const handlers = [];

  function on(el, ev, fn, opts){
    el.addEventListener(ev, fn, opts);
    handlers.push([el, ev, fn, opts]);
  }
  function cleanup(){
    handlers.forEach(([el, ev, fn, opts])=>{
      try { el.removeEventListener(ev, fn, opts); } catch(_) {}
    });
    handlers.length = 0;
  }
  modal.__galeriaCleanup = cleanup; // salva no modal pra próxima reabertura limpar

  // -------- helpers --------
  function basePaths(){
    const pasta = window.pastaFiles || (typeof pastaFiles !== 'undefined' ? pastaFiles : '');
    const base  = pasta ? `../${pasta}/img/servicos/galeria/` : `../img/servicos/galeria/`;
    return { base, mini: base + 'mini/' };
  }
  function formatDateBR(iso){
    if (!iso) return '';
    const [y,m,d] = iso.split('-'); return (y&&m&&d) ? `${d}/${m}/${y}` : iso;
  }
  function getIds(){
    const idServico = parseInt(modal.querySelector('#formCadServico input[name="id"]')?.value || '0', 10);
    return { idServico };
  }

  // ------ AJAX ------
  async function fetchRows(q=''){
    const { idServico } = getIds();
    const url = `servicos/SModServGaleria.php?id_servico=${encodeURIComponent(idServico)}&q=${encodeURIComponent(q)}`;
    const r = await fetch(url, { credentials: 'same-origin', cache:'no-store' });
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    const j = await r.json();
    if (!j.success) throw new Error(j.msg || 'Falha ao carregar galeria');
    return j.rows || [];
  }

  function reloadTable(){
    const cont   = modal.querySelector('#galeria-container-modal');
    if (!cont) return;
    const tbody  = cont.querySelector('tbody');
    const sInput = cont.querySelector('.searchBox');
    const q = (sInput?.value || '').trim();

    fetchRows(q).then(rows=>{
      G_rows = rows;
      renderTable();
    }).catch(err=>{
      console.error(err);
      if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-danger">Erro ao carregar: ${String(err.message||err)}</td></tr>`;
    });
  }

  function renderTable(){
    const cont  = modal.querySelector('#galeria-container-modal');
    if (!cont) return;
    const tbody = cont.querySelector('tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    const { base, mini } = basePaths();

    if (!Array.isArray(G_rows) || G_rows.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-muted">Nenhuma foto cadastrada.</td></tr>`;
      return;
    }

    G_rows.forEach(row=>{
      const tr = document.createElement('tr');

      // thumb
      const tdThumb = document.createElement('td');
      const img = document.createElement('img');
      img.src = row.arquivo_mini ? (mini + row.arquivo_mini) : '../img/sem-imagem.svg';
      img.alt = row.titulo || 'Foto';
      Object.assign(img.style, { width:'56px', height:'56px', objectFit:'cover', borderRadius:'8px', cursor:'pointer' });
      img.title = 'Abrir imagem';
      on(img, 'click', ()=>{ if (row.arquivo_ori) window.open(base + row.arquivo_ori, '_blank'); });
      tdThumb.appendChild(img);
      tr.appendChild(tdThumb);

      // Título
      const tdTitulo = document.createElement('td');
      tdTitulo.textContent = row.titulo || '';
      tdTitulo.style.cursor = 'pointer';
      tdTitulo.title = 'Editar';
      on(tdTitulo, 'click', ()=> openEditor(row));
      tr.appendChild(tdTitulo);

      // Data
      const tdData = document.createElement('td');
      tdData.textContent = formatDateBR(row.data_foto);
      tr.appendChild(tdData);

      // Tipo
      const tdTipo = document.createElement('td');
      tdTipo.textContent = row.tipo_foto || '';
      tr.appendChild(tdTipo);

      // Descrição
      const tdDesc = document.createElement('td');
      tdDesc.textContent = row.descricao || '';
      tr.appendChild(tdDesc);

      // Ações (ícones sem borda, lado a lado)
      const tdAcoes = document.createElement('td');
      tdAcoes.className = 'text-end';
      tdAcoes.innerHTML = `
        <button type="button" class="btn btn-sm me-1 btn-outline-primary btn-acoes-tabelas-modal" data-role="edit" title="Editar">
          <i class="bi bi-pencil ico-act-tab-mod"></i>
        </button>
        <button type="button" class="btn btn-sm me-1 btn-outline-danger btn-acoes-tabelas-modal" data-role="del" title="Excluir">
          <i class="bi bi-trash ico-act-tab-mod"></i>
        </button>
      `;
      const bEdit = tdAcoes.querySelector('[data-role="edit"]');
      const bDel  = tdAcoes.querySelector('[data-role="del"]');
      on(bEdit, 'click', ()=> openEditor(row));
      on(bDel,  'click', ()=> delRow(row.id));
      tr.appendChild(tdAcoes);

      tbody.appendChild(tr);
    });
  }

  // ------ Editor ------
  function bindEditor(){
    const container   = modal.querySelector('.container-foto-add');
    const btnNova     = modal.querySelector('#btn-nova-foto');
    const btnCancelar = modal.querySelector('#btn-cancelar-foto');
    const btnSalvar   = modal.querySelector('#btn-salvar-foto');
    const fileInput   = modal.querySelector('#fotoUpload');
    const previewImg  = modal.querySelector('#preview-foto');
    const tituloInput = modal.querySelector('#fotoTitulo');
    const dateInput   = modal.querySelector('#fotoData');
    const tipoSelect  = modal.querySelector('#fotoTipo');
    const descTxt     = modal.querySelector('#fotoDescricao');

    if (!container || !btnNova || !btnCancelar || !btnSalvar) return;

    // garante 1 bind por abertura
    if (container.dataset.bound === '1') return;
    container.dataset.bound = '1';

    on(previewImg, 'click', ()=>{ if (!fileInput.disabled) fileInput.click(); });

    on(fileInput, 'change', function(){
      if (this.files && this.files[0]) {
        const fr = new FileReader();
        fr.onload = e => previewImg.src = e.target.result;
        fr.readAsDataURL(this.files[0]);

        if (!dateInput.value) {
          const dt = new Date(this.files[0].lastModified);
          const yyyy = dt.getFullYear();
          const mm = String(dt.getMonth()+1).padStart(2,'0');
          const dd = String(dt.getDate()).padStart(2,'0');
          dateInput.value = `${yyyy}-${mm}-${dd}`;
        }
      }
    });

    on(btnNova, 'click', ()=>{
      G_editId = null;
      fileInput.disabled = false;
      fileInput.value = '';
      previewImg.src = '../img/sem-imagem.svg';
      tituloInput.value = '';
      dateInput.value = '';
      tipoSelect.value = '';
      descTxt.value = '';
      container.style.display = 'block';
    });

    on(btnCancelar, 'click', ()=>{
      container.style.display = 'none';
    });

    on(btnSalvar, 'click', async ()=>{
      const { idServico } = getIds();
      if (!idServico) return alert('Serviço inválido.');

      if (!G_editId && (!fileInput.files || fileInput.files.length === 0)) {
        alert('Selecione uma foto.'); return;
      }
      if (!tituloInput.value.trim()) { alert('Informe um título.'); return; }
      if (!dateInput.value) { alert('Informe a data.'); return; }
      if (!tipoSelect.value) { alert('Selecione o tipo de foto.'); return; }

      const fd = new FormData();
      fd.append('id_servico', idServico);
      if (G_editId) fd.append('id', G_editId);
      if (fileInput.files && fileInput.files[0]) fd.append('foto', fileInput.files[0]);
      fd.append('titulo', tituloInput.value.trim());
      fd.append('data_foto', dateInput.value);
      fd.append('tipo_foto', tipoSelect.value);
      fd.append('descricao', descTxt.value.trim());

      try {
        const r = await fetch('servicos/inserir_foto.php', { method: 'POST', body: fd, credentials: 'same-origin' });
        const j = await r.json();
        if (!j.success) throw new Error(j.msg || 'Falha ao salvar');
        container.style.display = 'none';
        reloadTable();
      } catch (e) {
        alert('Erro ao salvar: ' + e.message);
      }
    });
  }

  function openEditor(row){
    const container   = modal.querySelector('.container-foto-add');
    const fileInput   = modal.querySelector('#fotoUpload');
    const previewImg  = modal.querySelector('#preview-foto');
    const tituloInput = modal.querySelector('#fotoTitulo');
    const dateInput   = modal.querySelector('#fotoData');
    const tipoSelect  = modal.querySelector('#fotoTipo');
    const descTxt     = modal.querySelector('#fotoDescricao');
    const { mini } = basePaths();

    G_editId = row.id;
    fileInput.disabled = true;
    fileInput.value = '';
    previewImg.src = row.arquivo_mini ? (mini + row.arquivo_mini) : '../img/sem-imagem.svg';
    tituloInput.value = row.titulo || '';
    dateInput.value = row.data_foto || '';
    tipoSelect.value = row.tipo_foto || '';
    descTxt.value = row.descricao || '';
    container.style.display = 'block';
  }

  async function delRow(id){
    if (!confirm('Excluir esta foto?')) return;
    try {
      const fd = new FormData(); fd.append('id', id);
      const r = await fetch('servicos/excluir_foto.php', { method: 'POST', body: fd, credentials: 'same-origin' });
      const j = await r.json();
      if (!j.success) throw new Error(j.msg || 'Falha ao excluir');
      reloadTable();
    } catch (e) {
      alert('Erro ao excluir: ' + e.message);
    }
  }

  function bindSearch(){
    const cont = modal.querySelector('#galeria-container-modal');
    if (!cont) return;
    const s = cont.querySelector('.searchBox');
    if (!s || s.dataset.bound === '1') return;
    s.dataset.bound = '1';

    let t = null;
    on(s, 'input', ()=>{
      clearTimeout(t);
      t = setTimeout(()=> reloadTable(), 250);
    });
  }

  function initTabela(){
    bindEditor();
    bindSearch();
    reloadTable();
  }

  // exibe quando a aba "Imagens" for ativada — ANCORADO NO MODAL (não no document!)
const onTabShown = (e) => {
  const target = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
  if (target === '#aba-imagens') {
    const pane = modal.querySelector('#aba-imagens');
    if (pane.dataset.gInit === '1') return; // já inicializado nesta abertura
    pane.dataset.gInit = '1';
    initTabela(); // initTabela já faz reloadTable()
  }
};
on(modal, 'shown.bs.tab', onTabShown);

  // se já estiver ativa
const pane = modal.querySelector('#aba-imagens');
const tab  = modal.querySelector('#imagens-tab');
if (tab?.classList.contains('active') || pane?.classList.contains('show')) {
  if (pane.dataset.gInit !== '1') {
    pane.dataset.gInit = '1';
    initTabela(); // initTabela já chama reloadTable()
  }
}

  // cleanup ao fechar modal (remove TODOS os listeners desta instância)
  on(modal, 'hidden.bs.modal', ()=>{
    cleanup();
    delete modal.__galeriaCleanup;
  }, { once:true });
})();
