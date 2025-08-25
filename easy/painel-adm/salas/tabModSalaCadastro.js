// salas/tabModSalaCadastro.js
(function(){
  const modal = document.querySelector('#modalCadSala');
  if (!modal) return;

  // --- evita empilhar ao reabrir o modal
  if (modal.__salaCadCtl && typeof modal.__salaCadCtl.abort === 'function') {
    try { modal.__salaCadCtl.abort(); } catch(_) {}
  }
  const ctl = new AbortController();
  modal.__salaCadCtl = ctl;
  const on = (el, ev, fn, opts) => { if (el) el.addEventListener(ev, fn, { ...(opts||{}), signal: ctl.signal }); };

  // --- estado
  let edit = (typeof tipo_cadastro !== 'undefined' && tipo_cadastro === 'novo');
  let cropOpen = false;

  // --- helpers
  const qs  = (s, c=document)=>c.querySelector(s);
  const qsa = (s, c=document)=>Array.from(c.querySelectorAll(s));

  function toggleFooterByTab(){
    const footer = qs('#modalCadSala .modal-footer');
    const activeBtn = qs('#v-tab .tab-btn.active');
    const onCadastro = !!activeBtn && activeBtn.id === 'cadastro-tab';
    if (footer) footer.style.display = onCadastro ? '' : 'none';
  }

  function updateSalvarState(){
    const btnSalvar = qs('#btn-salvar_sala');
    if (btnSalvar) btnSalvar.disabled = !edit || cropOpen;
  }

  function setCadastroEdit(enabled){
    edit = !!enabled;

    const campos    = qsa('#aba-cadastro input, #aba-cadastro select, #aba-cadastro textarea');
    const btnEditar = qs('#btn-editar-cadastro');
    const inputFoto = qs('#input-foto_cadSala');

    campos.forEach(c=>{
      c.disabled = !edit;
      if (c.tagName === 'INPUT' || c.tagName === 'TEXTAREA') {
        c.readOnly = !edit;
      }
    });

    if (inputFoto) {
      inputFoto.style.pointerEvents = edit ? 'auto' : 'none';
      inputFoto.style.cursor = edit ? 'pointer' : '';
    }
    if (btnEditar) btnEditar.style.display = edit ? 'none' : 'block';

    updateSalvarState(); // respeita cropOpen
  }

  function bindEditar(){
    const btnEdit = qs('#btn-editar-cadastro');
    on(btnEdit, 'click', ()=> setCadastroEdit(true));
  }

  // ---- Cropper / Webcam para a foto da sala
  function initImagemCropper(){
  const root        = document.querySelector('#modalCadSala');
  if (!root) return;

  let imgAvatar     = root.querySelector('#img-foto_cadSala');
  let inputFile     = root.querySelector('#input-foto_cadSala');
  const cropArea    = root.querySelector('#cropper-area');
  const btnCropOk   = root.querySelector('#btn-crop-ok');
  const btnCropCancel = root.querySelector('#btn-crop-cancel');
  const previewCrop = root.querySelector('#preview-crop');

  if (!imgAvatar || !inputFile || !cropArea || !previewCrop) return;

  // ⚠️ ZERA QUALQUER LISTENER PRÉVIO (de outros scripts/aberturas)
  const imgClone = imgAvatar.cloneNode(true);
  imgAvatar.parentNode.replaceChild(imgClone, imgAvatar);
  imgAvatar = imgClone;

  const inpClone = inputFile.cloneNode(true);
  inputFile.parentNode.replaceChild(inpClone, inputFile);
  inputFile = inpClone;

  let cropper = null;

  // Abrir seletor de arquivo (UMA única chamada)
  imgAvatar.addEventListener('click', (ev)=>{
    if (!edit) return;
    ev.preventDefault();
    ev.stopPropagation();
    if (typeof inputFile.showPicker === 'function') inputFile.showPicker();
    else inputFile.click();
  });

  // Ao escolher arquivo → abrir cropper e desabilitar "Salvar"
  inputFile.addEventListener('change', (e)=>{
    const file = e.target.files?.[0];
    if (!file) return;

    cropArea.style.display = 'block';
    cropOpen = true;
    const btnSalvar = document.querySelector('#btn-salvar_sala');
    if (btnSalvar) btnSalvar.disabled = true;

    if (cropper) { cropper.destroy(); cropper = null; }
    const url = URL.createObjectURL(file);
    previewCrop.src = url;
    previewCrop.onload = ()=>{
      if (cropper) cropper.destroy();
      cropper = new Cropper(previewCrop, {
        aspectRatio: 1, viewMode: 1, autoCropArea: 1, responsive: true
      });
    };
  });

  // Confirmar recorte
  btnCropOk?.addEventListener('click', ()=>{
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({ width: 400, height: 400, imageSmoothingQuality: 'high' });
    canvas.toBlob((blob)=>{
      const url = URL.createObjectURL(blob);
      imgAvatar.src = url;

      const newFile = new File([blob], 'foto_sala.jpg', { type:'image/jpeg' });
      const dt = new DataTransfer();
      dt.items.add(newFile);
      inputFile.files = dt.files;

      cropArea.style.display = 'none';
      cropper.destroy(); cropper = null;

      cropOpen = false;
      const btnSalvar = document.querySelector('#btn-salvar_sala');
      if (btnSalvar) btnSalvar.disabled = !edit;
    }, 'image/jpeg', 0.85);
  });

  // Cancelar recorte
  btnCropCancel?.addEventListener('click', ()=>{
    cropArea.style.display = 'none';
    if (cropper) { cropper.destroy(); cropper = null; }
    inputFile.value = '';

    cropOpen = false;
    const btnSalvar = document.querySelector('#btn-salvar_sala');
    if (btnSalvar) btnSalvar.disabled = !edit;
  });
}

  // ---- Submit do cadastro
  function bindSubmitCadastro(){
    const form = qs('#formCadSala');
    if (!form) return;

    on(form, 'submit', (e)=>{
      e.preventDefault();
      if (cropOpen) return; // proteção extra

      const fd = new FormData(form);

      // normaliza checkboxes / radios
      const agOnline = qs('#frm-agendamento_online')?.checked ? '1' : '0';
      const fidel    = qs('#frm-fidelidade')?.checked ? '1' : '0';
      const paralelo = (qs('input[name="frm-paralelo"]:checked')?.value ?? '0');
      const catText  = qs('#frm-categoria option:checked')?.text.trim() || '';

      fd.set('frm-agendamento_online', agOnline);
      fd.set('frm-fidelidade', fidel);
      fd.set('frm-paralelo', paralelo);
      fd.set('frm-categoria_txt', catText);

      fetch('salas/grava_cadastro.php', { method: 'POST', body: fd })
        .then(r=>r.json())
        .then(res=>{
          const msg = qs('#mensagem');
          if (res.success) {
            if (msg){ msg.className='text-success'; msg.textContent='Salvo com sucesso.'; }

            // trava edição
            setCadastroEdit(false);

            // atualiza hidden com ID recém-criado
            const novoId = res.data?.id;
            if (novoId) {
              const idInput = qs('#formCadSala input[name="id"]');
              if (idInput) idInput.value = novoId;

              // opcional: propaga para a aba documentos imediatamente
              const hidDoc = document.getElementById('doc-id_sala');
              if (hidDoc) hidDoc.value = novoId;
            }

            // atualiza cabeçalho
            if (res.data?.foto_head) {
              const iHead = qs('#img-foto_head');
              if (iHead) iHead.src = res.data.foto_head;
            }
            if (res.data?.titulo) {
              const h = qs('.modal-title');
              if (h) h.innerHTML = res.data.titulo;
            }

            // exibe as tabs após salvar
            const tab = modal.querySelector('#v-tab');
            if (tab) tab.style.display='';

            // avisa outras guias (ex.: Documentos) que ID está disponível
            document.dispatchEvent(new CustomEvent('sala:salvo', { detail: { id: res.data?.id || null } }));

          } else {
            if (msg){ msg.className='text-danger'; msg.textContent=res.msg || 'Falha ao salvar.'; }
          }
        })
        .catch(err=>{
          const msg = qs('#mensagem');
          if (msg){ msg.className='text-danger'; msg.textContent='Erro de comunicação: '+err; }
        });
    });
  }

  // ---- Inicialização quando modal abrir e quando trocar de aba
  function init(){
    setCadastroEdit(edit);
    bindEditar();
    initImagemCropper();
    bindSubmitCadastro();
    toggleFooterByTab();
  }

  // inicia ao mostrar este modal
  on(document, 'shown.bs.modal', (e)=>{
    if (e.target && e.target.id === 'modalCadSala') init();
  });

  // alternância de abas
  on(document, 'shown.bs.tab', (e)=>{
    const t = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
    if (t) toggleFooterByTab();
  });

  // fallback (se já está aberto)
  //if (qs('#modalCadSala')) init();

  // cleanup ao fechar o modal
  on(modal, 'hidden.bs.modal', ()=>{
    try { ctl.abort(); } catch(_) {}
    delete modal.__salaCadCtl;
  }, { once:true });

})();
