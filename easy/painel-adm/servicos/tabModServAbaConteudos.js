// servicos/tabModServAbaConteudos.js (reescrito: init 1x por abertura, sem acumular listeners)
(function () {
  const modal = document.getElementById('modalCadServico');
  if (!modal) return;

  // Limpa instância anterior desta guia (evita pilha de listeners entre aberturas)
  if (typeof modal.__conteudosCleanup === 'function') {
    try { modal.__conteudosCleanup(); } catch (_) {}
  }

  // ---------- estado local ----------
  let C_rows = [];
  let C_filtroTipo = ''; // '', 'TERMO', 'POP', 'TREINAMENTO', 'ARQUIVO', 'LINK'

  // ---------- registry de listeners para cleanup ----------
  const handlers = [];
  function on(el, ev, fn, opts) {
    if (!el) return;
    el.addEventListener(ev, fn, opts);
    handlers.push([el, ev, fn, opts]);
  }
  function cleanup() {
    handlers.forEach(([el, ev, fn, opts]) => {
      try { el.removeEventListener(ev, fn, opts); } catch (_) {}
    });
    handlers.length = 0;
  }
  modal.__conteudosCleanup = cleanup; // expõe ao modal para ser chamado ao fechar

  // ---------- helpers ----------
  const pane = modal.querySelector('#aba-conteudos');
  if (!pane) return;

  const URL_LISTAR = 'servicos/SModServConteudos.php';
  const URL_UPSERT = 'servicos/UModServConteudos.php';
  const URL_DELETE = 'servicos/DModServConteudos.php';

  function getIdServico() {
    return (modal.querySelector('#ct-id_servico')?.value || modal.querySelector('#frm-id')?.value || '').trim();
  }

  function formatDateBR(iso) {
    if (!iso) return '';
    const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(iso);
    return m ? `${m[3]}/${m[2]}/${m[1]}` : iso;
  }

  function extrairExtensao(nome) {
    if (!nome) return '';
    const i = nome.lastIndexOf('.');
    return i >= 0 ? nome.slice(i + 1).toLowerCase() : '';
  }

  function getHref(r) {
    const link = (r.url || '').trim();
    if (link) return link;
    const href = (r.href_arquivo || r.arquivo_url || '').trim();
    if (href) return href;
    return '';
  }

  // ---------- AJAX ----------
  async function carregarLista() {
    const id = getIdServico();
    if (!id) return;

    const q = C_filtroTipo ? `&tipo=${encodeURIComponent(C_filtroTipo)}` : '';
    const resp = await fetch(`${URL_LISTAR}?id_servico=${encodeURIComponent(id)}${q}`, {
      cache: 'no-store',
      credentials: 'same-origin'
    });
    const data = await resp.json();
    C_rows = Array.isArray(data.rows) ? data.rows : [];
    renderTabela();
  }

  async function excluirRow(id) {
    const fd = new FormData();
    fd.append('id', id);
    const resp = await fetch(URL_DELETE, { method: 'POST', body: fd, credentials: 'same-origin' });
    const data = await resp.json();
    if (!resp.ok || !data.ok) throw new Error(data.error || 'Não foi possível excluir');
  }

  async function upsertRow(payload, file) {
    const fd = new FormData();
    Object.entries(payload).forEach(([k, v]) => fd.append(k, v == null ? '' : v));
    if (file) fd.append('file', file);
    const resp = await fetch(URL_UPSERT, { method: 'POST', body: fd, credentials: 'same-origin' });
    const data = await resp.json();
    if (!resp.ok || !data.ok) throw new Error(data.error || 'Falha ao salvar');
    return data; // ideal: { ok:true, id, row }
  }

  // ---------- render ----------
  function renderTabela() {
    const tbody = pane.querySelector('#tabela-conteudos tbody');
    if (!tbody) return;
    tbody.innerHTML = '';

    const lista = C_filtroTipo
      ? C_rows.filter(r => String(r.tipo || '').toUpperCase() === C_filtroTipo)
      : C_rows;

    if (!lista.length) {
      tbody.innerHTML = `<tr><td colspan="5" class="text-muted">Nenhum conteúdo cadastrado.</td></tr>`;
      return;
    }

    lista.forEach(r => {
      const tr = document.createElement('tr');

      // ícone
      const tdIcon = document.createElement('td');
      const ext = extrairExtensao(r.arquivo || r.arquivo_nome);
      const ico = (window.FileIconRegistry && window.FileIconRegistry.get(ext)) || { icon: 'bi-file-earmark-fill', color: 'text-muted' };
      tdIcon.innerHTML = `<i class="bi ${ico.icon} ${ico.color} me-2" title="${ext || 'arquivo'}"></i>`;
      tr.appendChild(tdIcon);

      // título (linkável)
      const tdTitulo = document.createElement('td');
      const href = getHref(r);
      if (href) {
        const a = document.createElement('a');
        a.href = href; a.target = '_blank'; a.rel = 'noopener';
        a.textContent = r.titulo || '';
        tdTitulo.appendChild(a);
      } else {
        tdTitulo.textContent = r.titulo || '';
      }
      tr.appendChild(tdTitulo);

      // tipo
      const tdTipo = document.createElement('td');
      tdTipo.textContent = r.tipo || '';
      tr.appendChild(tdTipo);

      // data
      const tdData = document.createElement('td');
      tdData.textContent = formatDateBR(r.data_referencia || r.data || '');
      tr.appendChild(tdData);

      // ações
      const tdAcoes = document.createElement('td');
      tdAcoes.className = 'text-center';
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
      on(bEdit, 'click', () => editar(r));
      on(bDel,  'click', () => {
        if (!confirm('Excluir este conteúdo?')) return;
        excluirRow(r.id)
          .then(() => {
            // remove local e re-renderiza
            C_rows = C_rows.filter(x => String(x.id) !== String(r.id));
            renderTabela();
          })
          .catch(e => alert('Erro ao excluir: ' + (e.message || e)));
      });
      tr.appendChild(tdAcoes);

      // clique na linha → abre link (se houver), sem interferir nos botões/anchors
      const hrefRow = getHref(r);
      if (hrefRow) {
        on(tr, 'click', (ev) => {
          const isActionBtn = ev.target.closest('td') === tdAcoes || ev.target.closest('button');
          const isAnchor    = ev.target.closest('a');
          if (isActionBtn || isAnchor) return;
          window.open(hrefRow, '_blank', 'noopener');
        });
      }

      // dblclick = editar
      on(tr, 'dblclick', () => editar(r));

      tbody.appendChild(tr);
    });
  }

  // ---------- formulário ----------
  function updateConditionalBlocks() {
    const tipo = (pane.querySelector('#ct-tipo')?.value || '').toUpperCase();
    const fileBlock = pane.querySelector('.ct-bloco.file-required');
    const linkBlock = pane.querySelector('.ct-bloco.link');
    const treBlock  = pane.querySelector('.ct-bloco.treinamento');

    if (fileBlock) fileBlock.style.display = (['TERMO', 'POP', 'ARQUIVO'].includes(tipo)) ? '' : 'none';
    if (linkBlock) linkBlock.style.display = (tipo === 'LINK') ? '' : 'none';
    if (treBlock)  treBlock.style.display  = (tipo === 'TREINAMENTO') ? '' : 'none';
  }

  function resetForm() {
    const form = pane.querySelector('#form-conteudo');
    if (!form) return;
    form.style.display = 'none';

    pane.querySelector('#ct-id').value = '';
    pane.querySelector('#ct-id_servico').value = getIdServico() || '';
    pane.querySelector('#ct-tipo').value = 'TERMO';
    pane.querySelector('#ct-titulo').value = '';
    pane.querySelector('#ct-data').value = '';
    pane.querySelector('#ct-obrigatorio').checked = false;
    pane.querySelector('#ct-desc').value = '';
    pane.querySelector('#ct-url').value = '';
    pane.querySelector('#ct-carga').value = '';
    pane.querySelector('#ct-validade').value = '';
    pane.querySelector('#ct-tags').value = '';

    const f  = pane.querySelector('#ct-file');
    const fn = pane.querySelector('#ct-file-name');
    const fc = pane.querySelector('#ct-file-current');
    const fl = pane.querySelector('#ct-file-link');
    if (f)  f.value = '';
    if (fn) fn.value = '';
    if (fc) fc.style.display = 'none';
    if (fl) fl.removeAttribute('href');

    updateConditionalBlocks();
  }

  function showForm() {
    const form = pane.querySelector('#form-conteudo');
    if (form) form.style.display = 'block';
  }

  function editar(r) {
    pane.querySelector('#ct-id').value          = r.id || '';
    pane.querySelector('#ct-id_servico').value  = getIdServico() || '';
    pane.querySelector('#ct-tipo').value        = r.tipo || 'TERMO';
    pane.querySelector('#ct-titulo').value      = r.titulo || '';
    pane.querySelector('#ct-data').value        = (r.data_referencia || r.data || '').slice(0,10);
    pane.querySelector('#ct-obrigatorio').checked = String(r.obrigatorio) === '1';
    pane.querySelector('#ct-desc').value        = r.descricao || '';
    pane.querySelector('#ct-url').value         = r.url || '';
    pane.querySelector('#ct-carga').value       = (r.carga_horaria ?? '');
    pane.querySelector('#ct-validade').value    = (r.validade_dias ?? '');
    pane.querySelector('#ct-tags').value        = r.tags || '';

    const fc = pane.querySelector('#ct-file-current');
    const fl = pane.querySelector('#ct-file-link');
    if (r.arquivo_url) {
      fc.style.display = '';
      fl.textContent = r.arquivo_nome || 'arquivo atual';
      fl.href = r.arquivo_url;
    } else {
      fc.style.display = 'none';
      fl.removeAttribute('href');
    }

    const f  = pane.querySelector('#ct-file');
    const fn = pane.querySelector('#ct-file-name');
    if (f)  f.value = '';
    if (fn) fn.value = '';

    updateConditionalBlocks();
    showForm();
  }

  // ---------- binds ----------
  function binds() {
    // filtro por tipo (mantém comportamento atual: consulta o backend)
    pane.querySelectorAll('.btn-filter').forEach(btn => {
      on(btn, 'click', () => {
        pane.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        C_filtroTipo = (btn.dataset.tipo || '').toUpperCase();
        carregarLista(); // mantém como estava (sem apenas re-render local)
      });
    });

    // novo
    const btnNovo = pane.querySelector('#btn-novo-conteudo');
    on(btnNovo, 'click', () => { resetForm(); showForm(); });

    // cancelar
    const btnCancel = pane.querySelector('#ct-cancelar');
    on(btnCancel, 'click', resetForm);

    // tipo muda blocos condicionais
    const selTipo = pane.querySelector('#ct-tipo');
    on(selTipo, 'change', updateConditionalBlocks);

    // arquivo
    const fileBtn  = pane.querySelector('#ct-file-btn');
    const fileInp  = pane.querySelector('#ct-file');
    const fileName = pane.querySelector('#ct-file-name');
    on(fileBtn, 'click', () => fileInp?.click());
    on(fileInp, 'change', () => {
      fileName.value = fileInp.files && fileInp.files[0] ? fileInp.files[0].name : '';
    });

    // salvar
    const btnSalvar = pane.querySelector('#ct-salvar');
    on(btnSalvar, 'click', async () => {
      const id_servico = getIdServico();
      if (!id_servico) { alert('Serviço inválido.'); return; }

      const id           = (pane.querySelector('#ct-id').value || '').trim();
      const tipo         = (pane.querySelector('#ct-tipo').value || '').trim();
      const titulo       = (pane.querySelector('#ct-titulo').value || '').trim();
      const data_ref     = (pane.querySelector('#ct-data').value || '').trim();
      const obrigatorio  = pane.querySelector('#ct-obrigatorio').checked ? 1 : 0;
      const descricao    = (pane.querySelector('#ct-desc').value || '').trim();
      const url          = (pane.querySelector('#ct-url').value || '').trim();
      const carga        = (pane.querySelector('#ct-carga').value || '').trim();
      const validade     = (pane.querySelector('#ct-validade').value || '').trim();
      const tags         = (pane.querySelector('#ct-tags').value || '').trim();

      if (!titulo)  { alert('Informe o título.'); return; }
      if (!data_ref){ alert('Informe a data.'); return; }

      const tUpper = tipo.toUpperCase();
      const fInp   = pane.querySelector('#ct-file');

      if ((['TERMO','POP','ARQUIVO'].includes(tUpper)) && (!id) && (!fInp.files || fInp.files.length === 0)) {
        alert('Selecione um arquivo.'); return;
      }
      if (tUpper === 'LINK' && !url) {
        alert('Informe a URL.'); return;
      }

      const payload = {
        id_servico,
        tipo,
        titulo,
        data_referencia: data_ref,
        obrigatorio,
        descricao,
        url,
        carga_horaria: carga,
        validade_dias: validade,
        tags
      };
      if (id) payload.id = id;

      try {
        const result = await upsertRow(payload, (fInp.files && fInp.files[0]) ? fInp.files[0] : null);
        resetForm();

        if (!id) {
          // INSERT → recarrega do backend (mantém comportamento simples e consistente)
          await carregarLista();
        } else {
          // UPDATE → atualiza localmente se backend devolver a linha; se não, recarrega
          const row = result.row;
          if (row && row.id) {
            const idx = C_rows.findIndex(r => String(r.id) === String(row.id));
            if (idx >= 0) {
              C_rows[idx] = { ...C_rows[idx], ...row };
              renderTabela();
            } else {
              // segurança: se não achar, faz um refresh completo
              await carregarLista();
            }
          } else {
            await carregarLista();
          }
        }
      } catch (e) {
        alert('Erro ao salvar: ' + (e.message || e));
      }
    });
  }

  // ---------- init (1x por abertura) ----------
  function initConteudos() {
    if (pane.dataset.cInit === '1') return;
    pane.dataset.cInit = '1';
    binds();
    carregarLista();
  }

  // Inicializa quando a aba "Conteúdos" for ativada (ancorado no modal)
  const onTabShown = (e) => {
    const target = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
    if (target === '#aba-conteudos') {
      if (pane.dataset.cInit !== '1') initConteudos();
    }
  };
  on(modal, 'shown.bs.tab', onTabShown);

  // Se a aba já estiver ativa quando este script carregar
  const tab = modal.querySelector('#conteudos-tab');
  if (tab?.classList.contains('active') || pane?.classList.contains('show')) {
    if (pane.dataset.cInit !== '1') initConteudos();
  }

  // cleanup ao fechar o modal (remove TODOS os listeners desta instância)
  on(modal, 'hidden.bs.modal', () => {
    cleanup();
    delete pane.dataset.cInit;
    delete modal.__conteudosCleanup;
  }, { once: true });
})();
