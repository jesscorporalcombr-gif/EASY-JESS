// servicos/tabModServCadastro.js
(function(){
  let editar = false; // estado local

  const qs  = (s, c=document)=>c.querySelector(s);
  const qsa = (s, c=document)=>Array.from(c.querySelectorAll(s));

  function toggleFooterByTab(){
    const footer = qs('#modalCadServico .modal-footer');
    const activeBtn = qs('#v-tab .tab-btn.active');
    const onCadastro = !!activeBtn && activeBtn.id === 'cadastro-tab';
    if (footer) footer.style.display = onCadastro ? '' : 'none';
  }

  function setCadastroEdit(enabled){
    const campos = qsa('#aba-cadastro input, #aba-cadastro select, #aba-cadastro textarea');
    const btnSalvar = qs('#btn-salvar_servico');
    const btnEditar = qs('#btn-editar-cadastro');
    const inputFoto = qs('#input-foto_cadServico');
    editar = !!enabled;

    campos.forEach(c=>{
      c.disabled = !editar;
      if (c.tagName === 'INPUT' || c.tagName === 'TEXTAREA') {
        c.readOnly = !editar;
      }
    });
    if (btnSalvar) btnSalvar.disabled = !editar;
    if (inputFoto) {
      inputFoto.style.pointerEvents = editar ? 'auto' : 'none';
      inputFoto.style.cursor = editar ? 'pointer' : '';
    }
    if (btnEditar) btnEditar.style.display = editar ? 'none' : 'block';
  }

  function bindEditar(){
    const btnEdit = qs('#btn-editar-cadastro');
    if (!btnEdit) return;
    btnEdit.addEventListener('click', ()=>{
      setCadastroEdit(true);
    });
  }

  // ---- Cropper / Webcam para a foto do serviço
  function initImagemCropper(){
    const imgAvatar   = qs('#img-foto_cadServico');
    const inputFile   = qs('#input-foto_cadServico');
    const cropArea    = qs('#cropper-area');
    const webcamArea  = qs('#webcam-area');
    const btnCropOk   = qs('#btn-crop-ok');
    const btnCropCancel = qs('#btn-crop-cancel');
    const btnWebcap   = qs('#btn-capturar');
    const btnWebCancel= qs('#btn-cancelar-webcam');
    const previewCrop = qs('#preview-crop');

    if (!imgAvatar || !inputFile || !cropArea || !previewCrop) return;

    let cropper = null;
    let webcamStream = null;

    function mostrarCropper(src){
      cropArea.style.display = 'block';
      if (cropper) { cropper.destroy(); cropper = null; }
      previewCrop.src = src;
      previewCrop.onload = function(){
        if (cropper) cropper.destroy();
        cropper = new Cropper(previewCrop, {
          aspectRatio: 1,
          viewMode: 1,
          autoCropArea: 1,
          responsive: true
        });
      };
    }

    function abrirWebcam(){
      if (!navigator.mediaDevices?.getUserMedia) return alert('Webcam não suportada.');
      webcamArea.style.display = 'block';
      cropArea.style.display = 'none';
      navigator.mediaDevices.getUserMedia({video:true})
        .then(stream=>{
          webcamStream = stream;
          qs('#webcam').srcObject = stream;
        })
        .catch(err=>{
          alert('Não foi possível acessar a webcam: ' + err);
          webcamArea.style.display = 'none';
        });
    }

    function encerrarWebcam(){
      if (webcamStream) {
        webcamStream.getTracks().forEach(t=>t.stop());
        webcamStream = null;
      }
      webcamArea.style.display = 'none';
    }

    imgAvatar.addEventListener('click', ()=>{
      if (!editar) return;
      if (confirm('Deseja tirar uma foto com a webcam?')) {
        abrirWebcam();
      } else {
        inputFile.click();
      }
    });

    inputFile.addEventListener('change', (e)=>{
      const file = e.target.files?.[0];
      if (!file) return;
      mostrarCropper(URL.createObjectURL(file));
    });

    btnWebcap?.addEventListener('click', ()=>{
      const video = qs('#webcam');
      if (!video?.videoWidth) return;
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      mostrarCropper(canvas.toDataURL('image/jpeg', 0.92));
      encerrarWebcam();
    });

    btnWebCancel?.addEventListener('click', encerrarWebcam);

    btnCropOk?.addEventListener('click', ()=>{
      if (!cropper) return;
      const canvas = cropper.getCroppedCanvas({ width: 400, height: 400, imageSmoothingQuality: 'high' });
      canvas.toBlob((blob)=>{
        const url = URL.createObjectURL(blob);
        imgAvatar.src = url;
        // injeta no input file
        const newFile = new File([blob], 'foto_servico.jpg', { type:'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(newFile);
        inputFile.files = dt.files;
        cropArea.style.display = 'none';
        cropper.destroy(); cropper = null;
      }, 'image/jpeg', 0.85);
    });

    btnCropCancel?.addEventListener('click', ()=>{
      cropArea.style.display = 'none';
      if (cropper) { cropper.destroy(); cropper = null; }
      inputFile.value = '';
    });
  }

  // ---- Submit do cadastro
  function bindSubmitCadastro(){
    const form = qs('#formCadServico');
    if (!form) return;

    form.addEventListener('submit', (e)=>{
      e.preventDefault();
      const fd = new FormData(form);

      // normaliza checkboxes / radios
      const agOnline = qs('#frm-agendamento_online')?.checked ? '1' : '0';
      const fidel    = qs('#frm-fidelidade')?.checked ? '1' : '0';
      const paralelo = (qs('input[name="frm-paralelo"]:checked')?.value ?? '0');

      fd.set('frm-agendamento_online', agOnline);
      fd.set('frm-fidelidade', fidel);
      fd.set('frm-paralelo', paralelo);

      // chama o backend
      fetch('servicos/grava_cadastro.php', {
        method: 'POST',
        body: fd
      })
      .then(r=>r.json())
      .then(res=>{
        const msg = qs('#mensagem');
        if (res.success) {
          if (msg){ msg.className='text-success'; msg.textContent='Salvo com sucesso.'; }
          setCadastroEdit(false);
          // se retornou nova foto/título, atualiza cabeçalho
          if (res.data?.foto_head) {
            const iHead = qs('#img-foto_head');
            if (iHead) iHead.src = res.data.foto_head;
          }
          if (res.data?.titulo) {
            const h = qs('.modal-title');
            if (h) h.innerHTML = res.data.titulo;
          }
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
    setCadastroEdit(false);
    bindEditar();
    initImagemCropper();
    bindSubmitCadastro();
    toggleFooterByTab();
  }

  document.addEventListener('shown.bs.modal', (e)=>{
    if (e.target && e.target.id === 'modalCadServico') init();
  });
  document.addEventListener('shown.bs.tab', (e)=>{
    const t = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
    if (t) toggleFooterByTab();
  });

  // fallback (se já está aberto)
  if (qs('#modalCadServico')) init();

})();
