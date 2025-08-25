// equipamentos/tabModEquipamentoCadastro.js
(function(){
  //let editar = false;
  let edit= tipo_cadastro=='novo'?true:false;
  //let saving = false; // trava contra duplo submit

  const modal = document.getElementById('modalCadEquipamento');
  // --- evita empilhar ao reabrir o modal
  if (modal.__equipamentoCadCtl && typeof modal.__equipamentoCadCtl.abort === 'function') {
    try { modal.__equipamentoCadCtl.abort(); } catch(_) {}
  }
  const ctl = new AbortController();
  modal.__equipamentoCadCtl = ctl;
  const on = (el, ev, fn, opts) => { if (el) el.addEventListener(ev, fn, { ...(opts||{}), signal: ctl.signal }); };



  const qs  = (s, c=document)=>c.querySelector(s);
  const qsa = (s, c=document)=>Array.from(c.querySelectorAll(s));

  function toggleFooterByTab(){
    const footer = qs('#modalCadEquipamento .modal-footer');
    const activeBtn = qs('#v-tab .tab-btn.active');
    const onCadastro = !!activeBtn && activeBtn.id === 'cadastro-tab';
    if (footer) footer.style.display = onCadastro ? '' : 'none';
  }

  function setCadastroEdit(){
    const campos = qsa('#aba-cadastro input, #aba-cadastro select, #aba-cadastro textarea');
    const btnSalvar = qs('#btn-salvar_equipamento');
    const btnEditar = qs('#btn-editar-cadastro');
    const inputFoto = qs('#input-foto_cadEquipamento');
    

    campos.forEach(c=>{
      c.disabled = !edit;
      if (c.tagName === 'INPUT' || c.tagName === 'TEXTAREA') {
        c.readOnly = !edit;
      }
    });
    if (btnSalvar) btnSalvar.disabled = !edit;
    if (inputFoto) {
      inputFoto.style.pointerEvents = edit ? 'auto' : 'none';
      inputFoto.style.cursor = edit ? 'pointer' : '';
    }
    if (btnEditar) btnEditar.style.display = edit ? 'none' : 'block';
  }

  function bindEditar(){
    const btnEdit = qs('#btn-editar-cadastro');
    if (!btnEdit) return;
    btnEdit.addEventListener('click', ()=>{
      edit=true;
      setCadastroEdit();
    });
  }

  // ---- Cropper / Webcam para a foto do equipamento
function initImagemCropper(){
  const imgAvatar   = document.querySelector('#img-foto_cadEquipamento');
  const inputFile   = document.querySelector('#input-foto_cadEquipamento');
  const cropArea    = document.querySelector('#cropper-area');
  const btnCropOk   = document.querySelector('#btn-crop-ok');
  const btnCropCancel = document.querySelector('#btn-crop-cancel');
  const previewCrop = document.querySelector('#preview-crop');

  if (!imgAvatar || !inputFile || !cropArea || !previewCrop) return;

  // evita múltiplos listeners ao reabrir o modal
  if (imgAvatar.dataset.bound === '1') return;
  imgAvatar.dataset.bound = '1';

  let cropper = null;

  // clique na foto -> abre o file picker (SEM webcam/confirm)
  imgAvatar.addEventListener('click', ()=>{
    if (!window.edit && typeof edit !== 'undefined' ? !edit : false) return;
    inputFile.click();
  });

  // ao escolher arquivo -> abre cropper
  inputFile.addEventListener('change', (e)=>{
    const file = e.target.files?.[0];
    if (!file) return;

    cropArea.style.display = 'block';
    if (cropper) { cropper.destroy(); cropper = null; }

    const url = URL.createObjectURL(file);
    previewCrop.src = url;
    previewCrop.onload = ()=>{
      if (cropper) cropper.destroy();
      cropper = new Cropper(previewCrop, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 1,
        responsive: true
      });
    };
  });

  // confirmar recorte
  btnCropOk?.addEventListener('click', ()=>{
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({ width: 400, height: 400, imageSmoothingQuality: 'high' });
    canvas.toBlob((blob)=>{
      const url = URL.createObjectURL(blob);
      imgAvatar.src = url;

      // injeta o recorte no input file
      const newFile = new File([blob], 'foto_equipamento.jpg', { type:'image/jpeg' });
      const dt = new DataTransfer();
      dt.items.add(newFile);
      inputFile.files = dt.files;

      cropArea.style.display = 'none';
      cropper.destroy(); cropper = null;
    }, 'image/jpeg', 0.85);
  });

  // cancelar recorte
  btnCropCancel?.addEventListener('click', ()=>{
    cropArea.style.display = 'none';
    if (cropper) { cropper.destroy(); cropper = null; }
    inputFile.value = '';
  });
}


  // ---- Submit do cadastro
  // ---- Submit do cadastro (limpo)
function bindSubmitCadastro(){
  const form = qs('#formCadEquipamento');
  if (!form) return;

  form.addEventListener('submit', (e)=>{
    e.preventDefault();

    // coleta segura (sem “lixo” de campos de serviços)
    const fd = new FormData();

    const id                 = qs('#frm-id')?.value || '';
    const nome               = qs('#frm-nome')?.value?.trim() || '';
    const marca              = qs('#frm-marca')?.value?.trim() || '';
    const modelo             = qs('#frm-modelo')?.value?.trim() || '';
    const anvisa             = qs('#frm-anvisa')?.value?.trim() || '';
    const ag_paralelo        = (qs('input[name="frm-paralelo"]:checked')?.value ?? '0');
    const patrimonio         = (qs('input[name="frm-patrimonio"]:checked')?.value ?? '1');

    const data_compra        = qs('#frm-data_compra')?.value || '';
    const nota_fiscal_compra = qs('#frm-nota_fiscal_compra')?.value || '';
  
    const numero_serie       = qs('#frm-numero_serie')?.value?.trim() || '';

    const data_ultima_rev    = qs('#frm-data_ultima_revisao')?.value || '';
    const data_proxima_rev   = qs('#frm-data_proxima_revisao')?.value || '';

    const pag_fabricante     = qs('#frm-pag_fabricante')?.value?.trim() || '';
    const site_referencia    = qs('#frm-site_referencia')?.value?.trim() || '';

    const descricao          = qs('#frm-descricao')?.value?.trim() || '';
    const excluido           = (qs('input[name="frm-excluido"]:checked')?.value ?? '0');

    if (!nome){
      const msg = qs('#mensagem');
      if (msg){ msg.className='text-danger'; msg.textContent='Informe o nome do equipamento.'; }
      return;
    }

    // append padrão
    fd.append('id', id);
    fd.append('frm-nome', nome);
    fd.append('frm-marca', marca);
    fd.append('frm-modelo', modelo);
    fd.append('frm-anvisa', anvisa);
    fd.append('frm-paralelo', ag_paralelo);
    fd.append('frm-patrimonio', patrimonio);

    // se não é patrimônio, zera campos de compra
    if (patrimonio === '1') {
      fd.append('frm-data_compra', data_compra);
      fd.append('frm-nota_fiscal_compra', nota_fiscal_compra);
      fd.append('frm-numero_serie', numero_serie);
    } else {
      fd.append('frm-data_compra', '');
      fd.append('frm-nota_fiscal_compra', '');
      fd.append('frm-numero_serie', '');
    }

    fd.append('frm-data_ultima_revisao', data_ultima_rev);
    fd.append('frm-data_proxima_revisao', data_proxima_rev);
    fd.append('frm-pag_fabricante', pag_fabricante);
    fd.append('frm-site_referencia', site_referencia);
    fd.append('frm-descricao', descricao);
    fd.append('frm-excluido', excluido);

    // foto (se o usuário recortou, o input já contém o blob)
    const inputFoto = qs('#input-foto_cadEquipamento');
    if (inputFoto?.files && inputFoto.files[0]) {
      fd.append('input-foto_cadEquipamento', inputFoto.files[0]);
    }

    // envia
    fetch('equipamentos/grava_cadastro.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        const msg = qs('#mensagem');
        if (res && res.success) {
          if (msg){ msg.className='text-success'; msg.textContent='Salvo com sucesso.'; }
          edit=false;
          setCadastroEdit();

          // atualiza id do formulário, se veio
          if (res.data?.id) {
            const idEl = document.querySelector('#formCadEquipamento input[name="id"]');
            if (idEl) idEl.value = res.data.id;
          }
          // atualiza imagem/headline, se veio
          if (res.data?.foto_head) {
            const iHead = qs('#img-foto_head');
            const iCard = qs('#img-foto_cadEquipamento');
            if (iHead) iHead.src = res.data.foto_head;
            if (iCard) iCard.src = res.data.foto_head;
          }
          if (res.data?.titulo) {
            const h = qs('.modal-title');
            if (h) h.innerHTML = res.data.titulo;
          }
            const tabs = document.getElementById('v-tab');
            if (tabs) {
              tabs.style.display='';
            }

          // avisa outras abas
          if(tipo_cadastro=='novo'){
            document.dispatchEvent(new CustomEvent('equipamento:salvo', {
              detail: { id: res.data?.id || null }
            }));
          }

          tipo_cadastro='edit';

        } else {
          if (msg){ msg.className='text-danger'; msg.textContent = res?.msg || 'Falha ao salvar.'; }
        }
      })
      .catch(err => {
        const msg = qs('#mensagem');
        if (msg){ msg.className='text-danger'; msg.textContent = 'Erro de comunicação: ' + err; }
      });
  });
}


  // ---- Inicialização quando modal abrir e quando trocar de aba
  function init(){
    setCadastroEdit();
    bindEditar();
    initImagemCropper();
    bindSubmitCadastro();
    toggleFooterByTab();
  }

  document.addEventListener('shown.bs.modal', (e)=>{
    if (e.target && e.target.id === 'modalCadEquipamentos') init();
  });
  document.addEventListener('shown.bs.tab', (e)=>{
    const t = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
    if (t) toggleFooterByTab();
  });

  // fallback (se já está aberto)
  if (qs('#modalCadEquipamento')) init();



  function toggleGrupoPatrimonio(){
      const sim = document.getElementById('frm-patrimonio_1')?.checked;
      const grupo = document.getElementById('grupo-patrimonio');
      if (grupo) grupo.style.display = sim ? '' : 'none';
    }
    document.addEventListener('change', (e)=>{
      if (e.target && e.target.name === 'frm-patrimonio') toggleGrupoPatrimonio();
    });
    // estado inicial
    document.addEventListener('DOMContentLoaded', toggleGrupoPatrimonio);
    // se abrir modal via ajax, roda ao exibir
    document.addEventListener('shown.bs.modal', (ev)=>{
      if (ev.target && ev.target.id === 'modalCadEquipamento') toggleGrupoPatrimonio();
    });

     on(modal, 'hidden.bs.modal', ()=>{
        try { ctl.abort(); } catch(_) {}
        delete modal.__equipamentoCadCtl;
      }, { once:true });

})();
