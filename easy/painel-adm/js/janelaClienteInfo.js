let infoDiv;
  function carregaDadosCliente(){
        
    const hiddenId = document.querySelector('#janela_id_cliente');
    const nomeCli  = document.querySelector('#janela_nome_cliente');
    const fotoElem = document.querySelector('#janela_foto_cliente');
  
    if (!hiddenId || hiddenId=='') return;          // não existe na janela -> sai

    const grupo = nomeCli.closest('.input-group');
    
    
    const id = hiddenId.value;
    if (!id) {             // limpou seleção
      fotoElem.src = '';
      if (infoDiv) {
        infoDiv.innerHTML = '';
      
      }
      return;

    }

    fetch(`./endPoints/clientes-busca-dados-principais.php?id=${encodeURIComponent(id)}`)
      .then(r => r.ok ? r.json() : Promise.reject(r.status))
      .then(data => {
        fotoElem.src = data.foto ? `../${pastaFiles}/img/clientes/${data.foto}` : `../img/sem-foto.svg`;

        if (!infoDiv) {
          infoDiv = document.createElement('div');
          infoDiv.id = 'janela_cliente_info';
          infoDiv.className = 'janela-cliente-dados';
          infoDiv.style.cursor = 'pointer';
          infoDiv.style.marginLeft='10px';
          grupo.insertAdjacentElement('afterend', infoDiv);
        }

        infoDiv.setAttribute('onclick', `abrirModal('modalClientes', ${id})`);
        infoDiv.innerHTML = `
          <p><strong>Telefone:</strong> ${data.celular || '-'}</p>
          <p><strong>Nascimento:</strong> ${data.aniversario || '-'}</p>
          <p><strong>Cadastrado em:</strong> ${data.data_cadastro || '-'}</p>
        `;

      })
      .catch(e => console.error('Erro ao buscar cliente', e));
  };

  
  
  const input = document.getElementById('janela_id_cliente');
  input.addEventListener('input', e => {
  console.log('valor mudou para', e.target.value);
  //buscaDetalhada(e.target.value);
   carregaDadosCliente();
   updateAllVirtualAppointments();
  });


