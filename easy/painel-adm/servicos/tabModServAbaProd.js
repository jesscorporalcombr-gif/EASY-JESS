// servicos/tabModServAbaProd.js
(function () {
  'use strict';

  // ---- helpers base
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
  const $  = (s, c = document) => c.querySelector(s);

  const brToFloat = (v) => {
    if (v == null || v === '') return 0;
    const n = parseFloat(String(v).replace(/\./g, '').replace(',', '.'));
    return isNaN(n) ? 0 : n;
    };
  const floatToBR = (n, d = 2) => (n == null || isNaN(n)) ? '' : Number(n).toFixed(d).replace('.', ',');

  const fotoSrcProduto = (f) => {
    if (!f) return '../img/sem-imagem.svg';
    if (f.startsWith('http') || f.startsWith('data:') || f.includes('/')) return f;
    const pasta = (window.PASTA_JS || '').trim();
    return pasta ? `../${pasta}/img/produtos/${f}` : `../img/produtos/${f}`;
  };

  const modal = document.getElementById('modalCadServico');
  if (!modal) return;

  const container = modal.querySelector('#aba-produtos-container');
  if (!container) return;

  // evita reinit se o arquivo for injetado 2x no mesmo modal
  if (container.dataset.inited === '1') return;
  container.dataset.inited = '1';

  const idInput        = modal.querySelector('#frm-id');
  const hiddenIdServ   = container.querySelector('#prod-id_servico');
  let   idServico      = (hiddenIdServ?.value || idInput?.value || '').trim();

  // aguarda o id quando for novo serviço
  if (!idServico) {
    const onSaved = (e) => {
      const novoId = e.detail?.id;
      if (!novoId) return;
      idServico = String(novoId);
      if (idInput)      idInput.value = idServico;
      if (hiddenIdServ) hiddenIdServ.value = idServico;
      init(); // agora pode carregar
    };
    document.addEventListener('servico:salvo', onSaved, { once: true });
    modal.addEventListener('hidden.bs.modal', () => {
      document.removeEventListener('servico:salvo', onSaved);
    }, { once: true });
    return;
  }

  init();

  // ================== INIT ==================
  function init() {
    // elementos da aba
    const tbody        = () => container.querySelector('#tabelaAbaProd tbody'); // sempre re-pega
    const inpBusca     = container.querySelector('#prod-busca');
    const dlSugId      = 'prod-sugestoes';
    const hidIdProd    = container.querySelector('#prod-id_produto');
    const hidIdServProd= container.querySelector('#prod-id_serv_prod');
    const inpUnid      = container.querySelector('#prod-unidade');
    const inpQtd       = container.querySelector('#prod-quantidade');
    const inpCustoUnit = container.querySelector('#prod-custo_unit');
    const inpTotal     = container.querySelector('#prod-total');
    const btnSalvar    = container.querySelector('#btn-prod-salvar');
    const btnCancelar  = container.querySelector('#btn-prod-cancelar');

    const URL_LISTAR   = `servicos/SModServTabProd.php?id_servico=${encodeURIComponent(idServico)}`;
    const URL_CATALOGO = `servicos/LModServProdutosCatalog.php`;
    const URL_UPSERT   = `servicos/UModServTabProd.php`;
    const URL_DELETE   = `servicos/DModServTabProd.php`;

    // estado
    let rows = [];
    let buscaTimer = null;

    // --------- UI helpers ----------
    const limparForm = () => {
      if (hidIdServProd) hidIdServProd.value = '';
      if (hidIdProd)     hidIdProd.value = '';
      if (inpBusca)      inpBusca.value = '';
      if (inpUnid)       inpUnid.value  = '';
      if (inpQtd)        inpQtd.value   = '';
      if (inpCustoUnit)  inpCustoUnit.value = '';
      if (inpTotal)      inpTotal.value = '';
      inpBusca?.focus();
    };

    const calcTotal = () => {
      const q  = DecimalIngles(inpQtd?.value);
      const cu = DecimalIngles(inpCustoUnit?.value);
      console.log('o valor de cu é', cu);
      if (inpTotal) inpTotal.value = DecimalBr(q * cu);

    };

    // --------- catálogo / datalist ----------
    const applyOptionData = (opt) => {
      if (!opt) return;
      if (hidIdProd)    hidIdProd.value    = opt.dataset.id || '';
      if (inpUnid)      inpUnid.value      = opt.dataset.unidade || '';
      if (inpCustoUnit) inpCustoUnit.value = opt.dataset.custo_unitario ? floatToBR(opt.dataset.custo_unitario) : '';
      calcTotal();
    };

    const norm = (s) => {
      const t = (s ?? '').toString().trim();
      try { return t.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase(); }
      catch { return t.toLowerCase(); }
    };

    const pickOptionFromInput = () => {
      const dl = document.getElementById(dlSugId);
      const val  = (inpBusca?.value || '').trim();
      if (!dl) return;
      if (!val) { if (hidIdProd) hidIdProd.value = ''; return; }
      const opts = $$('option', dl);
      let match = opts.find(o => norm(o.value) === norm(val));
      if (!match && opts.length === 1) match = opts[0];
      if (!match) match = opts.find(o => norm(o.value).startsWith(norm(val)));
      if (match) applyOptionData(match); else if (hidIdProd) hidIdProd.value = '';
    };

    inpQtd?.addEventListener('input', calcTotal);
    inpCustoUnit?.addEventListener('input', calcTotal);

    inpBusca?.addEventListener('input', () => {
      pickOptionFromInput();

      const dl = document.getElementById(dlSugId);
      if (dl) dl.innerHTML = '';

      const term = (inpBusca.value || '').trim();
      if (buscaTimer) clearTimeout(buscaTimer);
      if (term.length < 2) return;

      buscaTimer = setTimeout(async () => {
        try {
          const dl2 = document.getElementById(dlSugId);
          if (!dl2) return;

          const resp = await fetch(`${URL_CATALOGO}?q=${encodeURIComponent(term)}`);
          const data = await resp.json();
          const list = Array.isArray(data.rows) ? data.rows : [];

          dl2.innerHTML = '';
          list.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.produto; // texto apresentado
            opt.dataset.id = p.id;
            opt.dataset.unidade = p.unidade || '';
            opt.dataset.custo_unitario = p.custo_unitario ?? '';
            dl2.appendChild(opt);
          });

          if (list.length === 1) applyOptionData(dl2.querySelector('option'));
        } catch (e) {
          console.error('Catálogo:', e);
        }
      }, 300);
    });

    inpBusca?.addEventListener('keydown', (e) => { if (e.key === 'Enter') pickOptionFromInput(); });
    inpBusca?.addEventListener('change',  pickOptionFromInput);
    inpBusca?.addEventListener('blur',    pickOptionFromInput);

    // --------- tabela ----------
    const renderRows = () => {
      const tb = tbody();
      if (!tb) return;

      tb.innerHTML = '';
      if (!rows.length) {
        tb.innerHTML = `<tr><td colspan="7" class="text-muted">Nenhum produto vinculado.</td></tr>`;
        return;
      }

      rows.forEach((r, idx) => {
        const tr = document.createElement('tr');
        tr.dataset.idx = String(idx);

        const tdFoto = document.createElement('td');
        const img    = document.createElement('img');
        img.src = fotoSrcProduto(r.foto_produto);
        img.alt = 'Foto';
        Object.assign(img.style, { width:'40px', height:'40px', objectFit:'cover', borderRadius:'6px' });
        tdFoto.appendChild(img);

        const tdNome = document.createElement('td'); tdNome.textContent = r.produto ?? '';
        const tdUn   = document.createElement('td'); tdUn.textContent   = r.unidade ?? '';
        const tdQtd  = document.createElement('td'); tdQtd.className    = 'text-end'; tdQtd.textContent = floatToBR(r.quantidade);
        const tdCU   = document.createElement('td'); tdCU.className     = 'text-end'; tdCU.textContent  = 'R$ ' + floatToBR(r.custo_unitario);
        const tdTot  = document.createElement('td'); tdTot.className    = 'text-end'; tdTot.textContent = 'R$ ' + floatToBR((r.quantidade||0)*(r.custo_unitario||0));

        const tdAcoes = document.createElement('td');
        tdAcoes.className = 'text-center';
        tdAcoes.innerHTML = `
          <button type="button"  class="btn btn-sm me-1 btn-outline-primary btn-acoes-tabelas-modal btn-edit ">
            <i class="bi bi-pencil ico-act-tab-mod"></i>
          </button>
          <button type="button" class="btn btn-sm btn-outline-danger btn-acoes-tabelas-modal btn-del">
            <i  class="bi bi-trash ico-act-tab-mod"></i>
          </button>
        `;

        tr.addEventListener('dblclick', () => editarLinha(idx));
        tdAcoes.querySelector('.btn-edit').addEventListener('click', () => editarLinha(idx));
        tdAcoes.querySelector('.btn-del').addEventListener('click',  () => excluirLinha(idx));

        tr.appendChild(tdFoto);
        tr.appendChild(tdNome);
        tr.appendChild(tdUn);
        tr.appendChild(tdQtd);
        tr.appendChild(tdCU);
        tr.appendChild(tdTot);
        tr.appendChild(tdAcoes);

        tb.appendChild(tr);
      });
    };

    const editarLinha = (i) => {
      const r = rows[i]; if (!r) return;
      if (hidIdServProd) hidIdServProd.value = r.id_serv_prod || '';
      if (hidIdProd)     hidIdProd.value     = r.id_produto   || '';
      if (inpBusca)      inpBusca.value      = r.produto      || '';
      if (inpUnid)       inpUnid.value       = r.unidade      || '';
      if (inpQtd)        inpQtd.value        = floatToBR(r.quantidade);
      if (inpCustoUnit)  inpCustoUnit.value  = floatToBR(r.custo_unitario);
      calcTotal();
      inpBusca?.focus();
    };

    const excluirLinha = async (i) => {
      const r = rows[i];
      if (!r || !r.id_serv_prod) return;
      if (!confirm('Excluir este produto do serviço?')) return;

      try {
        const fd = new FormData();
        fd.append('id_serv_prod', r.id_serv_prod);
        const resp = await fetch(URL_DELETE, { method: 'POST', body: fd });
        const data = await resp.json();
        if (!resp.ok || !data.ok) {
          alert(data.error || 'Não foi possível excluir.');
          return;
        }
        rows.splice(i, 1);
        renderRows();
      } catch (e) {
        console.error(e);
        alert('Falha ao excluir.');
      }
    };

    btnCancelar?.addEventListener('click', limparForm);

    btnSalvar?.addEventListener('click', async () => {
      const id_serv_prod   = (hidIdServProd?.value || '').trim() || null;
      const id_produto     = (hidIdProd?.value     || '').trim();
      if (!id_produto) { alert('Selecione um produto do catálogo.'); inpBusca?.focus(); return; }

      const quantidade     = brToFloat(inpQtd?.value);
      const custo_unitario = brToFloat(inpCustoUnit?.value);

      try {
        const payload = {
          id_servico: idServico,
          item: { id_serv_prod, id_produto, quantidade, custo_unitario }
        };
        const resp = await fetch(URL_UPSERT, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await resp.json();
        if (!resp.ok || !data.ok) {
          alert((data.error || 'Não foi possível salvar este item.') + (data.detail ? '\n' + data.detail : ''));
          return;
        }

        const r = data.row;
        const ix = rows.findIndex(x => x.id_serv_prod == r.id_serv_prod);
        if (ix >= 0) rows[ix] = r; else rows.push(r);
        renderRows();
        limparForm();
      } catch (e) {
        console.error(e);
        alert(e?.message || 'Falha ao salvar o item.');
      }
    });

    // load inicial
    (async () => {
      try {
        const resp = await fetch(URL_LISTAR, { cache: 'no-store' });
        const data = await resp.json();
        rows = Array.isArray(data.rows) ? data.rows : [];
        renderRows();
      } catch (e) {
        console.error('Listar produtos:', e);
        const tb = tbody();
        if (tb) tb.innerHTML = `<tr><td colspan="7" class="text-muted">Falha ao carregar produtos do serviço.</td></tr>`;
      }
    })();
  }
})();
