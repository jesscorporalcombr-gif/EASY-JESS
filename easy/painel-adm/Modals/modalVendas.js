

function corMargem(valor) {
  if (valor < 2) {
    return 'rgb(128, 0, 128)';   // roxo
  } else if (valor < 7) {
    return 'rgb(255, 0, 0)';     // vermelho
  } else if (valor < 12) {
    return 'rgb(255, 192, 203)'; // rosa
  } else if (valor < 17) {
    return 'rgb(255, 165, 0)';   // laranja
  } else if (valor < 20) {
    return 'rgb(255, 255, 0)';   // amarelo
  } else if (valor < 25) {
    return 'rgb(0, 128, 0)';     // verde
  } else {
    return 'rgb(46, 136, 209)';     // azul
  }
}





// Botão Avançado - exibe/esconde colunas extras
function toggleAvancado() {
    avancadoVisivel = !avancadoVisivel;
    const colunasAvancadas = document.querySelectorAll('.col-avancada');
    colunasAvancadas.forEach(col => {
        if (avancadoVisivel) {
        col.classList.remove('hidden-col');
        } else {
        col.classList.add('hidden-col');
        }
    });
}


    // Seleção de item => carrega Preço Tabela e Preço Cobrado
function atualizarPreco(tr){

    const tipo = tr.querySelector(".tipo-item").value;
    const item = tr.querySelector(".item-select");
    const IDitem = item.getAttribute('selected-data-item-id');
    const itemId = tr.querySelector('.id-item');
    const precoTabela = tr.querySelector(".preco-tabela");
    const precoCobrado = tr.querySelector(".preco-cobrado");
    const quantidadeEl = tr.querySelector(".quantidade");
    const precoTotalEl = tr.querySelector(".preco-total");
    const percDescEl = tr.querySelector(".perc-desc");
    const valDescEl     = tr.querySelector(".valor-desconto");
    const custoOpEl     = tr.querySelector(".custo-operacional");
    const custoAdmEl     = tr.querySelector(".custo-adm");
    const impostoEl     = tr.querySelector(".imposto");
    const txCartEl     = tr.querySelector(".taxa-cartao");
    const lBrutEl     = tr.querySelector(".lucro-bruto");
    const lLiquEl     = tr.querySelector(".lucro-liquido");
    const margemEl     = tr.querySelector(".margem");

    let PTA = 0;
    let PCO = 0;
    let QTD = 0;
    let PTO = 0;
    let PDE = 0;
    let VDE = 0;
    let COP = 0;
    let CAD = 0;
    let IMP = 0;
    let TCA = 0;
    let LBR = 0;
    let LLI = 0;
    let MAR = 0;
    let CUTT = 0; //custo total

    let itemObj = null;
      
    if(tipo === 'produto') {
    itemObj = produtosArray.find(el => el.id == IDitem);
    } else if(tipo === 'servico') {
    itemObj = servicosArray.find(el => el.id == IDitem);
    }

    if(itemObj) {
        IDIT = itemObj.id
    
        PTA = parseFloat(itemObj.valor_venda) || 0 ;
        PCO = parseFloat(itemObj.valor_venda) || 0 ;
        QTD = 1 || 0;
        PTO = parseFloat(itemObj.valor_venda) || 0 ;
        PDE = 0 || 0 ;
        VDE = 0 || 0 ;
        COP = parseFloat(itemObj.valor_custo) || 0 ;
        CAD = (parseFloat(itemObj.tempo) * custoHora / 60) || 0 ;
        IMP = (PTA * impostoVenda/100) || 0;
        TCA = ((PTA * taxaMedia) / 100) || 0;
        CUTT = (COP + CAD + IMP + TCA) || 0;
        LBR = PTO - COP;
        LLI = PTO - CUTT;
        
        if (PTO ==0){
            MAR =0;
        }
            else{
            MAR= ((PTO / CUTT)-1)*100;
        }

        itemId.value = IDIT;
 
        precoTabela.value  = DecimalBr(PTA) || itemObj.valor || '';
        precoCobrado.value = DecimalBr(PCO) || itemObj.valor || '';
        quantidadeEl.value = QTD;
        precoTotalEl.value = DecimalBr(PTO) || itemObj.valor || '';
        percDescEl.value = '0,00';
        valDescEl.value = '0,00';
        custoOpEl.value = DecimalBr(COP);
        custoAdmEl.value = DecimalBr(CAD);
        impostoEl.value = DecimalBr(IMP);
        txCartEl.value = DecimalBr(TCA);
        lBrutEl.value = DecimalBr(LBR);
        lLiquEl.value = DecimalBr(LLI);
        margemEl.value = DecimalBr(MAR);
        margemEl.style.borderColor = corMargem(MAR);
        percDescEl.style.borderColor = corMargem(MAR);
       
    } else {
        itemId.value = '';
        precoTabela.value  = '';
        precoCobrado.value = '';
        quantidadeEl.value = 1;
        precoTotalEl.value = '0,00';
        percDescEl.value = '0,00';
        valDescEl.value = '0,00';
        custoOpEl.value = '0,00';
        custoAdmEl.value ='0,00';
        impostoEl.value ='0,00';
        txCartEl.value = '0,00';
        lBrutEl.value = '0,00';
        lLiquEl.value = '0,00';
        margemEl.value = '0,00';
        margemEl.style.borderColor = corMargem(MAR);
        percDescEl.style.borderColor = corMargem(MAR);
    }
} //Fim de atualizar preço




    // Preenche <select class="item-select"> de acordo com tipo
function atualizarItens(tr, tipo, selectedId, selectedItemName){
    const selectItem = tr.querySelector(".item-select");

    const idItem = tr.querySelector('.id-item');
    const precoTabelaEl  = tr.querySelector(".preco-tabela");
    const precoCobradoEl = tr.querySelector(".preco-cobrado");
    const quantidadeEl   = tr.querySelector(".quantidade");
    const precoTotalEl   = tr.querySelector(".preco-total");
    const percDescEl     = tr.querySelector(".perc-desc");
    const valDescEl     = tr.querySelector(".valor-desconto");
    const custoOpEl     = tr.querySelector(".custo-operacional");
    const custoAdmEl     = tr.querySelector(".custo-adm");
    const impostoEl     = tr.querySelector(".imposto");
    const txCartEl     = tr.querySelector(".taxa-cartao");
    const lBrutEl     = tr.querySelector(".lucro-bruto");
    const lLiquEl     = tr.querySelector(".lucro-liquido");
    const margemEl     = tr.querySelector(".margem");


     
    if (!evInicia){ // SE NÃO ESTIVER INICIANDO O MODAL LIMPA OS CAMPOS PARA ALTERAÇÃO
        idItem.value              = '';
        precoTabelaEl.value       = '';
        precoCobradoEl.value      = '';
        quantidadeEl.value        = '';
        precoTotalEl.value        = '';
        percDescEl.value          = '';
        valDescEl.value           = '';
        custoOpEl.value           = '';
        custoAdmEl.value          = '';
        impostoEl.value           = '';
        txCartEl.value            = '';
        lBrutEl.value             = '';
        lLiquEl.value             = '';
        margemEl.value            = '';
    }


    if(!selectItem) return;

    selectItem.innerHTML = '<option value="">Selecione uma opção</option>';

    if(tipo === 'produto') {
        produtosArray.forEach(obj => {
            const option = document.createElement('option');
            option.value = obj.nome;
            option.textContent = obj.nome;
            
            if(selectedId && (obj.id == selectedId)) {
                option.selected = true;
            } else if(!selectedId && selectedItemName && (obj.nome.toLowerCase() === selectedItemName.toLowerCase())) {
                option.selected = true;
            }
            selectItem.appendChild(option);
        });
    }
    else if(tipo === 'servico') {
        servicosArray.forEach(obj => {
            const option = document.createElement('option');
            option.value = obj.servico;
            option.textContent = obj.servico;
            // Atributos extras personalizados
            option.setAttribute('data-item-id', obj.id);
            option.setAttribute('data-item-nome', obj.servico);
            option.setAttribute('data-item-tempo', obj.tempo);
            option.setAttribute('data-item-custo-adm', obj.valor_custo);
            option.setAttribute('data-item-valor-venda', obj.valor_venda);

            // Selecionado
            if(selectedId && (obj.id == selectedId)) {
                option.selected = true;
                selectItem.setAttribute('selected-data-item-id',obj.id );
                selectItem.setAttribute('selected-data-item-nome',obj.servico );
                selectItem.setAttribute('selected-data-tempo',obj.tempo );
                selectItem.setAttribute('selected-data-custo-adm',obj.valor_custo);
                selectItem.setAttribute('selected-data-valor-venda',obj.valor_venda);
                option.selected = true;
            } else if(!selectedId && selectedItemName && (obj.servico.toLowerCase() === selectedItemName.toLowerCase())) {
                selectItem.setAttribute('selected-data-item-id',obj.id );
                selectItem.setAttribute('selected-data-item-nome',obj.servico );
                selectItem.setAttribute('selected-data-tempo',obj.tempo );
                selectItem.setAttribute('selected-data-custo-adm',obj.valor_custo);
                selectItem.setAttribute('selected-data-valor-venda',obj.valor_venda);
                option.selected = true;
            }

            selectItem.appendChild(option);
        
        });
    }
    else if(tipo === 'cartao_presente') {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = '';
        option.selected = true;
        selectItem.appendChild(option);
    }
} // fim de atualizar itens



 
function atualizarTaxaItensOn() {

    const pagamentosBody = document.querySelector('#tabela-pagamentos tbody');
    const itensBody = document.querySelector('#tabela-itensVenda tbody');
    if (!pagamentosBody || !itensBody) return;

    let somaValores = 0;
    let somaTaxasReais = 0;
    // 1. Calcular taxa média dos pagamentos
    pagamentosBody.querySelectorAll('tr').forEach(tr => {
        const inputValor = tr.querySelector('.valor-pagamento');
        const taxaPerc = tr.querySelector('.forma-pagamento');
    
        if (!inputValor) return;

        const valor = parseFloat(DecimalIngles(inputValor.value)) || 0;
        const taxa = parseFloat(taxaPerc.getAttribute('selected-taxa-pagamento')) || 0;
        
        somaValores += valor;
        somaTaxasReais += valor * (taxa / 100);
    });

    totPagamentos=somaValores
    
    if (somaValores > 0 && totValItens > 0) {
        taxaMedia = (somaTaxasReais / totValItens) * 100;
    } else {
        taxaMedia = 0;  // evita divisão por zero
    }
    
    return taxaMedia;
}

  



    //Esta função soma todas as colunas dos itens obtendo e preenchendo os valores totais
function atualizaTotaisItens() {
    const tableItens = document.querySelectorAll("#tabela-itensVenda tbody tr");
    const ultimaLinha = document.querySelector("#tabela-itensVenda tbody tr:last-child");
    // Elementos da última linha (totais)
    const ttValItens= ultimaLinha.querySelector(".total-preco-total");
    const ttPercDesc = ultimaLinha.querySelector(".total-perc-desc");
    const ttValDesc = ultimaLinha.querySelector(".total-valor-desconto");
    const ttCuOp = ultimaLinha.querySelector(".total-custo-operacional");
    const ttCuAdm = ultimaLinha.querySelector(".total-custo-adm");
    const ttImp = ultimaLinha.querySelector(".total-imposto");
    const ttTxCart = ultimaLinha.querySelector(".total-taxa-cartao");
    const ttLB = ultimaLinha.querySelector(".total-lucro-bruto");
    const ttLL = ultimaLinha.querySelector(".total-lucro-liquido");
    const ttMa = ultimaLinha.querySelector(".total-margem");
    //Bloco dos Dados principais da proposta
    const blValorFinal = document.querySelector('#bl-valor-final');
    const spTotPagamentos = document.querySelector('#sp-total-pagamentos');
    const spSaldoFinal = document.querySelector('#sp-saldo-final');
    const chkSaldoFinal = document.getElementById('bl-chk-liberaSaldo');


    const spValorDesconto = document.querySelector('#sp-valor-desconto');
    const spValorOriginal = document.querySelector('#sp-valor-original');
    const txtValorDesconto = document.querySelector('#txt-valor-desconto');
    const txtValorOriginal = document.querySelector('#txt-valor-original');
    const txtValorFinal = document.querySelector('#txt-valor-final');


    //const txTotalPagamentos = document.querySelector('#txt-valor-final');
    const ValorDesconto = document.querySelector('#valor-desconto');
    const ValorOriginal = document.querySelector('#valor-original');
    const ValorFinal = document.querySelector('#valor-final');
    const textoValorSaldo = document.querySelector('#sp-valor-saldo');
    


    const blValorSaldo  = document.querySelector('#bloco-valor-saldo');
    
    const saldoFinInputHidden = document.getElementById('saldo-final');
    const saldoVendaInputHidden= document.getElementById('saldo-venda');
    
    const blLucroLiquido = document.querySelector('#valor-lucro-liquido');          //valor-lucro-liquido
    const blPercentualMargem = document.querySelector('#percentual-margem');   //percentual-margem
    
    const blCustoTotal = document.querySelector('#valor-custo-total');//valor-custo-total
    const inputCustoTotal = document.querySelector('#custo-total'); //é o input hidden que vai para o banco de dados com name=custo-total
    const blCustoAdm = document.querySelector('#valor-custo-adm');    //valor-custo-adm>     
    const blCustoOp = document.querySelector('#valor-custo-op');     //valor-custo-op> 
    const blCustoImp = document.querySelector('#valor-custo-imp');   //valor-custo-imp>           
    const blCustotaxa = document.querySelector('#valor-custo-taxa');  //valor-custo-taxa>      
    const blBlocoPrincipal = document.querySelector('#bloco-principal');       
    const labelValorFinal = document.querySelector('#label-valor-final');

    // Inicializando totais
    totValTabela=0;
    totValItens = 0;
    totPercDesc = 0;
    totValDesc = 0;
    totCuOp = 0;
    totCuAdm = 0;
    totImp = 0;
    totTxCart = 0;
    totLB = 0;
    totLL = 0;
    totMa = 0;
    totCusto = 0;
    saldoVenda = 0;

    tableItens.forEach((tr, index) => {
    // Ignora a última linha
        if (index === tableItens.length - 1) return;

        // Função auxiliar para extrair número do input
        const getValue = (cls) => {
            const input = tr.querySelector(`.${cls}`);
            return input ? parseFloat(DecimalIngles(input.value)) || 0 : 0;
        };
         // Soma cada campo
        totValTabela += getValue('preco-tabela') * getValue('quantidade');
        totValItens += getValue('preco-total');
        totValDesc += getValue('valor-desconto');
        totCuOp += getValue('custo-operacional');
        totCuAdm += getValue('custo-adm');
        totImp += getValue('imposto');
        totTxCart += getValue('taxa-cartao');
        totLB += getValue('lucro-bruto');
        totLL += getValue('lucro-liquido');
    });

    
    
    if (totValTabela>0){
    totPercDesc = (totValDesc/totValTabela*100);
    }

    totCusto = totCuAdm + totCuOp + totImp + totTxCart;
    
    if (totValItens==0){
        totMa = 0;
    }else{
        totMa = ((totValItens-totCusto) / totValItens)*100;
    }
    
    ttMa.style.borderColor = corMargem(totMa);
    ttPercDesc.style.borderColor = corMargem(totMa);//totItens
    ultimaLinha.style.borderTop = '2px solid ' + corMargem(totMa);
     //PREENCHENDO OS BLOCOS SUPERIORES
    blBlocoPrincipal.style.border = '1px solid ' +corMargem(totMa);
    labelValorFinal.style.color = corMargem(totMa);


    blValorFinal.textContent= 'R$ ' + (DecimalBr(totValItens));
    spTotPagamentos.textContent = 'R$ ' + (DecimalBr(totPagamentos));
   


    spValorDesconto.textContent = ("R$ -" + DecimalBr(totValDesc) + "  |  -" + DecimalBr(totPercDesc) + "%");
    spValorOriginal.textContent= ("R$ " + DecimalBr(totValTabela));
    txtValorDesconto.value = reaisPorExtenso(totValDesc);
    txtValorOriginal.value= reaisPorExtenso(totValTabela);
    txtValorFinal.value = reaisPorExtenso(totValItens);
    ValorDesconto.value = (totValDesc);
    ValorOriginal.value= (totValTabela);
    ValorFinal.value = (totValItens);

    let palavra= '';
    vSaldo = totPagamentos-totValItens;
    saldoVenda = parseFloat(vSaldo);


    
        if (vSaldo >0){ 
            textoValorSaldo.style.color = 'green'
            palavra = ', crédito.'
        } else{
            textoValorSaldo.style.color = 'red'
            palavra = ', débito.';
        }
 

    textoValorSaldo.textContent = ("R$ " + DecimalBr(vSaldo));



    saldoFinal = saldoCliente+saldoVenda;

    spSaldoFinal.textContent = 'R$ ' + DecimalBr(saldoFinal);

    if (saldoFinal !=0){
      chkSaldoFinal.style.display='block';
    }else{
      chkSaldoFinal.style.display='none';
    }

    //const blocoSaldoFinal = document.querySelector('#bloco-saldo-final');

  
        if(saldoFinal>0){
          spSaldoFinal.classList.add('num-positivo');
          spSaldoFinal.classList.remove('num-negativo');
        } else{
          spSaldoFinal.classList.add('num-negativo');
          spSaldoFinal.classList.remove('num-positivo');
        }

        const colBlPrinc = document.querySelector('#col-bloco-principal');

        if (totValDesc>0){
          colBlPrinc.style.display='block';
        }else{
          colBlPrinc.style.display='none';
        }
      

  //blSaldoFinal.style.display = 'block';
  //blSaldoFinal.style.display = 'none';


    saldoFinInputHidden.value = saldoFinal;
    saldoVendaInputHidden.value = saldoVenda;
    blLucroLiquido.textContent = 'R$ ' + (DecimalBr(totLL));
    blPercentualMargem.textContent = (DecimalBr(totMa)) +'%';
    blPercentualMargem.style.color =  corMargem(totMa);
    blCustoTotal.textContent =  'R$ ' + (DecimalBr(totCusto));
    inputCustoTotal.value = (DecimalBr(totCusto)); // input hidden que vai para o bando
    blCustoAdm.textContent =  'R$ ' + (DecimalBr(totCuAdm));
    blCustoOp.textContent =  'R$ ' + (DecimalBr(totCuOp));
    blCustoImp.textContent =  'R$ ' + (DecimalBr(totImp));
    blCustotaxa.textContent =  'R$ ' + (DecimalBr(totTxCart));
    
    // Atualiza os campos da última linha com os totais
    const setValue = (el, val) => {
        if (el) el.value = DecimalIngles(val);
    };

    
    ttValItens.value = DecimalBr(totValItens);
    ttPercDesc.value = DecimalBr(totPercDesc);
    ttValDesc.value = DecimalBr(totValDesc);
    ttCuOp.value = DecimalBr(totCuOp);
    ttCuAdm.value = DecimalBr(totCuAdm);
    ttImp.value =  DecimalBr(totImp);
    ttTxCart.value = DecimalBr(totTxCart);
    ttLB.value =  DecimalBr(totLB);
    ttLL.value = DecimalBr(totLL);
    ttMa.value = DecimalBr(totMa);

}




function atualizarTodasLinhasItens() {
    totUnItens = 0
    const tableItens = document.querySelectorAll("#tabela-itensVenda tbody tr");
    let linha = 0;
    campoEmEdicao = 'preco-tabela';

    tableItens.forEach((tr, index) => {
        if (index === tableItens.length - 1) {
            // Ignora a última linha
            return;
        }
        totUnItens++;
        linha++;
        recalcularLinha(tr);
    });

    campoEmEdicao = '';
}



function recalcularLinha(tr){

    const IdItem = tr.querySelector(".id-item")
    const precoTabelaEl  = tr.querySelector(".preco-tabela");
    const precoCobradoEl = tr.querySelector(".preco-cobrado");
    const quantidadeEl   = tr.querySelector(".quantidade");
    const precoTotalEl   = tr.querySelector(".preco-total");
    const percDescEl     = tr.querySelector(".perc-desc");
    const valDescEl     = tr.querySelector(".valor-desconto");
    const custoOpEl     = tr.querySelector(".custo-operacional");
    const custoAdmEl     = tr.querySelector(".custo-adm");
    const impostoEl     = tr.querySelector(".imposto");
    const txCartEl     = tr.querySelector(".taxa-cartao");
    const lBrutEl     = tr.querySelector(".lucro-bruto");
    const lLiquEl     = tr.querySelector(".lucro-liquido");
    const margemEl     = tr.querySelector(".margem");
    let PT    = parseFloat(DecimalIngles(precoTabelaEl.value))  || 0;
     console.log('preço tabele é: ', precoTabelaEl.value, '   e  para dicimal ingles fica: ', PT  );

   
    let PC    = parseFloat(DecimalIngles(precoCobradoEl.value)) || 0;
    let Q     = parseFloat(quantidadeEl.value)  || 1;
    let PTot  = parseFloat(DecimalIngles(precoTotalEl.value))   || 0;
    let D     = parseFloat(DecimalIngles(percDescEl.value))     || 0;
    let VD = parseFloat(DecimalIngles(valDescEl.value)) || 0;
    let CO = parseFloat(DecimalIngles(custoOpEl.value)) || 0;
    let CA = parseFloat(DecimalIngles(custoAdmEl.value)) || 0;
    let IM = parseFloat(DecimalIngles(impostoEl.value)) || 0;
    let TC = parseFloat(DecimalIngles(txCartEl.value)) || 0;
    let LB = parseFloat(DecimalIngles(lBrutEl.value)) || 0;
    let LL = parseFloat(DecimalIngles(lLiquEl .value)) || 0;
    let MA = parseFloat(DecimalIngles(margemEl.value)) || 0;

    let CUTT = 0; //custo total
    const CCO  = tr.querySelector('.item-select').getAttribute('selected-data-custo-adm') || '';
    const TSE = tr.querySelector('.item-select').getAttribute('selected-data-tempo');
    const IDIT = tr.querySelector('.item-select').getAttribute('selected-data-item-id');

    //caso especial quando libera a edição
    if (campoEmEdicao == 'preco-tabela') { 
        const NPT = tr.querySelector('.item-select').getAttribute('selected-data-valor-venda')//preço atualizado novo preço cobrado 
        PTot = PC * Q;
        D    = (NPT > 0) ? (100 - (PC / NPT * 100)) : 0;
        PT = NPT;
        if (precoTabelaEl.value){
          precoTabelaEl.value = DecimalBr(NPT);
          precoTotalEl.value = DecimalBr(PTot);
          percDescEl.value   = DecimalBr(D);
        }
    }

    if (campoEmEdicao == 'preco-cobrado') {
        PTot = PC * Q;
        D    = (PT > 0) ? (100 - (PC / PT * 100)) : 0;
        precoTotalEl.value = DecimalBr(PTot);
        percDescEl.value   = DecimalBr(D);
    }

    if (campoEmEdicao == 'quantidade') {
        PTot = PC * Q;
        D    = (PT > 0) ? (100 - (PC / PT * 100)) : 0;
        precoTotalEl.value = DecimalBr(PTot);
        percDescEl.value   = DecimalBr(D);
    }

    if (campoEmEdicao == 'perc-desc') {
        PC   = PT * ((100 - D) / 100);
        PTot = PC * Q;
        precoCobradoEl.value = DecimalBr(PC);
        precoTotalEl.value   = DecimalBr(PTot);
    }

    if (campoEmEdicao == 'preco-total') {
        PC   = (Q > 0) ? (PTot / Q) : 0;
        D    = (PT > 0) ? (100 - (PC / PT * 100)) : 0;
        precoCobradoEl.value = DecimalBr(PC);
        percDescEl.value     = DecimalBr(D);
    }


    VD = (PT*Q)-PTot;
    CO = CCO * Q;
    CA = TSE * Q / 60 * custoHora; //Ver Formula
    IM = impostoVenda * PTot / 100; 
    TC = taxaMedia * PTot /100; //já foi alculado
    CUTT = (CO+CA+IM+TC);
    LB = PTot - CO;
    LL = PTot - CUTT;

    if (PTot==0){
        MA=0;
    } else{
        MA = ((PTot-CUTT) / PTot * 100); // calcula a margem da linha
    }
    
    IdItem.value        = IDIT;
    valDescEl.value     = DecimalBr(VD);
    custoOpEl.value     = DecimalBr(CO);
    custoAdmEl.value    = DecimalBr(CA);
    impostoEl.value     = DecimalBr(IM);
    txCartEl.value      = DecimalBr(TC);
    lBrutEl.value       = DecimalBr(LB);
    lLiquEl.value       = DecimalBr(LL);
    margemEl.value      = DecimalBr(MA);
    margemEl.style.borderColor = corMargem(MA);
    percDescEl.style.borderColor = corMargem(MA);
}
          
      
 
    // Função para adicionar nova linha nos ITENS
    
function adicionarLinha(dados = {}) {
    const tbody = document.querySelector('#itens-body');
    if(!tbody) return;

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <button type="button" class="btn btn-danger remover-item centBt">-</button>
        </td>
        <td style="display:none;">
            <input name="item_id[]" value="">
        </td>
        <td>
            <select name="tipo_item[]" class="tipo-item form-control input-liberado">
            <option value="servico" selected>Serviço</option>
            <option value="produto" >Produto</option>
            <option value="cartao_presente" >Cartão Presente</option>
            </select>
        </td>
        <td>
            <select
            name="item[]"
            class="item-select form-control input-liberado"  
            style="width:200px;">
            </select>
        </td>
        <td style="display:none;">
            <input name="id_item[]" readOnly class="id-item" value="">
        </td>
        <td class="col-avancada hidden-col">
            <input type="text" name="preco_tabela[]" class="form-control preco-tabela bloclItem" value="">
        </td>
        <td>
            <input type="text" name="preco_cobrado[]" class="form-control preco-cobrado numero-virgula-calc input-liberado" value="">
        </td>
        <td>
            <input type="number" name="quantidade[]" min="1" step="1" class="form-control quantidade num input-liberado" value="">
        </td>
        <td>
            <input type="text" name="preco_total[]" class="form-control preco-total numero-virgula-calc input-liberado" value="">
        </td>
        <td>
            <input type="text" name="perc_desc[]" class="form-control perc-desc numero-virgula-calc porcento input-liberado" value="">
        </td>
    
        <td >
            <input type="text" readOnly name="valor_desconto[]" class="form-control valor-desconto blockItem" value="">
        </td>

        <!-- Todas as colunas avançadas -->
        <td class="col-avancada hidden-col">
            <input type="text" readOnly name="custo_operacional[]" class="form-control custo-operacional blockItem" value="">
        </td>
        <td class="col-avancada hidden-col">
            <input type="text" readOnly name="custo_adm[]" class="form-control custo-adm blockItem" value="">
        </td>
        <td class="col-avancada hidden-col">
            <input type="text" readOnly name="imposto[]" class="form-control imposto blockItem" value="">
        </td>
        <td class="col-avancada hidden-col">
            <input type="text" readOnly name="taxa_cartao[]" class="form-control taxa-cartao blockItem" value="">
        </td>
        <td class="col-avancada hidden-col">
            <input type="text" readOnly name="lucro_bruto[]" class="form-control lucro-bruto blockItem" value="">
        </td>
        <td class="col-avancada hidden-col">
            <input type="text" readOnly name="lucro_liquido[]" class="form-control lucro-liquido blockItem" value="">
        </td>
        <td class="col-avancada hidden-col">
            <input type="text" readOnly name="margem[]" class="form-control margem blockItem" value="">
        </td>
    `;

    tbody.prepend(tr);

    // Já existe no seu código, mas deixo aqui como exemplo
    const tipoItem   = tr.querySelector('.tipo-item');
        // Atualiza a combo do item (produto, serviço, etc.)
    atualizarItens(tr, tipoItem.value, '', '')//, itemSelectId, itemSelectNm);

    // ** Se as colunas avançadas já estiverem visíveis, remove a classe que as oculta **
    if (avancadoVisivel) {
        const colunasAvancadas = tr.querySelectorAll('.col-avancada');
        colunasAvancadas.forEach(col => col.classList.remove('hidden-col'));
    }


} // fim da função adicionar linhas dos itens

   
    // Inicializa as linhas existentes (popular <select>)
    document.querySelectorAll('.tipo-item').forEach(select => {
      
      const tr = select.closest('tr');
      if(!tr) return;
      const selIdAttr   = tr.querySelector('.item-select').getAttribute('selected-data-item-id') || '';
      const selNameAttr = tr.querySelector('.item-select').getAttribute('selected-data-item-nome') || '';
      
      atualizarItens(
        tr,
        select.value,
        selIdAttr,
        selNameAttr
      );
    });
    evInicia=false;





function atualizarTipoPagamento(tr, selectedValue = '') {
    const selectTipo = tr.querySelector('.tipo-pagamento');
    if(!selectTipo) return;

    // Limpa
    selectTipo.innerHTML = '<option value="">Forma de Pagamento</option>';

    // Preenche a combo de tipos
    tiposPagamentosArray.forEach(tp => {
        const option = document.createElement('option');
        option.value = tp.nome;            // Ex: "Crédito"
        option.textContent = tp.nome;      // Texto exibido
        option.setAttribute('tipo-pg-id', tp.id); // Ex: "1"
       // Se o 'selectedValue' for igual a 'tp.nome', marcamos como selecionado
        if (selectedValue && selectedValue === tp.nome) {
            option.selected = true;
        }
       selectTipo.appendChild(option);
    });
}

    // -------------------------------------------------------
    // (B) Função para preencher o select de Pagamento (formas)
    // -------------------------------------------------------

function atualizarPagamento(tr) {
    const selectTipo = tr.querySelector('.tipo-pagamento');
    const selectForma = tr.querySelector('.forma-pagamento');
    // Armazena o valor selecionado atualmente (se houver)
    let valorSelecionado = selectForma.value;

    if (!selectTipo || !selectForma) return;
    // Obtém a opção selecionada do tipo de pagamento
    const selectedTipoOption = selectTipo.selectedOptions[0];

    if (!selectedTipoOption) {
    selectForma.innerHTML = '<option value="">Selecione o Pagamento</option>';
    return;
    }

    const selectTipoId = parseInt(selectedTipoOption.getAttribute('tipo-pg-id')) || 0;
    // Reconstroi as opções do select de forma
    selectForma.innerHTML = '<option value="">Selecione Forma / Conta</option>';
    // Tenta filtrar as formas de pagamento a partir do array
    const formasFiltradas = formasPagamentosArray.filter(f => f.tipo_id == selectTipoId);

    if (formasFiltradas.length > 0) {
        formasFiltradas.forEach(f => {
            if (!f.id_conta_pagamento || f.id_conta_pagamento == 0) {
            // Caso não haja conta vinculada, percorre as contas correntes
                contasCorrentesArray.forEach(cc => {
                const option = document.createElement('option');
                option.value = `${f.nome} - ${cc.nome}`;
                option.textContent = `${f.nome} - ${cc.nome}`;
                option.setAttribute('id_forma-pagamento', 0);
                option.setAttribute('nome_forma-pagamento', 0);
                option.setAttribute('taxa-pagamento', 0);
                option.setAttribute('dias-pagamento', 0);
                option.setAttribute('conta-pagamento_id', cc.id);
                selectForma.appendChild(option);
            });
            } else {
                // Caso haja forma com conta vinculada, cria uma única opção
                const option = document.createElement('option');
                option.value = f.nome;
                option.textContent = f.nome;
                option.setAttribute('id_forma-pagamento', f.id || 0);
                option.setAttribute('nome_forma-pagamento', f.nome || 0);
                option.setAttribute('taxa-pagamento', f.taxa || 0);
                option.setAttribute('dias-pagamento', f.dias_pagamento || 0);
                option.setAttribute('conta-pagamento_id', f.id_conta_pagamento);
                selectForma.appendChild(option);
            }
        });

        // Tenta selecionar a opção que o usuário já tinha escolhido
        let encontrou = false;
        Array.from(selectForma.options).forEach(opt => {
            if (opt.value === valorSelecionado && valorSelecionado !== "") {
                opt.selected = true;
                selectForma.setAttribute('selected-id_forma-pagamento', opt.getAttribute('id_forma-pagamento'));
                selectForma.setAttribute('selected-nome_forma-pagamento', opt.getAttribute('nome_forma-pagamento'));
                selectForma.setAttribute('selected-taxa-pagamento', opt.getAttribute('taxa-pagamento'));
                selectForma.setAttribute('selected-dias-pagamento', opt.getAttribute('dias-pagamento'));
                selectForma.setAttribute('selected-conta-pagamento_id', opt.getAttribute('conta-pagamento_id'));
                encontrou = true;
            }
        });
        // Se o valor anterior não foi encontrado e há pelo menos uma opção válida (além do placeholder)
        if (!encontrou && selectForma.options.length > 1) {
            let defaultOpt = selectForma.options[1];
            defaultOpt.selected = true;
            selectForma.setAttribute('selected-id_forma-pagamento', defaultOpt.getAttribute('id_forma-pagamento'));
            selectForma.setAttribute('selected-nome_forma-pagamento', defaultOpt.getAttribute('nome_forma-pagamento'));
            selectForma.setAttribute('selected-taxa-pagamento', defaultOpt.getAttribute('taxa-pagamento'));
            selectForma.setAttribute('selected-dias-pagamento', defaultOpt.getAttribute('dias-pagamento'));
            selectForma.setAttribute('selected-conta-pagamento_id', defaultOpt.getAttribute('conta-pagamento_id'));
        }
    } else {
        // Caso não haja nenhuma forma de pagamento associada, usa o array de contas correntes
        console.warn('Nenhuma forma de pagamento encontrada para esse tipo. Exibindo contas...');
        contasCorrentesArray.forEach(cc => {
            const option = document.createElement('option');
            option.value = cc.nome;
            option.textContent = `Conta: ${cc.nome}`;
            option.setAttribute('id_forma-pagamento', cc.id);
            option.setAttribute('taxa-pagamento', 0);
            option.setAttribute('dias-pagamento', 0);
            option.setAttribute('conta-pagamento_id', cc.id);
            selectForma.appendChild(option);
        });
        // Tenta manter o valor selecionado se existir
        let encontrou = false;
        Array.from(selectForma.options).forEach(opt => {
            if (opt.value === valorSelecionado && valorSelecionado !== "") {
                opt.selected = true;
                selectForma.setAttribute('selected-id_forma-pagamento', opt.getAttribute('id_forma-pagamento'));
                selectForma.setAttribute('selected-nome_forma-pagamento', opt.value);
                selectForma.setAttribute('selected-taxa-pagamento', opt.getAttribute('taxa-pagamento'));
                selectForma.setAttribute('selected-dias-pagamento', opt.getAttribute('dias-pagamento'));
                selectForma.setAttribute('selected-conta-pagamento_id', opt.getAttribute('conta-pagamento_id'));
                encontrou = true;
            }
        });
        if (!encontrou && selectForma.options.length > 1) {
            let defaultOpt = selectForma.options[1];
            defaultOpt.selected = true;
            selectForma.setAttribute('selected-id_forma-pagamento', defaultOpt.getAttribute('id_forma-pagamento'));
            selectForma.setAttribute('selected-nome_forma-pagamento', defaultOpt.value);
            selectForma.setAttribute('selected-taxa-pagamento', defaultOpt.getAttribute('taxa-pagamento'));
            selectForma.setAttribute('selected-dias-pagamento', defaultOpt.getAttribute('dias-pagamento'));
            selectForma.setAttribute('selected-conta-pagamento_id', defaultOpt.getAttribute('conta-pagamento_id'));
        }
    }
}






function atualizarTudoPagamentos() {
    const tablePagamentos = document.querySelectorAll("#tabela-pagamentos tbody tr");
    totUnPagamentos = 0;    
    tablePagamentos.forEach(tr => {
       atualizarPagamento(tr);
            
    });

    campoEmEdicao = '';
}
    // -------------------------------------------------------
    // (D) Função para adicionar nova linha na tabela de pagamentos
    // -------------------------------------------------------
    
function adicionarPagamento(dados = {}) {
      // Se já tiver um <tbody> com id="pagamentos-body"
     const pagBody = document.querySelector('#pagamentos-body');
      if(!pagBody) return;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <button type="button" class="btn btn-danger remover-pagamento centBt">-</button>
        </td>
        <td style="display:none">
          <input name="pagamento_id[]" value="${dados.id || ''}">
        </td>
        
        <td>
          <select name="tipo_pagamento[]" class="tipo-pagamento form-select input-liberado">
          </select>
        </td>
        <td>
          <select name="pagamento[]" class="forma-pagamento form-select input-liberado">
          </select>
        </td>
        <td style="display:none">
          <input type="text" name="id_pagamento[]" class="id-pagamento form-control">
          
        </td>
        <td>
          <input type="text" class="form-control numero-virgula-calc valor-pagamento input-liberado" name="valor_pagamento[]" value="${dados.valor || ''}">
        </td>
        <td style="display:none">
          <input readonly type="text" class=" form-control qt-parcelas blockItem" name="qt_parcelas[]" value="">
        </td>
        <td>
          <input  type="text" class="form-control parcela-pagamento blockItem" readonly value="">
        </td>
        <td class="col-avancada hidden-col">
          <input readonly type="text" class="numero-virgula form-control valor-taxa blockItem" name="valor_taxa[]" value="">

        </td>
        <td  class="col-avancada hidden-col">
          <input readonly type="text" class="numero-virgula form-control perc-taxa blockItem" name="perc_taxa[]" value="">

        </td>
        <td style="display:none">
          <input  readonly type="text" class="form-control dias-pagamento blockItem" name="dias_pagamento[]" value="">
        </td>
        
        <td style="display:none">
          <input  readonly type="text"  class="form-control id-conta-corrente blockItem" name="id_conta_corrente[]" value="">
        </td>
        <td style="display:none">
          <input readonly type="text" class="form-control pago blockItem" name="pago[]" value="">
        </td>

      `;

      // Adiciona a nova linha no início
      pagBody.prepend(tr);


      // Preenche o select tipo_pagamento
      atualizarTipoPagamento(tr, dados.tipo_nome || '');

      // Se tiver valor default para o tipo, já chama atualizarPagamento
      if(dados.tipo_nome) {
        const comboTipo = tr.querySelector('.tipo-pagamento');
        if(comboTipo) {
          // Seleciona a option
          [...comboTipo.options].forEach(op => {
            if(op.value === dados.tipo_nome) {
              op.selected = true;
            }
          });
          // E preenche as formas
          atualizarPagamento(tr);
        }
      }

      // Evento de remover


}

    // Botão + ITEN
document.getElementById('adicionar-item-modVenda').addEventListener('click', function(){
      
      if (!verificaItens()) {
         return;
      }

      adicionarLinha();
});




      // =============== EVENTOS ===============
      
      //Evento 1 
document.querySelector('#itens-body').addEventListener('focus', function(e) {
  
        
        if (e.target.matches('input')) {
          const el = e.target; 
         // validarInput(el);
          e.target.classList.add('input-focado');
  
          // Captura a classe do campo atual para evitar loop
          if (e.target.classList.contains('preco-cobrado')) campoEmEdicao = 'preco-cobrado';
          else if (e.target.classList.contains('quantidade')) campoEmEdicao = 'quantidade';
          else if (e.target.classList.contains('preco-total')) campoEmEdicao = 'preco-total';
          else if (e.target.classList.contains('perc-desc')) campoEmEdicao = 'perc-desc';
          
        }
}, true); // fim do evento 1
      

//Evento 2 
document.querySelector('#itens-body').addEventListener('blur', function(e) {
    if (e.target.matches('input')) {
          e.target.classList.remove('input-focado');
          campoEmEdicao = null;
        
        }
}, true); // fim do evento 2
  
  
      // Evento 3       (A) keyup => recalculo imediato SEM reescrever o mesmo campo



document.querySelector('#itens-body').addEventListener('input', function(e){
   const tr = e.target.closest('tr');
        
        if (!tr) return;
        
        if ([
           'preco-cobrado',
           'quantidade',
           'preco-total',
           'perc-desc'
         ].some(cls => e.target.classList.contains(cls))) {
          const el = e.target;
          //validarInput(el);
          
          recalcularLinha(tr);
          atualizaTotaisItens();
          atualizarTaxaItensOn();
         }
  
}); //fim doevento 3
  
      
          // (C) change => se mudar o tipo ou item
document.querySelector('#itens-body').addEventListener('change', function(e){
  const tr = e.target.closest('tr');
  if(!tr) return;

  if(e.target.classList.contains('tipo-item')){
    const itemSel = tr.querySelector('.item-select');
    //esvazia os atributos
    itemSel.setAttribute('selected-data-item-id', '' );
    itemSel.setAttribute('selected-data-item-nome', '');
    itemSel.setAttribute('selected-data-tempo', '' );
    itemSel.setAttribute('selected-data-custo-adm', '');
    itemSel.setAttribute('selected-data-valor-venda', '');

    atualizarItens(tr, e.target.value, '', '');
    atualizaTotaisItens();
  }else if(e.target.classList.contains('item-select')) {
    const select = e.target;
    const selectedOption = select.selectedOptions[0];

    // Pega os dados dos atributos da <option> selecionada
    const itemId      = selectedOption.getAttribute('data-item-id') || '';
    const itemNome    = selectedOption.getAttribute('data-item-nome') || '';
    const tempo       = selectedOption.getAttribute('data-item-tempo') || '';
    const custoAdm    = selectedOption.getAttribute('data-item-custo-adm') || '';
    const valorVenda  = selectedOption.getAttribute('data-item-valor-venda') || '';

    // Atribui esses valores ao próprio <select> (item-select)
    select.setAttribute('selected-data-item-id', itemId);
    select.setAttribute('selected-data-item-nome', itemNome);
    select.setAttribute('selected-data-tempo', tempo);
    select.setAttribute('selected-data-custo-adm', custoAdm);
    select.setAttribute('selected-data-valor-venda', valorVenda);

    // Atualiza os campos relacionados
    
    atualizarPreco(tr);
    atualizaTotaisItens();
    atualizarTaxaItensOn();
    
  }

});
  
  
  
      // (D) Remover item
document.querySelector('#itens-body').addEventListener('click', function(e){
    if(e.target.classList.contains('remover-item')){
        e.target.closest('tr').remove();
          atualizaTotaisItens()
    }
        
});
  
  
   
    // 1) Localiza a tabela
    


    // -------------------------------------------------------
    // (A) Função para preencher o select de Tipo de Pagamento
    // -------------------------------------------------------



    // -------------------------------------------------------
    // (C) Eventos na tabela
    // -------------------------------------------------------
document.querySelector('#pagamentos-body').addEventListener('change', function(e){
      
      const tr = e.target.closest('tr');
      if(!tr) return;

      // Se mudar o tipo_pagamento, atualizamos a forma de pagamento

        const fPagtoEl = tr.querySelector('.forma-pagamento');
        const idFPagto = tr.querySelector('.id-pagamento');

        const valorEl = tr.querySelector('.valor-pagamento');
        const parcEl = tr.querySelector('.parcela-pagamento');
        
        const qtParcEl = tr.querySelector('.qt-parcelas');
        const valTaxaEl = tr.querySelector('.valor-taxa');
        const percTaxaEl = tr.querySelector('.perc-taxa');
        const diasPagEl = tr.querySelector('.dias-pagamento');
        const idCCorrEl = tr.querySelector('.id-conta-corrente');


      if(e.target.classList.contains('tipo-pagamento')) {

         //const taxa = tr.querySelector('.forma-pagamento').getAttribute('selected-taxa-pagamento');
        
        fPagtoEl.removeAttribute('selected-nome_forma-pagamento');
        fPagtoEl.removeAttribute('selected-taxa-pagamento');
        fPagtoEl.removeAttribute('selected-dias-pagamento');
        fPagtoEl.removeAttribute('selected-conta-pagamento_id');
        fPagtoEl.removeAttribute('selected-id_forma-pagamento');
        
        fPagtoEl.value = '';
        idFPagto.value = '';
        valorEl.value = '';
        qtParcEl.value = '';
        parcEl.value = '';
        valTaxaEl.value = '';
        percTaxaEl.value = '';
        diasPagEl.value = '';
        idCCorrEl.value = '';
       
       // parcEl.setAttribute('hidden',0);
       
        atualizarPagamento(tr); //
        
        atualizarTaxaItensOn(); // atualiza a taxa média
        
        atualizarTodasLinhasItens(); //recalcula todas as linhas
        atualizaTotaisItens() // recalcula os totais da proposta/venda
      }
       // Se mudar se mudar a forma de pagamento
      else if(e.target.classList.contains('forma-pagamento')) {
      //tiago
        atualizarPagamento(tr); 

             if (!tr) return;

        const taxa = fPagtoEl.getAttribute('selected-taxa-pagamento');
        
        const valor = DecimalIngles(valorEl.value);

       
        
        if (percTaxaEl.value) {
         percTaxaEl.value = DecimalBr(taxa);
        } else{
          percTaxaEl.value = 0;
        }
        
        diasPagEl.value = fPagtoEl.getAttribute('selected-dias-pagamento');
        idCCorrEl.value = fPagtoEl.getAttribute('selected-conta-pagamento_id');
        idFPagto.value = fPagtoEl.getAttribute('selected-id_forma-pagamento');

        
        valTaxaEl.value = DecimalBr(valor * taxa / 100);
        
        
        
   

        if (!valorEl) return;
       
        
        let texto = fPagtoEl.selectedOptions[0]?.textContent || '';

      
        let parcelas = 0;
        
        // Regex para capturar "3x", "6x", etc.
          const match = texto.match(/(\d+)\s*[xX]/);
          
          if (match) {
            parcelas = parseInt(match[1]);
          }

          console.log('parcelas: ' + parcelas);
        
        if (valor> 0 && parcelas > 0) {



          const valorParcela = valor / parcelas;

              if (valorParcela > 0){
                

              parcEl.value = DecimalBr(valorParcela);
              qtParcEl.value = parcelas;
              parcEl.removeAttribute('hidden');
              } else{
                parcEl.value = '';
                parcEl.setAttribute('hidden', true);
              }
        } else {
          parcEl.value = '';
          parcEl.setAttribute('hidden', true);
        }


        
        atualizarTaxaItensOn(); // atualiza a taxa média
        atualizarTodasLinhasItens();
        //atualizarTaxaItensOn(); // atualiza a taxa média
         //recalcula todas as linhas
        atualizaTotaisItens() // recalcula os totais da proposta/venda


      }
});


    // -------------------------------------------------------
    // (C) Eventos nos inputs de valor da tabela pagamentos
    // -------------------------------------------------------

document.querySelector('#pagamentos-body').addEventListener('input', function(e){

      const tr = e.target.closest('tr');
      if(!tr) return;

      // Se mudar o tipo_pagamento, atualizamos a forma de pagamento
      if(e.target.classList.contains('valor-pagamento')) {
       
      
        const input = e.target;

        const tr = input.closest('tr');
        const select = tr.querySelector('.forma-pagamento');
        
        if (!tr) return;

        const idFPagto = tr.querySelector('.id-pagamento');
        const valorEl = tr.querySelector('.valor-pagamento');
        const parcelaEl = tr.querySelector('.parcela-pagamento');
        const qtParcEl = tr.querySelector('.qt-parcelas');
        const valTaxaEl = tr.querySelector('.valor-taxa');
        const percTaxaEl = tr.querySelector('.perc-taxa');
        const diasPagEl = tr.querySelector('.dias-pagamento');
        const idCCorrente = tr.querySelector('.id-conta-corrente');

        let taxa = tr.querySelector('.forma-pagamento').getAttribute('selected-taxa-pagamento');
        const valor = DecimalIngles(valorEl.value);

        
        if (!taxa){
          taxa=0;
        }

        valTaxaEl.value = DecimalBr(valor * taxa/100);

               
        percTaxaEl.value = DecimalBr(taxa);
        

        diasPagEl.value = select.getAttribute('selected-dias-pagamento');
        idCCorrente.value = select.getAttribute('selected-conta-pagamento_id');
        
        idFPagto.value = select.getAttribute('selected-id_forma-pagamento');
        
        if (!valorEl) return;

        let texto = select.getAttribute('selected-nome_forma-pagamento') || '';
        let parcelas = 0;
        
        // Regex para capturar "3x", "6x", etc.
        const match = texto.match(/(\d+)\s*[xX]/);
        

        if (match) {
          parcelas = parseInt(match[1]);
        }

        
        if (parcelas > 1) {
          const valorParcela = valor / parcelas;
              if (valorParcela > 0){ 
              
              parcelaEl.value = DecimalBr(valorParcela);
              qtParcEl.value = parcelas;
              parcelaEl.removeAttribute('hidden');
              } else{
                parcelaEl.value = '';
                qtParcEl.value = 0;
                parcelaEl.setAttribute('hidden', true);
              }
              
        } else {
          parcelaEl.value = '';
          qtParcEl.value = '';
          parcelaEl.setAttribute('hidden', true);
        }
          

        


        const tdPagamentos = document.querySelectorAll('.valor-pagamento');
          let pagamentos = 0;

          tdPagamentos.forEach((elemento) => {
            pagamentos += DecimalIngles(elemento.value);
          });

          totPagamentos = pagamentos;
          document.getElementById('total-pagamentos').value = totPagamentos;
         
        atualizarTaxaItensOn(); // atualiza a taxa média
        
        atualizarTodasLinhasItens(); //recalcula todas as linhas
        atualizaTotaisItens() // recalcula os totais da proposta/venda


      }
});


document.querySelector('#pagamentos-body').addEventListener('click', function(e){
    if(e.target.classList.contains('remover-pagamento')){
        e.target.closest('tr').remove();
        teste= 'teste removido o botão';
        atualizarTaxaItensOn();
        atualizarTodasLinhasItens();
        atualizaTotaisItens();
    }
        
});

   


document.querySelector('#adicionar-pagamento').addEventListener('click', function(){
    
    if (!verificaPagamento()) {
      return;
    }
    adicionarPagamento();
});
    

//=========         FIM DAS FUNÇÕES DOS PAGAMENTOS       ========================
  
  
var btn = document.querySelector('#btnHabilitaEdicao');

if (btn && !btn.dataset.listenerAdicionado) {
    btn.addEventListener('click', async function() {
        let confirma = confirm("Deseja   realmente habilitar a edição? Os valores serão recalculados...");                                                                      
        if (confirma) {                                
          const temUso = await verificaUsoServicos();
          if (temUso) return; 
         
          atualizarTaxaItensOn();
          atualizarTodasLinhasItens();
          atualizaTotaisItens();
          atualizarTudoPagamentos(); 
          habilitarEdicao();
          }
      });
    btn.dataset.listenerAdicionado = "true"; // marca que já adicionou
}




async function verificaUsoServicos() {
  
 const servicoIds = Array.from(document.querySelectorAll('input[name="item_id[]"]')).map(input => input.value);
  if (!servicoIds.length) return Promise.resolve(false);

  return fetch('venda/verificar_uso_servicos.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ ids: servicoIds })
  })

  .then(response => response.json())
  .then(data => {
    if (data.sucesso && data.tabelaHtml && data.tabelaHtml.trim()) {
      Swal.fire({
        icon: 'warning',
        title: 'Serviços com uso registrado',
        html: data.tabelaHtml,
        width: 600,
        confirmButtonText: 'Entendi',
        customClass: {
          popup: 'swal2-servicos-uso'
        }
      });
      return true;
    }
    return false;
  })
  .catch(error => {
    console.error('Erro ao consultar uso dos serviços:', error);
    return false;
  });
}





   

    document.querySelector("#nome-cliente").addEventListener("input", () => {

      // limpa todos os campos ao digitar
      document.querySelector("#id-cliente").value = "";
      document.querySelector("#sexo-cliente").value = "";
      document.querySelector("#cpf-cliente").value = "";
      document.querySelector("#celular-cliente").value = "";
      document.querySelector("#email-cliente").value = "";
      document.getElementById('proposta-vendas').style.display = 'none';
      document.getElementById('ico-inputCliente').classList.remove('bi-eye');
      document.getElementById('ico-inputCliente').classList.add('bi-person-plus');
      document.getElementById('sp-saldo-cliente').textContent="";
      document.querySelector('#img-foto-cliente-modVendas').setAttribute('src', '../img/sem-foto.svg');
      document.querySelector('#col-img-foto-cliente').style.display='none';

      const termo = document.querySelector("#nome-cliente").value.toLowerCase();
      document.querySelector("#lista-clientes").innerHTML = "";
      selecionadoIndexV = -1;

      if (termo.length === 0) {
        document.querySelector("#lista-clientes").style.display = "none";
        document.querySelector("#id-cliente").value = ""; // Garante que idCliente esteja vazio se nenhum termo digitado
        return;
      }

      function removerAcentos(str) {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
      }

      resultadosFiltrados = clientes.filter(cliente =>
        removerAcentos(cliente.nome.toLowerCase()).includes(removerAcentos(termo))
      );

      resultadosFiltrados.forEach((cliente, index) => {
        const li = document.createElement("li");
        li.textContent = cliente.nome;
        li.addEventListener("click", () => carregarCliente(cliente));
        document.querySelector("#lista-clientes").appendChild(li);
      });

      document.querySelector("#lista-clientes").style.display = resultadosFiltrados.length ? "block" : "none";

      if (resultadosFiltrados.length === 0) {
        document.querySelector("#id-cliente").value = ""; // garante que fique vazio se não houver resultados
      }
    });


    document.querySelector("#nome-cliente").addEventListener("keydown", (e) => {
      const itens = document.querySelector("#lista-clientes").querySelectorAll("li");

      if (e.key === "ArrowDown") {
        if (selecionadoIndexV < itens.length - 1) {
          selecionadoIndexV++;
          atualizarSelecao(itens);
        }
        e.preventDefault();
      }

      if (e.key === "ArrowUp") {
        if (selecionadoIndexV > 0) {
          selecionadoIndexV--;
          atualizarSelecao(itens);
        }
        e.preventDefault();
      }

      if (e.key === "Enter") {
        if (selecionadoIndexV >= 0) {
          carregarCliente(resultadosFiltrados[selecionadoIndexV]);
        } else if (resultadosFiltrados.length === 1) {
          carregarCliente(resultadosFiltrados[0]);
        }
        document.querySelector("#lista-clientes").style.display = "none";
        e.preventDefault();
      }
    });





    function atualizarSelecao(itens) {
      itens.forEach((item, index) => {
        const isSelected = index === selecionadoIndexV;
        item.classList.toggle("selecionado", isSelected);
        if (isSelected) {
          item.scrollIntoView({ block: "nearest" });
        }
      });
    }

    function carregarCliente(cliente) {
      document.querySelector("#nome-cliente").value = cliente.nome;
      document.querySelector("#id-cliente").value = cliente.id;

      if (cliente.id != "") {
        document.getElementById('proposta-vendas').style.display = 'block';
        document.getElementById('ico-inputCliente').classList.remove('bi-person-plus');
        document.getElementById('ico-inputCliente').classList.add('bi-eye');
      } else {
        document.getElementById('proposta-vendas').style.display = 'none';
        document.getElementById('ico-inputCliente').classList.remove('bi-eye');
        document.getElementById('ico-inputCliente').classList.add('bi-person-plus');
      }

      document.querySelector("#id-cliente").value = cliente.id;
      document.querySelector("#sexo-cliente").value = cliente.sexo;
      document.querySelector("#cpf-cliente").value = cliente.cpf;
      document.querySelector("#celular-cliente").value = cliente.celular;
      document.querySelector("#email-cliente").value = cliente.email;
      document.querySelector("#lista-clientes").innerHTML = "";
      document.querySelector("#lista-clientes").style.display = "none";

      if(cliente.foto){
        document.querySelector('#img-foto-cliente-modVendas').setAttribute('src', '../'+ pastaFiles +'/img/clientes/'+cliente.foto);
        document.querySelector('#col-img-foto-cliente').style.display='block';
      }else{
        document.querySelector('#img-foto-cliente-modVendas').setAttribute('src', '../img/sem-foto.svg');
        document.querySelector('#col-img-foto-cliente').style.display='none';

      }
      
      saldoCliente = parseFloat(cliente.saldo) || 0;
      
      //const blocoCliente = document.querySelector('#bloco-saldo-cliente');
      //const blocoSaldoFinal = document.querySelector('#bloco-saldo-final');
      const spSaldoCliente = document.querySelector('#sp-saldo-cliente');
      const spSaldoFinal = document.querySelector('#sp-saldo-final');
      
      spSaldoCliente.textContent = 'R$ ' + DecimalBr(cliente.saldo);
      

      console.log('O saldo do Cliente é:', saldoCliente);
      


        if(saldoCliente>0){
          spSaldoCliente.classList.remove('num-negativo');
          spSaldoCliente.classList.add('num-positivo');
        }else{
          spSaldoCliente.classList.remove('num-positivo');
          spSaldoCliente.classList.add('num-negativo');
        }
 


        saldoFinal = saldoCliente+saldoVenda;

      spSaldoFinal.textContent = 'R$ ' + DecimalBr(saldoFinal);
      
        if(saldoFinal!=0){}
        if(saldoFinal>0){
          spSaldoFinal.classList.add('num-positivo');
          spSaldoFinal.classList.remove('num-negativo');
        } else{
          spSaldoFinal.classList.add('num-negativo');
          spSaldoFinal.classList.remove('num-positivo');
        }

      
}

    document.querySelector("#id-cliente").addEventListener("change", () => {
      console.log('ouvido inputcliente');
    });



  
   // fecha a função grande initModalVenda


// para os botões de habilitar e desabilitar



function habilitarEdicao(){
  // 1) Remove o 'disabled' ou 'readonly' dos inputs que estavam travados
  
    let itensHabilitar ='#tabela-itensVenda th, #tabela-itensVenda td, #tabela-pagamentos th, #tabela-pagamentos td';
    const inputDataVenda = document.getElementById('data-venda');

    if (tipo_venda=='venda' && data_venda){
        console.log('habilidtando input data venda');
        inputDataVenda.setAttribute('type', 'date');
        inputDataVenda.classList.remove('blockItem');
        inputDataVenda.value =data_venda;
        
    }

    const campos = document.querySelectorAll(itensHabilitar);

    campos.forEach(el => {
        el.removeAttribute('hidden');
    });

    const bloqueados = document.querySelectorAll('.input-bloqueado');

    bloqueados.forEach(el => {

        if (el.classList.contains('data-validade')){
            el.setAttribute('type', 'date');
            el.value = dataValidade;
        }

        el.classList.remove('input-bloqueado');     // Remove a classe bloqueada
        el.classList.add('input-liberado');         // Adiciona a classe liberado
        el.removeAttribute('readonly');             // Remove o atributo readonly
    });

    if (saldoFinal!=0){
        document.getElementById('bl-chk-liberaSaldo').style.display='block';
    }else{
      document.getElementById('bl-chk-liberaSaldo').style.display='none';
    }
    document.querySelectorAll('.btn-top-venda')
          .forEach(btn => btn.style.display = 'none');




    
    
    document.querySelector('#btnHabilitaEdicao').hidden = true;

    document.querySelector('#footer').removeAttribute('hidden');
    document.querySelector('#nome-vendedor').classList.remove('blockItem');
console.log('Edição habilitada');


}




function bloquearEdicao(){
  // 1) Remove o 'disabled' ou 'readonly' dos inputs que estavam travados
  
    let itensBloquear = '#tabela-itensVenda th:first-child, #tabela-itensVenda td:first-child, #tabela-pagamentos th:first-child, #tabela-pagamentos td:first-child';
    const inputDataVenda = document.getElementById('data-venda');

    if (tipo_venda == 'venda' && data_venda){
        inputDataVenda.setAttribute('type', 'text');
    }
    const campos = document.querySelectorAll(itensBloquear);
    campos.forEach(el => {
        el.setAttribute('hidden','');
    });

    const desbloqueados = document.querySelectorAll('.input-liberado');

    desbloqueados.forEach(el => {

        if (el.classList.contains('data-validade')){
            dataValidade = el.value;
            let dataFormatada = el.value.split('-').reverse().join('/');
            el.setAttribute('type', 'text');
            el.value = dataFormatada;
        }
        el.classList.remove('input-liberado');     // Remove a classe bloqueada
        el.classList.add('input-bloqueado');         // Adiciona a classe liberado
        el.setAttribute('readonly','');             // Remove o atributo readonly
    });

  
    const btnEdicao = document.querySelector('#btnHabilitaEdicao'); //.hidden = false;
    if (btnEdicao){
      btnEdicao.style.display='none';
    }

    document.getElementById('bl-chk-liberaSaldo').style.display='none'
    
    document.querySelector('#footer').setAttribute('hidden', '');
    
   
    document.querySelector('#nome-vendedor').classList.add('blockItem');

} //fim de habilitar edição

var selectVendedor = document.querySelector('.vendedor-selecionado');

function mudaVendedor(){

    const vendSelect = document.querySelector('.vendedor-selecionado');
    const vendedorId = document.getElementById('id-vendedor');
    // Pega a option selecionada
    const selectedVendedor = vendSelect.options[vendSelect.selectedIndex];

    // Pega o atributo personalizado "id-vendedor" da option selecionada
    const idVendedor = selectedVendedor.getAttribute('id-vendedor');

    // Atribui o valor no select como "selected-id-vendedor"
    vendSelect.setAttribute('selected-id-vendedor', idVendedor);
    vendedorId.value = idVendedor;

}


selectVendedor.addEventListener('change', function () {
    console.log('Mudou o vendedor');
    mudaVendedor();


});



function gerarContrato(dataContrato, diasValidade) {
  // Após a linha texto_valor_final:
    let textoItensTabela = '';
    let textoItensCobrado = '';

    let trs = $('#tabela-itensVenda tbody tr').not(':last'); // Ignora a última tr
    let totalItens = trs.length;
    trs.each(function (index) {
        let quantidade = parseFloat($(this).find('.quantidade').val() || 0);
        let item = $(this).find('.item-select option:selected').text().trim();
        let precoTabela = parseFloat($(this).find('.preco-tabela').val() || 0);
        let precoCobrado = parseFloat($(this).find('.preco-cobrado').val() || 0);
        

        let totalTabela = quantidade * precoTabela;
        let totalCobrado = quantidade * precoCobrado;

        let separador = '';
        if (index < totalItens - 2) {
            separador = ', ';
        } else if (index === totalItens - 2) {
            separador = ' e ';
        } // último item fica vazio

        textoItensTabela += `${quantidade} ${item} R$${DecimalBr(totalTabela)}${separador}`;
        textoItensCobrado += `${quantidade} ${item} R$${DecimalBr(totalCobrado)}${separador}`;
    });

    // Remove a vírgula ou " e " final, se necessário
    textoItensTabela = textoItensTabela.trim().replace(/(,| e )$/, '');
    textoItensCobrado = textoItensCobrado.trim().replace(/(,| e )$/, '');

    console.log('data Contrato' + dataContrato);
  
  
    let dadosContrato = {

        id_venda: $('#id-venda').val(),
        id_cliente: $('#id-cliente').val(),

        valor_original: $('#valor-original').val(),
        valor_desconto: $('#valor-desconto').val(),
        valor_final: $('#valor-final').val(),
        
        texto_valor_original: $('#txt-valor-original').val(),
        texto_valor_desconto: $('#txt-valor-desconto').val(),
        texto_valor_final: $('#txt-valor-final').val(),


        texto_itens_tabela: textoItensTabela,
        texto_itens_cobrado: textoItensCobrado,


                
        data_contrato: formatarDataBr(dataContrato),
        dias_validade: diasValidade
    };


 
    $.ajax({
      url: './venda/gerar-contrato.php',
      type: 'POST',
      data: dadosContrato,
      xhrFields: {
          responseType: 'blob'
      },
      success: function (data) {
          const blob = new Blob([data], { type: 'application/pdf' });
          const blobURL = URL.createObjectURL(blob);
          window.open(blobURL, '_blank');
      },
      error: function (err) {
          console.error('Erro ao gerar contrato:', err);
          Swal.fire('Erro', 'Ocorreu um erro ao gerar o contrato.', 'error');
      }
  });

}



$('#btn-gerar-contrato').on('click', function () {
    Swal.fire({
        title: 'Preencha os Dados do Contrato',
        html:
            '<input type="date" id="dataContrato" class="swal2-input" placeholder="Data do Contrato" value="${new Date().toISOString().slice(0,10)}">' +
            '<input type="number" id="diasValidade" min="1" class="swal2-input" placeholder="Dias de Validade" value="30">',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        focusConfirm: false,
        preConfirm: () => {
            const dataContrato = $('#dataContrato').val();
            const diasValidade = $('#diasValidade').val();

            if (!dataContrato || !diasValidade || diasValidade <= 0) {
                Swal.showValidationMessage('Por favor, preencha todos os campos corretamente.');
                return false;
            }

            // Chama tua função que gera o contrato
            gerarContrato(dataContrato, diasValidade);
        }
    });
});





function atualizarTextos() {
    const textoVBruto = document.getElementById('txt-valor-original');
    const textoVFinal = document.getElementById('txt-valor-final');
    const textoVDesconto = document.getElementById('txt-valor-desconto');

    let txtVBruto = reaisPorExtenso(textoVBruto.value);
    let txtVFinal = reaisPorExtenso(textoVFinal.value);
    let txtVDesconto = reaisPorExtenso(textoVDesconto.value);

    textoVBruto.value = txtVBruto;
    textoVFinal.value = txtVFinal;
    textoVDesconto.value = txtVDesconto;
}
atualizarTextos();




    function marcaErro(elemento) {
          elemento.style.border = '2px solid red';
          setTimeout(() => {
              elemento.style.border = '';
          }, 1000);
      }




    function verificaPagamento() {
        const linhas = document.querySelector('#tabela-pagamentos tbody tr');
        console.log('função verificar pagametno');
        
        if (!linhas) {
            return true; // Se não existir linha, não faz nada
        }

        const selectTipo = linhas.querySelector('.tipo-pagamento');
        const selectForma = linhas.querySelector('.forma-pagamento');
        const inputValor = linhas.querySelector('.valor-pagamento');

        // Pega os valores já tratados (removendo espaços e colocando em lowercase)
        const tipoValor = selectTipo.value.trim().toLowerCase();
        const formaValor = selectForma.value.trim().toLowerCase();
        const valorPagamento = inputValor.value.trim();

        // Função para marcar de vermelho e voltar ao normal depois de 1 segundo
    
        // Condições
        if (!tipoValor || tipoValor === 'forma pagamento') {
            marcaErro(selectTipo);
            
            return false;
        }

        if (!formaValor || formaValor === 'selecione forma' || formaValor === 'selecione conta') {
            marcaErro(selectForma);
            
            return false;
        }

        // Trata o valor do pagamento no formato brasileiro (remove ponto dos milhares e troca vírgula por ponto)
        const valorNumerico = parseFloat(DecimalIngles(valorPagamento.replace(/\./g, '')));

        if (!valorPagamento || valorNumerico <= 0 || isNaN(valorNumerico)) {
            marcaErro(inputValor);
           
            return false;
        }
        
        return true;

    }
  





    //esta
function verificaItens() {
    
    const linhas = document.querySelectorAll('#itens-body tr');
    const primeiraLinha = linhas[0]; // linha inserida nova está sempre no início
    const quantidade = linhas.length-1;
    console.log('a quantidade de linhas é:   ', quantidade);
    if (quantidade==0) {
      return true; 
    }

    const selectItem = primeiraLinha.querySelector('.item-select');
    const idItem = primeiraLinha.querySelector('.id-item').value;

    const selectItemV = selectItem.value.trim().toLowerCase();

    if (!selectItemV || selectItemV === 'selecione uma opção') {
            marcaErro(selectItem);
      return false;
    }

    if (!idItem || idItem == 0) {
      marcaErro(selectItem);
      return false;
    }

    return true;
}




//var formularioEnvio =false;

function exibirMensagem(texto) {
    $('#mensagem').stop(true, true).hide().text(texto).fadeIn();

    setTimeout(function () {
        $('#mensagem').fadeOut();
    }, 3000);
}


function verificaEnvio(){
    const itemSaldo= document.querySelector('#sp-saldo-final');
    //verificaItens();
    
    
    if (!verificaItens()) {
        exibirMensagem('Verifique os itens');
        return false;
    }
  
    if (!verificaPagamento()) {
        exibirMensagem('Verifique as formas de pagamento.');
        return false;
    }


    if(totValItens==0 && totPagamentos==0){
      exibirMensagem('Verifique as condições.');
      return false;
    }



    const chkLiberaSaldo = document.getElementById('chck-libera-saldo');
    if ((saldoFinal != 0 && !chkLiberaSaldo.checked && chkLiberaSaldo)|| (saldoFinal != 0 && !chkLiberaSaldo)) {
       
        marcaErro(itemSaldo);
        exibirMensagem('Não deve haver saldo');
        return false;
    }

    return true;
}








//<script type="text/javascript">

/**
 * Busca os pagamentos de uma venda e preenche o input pagamento_id[] em cada linha da tabela.
 * @param {number|string} idVenda - ID da venda a consultar.
 */
async function preencherIdPagto(idVenda) {
    try {
        // 1) Busca os pagamentos no servidor
        const resp = await fetch(`./endPoints/get_vendas_pagamentos.php?idVenda=${encodeURIComponent(idVenda)}`);
        if (!resp.ok) throw new Error(`Erro na requisição: ${resp.status}`);
        const pagamentos = await resp.json();

        // espera algo como: [{id: 123, forma: "Cartão", condicao: "À vista", valor: "150.00"}, …]

        // 2) Seleciona todas as linhas do corpo da tabela
        const rows = document.querySelectorAll('#tabela-pagamentos tbody tr');

        // 3) Verificação de quantidade
        if (pagamentos.length !== rows.length) {
            console.warn(
                `Foram retornados ${pagamentos.length} pagamentos, ` +
                `mas existem ${rows.length} linhas na tabela.`
            );
            // Opcional: você pode lançar um erro aqui ou apenas logar
        }

        // 4) Para cada registro retornado, procura a linha correspondente e preenche o input hidden
        pagamentos.forEach(pag => {
            // normaliza valores do servidor
            const formaServer     = String(pag.forma).trim();
            const condicaoServer  = String(pag.condicao).trim();
            const valorServer     = String(pag.valor).trim();

            rows.forEach(row => {
                const tipoSelect     = row.querySelector('.tipo-pagamento');
                const condicaoSelect = row.querySelector('.forma-pagamento');
                const valorInput     = row.querySelector('.valor-pagamento');
                const idHiddenInput  = row.querySelector('input[name="pagamento_id[]"]');

                // normaliza valores do front
                const formaFront    = tipoSelect.value.trim();
                const condicaoFront = condicaoSelect.value.trim();
                const valorFront    = valorInput.value.trim();

                // debug para ver comparações
                let testeServer =  DecimalBr(valorServer);
                if (
                formaFront    === formaServer &&
                condicaoFront === condicaoServer &&
                valorFront    === DecimalBr(valorServer)
                ) {
                idHiddenInput.value = pag.id;
                }
            });
        });

    } catch (err) {
        console.error('preencherIdPagto:', err);
    }
}






$("#formVenda").submit(function (event) {
   event.preventDefault();
   
  
  if (!verificaEnvio()){
    console.log('não passou na verificação');
    return;
  }



  console.log('Gravação Liberada----------------')



  var formData = new FormData(this);
 
    $.ajax({
        url: "venda/inserir.php",
        type: 'POST',
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
            $('#mensagem').removeClass();

            if (response.status === "success") {
                bloquearEdicao(); // ✅ Sem o $
                  
                // ✅ Preencher o campo corretamente:
                $('#id-venda').val(response.id_venda);
                //$(data_venda).val(response.data_venda);
               // $('#data-validade').val(response.data_validade);
                
                // ✅ Se quiser, mensagem de sucesso:
                $('#mensagem').addClass('text-success').text(response.mensagem);
                
                 

                if (tipo_venda=='venda'){
                 console.log('atualizando Financeiro com a data: ' + data_venda )
                 console.log(' TIPO: ' + tipo_venda);

                    if(!data_venda){
                      data_venda = document.getElementById("data-venda").value
                    }
                      
                    if (novo){
                      console.log('chamando a função preencher id');
                      preencherIdPagto(response.id_venda);
                    }


                  //atualizarVenda(data_venda);
                  gravarVenda(data_venda);

                }else if (tipo_venda=='proposta'){


                }

            } else {
                $('#mensagem').addClass('text-danger').text(response.mensagem);
            }
        },
        error: function (xhr, status, error) {
            console.error('Erro na requisição:', error);
            $('#mensagem').addClass('text-danger').text('Erro ao salvar. Verifique e tente novamente.');
        },

        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function () {
                    // Progresso de upload
                }, false);
            }
            return myXhr;
        }
    });
});








function gravarVenda(dataVenda){
    let pagamentosV = [];
    
    // Itera sobre cada linha (tr) do tbody dos pagamentos
    $('#pagamentos-body tr').each(function() {
        // Para cada linha, cria um objeto com os dados dos inputs/selects
        let tipoPagamentoSelect = $(this).find("select[name='tipo_pagamento[]']");
        let tipoPagamentoValor = tipoPagamentoSelect.val();
        let idTipoPagamento = tipoPagamentoSelect.find("option:selected").attr("tipo-pg-id");
        let valorLiquido = $(this).find("input[name='valor_pagamento[]']").val() - $(this).find("input[name='valor_taxa[]']").val();
        let contaIdRaw = $(this).find("input[name='id_conta_corrente[]']").val();
        let contaId = parseInt(contaIdRaw); // remove tudo que não é dígito
        let contaObj = contasCorrentesArray.find(c => c.id === contaId);
        let contaNome = contaObj ? contaObj.nome : '';

        let pagamentoLinha = {
            pagamento_id: $(this).find("input[name='pagamento_id[]']").val(), //
            tipo_pagamento: tipoPagamentoValor,
            id_tipo_pagamento: idTipoPagamento,
            valor_liquido: valorLiquido,
            pagamento: $(this).find("select[name='pagamento[]']").val(),
            id_pagamento: $(this).find("input[name='id_pagamento[]']").val(),
            valor_pagamento: $(this).find("input[name='valor_pagamento[]']").val(),
            qt_parcelas: $(this).find("input[name='qt_parcelas[]']").val(),
            parcelas: $(this).find("input[name='parcelas[]']").val(),
            valor_taxa: $(this).find("input[name='valor_taxa[]']").val(),
            perc_taxa: $(this).find("input[name='perc_taxa[]']").val(),
            dias_pagamento: $(this).find("input[name='dias_pagamento[]']").val(),
            id_conta_corrente: contaId,
            conta_corrente: contaNome,
            pago: $(this).find("input[name='pago[]']").val()
        };
        pagamentosV.push(pagamentoLinha);
    });

    // Coleta outros dados principais
    let idVendaV    = $('#id-venda').val();
    let idClienteV  = $('#id-cliente').val();
    let valorFinalV = $('#valor-final').val();
    let dataVendaV  = dataVenda; // supondo que você tenha um input para a data da venda

    // Monta o objeto a ser enviado via AJAX, incluindo o array de pagamentos agrupado
    let vendaDataV = {
        id_venda: idVendaV,
        id_cliente: idClienteV,
        valor_final: valorFinalV,
        data_venda: dataVendaV,
        pagamentos: pagamentosV  // este campo será enviado como um array de objetos
    };

    $.ajax({
        url: './venda/gravar_venda.php',
        type: 'POST',
        data: vendaDataV,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
            Swal.fire('Sucesso', 'Venda concluída com sucesso!', 'success')
                .then(function() {
                    // Fecha o modal (ajuste o seletor conforme o id do modal)



                    const evt = new CustomEvent('VendaGravadaComSucesso', {
                        detail: "venda gravada com sucesso"// aqui pode incluir o id_venda, dados etc
                    });

                    document.dispatchEvent(evt);

                    //$('#modalVenda').modal('hide');
                    // Atualiza a página
                    //location.reload();
                });
                // Atualize a interface conforme necessário
            } else {
                Swal.fire('Erro', response.mensagem || 'Ocorreu um erro ao processar a venda.', 'error');


            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Erro', 'Erro ao processar a venda. Por favor, tente novamente.', 'error');
        }
    });


}

// Evento para o botão vender
$('#btn-vender').on('click', function () {
  
    Swal.fire({
        title: 'Informe a data da venda',
        html: '<input type="date" id="dataVenda" class="swal2-input" value="${new Date().toISOString().slice(0,10)}">',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        focusConfirm: false,
        preConfirm: () => {
            const dataVenda = $('#dataVenda').val();
            if (!dataVenda) {
                Swal.showValidationMessage('Por favor, informe a data da venda.');
                return false;
            }
            return dataVenda;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Chama a função para enviar os dados passando a data da venda
            
            gravarVenda(result.value);
        }
    });
})




//function atualizarVenda(dataVenda){
//    const dtVenda = document.getElementById('data-venda');
//    gravarVenda(dtVenda.value);
//}



if (origemAgenda){
    
    habilitarEdicao();
          atualizarTaxaItensOn();
          atualizarTodasLinhasItens();
          atualizaTotaisItens();
}