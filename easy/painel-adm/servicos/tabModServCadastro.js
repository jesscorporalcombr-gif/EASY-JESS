// servicos/tabModServCadastro.js
// Carregado JUNTO com o HTML do modal (dinâmico).
// Foco: binds ancorados no #modalCadServico + namespace + off antes de on + trava submit + CROPper.

(function () {
  const $modal = $('#modalCadServico');
  if (!$modal.length) return; // só roda se o modal existir

  // Evita acumular binds em reaberturas do MESMO modal
  $modal.off('.cadServico');

  // -------------------- Estado local por abertura --------------------
  let editar = true;
  try { editar = (window.tipo_cadastro === 'novo') ? true : false; } catch(e) {}
  let saving = false; // trava contra duplo submit

  // Estado do Cropper/URLs (para limpar no hidden)
  let cropper = null;
  let fileURL = null; // URL do arquivo escolhido
  let blobURL = null; // URL do recorte (canvas->blob)

  // -------------------- Utils --------------------
  const qs  = (sel) => $modal[0].querySelector(sel);
  const qsa = (sel) => Array.from($modal[0].querySelectorAll(sel));

  function toggleFooterByTab() {
    const footer = qs('.modal-footer');
    const activeBtn = $modal.find('#v-tab .tab-btn.active')[0];
    const onCadastro = !!activeBtn && activeBtn.id === 'cadastro-tab';
    if (footer) footer.style.display = onCadastro ? '' : 'none';
  }

  function setCadastroEdit(enabled) {
    const campos = qsa('#aba-cadastro input, #aba-cadastro select, #aba-cadastro textarea');
    const btnSalvar = qs('#btn-salvar_servico');
    const btnEditar = qs('#btn-editar-cadastro');
    const inputFoto = qs('#input-foto_cadServico');

    editar = !!enabled;

    campos.forEach(c => {
      c.disabled = !editar;
      if (c.tagName === 'INPUT' || c.tagName === 'TEXTAREA') c.readOnly = !editar;
    });
    if (btnSalvar) btnSalvar.disabled = !editar;
    if (inputFoto) {
      inputFoto.style.pointerEvents = editar ? 'auto' : 'none';
      inputFoto.style.cursor = editar ? 'pointer' : '';
    }
    if (btnEditar) btnEditar.style.display = editar ? 'none' : 'block';
  }

  // -------------------- Binds (todos com namespace .cadServico) --------------------

  // Editar cadastro
  $modal.on('click.cadServico', '#btn-editar-cadastro', () => setCadastroEdit(true));

  // Troca de aba → mostra/esconde footer
  $modal.on('shown.bs.tab.cadServico', '#v-tab .tab-btn', toggleFooterByTab);

  // Delegação de inputs (substitui listener global em document)
  $modal.on('input.cadServico', '.numero-virgula-financeiro, .numero-inteiro, .numero-porcento, .numero-uma-virgula', function () {
    if (this !== document.activeElement) return;
    if (this.classList.contains('numero-virgula-financeiro') && typeof validarInput === 'function') {
      validarInput(this);
    } else if (this.classList.contains('numero-inteiro') && typeof validarInteiro === 'function') {
      validarInteiro(this);
    } else if (this.classList.contains('numero-porcento') && typeof validarPorcento === 'function') {
      validarPorcento(this);
    } //else if (this.classList.contains('numero-uma-virgula') && typeof validarNumeroVirgula === 'function') {
      //validarNumeroVirgula(this);

    //}

  });

  // -------------------- C R O P P E R  (reinserido) --------------------

  // Clique na foto → abre input file (só quando em modo edição)
  $modal.on('click.cadServico', '#img-foto_cadServico', function () {
    if (!editar) return;
    const inputFile = qs('#input-foto_cadServico');
    if (inputFile) inputFile.click();
  });

  // Troca do arquivo → abre área de crop e instancia Cropper
  $modal.on('change.cadServico', '#input-foto_cadServico', function (e) {
    const file = this.files && this.files[0];
    const cropArea = qs('#cropper-area');
    const preview  = qs('#preview-crop');
    if (!file || !cropArea || !preview) return;
    
    const btnSalvar = qs('#btn-salvar_servico');
    // mostra área de crop
    cropArea.style.display = 'block';
    btnSalvar.disabled=true;

    // destruir instância anterior (se houver)
    if (cropper) { try { cropper.destroy(); } catch(_) {} cropper = null; }
    if (fileURL) { URL.revokeObjectURL(fileURL); fileURL = null; }

    // cria URL do arquivo escolhido
    fileURL = URL.createObjectURL(file);
    preview.src = fileURL;

    preview.onload = () => {
      if (cropper) { try { cropper.destroy(); } catch(_) {} }
      // Requer a lib Cropper carregada na página
      cropper = new Cropper(preview, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 1,
        responsive: true
      });
    };
  });

  // Confirmar recorte
  $modal.on('click.cadServico', '#btn-crop-ok', function () {
    if (!cropper) return;
    const imgAvatar = qs('#img-foto_cadServico');
    const inputFile = qs('#input-foto_cadServico');
    const cropArea  = qs('#cropper-area');
    if (!imgAvatar || !inputFile || !cropArea) return;

    const canvas = cropper.getCroppedCanvas({
      width: 400,
      height: 400,
      imageSmoothingQuality: 'high'
    });

    canvas.toBlob((blob) => {
      if (!blob) return;

      // limpa URL anterior do recorte
      if (blobURL) { URL.revokeObjectURL(blobURL); blobURL = null; }
      blobURL = URL.createObjectURL(blob);

      // atualiza preview do avatar no formulário
      imgAvatar.src = blobURL;

      // injeta o recorte no input file (para enviar no submit)
      const newFile = new File([blob], 'foto_servico.jpg', { type: 'image/jpeg' });
      const dt = new DataTransfer();
      dt.items.add(newFile);
      inputFile.files = dt.files;
      const btnSalvar =qs('#btn-salvar_servico');
     // mostra área de crop
      btnSalvar.disabled=false;
      // esconde área e destrói cropper
      cropArea.style.display = 'none';
      try { cropper.destroy(); } catch(_) {}
      cropper = null;
    }, 'image/jpeg', 0.9);
  });

  // Cancelar recorte
  $modal.on('click.cadServico', '#btn-crop-cancel', function () {
    const cropArea = qs('#cropper-area');
    const inputFile = qs('#input-foto_cadServico');
    if (cropArea) cropArea.style.display = 'none';
    if (cropper) { try { cropper.destroy(); } catch(_) {} cropper = null; }
    if (fileURL) { URL.revokeObjectURL(fileURL); fileURL = null; }
    if (inputFile) inputFile.value = ''; // volta ao estado inicial
  });

  // -------------------- Submit do cadastro (com trava) --------------------
  $modal.on('submit.cadServico', '#formCadServico', function (e) {
    e.preventDefault();
    if (saving) return;
    saving = true;

    const form = this;
    const btnSalvar = qs('#btn-salvar_servico');
    if (btnSalvar) btnSalvar.disabled = true;

    const fd = new FormData(form);
    const agOnline = qs('#frm-agendamento_online')?.checked ? '1' : '0';
    const fidel    = qs('#frm-fidelidade')?.checked ? '1' : '0';
    const paralelo = ($modal.find('input[name="frm-paralelo"]:checked').val() ?? '0');
    const catText  = $modal.find('#frm-categoria option:checked').text().trim() || '';

    fd.set('frm-agendamento_online', agOnline);
    fd.set('frm-fidelidade', fidel);
    fd.set('frm-paralelo', paralelo);
    fd.set('frm-categoria_txt', catText);

    fetch('servicos/grava_cadastro.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        const msg = qs('#mensagem');
        if (res.success) {
          if (msg) { msg.className = 'text-success'; msg.textContent = 'Salvo com sucesso.'; }
          setCadastroEdit(false);

          if (res.data?.id) {
            const idInput = form.querySelector('input[name="id"]');
            if (idInput) idInput.value = res.data.id;
          }
          if (res.data?.foto_head) {
            const iHead = qs('#img-foto_head');
            if (iHead) iHead.src = res.data.foto_head;
          }
          if (res.data?.titulo) {
            const h = $modal.find('.modal-title')[0];
            if (h) h.innerHTML = res.data.titulo;
          }

          // Notifica a listagem externa recarregar
          document.dispatchEvent(new CustomEvent('servico:salvo', { detail: { id: res.data?.id || null } }));

          const navTab = qs('#v-tab');
          if (navTab) navTab.style.display = '';
        } else {
          if (msg) { msg.className = 'text-danger'; msg.textContent = res.msg || 'Falha ao salvar.'; }
        }
      })
      .catch(err => {
        const msg = qs('#mensagem');
        if (msg) { msg.className = 'text-danger'; msg.textContent = 'Erro de comunicação: ' + err; }
      })
      .finally(() => {
        saving = false;
        const btnSalvar = qs('#btn-salvar_servico');
        if (btnSalvar) btnSalvar.disabled = !editar;
      });
  });

  // -------------------- Limpeza no fechamento deste modal --------------------
  $modal.one('hidden.bs.modal.cadServico', function () {
    // limpa binds do namespace
    $modal.off('.cadServico');

    // destrói cropper e revoga URLs
    if (cropper) { try { cropper.destroy(); } catch(_) {} cropper = null; }
    if (fileURL) { URL.revokeObjectURL(fileURL); fileURL = null; }
    if (blobURL) { URL.revokeObjectURL(blobURL); blobURL = null; }

    // Se você já tem remove() e backdrop.remove() em outro trecho, não duplique.
    // $(this).remove(); $('.modal-backdrop').remove();
  });

  // -------------------- Inicialização visual --------------------
  setCadastroEdit(editar);
  toggleFooterByTab();
})();
