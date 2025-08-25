


var oculto=true;
var editar=false; 

function ocultarServicos(elemento) {

        const ths = document.querySelectorAll('th.minimizar');
        ths.forEach(th => {
          if (oculto) {
          th.style.display='none';
           } else {
            th.style.display='';
            }
        });
        if(oculto){
            elemento.style.display='none';
        }else{
            elemento.style.display='';
        }


       
}

document.querySelectorAll('#v-tab li.tab-btn').forEach(tab => {
   
  tab.addEventListener('click', () => {
    // Esconde os containers
    document.querySelectorAll('.container-documento-add, .container-foto-add')
      .forEach(el => el.style.display = 'none');
  });
});









function habilitarEditaCadastro() {
  const imgCadastro = document.getElementById('input-foto_cadColaborador')
  const campos = document.querySelectorAll(
    '#aba-cadastro input, ' +
    '#aba-cadastro select, ' +
    '#aba-cadastro textarea'
  );
  const btnSalvar = document.getElementById('btn-salvar_colaborador');
  const btnEditar = document.getElementById('btn-editar-cadastro');
  if (!editar) {
    // Se editar for false, bloqueia tudo
    campos.forEach(c => {
      c.disabled = true;
      if (c.tagName === 'INPUT' || c.tagName === 'TEXTAREA') {
        c.readOnly = true;
      }
    });
    btnSalvar.disabled = true;
    imgCadastro.style.pointerEvents=false;
    imgCadastro.style.cursor='';
    btnEditar.style.display='block';
  } else {
    // Se editar for true, habilita tudo
    campos.forEach(c => {
      c.disabled = false;
      if (c.tagName === 'INPUT' || c.tagName === 'TEXTAREA') {
        c.readOnly = false;
      }
    });
    btnSalvar.disabled = false;
    imgCadastro.style.pointerEvents=true;
    imgCadastro.style.cursor='pointer';

    btnEditar.style.display='none';
  }
}

// Executa na carga da página
habilitarEditaCadastro();

document.getElementById('btn-editar-cadastro').addEventListener('click', function() {
editar=true;
habilitarEditaCadastro();

});





document.getElementById('btnMinServicos').addEventListener('click', function() {
       
                const btn = document.getElementById('btnMinServicos');
     
        if (oculto==true){
            oculto=false;
        }else if (oculto==false){
            oculto=true;
        }
        const tds = document.querySelectorAll('td.minimizar');

        tds.forEach(td => {
            ocultarServicos(td);

        });

       
        const icon = btn.querySelector('i');
        icon.classList.toggle('bi-chevron-left');
        icon.classList.toggle('bi-chevron-right');
});


function iniciarDocumentosColaboradores(){
 
  let editingDocId = null;
  const container   = document.querySelector('.container-documento-add');
  const btnNova     = document.getElementById('btn-novo-documento');
  const btnCancelar = document.getElementById('btn-cancelar-documento');
  const btnSalvar   = document.getElementById('btn-salvar-documento');
  const fileInput   = document.getElementById('documentoUpload');
  const previewImg  = document.getElementById('preview-documento');
  const tituloInput = document.getElementById('documentoTitulo');
  const dataInput   = document.getElementById('documentoData');
  const tipoSelect  = document.getElementById('documentoTipo');
  const descTextarea= document.getElementById('documentoDescricao');
  const idColaborador   = document.querySelector('#formCadColaborador input[name="id"]').value;
  


function setEditMode(isEdit) {
  fileInput.disabled = isEdit;                          // trava o input
  previewImg.style.cursor = isEdit ? 'default' : 'pointer';
  if (isEdit) {
    previewImg.removeEventListener('click', openFileDialog);
  } else {
    previewImg.addEventListener('click', openFileDialog);
    fileInput.value = '';
    previewImg.src = 'sem-foto.svg';
  }
}

// handler genérico para abrir o dialog
function openFileDialog() {
  if (!fileInput.disabled) fileInput.click();
}



previewImg.addEventListener('click', openFileDialog);

  // mantém o preview carregando a miniatura
fileInput.addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;

  // atualiza preview de ícone
  const ext  = file.name.split('.').pop();
  const info = window.FileIconRegistry.get(ext);
  previewImg.className = `${info.icon} ${info.color}`;
  previewImg.style.fontSize = '2rem';

  // exibe nome e data…
});


  // “Nova Foto”
  btnNova.addEventListener('click', () => {

    setEditMode(false); 
    editingDocId = null;
    fileInput.value = '';
    previewImg.src = '../img/sem-foto.svg';
    tituloInput.value = '';
    dataInput.value = '';
    tipoSelect.value = '';
    descTextarea.value = '';
    container.style.display = 'block';
  });


// Substitua toda a função abrirFormDocumento por esta versão que usa FileIconRegistry:
window.abrirFormDocumento = function(documentoRow) {
  window.editingDocId = documentoRow.id;

  // elementos do form
  const previewIcon   = document.getElementById('preview-documento');       // <i>
  const nomeSpan      = document.getElementById('preview-documento-nome');
  const tituloInput   = document.getElementById('documentoTitulo');
  const dataInput     = document.getElementById('documentoData');
  const tipoSelect    = document.getElementById('documentoTipo');
  const descTextarea  = document.getElementById('documentoDescricao');
  const fileInput     = document.getElementById('documentoUpload');
  const container     = document.querySelector('.container-documento-add');

  // popula nome do arquivo
  nomeSpan.textContent = documentoRow.arquivo || '';

  // popula demais campos
  tituloInput.value   = documentoRow.titulo       || '';
  dataInput.value     = documentoRow.data_arquivo || '';
  tipoSelect.value    = documentoRow.tipo_arquivo || '';
  descTextarea.value  = documentoRow.descricao    || '';

  // bloqueia alteração de arquivo neste modo
  fileInput.disabled        = true;
  previewIcon.style.cursor  = 'default';

  // iconografia via FileIconRegistry
  const info = window.FileIconRegistry.get(documentoRow.extensao);
  previewIcon.className     = `${info.icon} ${info.color}`;
  previewIcon.style.fontSize= '2rem';

  // exibe o formulário
  container.style.display   = 'block';
};



    window.excluirDocumento = function(idDocumento) {
        if (!confirm('Excluir este documento')) return;
        $.post('colaboradores/excluir_documento.php', { id: idDocumento }, res => {
            if (res.success) {
                // recarrega tabela de galeria
                const tbl = document.querySelector('#documentos-container-modal .tablesModColaborador');
                const f = tbl.getAttribute('data-filtro');
                tbl.setAttribute('data-filtro', '');
                tbl.setAttribute('data-filtro', f);
            } else {
                alert(res.msg);
            }
        }, 'json');
    };


  // “Cancelar”
  btnCancelar.addEventListener('click', () => {
    container.style.display = 'none';
  });

  // Preview da imagem
fileInput.addEventListener('change', function() {
  const file = this.files[0];
  

  if (!file) return;

  // data e demais campos
  const lastMod = file.lastModified;
  if (lastMod) {
    dataInput.value = new Date(lastMod).toISOString().slice(0,10);
  }

  // mapeamento de extensões → ícones/bootstrap
  const extMap = {
    pdf:  { icon: 'bi-file-earmark-pdf-fill',   color: 'text-danger' },
    doc:  { icon: 'bi-file-earmark-word-fill',  color: 'text-primary' },
    docx: { icon: 'bi-file-earmark-word-fill',  color: 'text-primary' },
    xls:  { icon: 'bi-file-earmark-excel-fill', color: 'text-success' },
    xlsx: { icon: 'bi-file-earmark-excel-fill', color: 'text-success' },
    ppt:  { icon: 'bi-file-earmark-ppt-fill',   color: 'text-warning' },
    pptx: { icon: 'bi-file-earmark-ppt-fill',   color: 'text-warning' },
    zip:  { icon: 'bi-file-earmark-zip-fill',   color: 'text-secondary' },
    txt:  { icon: 'bi-file-earmark-text-fill',  color: 'text-muted' },
    default: { icon: 'bi-file-earmark-fill',    color: 'text-muted' }
  };

   const nomeSpan = document.getElementById('preview-documento-nome');
   const name = file.name;
  const ext  = name.split('.').pop().toLowerCase();
  const info = extMap[ext] || extMap.default;
 
  nomeSpan.textContent = name;

  // atualiza o <i id="preview-documento">
  previewImg.className = `${info.icon} ${info.color}`;
  previewImg.style.fontSize = '2rem';
});

  // “Salvar Foto” via AJAX
  btnSalvar.addEventListener('click', () => {

    
    if (!editingDocId && fileInput.files.length === 0) {
    alert('Selecione um documento.');
    return;
    }
    if (!tituloInput.value.trim()) {
        alert('Informe um título.');
        tituloInput.focus();
        return;
    }
    if (!dataInput.value) {
        alert('Informe a data da foto.');
        dataInput.focus();
        return;
    }
    if (!tipoSelect.value) {
        alert('Selecione o tipo de foto.');
        tipoSelect.focus();
        return;
    }

  const formData = new FormData();
  formData.append('id_cliente', idColaborador);
  if (editingDocId) formData.append('id', editingDocId);
  if (fileInput.files.length) formData.append('documento', fileInput.files[0]);
  formData.append('titulo', tituloInput.value.trim());
  formData.append('data_documento', dataInput.value);
  formData.append('tipo_documento', tipoSelect.value);
  if (descTextarea.value.trim()) formData.append('descricao', descTextarea.value.trim());

    $.ajax({
      url: 'colaboradores/inserir_documento.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          container.style.display = 'none';
          // recarrega galeria
          const tbl = document.querySelector('#documentos-container-modal .tablesModColaborador');
          const f = tbl.getAttribute('data-filtro');
          tbl.setAttribute('data-filtro', f);
          
        } else {
          alert(res.msg);
        }
      },
      error: function(xhr, status, err) {
        alert('Erro ao salvar foto: ' + err);
      }
    });
  });
}
iniciarDocumentosColaboradores();

function iniciarFotosColaboradores(){
 
  let editingFotoId = null;
  const container   = document.querySelector('.container-foto-add');
  const btnNova     = document.getElementById('btn-nova-foto');
  const btnCancelar = document.getElementById('btn-cancelar-foto');
  const btnSalvar   = document.getElementById('btn-salvar-foto');
  const fileInput   = document.getElementById('fotoUpload');
  const previewImg  = document.getElementById('preview-foto');
  const tituloInput = document.getElementById('fotoTitulo');
  const dataInput   = document.getElementById('fotoData');
  const tipoSelect  = document.getElementById('fotoTipo');
  const descTextarea= document.getElementById('fotoDescricao');
  const idColaborador   = document.querySelector('#formCadColaborador input[name="id"]').value;
  


function setEditMode(isEdit) {
  fileInput.disabled = isEdit;                          // trava o input
  previewImg.style.cursor = isEdit ? 'default' : 'pointer';
  if (isEdit) {
    previewImg.removeEventListener('click', openFileDialog);
  } else {
    previewImg.addEventListener('click', openFileDialog);
    fileInput.value = '';
    previewImg.src = 'sem-foto.svg';
  }
}

// handler genérico para abrir o dialog
function openFileDialog() {
  if (!fileInput.disabled) fileInput.click();
}



previewImg.addEventListener('click', openFileDialog);

  // mantém o preview carregando a miniatura
  fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = e => previewImg.src = e.target.result;
      reader.readAsDataURL(this.files[0]);
    }
  });


  // “Nova Foto”
  btnNova.addEventListener('click', () => {
    setEditMode(false); 
    editingFotoId = null;
    fileInput.value = '';
    previewImg.src = '../img/sem-foto.svg';
    tituloInput.value = '';
    dataInput.value = '';
    tipoSelect.value = '';
    descTextarea.value = '';
    container.style.display = 'block';
  });


    window.abrirFormFoto = function(fotoRow) {
    
        window.editingFotoId = fotoRow.id;

        // campos do form
        const previewImg   = document.getElementById('preview-foto');
        const tituloInput  = document.getElementById('fotoTitulo');
        const dataInput    = document.getElementById('fotoData');
        const tipoSelect   = document.getElementById('fotoTipo');
        const descTextarea = document.getElementById('fotoDescricao');
        const fileInput    = document.getElementById('fotoUpload');
        const container    = document.querySelector('.container-foto-add');

        // popula preview e campos
        previewImg.src       = fotoRow.arquivo_mini
                                ? `../${pastaFiles}/img/colaboradores/galeria/mini/${fotoRow.arquivo_mini}`
                                : 'sem-foto.svg';
        tituloInput.value    = fotoRow.titulo    || '';
        dataInput.value      = fotoRow.data_foto || '';
        tipoSelect.value     = fotoRow.tipo_foto || '';
        descTextarea.value   = fotoRow.descricao || '';

        // trava upload em edição
        fileInput.disabled   = true;
        previewImg.style.cursor = 'default';

        // exibe form
        container.style.display = 'block';
  };


    window.excluirFoto = function(idFoto) {
        if (!confirm('Excluir esta foto?')) return;
        $.post('colaboradores/excluir_foto.php', { id: idFoto }, res => {
            if (res.success) {
                // recarrega tabela de galeria
                const tbl = document.querySelector('#galeria-container-modal .tablesModColaborador');
                const f = tbl.getAttribute('data-filtro');
                tbl.setAttribute('data-filtro', '');
                tbl.setAttribute('data-filtro', f);
            } else {
                alert(res.msg);
            }
        }, 'json');
    };


  // “Cancelar”
  btnCancelar.addEventListener('click', () => {
    container.style.display = 'none';
  });

  // Preview da imagem
  fileInput.addEventListener('change', function() {
     if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => previewImg.src = e.target.result;
        reader.readAsDataURL(this.files[0]);

        // preenche o input de data com a última modificação do arquivo
        const lastMod = this.files[0].lastModified;
        if (lastMod) {
        const dt = new Date(lastMod).toISOString().slice(0,10);
        dataInput.value = dt;
        }
    }
  });

  // “Salvar Foto” via AJAX
  btnSalvar.addEventListener('click', () => {

    
    if (!editingFotoId && fileInput.files.length === 0) {
    alert('Selecione uma foto.');
    return;
  }
  if (!tituloInput.value.trim()) {
    alert('Informe um título.');
    tituloInput.focus();
    return;
  }
  if (!dataInput.value) {
    alert('Informe a data da foto.');
    dataInput.focus();
    return;
  }
  if (!tipoSelect.value) {
    alert('Selecione o tipo de foto.');
    tipoSelect.focus();
    return;
  }

  const formData = new FormData();
  formData.append('id_cliente', idColaborador);
  if (editingFotoId) formData.append('id', editingFotoId);
  if (fileInput.files.length) formData.append('foto', fileInput.files[0]);
  formData.append('titulo', tituloInput.value.trim());
  formData.append('data_foto', dataInput.value);
  formData.append('tipo_foto', tipoSelect.value);
  if (descTextarea.value.trim()) formData.append('descricao', descTextarea.value.trim());

    $.ajax({
      url: 'colaboradores/inserir_foto.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          container.style.display = 'none';
          // recarrega galeria
          const tbl = document.querySelector('#galeria-container-modal .tablesModColaborador');
          const f = tbl.getAttribute('data-filtro');
          tbl.setAttribute('data-filtro', '');
          tbl.setAttribute('data-filtro', f);
        } else {
          alert(res.msg);
        }
      },
      error: function(xhr, status, err) {
        alert('Erro ao salvar foto: ' + err);
      }
    });
  });
}
iniciarFotosColaboradores();














// carregar imagem -->



function imagemCropper(){
    let cropper = null;
    let webcamStream = null;

    // Ao clicar na imagem, decide origem
    document.getElementById("img-foto_cadColaborador").addEventListener("click", function() {
      if(editar==true){
        if (confirm("Deseja tirar uma foto com a webcam?")) {
            abrirWebcam();
        } else {
            document.getElementById("input-foto_cadColaborador").click();
        }
      }
    });

    // ====== ARQUIVO ======
    document.getElementById("input-foto_cadColaborador").addEventListener("change", function(e) {
        const file = e.target.files[0];
        if (!file) return;
        mostrarCropper(URL.createObjectURL(file));
    });

    // ====== WEBCAM ======
    function abrirWebcam() {
        document.getElementById('webcam-area').style.display = "block";
        document.getElementById('cropper-area').style.display = "none";
        // Solicita acesso à webcam
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                webcamStream = stream;
                document.getElementById('webcam').srcObject = stream;
            })
            .catch(function(err) {
                alert("Não foi possível acessar a webcam: " + err);
                document.getElementById('webcam-area').style.display = "none";
            });
    }

    // Capturar imagem da webcam → cropper
    document.getElementById("btn-capturar").addEventListener("click", function() {
        const video = document.getElementById('webcam');
        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        mostrarCropper(canvas.toDataURL("image/jpeg", 0.9));

        // Desliga webcam
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        document.getElementById('webcam-area').style.display = "none";
    });

    // Cancelar webcam
    document.getElementById("btn-cancelar-webcam").addEventListener("click", function() {
        document.getElementById('webcam-area').style.display = "none";
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
    });

    // ====== CROP ====
    function mostrarCropper(imgSrc) {
        // Mostra área cropper
        document.getElementById('cropper-area').style.display = 'block';
        
        // Limpa cropper antigo
        if (cropper) cropper.destroy();
        
        // Exibe imagem para crop
        const preview = document.getElementById('preview-crop');
        preview.src = imgSrc;
        preview.onload = function() {
        
            if (cropper) cropper.destroy();
                cropper = new Cropper(preview, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true
            });
        };
    }

    // OK do cropper
    document.getElementById("btn-crop-ok").addEventListener("click", function() {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({ width: 400, height: 400, imageSmoothingQuality: 'high' });
        canvas.toBlob(function(blob) {
                // Atualiza preview final
            const url = URL.createObjectURL(blob);
            document.getElementById('img-foto_cadColaborador').src = url;

            // Prepara novo arquivo para input file
            const newFile = new File([blob], "foto_cliente.jpg", { type: "image/jpeg" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(newFile);
            document.getElementById('input-foto_cadColaborador').files = dataTransfer.files;

            // Esconde cropper
            document.getElementById('cropper-area').style.display = 'none';

            cropper.destroy();
            cropper = null;
        }, 'image/jpeg', 0.8);
    });

    // Cancelar cropper
    document.getElementById("btn-crop-cancel").addEventListener("click", function() {
        document.getElementById('cropper-area').style.display = 'none';
        if (cropper) { cropper.destroy(); cropper = null; }
        document.getElementById('input-foto_cadColaborador').value = '';
    });
}

imagemCropper();


	$("#formCadColaborador").submit(function (event) {
		event.preventDefault();
		var pag = 'colaboradores';
		var formData = new FormData(this);

		$.ajax({
			url: "colaboradores/grava_cadastro.php",
			type: 'POST',
			data: formData,
			dataType: "json", // <-- Aqui força o jQuery a interpretar como JSON

			success: function (resposta) {
				$('#mensagem').removeClass();

				if (resposta.success) {
					// Sucesso
					editar=false;
                    habilitarEditaCadastro();

                    //$('#btn-fechar_cliente').click();
					// window.location = "index.php?pagina=" + pag; // descomente se quiser reload/redirect

				} else {
					$('#mensagem').addClass('text-danger');
					$('#mensagem').text(resposta.msg);
				}
			},
			error: function(xhr, status, error) {
				$('#mensagem').addClass('text-danger');
				$('#mensagem').text("Erro de comunicação com o servidor: " + error);
			},

			cache: false,
			contentType: false,
			processData: false,
			xhr: function () {
				var myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) {
					myXhr.upload.addEventListener('progress', function () {
						// Progresso do upload
					}, false);
				}
				return myXhr;
			}
		});
	});
