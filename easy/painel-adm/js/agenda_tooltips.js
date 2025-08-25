document.addEventListener('DOMContentLoaded', function() {
    var tooltipAg = document.getElementById('tooltip-ag');
    var tooltipProf = document.getElementById('tooltip-prof');
    var isMenuVisible = false;
   
   
    document.body.addEventListener('mouseover', function(event) {
       var agendamento = event.target.closest('.agendamento');
       var agendaContainer = event.target.closest('.agenda-container');

        if (agendamento && agendaContainer) {


            const menu = document.getElementById('custom-menu');
            if (getComputedStyle(menu).display === 'block') {
                return;
            }
            var observacoes = agendamento.getAttribute('data-serv-observacoes');
            var cliente = agendamento.getAttribute('data-serv-cliente');
            var dataAgenda = agendamento.getAttribute('data-serv-dataAgenda');
            var servico = agendamento.getAttribute('data-serv-servico');
            var hora = agendamento.getAttribute('data-serv-hora');
            var telefone = agendamento.getAttribute('data-serv-telefone');
            var status = agendamento.getAttribute('data-serv-status');
            var fotoCliente = agendamento.getAttribute('data-foto_cliente')

            var dataFormatada = new Date(dataAgenda).toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            var horaFormatada = hora.substring(0, 5);

            var statusClass = 'statusTooltip status' + status.replace(/\s+/g, ''); // Remove espaços do status
            if (status == 'Em Atendimento'){
            statusClass = 'statusTooltip statusAtendimento';
            }
            if (status =='Atendimento Concluido'){
                statusClass = 'statusTooltip statusConcluido';
            }

            if (fotoCliente){
            var urlFoto = `../${pastaFiles}/img/clientes/${fotoCliente}`; // ajuste se a pasta/URL for diferente]
            }else{
                var urlFoto = `../img/sem-foto.svg`;
            }

            var tooltipContentAg = `
            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                <img src="${urlFoto}" alt="Foto do Cliente" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 1px solid #eee;">
                <span style="font-weight: bold; font-size: 16px;">${cliente}</span>
            </div>
            <p><b>Data de Agendamento:</b> ${dataFormatada}</p>
            <p><b>Hora:</b> ${horaFormatada}</p>
            <p><b>Serviço:</b> ${servico}</p>   
            <p><b>Telefone:</b> ${telefone}</p>
            <p><b>Status:</b> <span class="${statusClass}">${status}</span></p>
            <p><b>Observações:</b> ${observacoes}</p>
            `;

            tooltipAg.innerHTML = tooltipContentAg;
            
            var posX = event.pageX - 80;
            var posY = event.pageY - 230; // Posiciona um pouco abaixo do cursor
            tooltipAg.style.left = posX + 'px';
            tooltipAg.style.top = posY + 'px';
            tooltipAg.style.display = 'block';

            setTimeout(function() {
                tooltipAg.style.opacity = '1';
            }, 30);
        }


        var profissional = event.target.closest('.agenda-easy-th:not(:first-child');
        
        if (profissional) {
            const menu = document.getElementById('menu-prof');
            if (getComputedStyle(menu).display === 'block') {
                return;
            }
               
            var nome =  profissional.getAttribute('data-nome');
            var telefoneProf =  profissional.getAttribute('data-telefone');
            var especialidade =  profissional.getAttribute('data-especialidade');
            var idcontrato =  profissional.getAttribute('data-contrato');
            var descricao = profissional.getAttribute('data-descricao');

            var entrada =  profissional.getAttribute('data-entrada');
            var saida =  profissional.getAttribute('data-saida');
            let entradaSaida='';

            if (entrada=='00:00'){
                entradaSaida = '<p><b>FOLGA</b><p>';
            }else{
                entradaSaida = '<p><b>Entrada as: </b>'+ entrada + '</p> <p><b>Saída as: </b>'+ saida +'</p>';
            }
            

            var tooltipContentProf = `
                <p><b>Profissional:</b> ${nome}</p>
                <p><b>Telefone:</b> ${telefoneProf}</p>
                <p><b>Especialidade:</b> ${especialidade}</p>
                <p><b></b> ${descricao}</p>
                ${entradaSaida}
            `;

            tooltipProf.innerHTML = tooltipContentProf;
            
            var posX = event.pageX - 80;
            var posY = event.pageY - 160;
            tooltipProf.style.left = posX + 'px';
            tooltipProf.style.top = posY + 'px';
            tooltipProf.style.display = 'block';

            setTimeout(function() {
                tooltipProf.style.opacity = '1';
            }, 30);

        }

        var celula = event.target.closest('.celula');   
        if(celula) {
            var linha = celula.closest('tr'); // sobe até a linha
            if (linha) {
                var primeiraTd = linha.querySelector('td'); // pega a primeira td da linha
                if (primeiraTd) {
                    if(!isMenuVisible){
                        primeiraTd.classList.add('celula-hover');
                        celula.classList.add('celula-hover');
                    }
                }
            }
        }

        var celHorario = event.target.closest('.agenda-easy-td-horario');
        if(celHorario){
            var linha = celHorario.closest('tr');
            if(!isMenuVisible){
                 linha.classList.add('celula-hover');
                 //celula.classList.add('celula-hover');
            }


        }

    });
    

    document.body.addEventListener('mouseout', function(event) {
        
        if (tooltipAg){
        tooltipAg.style.opacity = '0';
        // Espera a transição terminar antes de definir display: none
        setTimeout(function() {
            if (tooltipAg.style.opacity === '0') {
                tooltipAg.style.display = 'none';
            }
        }, 300); // Este valor deve corresponder ao tempo da transição CSS

        }

        if (tooltipProf) {
        tooltipProf.style.opacity = '0';
        // Espera a transição terminar antes de definir display: none
        setTimeout(function() {
            if (tooltipProf.style.opacity === '0') {
                tooltipProf.style.display = 'none';
            }
        }, 300); // Este valor deve corresponder ao tempo da transição 
        }

        var celula = event.target.closest('.celula');   
        if(celula) {

            
            var linha = celula.closest('tr'); // sobe até a linha
            if (linha) {
                var primeiraTd = linha.querySelector('td'); // pega a primeira td da linha
                if (primeiraTd) {
                    if (!isMenuVisible){
                    primeiraTd.classList.remove('celula-hover');
                    celula.classList.remove('celula-hover');
                    }
                }
            }
        }


        var celHorario = event.target.closest('.agenda-easy-td-horario');
        if(celHorario){
            var linha = celHorario.closest('tr');
            
            if (!isMenuVisible){
                    linha.classList.remove('celula-hover');
                    //primeiraTd.classList.remove('celula-hover');
            }


        }




    });
    





});