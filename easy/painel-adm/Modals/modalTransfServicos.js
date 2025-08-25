// 1) referências e estado


// variável que guarda a soma de todos os itens
var somaTotalItens = 0;



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





// dispara quando muda a quantidade de um item
function atualizaItemTotal(e) {
  const inputQtd = e.target;
  const tr       = inputQtd.closest("tr");
  const qtd      = parseInt(inputQtd.value, 10) || 0;
  const unit     = parseFloat(
    tr.querySelector(".valor-unitario-calc").value
  ) || 0;

  const total    = qtd * unit;
  const inputTot = tr.querySelector(".valor-total-item");
  const inputTotCalc = tr.querySelector(".valor-total-item-calc");
  // se quiser formatar com vírgula:
  inputTot.value = total.toFixed(2).replace(".", ",");
  inputTotCalc.value = total;
 atualizaResumoTransferencia();
}

// percorre todos os 'valor-total-item' e soma
function atualizaResumoTransferencia() {
  // recalcula somaTotalItens e monta lista de partes
  let soma = 0;
  const partes = [];
  document
    .querySelectorAll('#tabela-itensTransferencia_servicos tbody tr')
    .forEach(tr => {
      console.log('carregando item');
      const qtd = parseInt(tr.querySelector('input[name="qtd-transf[]"]').value, 10) || 0;
      if (qtd < 1) return;

      const nome = tr.querySelector('input[name="servico[]"]').value.trim();
      const unit = parseFloat(
        tr.querySelector('.valor-unitario-calc').value
      ) || 0;

      soma += qtd * unit;

      // pluraliza se preciso
      const label = qtd > 1 
        ? `${qtd} ${nome}s` 
        : `${qtd} ${nome}`;
      partes.push(label);
    });

  somaTotalItens = soma;

  // monta texto das sessões
  let textoServ;
  if (partes.length === 0) {
    textoServ = '';
  } else if (partes.length === 1) {
    textoServ = partes[0] + '.';
  } else {
    const last = partes.pop();
    textoServ = partes.join(', ') + ' e ' + last + '.';
  }

  // frase do valor
  const valorFormatado = DecimalBr(somaTotalItens);
  const extenso       = reaisPorExtensoBr(somaTotalItens, true);

  const fraseValor    = somaTotalItens? `A soma dos valores dos serviços transferidos totaliza R$ ${valorFormatado} (${extenso}).` : '';

  // coloca tudo no mesmo span
  const span = document.getElementById('sp-valor-transferencia_servicos');
  span.textContent = textoServ
    ? textoServ + ' ' + fraseValor
    : fraseValor;
}




async function carregarServicosDisponiveis(idCliente) {
  try {
    const res = await fetch(`api/servicos_disponiveis.php?id_cliente=${idCliente}`);
    const servicos = await res.json();
    const tbody = document.querySelector('#tabela-itensTransferencia_servicos tbody');
    tbody.innerHTML = ''; // limpa linhas antigas
    if (servicos.length>0){
        servicos.forEach(s => {
          const tr = document.createElement('tr');
          
          tr.innerHTML = `
            <td>
              <input name="data-venda[]" type="date" class="blockItem form-control" readonly value="${s.data_venda}">
            </td>
            <td>
              <input  class="blockItem form-control"  readonly  value="${s.tipo_venda ?? ''}">
            </td>
            <td>
              <input name="servico[]" class="blockItem" readonly value="${s.item}">
              <input type="hidden" name="id_servico[]" value="${s.id_item}">
            </td>
            <td hidden>
              <input type="hidden" name="id_transferencia[]" value="">
              <input type="hidden" name="id_comum[]" value="">
              <input type="hidden" name="id_venda_item[]" value="${s.id}">
            </td>
            <td>
              <input name="qtd-disp[]" class="blockItem" readonly value="${s.disponiveis}">
            </td>
            <td>
              <input name="valor-unitario[]" class="blockItem numero-virgula" readonly 
                    value="R$ ${DecimalBr(s.precoUn_efetivo)}">
              <input type="hidden" class="valor-unitario-calc" 
                    name="valor-unitario-calc[]" value="${s.precoUn_efetivo}">
            </td>
            <td>
              <input type="number" name="qtd-transf[]" class="form-control qtd-transf"
                    style="height:25px;text-align:center;width:50px;"
                    min="0" step="1" max="${s.disponiveis}" value="">
            </td>
            <td>
              <input name="valor-total-item[]" readOnly class="form-control valor-total-item blockItem" value="">
              <input type="hidden" name="valor-total-item-calc[]" class="valor-total-item-calc" class="form-control valor-total-item blockItem" value="">
            </td>
          `;
          
          tbody.prepend(tr);
          tr.querySelector(".qtd-transf").addEventListener("input", atualizaItemTotal);
        });

      document.querySelectorAll(".qtd-transf").forEach(inp => inp.addEventListener("input", atualizaItemTotal));
      document.getElementById('transferencia-container').style.display = 'block';
    }else{
      document.getElementById('transferencia-container').style.display = 'none';
    }


  } catch (err) {
    console.error('Erro ao carregar serviços disponíveis:', err);
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
    

    // Preenche sempre estes campos, se existirem:
    if (fields.includes('#sexo-cliente'))            document.querySelector('#sexo-cliente').value            = d.sexo || '';
    if (fields.includes('#cpf-cliente'))              document.querySelector('#cpf-cliente').value              = d.cpf  || '';
    if (fields.includes('#celular-cliente'))          document.querySelector('#celular-cliente').value          = d.celular || '';
    if (fields.includes('#email-cliente'))            document.querySelector('#email-cliente').value            = d.email   || '';

    if (!loadServices) {
      // destino
      if (fields.includes('#sexo-cliente-destino'))    document.querySelector('#sexo-cliente-destino').value    = d.sexo     || '';
      if (fields.includes('#cpf-cliente-recebe'))      document.querySelector('#cpf-cliente-recebe').value      = d.cpf      || '';
      if (fields.includes('#celular-cliente-recebe'))  document.querySelector('#celular-cliente-recebe').value  = d.celular  || '';
      if (fields.includes('#email-cliente-recebe'))    document.querySelector('#email-cliente-recebe').value    = d.email    || '';
    }

    // ícones e container
    if (loadServices) {
      toggleTransfere(true);
      document.querySelector('#ico-inputCliente').classList.replace('bi-person-plus','bi-eye');
      
      carregarServicosDisponiveis(c.id);
    } else {
      toggleRecebe(true);
      document.querySelector('#ico-inputClienteDestino').classList.replace('bi-person-plus','bi-eye');
    }
    // —————— foto ——————
      const fotoEl = document.querySelector(
        loadServices
          ? '#img-foto-cliente-modTransferencia_servicos'
          : '#img-foto-cliente-modTransferenciaServicos_destino'
      );
      const colFotoEl = document.querySelector(
        loadServices
          ? '#col-img-foto-cliente'
          : '#col-img-foto-cliente-destino'
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
      document.querySelector('#img-foto-cliente-modTransferencia_servicos').src = '../img/sem-foto.svg';
      document.querySelector('#col-img-foto-cliente').style.display = 'none';
    } else {
      toggleRecebe(false);
      document.querySelector('#ico-inputClienteDestino').classList.replace('bi-eye','bi-person-plus');
      document.querySelector('#img-foto-cliente-modTransferenciaServicos_destino').src = '../img/sem-foto.svg';
      document.querySelector('#col-img-foto-cliente-destino').style.display = 'none';
    }
    buscar(e.target.value.trim().toLowerCase());
  });
}

// inicializações
initClienteAutocomplete({
  inputSelector: '#nome-cliente',
  listSelector: '#lista-clientes',
  loadServices: true,
  fields: ['#id-cliente','#sexo-cliente','#cpf-cliente','#celular-cliente','#email-cliente']
});
initClienteAutocomplete({
  inputSelector: '#nome-cliente-recebe',
  listSelector: '#lista-clientes-destino',
  loadServices: false,
  fields: ['#id-cliente-destino','#sexo-cliente-destino','#cpf-cliente-recebe','#celular-cliente-recebe','#email-cliente-recebe']
});

function limparLinhasInativas() {
    const tabela = document.querySelector("#tabela-itensTransferencia_servicos");
    if (!tabela) return;

    const linhas = tabela.querySelectorAll("tbody tr");

    linhas.forEach(linha => {
        const inputQtd = linha.querySelector("input.qtd-transf");

        if (!inputQtd) return;

        const valor = parseFloat(inputQtd.value.replace(",", ".")) || 0;

        if (valor < 1) {
            linha.remove();
        } else {
            inputQtd.classList.add("blockItem");
        }
    });
}






document.getElementById('formTransferencia_servicos').addEventListener('submit', async function (e) {
  e.preventDefault();

  var msgBox   = document.getElementById('mensagem');
  const formTransf = document.getElementById('formTransferencia_servicos');
  const idOrigem  = document.getElementById('id-cliente').value;
  const idDestino = document.getElementById('id-cliente-destino').value;
  
  function limpaMsgbox(){ setTimeout(() => {msgBox.innerHTML = '';}, 5000);}


  if (!idOrigem || !idDestino) {
    msgBox.innerHTML = `<div class="alert alert-warning">Verifique se os clientes estão selecionados!</div>`;
    limpaMsgbox();
    return;
  }


  if (idOrigem === idDestino) {
    msgBox.innerHTML = `<div class="alert alert-warning">Não é possível transferir para o mesmo cliente.</div>`;
    limpaMsgbox();
    return;
  }

  // 2) calcula total de valor e total de quantidade
  let totalQty = 0;
  document.querySelectorAll('input[name="qtd-transf[]"]').forEach(inp => {
    totalQty += parseInt(inp.value, 10) || 0;
  });
  
  if (totalQty === 0 || somaTotalItens <= 0) {
    msgBox.innerHTML = `<div class="alert alert-warning">Selecione ao menos um serviço e valor maior que zero.</div>`;
    limpaMsgbox();
    return;
  }



  let plural = totalQty>1? 's':'';

  const { value: confirm } = await Swal.fire({
    title: 'Confirmar transferência?',
    text: `Você está prestes a transferir ${totalQty} serviço${plural} num total de R$ ${DecimalBr(somaTotalItens)}.`,
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
    const res = await fetch('conversoes/grava_TransfServicos.php', {
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

    // tenta parsear JSON, se for o caso

    const json = await res.json();
    if (!json.success) {
      msgBox.innerHTML = `<div class="alert alert-warning">${json.message}</div>`;
      limpaMsgbox();
      return;
    }
   
    limparLinhasInativas();
    document.getElementById('btn-salvar_venda').remove();
    document.getElementById('nome-cliente').classList.add('blockItem');
    document.getElementById('nome-cliente-recebe').classList.add('blockItem');
    
    const elComum   = document.querySelector('#id-comum');
    if (elComum)  elComum.value      = json.id_comum;
    Swal.fire({
      icon: 'success',
      title: 'Sucesso',
      text: 'Transferência efetuada com sucesso',
      confirmButtonText: 'OK'
    });
    
    msgBox.innerHTML = `<div class="alert alert-warning">Gravado com Sucesso!</div>`;
    limpaMsgbox();


  } catch (err) {
    // qualquer outro erro de rede/JS
    msgBox.innerHTML = `<div class="alert alert-danger">Falha ao conectar: ${err.message}</div>`;
    limpaMsgbox();
  }
});
