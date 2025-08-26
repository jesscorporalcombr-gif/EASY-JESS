window.ECFormRenderer = (function(){
  'use strict';

  const api = { render };
  return api;

  function render(schema, mount){
    mount.innerHTML = '';

    // título + descrição
    if(schema?.meta?.title){
      const h = document.createElement('h2'); h.textContent = schema.meta.title; mount.appendChild(h);
    }
    if(schema?.meta?.description){
      const p = document.createElement('p'); p.className='muted'; p.textContent = schema.meta.description; mount.appendChild(p);
    }

    // seções
    (schema.sections||[]).forEach(sec => {
      const s = document.createElement('section'); s.className='pv-sec';
      const h3 = document.createElement('h3'); h3.textContent = sec.title || 'Seção'; s.appendChild(h3);

      const wrap = document.createElement('div'); wrap.className='pv-fields';
      (sec.fields||[]).forEach(f => {
        if(f.visible===false) return;
        wrap.appendChild(renderField(f));
      });

      s.appendChild(wrap); mount.appendChild(s);
    });
  }


  

  function renderField(f){
    const row = document.createElement('div'); row.className='pv-row';
    const lab = document.createElement('label'); lab.textContent = f.label || f.type;
    row.appendChild(lab);

    let input;
    switch(f.type){
      case 'text': input = el('input',{type:'text', placeholder:f.placeholder||''}); break;
      case 'textarea': input = el('textarea',{rows:3, placeholder:f.placeholder||''}); break;
      case 'number': input = el('input',{type:'number'}); break;
      case 'date': input = el('input',{type:'date'}); break;
      case 'radio': input = renderOptions(f,'radio'); break;
      case 'checkbox': input = renderOptions(f,'checkbox'); break;
      case 'select': input = renderSelect(f); break;
      case 'scale': input = renderScale(f); break;
      default: input = document.createTextNode('Tipo não suportado');
    }
    row.appendChild(input);
    if(f.help){ const small=document.createElement('small'); small.className='muted'; small.textContent=f.help; row.appendChild(small); }
    return row;
  }

  function renderOptions(f, kind){
    const box = document.createElement('div'); box.className='pv-options';
    (f.options||[]).forEach((o,idx)=>{
      const id = `${f.id}_${idx}`;
      const w = document.createElement('div'); w.className='pv-opt';
      const inp = el('input',{type:kind, name:f.id, id});
      const lab = el('label',{for:id}); lab.textContent = o.v;
      w.appendChild(inp); w.appendChild(lab); box.appendChild(w);
    });
    return box;
  }

  function renderSelect(f){
    const sel = el('select',{});
    if(f.multiple) sel.multiple = true;
    (f.options||[]).forEach(o=>{
      const op = document.createElement('option'); op.value=o.v; op.textContent=o.v; sel.appendChild(op);
    });
    return sel;
  }

  function renderScale(f){
    const w = document.createElement('div'); w.className='pv-scale';
    const {min=1,max=5,labels={left:'Pouco',right:'Muito'}} = f.validations||{};
    const labL = document.createElement('span'); labL.textContent = labels.left || '';
    const labR = document.createElement('span'); labR.textContent = labels.right || '';
    const range = el('input',{type:'range', min:String(min), max:String(max), step:String(f.validations?.step||1)});
    w.appendChild(labL); w.appendChild(range); w.appendChild(labR);
    return w;
  }

  function el(tag, attrs){
    const e = document.createElement(tag);
    Object.entries(attrs||{}).forEach(([k,v])=>{ e.setAttribute(k,String(v)); });
    return e;
  }
})();