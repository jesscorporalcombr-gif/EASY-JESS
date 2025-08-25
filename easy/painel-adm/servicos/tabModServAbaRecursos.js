// servicos/tabModServAbaRecursos.js
(function () {
  // ---------- helpers ----------
  const qs  = (s, c = document) => c.querySelector(s);
  const qsa = (s, c = document) => Array.from(c.querySelectorAll(s));
  const norm = (s) => (s ?? '').toString().trim();
    window.__recursosInited = window.__recursosInited || new Set();




  function fotoSrc(file, subfolder) {
    if (!file) return '../img/sem-imagem.svg';
    if (file.startsWith('http') || file.startsWith('data:') || file.includes('/')) return file;
    const pasta = (pastaFiles || '').trim();
    return pasta ? `../${pasta}/img/${subfolder}/${file}` : `../img/${subfolder}/${file}`;
  }

  // ---------- controller genérico para um bloco (salas|equipamentos) ----------
  function RecursosBlock(cfg) {
    const root = qs('#' + cfg.containerId);
    if (!root) return;

    // ID DINÂMICO (NÃO capture!)
    const getIdServ = () => {
      // 1º tenta o hidden da própria aba ( #rec-salas-id_servico / #rec-equip-id_servico )
      const vLocal = root.querySelector('input[id$="id_servico"]')?.value?.trim();
      if (vLocal) return vLocal;
      // 2º fallback para o hidden do formulário principal
      const vForm  = document.querySelector('#frm-id')?.value?.trim();
      return vForm || '';
    }




    // Se ainda não existe ID (cadastro novo), espere o evento de salvamento
    const sid = getIdServ();
    if (!sid) {
      const onSaved = (ev) => {
        const novoId = String(ev.detail?.id || '').trim();
        if (!novoId) return;

        // 1) Escreve no hidden global do form
        const frmId = document.querySelector('#frm-id');
        if (frmId) frmId.value = novoId;

        // 2) Escreve no hidden local da aba (salas/equip)
        const hidLocal = root.querySelector('input[id$="id_servico"]');
        if (hidLocal) hidLocal.value = novoId;

        // 3) Agora reentra DEPOIS que os outros listeners também rodarem
        document.removeEventListener('servico:salvo', onSaved);
        setTimeout(() => RecursosBlock(cfg), 0);
      };
      document.addEventListener('servico:salvo', onSaved, { once: true });
      return;
    }

    // Evita reinit para o MESMO serviço
    if (root.dataset.boundFor === sid) return;
    root.dataset.boundFor = sid;
     // garante que o hidden local receba o ID (caso ainda esteja vazio)
    const hidLocal = root.querySelector('input[id$="id_servico"]');
     if (hidLocal && !hidLocal.value) hidLocal.value = sid;
    const hidLink   = qs(`#${cfg.containerId} input[id$="id_link"]`);
    const hidRecurso= qs(`#${cfg.containerId} input[id$="id_recurso"]`);
    const sel       = qs(`#${cfg.containerId} select[id$="select"]`);
    const tbody     = qs(`#${cfg.containerId} .rec-rows`);
    const btnSalvar = qs(`#${cfg.containerId} button[id$="salvar"]`);
    const btnCancelar = qs(`#${cfg.containerId} button[id$="cancelar"]`);

    const URL_LIST = () =>
    `servicos/SModServTabRecurso.php?tipo=${cfg.tipo}&id_servico=${encodeURIComponent(getIdServ())}`;
    const URL_SAVE = `servicos/UModServTabRecurso.php`;
    const URL_DEL  = `servicos/DModServTabRecurso.php`;

    let rows = []; // vínculos atuais

    // ----- catálogo em memória vindo do PHP do modal -----
    function getCatalog() {
      return cfg.tipo === 'salas'
        ? (window.CATALOGO_SALAS || [])
        : (window.CATALOGO_EQUIPAMENTOS || []);
    }

    // Reconstrói o <select>, removendo os itens já usados (exceto o da edição)
        function rebuildSelect() {
        const catalog = getCatalog();
        const usados = new Set(rows.map(r => Number(r.id_recurso)));

        const current = sel.value;
        sel.innerHTML = '<option value="">Selecione...</option>';

        catalog.forEach(item => {
            const id = Number(item.id);
            if (!usados.has(id)) {
            const opt = document.createElement('option');
            opt.value = String(id);
            opt.textContent = item.nome;
            sel.appendChild(opt);
            }
        });

        if (qsa('option', sel).some(o => o.value === current)) {
            sel.value = current;
        }
        }

    function limparForm() {
      hidLink.value = '';
      hidRecurso.value = '';
      sel.value = '';
      sel.focus();
    }

    // ----- render tabela -----
    function render() {
      tbody.innerHTML = '';
      if (!rows.length) {
        tbody.innerHTML = `<tr><td colspan="3" class="text-muted">Nenhum ${cfg.tipo.slice(0, -1)} vinculado.</td></tr>`;
        return;
      }
      rows.forEach((r, i) => {
        const tr = document.createElement('tr');
        tr.dataset.idx = String(i);

        const tdFoto = document.createElement('td');
        const img = document.createElement('img');
        img.src = fotoSrc(r.foto_recurso, cfg.pastaImgSub);
        img.alt = 'Foto';
        img.style.width = '40px';
        img.style.height = '40px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '6px';
        tdFoto.appendChild(img);

        const tdNome = document.createElement('td');
        tdNome.textContent = r.recurso || '';

        const tdAcoes = document.createElement('td');
        tdAcoes.className = 'text-center';
        tdAcoes.innerHTML = `
          <button type="button" class="btn btn-sm btn-outline-danger btn-acoes-tabelas-modal btn-del" title="Excluir">
            <i class="bi bi-trash  ico-act-tab-mod"></i>
          </button>
        `;
     
        tdAcoes.querySelector('.btn-del').addEventListener('click', () => excluir(i));
   

        tr.appendChild(tdFoto);
        tr.appendChild(tdNome);
        tr.appendChild(tdAcoes);
        tbody.appendChild(tr);
      });
    }


    async function excluir(i) {
      const r = rows[i];
      if (!r) return;
      if (!confirm('Excluir este vínculo?')) return;
      try {
        const fd = new FormData();
        fd.append('tipo', cfg.tipo);
        fd.append('id_link', r.id_link);
        const resp = await fetch(URL_DEL, { method: 'POST', body: fd });
        const data = await resp.json();
        if (!resp.ok || !data.ok) { alert('Não foi possível excluir.'); return; }
        rows.splice(i, 1);
        render();
        rebuildSelect();
      } catch (e) {
        console.error(e);
        alert('Falha ao excluir.');
      }
    }

    btnCancelar?.addEventListener('click', () => {
      limparForm();
      rebuildSelect();
    });

    btnSalvar?.addEventListener('click', async () => {
    let idServ = getIdServ();
    if (!idServ) {
      // tenta sincronizar do #frm-id -> hidden local
      const fid = document.querySelector('#frm-id')?.value?.trim() || '';
      if (fid) {
        const hidLocal = root.querySelector('input[id$="id_servico"]');
        if (hidLocal) hidLocal.value = fid;
        idServ = fid;
      }
    }
    if (!idServ) { alert('Salve o cadastro antes.'); return; }
  
      const id_link = norm(hidLink.value) || null;
      const id_recurso = norm(sel.value);
      if (!id_recurso) { alert(`Selecione ${cfg.tipo === 'salas' ? 'uma sala' : 'um equipamento'}.`); sel.focus(); return; }

      // mantém compatibilidade com backend (hidRecurso não é necessário, mas não atrapalha)
      hidRecurso.value = id_recurso;

      try {
        const resp = await fetch(URL_SAVE, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ tipo: cfg.tipo, id_servico: idServ, item: { id_link, id_recurso } })
        });
        const data = await resp.json();
        if (!resp.ok || !data.ok) { console.error(data); alert('Não foi possível salvar.'); return; }

        const r = data.row;
        const ix = rows.findIndex(x => x.id_link == r.id_link);
        if (ix >= 0) rows[ix] = r; else rows.push(r);

        render();
        rebuildSelect();
        limparForm();
      } catch (e) {
        console.error(e);
        alert('Falha ao salvar.');
      }
    });

    // load inicial
    (async function load() {
      try {
        const idServ = getIdServ();
        if (!idServ) return;
        const resp = await fetch(URL_LIST());

        const data = await resp.json();
        rows = Array.isArray(data.rows) ? data.rows : [];
        render();
        rebuildSelect();
      } catch (e) {
        console.error('listar', cfg.tipo, e);
        tbody.innerHTML = `<tr><td colspan="3" class="text-muted">Falha ao carregar.</td></tr>`;
        rebuildSelect();
      }
    })();
    // Marca como inicializado (agora sim, com ID válido)
    root.dataset.inited = '1';
  }

  // ---------- init 2 blocos ----------
   
 function TabRecursosInit() {
   RecursosBlock({ tipo: 'salas',        containerId: 'aba-recursos-salas', pastaImgSub: 'salas' });
   RecursosBlock({ tipo: 'equipamentos', containerId: 'aba-recursos-equip', pastaImgSub: 'equipamentos' });
 }
 window.TabRecursosInit = TabRecursosInit;

 // Bind dos eventos apenas UMA vez
 (function bindRecursosOnce(){
   if (window.__recursosHandlersBound) return;
   window.__recursosHandlersBound = true;

   // Toda vez que o modal de serviço for mostrado, inicializa os blocos
   document.addEventListener('shown.bs.modal', function(e){
     if (e.target && e.target.id === 'modalCadServico') {
       TabRecursosInit();
     }
   });

   // Se o usuário trocar para a aba "Recursos" depois, garantimos init também
   document.addEventListener('shown.bs.tab', function(e){
     const target = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
     if (target === '#aba-recursos') {
       TabRecursosInit();
     }
   });
 })();

 // Fallback: se os containers já estiverem no DOM (caso o modal seja injetado já aberto)
 if (document.getElementById('aba-recursos-salas') || document.getElementById('aba-recursos-equip')) {
   TabRecursosInit();
 }


})();
