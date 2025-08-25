//let usuarios=[]; //variavem para receber o array comos profissionais da agenda do dia
   
    //Função que pega e transforma para js os dados dos profissionaisl vindos junto com a agenda no input hidden usuarios-data
    //let dataCalend = '';
   
  //  function carregarDadosUsuarios() {
    //const usuariosJson = document.getElementById('usuarios-data').value;
      //  if(usuariosJson){
        //usuarios = JSON.parse(usuariosJson);
        //}
    //}

 let agCancelados = false;

 let efeitos= true;
    //função que é acionada ao clicar no botão mostrar cancelados 
    function mostrarAgCancelados(){
        var botao = document.getElementById('botaoCancelados');
        if (agCancelados==false){
                
                agCancelados=true;
                document.getElementById('botaoCancelados').className = 'btn btn-outline-secondary';
                document.getElementById('icoBtCancelados').className = 'bi bi-eye-fill';
                
                
            } else {
                agCancelados=false;
                document.getElementById('botaoCancelados').className = 'btn btn-secondary';
                document.getElementById('icoBtCancelados').className = 'bi bi-eye-slash';
            }
            trocaDia=false;
            carregarAgenda(dataCalend, agCancelados);
    }

console.log('carregando a agenda');
    //função que chama o carregamento da agenda      

    function carregarAgenda(data, mostrarCancelados) {
       

            //buscarAgendamentos(data); // aquie já traz todos os agendamentos do dia 

           // $("#tabelaAgendamentos").attr('hidden', true);
           // $("#agenda-container").removeAttr('hidden');
           // $("#agTabShow").removeClass('active').find('i').removeClass('bi-calendar2-week').addClass('bi-card-list');
            
            dataCalend = data; // define e data do calendario selecionada
            //agCancelados = mostrarCancelados;
             const dados = new URLSearchParams({ //monta o array para a url
            data: data,
            mostrarCancelados: mostrarCancelados
            // Adicionar mais variáveis conforme necessário
        });


        fetch(`agenda_atendimentos/agenda2.php?${dados.toString()}`)
        .then(response => response.text()) // recebe a resposta da agenda
        .then(htmlAgenda => {
            
           ocultarElemento("#cards-container");
            // Retorna ao último estado
            if (ultimoEstado === "agenda") {
                mostrarElemento("#agenda-container");
                ocultarElemento("#tabelaAgendamentos");
            } else if (ultimoEstado === "tabela") {
                mostrarElemento("#tabelaAgendamentos");
                ocultarElemento("#agenda-container");
            }
            
            const $c = $('#agenda-container');

                if (trocaDia && efeitos==true){
                        $c.finish()
                        .css({
                            position: 'relative',
                            left: '-100px',      // começa 100px à esquerda
                            opacity: 0,          // invisível
                            filter: 'blur(5px)'  // 5px de desfoque
                        })
                        .html(htmlAgenda)
                        // Anima left → 0, opacity → 1 e, via step, blur → 0
                        .animate(
                            { left: '0px', opacity: 1 },
                            {
                            duration: 600,
                            easing: 'swing',
                            step(now, fx) {
                                // Quando estiver animando a opacidade, reduza o blur
                                if (fx.prop === 'opacity') {
                                const blurVal = (1 - now) * 5; 
                                $c.css('filter', `blur(${blurVal}px)`);
                                }
                            },
                            complete() {
                                // Limpa o filtro pra não deixar estilos residuais
                                $c.css('filter', '');
                            }
                            }
                        );
                } else if (efeitos==true){
                    
                    
                    efeito5($c, htmlAgenda);
                    
                    
                    //$c.html(htmlAgenda).css({
                      //      position: '',
                        //    left: '',
                          //  opacity: '',
                            //filter: ''
                       // });
                }else if(!efeitos){

                $c.html(htmlAgenda)

                }

                atualizarTabela($('#searchAgenda').val());

                fecharBloqueio();
                //fecharAgendamento();


                if(dataCalend==dataHoje){
                    initTimeIndicator();   
                }
                
                updateProfissionais();
                //carregarDadosUsuarios();
                console.log('a janela está aberta:', janelaAberta)

            if (janelaAberta){
                updateAllVirtualAppointments();
            }
            
            efeitos=true;


        });
    }


   //Função para lidar com mudanças no input do calendário
   function onCalendarioChange() {
           var calendario = document.getElementById('calendario');
           agCancelados=false;

            
            if (calendario) {
               calendario.addEventListener('change', function() {
                    dataCalend = this.value;
                   trocaDia=true;
                    carregarAgenda(dataCalend, agCancelados); 
                   
                    // Chama carregarAgenda c a nova data
                });
            }
    }


function marcarHojeNoCalendario() {
    // Remove o atributo de todos os dias
    document.querySelectorAll('#meuCalendario .vc-date[data-vc-date-selected]')
      .forEach(el => el.removeAttribute('data-vc-date-selected'));
    // Marca o dia de hoje
    const divHoje = document.querySelector('#meuCalendario .vc-date[data-vc-date-today]');
    if (divHoje) {
        divHoje.setAttribute('data-vc-date-selected', '');
        // Atualiza o texto do campo extenso (opcional)
        const btnHoje = divHoje.querySelector('.vc-date__btn');
        const dataHojeExt = btnHoje ? btnHoje.getAttribute('aria-label') : '';
        document.getElementById('data-extenso-agenda').innerText = 'Hoje - ' + dataHojeExt;
    }
}

        document.addEventListener('click', function(event) {
            // só prossegue se clicou num elemento (ou filho) com a classe .vc-date__btn
            const btn = event.target.closest('.vc-date__btn');
            const divBotaoData= event.target.closest('.vc-date');
            
             const divDataCalend =document.getElementById('data-extenso-agenda');
            if (!btn) return;

            // encontra a div .vc-date que tenha o atributo data-vc-date
            const dateDiv = btn.closest('.vc-date[data-vc-date]');
            if (!dateDiv) return;

            dataCalendario = event.target.closest('.vc-date').getAttribute('data-vc-date');
            
            
            
            let complemento =''
            if (dataHoje==dataCalendario){
                complemento='Hoje - ';
            }
             
            divDataCalend.textContent = complemento + btn.getAttribute('aria-label');
            // lê direto o valor de data-vc-date
            dataCalend = dateDiv.getAttribute('data-vc-date');
            // ou: const dataCalend = dateDiv.dataset.vcDate;
            trocaDia=true;
            carregarAgenda(dataCalend, agCancelados);
            divBotaoData.setAttribute('data-vc-date-selected','');
            
        });



    //função carregada quando o DOM está pronto para chamar a agenda do dia
   // document.addEventListener('DOMContentLoaded', function () {    
        var dataHoje = new Date().toISOString().split('T')[0];// Obtém a data de hoje no formato Y-m-d
        //padrão para não mostrar os cancelados no carregamento
      trocaDia=true;
        carregarAgenda(dataHoje, agCancelados);  // Chama carregarAgenda passando a data de hoje como argumento
       
   // });
