(function(){
  'use strict';
  const $  = (s,r=document)=>r.querySelector(s);
  const $$ = (s,r=document)=>Array.from(r.querySelectorAll(s));
  const fmtDT = s => s ? s.replace('T',' ').slice(0,19) : '';

  async function fetchRows(){
    const form_id = Number($('#selFormAnamnese')?.value || 0);
    if(!form_id) return { ok:true, rows:[], total:0 };

    const params = new URLSearchParams({
      form_id,
      paciente_id: ($('#inpPacienteId')?.value||'').trim(),
      data_inicial: $('#dtIni')?.value || '',
      data_final:   $('#dtFim')?.value || '',
      status: $('#selStatus')?.value || '',
      source: $('#selOrigem')?.value || ''
    }).toString();

    const res = await fetch('tecnico/anamnese/searchModAnamneses.php?'+params);
    return res.json();
  }

  function badgeStatus(st){
    const mapTxt = { draft:'Rascunho', pending:'Pendente', validated:'Validado', invalidated:'Invalidado' };
    const mapCls = { draft:'badge-draft', pending:'badge-pending', validated:'badge-ok', invalidated:'badge-bad' };
    return `<span class="badge ${mapCls[st]||'badge'}">${mapTxt[st]||st||''}</span>`;
  }
  function badgeSource(src){
    return `<span class="badge badge-src">${src==='link'?'Link':'Interno'}</span>`;
  }
  function fileIcon(has){ return has ? '📎' : '—'; }
  function avatar(url){ return `<img src="${url||'forms/img/avatar-default.png'}" alt="" style="width:38px;height:38px;border-radius:50%;object-fit:cover">`; }

  function paginate(arr, page, pageSize){
    const total = arr.length;
    const pages = Math.max(1, Math.ceil(total/pageSize));
    const p = Math.min(Math.max(1,page), pages);
    const start = (p-1)*pageSize;
    return { page:p, pages, total, rows: arr.slice(start, start+pageSize) };
  }
  function filterClient(rows, term){
    if(!term) return rows;
    const q = term.toLowerCase();
    return rows.filter(r =>
      String(r.anamnese_id).includes(q) ||
      (r.paciente_nome||'').toLowerCase().includes(q) ||
      (r.status||'').toLowerCase().includes(q) ||
      (r.source||'').toLowerCase().includes(q) ||
      (r.created_at||'').toLowerCase().includes(q)
    );
  }

  function renderBody(tbody, rows, onAction){
    tbody.innerHTML='';
    if(!rows.length){
      tbody.innerHTML = `<tr><td colspan="7" class="muted">Nenhum registro.</td></tr>`;
      return;
    }
    rows.forEach(r=>{
      const tr = document.createElement('tr');
      tr.dataset.responseId = r.anamnese_id;
      tr.innerHTML = `
        <td>${avatar(r.paciente_foto_url)}</td>
        <td>${r.paciente_nome||''}</td>
        <td>${badgeStatus(r.status)}</td>
        <td>${badgeSource(r.source)}</td>
        <td>${fmtDT(r.created_at)}</td>
        <td style="text-align:center">${fileIcon(r.has_attachment)}</td>
        <td class="actions" style="display:flex;gap:6px;flex-wrap:wrap">
          <button class="btn btn-light" data-act="editar">Editar</button>
          <button class="btn btn-success" data-act="validar">Validar</button>
          <button class="btn btn-warning" data-act="invalidar">Invalidar</button>
          <button class="btn btn-light" data-act="imprimir">Imprimir</button>
          <button class="btn btn-light" data-act="upload">Upload</button>
          <button class="btn btn-danger" data-act="excluir">Excluir</button>
        </td>`;
      tr.querySelectorAll('[data-act]').forEach(b=>{
        b.addEventListener('click', ()=> onAction && onAction(b.dataset.act, r));
      });
      tbody.appendChild(tr);
    });
  }

  async function loadAndRender(onAction){
    const table = $('#tbAnamneses');
    if(!table){ console.warn('Tabela #tbAnamneses não encontrada'); return; }
    const tbody = table.tBodies[0] || table.createTBody();

    const out = await fetchRows();
    if(!out.ok){ tbody.innerHTML = `<tr><td colspan="7" class="error">${out.error||'Erro'}</td></tr>`; return; }

    const all = out.rows || [];
    const pageSizeEl = $('#selPageSize');
    const searchEl = $('#txtBusca');
    const prev = $('#btnPrev'), next = $('#btnNext'), info = $('#lblPageInfo');

    let page = 1;
    let pageSize = parseInt(pageSizeEl?.value||'20',10);
    let term = (searchEl?.value||'').trim();

    function refresh(){
      const filtered = filterClient(all, term);
      const {page:pg, pages, total, rows} = paginate(filtered, page, pageSize);
      page=pg;
      renderBody(tbody, rows, onAction);
      if(info) info.textContent = `${page}/${pages} — ${total} registros`;
      if(prev) prev.disabled = (page<=1);
      if(next) next.disabled = (page>=pages);
    }

    prev?.addEventListener('click', ()=>{ page=Math.max(1,page-1); refresh(); });
    next?.addEventListener('click', ()=>{ page=page+1; refresh(); });
    pageSizeEl?.addEventListener('change', ()=>{ pageSize=parseInt(pageSizeEl.value,10)||20; page=1; refresh(); });
    searchEl?.addEventListener('input', ()=>{ term=searchEl.value.trim(); page=1; refresh(); });

    refresh();
  }

  window.TabelaAnamneses = {
    async render(onAction){
      // botão “Aplicar” e mudanças em filtros recarregam a tabela
      const rebind = ()=>{
        loadAndRender(onAction);
      };
      $('#btnAplicarFiltros')?.addEventListener('click', rebind);
      $('#selFormAnamnese')?.addEventListener('change', rebind);
      $('#inpPacienteId')?.addEventListener('change', rebind);
      $('#dtIni')?.addEventListener('change', rebind);
      $('#dtFim')?.addEventListener('change', rebind);
      $('#selStatus')?.addEventListener('change', rebind);
      $('#selOrigem')?.addEventListener('change', rebind);

      // primeira carga
      await loadAndRender(onAction);
    },
    reload(onAction){ return loadAndRender(onAction); }
  };
})();
