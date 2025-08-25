(function(){
  'use strict';
  const $ = (s,r=document)=>r.querySelector(s);

  async function ensureRenderer(){
    if(typeof window.ECFormRenderer!=='undefined') return;
    await new Promise((res,rej)=>{
      const s = document.createElement('script');
      s.src = 'forms/form_renderer.js?v='+Date.now();
      s.onload = res; s.onerror = ()=>rej(new Error('forms/form_renderer.js não encontrado'));
      document.head.appendChild(s);
    });
  }

  async function openModal({form_id=null, response_id=null}){
    const qs = new URLSearchParams();
    if(form_id) qs.set('form_id', form_id);
    if(response_id) qs.set('response_id', response_id);

    const html = await fetch('forms/modal_response_fill.php?'+qs.toString()+'&_ts='+Date.now()).then(r=>r.text());
    const host = $('#modalHost'); host.innerHTML = html; host.setAttribute('aria-hidden','false');

    // executa os <script> do HTML injetado (define window.__EC_MODAL_CTX__)
    host.querySelectorAll('script').forEach(scr=>{ try{ eval(scr.innerText); }catch(e){ console.error(e); } });

    await ensureRenderer();

    const ctx = window.__EC_MODAL_CTX__;
    const mount = host.querySelector('#modalFormMount');
    if(!ctx || !ctx.schema || !mount){ alert('Erro ao carregar formulário.'); return; }

    try { ECFormRenderer.render(ctx.schema, mount, ctx.answers || {}); }
    catch(e){ console.error(e); alert('Falha ao renderizar.'); }

    host.querySelector('[data-close]')?.addEventListener('click', ()=>{ host.setAttribute('aria-hidden','true'); host.innerHTML=''; });

    host.querySelector('#btnSalvarResp')?.addEventListener('click', async ()=>{
      let answers={}; try{ answers = ECFormRenderer.collect ? ECFormRenderer.collect(mount) : {}; }catch(e){}
      const payload = {
        form_id: ctx.form_id,
        response_id: ctx.response_id || null,
        version_id: ctx.version_id,
        answers_json: answers
      };
      const out = await fetch('forms/response_update.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).then(r=>r.json());
      if(!out.ok){ alert(out.error||'Falha ao salvar'); return; }
      host.setAttribute('aria-hidden','true'); host.innerHTML='';
      // recarrega a tabela
      if(window.TabelaAnamneses?.reload) window.TabelaAnamneses.reload();
    });
  }

  async function validate(response_id, action){
    const out = await fetch('forms/response_validate.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({response_id, action})}).then(r=>r.json());
    if(!out.ok){ alert(out.error||'Falha'); return; }
    if(window.TabelaAnamneses?.reload) window.TabelaAnamneses.reload();
  }

  async function remove(response_id){
    if(!confirm('Excluir esta anamnese? (remoção lógica)')) return;
    const out = await fetch('forms/response_delete.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({response_id})}).then(r=>r.json());
    if(!out.ok){ alert(out.error||'Falha'); return; }
    if(window.TabelaAnamneses?.reload) window.TabelaAnamneses.reload();
  }

  function upload(response_id){
    alert('TODO: modal de upload único → POST forms/response_upload.php (has_attachment=1).');
  }

  window.AnamneseModal = {
    open: openModal,
    validate,
    remove,
    upload
  };
})();
