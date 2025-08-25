let janelaAberta =false;
var bloquearAdicaoLinha = false;
document.addEventListener('click', function(event) {
    
  
    const agenda = document.getElementById('agenda-container');
    const tabela = document.getElementById('agendaBody');

    document.getElementById('tooltip-ag').style.display='none';
        var ag = event.target.closest('.agendamento');
        var cel = event.target.closest('.celula');
        var bloq = event.target.closest('.bloqueio');

    if (
        ag && (
            (agenda && agenda.contains(ag)) ||
            (tabela && tabela.contains(ag))
        )
    ) {
                tipoAgendamento = 'edicao';
                var dados = {
                    idAgendamento: ag.getAttribute('data-id_agendamento'),
                    dataAgenda:    ag.getAttribute('data-serv-dataagenda'),
                    horaAgenda:    ag.getAttribute('data-serv-hora'),
                    idCliente:     ag.getAttribute('data-id-cliente'),
                    observacoes:   ag.getAttribute('data-serv-observacoes'),
                    nomeCliente:   ag.getAttribute('data-serv-cliente'),
                    nomeServico:   ag.getAttribute('data-serv-servico'),
                    telefoneCliente: ag.getAttribute('data-serv-telefone'),
                    status:        ag.getAttribute('data-serv-status'),
                    idProfissional: ag.getAttribute('data-serv-id_profissional'),
                    profissional: ag.getAttribute('data-serv-nome_profissional'),
                    fotoCliente:   ag.getAttribute('data-foto_cliente'),
                    aniversario:   ag.getAttribute('data-aniversario'),
                    idServico:     ag.getAttribute('data-serv-id_servico'),
                    preco:     ag.getAttribute('data-serv-preco'),
                    tempo: ag.getAttribute('data-tempo-min')
                };
                
                abrirAgendamento(dados);
                event.stopImmediatePropagation();
            } else if (bloq) {
              selectedOptMenuBloqueio = [
                            bloq.getAttribute('data-id_bloqueio'),
                            bloq.getAttribute('data-serv-hora'),
                            bloq.getAttribute('data-serv-id_profissional'),
                            bloq.getAttribute('data-serv-observacoes'),
                            bloq.getAttribute('data-titulo-bloqueio'),
                            bloq.getAttribute('data-tempo-bloqueio')
                        ]; 

                const idProfBl = selectedOptMenuBloqueio[2];
                const dtHAgBl = selectedOptMenuBloqueio[1];
                const idBloq = selectedOptMenuBloqueio[0];

            abrirBloqueios(idProfBl, dtHAgBl, idBloq);




             }else if (cel) {
              abrirAgendamentoCelula(cel);

             }


        modalAberto = true;
        setTimeout(function() { modalAberto = false; }, 500);
    });


function abrirAgendamentoCelula(celula){
  if (bloquearAdicaoLinha) return;

    tipoAgendamento = 'novo';
    var dados = {
        idProfissional:    celula.getAttribute('data-id_profissional'),
        dataAgenda:        celula.getAttribute('data-data_agenda'),
        nomeProfissional:  celula.getAttribute('data-profissional'),
        horaAgenda:        celula.getAttribute('data-hora_agenda'),
        status: '',
    };
    abrirAgendamento(dados);
    event.stopImmediatePropagation();

}



document.getElementById('janela-agendamentos').addEventListener('mousedown',function(event){
      janela.classList.add('selectedJan');
      janelaBl.classList.remove('selectedJan');
});


document.getElementById('janela-agendamentos').addEventListener('click',function(event){

    const btnFechar = event.target.closest('#btn-fechar-janela');
    const btnCancelar= event.target.closest('#btn-cancelar-janela');
    const btnMinimizar =event.target.closest('#btn-minimizar-janela');
    const btnAddLinha = event.target.closest('#adicionar-item');


    if (btnCancelar || btnFechar){
        fecharAgendamento();
    }

    if (btnMinimizar){
        minimizarJanela(); 
    }

    if (btnAddLinha){
        const tbody = document.querySelector('#tabela-itens tbody');
        // 2. Seleciona a última <tr> (linha) do tbody
        const ultimaLinha = tbody.lastElementChild; 
        // 3. Dentro dessa linha, busca os campos
        const profissional = ultimaLinha.querySelector('.select-profissional').value;
        const servico = ultimaLinha.querySelector('.select-servico').value;
        const valorHoraFim = ultimaLinha.querySelector('.input-hora-fim').value;
        const dataJan = ultimaLinha.querySelector('.input-data-agendamento').value;
          adicionarLinhaItens({
            
            data: dataJan,
            idProfissional: profissional,
            status:'',
            horaInicio: valorHoraFim,
            tempo: '',
            horaFim: ''
        });
        event.stopImmediatePropagation();
    }

});



function limparJanela(){
    document.getElementById('janela_nome_cliente').value = '';
    document.getElementById('janela_id_cliente').value = '';
    document.getElementById('agendamento-descricao').value='';
    bloquearAdicaoLinha =false;
    const dadosCliente = document.getElementById('janela_cliente_info')
    if (dadosCliente){

    dadosCliente.remove();
    }

    infoDiv = '';
    document.querySelector('#janela_foto_cliente').src=''


  // Remove todas as linhas da tabela
  const tbody = document.getElementById('tabela-agendamentos-janela');
  Array.from(tbody.querySelectorAll('tr')).forEach(tr => tr.remove());

  // Remove TODOS os blocos virtuais da agenda
  document
  .querySelectorAll('.agendamento-virtual')
  .forEach(el => {
    const idx = el.dataset.lineIndex;
    removeVirtualAppointment(Number(idx));
  });
  // Zera o array de agendamentos
  linhasAgendamentos.length = 0;
}

        




 agendamentoAberto = false;

function fecharAgendamento() {
    const janela = document.getElementById('janela-agendamentos');
    janela.classList.remove('visivel', 'minimizada');
    
    // Espera a transição acabar antes de esconder de vez
    setTimeout(() => {
      limparJanela();
        janela.setAttribute('hidden', true);
         
    }, 400); // mesmo tempo do transition

    
    janelaAberta=false;
    agendamentoAberto=false;
   
}



let janelaMinimizada=false;

function minimizarJanela() {
  const jan = document.getElementById('janela-agendamentos');
  const foiMinimizada = jan.classList.toggle('minimizada');
  janelaMinimizada = foiMinimizada;
}





function abrirAgendamento(dados) {
  // transforma cada campo de dados em variável
  const {
    idAgendamento,
    dataAgenda,
    horaAgenda,
    idCliente,
    observacoes,
    nomeCliente,
    nomeServico,
    telefoneCliente,
    status,
    idProfissional,
    profissional,
    nomeProfissional,
    fotoCliente,
    aniversario,
    idServico,
    preco,
    tempo
  } = dados;

   const idClienteAg = document.getElementById('janela_id_cliente');
   const nomeClienteAg = document.getElementById('janela_nome_cliente');
   const btnClienteAg = document.getElementById('btn-adicionar-cliente');
   const blDescricao = document.getElementById('agendamento-descricao');


  // abre a janela se ainda não estiver aberta
    if (!janelaAberta) {
      //const janela = document.getElementById('janela-agendamentos');
      janela.removeAttribute('hidden');
      requestAnimationFrame(() => {
        janela.classList.add('visivel');
      });
 
      if (idAgendamento){
          nomeClienteAg.value=nomeCliente;
          idClienteAg.value=idCliente;
          blDescricao.value=observacoes;
          carregaDadosCliente();
          nomeClienteAg.classList.add('blockItem');
          btnClienteAg.style.display = 'none';
          var horaFimAg = calcularHoraFim(horaAgenda, tempo);

          adicionarLinhaItens({
            idAgendamento: idAgendamento,
            data: dataAgenda,
            idProfissional: idProfissional,
            profissional: profissional,
            idServico: idServico,
            servico: nomeServico,
            preco: preco,
            horaInicio: horaAgenda.substring(0,5),
            tempo: tempo,
            horaFim: horaFimAg.slice(0,5),
            status: status,

          });

          agendamentoAberto=true;

        }else{
       
         
            nomeClienteAg.classList.remove('blockItem');
            btnClienteAg.style.display = 'block';
         
          adicionarLinhaItens({
              data: dataAgenda,
              idProfissional:idProfissional,
              horaInicio: horaAgenda,
              status:''
              
            });
        }

        janelaAberta = true;
    }else{


      if (!idAgendamento){
         if (!agendamentoAberto){
          nomeClienteAg.classList.remove('blockItem');
          btnClienteAg.style.display = 'block';
         }
          adicionarLinhaItens({
              data: dataAgenda,
              idProfissional:idProfissional,
              horaInicio: horaAgenda,
              status:''
              
            });

      }



    }


 if(janelaMinimizada){minimizarJanela()};

}
  



// Auxiliar para criar <option>
function newOpt(text, value) {
  const o = document.createElement('option');
  o.textContent = text;
  o.value = value;
  return o;
}




function calcularHoraFim(horaInicio, tempoParam) {
  const [h, m] = horaInicio.split(':').map(n => parseInt(n, 10));
  const tempo = parseInt(tempoParam, 10) || 0;
  if (isNaN(h) || isNaN(m)) return '';
  const totalMin = h * 60 + m + tempo;
  const fh = String(Math.floor(totalMin / 60)).padStart(2, '0');
  const fm = String(totalMin % 60).padStart(2, '0');
  return `${fh}:${fm}`;
}



// Máscara e validação para campos de hora (HH:MM) dentro do horário da agenda













/**
 * Exibe uma mensagem temporária em vermelho dentro de #mensagem-janela
 * e destaca as linhas incompletas por 3 segundos.
 */
function showTemporaryMessage(text, elemento) {
  const container = document.getElementById('mensagem-janela');
  if (!container) return;
  container.textContent = text;
  container.style.color = 'red';
  
  
    elemento.style.border = '2px solid red';
   elemento.style.borderRadius = '8px';
  
  setTimeout(() => {
    container.textContent = '';
  
  
        elemento.style.border = '';
      elemento.style.borderRadius = '';
  
  }, 3000);


}

/**
 * Verifica se existe alguma <tr> em #tabela-agendamentos-janela
 * com campos obrigatórios vazios.
 * Retorna o primeiro <tr> incompleto ou null se nenhum.
 */
function findIncompleteRow() {
  const rows = document.querySelectorAll('#tabela-agendamentos-janela tr');
  for (const tr of rows) {
    const inputs = tr.querySelectorAll('input, select');
    for (const field of inputs) {
      // Ignora campos ocultos ou marcado como input-agendamento
      if (field.type === 'hidden' || field.classList.contains('input-agendamento')) {
        continue;
      }
      // considera obrigatório se value for string vazia
      if (field.value === '') {
        return tr;
      }
    }
  }
  return null;
}



function adicionarLinhaItens(valores = {}) {

  const tbody = document.getElementById('tabela-agendamentos-janela');
  // Validação antes de inserir
  const incomplete = findIncompleteRow();
  if (incomplete) {
       showTemporaryMessage('Preencha todos os campos antes de adicionar uma nova linha.', incomplete);
    return;
  }

  const status = valores.status;

  const index = tbody.rows.length;
  
  // cria a linha
  const tr = document.createElement('tr');

  // 1) botão de excluir
  let carrAgendamento = valores.idAgendamento;

  if (!carrAgendamento){
      const tdExcluir = document.createElement('td');
      const btnExcluir = document.createElement('button');
      btnExcluir.type = 'button';
      btnExcluir.textContent = '–';
      btnExcluir.classList.add('btn-excluir-item', 'btn', 'btn-danger', 'remover-item', 'centBt');
      
      btnExcluir.addEventListener('click', () => {
      // só remove se ainda houver mais de uma linha
          if (tbody.rows.length > 1) {
            removerItemDoArray(tr);
            tbody.removeChild(tr);
          }
        });
      tdExcluir.appendChild(btnExcluir);
      tr.appendChild(tdExcluir);
  } else {
      const tdAgend = document.createElement('td');
      tdAgend.classList.add('text-center', 'align-middle', 'td-icone-ok');
      tdAgend.innerHTML = '<i class="bi bi-check2-circle text-success fs-4"></i>';
      tr.appendChild(tdAgend);
  }

  // 2) input hidden com id do agendamento
  const tdHidden = document.createElement('td');
  const inputHidden = document.createElement('input');
  tdHidden.setAttribute('hidden', 'true');
  inputHidden.type = 'hidden';
  inputHidden.classList.add ('input-agendamento');
  inputHidden.name = `agendamento-jan[${index}].idAgendamento`;
  inputHidden.value = valores.idAgendamento || '';
  tdHidden.appendChild(inputHidden);
  tr.appendChild(tdHidden);

  // 3) Data
  const tdData = document.createElement('td');
  const inputData = document.createElement('input');
  inputData.type = 'date';
  inputData.name = `agendamento-jan[${index}].data`;
  inputData.classList.add('form-control', 'input-data-agendamento');
  inputData.value = valores.data || '';
  tdData.appendChild(inputData);
  tr.appendChild(tdData);

  // 4) Profissional
  const tdProf = document.createElement('td');
  let selectProf;
  
  if (valores.status != 'Finalizado'){
  
  selectProf = document.createElement('select');
  selectProf.name = `agendamento-jan[${index}].idProfissional`;
  selectProf.classList.add('select-profissional', 'form-select');
   // opção padrão options
      const optProfDefault = document.createElement('option');
      optProfDefault.value = '';
      optProfDefault.textContent = 'Selecione';
      selectProf.appendChild(optProfDefault);
      // se vier pré-selecionado
      if (valores.idProfissional) {
        const opt = document.createElement('option');
        opt.value = valores.idProfissional;
        opt.textContent = valores.idProfissional;
        opt.selected = true;
        selectProf.appendChild(opt);
      }
      
  }else{
    selectProf = document.createElement('input');
    selectProf.value = valores.profissional;
    selectProf.setAttribute('data-id_profissional', valores.idProfissional);
    selectProf.classList.add('form-control', 'blockItem');
  }
  tdProf.appendChild(selectProf);
  tr.appendChild(tdProf);



  // 5) Serviço
  const tdServ = document.createElement('td');
  let selectServ;
  
  if (valores.status != 'Finalizado'){
      selectServ = document.createElement('select');
      selectServ.name = `agendamento-jan[${index}].idServico`;
      selectServ.classList.add('select-servico', 'form-select');
      const optServDefault = document.createElement('option');
      optServDefault.value = '';
      optServDefault.textContent = 'Selecione';
      selectServ.appendChild(optServDefault);
      if (valores.idServico) {
        const opt = document.createElement('option');
        opt.value = valores.idServico;
        opt.textContent = valores.idServico;
        opt.selected = true;
        selectServ.appendChild(opt);
      }
  }else{
      selectServ = document.createElement('input');
      selectServ.value = valores.servico;
      selectServ.classList.add('blockItem', 'form-control');
  }


  tdServ.appendChild(selectServ);
  tr.appendChild(tdServ);

 
 
 
  // 6) Preço
  const tdPreco = document.createElement('td');

  // 1️⃣ Cria o wrapper .input-group
  const divInputGroup = document.createElement('div');
  divInputGroup.classList.add('input-group', 'flex-nowrap');

  // 2️⃣ Cria o span com o prefixo "R$"
  const spanPrefix = document.createElement('span');
  spanPrefix.classList.add('input-group-text');
  if (status=='Finalizado'){
    spanPrefix.style.backgroundColor = 'transparent';
    spanPrefix.style.border = 'none';

  }
  spanPrefix.textContent = 'R$';

  // 3️⃣ Cria o input formatado
  const inputPreco = document.createElement('input');
  inputPreco.classList.add('form-control', 'numero-virgula-financeiro', 'input-preco');
  inputPreco.type  = 'text';
  inputPreco.name  = `agendamento-jan[${index}].preco`;
  console.log('recebeu o valor do preço em: ', valores.preco);
  inputPreco.value = (valores.preco && valores.preco>0)?DecimalBr(valores.preco):'';
  console.log('o valor do input ficou em : ', inputPreco.value);

  // 4️⃣ Monta a estrutura
  divInputGroup.appendChild(spanPrefix);
  divInputGroup.appendChild(inputPreco);
  tdPreco.appendChild(divInputGroup);
  tr.appendChild(tdPreco);

  // 7) Início
  const tdInicio = document.createElement('td');
  const inputInicio = document.createElement('input');
  inputInicio.classList.add('form-control', 'input-hora');
  inputInicio.type = 'text';
  inputInicio.name = `agendamento-jan[${index}].horaInicio`;
  inputInicio.value = valores.horaInicio || '';
  tdInicio.appendChild(inputInicio);
  tr.appendChild(tdInicio);

  // 8) Tempo
  const tdTempo = document.createElement('td');
  const inputTempo = document.createElement('input');
  inputTempo.classList.add('form-control', 'input-tempo');
  inputTempo.type = 'number';
  inputTempo.name = `agendamento-jan[${index}].tempo`;
  inputTempo.value = valores.tempo || '';
  console.log('o tempo é de: ', valores.tempo);
  tdTempo.appendChild(inputTempo);
  tr.appendChild(tdTempo);

  // 9) Fim


  const tdFim = document.createElement('td');
  const inputFim = document.createElement('input');
  inputFim.classList.add('form-control', 'input-hora-fim', 'input-hora');
  inputFim.style.pointerEvents='none';
  inputFim.type = 'text';
  inputFim.name = `agendamento-jan[${index}].horaFim`;
  inputFim.value = valores.horaFim || '';
  tdFim.appendChild(inputFim);
  tr.appendChild(tdFim);


if (status=='Finalizado'){
    tr.querySelectorAll('td input:not([type="hidden"]), td select').forEach(el => {
      if (el.classList && typeof el.classList.add === "function") {
        el.classList.add('blockItem');
      }
    });
    document.getElementById('adicionar-item').style.display = 'none';
    document.getElementById('btn-salvarAgendamento').setAttribute('disabled', 'true');
    bloquearAdicaoLinha = true;
} else{
    document.getElementById('adicionar-item').style.display = '';
    document.getElementById('btn-salvarAgendamento').removeAttribute('disabled');
}

  // insere no topo
  //tbody.insertBefore(tr, tbody.firstChild);
tbody.appendChild(tr);

  if (valores.status != 'Finalizado'){
  attachListeners(tr);
  populaLinha(tr);
  };
  
    if (!carrAgendamento){
      preenchePrecoTempo(tr);
      adicionarItemAoArray(tr);
      attachFieldListeners(tr);
    }
}






// Popula selects de uma linha, preservando seleção atual
// Popula selects de uma linha, preservando seleção atual
function populaLinha(tr) {
  const selectProf = tr.querySelector('.select-profissional');
  const selectServ = tr.querySelector('.select-servico');
  const selProf = selectProf.value;
  const selServ = selectServ.value;

  selectProf.innerHTML = '';
  selectServ.innerHTML = '';
  selectProf.appendChild(newOpt('Selecione', ''));
  selectServ.appendChild(newOpt('Selecione', ''));

  if (selProf && selServ) {
    profServData[selProf].servicos.forEach(item => {
      selectServ.appendChild(newOpt(item.servico, item.id_servico));
    });
    Object.entries(profServData).forEach(([pid, obj]) => {
      if (obj.servicos.some(s => s.id_servico == selServ)) {
        selectProf.appendChild(newOpt(obj.nome, pid));
      }
    });
  } else if (selProf) {
    profServData[selProf].servicos.forEach(item => {
      selectServ.appendChild(newOpt(item.servico, item.id_servico));
    });
    Object.entries(profServData).forEach(([pid, obj]) => {
      selectProf.appendChild(newOpt(obj.nome, pid));
    });
  } else if (selServ) {
      const seen = new Set();
      Object.values(profServData).forEach(obj => {
        obj.servicos.forEach(item => {
          if (!seen.has(item.id_servico)) {
            seen.add(item.id_servico);
            selectServ.appendChild(newOpt(item.servico, item.id_servico));
          }
        });
      });
      Object.entries(profServData).forEach(([pid, obj]) => {
        if (obj.servicos.some(s => s.id_servico == selServ)) {
          selectProf.appendChild(newOpt(obj.nome, pid));
        }
      });
  } else {
      // 1) popula todos os profissionais
      Object.entries(profServData).forEach(([pid, obj]) => {
        selectProf.appendChild(newOpt(obj.nome, pid));
      });
      // 2) popula todos os serviços disponíveis (defaults)
      const seen = new Set();
      Object.values(profServData).forEach(({ servicos }) => {
        servicos.forEach(item => {
          if (!seen.has(item.id_servico)) {
            seen.add(item.id_servico);
            selectServ.appendChild(newOpt(item.servico, item.id_servico));
          }
        });
      });
    }

  selectProf.value = selProf;
  selectServ.value = selServ;
}

// Preenche preço, tempo e calcula hora fim
function preenchePrecoTempo(tr) {
  const selectProf = tr.querySelector('.select-profissional');
  const selectServ = tr.querySelector('.select-servico');
  const inputPreco = tr.querySelector('.input-preco');
  const inputTempo = tr.querySelector('.input-tempo');
  const inputHoraInicio = tr.querySelector('input[name$=".horaInicio"]');
  const inputHoraFim = tr.querySelector('.input-hora-fim');

  const selProf = selectProf.value;
  const selServ = selectServ.value;
  let tempo, preco;

  // Só busca do array se o campo está vazio
  if (selProf && selServ && profServData[selProf]) {
  const servItem = profServData[selProf].servicos.find(s => s.id_servico == selServ);
  if (servItem) {
    inputTempo.value = servItem.tempo;
    inputPreco.value = DecimalBr(servItem.preco);
  }
} else if (selServ && defaultServ[selServ]) {
  inputTempo.value = defaultServ[selServ].tempo;
  inputPreco.value = DecimalBr(defaultServ[selServ].preco);
}




  // Calcula hora fim usando função dedicada
  if (inputHoraInicio && inputTempo.value) {
    inputHoraFim.value = calcularHoraFim(inputHoraInicio.value, inputTempo.value);
  }
}






// Anexa listeners aos selects e inputs de hora/tempo
// Anexa listeners aos selects e inputs de hora/tempo
function attachListeners(tr) {
  const selectProf = tr.querySelector('.select-profissional');
  const selectServ = tr.querySelector('.select-servico');
  const inputTempo = tr.querySelector('.input-tempo');
  const inputHoraInicio = tr.querySelector('input[name$=".horaInicio"]');
  const inputHoraFim = tr.querySelector('.input-hora-fim');
  const tempo = inputTempo.value;
  

  selectProf.addEventListener('change', () => {
    populaLinha(tr);
    //preenchePrecoTempo(tr);
  });
  selectServ.addEventListener('change', () => {
    console.log('serviço alterado');
    populaLinha(tr);
    preenchePrecoTempo(tr);
  });
  inputTempo.addEventListener('change', () => {
    inputHoraFim.value = calcularHoraFim(inputHoraInicio.value, tr.querySelector('.input-tempo').value);
    
  });
  inputHoraInicio.addEventListener('change', () => {
   inputHoraFim.value = calcularHoraFim(inputHoraInicio.value,tr.querySelector('.input-tempo').value);
  
  });

  // Máscara e validação de hora de início
  inputHoraInicio.addEventListener('input', () => maskHoraAgenda(inputHoraInicio));
  inputHoraInicio.addEventListener('blur', () => maskHoraAgenda(inputHoraInicio));
}



const profServData = {};

// Atualiza profServData a partir do endpoint e popula todas as linhas
let defaultServ = {};

// Atualiza profServData e defaultServ via endpoint



  function maskHoraAgenda(input) {
  // 1) Remove tudo que não for dígito e limita a 4 caracteres
  let raw = input.value.replace(/\D/g, '').slice(0, 4);

  // 2) Formata com ':' após os dois primeiros dígitos
  let formatted;
  if (raw.length > 2) {
    formatted = raw.slice(0, 2) + ':' + raw.slice(2);
  } else {
    formatted = raw;
  }

  // 3) Valida se está completo (HH:MM)
  if (formatted.length === 5) {
    let [h, m] = formatted.split(':').map(n => parseInt(n, 10));

    // 4) Obtém limites da agenda a partir dos data-attributes das células
    const cells = document.querySelectorAll('#agenda-container .celula');
    const times = Array.from(cells)
      .map(c => c.getAttribute('data-hora_agenda'))
      .filter(Boolean);
    times.sort((a, b) => {
      const [ha, ma] = a.split(':').map(Number);
      const [hb, mb] = b.split(':').map(Number);
      return ha * 60 + ma - (hb * 60 + mb);
    });
    const abertura     = times[0] || '00:00';
    const encerramento = times[times.length - 1] || '23:59';
    const [hMin, mMin] = abertura.split(':').map(Number);
    const [hMax, mMax] = encerramento.split(':').map(Number);

    // 5) Ajusta para dentro dos limites
    if (isNaN(h) || h < hMin) h = hMin;
    if (h > hMax) h = hMax;
    if (isNaN(m) || m < 0) m = 0;
    if (m > 59) m = 59;
    if (h === hMax && m > mMax) m = mMax;

    formatted = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
  }

  // 6) Atualiza valor e posiciona cursor no fim
  input.value = formatted;
  const pos = input.value.length;
  input.setSelectionRange(pos, pos);
}







function verificarEnvio(){

 const incomplete = findIncompleteRow();
  if (incomplete) {
    // destaca a linha incompleta
  
    showTemporaryMessage('Preencha todos os campos antes de adicionar uma nova linha.', incomplete);
    return false;
  }

  const idClienteEnvio = document.getElementById('janela_id_cliente').value;
  const nomeClienteEnvio = document.getElementById('janela_nome_cliente');
  if (!idClienteEnvio){
   
    showTemporaryMessage('Selecione um cliente na lista', nomeClienteEnvio);
    return false;
  }

  return true;


}






function enviarAgendamentos() {


enviar = verificarEnvio();


if(!enviar)return;
  
  const tbody = document.getElementById('tabela-agendamentos-janela');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  const formData = new FormData();

  // Campos globais da janela
  const idCliente = document.querySelector('input[name="janela_id_cliente"]').value;
  const descricao = document.querySelector('textarea[name="agendamento-descricao"]').value;
  const imageInput = document.getElementById('input-imagem');

  rows.forEach((tr, i) => {
    const idAg = tr.querySelector('input[name^="agendamento-jan"][name$=".idAgendamento"]').value;
    const data = tr.querySelector('input[type="date"]').value;
    const horaIni = tr.querySelector('input[name$=".horaInicio"]').value;
    const tempo = tr.querySelector('input[name$=".tempo"]').value;
    const horaFim = tr.querySelector('input[name$=".horaFim"]').value;
    const preco = tr.querySelector('input[name$=".preco"]').value;
    const idServ = tr.querySelector('select[name$=".idServico"]').value;
    const idProf = tr.querySelector('select[name$=".idProfissional"]').value;
    const servicoTexto = tr.querySelector('select[name$=".idServico"] option:checked').textContent;
    const nomeProfTexto = tr.querySelector('select[name$=".idProfissional"] option:checked').textContent;

    // Campos por linha
    formData.append(`agendamento-jan[${i}][idAgendamento]`, idAg);
    formData.append(`agendamento-jan[${i}][data]`, data);
    formData.append(`agendamento-jan[${i}][horaInicio]`, horaIni);
    formData.append(`agendamento-jan[${i}][tempo]`, tempo);
    formData.append(`agendamento-jan[${i}][horaFim]`, horaFim);
    formData.append(`agendamento-jan[${i}][preco]`, preco);
    formData.append(`agendamento-jan[${i}][idServico]`, idServ);
    formData.append(`agendamento-jan[${i}][servicoTexto]`, servicoTexto);
    formData.append(`agendamento-jan[${i}][idProfissional]`, idProf);
    formData.append(`agendamento-jan[${i}][profissionalTexto]`, nomeProfTexto);
    formData.append(`agendamento-jan[${i}][idCliente]`, idCliente);
    formData.append(`agendamento-jan[${i}][observacoes]`, descricao);

    // Imagem opcional
    if (imageInput && imageInput.files.length > 0) {
      formData.append(`agendamento-jan[${i}][imagem]`, imageInput.files[0]);
    }
  });

  fetch('agenda_atendimentos/inserir_agendamentos.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        //alert('Agendamentos salvos com sucesso!');
      
      
      trocaDia=false;


     

      carregarAgenda(dataCalend, agCancelados);
      fecharAgendamento();
    
    } else {
        alert('Erro: ' + (result.message || result.error));
      }
    })
    .catch(err => console.error(err));
}

// Botão Salvar
const btnSalvar = document.querySelector('#btn-salvarAgendamento');
if (btnSalvar) {
  btnSalvar.addEventListener('click', e => {
    
    
    e.preventDefault();
    enviarAgendamentos();
  });
}


//trocaDia=false;
//carregarAgenda(dataCalend, agCancelados);
