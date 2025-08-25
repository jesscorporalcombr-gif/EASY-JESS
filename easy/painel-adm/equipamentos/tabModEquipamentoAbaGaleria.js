// equipamentos/tabModEquipamentoGaleria.js  (v0.15)
// Corrige multi-fetch: remove reload duplicado, adiciona lock e mantém debounce da busca

(function(){
  let G_inited = false;
  let G_rows   = [];
  let G_editId = null;
  let G_loading = false;
  let G_reloadPending = false;

  function basePaths(){
    const pasta = window.pastaFiles || (typeof pastaFiles !== 'undefined' ? pastaFiles : '');
    const base  = pasta ? `../${pasta}/img/equipamentos/galeria/` : `../img/equipamentos/galeria/`;
    return { base, mini: base + 'mini/' };
  }

  function formatDateBR(iso){
    if (!iso) return '';
    const [y,m,d] = iso.split('-');
    if (!y || !m || !d) return iso;
    return `${d}/${m}/${y}`;
  }

  function getIds(){
    const idEquipamento = parseInt(document.querySelector('#formCadEquipamento input[name="id"]')?.value || '0', 10);
    return { idEquipamento };
  }

  // ------ AJAX ------
  async function fetchRows(q=''){
    const { idEquipamento } = getIds();
    const url = `equipamentos/SModEquipamentoTabGaleria.php?id_equipamento=${encodeURIComponent(idEquipamento)}&q=${encodeURIComponent(q)}`;
    const r = await fetch(url, { credentials: 'same-origin' });
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    const j = await r.json();
    if (!j.success) throw new Error(j.msg || 'Falha ao carregar galeria');
    return j.rows || [];
  }

  async function reloadTable(){
    const cont   = document.querySelector('#galeria-container-modal');
    if (!cont) return;

    const tbody  = cont.querySelector('tbody');
    const sInput = cont.querySelector('.searchBox');
    const q = (sInput?.value || '').trim();

    // anti reentrância
    if (G_loading) { G_reloadPending = true; return; }
    G_loading = true;

    try{
      const rows = await fetchRows(q);
      G_rows = rows;
      renderTable();
    }catch(err){
      console.error(err);
      if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-danger">Erro ao carregar: ${String(err.message||err)}</td></tr>`;
    }finally{
      G_loading = false;
      if (G_reloadPending) { G_reloadPending = false; reloadTable(); }
    }
  }

  function renderTable(){
    const cont  = document.querySelector('#galeria-container-modal');
    if (!cont) return;
    const tbody = cont.querySelector('tbody');
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
      img.style.width = '56px';
      img.style.height = '56px';
      img.style.objectFit = 'cover';
      img.style.borderRadius = '8px';
      img.style.cursor = 'pointer';
      img.title = 'Abrir imagem';
      img.addEventListener('click', ()=>{
        if (row.arquivo_ori) window.open(base + row.arquivo_ori, '_blank');
      });
      tdThumb.appendChild(img);
      tr.appendChild(tdThumb);

      // Título
      const tdTitulo = document.createElement('td');
      tdTitulo.textContent = row.titulo || '';
      tdTitulo.style.cursor = 'pointer';
      tdTitulo.title = 'Editar';
      tdTitulo.addEventListener('click', ()=> openEditor(row));
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

      // Ações
      const tdAcoes = document.createElement('td');
      tdAcoes.className = 'text-end';
      tdAcoes.innerHTML = `
        <button class="btn btn-sm btn-outline-primary me-1 btn-acoes-tabelas-modal" data-role="edit" title="Editar">
          <i class="bi bi-pencil ico-act-tab-mod"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger me-1 btn-acoes-tabelas-modal" data-role="del" title="Excluir">
          <i class="bi bi-trash ico-act-tab-mod"></i>
        </button>
      `;
      tdAcoes.querySelector('[data-role="edit"]').addEventListener('click', ()=> openEditor(row));
      tdAcoes.querySelector('[data-role="del"]').addEventListener('click', ()=> delRow(row.id));
      tr.appendChild(tdAcoes);

      tbody.appendChild(tr);
    });
  }

  // ------ Editor ------
  function bindEditor(){
    const container   = document.querySelector('.container-foto-add');
    const btnNova     = document.getElementById('btn-nova-foto');
    const btnCancelar = document.getElementById('btn-cancelar-foto');
    const btnSalvar   = document.getElementById('btn-salvar-foto');
    const fileInput   = document.getElementById('fotoUpload');
    const previewImg  = document.getElementById('preview-foto');
    const tituloInput = document.getElementById('fotoTitulo');
    const dateInput   = document.getElementById('fotoData');
    const tipoSelect  = document.getElementById('fotoTipo');
    const descTxt     = document.getElementById('fotoDescricao');

    if (!container || !btnNova || !btnCancelar || !btnSalvar) return;
    if (container.dataset.bound === '1') return;
    container.dataset.bound = '1';

    previewImg.addEventListener('click', ()=>{ if (!fileInput.disabled) fileInput.click(); });

    fileInput.addEventListener('change', function(){
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

    btnNova.addEventListener('click', ()=>{
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

    btnCancelar.addEventListener('click', ()=>{
      container.style.display = 'none';
    });

    btnSalvar.addEventListener('click', async ()=>{
      const { idEquipamento } = getIds();
      if (!idEquipamento) return alert('Equipamento inválido.');

      if (!G_editId && (!fileInput.files || fileInput.files.length === 0)) {
        alert('Selecione uma foto.'); return;
      }
      if (!tituloInput.value.trim()) { alert('Informe um título.'); return; }
      if (!dateInput.value) { alert('Informe a data.'); return; }
      if (!tipoSelect.value) { alert('Selecione o tipo de foto.'); return; }

      const fd = new FormData();
      fd.append('id_equipamento', idEquipamento);
      if (G_editId) fd.append('id', G_editId);
      if (fileInput.files && fileInput.files[0]) fd.append('foto', fileInput.files[0]);
      fd.append('titulo', tituloInput.value.trim());
      fd.append('data_foto', dateInput.value);
      fd.append('tipo_foto', tipoSelect.value);
      fd.append('descricao', descTxt.value.trim());

      try {
        const r = await fetch('equipamentos/inserir_foto.php', { method: 'POST', body: fd, credentials: 'same-origin' });
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
    const container   = document.querySelector('.container-foto-add');
    const fileInput   = document.getElementById('fotoUpload');
    const previewImg  = document.getElementById('preview-foto');
    const tituloInput = document.getElementById('fotoTitulo');
    const dateInput   = document.getElementById('fotoData');
    const tipoSelect  = document.getElementById('fotoTipo');
    const descTxt     = document.getElementById('fotoDescricao');

    const { mini } = basePaths();

    G_editId = row.id;
    fileInput.disabled = true; // edição sem troca de arquivo (ajuste se quiser permitir)
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
      const fd = new FormData();
      fd.append('id', id);
      const r = await fetch('salas/excluir_foto.php', { method: 'POST', body: fd, credentials: 'same-origin' });
      const j = await r.json();
      if (!j.success) throw new Error(j.msg || 'Falha ao excluir');
      reloadTable();
    } catch (e) {
      alert('Erro ao excluir: ' + e.message);
    }
  }

  function bindSearch(){
    const cont = document.querySelector('#galeria-container-modal');
    if (!cont) return;
    const s = cont.querySelector('.searchBox');
    if (!s) return;
    if (s.dataset.bound === '1') return;
    s.dataset.bound = '1';

    let t = null;
    s.addEventListener('input', ()=>{
      clearTimeout(t);
      t = setTimeout(()=> reloadTable(), 250);
    });
  }

  function initTabela(){
    if (G_inited) return;
    G_inited = true;
    bindEditor();
    bindSearch();
    reloadTable(); // apenas 1 chamada aqui
  }

  // Expor se precisar chamar manualmente
  window.initTabModEquipamentoGaleria = initTabela;

  // Carregar quando a aba de imagens for exibida (SOMENTE init, sem reload extra)
  document.addEventListener('shown.bs.tab', (e)=>{
    const target = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
    if (target === '#aba-imagens') {
      initTabela();
    }
  });

  // Se a aba já estiver ativa ao carregar o script
  if (document.querySelector('#imagens-tab')?.classList.contains('active') ||
      document.querySelector('#aba-imagens')?.classList.contains('show')) {
    initTabela();
  }
})();
