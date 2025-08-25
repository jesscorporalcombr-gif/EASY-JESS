<?php
// admin/forms/builder_free_full.php
// Builder essencial (gratuito) que gera JSON do SurveyJS (Form Library).
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Builder (Gratuito) - EasyClínicas</title>
  <style>
    :root{ --bg:#fff; --muted:#666; --border:#e5e7eb; --pri:#0e7afe; --ok:#188038; --warn:#d97706; --err:#b00020;}
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,Segoe UI,Arial;background:var(--bg);color:#111}
    header{display:flex;gap:12px;align-items:center;padding:10px 14px;border-bottom:1px solid var(--border);flex-wrap:wrap}
    header input, header textarea{padding:8px;border:1px solid #ddd;border-radius:8px}
    header input{width:320px}
    header textarea{width:520px;height:42px;resize:vertical}
    .btn{padding:10px 14px;border-radius:10px;border:1px solid #ccc;background:#fff;cursor:pointer}
    .btn.primary{background:var(--pri);color:#fff;border-color:var(--pri)}
    .btn.ghost{background:#fff;border-color:#ddd}
    .btn.small{padding:6px 10px;font-size:12px}
    .wrap{max-width:1100px;margin:14px auto;padding:0 14px}
    .row{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    #saveMsg{margin-left:8px;color:var(--ok);font-weight:600}
    .grid{display:grid;grid-template-columns:1fr 380px;gap:16px}
    .card{border:1px solid var(--border);border-radius:12px;padding:14px;background:#fff}
    .muted{color:var(--muted);font-size:13px}
    .q{border:1px solid var(--border);border-radius:12px;padding:12px;margin-bottom:10px}
    .qhead{display:flex;gap:8px;align-items:center;justify-content:space-between}
    .qtitle{display:grid;grid-template-columns:1fr 220px 110px 110px;gap:10px;align-items:center;margin-top:8px}
    .qtitle input, .qtitle select{padding:8px;border:1px solid #ddd;border-radius:8px;width:100%}
    .qopt{margin-top:8px}
    .qopt textarea{width:100%;min-height:64px;padding:8px;border:1px solid #ddd;border-radius:8px}
    .qfoot{margin-top:8px;display:flex;gap:8px;justify-content:flex-end}
    .pill{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:4px 10px;font-size:12px}
    .list{max-height:70vh;overflow:auto}
    .hl{background:#fafafa;border:1px dashed var(--border)}
    .danger{color:#b00020}
  </style>
</head>
<body>
  <header>
    <strong>Formulário:</strong>
    <input id="formName" placeholder="Ex.: Anamnese Corporal">
    <textarea id="formDesc" placeholder="Descrição (opcional)"></textarea>
    <button id="btnAddQ" class="btn ghost">+ Adicionar pergunta</button>
    <button id="btnSave" class="btn primary">Salvar</button>
    <span id="saveMsg"></span>
  </header>

  <div class="wrap grid">
    <div class="card">
      <div class="row" style="justify-content:space-between">
        <div class="muted">Arraste ↑/↓ com os botões para reordenar. Campos suportados: Texto, Comentário, Múltipla escolha, Checkbox, Dropdown, Data, Booleano, Número.</div>
        <span class="pill">ID: <?= $id ?: 'novo' ?></span>
      </div>
      <div id="list" class="list"></div>
      <div class="muted" style="margin-top:8px">Dica: “Nome (name)” é a chave no JSON. Se deixar vazio, geramos a partir do título.</div>
    </div>

    <div class="card">
      <div class="row" style="justify-content:space-between">
        <strong>Prévia (JSON SurveyJS)</strong>
        <button id="btnCopy" class="btn small ghost">Copiar JSON</button>
      </div>
      <textarea id="jsonPreview" style="width:100%;min-height:60vh;font-family:ui-monospace,monospace;border:1px solid #ddd;border-radius:8px;padding:10px"></textarea>
    </div>
  </div>

  <script>
    const FORM_ID = <?= $id ?>;
    const qtypes = [
      {v:'text', label:'Texto'},
      {v:'comment', label:'Comentário (área de texto)'},
      {v:'radiogroup', label:'Múltipla escolha (uma opção)'},
      {v:'checkbox', label:'Checkbox (várias opções)'},
      {v:'dropdown', label:'Dropdown (select)'},
      {v:'boolean', label:'Booleano (Sim/Não)'},
      {v:'date', label:'Data'},
      {v:'text:number', label:'Número'}
    ];

    const state = {
      title: 'Nova Anamnese',
      description: '',
      elements: [] // {type, name, title, isRequired, choices?, inputType?}
    };

    const $ = sel => document.querySelector(sel);
    const list = $('#list');
    const formName = $('#formName');
    const formDesc = $('#formDesc');
    const saveMsg = $('#saveMsg');
    const jsonPreview = $('#jsonPreview');

    function slugify(s){
      return (s||'').toString()
        .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
        .toLowerCase().replace(/[^a-z0-9]+/g,'_').replace(/^_+|_+$/g,'')
        .slice(0,40) || ('q_'+Math.random().toString(36).slice(2,7));
    }
    function ensureUniqueName(name){
      let n = name||'';
      if(!n) n = slugify('pergunta');
      let base = n, i=1;
      const names = new Set(state.elements.map(e=>e.name));
      while(names.has(n)) { n = base+'_'+i++; }
      return n;
    }

    function render(){
      // header
      if (!formName.value) formName.value = state.title;
      formDesc.value = state.description||'';

      // list
      list.innerHTML = '';
      state.elements.forEach((q, idx)=>{
        const el = document.createElement('div');
        el.className = 'q';
        el.innerHTML = `
          <div class="qhead">
            <strong>#${idx+1}</strong>
            <div>
              <button class="btn small ghost" data-act="up">↑</button>
              <button class="btn small ghost" data-act="down">↓</button>
              <button class="btn small danger" data-act="del">Excluir</button>
            </div>
          </div>
          <div class="qtitle">
            <input data-k="title" placeholder="Título da pergunta" value="${q.title||''}">
            <input data-k="name" placeholder="name (auto)" value="${q.name||''}">
            <select data-k="type">${qtypes.map(t=>`<option value="${t.v}" ${t.v===q.type?'selected':''}>${t.label}</option>`).join('')}</select>
            <select data-k="isRequired">
              <option value="0" ${!q.isRequired?'selected':''}>Opcional</option>
              <option value="1" ${q.isRequired?'selected':''}>Obrigatória</option>
            </select>
          </div>

          <div class="qopt" data-opt></div>

          <div class="qfoot muted">type: <code>${q.type}</code></div>
        `;
        const opt = el.querySelector('[data-opt]');
        const type = q.type||'text';

        if (['radiogroup','checkbox','dropdown'].includes(type)) {
          const lines = (q.choices||[]).map(c => (typeof c==='string'?c:(c?.value??''))).join('\n');
          opt.innerHTML = `
            <label class="muted">Opções (uma por linha)</label>
            <textarea data-k="choices">${lines}</textarea>
          `;
        } else if (type==='boolean') {
          opt.innerHTML = `<span class="muted">Saída será true/false.</span>`;
        } else if (type==='text:number') {
          opt.innerHTML = `<span class="muted">Campo numérico (renderiza como "text" com validação simples no respondedor).</span>`;
        } else if (type==='date') {
          opt.innerHTML = `<span class="muted">Campo de data.</span>`;
        } else {
          opt.innerHTML = `<span class="muted">Campo de texto simples.</span>`;
        }

        // bindings
        el.querySelectorAll('input,select,textarea').forEach(inp=>{
          inp.addEventListener('input', e=>{
            const k = inp.dataset.k;
            if (k==='title'){ q.title = inp.value; if (!q.name) { q.name = ensureUniqueName(slugify(q.title)); } }
            else if (k==='name'){ q.name = slugify(inp.value); }
            else if (k==='type'){ q.type = inp.value; }
            else if (k==='isRequired'){ q.isRequired = (inp.value==='1'); }
            else if (k==='choices'){
              const arr = inp.value.split('\n').map(s=>s.trim()).filter(Boolean);
              q.choices = arr;
            }
            renderJSON();
          });
        });

        el.querySelectorAll('button[data-act]').forEach(b=>{
          b.addEventListener('click', ()=>{
            const act = b.dataset.act;
            if (act==='del'){ state.elements.splice(idx,1); renderJSON(); render(); }
            if (act==='up' && idx>0){ const t=state.elements[idx-1]; state.elements[idx-1]=state.elements[idx]; state.elements[idx]=t; renderJSON(); render(); }
            if (act==='down' && idx<state.elements.length-1){ const t=state.elements[idx+1]; state.elements[idx+1]=state.elements[idx]; state.elements[idx]=t; renderJSON(); render(); }
          });
        });

        list.appendChild(el);
      });

      renderJSON();
    }

    function renderJSON(){
      const elements = state.elements.map(q=>{
        const base = { type: q.type==='text:number'?'text':q.type, name: q.name||ensureUniqueName(slugify(q.title||'pergunta')), title: q.title||q.name, isRequired: !!q.isRequired };
        if (q.type==='text:number') base.inputType = 'number';
        if (['radiogroup','checkbox','dropdown'].includes(q.type)) base.choices = (q.choices||[]).map(s=>s);
        return base;
      });
      const schema = {
        title: state.title || 'Formulário',
        description: state.description || undefined,
        showQuestionNumbers: "on",
        elements
      };
      jsonPreview.value = JSON.stringify(schema, null, 2);
    }

    function addQuestion(){
      state.elements.push({
        type: 'text',
        name: '',
        title: '',
        isRequired: false,
        choices: []
      });
      render();
    }

    // carregar form existente
    async function loadExisting(){
      if (FORM_ID <= 0) return;
      try{
        const r = await fetch(`api/forms/get.php?id=${FORM_ID}`);
        const j = await r.json();
        if (!j.ok) throw new Error(j.error||'erro');
        const schema = (typeof j.form.schema_json==='string') ? JSON.parse(j.form.schema_json) : j.form.schema_json;
        state.title = j.form.name || schema?.title || 'Formulário';
        state.description = j.form.description || schema?.description || '';
        state.elements = Array.isArray(schema?.elements) ? schema.elements.map(e=>{
          const t = (e.type==='text' && e.inputType==='number') ? 'text:number' : e.type;
          return {
            type: t||'text',
            name: e.name||'',
            title: e.title||'',
            isRequired: !!e.isRequired,
            choices: (e.choices||[]).map(c => (typeof c==='string'?c:(c?.value??'')))
          };
        }) : [];
        render();
      }catch(e){
        console.error(e);
        alert('Falha ao carregar formulário existente.');
      }
    }

    async function salvar(){
      state.title = formName.value.trim() || 'Formulário';
      state.description = formDesc.value.trim();
      renderJSON();
      const schema_json = jsonPreview.value;

      const fd = new FormData();
      fd.append('name', state.title);
      fd.append('description', state.description);
      fd.append('schema_json', schema_json);

      let url = '../../api/forms/create.php';
      if (FORM_ID>0){ url='../../api/forms/update.php'; fd.append('id', String(FORM_ID)); }

      const resp = await fetch(url, { method:'POST', body: fd });
      const out = await resp.json();
      if (out.ok){
        saveMsg.textContent = `Salvo v${out.version ?? 1} ✔`;
        if (!FORM_ID && out.id) location.href = `builder_free_full.php?id=${out.id}`;
      } else {
        alert('Erro: '+(out.error||'desconhecido'));
      }
    }

    document.getElementById('btnAddQ').addEventListener('click', addQuestion);
    document.getElementById('btnSave').addEventListener('click', salvar);
    document.getElementById('btnCopy').addEventListener('click', ()=> {
      jsonPreview.select(); document.execCommand('copy');
      alert('JSON copiado para a área de transferência.');
    });

    // Inicial
    addQuestion(); // começa com 1 pergunta vazia
    loadExisting();
  </script>
</body>
</html>
