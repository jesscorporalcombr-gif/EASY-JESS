  
  
  document.addEventListener('DOMContentLoaded', function() { // aguarda o carregamento do DOM
        
        const agendaContainer = document.getElementById('agenda-container');
        
        agendaContainer.addEventListener('dragstart', function(e) {
           
document.getElementById('tooltip-ag').style.display='none';

            if (e.target.classList.contains('agendamento')) {
               
                e.dataTransfer.setData('text', e.target.getAttribute('data-id_agendamento'));
                e.target.style.opacity = '0.5';
                //e.target.style.boxShadow = '-5px -5px 15px rgba(0, 200, 138, 0.5)';
                celulaOrigem = e.target.parentElement;
            }
            
            if (e.target.classList.contains('bloqueio')) {
               
                e.dataTransfer.setData('text', e.target.getAttribute('data-id_bloqueio'));
                e.target.style.opacity = '0.5';
                //e.target.style.boxShadow = '-5px -5px 15px rgba(0, 200, 138, 0.5)';
                celulaOrigem = e.target.parentElement;
            }






        });

        agendaContainer.addEventListener('dragend', function(e) {
           
            if (e.target.classList.contains('agendamento')) {
                e.target.style.opacity = ''; // Restaura a opacidade origina
                
                document.querySelectorAll('.celula-hover').forEach(celula => celula.classList.remove('celula-hover'));
            }
             if (e.target.classList.contains('bloqueio')) {
                e.target.style.opacity = ''; // Restaura a opacidade origina
                
                document.querySelectorAll('.celula-hover').forEach(celula => celula.classList.remove('celula-hover'));
            }


        });

        agendaContainer.addEventListener('dragover', function(e) {
            const celula = e.target.closest('.celula');
            if (celula) {
                e.preventDefault(); // Necessário para permitir o drop
                celula.classList.add('celula-hover'); // Adiciona efeito visual de hover
            }
        });

        agendaContainer.addEventListener('dragleave', function(e) {
            const celula = e.target.closest('.celula');
            if (celula) {
                celula.classList.remove('celula-hover'); // Remove efeito visual de hover quando o agendamento sai da célula
            }
        });


        
        agendaContainer.addEventListener('drop', function(e) {
            e.preventDefault(); // Previne o comportamento padrão
            const id_agendamento = e.dataTransfer.getData('text');
            const agendamento = agendaContainer.querySelector(`[data-id_agendamento="${id_agendamento}"]`);
            const bloqueio = agendaContainer.querySelector(`[data-id_bloqueio="${id_agendamento}"]`);

            let celulaDestino = e.target.closest('.celula');
            
 
            if (celulaDestino){
                
                if (agendamento) {

                        idNovoProf=celulaDestino.getAttribute('data-id_profissional');
                        idAntigoProf=agendamento.getAttribute('data-serv-id_profissional');
                        idServAg= agendamento.getAttribute('data-serv-id_servico');
                        nomeServicoAg = agendamento.getAttribute('data-serv-servico');
                        statusServico = agendamento.getAttribute('data-serv-status');

                        if (statusServico=='Finalizado'){
                                    alert(" Não é possível arrastar um agendamento Finalizado");
                                    celulaOrigem.appendChild(agendamento);
                                    return;
                        }

                        let executa = profissionalExecutaServico(idNovoProf,idServAg);
   
                                 if (executa) {

                                    celulaDestino.appendChild(agendamento);
                                   
                                    tipo='agendamento';
                                    realizarDrop(celulaDestino, agendamento, tipo);
                                   
                                    }else {
                                    alert("Este profissional não executa o serviço " + nomeServicoAg);
                                    celulaOrigem.appendChild(agendamento);
                                  
                                }
                }
                if (bloqueio) {
                     
                        celulaDestino.appendChild(bloqueio);
                        tipo='bloqueio';
                        realizarDrop(celulaDestino, bloqueio, tipo);
                        
                }
               
            }
        }); 
                                
    
        function realizarDrop(celulaDestino, agendamento, tipo) {
            let id_agendamento='';
            if (tipo=='agendamento'){
                id_agendamento = agendamento.getAttribute('data-id_agendamento');
            }
            if (tipo=='bloqueio'){
                id_agendamento = agendamento.getAttribute('data-id_bloqueio');
            }

            const id_profissional = celulaDestino.getAttribute('data-id_profissional');
            const profissional = celulaDestino.getAttribute('data-profissional');
            const hora_agenda = celulaDestino.getAttribute('data-hora_agenda')+ ':00';
            const tempoAgenda = celulaDestino.getAttribute('data-tempo-min');
            const horaFim_agenda = calculaHoraFim(hora_agenda, tempoAgenda);
            const formData = new FormData();// Prepara os dados a serem enviados
            formData.append('id_agendamento', id_agendamento);
            formData.append('id_profissional', id_profissional);
            formData.append('profissional', profissional);
            formData.append('hora_agenda', hora_agenda);
            formData.append('hora_fim', horaFim_agenda);

            // Realiza a requisição para o script PHP
            if (tipo=='agendamento'){
                    
                
                    fetch('agenda_atendimentos/update-agendamento.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // A atualização foi bem-sucedida, mova o agendamento para a nova célula
                        trocaDia=false;
                        efeitos=false;
                        
                            carregarAgenda(dataCalend, agCancelados); // Suponho que 'dataCalend' seja uma variável definida em algum lugar doscript

                            // ... (código para mover o agendamento)
                        } else {
                            // Houve um erro ao atualizar o banco de dados
                            alert('Erro ao atualizar o agendamento: ' + (data.error || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        // Houve um erro na requisição
                        console.error('Erro na atualização:', error);
                    });
                    
            }
            if (tipo=='bloqueio'){
                    fetch('agenda_atendimentos/grava_arrasta_bloqueio.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.sucesso) {
                            // A atualização foi bem-sucedida, mova o agendamento para a nova célula
                        trocaDia=false;
                        efeitos=false;
                        
                            carregarAgenda(dataCalend, agCancelados); // Suponho que 'dataCalend' seja uma variável definida em algum lugar doscript

                            // ... (código para mover o agendamento)
                        } else {
                            // Houve um erro ao atualizar o banco de dados
                            alert('Erro ao atualizar o agendamento: ' + (data.error || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        // Houve um erro na requisição
                        console.error('Erro na atualização:', error);
                    });
                    
            }






            // Limpa o efeito de hover da célula de destino
            celulaDestino.classList.remove('celula-hover');
        } 



    });



function profissionalExecutaServico(idProfissional, idServico) {
  const prof = profServData[idProfissional];
  if (!prof || !Array.isArray(prof.servicos)) return false;
  return prof.servicos.some(s => s.id_servico == idServico);
}





    

//=============================ARRASTAR A JANELA============================================
 const janela = document.getElementById('janela-agendamentos');
 const header = document.getElementById('janela-header');

  let isDragging = false;
  let offsetX = 0;
  let offsetY = 0;

    header.addEventListener('mousedown', function (e) {
    isDragging = true;
    const rect = janela.getBoundingClientRect();
    offsetX = e.clientX - rect.left;
    offsetY = e.clientY - rect.top;
    //janela.style.position = 'absolute';
    //janela.style.zIndex = 9999;
     });

  document.addEventListener('mousemove', function (e) {
    if (!isDragging) return;
    janela.style.left = (e.clientX - offsetX) + 'px';
    janela.style.top = (e.clientY - offsetY) + 'px';
  });

  document.addEventListener('mouseup', function () {
    isDragging = false;





  });



  //=============================ARRASTAR A JANELA============================================
 const janelaBl = document.getElementById('janela-bloqueios');
 const headerBl = document.getElementById('bloqueios-header');

  let isDraggingBl = false;
  let offsetXBl = 0;
  let offsetYBl = 0;

    headerBl.addEventListener('mousedown', function (f) {
    isDraggingBl = true;
    const rectBl = janelaBl.getBoundingClientRect();
    offsetXBl =f.clientX - rectBl.left;
    offsetYBl = f.clientY - rectBl.top;
    //janela.style.position = 'absolute';
    //janela.style.zIndex = 9999;
     });

  document.addEventListener('mousemove', function (f) {
    if (!isDraggingBl) return;
    janelaBl.style.left = (f.clientX - offsetXBl) + 'px';
    janelaBl.style.top = (f.clientY - offsetYBl) + 'px';
  });

  document.addEventListener('mouseup', function () {
    isDraggingBl = false;

  });


