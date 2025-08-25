// 1) referências e estado


// variável que guarda a soma de todos os itens
var somaTotalItens = 0;

function toggleConverte(show) {
  var elsTransf = document.querySelectorAll('.blClienteConverte');
  elsTransf.forEach(el => el.style.display = show ? 'block' : 'none');
}
toggleConverte(false);



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
  atualizaSomaTotal();
}

// percorre todos os 'valor-total-item' e soma
function atualizaSomaTotal() {
  const inputTotal = document.getElementById('input-valor-total-conversao');
  const inputTotalCalc = document.getElementById('input-valor-total-conversao-calc');
  spTotal = document.getElementById('sp-valor-conversao');
  spSaldoFinal = document.getElementById('sp-saldo-final');
  spSaldoCliente = document.getElementById('sp-saldo-cliente');
  let soma = 0;
  document.querySelectorAll(".valor-total-item").forEach(inp => {
    const v = parseFloat(inp.value.replace(",", ".")) || 0;
    soma += v;
  });
  somaTotalItens = soma;

  inputTotal.value = 'R$ ' + DecimalBr(somaTotalItens);
  inputTotalCalc.value = somaTotalItens;
  saldoFinalCliente = parseFloat(somaTotalItens) + parseFloat(saldoCliente);
  console.log('Saldo total do cliente:', saldoFinalCliente);
 
  
  if (saldoFinalCliente < 0 ){
    console.log('saldo menor que zero');
    spSaldoFinal.classList.add('num-negativo');
    spSaldoFinal.classList.remove('num-positivo');
  }else{
    console.log('saldo maior que zero');
    spSaldoFinal.classList.add('num-positivo');
    spSaldoFinal.classList.remove('num-negativo');
  }

  spTotal.textContent = 'R$ ' + DecimalBr(somaTotalItens);
  spSaldoFinal.textContent = 'R$ ' + DecimalBr(saldoFinalCliente);
  


  // console.log("Soma geral:", somaTotalItens);
}

function limparLinhasInativas() {
    const tabela = document.querySelector("#tabela-itensConversao");
    if (!tabela) return;

    const linhas = tabela.querySelectorAll("tbody tr");

    linhas.forEach(linha => {
        const inputQtd = linha.querySelector("input.qtd-convert");

        if (!inputQtd) return;

        const valor = parseFloat(inputQtd.value.replace(",", ".")) || 0;

        if (valor < 1) {
            linha.remove();
        } else {
            inputQtd.classList.add("blockItem");
        }
    });
}


async function carregarServicosDisponiveis(idCliente) {
  try {
    const res = await fetch(`api/servicos_disponiveis.php?id_cliente=${idCliente}`);
    const servicos = await res.json();
    const tbody = document.querySelector('#tabela-itensConversao tbody');
    tbody.innerHTML = ''; // limpa linhas antigas

    servicos.forEach(s => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <input name="data-venda[]" type="date" style="width:110px" class="blockItem form-control" readonly value="${s.data_venda}">
        </td>
         <td>
          <input  class="blockItem form-control"  style="width:150px" readonly  value="${s.tipo_venda ?? ''}">
        </td>
        <td>
          <input name="servico[]" style="width:250px"class="blockItem" readonly value="${s.item}">
          <input type="hidden" name="id_servico[]" value="${s.id_item}">
        </td>
        <td hidden>
          <input type="hidden" name="id_conversao[]" value="">
          <input type="hidden" name="id_venda[]" value="${s.id_venda}">
          <input type="hidden" name="id_venda_item[]" value="${s.id}">
        </td>
        <td>
          <input name="qtd-disp[]" style="width:30px" class="blockItem qtd-disp" readonly value="${s.disponiveis}">
        </td>
        <td>
          <input name="valor-unitario[]" style="width:130px" class="blockItem numero-virgula" readonly 
                value="R$ ${DecimalBr(s.precoUn_efetivo)}">
          <input type="hidden" class="valor-unitario-calc" 
                name="valor-unitario-calc[]" value="${s.precoUn_efetivo}">
        </td>
        <td>
          <input type="number" name="qtd-convert[]" style="width:50px" class="form-control qtd-convert"
                style="height:25px;text-align:center;width:50px;"
                min="0" step="1" max="${s.disponiveis}" value="">
        </td>
        <td>
          <input name="valor-total-item[]" readOnly style="width:130px" class="form-control valor-total-item blockItem" value="">
          <input type="hidden" name="valor-total-item-calc[]" class="valor-total-item-calc" class="form-control valor-total-item blockItem" value="">
        </td>
      `;
      tbody.prepend(tr);
      tr.querySelector(".qtd-convert").addEventListener("input", atualizaItemTotal);
    });
    const trf = document.createElement('tr');
    trf.innerHTML = `
                    <tr class="backGround-dataTable-head">
                    <td hidden></td>
                    <td class="backGround-dataTable-head" style="border-top: black solid 3px; height:52px;"></td>
                    <td class="backGround-dataTable-head" style="border-top: black solid 3px; height:52px;"></td>
                    <td class="backGround-dataTable-head" style="border-top: black solid 3px; height:52px;"></td>
                     <td class="backGround-dataTable-head" style="border-top: black solid 3px; height:52px;"></td>
                     <td class="backGround-dataTable-head" style="border-top: black solid 3px; height:52px;"></td>
                    <td class="backGround-dataTable-head text-dataTable-head" style="border-top: black solid 3px; height:52px; text-align:right; font-weigth: 700;">TOTAL:</td>
                    <td class="backGround-dataTable-head" style="border-top: black solid 3px; height:52px;">
                      <input name="valor-total-conversao" style="font-size:16px; text-align:left; margin-left:-35px;font-weigth: 700;" id="input-valor-total-conversao" class="form-control blockItem text-dataTable-head" readOnly value="">
                      <input type="hidden" name="valor-total-conversao-calc" id="input-valor-total-conversao-calc" readOnly value="">
                    </td>

                  </tr>`;

    tbody.appendChild(trf);



    document.querySelectorAll(".qtd-convert").forEach(inp => inp.addEventListener("input", atualizaItemTotal));


  } catch (err) {
    console.error('Erro ao carregar serviços disponíveis:', err);
  }
}




function iniciarConsultaClientes(){
  const inputEl = document.querySelector("#nome-cliente");
  const listaEl  = document.querySelector("#lista-clientes");
  let selecionadoIndexV = -1;

  // 2) debounce auxiliar
  function debounce(fn, wait = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), wait);
    };
  }

  // 3) busca no servidor só id+nome
  const buscarClientes = debounce(async termo => {
    if (!termo) {
      listaEl.style.display = "none";
      return;
    }
    try {
      const res = await fetch(`api/clientes_search.php?search=${encodeURIComponent(termo)}`);
      const clientes = await res.json();

      listaEl.innerHTML = "";
      clientes.forEach((c, idx) => {
        const li = document.createElement("li");
        li.textContent      = c.nome;
        li.dataset.id       = c.id;
        li.dataset.index    = idx;
        li.classList.add("item-cliente");
        li.addEventListener("click", () => selecionarCliente(c.id, c.nome));
        listaEl.appendChild(li);
      });

      selecionadoIndexV = -1;
      listaEl.style.display = clientes.length ? "block" : "none";
    } catch (err) {
      console.error("Erro ao buscar clientes:", err);
    }
  }, 300);

  // 4) função para buscar detalhes e preencher formulário
  async function selecionarCliente(id, nome) {
    // preenche nome e ID imediatamente
    inputEl.value = nome;
    document.querySelector("#id-cliente").value = id;

    // limpa visual da lista
    listaEl.innerHTML = "";
    listaEl.style.display = "none";

    // troca ícone e revela proposta
    document.getElementById("conversao-container").style.display = "block";
    document.getElementById("ico-inputCliente")
            .classList.replace("bi-person-plus","bi-eye");

    try {
      const res = await fetch(`api/clientes_detalhes.php?id=${id}`);
      const c   = await res.json();

      // preenche o restante dos campos
      document.querySelector("#sexo-cliente").value    = c.sexo;
      document.querySelector("#cpf-cliente").value     = c.cpf;
      document.querySelector("#celular-cliente").value = c.celular;
      document.querySelector("#email-cliente").value   = c.email;
      toggleConverte(true);

      // foto
      if (c.foto) {
        document.querySelector("#img-foto-cliente-modConversao")
                .src = `../img/clientes/${c.foto}`;
        document.querySelector("#col-img-foto-cliente")
                .style.display = "block";
      }

      // saldo
      const spSaldo = document.getElementById("sp-saldo-cliente");
      const spSaldoFinal = document.getElementById("sp-saldo-cliente");
      spSaldo.textContent = 'R$ ' + DecimalBr(c.saldo);
      spSaldoFinal.textContent = 'R$ ' + DecimalBr(c.saldo);

      saldoCliente = c.saldo;
      spSaldo.classList.toggle("num-positivo", parseFloat(c.saldo) > 0);
      spSaldo.classList.toggle("num-negativo", parseFloat(c.saldo) <= 0);
      spSaldoFinal.classList.toggle("num-positivo", parseFloat(c.saldo) > 0);
      spSaldoFinal.classList.toggle("num-negativo", parseFloat(c.saldo) <= 0);

    } catch (err) {
      console.error("Erro ao carregar detalhes:", err);
    }

    await carregarServicosDisponiveis(id);
  }

  // 5) navegação por teclado
  inputEl.addEventListener("keydown", e => {
    const itens = listaEl.querySelectorAll("li");
    if (!itens.length) return;

    if (e.key === "ArrowDown") {
      selecionadoIndexV = Math.min(selecionadoIndexV + 1, itens.length - 1);
      atualizarSelecao(itens);
      e.preventDefault();
    }
    if (e.key === "ArrowUp") {
      selecionadoIndexV = Math.max(selecionadoIndexV - 1, 0);
      atualizarSelecao(itens);
      e.preventDefault();
    }
    if (e.key === "Enter") {
      if (selecionadoIndexV >= 0) {
        const li = itens[selecionadoIndexV];
        selecionarCliente(li.dataset.id, li.textContent);
      }
      e.preventDefault();
    }
  });

  function atualizarSelecao(itens) {
    itens.forEach((li, idx) => {
      li.classList.toggle("selecionado", idx === selecionadoIndexV);
      if (idx === selecionadoIndexV) {
        li.scrollIntoView({ block: "nearest" });
      }
    });
  }

  // 6) listener principal de `input`
  inputEl.addEventListener("input", e => {
    // limpa campos ao digitar
    ["#id-cliente", "#sexo-cliente", "#cpf-cliente",
    "#celular-cliente", "#email-cliente"]
      .forEach(sel => document.querySelector(sel).value = "");

      toggleConverte(false);

    document.getElementById("conversao-container").style.display = "none";
    document.getElementById("ico-inputCliente")
            .classList.replace("bi-eye","bi-person-plus");
    document.getElementById("sp-saldo-cliente").textContent = "";
    document.querySelector("#img-foto-cliente-modConversao")
            .src = "../img/sem-foto.svg";
    document.querySelector("#col-img-foto-cliente")
            .style.display = "none";

    // dispara autocomplete
    const termo = e.target.value.trim().toLowerCase();
    buscarClientes(termo);
  });
}

iniciarConsultaClientes();




function checarValoresConversao() {
    const linhas = document.querySelectorAll("#tabela-itensConversao tbody tr");
    let tudoCerto = true;
    console.log('checando os valores de conversão');
    let soma = 0;
    linhas.forEach((linha, index) => {
        const inputQtd = linha.querySelector("input.qtd-convert");
        const inputDisp = linha.querySelector("input.qtd-disp");

        if (!inputQtd || !inputDisp) return;

      const qtd = isNaN(parseFloat(inputQtd.value)) ? 0 : parseFloat(inputQtd.value);
      const qtdDisp = isNaN(parseFloat(inputDisp.value)) ? 0 : parseFloat(inputDisp.value);

        console.log('a quantidade do input é:' , qtd);
        // Verificações
        const naoInteiro = !Number.isInteger(qtd);
        const negativo = qtd < 0;
        const menorOuIgualZero = qtd <= 0;
        const maiorQueDisponivel = qtd > qtdDisp;

        if (naoInteiro || negativo || maiorQueDisponivel) {
            tudoCerto = false;
            linha.classList.add("erro-validacao");

            // Opcional: exibir mensagem ao lado
            //inputQtd.setCustomValidity("Valor inválido para conversão.");
        } else {
            linha.classList.remove("erro-validacao");
            //inputQtd.setCustomValidity(""); // limpa erro
        }
        soma = soma +qtd;

    });


    if(soma==0){
      tudoCerto=false;
    }

    return tudoCerto;
}





var permiteGravar=true;

document.getElementById('formConversao').addEventListener('submit', async function (e) {
  e.preventDefault();
  if(!permiteGravar){
    return;
  }

  const formConv = document.querySelector('#formConversao');
  const msgBox   = document.getElementById('mensagem');
  msgBox.textContent = '';
  const inputIdCliente = document.getElementById('id-cliente');
  
  function limpaMsgbox(){ setTimeout(() => {msgBox.innerHTML = '';}, 5000);}

  if(inputIdCliente.value===''){
    msgBox.innerHTML = `<div class="alert alert-warning">Nenhum cliente selecionado!</div>`;
    limpaMsgbox();
    return;
  }
  if (!checarValoresConversao()) {
    msgBox.innerHTML = `<div class="alert alert-warning">Há valores inválidos na conversão!</div>`;
    limpaMsgbox();
    return;
  }
  let totalQty = 0;
  document.querySelectorAll('.qtd-convert').forEach(inp => {
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
    text: `Você está prestes a converter ${totalQty} serviço${plural} num total de R$ ${DecimalBr(somaTotalItens)}.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sim, converter',
    cancelButtonText: 'Não, cancelar'
  });

  if (!confirm) {
    // usuário cancelou
    return;
  }
  // agora sim continua com o fetch…
  msgBox.textContent = '';

  try {
    const res = await fetch('conversoes/grava_conversao.php', {
      method: 'POST',
      body: new FormData(formConv)
    });

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
      //$('#modalConversao').modal('hide');
      //location.reload();
      msgBox.innerHTML = `<div class="alert alert-warning">${json.message}</div>`;
      document.getElementById('id_comum').value = json.id_comum;
      document.getElementById('nome-cliente').classList.add('blockItem');
      document.getElementById('btn-salvar_venda').remove();
      permiteGravar=false;

      limparLinhasInativas();
      limpaMsgbox();
    }

  } catch (err) {
    // qualquer outro erro de rede/JS
    msgBox.innerHTML = `<div class="alert alert-danger">Falha ao conectar: ${err.message}</div>`;
    limpaMsgbox();
  }
});
