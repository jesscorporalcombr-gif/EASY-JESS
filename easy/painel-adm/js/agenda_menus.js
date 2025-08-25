


const menuAg = document.getElementById('custom-menu');
const menuProf = document.getElementById('menu-prof');
const menuCelula = document.getElementById('menu-celula');
const menuLinha = document.getElementById('menu-linha');
const menuBloqueio= document.getElementById('menu-bloqueio');



let menuSelected='';


let hideMenuTimeout = null;
var isMenuVisible = false;
let selectedAgendamentoId = null;

let selectedOptMenuProf = null; 
let selectedOptMenulinha = null;

let selectedOptMenuAgendamento = [];
let selectedOptMenuCelula = [];
let selectedOptMenuBloqueio = [];

let atualizarStatus = false;

const mOffsetX = 15;
const mOffsetY = 15;

function showMenu(x, y, menu) {
    clearTimeout(hideMenuTimeout);

    // Primeiro, exibe o menu fora da tela para calcular largura/altura corretamente
    menu.style.display = 'block';
    menu.style.opacity = '0'; // Oculta visualmente para não piscar
    menu.style.left = '-9999px';
    menu.style.top = '-9999px';

    // Força o browser a calcular tamanho
    const menuRect = menu.getBoundingClientRect();
    const menuWidth = menuRect.width;
    const menuHeight = menuRect.height;

    // Pega tamanho da tela
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    let left = x + mOffsetX;
    let top = y + mOffsetY;

    // Se o menu extrapolar a direita, abre para a esquerda
    if (left + menuWidth > viewportWidth) {
        left = x - menuWidth - mOffsetX;
        if (left < 0) left = 0; // garante que não fique negativo
    }

    // Se extrapolar embaixo, sobe o menu
    if (top + menuHeight > viewportHeight) {
        top = y - menuHeight - mOffsetY;
        if (top < 0) top = 0;
    }

    // Agora sim exibe corretamente
    menu.style.left = `${left}px`;
    menu.style.top = `${top}px`;
    menu.style.opacity = '1';

    isMenuVisible = true;
    hideMenuTimeout = setTimeout(() => hideMenuSmoothly(menu, false), 3000);
}



function hideMenuSmoothly(menu, all) {


 var mMenu = menu;

    if (all){
        const menus = document.querySelectorAll('.menu-agenda');

        menus.forEach(menu => {
            if (menu != mMenu) {
                menu.style.opacity = '0';
                
                setTimeout(() => {
                menu.style.display = 'none';
                }, 300);
           
            }
        
        });


     // Se essa variável controlar o estado geral do menu
    }else{
        mMenu.style.opacity = '0';
            
            setTimeout(() => {
            mMenu.style.display = 'none';
            }, 300);
            isMenuVisible = false;
            removerCelulaHover();
            //celula.classList.remove('celula-hover');
    }

}



function removerCelulaHover(){
if (celula){
            celula.classList.remove('celula-hover');
            const tr = celula.closest('tr');
                if (tr) {
                    const firstTd = tr.querySelector('.agenda-easy-td-horario');
                    if (firstTd) firstTd.classList.remove('celula-hover');
                }
            
            }
            if (linha) {
              
                const tr = linha.closest('tr');
                if (tr) {
                    
                    tr.classList.remove('celula-hover');
                    
                }
            }



}



let celula='';
let agendamento='';
let bloqueio = '';
let linha='';
// Captura clique direito no agendamento
document.addEventListener('contextmenu', function(e) {
removerCelulaHover();



  agendamento = e.target.closest('.agendamento');
  const profissional = e.target.closest('.agenda-easy-th');
  linha = e.target.closest('.agenda-easy-td-horario');
  celula = e.target.closest('.celula');
  bloqueio = e.target.closest('.bloqueio');


  if (agendamento) {
    e.preventDefault();
    menuSelected = menuAg;
    // Pega o ID do agendamento e guarda
    selectedAgendamentoId = agendamento.getAttribute('data-id_agendamento');
    const horaIni = agendamento.getAttribute('data-serv-hora').slice(0,5);
    const fHTempo = agendamento.getAttribute('data-tempo-min');
    const horaFini = calculaHoraFim(horaIni, fHTempo);
    const id_cliente_agendamento = agendamento.getAttribute('data-id-cliente');
    const statusAgAt = agendamento.getAttribute('data-serv-status');
    const descricaoAg = agendamento.getAttribute('data-serv-observacoes');
    document.getElementById('tooltip-ag').style.display='none';
    selectedOptMenuAgendamento=[
                                agendamento.getAttribute('data-id_agendamento'),
                                agendamento.getAttribute('data-serv-hora'),
                                horaIni,
                                horaFini,
                                id_cliente_agendamento,
                                statusAgAt,
                                agendamento,
                                descricaoAg
                                ]
    
    showMenu(e.pageX, e.pageY, menuSelected);
    hideMenuSmoothly(menuAg,true);
    

  }else if (bloqueio){

   
    e.preventDefault();
    selectedOptMenuBloqueio = [
                            bloqueio.getAttribute('data-id_bloqueio'),
                            bloqueio.getAttribute('data-serv-hora'),
                            bloqueio.getAttribute('data-serv-id_profissional'),
                            bloqueio.getAttribute('data-serv-observacoes'),
                            bloqueio.getAttribute('data-titulo-bloqueio'),
                            bloqueio.getAttribute('data-tempo-bloqueio')
                        ]; 
   
    menuSelected = menuBloqueio;
    showMenu(e.pageX, e.pageY, menuSelected);
    hideMenuSmoothly(menuBloqueio,true);

  } else if (profissional){
    document.getElementById('tooltip-prof').style.display='none';
    e.preventDefault();
    selectedOptMenuProf = [
                            profissional.getAttribute('data-contrato'),
                            profissional.getAttribute('data-telefone'),
                            profissional.getAttribute('data-id-profissional')
                        ];    //selectedProfissional = profissional.getAttribute('data-nome');;
    
    
    menuSelected = menuProf;
    showMenu(e.pageX, e.pageY, menuSelected);
    hideMenuSmoothly(menuProf,true);
} else if (linha){
    e.preventDefault();
    
    
    selectedOptMenulinha = linha.getAttribute('data-hora_agenda'),
    menuSelected = menuLinha;
    showMenu(e.pageX, e.pageY, menuSelected);
    hideMenuSmoothly(menuLinha,true);



} else if (celula){
    e.preventDefault();
    celula.classList.add('celula-hover');
    selectedOptMenuCelula = [
                            celula.getAttribute('data-id_profissional'),
                            celula.getAttribute('data-data_agenda'),
                            celula.getAttribute('data-hora_agenda'),
                        ]; 
   
    menuSelected = menuCelula;
    showMenu(e.pageX, e.pageY, menuSelected);
    hideMenuSmoothly(menuCelula,true);

  } else{
     hideMenuSmoothly('',true);
  }

});


document.addEventListener('click', function(e) {
    if (!e.target.closest('#custom-menu')) {
    hideMenuSmoothly('',true);
  } 
  if (!e.target.closest('#menu-prof')) {
    hideMenuSmoothly('',true);
  }
});

const menusAgenda = document.querySelectorAll('.menu-agenda');




menusAgenda.forEach(menu => {
      
    menu.addEventListener('mouseenter', function() {
    clearTimeout(hideMenuTimeout);
    
    });


    menu.addEventListener('mouseleave', function() {
    hideMenuTimeout = setTimeout(() => hideMenuSmoothly(menu,false), 3000);
    
    });

});








// Clique nas opções do menu do profissional
menuProf.addEventListener('click', function(e) {
  const menuItem = e.target.closest('.menu-item');
  const itemSelecionado = menuItem.getAttribute('data-option');

  if (menuItem && selectedOptMenuProf) {
    if (itemSelecionado == 'Mensagem' ){
       
        var foneProf = selectedOptMenuProf[1];
        const novaJanela = window.open(whatsapp(foneProf), '_blank', 'width=600,height=400');
        
        setTimeout(() => {
            if (novaJanela && !novaJanela.closed) novaJanela.close();
        }, 2000);
    } else if(itemSelecionado=="Editar"){
        var idContr = selectedOptMenuProf[0];

        abrirModal('modalProfissional',idContr, 'agenda');
    }else if(itemSelecionado=="Bloquear"){
        
        var idProfSel = selectedOptMenuProf[2];
       
        abrirBloqueios(idProfSel, '', '');
    }

   
  }
});




menuCelula.addEventListener('click', function(e) {
  const menuItem = e.target.closest('.menu-item');
  const itemSelecionado = menuItem.getAttribute('data-option');
  if (menuItem) {
    if (itemSelecionado=='novo-agendamento') {
            abrirAgendamentoCelula(celula);
    }
    if (itemSelecionado=='bloquear-horario'){
        var idProfSel = selectedOptMenuCelula[0];
        var dtHAgBl = selectedOptMenuCelula[2];
        abrirBloqueios(idProfSel, dtHAgBl, '');
    }
  }
});




menuBloqueio.addEventListener('click', function(e) {
  const menuItem = e.target.closest('.menu-item');
  const itemSelecionado = menuItem.getAttribute('data-option');
  if (menuItem) {
    if (itemSelecionado=='alterar-bloqueio') {
      const idProfBl = selectedOptMenuBloqueio[2];
      const dtHAgBl = selectedOptMenuBloqueio[1];
      const idBloq = selectedOptMenuBloqueio[0];

            abrirBloqueios(idProfBl, dtHAgBl, idBloq);
    } 
    if (itemSelecionado=='excluir') {
      const idProfBl = selectedOptMenuBloqueio[2];
      const dtHAgBl = selectedOptMenuBloqueio[1];
      const idBloq = selectedOptMenuBloqueio[0];

            excluirBloqueios(idBloq, '1');
    }


  }
});



menuLinha.addEventListener('click', function(e) {
  const menuItem = e.target.closest('.menu-item');
  const itemSelecionado = menuItem.getAttribute('data-option');
  if (menuItem) {
      if (itemSelecionado=='bloquear'){
          var idProfSel = 0;
          var dtHAgBl = selectedOptMenulinha;
          abrirBloqueios(idProfSel, dtHAgBl, '');
      }
  }
});


// Clique nas opções do menu de agendamento
menuAg.addEventListener('click', function(e) {
  const menuItem =e.target.closest('.menu-item');

    if (menuItem && selectedAgendamentoId) {
        const status = menuItem.getAttribute('data-status');
        const sala = menuItem.getAttribute('data-sala');
        const equipamento = menuItem.getAttribute('data-equipamento');
        const vender = menuItem.getAttribute('data-vender') 
        const mensagem = menuItem.getAttribute('data-id-mensagem')
    
        trocaDia=false;
        
        if (status){

          if (status=='Cancelado' || status =='Faltou' || status =='NRealizado'){
              
                  const modCancelInputId =  document.getElementById('idAgendamentoModCancel');
                  const modCancelInputStatus =  document.getElementById('statusAgendamentoModCancel');
                  const modCancelInputText =  document.getElementById('textAgendamentoModCancel')
                  modCancelInputId.value= selectedAgendamentoId;
                  modCancelInputStatus.value = status;
                  modCancelInputText.value = agendamento.getAttribute('data-serv-observacoes');
                  const idAgSelected = selectedAgendamentoId;
                fetch('agenda_atendimentos/consulta_atendimentos.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_agendamento=${encodeURIComponent(idAgSelected)}`
              })
                .then(response => response.json())
                .then(data => {

                  if (data.existe){

                    $('#modConfirmExc').modal('show');

                  }else{
                    $('#modCancel').modal('show');
                  }

                 })
                .catch(error => {
                  console.error('Erro na consulta de atendimento:', error);
                  // Aqui  pode exibir uma mensagem de erro na interface, se quiser
                });
              
                
            
            } else if(status =='Agendado' || status =='Confirmado' || status =='Aguardando'){
                      const idAgSelected = selectedAgendamentoId;
                       gravaAlteraAtendimento(idAgSelected, status, '', '', '');
                        return;
            }else if (status === 'Finalizado' || status === 'Atendimento' || status === 'Concluido') {
              const InputHoraIniAt = document.getElementById('row-hora_iniAt');
              const InputHoraFimAt = document.getElementById('row-hora_fimAt');
              const IInputHoraIniAt = document.getElementById('inputAtHIni');
              const IInputHoraFimAt = document.getElementById('inputAtHFim');
              const IHSMA = document.getElementById('IHinddenModAtAg');
              const idAgSelected = selectedAgendamentoId;
              const titModalAt = document.getElementById('headModAendimento');
              let status_antigo = selectedOptMenuAgendamento[5];
              const textoProntuario = document.getElementById('textoProntuario');

              fetch('agenda_atendimentos/consulta_atendimentos.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_agendamento=${encodeURIComponent(idAgSelected)}`
              })
                .then(response => response.json())
                .then(data => {
                  // Esconde ambos por padrão
                  InputHoraIniAt.style.display = 'none';
                  InputHoraFimAt.style.display = 'none';

                  // Limpa valores dos campos
                  InputHoraIniAt.value = '';
                  InputHoraFimAt.value = '';

                  // Modal do Bootstrap, pode adaptar se for outro framework
                  // $('#modCancel').modal('show'); // só mostrar depois de preparar tudo

                  // Decide o que mostrar/preencher
                  if (data.existe) {
                    // Se veio 'Em Atendimento'
                    if (status == 'Atendimento') {
                      InputHoraIniAt.style.display = '';
                      InputHoraFimAt.style.display = 'none';

                      if (data.hora_inicio && data.hora_inicio.slice(0,5) !='00:00') {
                        IInputHoraIniAt.value = data.hora_inicio.slice(0,5);
                      } else{
                        IInputHoraIniAt.value = selectedOptMenuAgendamento[2].slice(0, 5);
                      }
                    }
                    // Se veio 'Atendimento Concluido' ou 'Finalizado'
                    else if (status === 'Concluido'){
                      
                      InputHoraIniAt.style.display = '';
                      InputHoraFimAt.style.display = '';
                      
                      if (data.hora_inicio && data.hora_inicio.slice(0,5) !='00:00'){
                         IInputHoraIniAt.value = data.hora_inicio.slice(0,5);
                      }else{
                        IInputHoraIniAt.value = selectedOptMenuAgendamento[2].slice(0, 5);
                      }

                      if (data.hora_fim && data.hora_fim != '00:00:00') {
                        IInputHoraFimAt.value = data.hora_fim.slice(0,5);
                      }else{
                        IInputHoraFimAt.value = selectedOptMenuAgendamento[3].slice(0, 5);
                      }
                      
                    } else if (status === 'Finalizado'){
                          const statusAtual = selectedOptMenuAgendamento[5];

                          if (data.hora_fim && data.hora_fim.slice(0,5) != '00:00' && data.hora_inicio && data.hora_inicio.slice(0,5) != '00:00') {
                                gravaAlteraAtendimento(idAgSelected, status, data.hora_inicio, data.hora_fim, data.texto_prontuario);

                                return;
                          }else{
                              
                              InputHoraIniAt.style.display = '';
                              InputHoraFimAt.style.display = '';
                              
                              if (data.hora_inicio && data.hora_inicio.slice(0,5) !='00:00'){
                                 IInputHoraIniAt.value = data.hora_inicio.slice(0,5);

                              }else{
                                IInputHoraIniAt.value = selectedOptMenuAgendamento[2].slice(0, 5);

                              }


                              
                              if (data.hora_fim && data.hora_fim.slice(0,5) != '00:00') {
                                IInputHoraFimAt.value = data.hora_fim.slice(0,5);

                              }else{
                                IInputHoraFimAt.value = selectedOptMenuAgendamento[3].slice(0, 5);

                              }


                          }
                      }
                      textoProntuario.value = data.texto_prontuario;

                  } else {
                    // Se não existe atendimento 
                    // ainda: deixa ambos visíveis se for finalizado/concluído, só hora_ini se for em atendimento

                    if (status === 'Atendimento') {
                      InputHoraIniAt.style.display = '';
                      InputHoraFimAt.style.display = 'none';
                      IInputHoraIniAt.value = selectedOptMenuAgendamento[2].slice(0, 5);
                      IInputHoraFimAt.value = '';
                     
                    } else if(status == 'Finalizado' || status =='Concluido'){
                      IInputHoraIniAt.value = selectedOptMenuAgendamento[2].slice(0, 5);
                      IInputHoraFimAt.value = selectedOptMenuAgendamento[3].slice(0, 5);
                      InputHoraIniAt.style.display = '';
                      InputHoraFimAt.style.display = '';
                    }
                    
                    textoProntuario.value = data.texto_prontuario;


                    // Campos ficam vazios mesmo
                  }

                  // Agora sim mostra o modal!
                 IHSMA.value = status;
                  $('#modAtAgenda').modal('show');
                })
                .catch(error => {
                  console.error('Erro na consulta de atendimento:', error);
                  // Aqui  pode exibir uma mensagem de erro na interface, se quiser
                });
            }

        }else if(sala){
          $('#modSalas').modal('show');
          const salaInput =  document.getElementById('idAgendamentoModSalas')
          salaInput.value=selectedAgendamentoId;

        }else if(equipamento){

          $('#modEquipamentos').modal('show');
          const equipamentoInput =  document.getElementById('idAgendamentoModEquipamentos');
          equipamentoInput.value=selectedAgendamentoId;
          
        }else if(vender){

            if (vender=='venda'){
              let id_cliente = selectedOptMenuAgendamento[4];
              const idAgSelected = selectedOptMenuAgendamento[0]
              fetch('agenda_atendimentos/agenda_venda.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                  },
                  body: `id_agendamento=${encodeURIComponent(idAgSelected)}`
                })
                .then(response => response.json())
                .then(data => {

                  console.log(data);
                  const payload = {
                    origem: 'agenda', // pode ser usado para identificar a origem lá no PHP
                    id_cliente: id_cliente,
                    servicos: data.itensVendaAg // Array de serviços sem saldo
                };

                fetch('Modals/modalVendas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', // envia JSON puro
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.text())
                .then(html => {
                    // Aqui  pode abrir o modal com o HTML recebido!
                    // Exemplo com jQuery:
                    atualizarStatus = false;
                    
                    const $modal = $(html).appendTo('body');
                    const modalInstance = new bootstrap.Modal($modal[0]);
                    modalInstance.show();
                    // remove o DOM quando fechar
                    $modal.on('hidden.bs.modal', () => $modal.remove());
                    hideMenuSmoothly('','all');
                   

                })
                .catch(error => {
                    console.error('Erro ao abrir modal de venda:', error);
                });
              });

            }
        }else if(mensagem){
            templateId = mensagem;


            var nomeCompletoMsg = agendamento.getAttribute('data-serv-cliente');
            var primeiroNomeMsg = nomeCompletoMsg.trim().split(' ')[0];
            var dataMsg = formatarDataBr(agendamento.getAttribute('data-serv-dataagenda'));

            var dataMsgExt = dataPorExtenso(agendamento.getAttribute('data-serv-dataagenda'), true);
            
            
            
            var horaAgendamentoMsg = agendamento.getAttribute('data-serv-hora').slice(0,5);
            
            var servicoMsg = agendamento.getAttribute('data-serv-servico');
            var ProfMsg = agendamento.getAttribute('data-serv-nome_profissional').trim().split(' ')[0];
            var precoServMsg = DecimalBr(agendamento.getAttribute('data-serv-preco'));
            var telefoneMsg =agendamento.getAttribute('data-serv-telefone');

            var sexoCliente = (agendamento.getAttribute('data-sexo-cliente').slice(0,1)).toLowerCase();
                      

            
            if (sexoCliente =='m'){
              sexoMsg1='o';
              sexoMsg2='ao';
              sexoMsg3='ele';
            }else{
              sexoMsg1='a';
              sexoMsg2='a';
              sexoMsg3='ela';
            }

            

            fetch('agenda_atendimentos/get_mensagem.php?id=' + templateId)
              .then(resp => resp.json())
              .then(data => {
                const template = data.mensagem; // Exemplo: "Olá {nome}, seu agendamento é {dataagendamento}."
                    const variaveis = {
                        nome: primeiroNomeMsg,
                        nomecompleto: nomeCompletoMsg,
                        data: dataMsg,
                        dataextenso: dataMsgExt,
                        sexo: sexoMsg1,
                        sexo2: sexoMsg2,
                        sexo3: sexoMsg3,
                        hora: horaAgendamentoMsg,
                        servico: servicoMsg,
                        profissional: ProfMsg,
                        preco: 'R$ ' + precoServMsg,
                        telefone: telefoneMsg,
                        nomeclinica: 'Jess Corporal',
                        enderecoclinica: 'Av. Assis Brasil, 4550',
                        whatsappclinica: '51985706133',
                        instagramclinica: '@jesscorporal'
                    };

                const mensagemPronta = prepararMensagem(template, variaveis);
                console.log(mensagemPronta);

                enviarWhatsapp(telefoneMsg, mensagemPronta);
                // Agora exibe ou envia a mensagem
              });

        }


      }
});

function prepararMensagem(template, variaveis) {
  return template.replace(/\{(\w+)\}/g, (match, key) => variaveis[key] ?? '');
}


//limpa todos os inputs e textarea do modal de atendimentos
$('#modAtAgenda').on('hidden.bs.modal', function () {
    $(this).find('input, textarea').val('');
});



$('#modCancel').on('hidden.bs.modal', function () {
    $(this).find('input, textarea').val('');
});



function gravaAlteraAtendimento(idAgSelected, status, horaIni, horaFini, textoProntuario){
  
  fetch('agenda_atendimentos/iniciar_alterar_atendimento.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `id_agendamento=${encodeURIComponent(idAgSelected)}
                &status=${encodeURIComponent(status)}
                &hora_inicio=${encodeURIComponent(horaIni)}
                &hora_fim=${encodeURIComponent(horaFini)}
                &texto_prontuario=${encodeURIComponent(textoProntuario)}`
        })
        .then(response => response.json())
        .then(data => {

          console.log(data);
            hideMenuSmoothly('','all');

          if (data.sucesso){
              trocaDia=false;
              efeitos=false;
              carregarAgenda(dataCalend, agCancelados);
              $('#modAtAgenda').modal('hide');
              // Aqui  pode adicionar um reload da tabela ou mensagem de sucesso
          }

          if (data.NVenda && data.NVenda.length > 0) {
              // Supondo que já tem os dados do cliente em variáveis:
              // id_cliente, nome_cliente, etc.
              // Se precisar pegar do agendamento, tem que ajustar conforme necessário.

              // Exemplo: pegar os dados do primeiro agendamento, tem que ajustar se precisar:
              let id_cliente = selectedOptMenuAgendamento[4];
              //let nome_cliente = agendamentos[0]?.nome_cliente || '';
              
              // ...pode pegar outros campos se necessário...

              // Prepara o corpo do POST
              const payload = {
                  origem: 'agenda', // pode ser usado para identificar a origem lá no PHP
                  id_cliente: id_cliente,
                  servicos: data.NVenda // Array de serviços sem saldo
              };

              fetch('Modals/modalVendas.php', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json', // envia JSON puro
                  },
                  body: JSON.stringify(payload)
              })
              .then(response => response.text())
              .then(html => {
                  // Aqui  pode abrir o modal com o HTML recebido!
                  // Exemplo com jQuery:
                  atualizarStatus = true;
                  
                  const $modal = $(html).appendTo('body');
                  const modalInstance = new bootstrap.Modal($modal[0]);
                  modalInstance.show();
                  // remove o DOM quando fechar
                  $modal.on('hidden.bs.modal', () => $modal.remove());
                  

              })
              .catch(error => {
                  console.error('Erro ao abrir modal de venda:', error);
              });
          }
        })
        .catch(error => {
          console.error('Erro na requisição:', error);
    });

}
















document.addEventListener('VendaGravadaComSucesso', function(e){
    // Aqui você continua o fluxo!
    console.log('Venda gravada, dados:', e.detail);

    if (atualizarStatus){

      const idAgSelected = selectedOptMenuAgendamento[0];
      const status = 'Finalizado';

      const el = document.querySelector('.agendamento[data-id_agendamento="' + idAgSelected + '"]');

      fetch('agenda_atendimentos/consulta_atendimentos.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_agendamento=${encodeURIComponent(idAgSelected)}`
              })
                .then(response => response.json())
                .then(data => {

                  if ($('#modAtAgenda').hasClass('show')) {
                    horaIni = document.getElementById('inputAtHIni').value;
                    horaFini= document.getElementById('inputAtHFim').value;
                    textoProntuario = document.getElementById('textoProntuario').value;
                  }else {
                    horaIni = data.hora_inicio.slice(0,5);
                    horaFini= data.hora_fim.slice(0,5);
                    textoProntuario =data.texto_prontuario;
                  }

                  gravaAlteraAtendimento(idAgSelected, status, horaIni, horaFini, textoProntuario);

                  atualizarStatus=false;

                })
                .catch(error => {
                  console.error('Erro na consulta de atendimento:', error);
                  // Aqui  pode exibir uma mensagem de erro na interface, se quiser
                });


    }else{

                        trocaDia=false;
                        efeitos=false;
                        carregarAgenda(dataCalend, agCancelados);

    }



    // Por exemplo, recarregar agenda:
    // carregarAgenda(dataCalend, agCancelados);
    // Ou qualquer outra ação!
});





















function gravaCancel(idAgSelected, status, descricao){
   fetch('agenda_atendimentos/alterar_status.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `id_agendamento=${encodeURIComponent(idAgSelected)}&status=${encodeURIComponent(status)}&descricao=${encodeURIComponent(descricao)}`
        })
        .then(response => response.text())
        .then(data => {
          
          hideMenuSmoothly('','all'


          );
          
          trocaDia=false;
          efeitos=false;
          carregarAgenda(dataCalend, agCancelados);
          // Aqui pode adicionar um reload da tabela ou mensagem de sucesso
        })
        .catch(error => {
          console.error('Erro na requisição:', error);
    });

}





function mostrarErroDiv(elemento, mensagem, tempo = 2500) {
  if (!elemento) return;

  // Aplica mensagem e classe
  elemento.textContent = mensagem;
  elemento.classList.add('erro-modal-msg', 'visivel');

  // Remove mensagem e classe após o tempo
  clearTimeout(elemento._timeout);
  elemento._timeout = setTimeout(() => {
    elemento.classList.remove('visivel');
    elemento.textContent = '';
  }, tempo);
}







document.addEventListener('DOMContentLoaded', function () {
  
  const btnGravarEquipamento = document.getElementById('gravaEquipamento');
  const btnGravarSala = document.getElementById('gravaSala');
  const btnGravaCancel = document.getElementById('gravaCancel');
  const btnGravaAtendimento = document.getElementById('gravaAtendimento');
  const gravaConfirmExc = document.getElementById('gravaConfirmExc');



  if (btnGravaAtendimento){
    btnGravaAtendimento.addEventListener('click', function (e) {
      const inputHIni = document.getElementById('inputAtHIni');
      const inputHFim = document.getElementById('inputAtHFim');
      const IHSMA = document.getElementById('IHinddenModAtAg');
      const textoPront = document.getElementById('textoProntuario').value;
      console.log('texto do prontuário é:', textoPront);
      idAgSelected = selectedOptMenuAgendamento[0];
      gravaAlteraAtendimento(idAgSelected, IHSMA.value, inputHIni.value, inputHFim.value, textoPront);
      
    });
  }

  
  if (gravaConfirmExc){
    gravaConfirmExc.addEventListener('click', function (e) {
        $('#modConfirmExc').modal('hide');
        $('#modCancel').modal('show');
    });
  }



  
  if (btnGravaCancel){
    btnGravaCancel.addEventListener('click', function (e) {
     
        selectedAgendamentoId = document.getElementById('idAgendamentoModCancel').value;
        const status = document.getElementById('statusAgendamentoModCancel').value;
        const descricao = document.getElementById('textAgendamentoModCancel').value;
        
      if(descricao!='' &&  descricao.trim().toLowerCase() !== selectedOptMenuAgendamento[7].trim().toLowerCase()){
        gravaCancel(selectedAgendamentoId, status, descricao);
  
        $('#modCancel').modal('hide');

      } else{
        let mMensagem ='';
          if (status=='Cancelado'){
            mMensagem = 'do Cancelamento.';
          }else{
            mMensagem = 'da Falta.';
          }
          
          
         let mensagem = "Preencha o motivo " + mMensagem;

          const divMensagem = document.getElementById('erroModalMsg');
         mostrarErroDiv(divMensagem, 'Erro: '+ mensagem);
                    

      }
    });



  }


  if (btnGravarEquipamento) {
    btnGravarEquipamento.addEventListener('click', function (e) {
      e.preventDefault();
      
      // Adicione aqui a lógica que  deseja executar ao clicar
      const equipamentoSelect= document.getElementById('selectEquipamento');
      var equipamento = equipamentoSelect.options[equipamentoSelect.selectedIndex].text;
      var id_equipamento = equipamentoSelect.value;
      
      

         fetch('agenda_atendimentos/alterar_equipamento.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `id_agendamento=${encodeURIComponent(selectedAgendamentoId)}&equipamento=${encodeURIComponent(equipamento)}&id_equipamento=${encodeURIComponent(id_equipamento)}`
        })
        .then(response => response.text())
        .then(data => {

          $('#modEquipamentos').modal('hide');
          trocaDia=false;
          carregarAgenda(dataCalend, agCancelados);
          // Aqui pode adicionar um reload da tabela ou mensagem de sucesso
        })
        .catch(error => {
          console.error('Erro na requisição:', error);
        });



    });

  }
  
  if (btnGravarSala) {
     btnGravarSala.addEventListener('click', function (e) {
      e.preventDefault();
      
      const salaSelect= document.getElementById('selectSala');
      var sala = salaSelect.options[salaSelect.selectedIndex].text;
      var id_sala = salaSelect.value;
      
      

         fetch('agenda_atendimentos/alterar_sala.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `id_agendamento=${encodeURIComponent(selectedAgendamentoId)}&sala=${encodeURIComponent(sala)}&id_sala=${encodeURIComponent(id_sala)}`
        })
        .then(response => response.text())
        .then(data => {

          $('#modSalas').modal('hide');
          trocaDia=false;
          carregarAgenda(dataCalend, agCancelados);
          // Aqui pode adicionar um reload da tabela ou mensagem de sucesso
        })
        .catch(error => {
          console.error('Erro na requisição:', error);
        });






      // Adicione aqui a lógica que  deseja executar ao clicar
    });
  }












});



