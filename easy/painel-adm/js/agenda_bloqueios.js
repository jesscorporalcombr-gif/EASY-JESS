

let janelaMinimizadaBl=false;
let janelaAbertaBl =false;
let agendamentoAbertoBl = false;
let bloqueiosVirtuais = [];
let nextBloqueioIndex = 1;
let valoresAntigos = { ini: "", fim: "", tempo: "" };

let bloqueioAtualizacao = false; // flag para evitar loop

document.addEventListener('DOMContentLoaded', () => {
  // Título
  const tituloInput = document.getElementById('titulo-bloqueio');
  const tituloContagem = document.getElementById('contagem-titulo');
  const tituloLimite = 50;

  // Descrição
  const descricaoInput = document.getElementById('descricao-bloqueio');
  const descricaoContagem = document.getElementById('contagem-descricao');
  const descricaoLimite = 300;

  function contadorEValida(input, label, limite) {
    input.addEventListener('input', () => {
      let texto = input.value;
      if (texto.length > limite) {
        input.value = texto.substring(0, limite);
        texto = input.value;
        // Borda vermelha temporária
        input.classList.add('input-borda-vermelha');
        setTimeout(() => input.classList.remove('input-borda-vermelha'), 3000);
      }
      label.textContent = `${texto.length}/${limite}`;
    });
  }

  contadorEValida(tituloInput, tituloContagem, tituloLimite);
  contadorEValida(descricaoInput, descricaoContagem, descricaoLimite);
});

async function excluirBloqueios(id, tpExc){
let idBloq=id;
const payload = {
    id: idBloq,
    tpExc: tpExc
  };

  // 5. Enviar via fetch (application/json, profissional)
  try {
    const resp = await fetch('agenda_atendimentos/excluir_bloqueio.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const resposta = await resp.json();
    if (resposta.sucesso) {
          trocaDia=false;
          efeitos=false;
          carregarAgenda(dataCalend, agCancelados);
          fecharBloqueio();



    } else {
      alert(resposta.mensagem || 'Erro ao gravar bloqueios!');
    }
  } catch (e) {
    alert('Erro de comunicação com o servidor. Tente novamente.');
    console.error(e);
  }


}

document.getElementById('gravar-bloqueio').addEventListener('click', async function() {
  // 1. Verificar se tem bloqueios no array
  if (!bloqueiosVirtuais.length) {
    alert('Nenhum bloqueio criado para gravar.');
    return;
  }

  // 2. Obter intervalo de datas (ajuste conforme seu flatpickr)
  const fp = document.querySelector("#periodo-bloqueio")._flatpickr;
  const selectedDates = fp.selectedDates;
  if (!selectedDates.length) {
    alert('Selecione o intervalo de datas.');
    return;
  }


  const dataIni = selectedDates[0].toISOString().slice(0,10);
  const dataFim = selectedDates.length > 1 ? selectedDates[1].toISOString().slice(0,10) : dataIni;
  const idBloqueioCar = document.getElementById('id-bloq-car')?.value || '';
  const titBloqueio = document.getElementById('titulo-bloqueio')?.value || '';
  const descBloqueio = document.getElementById('descricao-bloqueio')?.value || '';

  if (!titBloqueio.length) {
    alert('Preencha o Título do Bloqueio.');
    return;
  }



  // 3. Obter dias da semana marcados (checkboxes)
  const diasSemana = [];
  [
    'chk-bl-diaDom', 'chk-bl-diaSeg', 'chk-bl-diaTer',
    'chk-bl-diaQua', 'chk-bl-diaQui', 'chk-bl-diaSex', 'chk-bl-diaSab'
  ].forEach((id, idx) => {
    const el = document.getElementById(id);
    if (el && !el.disabled && el.checked) diasSemana.push(idx); // 0=Dom, 1=Seg...
  });




// Verifica diferença de dias
const diasIntervalo = (new Date(dataFim) - new Date(dataIni)) / (1000*60*60*24);

// Agora sim, só bloqueia se for mais de 0 dias E nenhum dia da semana marcado
if (diasIntervalo > 0 && !diasSemana.length) {
  alert('Marque pelo menos um dia da semana.');
  return;
}
  // 4. Preparar payload
  const payload = {
    bloqueios: bloqueiosVirtuais,
    intervalo: { inicio: dataIni, fim: dataFim },
    dias: diasSemana,
    idBloqueioCar: idBloqueioCar,
    titBloqueio: titBloqueio,
    descBloqueio: descBloqueio
  };

  // 5. Enviar via fetch (application/json, profissional)
  try {
    const resp = await fetch('agenda_atendimentos/grava_altera_bloqueio.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const resposta = await resp.json();
    if (resposta.sucesso) {
          trocaDia=false;
          efeitos=false;
          carregarAgenda(dataCalend, agCancelados);
          //fecharBloqueio();



    } else {
      alert(resposta.mensagem || 'Erro ao gravar bloqueios!');
    }
  } catch (e) {
    alert('Erro de comunicação com o servidor. Tente novamente.');
    console.error(e);
  }
});



function adicionarBloqueio(bloqueio) {
  // Garante que cada bloqueio tem um id único
  if (!bloqueio.id) {
    bloqueio.id = Date.now() + Math.random(); // simples para evitar repetição
  }
  bloqueiosVirtuais.push(bloqueio);
  renderizarTodosBloqueios();
}


function renderizarTodosBloqueios() {
  // Limpa todos os bloqueios visuais do DOM
  removerBloqueiosVisuais();
console.log('Bloqueios Virtuais:', bloqueiosVirtuais);
  // Percorre todos os bloqueios do array e cria visualmente
  bloqueiosVirtuais.forEach(bloqueio => {
    criarBloqueioVirtual(bloqueio);
  });
}




function removerBloqueio(idBloqueio) {
  bloqueiosVirtuais = bloqueiosVirtuais.filter(b => b.id !== idBloqueio);
  renderizarTodosBloqueios();
}



function editarBloqueio(id, novosDados) {
  bloqueiosVirtuais = bloqueiosVirtuais.map(bloqueio => {
    if (bloqueio.id === id) {
      return { ...bloqueio, ...novosDados }; // Atualiza apenas os campos informados
    }
    return bloqueio;
  });
  renderizarTodosBloqueios();
}













function criarBloqueioVirtual({ data, idProfissional, horaInicio, tempo, descricao }) {
  // Gera índice único
  const id = nextBloqueioIndex++;

  // Adiciona ao array global
  
  if (dataCalend !== data) return;
  
  // Usa a função profissional para localizar a célula e calcular o offset em minutos
  const { celulaCorreta: parentTd, diferencaMin } = encontrarCelulaMaisProxima(idProfissional, horaInicio);
  
  
  if (!parentTd) return;
  console.log('renderizando Bloqueio');
  // Cálculo de posicionamento
  const pixelsPerMin = alturaLinha / intervaloMin; // global do sistema
  const offsetPx = diferencaMin * pixelsPerMin;
  const heightPx = tempo * pixelsPerMin;

  // Cria o bloco virtual de bloqueio
  const bloco = document.createElement('div');
  bloco.className = 'bloqueio-virtual';
  bloco.dataset.bloqueioIndex = id;
  bloco.title = descricao || 'Bloqueio';

  // Estilos e layout
  parentTd.style.position = 'relative';
  bloco.style.position = 'absolute';
  bloco.style.top = `${offsetPx}px`;
  bloco.style.height = `${heightPx}px`;
  bloco.style.width = '90%';
  bloco.style.left = '5%';
  bloco.style.background = 'rgba(0,0,0,0.2)';
  bloco.style.border = '2px dashed #dc3545';
  bloco.style.borderRadius = '6px';
  bloco.style.zIndex = '12000';
  bloco.style.display = 'flex';
  bloco.style.justifyContent = 'center';
  bloco.style.alignItems = 'center';
  bloco.style.color = '#dc3545';
  bloco.style.fontWeight = 'bold';
  bloco.style.pointerEvents = 'none'; // não bloqueia interação

  bloco.innerHTML = descricao ? `<span>${descricao}</span>` : `<span>Bloqueado</span>`;

  // Adiciona à célula
  parentTd.appendChild(bloco);
  console.log(bloqueiosVirtuais)

  // Animação (opcional)
  requestAnimationFrame(() => bloco.classList.add('visivel'));
}









function abrirBloqueios(idProf, hora, idBloq) {
  
  
  if (!janelaAbertaBl) {


    let horaInicial ='';

    const fp = document.querySelector("#periodo-bloqueio")._flatpickr;
    fp.setDate([dataCalend], true);

    janelaBl.removeAttribute('hidden');
    requestAnimationFrame(() => {
      janelaBl.classList.add('visivel');
      janelaAbertaBl = true;

      // Popular select (isso apenas atualiza as opções)
      

      // Após garantir que o select está populado:
      setTimeout(() => {
        // Reseta campos do formulário
        const horaIni = document.getElementById('bl-h-ini');
        const horaFim = document.getElementById('bl-h-fim');
        const tempo   = document.getElementById('bl-h-tempo');
        const desc    = document.getElementById('descricao-bloqueio');
        const selectProf = document.getElementById('select-profissionalBl');

        if(idBloq){

            document.getElementById('id-bloq-car').value = idBloq;
            desc.value = selectedOptMenuBloqueio[3];
            console.log(selectedOptMenuBloqueio[3]);

            document.getElementById('titulo-bloqueio').value = selectedOptMenuBloqueio[4];
            tempo.value = selectedOptMenuBloqueio[5];
            hora = hora.slice(0, 5);
            horaInicial = hora || getMenorEMaiorHoraColuna().menor;
            horaIni.value = horaInicial;
            document.getElementById('div-Prof-bl').setAttribute('hidden', 'true');
            document.getElementById('coluna-data-bloqueio').setAttribute('hidden', 'true');
            

        }else{

        // Seta valores iniciais
        horaInicial = hora || getMenorEMaiorHoraColuna().menor;
        horaIni.value = horaInicial;
        tempo.value = 60;
        }
        
        

        // Calcula hora final
        let [h, m] = horaInicial.split(':').map(Number);
        let totalMin = h * 60 + m + parseInt(tempo.value, 10);
        let hFim = Math.floor(totalMin / 60);
        let mFim = totalMin % 60;
        if (hFim > 23) hFim = 23, mFim = 59;
        horaFim.value = String(hFim).padStart(2, '0') + ':' + String(mFim).padStart(2, '0');

        popularSelectProfissionalBl(idProf);




      }, 0);
    });
  }
}

function removerBloqueiosVisuais() {
  document.querySelectorAll('.bloqueio-virtual').forEach(el => el.remove());
}






function getTodosIdsProfissionais() {
  return Array.from(document.querySelectorAll('#easy-table th[data-id-profissional]'))
    .map(th => th.getAttribute('data-id-profissional'))
    .filter(id => !!id && id !== "0"); // ignora vazios e o próprio zero
}




  const inputIni   = document.getElementById('bl-h-ini');
  const inputTempo = document.getElementById('bl-h-tempo');
  const inputFim   = document.getElementById('bl-h-fim');

  ['input', 'blur'].forEach(eventType => {
  if (inputIni)
    inputIni.addEventListener(eventType, () => atualizarCampos('ini'));
  if (inputTempo)
    inputTempo.addEventListener(eventType, () => atualizarCampos('tempo'));
  if (inputFim)
    inputFim.addEventListener(eventType, () => atualizarCampos('fim'));
  });



function atualizarCampos(quemAlterou) {
  

  if (bloqueioAtualizacao) return;
  bloqueioAtualizacao = true;

  // Aplica máscara de hora e tempo
  maskHora(inputIni);
  maskHora(inputFim);
  maskTempo(inputTempo);

  const horaIni = inputIni.value;
  const horaFim = inputFim.value;
  const tempo   = parseInt(inputTempo.value, 10);

    if (quemAlterou === 'fim') {
    if (horaIni.length === 5) {
      // Se tem hora início, calcula tempo
      let minIni = toMinutes(horaIni);
      let minFim = toMinutes(horaFim);
      inputTempo.value = minFim - minIni > 0 ? minFim - minIni : 0;
    } else if (!isNaN(tempo) && tempo > 0) {
      // Se tem tempo, calcula hora início
      let minFim = toMinutes(horaFim);
      let minIni = minFim - tempo;
      inputIni.value = fromMinutes(minIni);
    }
    // Se não tem nenhum, não faz nada
  } else if (quemAlterou === 'tempo') {
    if (horaIni.length === 5) {
      // Se tem hora início, calcula hora fim
      let minIni = toMinutes(horaIni);
      inputFim.value = fromMinutes(minIni + tempo);
    } else if (horaFim.length === 5) {
      // Se tem hora fim, calcula hora início
      let minFim = toMinutes(horaFim);
      inputIni.value = fromMinutes(minFim - tempo);
    }
    // Se não tem nenhum, não faz nada
  } else if (quemAlterou === 'ini') {
    if (!isNaN(tempo) && tempo > 0) {
      // Se tem tempo, calcula hora fim
      let minIni = toMinutes(horaIni);
      inputFim.value = fromMinutes(minIni + tempo);
    } else if (horaFim.length === 5) {
      // Se tem hora fim, calcula tempo
      let minIni = toMinutes(horaIni);
      let minFim = toMinutes(horaFim);
      inputTempo.value = minFim - minIni > 0 ? minFim - minIni : 0;
    }
    // Se não tem nenhum, não faz nada
  }
  atualizarTodosBloqueiosComCamposAtuais();
  bloqueioAtualizacao=false;
    
}


function atualizarTodosBloqueiosComCamposAtuais() {
  // Captura valores atuais dos campos
  bloqueiosVirtuais = [];
  const horaInicio = document.getElementById('bl-h-ini').value;
  const tempo = parseInt(document.getElementById('bl-h-tempo').value, 10);
  const data = dataCalend;
  const descricao = document.getElementById('descricao-bloqueio')?.value || '';

  let profs = [];

  // Descobre se é "Todos" ou um só
  const selectProf = document.getElementById('select-profissionalBl');
  if (selectProf.value === "0") {
    // Todos marcados
    profs = Array.from(document.querySelectorAll('.form-check-input[type="checkbox"][id^="check-prof-bl-"]:checked'))
      .map(cb => cb.dataset.idProf);
  } else {
    // Só o selecionado
    profs = [selectProf.value];
  }

  // Zera o array de bloqueios
  bloqueiosVirtuais = [];

  // Adiciona um bloqueio para cada profissional selecionado
  profs.forEach(idProf => {
    adicionarBloqueio({
      idProfissional: idProf,
      data,
      horaInicio,
      tempo,
      descricao
    });
  });
}




document.getElementById('select-profissionalBl').addEventListener('change', function(event) {
  const selectProf = event.target;
  const idProf = selectProf.value;
  const fotoContainer = document.getElementById('foto-prof-container');
  fotoContainer.innerHTML = '';

  // 1. Limpa array e visual dos bloqueios (zera tudo)
  bloqueiosVirtuais = [];
  
  const horaInicio = document.getElementById('bl-h-ini').value;
  const tempo = parseInt(document.getElementById('bl-h-tempo').value, 10);
  const data = dataCalend; // data do calendário já disponível no escopo
  const descricao = document.getElementById('descricao-bloqueio')?.value || '';
  //atualizarCampos();
  // "TODOS" PROFISSIONAIS
  
  if (idProf === "0") {
    // Monta galeria de profissionais com checkbox

    console.log('entrando na função id prof = ZERO');
    const ths = document.querySelectorAll('#easy-table th[data-id-profissional]');
    ths.forEach(th => {
      const id = th.getAttribute('data-id-profissional');
      const nome = th.getAttribute('data-nome-agenda');
      const img = th.querySelector('img');
      if (img) {
        const div = document.createElement('div');
        div.className = 'text-center mb-1 d-inline-block';
        div.style.width = '50px';
        div.style.height = '60px';

        // Monta card
        div.innerHTML = `
        <div class="prof-bl-card">
          <div class="prof-bl-img-wrapper">
            <img src="${img.src}" alt="Foto" class="prof-bl-img">
            <input class="form-check-input prof-bl-checkbox" type="checkbox" id="check-prof-bl-${id}" data-id-prof="${id}" checked>
          </div>
          <div class="prof-bl-nome">${nome}</div>
        </div>
                `;
        fotoContainer.appendChild(div);

        // Adiciona bloqueio para cada profissional marcado
        adicionarBloqueio({
          idProfissional: id,
          data,
          horaInicio,
          tempo,
          descricao
        });

        // Listener do checkbox individual
        div.querySelector(`#check-prof-bl-${id}`).addEventListener('change', function() {
          if (this.checked) {
            adicionarBloqueio({
              idProfissional: id,
              data,
              horaInicio: document.getElementById('bl-h-ini').value,
              tempo: parseInt(document.getElementById('bl-h-tempo').value, 10),
              descricao: document.getElementById('descricao-bloqueio')?.value || ''
            });
            renderizarTodosBloqueios();
          } else {
            // Remove todos os bloqueios desse profissional do array
            bloqueiosVirtuais = bloqueiosVirtuais.filter(b => b.idProfissional !== id);
            renderizarTodosBloqueios();
          }
        });
      }
    });
    return;
  }

  // PROFISSIONAL ESPECÍFICO
  const th = document.querySelector(`#easy-table th[data-id-profissional="${idProf}"]`);
  if (th) {
    const img = th.querySelector('img');
    const nome = th.getAttribute('data-nome-agenda');
    if (img) {
      fotoContainer.innerHTML = `
        <div class="text-center">
          <img src="${img.src}" alt="Foto" class="prof-bl-img">
          <div style="font-size:11px;line-height:1">${nome}</div>
        </div>
      `;
      adicionarBloqueio({
        idProfissional: idProf,
        data,
        horaInicio,
        tempo,
        descricao
      });

    renderizarTodosBloqueios();
    }

  }
});




function fecharBloqueio() {
    if (!janelaAbertaBl)return;

    janelaBl.classList.remove('visivel', 'minimizada');
    
document.getElementById('descricao-bloqueio').value = '';
document.getElementById('titulo-bloqueio').value = '';
document.getElementById('div-Prof-bl').removeAttribute('hidden');
document.getElementById('coluna-data-bloqueio').removeAttribute('hidden');
document.getElementById('id-bloq-car').value='';


    // Espera a transição acabar antes de esconder de vez
    setTimeout(() => {
      //limparJanelaBl();
        janelaBl.setAttribute('hidden', true);
         
    }, 400); // mesmo tempo do transition

     removerBloqueiosVisuais()
    janelaAbertaBl=false;
      
}





flatpickr("#periodo-bloqueio", {
  mode: "range",
  dateFormat: "Y-m-d",
  locale: "pt",
  altInput: true,
  altFormat: "d/m/Y",
  onChange: function(selectedDates, dateStr, instance) {
    const altInput = instance.altInput;
    const rowSemana = document.getElementById('linha-semana-block');

    if (selectedDates.length === 1) {
      altInput.style.width='110px';
      rowSemana.setAttribute('hidden','true');
      marcarDiasSemanaNoIntervalo(selectedDates[0], null);
    } else if (selectedDates.length === 2) {
      const dia1 = selectedDates[0];
      const dia2 = selectedDates[1];
      if (
        dia1.getFullYear() === dia2.getFullYear() &&
        dia1.getMonth() === dia2.getMonth() &&
        dia1.getDate() === dia2.getDate()
      ) {
        altInput.style.width='110px';
        rowSemana.setAttribute('hidden','true');
        marcarDiasSemanaNoIntervalo(dia1, null);
      } else {
        altInput.style.width='210px';
        rowSemana.removeAttribute('hidden');
        marcarDiasSemanaNoIntervalo(dia1, dia2);
      }
    }
  }
});

function marcarDiasSemanaNoIntervalo(dataInicio, dataFim) {
  const diasIds = [
    'chk-bl-diaDom', // 0
    'chk-bl-diaSeg', // 1
    'chk-bl-diaTer', // 2
    'chk-bl-diaQua', // 3
    'chk-bl-diaQui', // 4
    'chk-bl-diaSex', // 5
    'chk-bl-diaSab'  // 6
  ];

  // Primeiro, desmarca e desabilita todos
  diasIds.forEach(id => {
    const el = document.getElementById(id);
    el.checked = false;
    el.disabled = true;
  });

  // Se só uma data, já retorna
  if (!dataFim || dataInicio.getTime() === dataFim.getTime()) return;

  // Calcula diferença em dias
  const diffDias = Math.round((dataFim - dataInicio) / (1000 * 60 * 60 * 24)) + 1;

  if (diffDias >= 7) {
    // Marca e habilita todos
    diasIds.forEach(id => {
      const el = document.getElementById(id);
      el.checked = true;
      el.disabled = false;
    });
    return;
  }

  // Marca e habilita apenas os dias do intervalo
  let dataTemp = new Date(dataInicio);
  for (let i = 0; i < diffDias; i++) {
    const diaSemana = dataTemp.getDay(); // 0=Dom, 1=Seg, ...
    const el = document.getElementById(diasIds[diaSemana]);
    el.checked = true;
    el.disabled = false;
    dataTemp.setDate(dataTemp.getDate() + 1);
  }
}



document.getElementById('chk-bl-dia').addEventListener('change', function() {
  
  
  if (this.checked) {
    // Guarda valores antigos
    valoresAntigos.ini = inputIni.value;
    valoresAntigos.fim = inputFim.value;
    valoresAntigos.tempo = inputTempo.value;

    // Busca menor e maior hora
    const { menor, maior } = getMenorEMaiorHoraColuna();
    inputIni.value = menor;
    inputFim.value = maior;

    // Calcula tempo em minutos
    const [h1, m1] = menor.split(':').map(Number);
    const [h2, m2] = maior.split(':').map(Number);
    const tempoMin = (h2 * 60 + m2) - (h1 * 60 + m1);
    inputTempo.value = tempoMin;

    // Bloqueia os campos
    inputIni.disabled = true;
    inputFim.disabled = true;
    inputTempo.disabled = true;

    // (Opcional) dispara atualização/calculo de bloqueio automático:
    

  } else {
    // Restaura valores antigos


    // Desbloqueia os campos
    inputIni.disabled = false;
    inputFim.disabled = false;
    inputTempo.disabled = false;
    inputIni.value = valoresAntigos.ini;
    inputFim.value = valoresAntigos.fim;
    inputTempo.value = valoresAntigos.tempo;



    // (Opcional) dispara atualização/calculo de bloqueio automático:
   
  }


atualizarCampos('ini');

});

function maskTempo(input) {
  // Só permite números inteiros positivos (máximo 3 dígitos, pode ajustar)
  input.value = input.value.replace(/\D/g, '').slice(0, 3);
}

function toMinutes(hora) {
  const [h, m] = hora.split(':').map(Number);
  return h * 60 + m;
}

function fromMinutes(minutos) {
  let h = Math.floor(minutos / 60);
  let m = minutos % 60;
  if (h > 23) h = 23, m = 59; // limita até 23:59
  if (h < 0) h = 0, m = 0;
  return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
}


function popularSelectProfissionalBl(idProf) {
  const select = document.getElementById('select-profissionalBl');
  const ths = document.querySelectorAll('#easy-table th[data-nome-agenda][data-id-profissional]');
  console.log('id do profissional select bloquio: ', idProf)
  // Limpa opções antigas
  select.innerHTML = '';
      const option1 = document.createElement('option');
      option1.value = '0';
      option1.textContent = 'Todos';
      select.appendChild(option1);


  ths.forEach(th => {
    const nome = th.getAttribute('data-nome-agenda');
    const id = th.getAttribute('data-id-profissional');
    if (nome && id) {
      const option = document.createElement('option');
      option.value = id;
      option.textContent = nome;
    if (idProf===id){
        option.selected = true;
    }

      select.appendChild(option);

       
 
    }
  });

        select.dispatchEvent(new Event('change'));
}



document.getElementById('janela-bloqueios').addEventListener('mousedown',function(event){

      janela.classList.remove('selectedJan');
      janelaBl.classList.add('selectedJan');
});

document.getElementById('janela-bloqueios').addEventListener('click',function(event){

            const btnFechar = event.target.closest('#btn-fechar-janelaBl');
            const btnCancelar= event.target.closest('#btn-cancelar-janelaBl');
            const btnMinimizar =event.target.closest('#btn-minimizar-janelaBl');

            if (btnCancelar || btnFechar){
                fecharBloqueio();
            }
            if (btnMinimizar){
                minimizarBloqueio(); 
           }

});

function minimizarBloqueio() {
  console.log('minimizando Janela');
  const janBl = document.getElementById('janela-bloqueios');
  const foiMinimizada = janBl.classList.toggle('minimizada');
  janelaMinimizadaBl = foiMinimizada;
}

function getMenorEMaiorHoraColuna() {
  // Assumindo: todas as tds da primeira coluna têm data-hora_agenda
  const tds = Array.from(document.querySelectorAll('#easy-table td.agenda-easy-td-horario'));
  if (!tds.length) return { menor: "00:00", maior: "23:59" };

  const horas = tds.map(td => td.getAttribute('data-hora_agenda'));
  horas.sort(); // ordena por ordem de string ("HH:MM" funciona)

  return {
    menor: horas[0],
    maior: horas[horas.length - 1]
  };
}


