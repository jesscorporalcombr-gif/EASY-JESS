// Escopo global: array de linhas adicionadas




// Configuração da agenda injetada via JSON em input hidden


let nextLineIndex = 1;


/**
 * Adiciona um registro ao array quando uma linha é criada.
 * Recebe o <tr> completo, cria um índice único e extrai valores dos campos.
 * @param {HTMLTableRowElement} tr - Elemento <tr> da linha adicionada
 */
function adicionarItemAoArray(tr) {
  // Gera índice único e armazena no tr
  const id = nextLineIndex++;
  tr.dataset.lineIndex = id;

  // Extrai valores dos campos da linha (mesmo que vazios)
  const data       = tr.querySelector('input[type="date"]').value;
  const idProf     = tr.querySelector('.select-profissional').value;
  const idServ     = tr.querySelector('.select-servico').value;
  const horaInicio = tr.querySelector('input[name$=".horaInicio"]').value;
  const tempo      = tr.querySelector('input[name$=".tempo"]').value;
  const horaFim    = tr.querySelector('input[name$=".horaFim"]').value;

  // Monta objeto e adiciona ao array
  linhasAgendamentos.push({
    id, data, idProfissional: idProf,
    idServico: idServ,
    horaInicio, tempo, horaFim
  });
  console.log('Array após adicionar:', linhasAgendamentos);
  updateVirtualAppointment(tr);
}

/**
 * Remove um registro do array quando uma linha é removida.
 * Recebe o <tr> correspondente.
 * @param {HTMLTableRowElement} tr - Elemento <tr> da linha removida
 */
function removerItemDoArray(tr) {
  const id = Number(tr.dataset.lineIndex);
  const idx = linhasAgendamentos.findIndex(item => item.id === id);
  if (idx !== -1) {
    linhasAgendamentos.splice(idx, 1);
  }
  // dispara o fade-out e remoção suave do bloco virtual
  removeVirtualAppointment(id);

  // por fim, remove a própria linha da tabela
  tr.remove();

  console.log('Array após remover:', linhasAgendamentos);
}





function updateLinhaArray(tr) {
  const id = Number(tr.dataset.lineIndex);
  const idx = linhasAgendamentos.findIndex(item => item.id === id);
  if (idx === -1) return;
  // Leia todos os valores de tr, monte um novo objeto
  const novo = {
    id,
    data: tr.querySelector('input[type="date"]').value,
    idProfissional: tr.querySelector('.select-profissional').value,
    idServico: tr.querySelector('.select-servico').value,
    horaInicio: tr.querySelector('input[name$=".horaInicio"]').value,
    tempo: tr.querySelector('input[name$=".tempo"]').value,
    horaFim: tr.querySelector('input[name$=".horaFim"]').value,
    // … qualquer outro campo
  };
  linhasAgendamentos[idx] = novo;
  console.log('Array após atualização:', linhasAgendamentos);

  updateVirtualAppointment(tr)
}




function attachFieldListeners(tr) {
  [
    tr.querySelector('.input-data-agendamento'),
    tr.querySelector('.select-profissional'),
    tr.querySelector('.select-servico'),
    tr.querySelector('input[name$=".horaInicio"]'),
    tr.querySelector('input[name$=".tempo"]'),
    tr.querySelector('input[name$=".horaFim"]')
  ].forEach(el => {
    if (!el) return;

    if (el.classList.contains('input-data-agendamento')) {
      // Se for o campo de data, ao mudar chama atualização de TODOS os blocos
      el.addEventListener('change', updateAllVirtualAppointments);
    } else {
      // Demais campos, atualiza apenas a linha e seu bloco
      el.addEventListener('change', () => {updateLinhaArray(tr)});
    }
  });
}


/**
 * updateVirtualAppointment(tr)
 * Cria ou atualiza o bloco virtual na agenda com base nos dados atuais da linha.
 * @param {HTMLTableRowElement} tr - Elemento <tr> da linha que disparou a atualização
 */
function updateVirtualAppointment(tr) {
  const dataTr = tr.querySelector('.input-data-agendamento').value;
  const lineIndex = Number(tr.dataset.lineIndex);
  // Remove bloco anterior desta linha, se existir
  removeVirtualAppointment(lineIndex);

  if (dataCalend != dataTr) return;

  // Extrai dados da linha
  const idProf     = tr.querySelector('.select-profissional').value;
  const horaInicio = tr.querySelector('input[name$=".horaInicio"]').value;
  const tempo      = parseFloat(tr.querySelector('input[name$=".tempo"]').value) || 0;
  if (!idProf || !horaInicio || tempo <= 0) return;

  // Calcula pixels por minuto
  const pixelsPerMin = alturaLinha / intervaloMin;
  // Converte horaInicio em minutos desde meia-noite
  const [h, m]    = horaInicio.split(':').map(Number);
  const startMin = h * 60 + m;
  // Posicionamento vertical relativo à primeira célula de horário
  const firstCell = document.querySelector('#easy-table td.agenda-easy-td-horario');
  const [h0, m0]  = firstCell.getAttribute('data-hora_agenda').split(':').map(Number);
  const baseMin   = h0 * 60 + m0;
  const deltaMin  = startMin - baseMin;
  //const offsetPx  = deltaMin * pixelsPerMin;

  // Altura do bloco
  const heightPx = tempo * pixelsPerMin;

  // Encontrar a célula de início
  //const parentTd = document.querySelector(
    //`.celula[data-id_profissional="${idProf}"][data-hora_agenda="${horaInicio}"]`
  //);

  const { celulaCorreta: parentTd, diferencaMin } = encontrarCelulaMaisProxima(idProf, horaInicio);
  
  if (!parentTd) return;
  
  parentTd.style.position = 'relative';
  parentTd.dataset.zIndexOriginal = parentTd.style.zIndex || '';
  parentTd.style.zIndex= '14000';
  // Cria o bloco virtual

  const offsetPx = diferencaMin * pixelsPerMin;

  console.log('a diferença mim é :, ', diferencaMin)

  const virtual = document.createElement('div');
  virtual.className = 'agendamento-virtual';
  virtual.dataset.lineIndex = lineIndex;

  // Conteúdo: cliente e serviço
  const servText = tr.querySelector('.select-servico option:checked')?.textContent || '';
  const cliText  = document.getElementById('janela_nome_cliente').value || '';
  virtual.innerHTML = `
    <p class="virtual-cliente">Cliente: ${cliText}</p>
    <p class="virtual-servico">${servText}</p>
  `;

  // Estilos de posicionamento e tamanho
  virtual.style.height = `${heightPx}px`;
  virtual.style.width  = '90%';
  virtual.style.top = `${offsetPx}px`;
  
  parentTd.appendChild(virtual);

  // Transição suave de entrada
  requestAnimationFrame(() => virtual.classList.add('visivel'));
}






function encontrarCelulaMaisProxima(idProf, horaInicio) {
  const [hora, minuto] = horaInicio.split(':').map(Number);
  const minutosInicio = hora * 60 + minuto;

  // Buscar todas as células do profissional e converter horários em minutos
  const celulas = [...document.querySelectorAll(`.celula[data-id_profissional="${idProf}"]`)];
  
  // Encontrar célula mais próxima sem ultrapassar o horário desejado
  let celulaCorreta = null;
  let menorDiferenca = Infinity;

  celulas.forEach(cel => {
    const horaCel = cel.getAttribute('data-hora_agenda');
    const [h, m] = horaCel.split(':').map(Number);
    const minutosCel = h * 60 + m;

    const diferenca = minutosInicio - minutosCel;

    if (diferenca >= 0 && diferenca < menorDiferenca) {
      menorDiferenca = diferenca;
      celulaCorreta = cel;
    }
  });

  return { celulaCorreta, diferencaMin: menorDiferenca };
}









/**
 * removeVirtualAppointment
 * Faz fade-out e remove o bloco virtual identificado pelo lineIndex
 */
function removeVirtualAppointment(lineIndex) {
  document.querySelectorAll(`.agendamento-virtual[data-line-index="${lineIndex}"]`).forEach(el => {
    const parentTd = el.parentElement;

      const originalZ = parentTd.dataset.zIndexOriginal;
      if (originalZ) {
        parentTd.style.zIndex = originalZ;
        delete parentTd.dataset.zIndexOriginal;
      } else {
        parentTd.style.zIndex = '';
      }


    el.classList.remove('visivel');
    el.addEventListener('transitionend', () => {
      el.remove();

      // Remove a classe z-alta se não restar nenhum bloco virtual dentro da célula



    }, { once: true });
  });
}



function updateAllVirtualAppointments() {
  document.querySelectorAll('#tabela-agendamentos-janela tr').forEach(tr => {
    updateVirtualAppointment(tr);
  });
  console.log('Todos blocos virtuais atualizados.');
}

