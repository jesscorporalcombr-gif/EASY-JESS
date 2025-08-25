// 1) referências e estado

var saldoTransfere = 0;
var saldoCliente = 0;
var saldoNovoCliente = 0;
var saldoClienteRecebe = 0;
var saldoNovoClienteRecebe = 0;


document.querySelector('textarea').style.overflow = 'hidden';

document.querySelector('textarea').addEventListener('input', e => {
  e.target.style.height = 'auto';
  e.target.style.height = e.target.scrollHeight + 'px';
});






function toggleTransfere(show) {
  var elsTransf = document.querySelectorAll('.blClienteTransfere');
  elsTransf.forEach(el => el.style.display = show ? 'block' : 'none');
}
function toggleRecebe(show) {
  var elsRecebe = document.querySelectorAll('.blClienteRecebe');
  elsRecebe.forEach(el => el.style.display = show ? 'block' : 'none');
}

// chama no início (antes de qualquer seleção) para esconder tudo:
toggleTransfere(false);
toggleRecebe(false);



  

  document.getElementById('anexo-transferencia').addEventListener('change', function() {
    const fileNameSpan = document.getElementById('nome-arquivo-anexo');
    const inputFileName = document.getElementById('input-nome-arquivo-anexo');
    if (this.files && this.files.length > 0) {
      // Pega só o nome do primeiro arquivo
      fileNameSpan.textContent = this.files[0].name;
      inputFileName.value = this.files[0].name;
    } else {
      fileNameSpan.textContent = '';
      inputFileName.value = '';
    }
  });




document.getElementById('valor-transferencia_saldo').addEventListener('input', function(event) {
    validarInput(event.target);

    const inputTransferir = document.getElementById('valor-transferencia_saldo');
    let transferir = document.getElementById('valor-transferencia_saldo').value;
    saldoTransfere = parseFloat(DecimalIngles(transferir));

    if (saldoTransfere>saldoCliente){
    inputTransferir.value = DecimalBr(saldoCliente);
    saldoTransfere=saldoCliente;
    }



    atualizarSaldos();

});

// variável que guarda a soma de todos os itens




function atualizarSaldos(){

  const valTransfere = document.getElementById('valor-transferencia');
  const inValtransfere = document.getElementById('valor-transferencia_saldo');

    
  valTransfere.value = saldoTransfere;

  //const inSaldoCliente = document.getElementById('saldo-cliente');
  const inNovoSaldoCliente = document.getElementById('novo-saldo-cliente');
  //const inSaldoClienteRecebe = document.getElementById('saldo-cliente-recebe');
  const inNovoSaldoClienteRecebe = document.getElementById('novo-saldo-cliente-recebe');
  saldoNovoCliente = saldoCliente-saldoTransfere;
  saldoNovoClienteRecebe = saldoClienteRecebe + saldoTransfere;

  inNovoSaldoCliente.value = 'R$ '+ DecimalBr(saldoNovoCliente);
  inNovoSaldoClienteRecebe.value = 'R$ '+ DecimalBr(saldoNovoClienteRecebe);

  if (saldoNovoCliente<0){
    inNovoSaldoCliente.classList.add('num-negativo');
    inNovoSaldoCliente.classList.remove('num-positivo');
  }else{
    inNovoSaldoCliente.classList.remove('num-negativo');
    inNovoSaldoCliente.classList.add('num-positivo');
  }

  
  if (saldoNovoClienteRecebe<0){
    inNovoSaldoClienteRecebe.classList.add('num-negativo');
    inNovoSaldoClienteRecebe.classList.remove('num-positivo');
  }else{
    inNovoSaldoClienteRecebe.classList.remove('num-negativo');
    inNovoSaldoClienteRecebe.classList.add('num-positivo');
  }


}

function initClienteAutocomplete({ inputSelector, listSelector, loadServices = false, fields = [] }) {
  const inputEl     = document.querySelector(inputSelector);
  const listaEl     = document.querySelector(listSelector);
  let selectedIndex = -1;

  function debounce(fn, wait = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), wait);
    };
  }

  function clearFields() {
    for (const sel of fields) {
      const el = document.querySelector(sel);
      if (el) el.value = '';
    }
  }

  const buscar = debounce(async termo => {
    if (!termo) {
      listaEl.style.display = 'none';
      return;
    }
    const res = await fetch(`api/clientes_search.php?search=${encodeURIComponent(termo)}`);
    const clientes = await res.json();
    listaEl.innerHTML = '';
    clientes.forEach((c, idx) => {
      const li = document.createElement('li');
      li.textContent   = c.nome;
      li.dataset.id    = c.id;
      li.dataset.index = idx;
      li.classList.add('item-cliente');
      li.addEventListener('click', () => selectClient(c));
      listaEl.append(li);
    });
    selectedIndex = -1;
    listaEl.style.display = clientes.length ? 'block' : 'none';
  }, 300);

  async function selectClient(c) {
    clearFields();
    inputEl.value = c.nome;
    document.querySelector(fields[0]).value = c.id;
    listaEl.innerHTML = '';
    listaEl.style.display = 'none';

    // busca dados completos
    const res = await fetch(`api/clientes_detalhes.php?id=${c.id}`);
    const d   = await res.json();
    const saldoTransfere = document.getElementById('saldo-cliente');
    const saldoRecebe = document.getElementById('saldo-cliente-recebe');

    // Preenche sempre estes campos, se existirem:
    if (fields.includes('#sexo-cliente'))            document.querySelector('#sexo-cliente').value            = d.sexo || '';
    if (fields.includes('#cpf-cliente'))              document.querySelector('#cpf-cliente').value              = cpfFormatado(d.cpf)  || '';
    if (fields.includes('#celular-cliente'))          document.querySelector('#celular-cliente').value          = celularFormatado(d.celular) || '';
    if (fields.includes('#email-cliente'))            document.querySelector('#email-cliente').value            = d.email   || '';
    if (fields.includes('#saldo-cliente'))            document.querySelector('#saldo-cliente').value            = 'R$ ' + DecimalBr(d.saldo)    || '';
    
    if (!loadServices) {
      // Recebe
      if (fields.includes('#sexo-cliente-recebe'))    document.querySelector('#sexo-cliente-recebe').value    = d.sexo     || '';
      if (fields.includes('#cpf-cliente-recebe'))      document.querySelector('#cpf-cliente-recebe').value      = cpfFormatado(d.cpf)      || '';
      if (fields.includes('#celular-cliente-recebe'))  document.querySelector('#celular-cliente-recebe').value  = celularFormatado(d.celular)  || '';
      if (fields.includes('#email-cliente-recebe'))    document.querySelector('#email-cliente-recebe').value    = d.email    || '';
      if (fields.includes('#saldo-cliente-recebe'))    document.querySelector('#saldo-cliente-recebe').value    = 'R$ ' + DecimalBr(d.saldo)   || '';
      if(parseFloat(d.saldo)<0){
        saldoRecebe.classList.add('num-negativo');
        saldoRecebe.classList.remove('num-positivo');
      }else{
        saldoRecebe.classList.add('num-positivo');
        saldoRecebe.classList.remove('num-negativo');
      }
    }

    // ícones e container
    if (loadServices) {
      toggleTransfere(true);
      document.querySelector('#ico-inputCliente').classList.replace('bi-person-plus','bi-eye');
      
     
      if (d.saldo){
            saldoCliente = parseFloat(d.saldo);
      }else{
        saldoCliente = 0;
      }

      if(saldoCliente>0){
        document.getElementById('transferencia-container').style.display = 'block';
      }


      if(parseFloat(d.saldo)<0){
            saldoTransfere.classList.add('num-negativo');
            saldoTransfere.classList.remove('num-positivo');
          }else{
            saldoTransfere.classList.add('num-positivo');
            saldoTransfere.classList.remove('num-negativo');
          }


    } else {
       toggleRecebe(true);
      if (d.saldo){
       saldoClienteRecebe = parseFloat(d.saldo);
      }else{
        saldoClienteRecebe = 0;
      }

      document.querySelector('#ico-inputClienteRecebe').classList.replace('bi-person-plus','bi-eye');
    }


    atualizarSaldos();
    // —————— foto ——————
      const fotoEl = document.querySelector(
        loadServices
          ? '#img-foto-cliente-modTransferencia_saldo'
          : '#img-foto-cliente-modTransferenciasaldo_recebe'
      );
      const colFotoEl = document.querySelector(
        loadServices
          ? '#col-img-foto-cliente'
          : '#col-img-foto-cliente-recebe'
      );

      if (d.foto) {
        fotoEl.src = `../img/clientes/${d.foto}`;
        colFotoEl.style.display = 'block';
      } else {
        // se não houver foto, esconde
        colFotoEl.style.display = 'none';
      }





  }

  // navegação por setas + scroll + Enter
  inputEl.addEventListener('keydown', e => {
    const items = Array.from(listaEl.querySelectorAll('li'));
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
      selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
      e.preventDefault();
    }
    if (e.key === 'ArrowUp') {
      selectedIndex = Math.max(selectedIndex - 1, 0);
      e.preventDefault();
    }
    items.forEach((li, idx) => {
      li.classList.toggle('selecionado', idx === selectedIndex);
      if (idx === selectedIndex) li.scrollIntoView({ block: 'nearest' });
    });
    if (e.key === 'Enter' && selectedIndex >= 0) {
      const li = items[selectedIndex];
      selectClient({ id: li.dataset.id, nome: li.textContent });
      e.preventDefault();
    }
  });

  // ao digitar
  inputEl.addEventListener('input', e => {
    clearFields();
   
   
    if (loadServices) {
      toggleTransfere(false);
      document.querySelector('#ico-inputCliente').classList.replace('bi-eye','bi-person-plus');
      document.getElementById('transferencia-container').style.display = 'none';
      document.querySelector('#img-foto-cliente-modTransferencia_saldo').src = '../img/sem-foto.svg';
      document.querySelector('#col-img-foto-cliente').style.display = 'none';

    } else { 
      toggleRecebe(false);
      document.querySelector('#ico-inputClienteRecebe').classList.replace('bi-eye','bi-person-plus');
      document.querySelector('#img-foto-cliente-modTransferenciasaldo_recebe').src = '../img/sem-foto.svg';
      document.querySelector('#col-img-foto-cliente-recebe').style.display = 'none';
    }
    buscar(e.target.value.trim().toLowerCase());
  });
}
 
// inicializações
initClienteAutocomplete({
  inputSelector: '#nome-cliente',
  listSelector: '#lista-clientes',
  loadServices: true,
  fields: ['#id-cliente','#sexo-cliente','#cpf-cliente','#celular-cliente','#email-cliente', '#saldo-cliente']
});

initClienteAutocomplete({
  inputSelector: '#nome-cliente-recebe',
  listSelector: '#lista-clientes-recebe',
  loadServices: false,
  fields: ['#id-cliente-recebe','#sexo-cliente-recebe','#cpf-cliente-recebe','#celular-cliente-recebe','#email-cliente-recebe', '#saldo-cliente-recebe']
});






document.getElementById('formTransferencia_saldo').addEventListener('submit', async function (e) {
  e.preventDefault();
  
  const formTransf = document.getElementById('formTransferencia_saldo');
  var msgBox   = document.getElementById('mensagem');
  const idOrigem  = document.getElementById('id-cliente').value;
  const idRecebe = document.getElementById('id-cliente-recebe').value;
  
  function limpaMsgbox(){ setTimeout(() => {msgBox.innerHTML = '';}, 5000);}
  
  if (idOrigem === idRecebe) {
    msgBox.innerHTML = `<div class="alert alert-warning">Não é possível transferir para o mesmo cliente.</div>`;
    limpaMsgbox();
    return;
  }

  // 2) calcula total de valor e total de quantidade

  const nomeClienteRecebe = document.getElementById('nome-cliente');
  const nomeClienteTransfere = document.getElementById('nome-cliente-recebe');

  if (saldoTransfere <1) {
    msgBox.innerHTML = `<div class="alert alert-warning">O Valor transferido não pode ser inferior a R$ 1,00!</div>`;
    limpaMsgbox();
    return;
  }

  const { value: confirm } = await Swal.fire({
    title: 'Confirmar transferência?',
    text: `Você confirma a transferrência de R$ ${DecimalBr(saldoTransfere)}, (${reaisPorExtenso(saldoTransfere)}), de ${nomeClienteRecebe.value} para ${nomeClienteTransfere.value}?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sim, transferir',
    cancelButtonText: 'Não, cancelar'
  });

  if (!confirm) {
    // usuário cancelou
    return;
  }
  // agora sim continua com o fetch…
  msgBox.textContent = '';
 

  try {
    const res = await fetch('conversoes/grava_TransfSaldo.php', {
      method: 'POST',
      body: new FormData(formTransf)
    });

    // lê o body inteiro como texto
    if (!res.ok) {
      const errText = await res.text();
      msgBox.innerHTML = `<div class="alert alert-danger">Erro no servidor: ${errText}</div>`;
      limpaMsgbox();
      return;
    }
    const json = await res.json()
    if (!json.success) {
      msgBox.innerHTML = `<div class="alert alert-warning">Erro Json: ${json.message}</div>`;
      limpaMsgbox();
    } else {
      // 1) Preenche os campos (inputs ou spans) com os IDs retornados
      const elComum   = document.querySelector('#id-comum');
      const elEnvio   = document.querySelector('#id-venda-enveia');
      const elRecebe  = document.querySelector('#id-venda-recebe');

      if (elComum)  elComum.value      = json.id_comum;
      if (elEnvio)  elEnvio.value      = json.id_envio;
      if (elRecebe) elRecebe.value     = json.id_recebimento;

      nomeClienteRecebe.classList.add('blockItem');
      nomeClienteTransfere.classList.add('blockItem');
      document.querySelector('#btn-salvar_venda').remove();
      document.querySelector('#valor-transferencia_saldo').remove();

      // se forem spans/divs em vez de inputs, use innerText:
      // if (elComum)  elComum.innerText  = json.id_comum;
      // ...

      // 2) Mostra alerta de sucesso com SweetAlert2
      Swal.fire({
        icon: 'success',
        title: 'Sucesso',
        text: 'Transferência efetuada com sucesso',
        confirmButtonText: 'OK'
      });
      msgBox.innerHTML = `<div class="alert alert-warning">Gravado com Sucesso</div>`;
      limpaMsgbox();
      return;
    }
        
  } catch (err) {
    // qualquer outro erro de rede/JS
    msgBox.innerHTML = `<div class="alert alert-danger">Falha ao conectar: ${err.message}</div>`;
  }
});
