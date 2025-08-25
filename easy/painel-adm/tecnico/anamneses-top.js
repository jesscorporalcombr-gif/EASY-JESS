
(async function(){
  const sel = document.getElementById('selModelo');
  const inpPac = document.getElementById('inpPacienteId');
  const boxLink = document.getElementById('boxLink');

  // Carrega modelos
  try{
    const r = await fetch('api/forms/list_published.php');
    const j = await r.json();
    if (!j.ok) throw new Error(j.error || 'Falha modelos');
    sel.innerHTML = '';
    j.rows.forEach(row=>{
      const opt = document.createElement('option');
      opt.value = row.id;
      opt.textContent = `${row.name} (v${row.published_version})`;
      sel.appendChild(opt);
    });
  }catch(e){
    console.error(e);
    sel.innerHTML = '<option value="">(erro ao carregar modelos)</option>';
  }

  // Gerar link
  document.getElementById('btnGerarLink').addEventListener('click', async ()=>{
    const form_id = parseInt(sel.value || '0', 10);
    if (!form_id) { alert('Escolha um modelo.'); return; }
    const patient_id = (inpPac.value || '').trim();

    const fd = new FormData();
    fd.append('form_id', String(form_id));
    if (patient_id) fd.append('patient_id', patient_id);
    fd.append('expires_hours', '72');

    const r = await fetch('api/tokens/create.php', { method:'POST', body: fd });
    const j = await r.json();
    if (!j.ok) { alert('Erro: ' + (j.error || '')); return; }

    boxLink.style.display = 'block';
    boxLink.innerHTML = `
      Link gerado (expira em 72h):<br>
      <a href="${j.link}" target="_blank">${j.link}</a>
      <br>
      <small>Copie e envie ao paciente.</small>
    `;
  });

  // Nova anamnese (interna no sistema)
  document.getElementById('btnNovaInterna').addEventListener('click', ()=>{
    const form_id = parseInt(sel.value || '0', 10);
    if (!form_id) { alert('Escolha um modelo.'); return; }
    const patient_id = (inpPac.value || '').trim();

    // abre o respondedor no modo interno (sem token)
    const url = 'index.php?pagina=forms/fill.php&form_id=' + form_id + (patient_id ? ('&patient_id='+encodeURIComponent(patient_id)) : '');
    window.location.href = url;
  });
})();

