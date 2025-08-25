(function(){
  'use strict';

  // ---------- Estado central ----------
  const elMain   = document.querySelector('.ecfb-layout');
  
const ctxTmp = window.__ECFB_CONTEXT__ || {};
const storageKey = (elMain?.dataset?.storageKey) || `EC_FORM_BUILDER_${ctxTmp.form_id || 'new'}_${ctxTmp.version_id || 'draft'}`;

  /** Estado do schema */
  const state = {
    meta: { title: '', description: '', type: 'anamnese', version: 1 },
    sections: []
  };

  // util simples
  const $  = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));
  const uid = (p) => `${p}_${Math.random().toString(36).slice(2,9)}`;

  // ---------- Bind topo/meta ----------
  const formTitle = $('#formTitle');
  const formDesc  = $('#formDesc');
  const formType  = $('#formType');

  formTitle.addEventListener('input', e => { state.meta.title = e.target.value; autosave(); });
  formDesc.addEventListener('input',  e => { state.meta.description = e.target.value; autosave(); });
  formType.addEventListener('change', e => { state.meta.type = e.target.value; autosave(); });

  // ---------- Seções ----------
  const sectionsWrap = $('#sections');
  const canvasEmpty  = $('#canvasEmpty');
  const btnAddSection = $('#btnAddSection');

  btnAddSection.addEventListener('click', () => {
    const s = { id: uid('sec'), title: 'Nova seção', layout: 'one-column', fields: [] };
    state.sections.push(s);
    render(); autosave();
  });

  // ---------- Catálogo de campos (botões da sidebar) ----------
  $$('.ecfb-field-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      const sec = state.sections[state.sections.length - 1];
      if(!sec){ alert('Crie uma seção antes de adicionar campos.'); return; }
      sec.fields.push(createField(type));
      render(); autosave();
    });
  });




  function createField(type){
    const base = { id: uid('f'), type, label: labelFromType(type), required: false, visible: true,
      placeholder: '', help: null, default: null, validations: {}, options: [] };
    switch(type){
      case 'text':     base.validations = { minLen:0, maxLen:300, pattern:null }; break;
      case 'textarea': base.validations = { minLen:0, maxLen:2000 }; break;
      case 'number':   base.validations = { min:null, max:null, step:null }; break;
      case 'date':     base.validations = { minDate:null, maxDate:null }; break;
      case 'radio':    base.options = [{v:'opcao_1'},{v:'opcao_2'}]; break;
      case 'checkbox': base.options = [{v:'opcao_1'},{v:'opcao_2'}]; base.validations={minChecked:null,maxChecked:null}; break;
      case 'select':   base.options = [{v:'opcao_1'},{v:'opcao_2'}]; base.multiple=false; break;
      case 'scale':    base.validations = { min:1, max:5, step:1, labels:{left:'Pouco', right:'Muito'} }; break;
    }
    base.label = labelFromType(type);
    return base;
  }

  function labelFromType(t){
    const map = { text:'Texto curto', textarea:'Texto longo', number:'Número', date:'Data',
      radio:'Múltipla (única)', checkbox:'Múltipla (múltiplas)', select:'Lista (select)', scale:'Escala' };
    return map[t] || t;
  }

  // ---------- Render ----------
  function render(){
    // meta
    formTitle.value = state.meta.title || '';
    formDesc.value  = state.meta.description || '';
    formType.value  = state.meta.type || 'anamnese';

    sectionsWrap.innerHTML = '';
    if(state.sections.length===0){ canvasEmpty.style.display='flex'; return; }
    canvasEmpty.style.display='none';

    state.sections.forEach((sec, sidx) => {
      const secEl = document.createElement('div');
      secEl.className = 'ecfb-section';
      secEl.dataset.sid = sec.id;
      secEl.innerHTML = `
        <header class="ecfb-section-hd">
          <input class="sec-title" value="${escapeHtml(sec.title)}" />
          <div class="sec-actions">
            <button class="btn btn-light" data-act="up">↑</button>
            <button class="btn btn-light" data-act="down">↓</button>
            <button class="btn btn-danger" data-act="del">Excluir</button>
          </div>
        </header>
        <div class="ecfb-fields" data-dropzone="${sec.id}"></div>
      `;
      sectionsWrap.appendChild(secEl);

      // campos
      const fieldsWrap = $('.ecfb-fields', secEl);
      sec.fields.forEach((f, fidx) => { fieldsWrap.appendChild(renderField(f, sidx, fidx)); });

      // eventos da seção
      $('.sec-title', secEl).addEventListener('input', e=>{ sec.title=e.target.value; autosave(); });
      $('.sec-actions [data-act="up"]', secEl).addEventListener('click', ()=>{ moveSection(sidx,-1); });
      $('.sec-actions [data-act="down"]', secEl).addEventListener('click', ()=>{ moveSection(sidx,1); });
      $('.sec-actions [data-act="del"]', secEl).addEventListener('click', ()=>{
        if(confirm('Excluir esta seção e seus campos?')){ state.sections.splice(sidx,1); render(); autosave(); }
      });

      // drag&drop do container
      enableDropzone(fieldsWrap, sec, sidx);
    });

    // painel de props
    $('#propsContainer').innerHTML = '<p class="muted">Selecione um campo para editar.</p>';
  }

  function renderField(f, sidx, fidx){
    const el = document.createElement('div');
    el.className = 'ecfb-field';
    el.draggable = true;
    el.dataset.sid = state.sections[sidx].id;
    el.dataset.fid = f.id;
    el.innerHTML = `
      <div class="fld-hd">
        <span class="fld-type">${f.type}</span>
        <strong class="fld-label">${escapeHtml(f.label)}</strong>
        <div class="fld-actions">
          <button class="btn btn-light" data-act="dup">Duplicar</button>
          <button class="btn btn-light" data-act="edit">Editar</button>
          <button class="btn btn-danger" data-act="del">Remover</button>
        </div>
      </div>
    `;

    $('.fld-actions [data-act="edit"]', el).addEventListener('click', ()=> selectField(f.id));
    $('.fld-actions [data-act="dup"]',  el).addEventListener('click', ()=> { duplicateField(sidx,fidx); });
    $('.fld-actions [data-act="del"]',  el).addEventListener('click', ()=> {
      if(confirm('Remover este campo?')){ state.sections[sidx].fields.splice(fidx,1); render(); autosave(); }
    });

    // drag
    el.addEventListener('dragstart', ev => {
      ev.dataTransfer.setData('text/plain', JSON.stringify({ type:'field', fid:f.id, fromS:sidx, fromI:fidx }));
    });

    return el;
  }

  function enableDropzone(zoneEl, sec, sidx){
    zoneEl.addEventListener('dragover', ev => { ev.preventDefault(); zoneEl.classList.add('drop'); });
    zoneEl.addEventListener('dragleave', ()=> zoneEl.classList.remove('drop'));
    zoneEl.addEventListener('drop', ev => {
      ev.preventDefault(); zoneEl.classList.remove('drop');
      const data = JSON.parse(ev.dataTransfer.getData('text/plain') || '{}');
      if(data.type !== 'field') return;
      const field = state.sections[data.fromS].fields.splice(data.fromI,1)[0];
      sec.fields.push(field);
      render(); autosave();
    });
  }

  function moveSection(idx, delta){
    const n = idx + delta; if(n < 0 || n >= state.sections.length) return;
    const [s] = state.sections.splice(idx,1); state.sections.splice(n,0,s);
    render(); autosave();
  }

  function duplicateField(sidx,fidx){
    const orig = state.sections[sidx].fields[fidx];
    const copy = JSON.parse(JSON.stringify(orig)); copy.id = uid('f'); copy.label = copy.label + ' (cópia)';
    state.sections[sidx].fields.splice(fidx+1,0,copy); render(); autosave();
  }

  // ---------- Propriedades do campo selecionado ----------
  function selectField(fid){ const ref = findFieldRef(fid); if(!ref) return; renderProps(ref.s, ref.i); }

  function findFieldRef(fid){
    for(let s=0; s<state.sections.length; s++){
      const i = state.sections[s].fields.findIndex(x=>x.id===fid);
      if(i>=0) return { s, i, field: state.sections[s].fields[i] };
    }
    return null;
  }

  function renderProps(sidx, fidx){
    const f = state.sections[sidx].fields[fidx];
    const wrap = $('#propsContainer');
    const common = `
      <label>Rótulo
        <input id="p_label" type="text" value="${escapeAttr(f.label)}" />
      </label>
      <label>Obrigatório
        <select id="p_required"><option value="0" ${!f.required?'selected':''}>Não</option><option value="1" ${f.required?'selected':''}>Sim</option></select>
      </label>
      <label>Visível
        <select id="p_visible"><option value="1" ${f.visible?'selected':''}>Sim</option><option value="0" ${!f.visible?'selected':''}>Não</option></select>
      </label>
      <label>Placeholder
        <input id="p_placeholder" type="text" value="${escapeAttr(f.placeholder||'')}" />
      </label>
      <label>Ajuda (tooltip)
        <input id="p_help" type="text" value="${escapeAttr(f.help||'')}" />
      </label>
    `;

    let specific = '';
    if(f.type==='text' || f.type==='textarea'){
      specific += `
        <div class="props-grid">
          <label>Min (chars)
            <input id="p_minLen" type="number" value="${valOr(f.validations.minLen,0)}" />
          </label>
          <label>Max (chars)
            <input id="p_maxLen" type="number" value="${valOr(f.validations.maxLen,200)}" />
          </label>
        </div>
      `;
    }
    if(f.type==='number'){
      specific += `
        <div class="props-grid">
          <label>Mínimo
            <input id="p_min" type="number" value="${valOr(f.validations.min,'')}" />
          </label>
          <label>Máximo
            <input id="p_max" type="number" value="${valOr(f.validations.max,'')}" />
          </label>
          <label>Step
            <input id="p_step" type="number" value="${valOr(f.validations.step,'')}" />
          </label>
        </div>
      `;
    }
    if(f.type==='date'){
      specific += `
        <div class="props-grid">
          <label>Data mín
            <input id="p_minDate" type="date" value="${valOr(f.validations.minDate,'')}" />
          </label>
          <label>Data máx
            <input id="p_maxDate" type="date" value="${valOr(f.validations.maxDate,'')}" />
          </label>
        </div>
      `;
    }
    if(['radio','checkbox','select'].includes(f.type)){
      const opts = (f.options||[]).map((o,idx)=>`
        <div class="opt-row">
          <input class="opt-val" data-idx="${idx}" value="${escapeAttr(o.v)}" />
          <button class="btn btn-light opt-del" data-idx="${idx}">Excluir</button>
        </div>`).join('');
      specific += `
        <div class="props-block">
          <div class="flex-space">
            <label>Opções</label>
            <button id="p_addOpt" class="btn btn-light">+ opção</button>
          </div>
          <div id="p_opts">${opts||'<p class="muted">Sem opções</p>'}</div>
        </div>
      `;
      if(f.type==='select'){
        specific += `
          <label>Múltipla seleção
            <select id="p_multiple"><option value="0" ${!f.multiple?'selected':''}>Não</option><option value="1" ${f.multiple?'selected':''}>Sim</option></select>
          </label>
        `;
      }
      if(f.type==='checkbox'){
        specific += `
          <div class="props-grid">
            <label>Mín. marcados
              <input id="p_minChecked" type="number" value="${valOr(f.validations.minChecked,'')}" />
            </label>
            <label>Máx. marcados
              <input id="p_maxChecked" type="number" value="${valOr(f.validations.maxChecked,'')}" />
            </label>
          </div>
        `;
      }
    }
    if(f.type==='scale'){
      specific += `
        <div class="props-grid">
          <label>Mínimo
            <input id="p_smin" type="number" value="${valOr(f.validations.min,1)}" />
          </label>
          <label>Máximo
            <input id="p_smax" type="number" value="${valOr(f.validations.max,5)}" />
          </label>
          <label>Step
            <input id="p_sstep" type="number" value="${valOr(f.validations.step,1)}" />
          </label>
        </div>
        <div class="props-grid">
          <label>Rótulo esquerdo
            <input id="p_lleft" type="text" value="${escapeAttr(f.validations.labels?.left||'Pouco')}" />
          </label>
          <label>Rótulo direito
            <input id="p_lright" type="text" value="${escapeAttr(f.validations.labels?.right||'Muito')}" />
          </label>
        </div>
      `;
    }

    wrap.innerHTML = common + specific + `
      <div class="props-actions">
        <button id="p_apply" class="btn btn-primary">Aplicar</button>
      </div>
    `;

    // binds
    $('#p_apply').addEventListener('click', ()=>{
      syncOptionsFromDOM(f); // garante salvar o que foi digitado
      f.label = $('#p_label').value.trim() || f.type;
      f.required   = $('#p_required').value === '1';
      f.visible    = $('#p_visible').value === '1';
      f.placeholder= $('#p_placeholder').value;
      f.help       = $('#p_help').value || null;

      if(f.type==='text' || f.type==='textarea'){
        f.validations.minLen = toNum($('#p_minLen').value, 0);
        f.validations.maxLen = toNum($('#p_maxLen').value, 200);
      }
      if(f.type==='number'){
        f.validations.min = toNum($('#p_min').value, null);
        f.validations.max = toNum($('#p_max').value, null);
        f.validations.step= toNum($('#p_step').value, null);
      }
      if(f.type==='date'){
        f.validations.minDate = $('#p_minDate').value || null;
        f.validations.maxDate = $('#p_maxDate').value || null;
      }
      if(['radio','checkbox','select'].includes(f.type)){
        if(f.type==='select') f.multiple = $('#p_multiple').value==='1';
        if(f.type==='checkbox'){
          f.validations.minChecked = toNum($('#p_minChecked').value, null);
          f.validations.maxChecked = toNum($('#p_maxChecked').value, null);
        }
      }
      if(f.type==='scale'){
        f.validations.min   = toNum($('#p_smin').value, 1);
        f.validations.max   = toNum($('#p_smax').value, 5);
        f.validations.step  = toNum($('#p_sstep').value, 1);
        f.validations.labels = { left:  $('#p_lleft').value || 'Pouco', right: $('#p_lright').value || 'Muito' };
      }
      render(); autosave();
    });

    // dinâmicas de opções — corrigido: primeiro sincroniza DOM, depois altera array
    if($('#p_addOpt')){
      $('#p_addOpt').addEventListener('click', ()=>{
        syncOptionsFromDOM(f);
        f.options.push({ v: '' });
        renderProps(sidx,fidx);
      });
      $$('.opt-del').forEach(btn=>btn.addEventListener('click', ()=>{
        const idx = Number(btn.dataset.idx);
        syncOptionsFromDOM(f);
        f.options.splice(idx,1);
        renderProps(sidx,fidx);
      }));
    }
  }

function syncOptionsFromDOM(f){
  const domOpts = $$('#p_opts .opt-val').map(inp=>({ v: inp.value.trim() }));
  if (Array.isArray(domOpts)) f.options = domOpts; // permite zero opções
}
  // ---------- Export / Preview / LocalStorage ----------
  const btnExport  = $('#btnExportJson');
  const btnPreview = $('#btnPreview');
  const btnSaveLoc = $('#btnSaveLocal');
  const btnLoadLoc = $('#btnLoadLocal');

  btnExport?.addEventListener('click', ()=>{ download('form-schema.json', JSON.stringify(state, null, 2)); });
  btnPreview?.addEventListener('click', ()=>{ openPreview(state); });
  btnSaveLoc?.addEventListener('click', ()=>{ autosave(true); alert('Rascunho salvo localmente.'); });
  btnLoadLoc?.addEventListener('click', ()=>{ const ok = loadFromLocal(); render(); alert(ok?'Rascunho carregado.':'Nada para carregar.'); });

  function autosave(){ try{ localStorage.setItem(storageKey, JSON.stringify(state)); }catch(e){} }
  function clearLocal(){ try{ localStorage.removeItem(storageKey); }catch(e){} }
  function loadFromLocal(){ try{ const raw = localStorage.getItem(storageKey); if(!raw) return false; const obj = JSON.parse(raw); Object.assign(state.meta, obj.meta||{}); state.sections = Array.isArray(obj.sections)? obj.sections : []; return true; }catch(e){ return false; } }

  function download(filename, text){ const a = document.createElement('a'); a.href = 'data:application/json;charset=utf-8,' + encodeURIComponent(text); a.download = filename; a.style.display='none'; document.body.appendChild(a); a.click(); a.remove(); }

  // ---------- Pré-visualização ----------
  function openPreview(schema){ const modal = document.getElementById('previewModal'); const body  = document.getElementById('previewBody'); body.innerHTML = ''; try { window.ECFormRenderer.render(schema, body); } catch(e){ body.innerHTML = '<p class="error">Erro ao renderizar prévia.</p>'; } modal.setAttribute('aria-hidden','false'); }
  document.getElementById('btnClosePreview').addEventListener('click', ()=>{ document.getElementById('previewModal').setAttribute('aria-hidden','true'); });

  // ---------- Helpers ----------
  function escapeHtml(s=''){ return s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c])); }
  function escapeAttr(s=''){ return escapeHtml(s); }
  function valOr(v, d){ return (v===undefined||v===null)? d : v; }
  function toNum(v, d){ const n = Number(v); return Number.isFinite(n)? n : d; }

  // ---------- Persistência no backend ----------
async function apiPost(url, payload){
  const res = await fetch(url, {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  });
  return await res.json();
}
async function apiGet(url){
  const res = await fetch(url);
  return await res.json();  
}

async function loadSchema(version_id){
  const out = await apiGet('forms_creator/version_get.php?version_id='+version_id);
  if(out.ok && out.schema){
    Object.assign(state.meta, out.schema.meta||{});
    state.sections = out.schema.sections||[];
  }
}

async function ensureDraft(){
  const ctx = window.__ECFB_CONTEXT__ || {}; // {form_id,version_id}
  if(ctx.version_id){ await loadSchema(ctx.version_id); return ctx.version_id; }
  if(ctx.form_id){
    const vlist = await apiGet('forms_creator/versions_list.php?form_id='+ctx.form_id);
    if(vlist.ok && Array.isArray(vlist.rows) && vlist.rows.length){
      const draft = vlist.rows.find(v=>v.status==='draft');
      if(draft){ ctx.version_id = String(draft.version_id); await loadSchema(ctx.version_id); return ctx.version_id; }
      const lastPub = [...vlist.rows].reverse().find(v=>v.status==='published');
      if(lastPub){
        const cloned = await apiPost('forms_creator/version_clone.php',{ form_id: ctx.form_id, from_version_id: lastPub.version_id });
        if(cloned.ok){ ctx.version_id = String(cloned.version_id); await loadSchema(ctx.version_id); return ctx.version_id; }
      }
    }
  }
  const out = await apiPost('forms_creator/create.php', {
    nome: state.meta.title || 'Novo formulário',
    descricao: state.meta.description || '',
    tipo: state.meta.type || 'anamnese'
  });
  if(!out.ok) throw new Error(out.error||'Falha ao criar');
  window.__ECFB_CONTEXT__.form_id = String(out.form_id);
  window.__ECFB_CONTEXT__.version_id = String(out.version_id);
  await loadSchema(out.version_id);
  return window.__ECFB_CONTEXT__.version_id;
}





    document.getElementById('btnSaveDraft')?.addEventListener('click', async ()=>{
        try{
            const version_id = await ensureDraft();
            const out = await apiPost('forms_creator/save_draft.php', { version_id, schema_json: state });
            if(!out.ok) throw new Error(out.error||'Erro ao salvar');
            clearLocal();
            alert('Rascunho salvo no sistema.');
        }catch(e){ alert('Falha: '+e.message); }
        });

    document.getElementById('btnPublish')?.addEventListener('click', async ()=>{
        try{
            const version_id = await ensureDraft();
            const out = await apiPost('forms_creator/publish.php', { version_id, schema_json: state });
            if(!out.ok) throw new Error(out.error||'Erro ao publicar');
            clearLocal();
            alert('Versão publicada com sucesso!');
        }catch(e){ alert('Falha: '+e.message); }
    });

const btnOpenDrawer  = document.getElementById('btnOpenDrawer');   // botão na topbar
const drawer         = document.getElementById('drawerMenu');      // painel lateral
const btnCloseDrawer = document.getElementById('btnCloseDrawer');  // X do painel
const drawerList     = document.getElementById('drawerList');
const btnNewForm     = document.getElementById('btnNewForm');

btnOpenDrawer?.addEventListener('click', ()=>{ 
  drawer.setAttribute('aria-hidden','false'); 
  loadFormsList();
});
btnCloseDrawer?.addEventListener('click', ()=> drawer.setAttribute('aria-hidden','true'));
document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') drawer.setAttribute('aria-hidden','true'); });

btnNewForm?.addEventListener('click', ()=>{
  clearLocal(); // só pra garantir que não carrega rascunho antigo
  window.location.href = './index.php?pagina=forms_creator/form_creator.php';
});

async function loadFormsList(){
  try{
    const out = await fetch('forms_creator/list.php', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({page:1,page_size:50})
    }).then(r=>r.json());
    if(!out.ok) throw new Error(out.error||'Falha');
    if(!out.rows.length){ drawerList.innerHTML = '<p class="muted">Nenhum formulário ainda.</p>'; return; }
    drawerList.innerHTML = out.rows.map(r=>{
      const title = r.title || r.nome || ('Form #'+r.id);
      const href = `index.php?pagina=forms_creator/form_creator.php&form_id=${r.id}`;
      return `<a class="tb-item" href="${href}"><strong>${title.replace(/</g,'&lt;')}</strong><span class="muted">#${r.id}</span></a>`;
    }).join('');
  }catch(e){
    drawerList.innerHTML = '<p class="error">Erro ao listar.</p>';
    console.error(e);
  }
}

  // ---------- init ----------
  // alteração: NÃO auto-carrega localStorage se já tem version_id (trazido do sistema)
  const hasVersion = Boolean((window.__ECFB_CONTEXT__||{}).version_id);
  //if(!hasVersion) loadFromLocal();
  render();


(async function init(){
  // contexto a partir da URL
  const qs = new URLSearchParams(window.location.search);
  const urlFormId = qs.get('form_id');
  const urlVersionId = qs.get('version_id');

  window.__ECFB_CONTEXT__ = window.__ECFB_CONTEXT__ || {};
  if (urlFormId)    window.__ECFB_CONTEXT__.form_id = String(urlFormId);
  if (urlVersionId) window.__ECFB_CONTEXT__.version_id = String(urlVersionId);

  // chave de cache por form/version (evita “vazar” rascunho)
  const ctx = window.__ECFB_CONTEXT__;
  const elMain = document.querySelector('.ecfb-layout');
  const key = (elMain?.dataset?.storageKey) || `EC_FORM_BUILDER_${ctx.form_id || 'new'}_${ctx.version_id || 'draft'}`;
  // reatribui a chave global usada nas funções já definidas:
  try { window.__ECFB_STORAGE_KEY__ = key; } catch(e){}

  // carrega do servidor quando tem form/version
  if (ctx.form_id || ctx.version_id) {
    try { await ensureDraft(); } catch(e){ console.error(e); }
  }
  // se for NOVO (sem ids), você decide: carregar local ou não
  // -> pra evitar cache grudado, deixe comentado:
  // else { loadFromLocal(); }

  render();
})();



})();