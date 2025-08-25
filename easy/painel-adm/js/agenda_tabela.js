var trocaDia=false;
let tipoAgendamento = [];
    
 
       //===================================TABELA AGENDA =========================================


        function atualizarTabela(text) {
            //const dadosJson = $('#agendamentosDoDia').val(); // Pega os dados do input
            //const agendamentos = JSON.parse(dadosJson); // Converte de JSON para objeto JavaScript
            
            agendamentos.sort((a, b) => {
                if(colunaAtual == "" ){ colunaAtual = 'hora'; }
                let valA = (a[colunaAtual] ?? '').toString().toLowerCase();
                let valB = (b[colunaAtual] ?? '').toString().toLowerCase();

                if (ordemAtual === 'asc') {
                    return valA.localeCompare(valB);
                } else {
                    return valB.localeCompare(valA);
                }
            });

            let $tbody = $("#agendaBody");
            $tbody.empty(); // Limpa a tabela antes de adicionar novos dados
          
              
            $.each(agendamentos, function(i, agendamento) {
               if (
                    (!text ||
                    (agendamento.nome_cliente && agendamento.nome_cliente.toLowerCase().includes(text.toLowerCase())) ||
                    (agendamento.profissional_1 && agendamento.profissional_1.toLowerCase().includes(text.toLowerCase())))&&
                    !agendamento.bloqueio
                    ) {
                        var status=agendamento.status;
                        let statusEtiqueta = '';
                            var statusClass = 'statusTooltip status' + status.replace(/\s+/g, ''); // Remove espaços do status
                            
                            if (status == 'Em Atendimento'){
                            statusClass = 'statusTooltip statusAtendimento';
                            }
                            if (status =='Atendimento Concluido'){
                                statusClass = 'statusTooltip statusConcluido';
                            }


                            const quantidade = agendamento.quantidade;
                            
                            if(quantidade && quantidade>0){
                                statusEtiqueta = 'Pago';
                            }
                            if(quantidade && quantidade<1){
                                statusEtiqueta = 'Pendente';
                            }

                            if(!quantidade){
                                statusEtiqueta='Pendente';
                            }
                            
                            if(agendamento.status=='Finalizado'){
                                statusEtiqueta='Finalizado';
                            }
                            if(agendamento.status=='Faltou'){
                                statusEtiqueta='Faltou';
                            } 
                            if(agendamento.status=='NRealizado'){
                                statusEtiqueta='NRealizado';
                            }                                               
                            if(agendamento.status=='Cancelado'){
                                statusEtiqueta='Cancelado';
                            }

                            $tbody.append(
                                `<tr class="agendamento"
                                        data-id_agendamento=${agendamento.id} 
                                        data-serv-dataagenda=${agendamento.data}
                                        data-serv-hora=${agendamento.hora} 
                                        data-serv-cliente=${agendamento.nome_cliente}  
                                        data-sexo-cliente=${agendamento.sexo} 
                                        data-id-cliente=${agendamento.id_cliente} 
                                        data-serv-telefone=${agendamento.telefone_cliente}  
                                        data-serv-servico=${agendamento.servico}  
                                        data-serv-id_servico=${agendamento.id_servico}  
                                        data-tempo-min=${agendamento.tempo_min} 
                                        data-serv-status=${agendamento.status}  
                                        data-serv-id_profissional=${agendamento.id_profissional_1} 
                                        data-serv-nome_profissional=${agendamento.profissional_1}  
                                        data-serv-preco=${agendamento.preco} 
                                        data-foto_cliente=${agendamento.foto_cliente}  
                                        data-aniversario=${agendamento.aniversario}  
                                        data-serv-observacoes=${agendamento.observacoes} 
                                        data-serv-origem=${agendamento.origem_agendamento} 
                                        data-sala=${agendamento.sala} 
                                        data-equipamento=${agendamento.equipamento} 
                                        data-etiqueta=${statusEtiqueta} 
                                        >
                                    <td>${agendamento.hora.slice(0,5)}</td>
                                    <td>${agendamento.nome_cliente}</td>
                                    <td>${agendamento.servico}</td>
                                    <td>${agendamento.profissional_1}</td>
                                    <td>${agendamento.observacoes}</td>
                                    <td>
                                        <span class="${statusClass}">
                                            ${agendamento.status}
                                        </span>
                                    </td>
                                    <td>R$ ${parseFloat(agendamento.preco).toFixed(2)}</td>
                                    
                                    </tr>`
                            );
                }
            });

        }


        let ordemAtual = 'asc';
        let colunaAtual = '';
        let ultimoEstado = "agenda"; // agenda | tabela
        let cardAtivo = false;

        $('.sortable').on('click', function() {
            colunaAtual = $(this).data('coluna');
            ordemAtual = ordemAtual === 'asc' ? 'desc' : 'asc';
            atualizarTabela($('#searchAgenda').val());
        });

function mostrarElemento(seletor) {
    const el = $(seletor);
    
    // 1. remove o display:none, mas mantém invisível imediatamente
    el.css({ display: '', opacity: 0 });
    
    // 2. Força o navegador a reconhecer a mudança antes de iniciar o fade-in
    requestAnimationFrame(() => {
        el.removeClass('hidden').addClass('fadeIn');
        el.css('opacity', 1); // inicia o fade-in corretamente agora
    });
}

function ocultarElemento(seletor) {
    const el = $(seletor);
    
    // inicia fade-out
    el.removeClass('fadeIn').addClass('hidden');
    // remove do fluxo APÓS o fade-out completo
    el.css('display', 'none');
   
}


       
$("#agTabShow").click(function() {
    let isActive = $(this).hasClass('active');

    if (!isActive) {
        ultimoEstado = "tabela";
        $(this).addClass('active').find('i').removeClass('bi-card-list').addClass('bi-calendar2-week');
       
        ocultarElemento("#agenda-container");
        ocultarElemento("#cards-container");
        mostrarElemento("#tabelaAgendamentos");
    } else {
        ultimoEstado = "agenda";
        $(this).removeClass('active').find('i').removeClass('bi-calendar2-week').addClass('bi-card-list');
        ocultarElemento("#tabelaAgendamentos");
        ocultarElemento("#tabelaAgendamentos"); 
        mostrarElemento("#agenda-container");
    }
    cardAtivo = false;
});


$("#searchAgenda").on("input", function() {
    let searchText = $(this).val();
    atualizarTabela(searchText);
    ultimoEstado = "tabela";
    
    $("#agTabShow").addClass('active').find('i').removeClass('bi-card-list').addClass('bi-calendar2-week');
        if (cardAtivo) {
            ocultarElemento("#cards-container");
        }
        ocultarElemento("#agenda-container");
        mostrarElemento("#tabelaAgendamentos");
        

});



const cardContainer = document.getElementById('card-container');

document.addEventListener('click', function (e) {
    // Botão para renderizar o card
    if (e.target.closest('#btn-lembrete-agenda')) {
        if (!cardAtivo) {
            cardAtivo = true;
            // Esconde agenda e tabela
            mostrarElemento("#cards-container");
            renderCards(agendamentos);
            ocultarElemento("#tabelaAgendamentos");
            ocultarElemento("#agenda-container");
            // Exibe o card (ou monta ele)
            
            
        } else {
            cardAtivo = false;
            ocultarElemento("#cards-container");
            // Retorna ao último estado
            if (ultimoEstado === "agenda") {
                mostrarElemento("#agenda-container");
                ocultarElemento("#tabelaAgendamentos");
            } else if (ultimoEstado === "tabela") {
                mostrarElemento("#tabelaAgendamentos");
                ocultarElemento("#agenda-container");
            }
        }
    }
});
