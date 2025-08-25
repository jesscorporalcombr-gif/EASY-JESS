<?php 
	$pag = 'agenda_atendimentos';
	@session_start();

	require_once('../conexao.php');
	require_once('verificar-permissao.php');
	
?>

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



<head>




<link rel="stylesheet" href="agenda_atendimentos/agenda.css?v=3.26" type="text/css">




<style>
/*______________AGENDA_______________*/
.agenda-easy{
    background-color: <?php echo $cor_fundo_agenda?>;
    box-shadow: <?php echo $desloc_horizontal; ?>px <?php echo $desloc_vertical; ?>px <?php echo $efeito; ?>px rgba(<?php echo hexToRgb($cor_sombra); ?>, <?php echo $opacidade; ?>);
}
.agenda-easy-tr, .agenda-easy-td:not(:first-child) {
border-bottom: 1px solid <?php echo $cor_linha_horizontal?>; /*#EDEDED*/

}
.celula{
    border-left:1px solid <?php echo $cor_linha_vertical?>;/*#9DE1E3;*/
}
.celula:hover{
            background-color: <?php echo $cor_celula_selecionada?>;/*#6bd3d1;*/
            border-radius: 3px;
        }    
.celula-hover { /* somente para o js */
                background-color: <?php echo $cor_celula_selecionada?>;
}

.font-td-tab{
    color: <?php echo $cor_fonte_celula?> ;
}

.font-td-tab:hover{
            color: <?php echo $cor_fonte_celula_selecionada?>;
}

.agenda-easy-td-horario{
    color: <?php echo $cor_fonte_horario?>;

}
.font-head-prof{
    color: <?php echo $cor_fonte_profissional?>;
}


/*________________STATUS___________________________*/

/*-------------PARA A AGENDA GERAL 'não funciona dentro do css'----------------*/

            /* Estilos específicos para cada status */
            .statusAgendado {  background-color: <?php echo $corSAgendado ?>; }
            .statusConfirmado {  background-color: <?php echo  $corSConfirmado?>; }
            .statusFinalizado {  background-color: <?php echo  $corSFinalizado?>; }
            .statusAguardando {  background-color: <?php echo  $corSAguardando?>; }
            .statusAtendimento {  background-color: <?php echo  $corSAtendimento?>; }
            .statusPago {  background-color: <?php echo  $corSPago?>; }
            .statusFaltou { background-color: <?php echo  $corSFaltou?>; }
            .statusCancelado { background-color: <?php echo  $corSCancelado?>; }




/*---MENU SUSPENSO*/

/* Contêiner centralizado e com padding */


</style>




<?php






	//<!---======================CONEXÕES=================-->
    $dataAgenda = date('Y-m-d');
    
    $query = $pdo->query("SELECT * from servicos WHERE excluido <> true order by id desc");
    $servicos = $query->fetchAll(PDO::FETCH_ASSOC);
    $total_servicos = @count($servicos);
        
?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
  




</head>


<body>


    <!--====================== SUBMENU =======================================-->
    
    <?php  gerarMenu($pag, $grupos); ?>
			
			
   

    <div id="corpo-agenda">
            
        <div class = "container-fuid">
                <div class="row">
                    <div class= "col-md-2">
                    
                    </div>

                    <div class= "col-md-2"  >
                        
                        

                        <div class="input-group mb-3" style="padding-top: 10px; padding-left:23px;" >
                                <a id="agTabShow" class="btn btn-secondary btn-span" >
                                    <i class="bi bi-card-list" style="padding-left:6px; padding-top:5px;"></i>
                                </a>
                            <input placeholder="pesquisar na agenda" type="text" id="searchAgenda" class="form-control">
                        </div>

                    </div>

                    <div class= "col-md-5" >
                        <nav>
                            <div class="container">
                                <div class="d-flex" style="padding: 10px;">
                                
                                    <button hidden onclick= "abrirModal('modalClientes', document.getElementById('frm-id_cliente').value)" class="btn btn-outline-primary me-2" style="width: 130px; height: 30px; padding: 2px; margin-left: 10px;">
                                        <i class="bi bi-person-x-fill"></i> <!-- Ícone de edição do Bootstrap -->
                                    </button>
                                    <button hidden class="btn btn-outline-primary me-2" style="width: 30px; height: 30px; padding: 2px;">
                                        <i class="bi bi-alarm"></i> <!-- Ícone de exclusão do Bootstrap -->
                                    </button>
                                    <button hidden class="btn btn-outline-primary me-2" style="width: 30px; height: 30px; padding: 2px;">
                                        <i class="bi bi-arrows-fullscreen"></i> <!-- Ícone de exclusão do Bootstrap -->
                                    </button>

                                </div>
                            </div>
                        </nav> 
                    </div>

                    <div class= "col-md-2">
                        <div style="padding-top: 10px; " class="form-check">
                             <button class="btn btn-secondary" id="botaoCancelados" onClick="mostrarAgCancelados()" style="width: 130px; height: 30px; padding: 2px;">Cancelados
                                    <i class="bi bi-eye-slash" id= "icoBtCancelados" style="text-align: center;"></i> <!-- Ícone de adição do Bootstrap -->
                                </button>
                        </div>
                    </div>


                </div>


                <div class="container-fluid">
                    <div class="row" style = "padding-top: 10px; "> <!-- coluna  -->

                                <div class = "col-auto" style="padding-left: 5px; padding-bottom: 30px; max-width: 280px; min-width:100px; z-index: 6600;" >
                                    <input  hidden class="form-control"   id="calendario" name="calendario" value="<?php echo $dataAgenda ?>" type="date" /> 
                                    
                                    <div id="meuCalendario"></div>


                                    <script>
                                        document.addEventListener('DOMContentLoaded', () => {
                                        const { Calendar } = window.VanillaCalendarPro;
                                                // Create a calendar instance and initialize it.


                                                const options = {


                                                    firstWeekday: 0,
                                                    selectedWeekends: [0],
                                                    selectedTheme: 'light',
                                                    locale: 'pt-br',
                                                };
                                                const calendar = new Calendar('#meuCalendario', options);
                                                calendar.init();
                                                });
                                    </script>
                
                                </div>
                            
                                <div class="col" style="min-width: 500px;">
                                    <div id="agenda-container" class= "agenda-container" style="align-content: flex-start; width: 100%;">          
                                        <!-- =====AGENDA CARREGA AQUI ======-->
                                    </div>

                                    <div id="tabelaAgendamentos" class="agenda-container" style="align-content: flex-start; width: 100%;" hidden>
                                        <table class="table" id="examples">
                                            <thead>
                                                <tr>
                                                    <th  scope="col" class="sortable" data-coluna="hora">Hora</th>
                                                    <th  scope="col" class="sortable" data-coluna="nome_cliente">Cliente</th>
                                                    <th  scope="col" class="sortable" data-coluna="telefone_cliente">Telefone</th>
                                                    <th  scope="col"class="sortable" data-coluna="profissional_1">Profissional</th>
                                                    <th scope="col">Observações</th>
                                                    <th scope="col" class="sortable" data-coluna="status">Status</th>
                                                    <th  scope="col"  class="sortable" data-coluna="preco">Preço</th>
                                                    <th scope="col">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody id="agendaBody">
                                                <!-- Os dados serão inseridos aqui via JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>




                                    <div id="tooltip" class = "tooltipAgenda">
                                        <!-- =====tolltip com o js ======-->
                                    </div>
                                    <input type="text" id="agendamentosDoDia" hidden>

                            
                                </div>
                    </div>
                </div>
        </div>
    </div>

    <div id="footer-agenda">


    </div> 
    


  


</body> <!--=======================================Fim da página===================--> 
 
 



<!--======MODAL confirmação arrasta e solta -->
 <div class="modal" tabindex="-1" role="dialog" id="confirmModal">
  <div class="modal-dialog" style="top:40%; left:40%; position:fixed; "  role="document">
    <div class="modal-content">
      <div class="modal-header" style= "background-color: #716EF6;  color: white; border-radius: 5px 5px 0px 0px; height: 50px">
        <h5 class="modal-title">Confirmar Alteração</h5>
        <button type="button"  class="btn-close btn-close-white" id = "fechaModalMove" data-bs-dismiss="modal" aria-label="Fechar">
         
        </button>
      </div>
      <div class="modal-body">
        <p>Tem certeza de que deseja alterar este agendamento?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary"  id="confirmChange">Sim</button>
        <button type="button" class="btn btn-secondary"  id="cancelChange" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

 



<!--======MODAL de Agendamentos -->
<div class="modal fade" tabindex="-1" id="modalAgendar" style = "z-index: 11000; "  data-bs-backdrop="static">
       <div class="modal-dialog modal-lg"  >
		    <div class="modal-content"style="border-radius: 5px;">
                <div class="modal-header">
                        <h5 class="modal-title">Agendamento</h5>   
                        <a type="button"  class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-lg"></i></a>

                </div>
                <form method="POST" id="form-agendar">
            		<div class="modal-body">
            			    
                    	            <div class= "container"  style = "padding-bottom: 20px; background-color: #fbfbfb; " >
                                    				    <div hidden>
                                                            <input  type="text" required id="frm-origem_agendamento" name="frm-origem_agendamento">
                                                            <input  type="text" required id="frm-tipo_agendamento" name="frm-tipo_agendamento">  <!-- novo ou edicao -->
                                                            <input  type="text" id="frm-id_agendamento" name="frm-id_agendamento"> 
                                                            <input  type="text" required id="frm-id_profissional" name="frm-id_profissional">
                                                    
                                                            <input  type="text" id="frm-id_servico" name="frm-id_servico"> 
                                                            
                                                            <input  type="text" required id="frm-id_cliente" name="frm-id_cliente">
                                                            <input  type="text" id="frm-cpf_cliente" name="frm-cpf_cliente">
                                                            <input  type="text" id="frm-telefone_cliente" name="frm-telefone_cliente">
                                                            <input  type="text" id="frm-email_cliente" name="frm-email_cliente">
                                                            <input  type="text" id="frm-aniversario_cliente" name="frm-aniversario_cliente">
                                                            
                                                            <input  type="text" id="frm-nome_profissional" name="frm-nome_profissional">
                                                            
                                                        </div>
                                                        
                                    					<div class="row" id="frm-cont-cliente" style= "position: relative; padding-top: 10px;  padding-bottom: 10px;">
                                                            
                                                            <div id="divFotoCliente" class="col-md-2" style="max-width: 100px;">
                                                                <div  style="margin-top:7px; ">
                                                                    <img id="img-foto_cliente" src="../img/sem-foto.svg" style="width:90px; height: 90px; border-radius:50%;">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-10">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div id="div-sel-cliente"> 
                                                                            
                                                                            <div> <!--label + input + icone do olho + drop de pesquisa-->
                                                                        
                                                                                <div class="input-group" style ="width: 350px;" id="frm-nome_cliente-icon-wrapper">

                                                                                    <input required id="frm-nome_cliente" autocomplete="off" name="frm-nome_cliente" type="text" class ="form-control" placeholder="Digite o nome do cliente">

                                                                                     <a class="btn btn-secondary btn-span"   id="ico-cad_cliente"  onclick= "abrirModal('modalClientes', document.getElementById('frm-id_cliente').value)"><i id="ico-cad" class="bi bi-eye-fill" style="padding-left:2px;"></i></a>

                                                                                    <!--<a class="btn btn-secondary btn-span" id="btn-cadastrar_cliente" onclick= "abrirModal('modalClientes', )"><i class="bi bi-person-plus bi bi-eye-fill"></i></a>-->
                                                                                    
                                                                                                                                                                    
                                                                                </div> 

                                                                                <div id="lista-clientes" style="position:absolute; z-index: 10; width: 300px;"           class="dropdown-cliente"aria-labelledby="lab-nome_cliente">
                                                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                    <!-- aqui aparece o dropdown com os clientes da pesquisa -->
                                                                                    </div>
                                                                                </div>

                                                                               

                                                                            </div>  
                                                                            
                                                                                <!--label com o NOME do cliente quando a função é editar-->
                                                                            <label onclick= "abrirModal('modalClientes', document.getElementById('frm-id_cliente').value)" style="padding-left: 10px; padding-top: 5px; font-size: 14px; cursor: pointer; text-decoration: underline; text-decoration-color: blue;" id="frm-lb-nome_cliente"></label> 
                                                                                <!--label com o CPF do cliente quando a função é editar-->       
                                                                            
                                                                            <div class="me-2"style="padding-left: 10px; padding-top: 5px;font-size: 14px;">   
                                                                                <label id="frm-lb-cpf_cliente" class="me-2"></label>
                                                                                <label id="frm-lb-telefone_cliente" class="me-2" style="font-size: 14px;"></label>
                                                                                <label id="frm-lb-email_cliente" class="me-2" style="font-size: 14px;"></label>
                                                                                <label id="frm-lb-aniversario_cliente" class="me-2" style="font-size: 14px;"></label>
                                                                            </div>
                                                                        </div>

                                                                        
                                                                    </div>
                                                                
                                                            
  
                                                                </div>

                                                            </div><!-- fecha o lado esquerdo onde estão somente os dados -->
                                                            
                                                        </div> <!--fecha -- dados do cliente-->
                                                        
                                                        <!--<hr>--linha separa o cliente dos dados do agendamento------>
                                             			
                                             			
                                             			    
                                                 		<div class = "row" style = " padding-top: 5px; padding-bottom: 8px; border-top: solid 1px #E8E8E8;">
                                                					     
                                                					   <div class="col-md-3">
                                                                            <label for="frm-data_agenda">Data:</label>
                                                                            <input require id="frm-data_agenda" class = "form-control" placeholder = "" name="frm-data_agenda" type="date" >
                                                                        </div>
                                                        </div>
                                                        
                                                        <!-- ==============================Bloco com as informações do agendamento============================------>
                                                        
                                                		<div style = "border-radius: 10px; border: 1px solid #E8E8E8; padding-top: 10px; padding-bottom: 5px; ">
                                                		        <div class = "container">
                                                                		 
                                                                		<div class="row">

                                                                 					     
                                                                 					     
                                                                        <table id="tabela-agendamento" class="table">
                                                                                    <thead style="border-bottom: 1px solid white;">
                                                                                        <tr>
                                                                                            <th width="150px">Profissional:</th>
                                                                                            <th width="200px">Serviço:</th>
                                                                                            <th width="100px">Valor:</th>
                                                                                            <th>Tempo:</th>
                                                                                            <th>Início:</th>
                                                                                            <th>Fim:</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <select id="frm-profissional" onmousedown="alterarServico()" name="frm-profissional" class="form-select" style="font-size: 12px" type="text" required>
                                                                                                    <!-- Opções do profissional serão inseridas aqui -->
                                                                                                </select>
                                                                                            </td>
                                                                                            <td>
                                                                                                <select required  id="frm-servico" name="frm-servico" class="form-select" style="font-size: 12px">
                                                                                                    <option></option>
                                                                                                    <!-- PHP para inserir opções de serviço -->
                                                                                                </select>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input required type="text" name="frm-valor" id="frm-valor" class="form-control" style="font-size: 12px;">
                                                                                            </td>
                                                                                            <td>
                                                                                                <input onchange="calcularFim()" type="number" id="frm-tempo_min" name="frm-tempo_min" class="form-control" style="font-size: 12px;"  min="0" step="1" required>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input onchange="calcularFim()" id="frm-hora_ini" name="frm-hora_ini" class="form-control" style="font-size: 12px;" type="time"  required>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input id="frm-hora_fim" name="frm-hora_fim" class="form-control" style="font-size: 12px;" type="time"  required readonly>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                        </div>  
                                                                
                                                        		
                                                                		<div class="row">
                                                                					    
                                                                        </div> 
                                                                        
                                                                       
                                                                     <div id="cont-status" class="status-container mb-5">
                                                                            <label for="" class="control-label"><b>Status: </b></label>
                                                                                    
                                                                                <div class="flex row">
                                                                                        
                                                                                    <!-- Agendado -->
                                                                                        <label class="radioStatus">
                                                                                        <span class="spanStatus statusAgendado">
                                                                                            <input type="radio" id="statusAgendado" name="frm-status" value="Agendado">
                                                                                            Agendado
                                                                                        </span>
                                                                                        </label> 
                                                                                        
                                                                                        <!-- Cancelado -->
                                                                                        <label class="radioStatus">
                                                                                        <span class="spanStatus statusCancelado">
                                                                                            <input type="radio" id="statusCancelado" name="frm-status" value="Cancelado">
                                                                                            Cancelado
                                                                                        </span>
                                                                                        </label>

                                                                                        <!-- Confirmado -->
                                                                                        <label class="radioStatus"> 
                                                                                        <span class="spanStatus statusConfirmado">
                                                                                            <input type="radio" id="statusConfirmado" name="frm-status" value="Confirmado">
                                                                                            Confirmado
                                                                                        </span>
                                                                                        </label>
                                                                                        
                                                                                                                                                                               
                                                                                         <!-- Faltou -->
                                                                                       
                                                                                        <label class="radioStatus"> 
                                                                                        <span class="spanStatus statusFaltou">
                                                                                            <input type="radio" id="statusFaltou" name="frm-status" value="Faltou">
                                                                                            Faltou
                                                                                        </span>
                                                                                        </label>

                                                                                        <!-- Aguardando -->
                                                                                        
                                                                                        <label class="radioStatus">
                                                                                        <span class="spanStatus statusAguardando">
                                                                                            <input type="radio" id="statusAguardando" name="frm-status" value="Aguardando">
                                                                                            Aguardando
                                                                                        </span>
                                                                                        </label>

                                                                                        <!-- Em Atendimento -->
                                                                                        
                                                                                        <label class="radioStatus"> 
                                                                                        <span class="spanStatus statusAtendimento">
                                                                                            <input type="radio" id="statusAtendimento" name="frm-status" value="Em Atendimento">
                                                                                            Em Atendimento
                                                                                        </span>
                                                                                       </label>

                                                                                        <!-- Finalizado -->
                                                                                        
                                                                                        <label class="radioStatus">
                                                                                        <span class="spanStatus statusFinalizado">
                                                                                            <input type="radio" id="statusFinalizado" name="frm-status" value="Finalizado">
                                                                                            Finalizado
                                                                                        </span>
                                                                                        </label>

                                                                                        <!-- Pago 
                                                                                        
                                                                                        <label class="radioStatus">
                                                                                        <span class="spanStatus statusPago">
                                                                                            <input type="radio" id="statusPago" name="frm-status" value="Pago">
                                                                                            Pago
                                                                                        </span>
                                                                                        </label> -->

                                                                                
                                                                                </div>
                                                                     </div>

                                                 <div class="form-group col-md-12" style="padding-bottom: 25px; ">
                                                    <label for="frm-observacao">Observações:</label>
                                                    <textarea id="frm-observacao" name = "frm-observacao" class="form-control" style="font-size: 12px; height: 100px" type="time"></textarea>
                                                  </div>
                                                                        
                                                                    
                                                                    
                                                                </div>
                                                        </div>                     

                                    </div>
                        
                        </div> 
                                                    
                                                    <div class="modal-footer" style="height: 80px; border-radius: 0px 0px 8px 8px; display: flex; justify-content: flex-end; align-items: center;">
                                                        <div class= "footer" style= "padding-right: 15px"; >
                                                            <button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    
                                                            <button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>
                                                        </div>
                                                    </div>
                             
            	    </form>
            </div>
        </div>    	    
</div> <!--================================== FECHANDO O MODAL DE LANÇAMENTO===============================-->


<div id="custom-menu" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; z-index:80000;">
  <div data-status= "Confirmado" class="menu-item status-confirmado">Confirmado</div>
  <hr>
  <div data-status= "Aguardando"class="menu-item status-aguardando">Aguardando</div>
  <div data-status= "Em Atendimento" class="menu-item status-atendimento">Em Atendimento</div>
  <div data-status= "Finalizado" class="menu-item status-finalizado">Finalizar Atendimento</div>
</div>


<!--================================== MODAL DE Clientes===============================-->

<!--================================== FECHANDO O MODAL DE LANÇAMENTO===============================-->


<div class="modal-cliente">
        <!-- Conteúdo carregado pelo PHP será inserido aqui -->
</div>







<!-- LIMPA O FORMULARIO DE AGENDAMENTOS -->
<script>

    function resetarFormulario(formId) {
        var form = document.getElementById(formId);

        form.reset();
        //listaClientes.classList.remove('show');
        document.getElementById('frm-lb-nome_cliente').textContent = "";
        document.getElementById('frm-lb-telefone_cliente').textContent = "";
        document.getElementById('frm-lb-email_cliente').textContent = "";
        document.getElementById('frm-lb-cpf_cliente').textContent = "";
        document.getElementById('frm-lb-aniversario_cliente').textContent = "";
        document.getElementById('ico-cad').className= '';
        document.getElementById('ico-cad').classList.add('bi', 'bi-person-plus');
                         // document.getElementById('btn-cadastrar_cliente').hidden=false; bi bi-person-plus bi bi-eye-fill

        document.getElementById('divFotoCliente').hidden=true;
        document.getElementById('frm-lb-nome_cliente').style.display='none';
        document.getElementById('img-foto_cliente').src='../img/sem-foto.svg';

        $('#frm-servico').empty().append('<option></option>');

    }
 
</script>




<!-- GRAVAR OU ALTERAR AGENDAMENTO -->
<script>
$(document).ready(function(){



    const $form = $('#form-agendar');
    const $botaoEnviar = $("#btnEnviar");

    $botaoEnviar.on('click', function(e) {
        e.preventDefault();

        // Se já estiver desabilitado, sai
        if ($botaoEnviar.prop("disabled")) {
            return;
        }

        // Desabilita imediatamente no click
        $botaoEnviar.prop("disabled", true);

        // Agora dispara manualmente o submit do form
        $form.submit();
    });


    $('#form-agendar').on('submit', function(e){
        e.preventDefault();

        const $botaoEnviar = $("#btnEnviar");

        // Se o botão já estiver desabilitado, sai da função para evitar re-envios
        if ($botaoEnviar.prop("disabled")) {
            return;
        }
        
        // **Desabilita o botão imediatamente**, antes de qualquer outra coisa
        $botaoEnviar.prop("disabled", true);

        // Agora faz as validações
        var idClienteValido = document.getElementById('frm-id_cliente').value.trim() != "" && document.getElementById('frm-id_cliente').value.trim() != "0";
        var origemAgendamentoValido = document.getElementById('frm-origem_agendamento').value.trim() != "" && document.getElementById('frm-origem_agendamento').value.trim() != "0";
        var tipoAgendamentoValido = document.getElementById('frm-tipo_agendamento').value.trim() != "" && document.getElementById('frm-tipo_agendamento').value.trim() != "0";
        var idProfissionalValido = document.getElementById('frm-id_profissional').value.trim() != "" && document.getElementById('frm-id_profissional').value.trim() != "0";

        if (idClienteValido && origemAgendamentoValido && tipoAgendamentoValido && idProfissionalValido) {
            
            var dados = $(this).serialize();
            
            $.ajax({
                url: 'agenda_atendimentos/inserir.php',
                type: 'POST', 
                dataType: 'json',
                data: dados,
                success: function(response) {
                    if(response.success) {
                        $('#modalAgendar').modal('hide');
                        carregarAgenda(dataCalend, agCancelados);
                    } else {
                        //alert("Erro ao salvar o agendamento: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Ocorreu um erro: " + error);
                },
                complete: function(){
                    // Reativa o botão após a requisição AJAX ter finalizado
                    $botaoEnviar.prop("disabled", false);
                }
            });

        } else {
            // Campos inválidos: reativa o botão e exibe alerta
            $botaoEnviar.prop("disabled", false);
            alert("Por favor, preencha todos os campos obrigatórios antes de agendar.");
        }
    });
});




</script><!-- FIMM  GRAVAR OU ALTERAR AGENDAMENTO -->








<!--------============= MODAL AGENDAMENTOS - CARREGAMENTO - TABELA   ===============-->
<script>
 let tipoAgendamento = [];
    
 
 let agendamentosData = [];

    function buscarAgendamentos(dataAgenda) {
        fetch(`endPoints/get_agendamentos.php?data=${dataAgenda}`)
            .then(response => response.json())
            .then(data => {
                agendamentosData = data;
                // Serializa os dados em JSON e armazena no input
                document.getElementById('agendamentosDoDia').value = JSON.stringify(data);
                
            })
            .catch(err => console.error('Falha ao carregar os agendamentos:', err));
    }




    //document.addEventListener('DOMContentLoaded', function() {
        
        $(document).ready(function() {
        //SE o clique é em um AGENDAMENTO ou em uma CELULA para novo agendamento
        let modalAberto = false;

        document.getElementById('agenda-container').addEventListener('click', function(event) {
                if (modalAberto) {
                    return; // Impede a abertura de múltiplos modais se um já está aberto
                }

                var agendamentoClicado = event.target.closest('.agendamento');
                var celulaClicada = event.target.closest('.celula');

                if (agendamentoClicado) {
                    tipoAgendamento = "edicao";
                                        abrirModalEditarAgendamento(agendamentoClicado.dataset.id_agendamento, agendamentoClicado.dataset.foto_cliente, agendamentoClicado.dataset.aniversario);
                    event.stopImmediatePropagation();
                } else if (celulaClicada) {
                    tipoAgendamento = "novo";
                    abrirModalNovoAgendamento(celulaClicada);
                    event.stopImmediatePropagation();
                }

                modalAberto = true;
                setTimeout(() => modalAberto = false, 500); // Reabilita após 500ms
        });



// FUNÇÕES PARA ABRIR MODAL AGENDAMENTOS
        
        //Função Novo Agendamento CELULA
        function abrirModalNovoAgendamento(celula) {
                           
            resetarFormulario('form-agendar');
                        
                        document.getElementById('frm-tipo_agendamento').value = "novo";
                        
                        document.getElementById('statusAgendado').checked = true;
                        document.getElementById('frm-origem_agendamento').value = "sistema";
                        document.getElementById('frm-id_cliente').value = "0";
                        document.getElementById('frm-id_servico').value ="0";
                        document.getElementById('cont-status').hidden =true;
                        document.getElementById('frm-id_agendamento').value ="0";
                        document.getElementById('frm-id_profissional').value = celula.dataset.id_profissional;
                        document.getElementById('frm-profissional').value = celula.dataset.profissional; // para a visualização
                        document.getElementById('frm-nome_profissional').value = celula.dataset.profissional; // para o post
                        document.getElementById('frm-data_agenda').value = celula.dataset.data_agenda;
                        document.getElementById('frm-hora_ini').value = celula.dataset.hora_agenda;
                    
                    
                        document.getElementById('frm-nome_cliente').value = "";
                        document.getElementById('frm-nome_cliente').readOnly = false;
                        document.getElementById('frm-nome_cliente').hidden = false;
                        document.getElementById('frm-nome_cliente-icon-wrapper').hidden = false;
                        //document.getElementById('lb-para-nome_cliente').style.display = 'block';


                        document.getElementById('frm-cont-cliente').style.backgroundColor = 'white';
                                                
                        
                        
                        // mostra o modal
                        var modalBootstrap = new bootstrap.Modal(document.getElementById('modalAgendar'));
                        modalBootstrap.show();
                         
        }

        //Função carregar AGENDAMENTO existente
        function abrirModalEditarAgendamento(idAgendamento, foto, aniversario) {
            // agendamentoEncontrado recebe o array do agendamento selecionado (já carregado em agendamentosData)

               var agendamentoEncontrado = agendamentosData.find(ag => Number(ag.id) === Number(idAgendamento));
           
            if (agendamentoEncontrado) {
                
                 resetarFormulario('form-agendar');
                            
                            // ao encontrar o agendamento preenche o formulario com as informações de agendamentoEncontrado
                            
                            if (foto!=""){
                            document.getElementById('img-foto_cliente').src= '../img/clientes/' + foto;
                            }else{
                            document.getElementById('img-foto_cliente').src= '../img/sem-foto.svg';

                            }

                            document.getElementById('frm-tipo_agendamento').value = "edicao";
                            document.getElementById('frm-id_agendamento').value = agendamentoEncontrado.id;
                            
                            document.getElementById('cont-status').hidden = false; // cointerner dos status ficar visivel

                            if (agendamentoEncontrado.status== "Agendado"){document.getElementById('statusAgendado').checked = true;}
                            if (agendamentoEncontrado.status== "Confirmado"){document.getElementById('statusConfirmado').checked = true;}
                            if (agendamentoEncontrado.status== "Aguardando"){document.getElementById('statusAguardando').checked = true;}
                            if (agendamentoEncontrado.status== "Em Atendimento"){document.getElementById('statusAtendimento').checked = true;}
                            if (agendamentoEncontrado.status== "Finalizado"){document.getElementById('statusFinalizado').checked = true;}
                            if (agendamentoEncontrado.status== "Pago"){document.getElementById('statusPago').checked = true;}
                            if (agendamentoEncontrado.status== "Faltou"){document.getElementById('statusFaltou').checked = true;}
                            if (agendamentoEncontrado.status== "Cancelado"){document.getElementById('statusCancelado').checked = true;}
                            
                            document.getElementById('frm-lb-nome_cliente').style.display = 'block'; //mostra o label com o nome do cliente
                            document.getElementById('frm-lb-telefone_cliente').style.display = 'block';
                            document.getElementById('frm-lb-cpf_cliente').style.display = 'block';
                            document.getElementById('frm-lb-email_cliente').style.display = 'block';
                            document.getElementById('frm-lb-aniversario_cliente').style.display = 'block';
                            
                            //document.getElementById('lb-para-nome_cliente').style.display = 'none';
                            


                            document.getElementById('frm-id_cliente').value = agendamentoEncontrado.id_cliente;
                            

                             
                            
                            
                            // Verifica se nao encontrou o cliente busca na função
                            if (!clientes) {
                               
                               
                            }

                            var clienteEncontrado = clientes.find(function(cliente) {
                                return cliente.id === agendamentoEncontrado.id_cliente;
                            });



                            document.getElementById('frm-nome_cliente').hidden = true;
                            document.getElementById('frm-nome_cliente-icon-wrapper').hidden = true;
                            document.getElementById('divFotoCliente').hidden = false;
                            
                            
                            document.getElementById('frm-nome_cliente').value = agendamentoEncontrado.nome_cliente;
                            document.getElementById('frm-telefone_cliente').value = agendamentoEncontrado.telefone_cliente;
                            document.getElementById('frm-email_cliente').value = agendamentoEncontrado.email_cliente;
                            document.getElementById('frm-cpf_cliente').value = agendamentoEncontrado.cpf_cliente;

                            document.getElementById('frm-lb-nome_cliente').textContent = "Cliente: " + clienteEncontrado.nome;
                            document.getElementById('frm-lb-telefone_cliente').textContent = "Telefone: " + clienteEncontrado.celular;
                            document.getElementById('frm-lb-email_cliente').textContent = "E-mail: " + clienteEncontrado.email;
                            document.getElementById('frm-lb-cpf_cliente').textContent = "CPF: " + clienteEncontrado.cpf;
                            document.getElementById('frm-lb-aniversario_cliente').textContent = "Nascimento: " + formatData(clienteEncontrado.aniversario);


                            document.getElementById('frm-observacao').value = agendamentoEncontrado.observacoes;
                            document.getElementById('frm-id_profissional').value = agendamentoEncontrado.id_profissional_1;
                            

                            

                            
                            document.getElementById('frm-data_agenda').value = agendamentoEncontrado.data;
                            document.getElementById('frm-hora_ini').value = agendamentoEncontrado.hora.substr(0, 5);
                            document.getElementById('frm-id_servico').value = agendamentoEncontrado.id_servico;


                            var servicoValor = agendamentoEncontrado.servico;
                            var selectElement = document.getElementById('frm-servico');

                            // Verifica se a opção já existe
                            var opcaoExistente = Array.from(selectElement.options).some(function(option) {
                                return option.value === servicoValor;
                            });

                            // Se a opção não existir, cria uma nova e a adiciona ao select
                            if (!opcaoExistente) {
                                var novaOpcao = new Option(servicoValor, servicoValor);
                                selectElement.add(novaOpcao);
                            }

                            // Define o valor do select
                            selectElement.value = servicoValor;





                            document.getElementById('frm-servico').value = agendamentoEncontrado.servico;
                            document.getElementById('frm-origem_agendamento').value = "sistema";
                            var precoFormatado = parseFloat(agendamentoEncontrado.preco).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            document.getElementById('frm-valor').value = precoFormatado;
                            document.getElementById('frm-tempo_min').value = agendamentoEncontrado.tempo_min
                            document.getElementById('frm-observacao').value = agendamentoEncontrado.observacoes;
                            //document.getElementById('frm-status').value = agendamentoEncontrado.status;
                            
                            //-----abaixo as alterações de configuraçõe quando um agendamento é carregado-->
                            document.getElementById('frm-cont-cliente').style.backgroundColor = '#fafafa';
                            document.getElementById('frm-cont-cliente').style.borderRadius = '5px'
                            
                            //document.getElementById('div-sel-cliente').style.display = 'none';
                            //document.getElementById('btn-cadastrar_cliente').hidden = true;


                            
                            
                            calcularFim(); //chama a função que calcula a hora de termino do serviço
                            
                            // mostra o modal
                            var modalBootstrap = new bootstrap.Modal(document.getElementById('modalAgendar'));
                            modalBootstrap.show();
            } else {
                alert("Erro: Agendamento não encontrado.");
            }
        }

        //===================================TABELA AGENDA =========================================


        function atualizarTabela(text) {
            const dadosJson = $('#agendamentosDoDia').val(); // Pega os dados do input
            const agendamentos = JSON.parse(dadosJson); // Converte de JSON para objeto JavaScript
            
            agendamentos.sort((a, b) => {
                if(colunaAtual == "" ){ colunaAtual = 'hora'}
                        let valA = a[colunaAtual].toString().toLowerCase(), valB = b[colunaAtual].toString().toLowerCase();
                    
                        if (ordemAtual === 'asc') {
                            return valA.localeCompare(valB);
                        } else {
                            return valB.localeCompare(valA);
                        }
            });

            let $tbody = $("#agendaBody");
            $tbody.empty(); // Limpa a tabela antes de adicionar novos dados
            console.log("Texto de pesquisa:", text);
              
            $.each(agendamentos, function(i, agendamento) {
                if (!text || agendamento.nome_cliente.toLowerCase().includes(text.toLowerCase()) || agendamento.profissional_1.toLowerCase().includes(text.toLowerCase())) {
                    $tbody.append(
                        `<tr>
                            <td>${agendamento.hora}</td>
                            <td>${agendamento.nome_cliente}</td>
                            <td><a href="https://api.whatsapp.com/send?phone=55${agendamento.telefone_cliente}" target="_blank">${agendamento.telefone_cliente}</a></td>
                            <td>${agendamento.profissional_1}</td>
                            <td>${agendamento.observacoes}</td>
                            <td>${agendamento.status}</td>
                            <td>R$ ${parseFloat(agendamento.preco).toFixed(2)}</td>
                            <td>
                                <button class="btn btnEditTable" data-id="${agendamento.id}"><i class="bi bi-pencil-square"></i></button>
                                <!--<button class="btn btn-success"><i class="bi bi-check-circle"></i></button>
                                <button class="btn btn-danger"><i class="bi bi-trash"></i></button>-->
                            </td>
                        </tr>`
                    );
                }
            });
            $('.btnEditTable').on('click', function() {
            const idAg = $(this).data('id'); // Obtém o ID do agendamento do atributo data-id
            tipoAgendamento = "edicao";
            abrirModalEditarAgendamento(idAg); // Chama a função para abrir o modal de edição
            });
        }


        let ordemAtual = 'asc';
        let colunaAtual = [];

        $('.sortable').on('click', function() {
            colunaAtual = $(this).data('coluna');
            ordemAtual = ordemAtual === 'asc' ? 'desc' : 'asc';
            atualizarTabela($('#searchAgenda').val());
        });

        $("#agTabShow").click(function() {
            let isActive = $(this).hasClass('active');
            atualizarTabela();
            if (!isActive) {
                $(this).addClass('active').find('i').removeClass('bi-card-list').addClass('bi-calendar2-week');
                $("#tabelaAgendamentos").removeAttr('hidden');
                $("#agenda-container").attr('hidden', true);
                
                // Atualiza a tabela sem buscar novos dados
            } else {
                $(this).removeClass('active').find('i').removeClass('bi-calendar2-week').addClass('bi-card-list');
                $("#tabelaAgendamentos").attr('hidden', true);
                $("#agenda-container").removeAttr('hidden');
            
            }
        });

        $("#searchAgenda").on("input", function() {
            let searchText = $(this).val();
            atualizarTabela(searchText);
            if (searchText) {
                $("#agTabShow").addClass('active').find('i').removeClass('bi-card-list').addClass('bi-calendar2-week');
                $("#tabelaAgendamentos").removeAttr('hidden');
                $("#agenda-container").attr('hidden', true);
                // Atualiza a tabela sem buscar novos dados
            }
        });







     
});      

</script><!-- MODAL AGENDAMENTOS - CARREGAMENTO-->



<!--================CARREGA AGENDA ===================-->
<script>
    let usuarios=[]; //variavem para receber o array comos profissionais da agenda do dia
    let agCancelados = false;
    //Função que pega e transforma para js os dados dos profissionaisl vindos junto com a agenda no input hidden usuarios-data
    //let dataCalend = '';
   
    function carregarDadosUsuarios() {
    const usuariosJson = document.getElementById('usuarios-data').value;
    usuarios = JSON.parse(usuariosJson);
    }


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
            carregarAgenda(dataCalend, agCancelados);
    }


    //função que chama o carregamento da agenda      

    function carregarAgenda(data, mostrarCancelados) {
       

            buscarAgendamentos(data); // aquie já traz todos os agendamentos do dia 

            $("#tabelaAgendamentos").attr('hidden', true);
            $("#agenda-container").removeAttr('hidden');
            $("#agTabShow").removeClass('active').find('i').removeClass('bi-calendar2-week').addClass('bi-card-list');
            
            dataCalend = data; // define e data do calendario selecionada
            //agCancelados = mostrarCancelados;
        const dados = new URLSearchParams({ //monta o array para a url
            data: data,
            mostrarCancelados: mostrarCancelados
            // Adicionar mais variáveis conforme necessário
        });

        fetch(`agenda_atendimentos/agenda.php?${dados.toString()}`)
        .then(response => response.text()) // recebe a resposta da agenda
        .then(htmlAgenda => {
            const $c = $('#agenda-container');



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




  carregarDadosUsuarios();




});
    }


   //Função para lidar com mudanças no input do calendário
   function onCalendarioChange() {
           var calendario = document.getElementById('calendario');
           agCancelados=false;

            
            if (calendario) {
               calendario.addEventListener('change', function() {
                    dataCalend = this.value;
                   
                    carregarAgenda(dataCalend, agCancelados); 
                   
                    // Chama carregarAgenda c a nova data
                });
            }
    }




        document.addEventListener('click', function(event) {
            // só prossegue se clicou num elemento (ou filho) com a classe .vc-date__btn
            const btn = event.target.closest('.vc-date__btn');
            if (!btn) return;

            // encontra a div .vc-date que tenha o atributo data-vc-date
            const dateDiv = btn.closest('.vc-date[data-vc-date]');
            if (!dateDiv) return;

            // lê direto o valor de data-vc-date
            const dataCalend = dateDiv.getAttribute('data-vc-date');
            // ou: const dataCalend = dateDiv.dataset.vcDate;

            carregarAgenda(dataCalend, agCancelados);
        });



    //função carregada quando o DOM está pronto para chamar a agenda do dia
    document.addEventListener('DOMContentLoaded', function () {    
        var dataHoje = new Date().toISOString().split('T')[0];// Obtém a data de hoje no formato Y-m-d
        //padrão para não mostrar os cancelados no carregamento
      
        carregarAgenda(dataHoje, agCancelados);  // Chama carregarAgenda passando a data de hoje como argumento
       
    });

</script>














<script>
        $(document).ready(function() {
            $('#frm-profissional').on('change', function() {
                var profissionalId = $(this).val();
                document.getElementById('frm-id_profissional').value = profissionalId;
                var profissionalNome = $(this).find('option:selected').text(); // isso pega o texto da opção selecionada
                document.getElementById('frm-nome_profissional').value = profissionalNome; // passa o nome do profisisonal para o post no input que está hiden

                // Requisição AJAX para buscar os serviços disponíveis
                $.ajax({
                    url: 'agenda_atendimentos/servicos_do_profissional.php', // Atualize com o caminho correto para o seu script PHP
                    type: 'GET',
                    data: { id_profissional: profissionalId },
                    dataType: 'json',
                    success: function(response) {
                        // Adiciona as opções de serviços baseadas na resposta
                        
                        
                        response.forEach(function(servico) {
                            var option = new Option(servico.servico, servico.servico);
                            // Adiciona atributos data para armazenar informações adicionais
                            $(option).data('id_servico', servico.id_servico);
                            $(option).data('preco', servico.preco);
                            $(option).data('tempo', servico.tempo);
                            $('#frm-servico').append(option);
                            
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao buscar serviços: " + error);
                    }
                });
            });




        });

        $('#frm-servico').on('change', function() {
        
        var selectedOption = $(this).find(':selected');
        var id_servico = selectedOption.data('id_servico');
        var tempo = selectedOption.data('tempo');
        var valor = selectedOption.data('preco');
        $('#frm-id_servico').val(id_servico);
        $('#frm-tempo_min').val(tempo);
        var valorFormatado = valor.toString().replace('.', ','); // Substitui ponto por vírgula
        $('#frm-valor').val(valorFormatado);
        calcularFim()

        })  ;

</script>



<!--============DRAG AND DROP FUNÇÕES =====================-->

<script>
    document.addEventListener('DOMContentLoaded', function() { // aguarda o carregamento do DOM
        
        const agendaContainer = document.getElementById('agenda-container');
        
        agendaContainer.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('agendamento')) {
                e.dataTransfer.setData('text', e.target.getAttribute('data-id_agendamento'));
                e.target.style.opacity = '0.5';
                e.target.style.boxShadow = '-5px -5px 15px rgba(0, 200, 138, 0.5)';
                celulaOrigem = e.target.parentElement;
            }
        });

        agendaContainer.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('agendamento')) {
                e.target.style.opacity = ''; // Restaura a opacidade original
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
            let celulaDestino = e.target.closest('.celula');
 
            if (celulaDestino && agendamento) {
                
                        idNovoProf=celulaDestino.getAttribute('data-id_profissional');
                        idAntigoProf=agendamento.getAttribute('data-serv-id_profissional');
                        idServAg= agendamento.getAttribute('data-serv-id_servico');
                        nomeServicoAg = agendamento.getAttribute('data-serv-servico');
                $.ajax({
                            url: 'agenda_atendimentos/verificar_servico.php', // Caminho para o seu script PHP
                            type: 'POST',
                            data: {
                                idNovoProf: idNovoProf,
                                idServAg: idServAg
                            },
                            success: function(response) {
                                // Supondo que seu PHP retorne {"executa": true} ou {"executa": false}
                                var data = JSON.parse(response);
                                if (data.executa) {
                                    celulaDestino.appendChild(agendamento);
                                        document.getElementById('confirmModal').style.zIndex = '36000';
                                       
                                        $('#confirmModal').modal('show');
                                        
                                        document.getElementById('confirmChange').onclick = function() {
                                            realizarDrop(celulaDestino, agendamento);
                                            $('#confirmModal').modal('hide'); // Esconde o modal após a confirmação
                                        };

                                        document.getElementById('cancelChange').onclick = function(){
                                                celulaOrigem.appendChild(agendamento);
                                                $('#confirmModal').modal('hide'); // Esconde o modal após a confirmação
                                        };
                                    }else {
                                    alert("Este profissional não executa o serviço " + nomeServicoAg);
                                    celulaOrigem.appendChild(agendamento);
                                                $('#confirmModal').modal('hide');
                                    // Tratamento adicional para quando o profissional não executa o serviço
                                }
                            }
                });
            }
        }); 
                                
                                





    
        function realizarDrop(celulaDestino, agendamento) {
            const id_agendamento = agendamento.getAttribute('data-id_agendamento');
            const id_profissional = celulaDestino.getAttribute('data-id_profissional');
            const profissional = celulaDestino.getAttribute('data-profissional');
            const hora_agenda = celulaDestino.getAttribute('data-hora_agenda')+ ':00';
            const formData = new FormData();// Prepara os dados a serem enviados
            formData.append('id_agendamento', id_agendamento);
            formData.append('id_profissional', id_profissional);
            formData.append('profissional', profissional);
            formData.append('hora_agenda', hora_agenda);

            // Realiza a requisição para o script PHP
            fetch('agenda_atendimentos/update-agendamento.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // A atualização foi bem-sucedida, mova o agendamento para a nova célula
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

            // Limpa o efeito de hover da célula de destino
            celulaDestino.classList.remove('celula-hover');
        } 

    });

</script> <!--============DRAG AND DROP FUNÇÕES =====================-->





<!--    CALCULA HORA FIM DO SERVIÇO   -->
<script>


    function calcularFim() {
        var duracao = parseInt(document.getElementById('frm-tempo_min').value, 10); // Convertendo a string para número
        var inicio = document.getElementById('frm-hora_ini').value; // Supondo que frm-hora_ini seja um input do tipo "time" para a hora de início

        if (duracao && inicio) {
            var inicioDate = new Date('1970-01-01T' + inicio + 'Z'); // Cria uma data com a hora de início
            var fimDate = new Date(inicioDate.getTime() + duracao * 60000); // Adiciona duracao em milissegundos

            var fimHours = fimDate.getUTCHours().toString().padStart(2, '0'); // Formata as horas para dois dígitos
            var fimMinutes = fimDate.getUTCMinutes().toString().padStart(2, '0'); // Formata os minutos para dois dígitos

            // Atualiza o valor do campo de hora de fim com o novo horário calculado
            document.getElementById('frm-hora_fim').value = fimHours + ':' + fimMinutes;
        }
    }




</script>





<!--====================LISTA DE CLIENTES============-->
<script>

let clientes = []; // Array vazio para clientes

document.addEventListener('DOMContentLoaded', function() {
    var inputCliente = document.getElementById('frm-nome_cliente');
    var listaClientes = document.getElementById('lista-clientes');
    
    fetch('endPoints/get_clientes_dados_principais.php')
    .then(response => response.json())
    .then(dados => {
      clientes = dados; // Agora o array "clientes" já fica populado
    })
    .catch(error => {
      console.error('Erro ao buscar clientes:', error);
    });

    // Função para carregar clientes  cada abertuda do modal de agendamentos
    $('#modalAgendar').on('shown.bs.modal', function (e) {
        

        listaClientes.classList.remove('show');
        //carrega a lista dos dados principais do endPoint dos clientes para ficar diponível para o modal
        if (clientes.length === 0) { // Verifica se os clientes já foram carregados
            clientes = [];
            buscarCliente();
        }

        const select = document.getElementById('frm-profissional');
           
        select.innerHTML = '';// Limpa as opções existentes
        // Preenche o select com os usuários
        var option = document.createElement('option');
        option.textContent = "";
        select.appendChild(option);
            
            usuarios.forEach(usuario => {
                // Cria uma nova opção para o select
                option = document.createElement('option');
                option.value = usuario.id_profissional; // Define o valor da opção como o id do profissional
                option.textContent = usuario.profissional_ag; // Define o texto da opçso como o nome do profissional
                
                // Adiciona a nova opção ao select
                select.appendChild(option);
            });

            var idProfForm = document.getElementById('frm-id_profissional').value; 
                if (idProfForm != "") {
                    usuarios.forEach(usuario => {
                        if (idProfForm === usuario.id_profissional.toString()) { 
                            var selectProfissional = document.getElementById('frm-profissional');
                            // Cria uma nova opção
                            //var option = new Option(usuario.profissional_ag, usuario.id_profissional); não é necessário
                            // Adiciona a nova opção no select
                            selectProfissional.appendChild(option);
                            // seleciona a opção correspondente
                            selectProfissional.value = usuario.id_profissional;
                           // document.getElementById('frm-')
                            $('#frm-profissional').trigger('change');
                        }
                    });
                }
            
        




    });

    $('#modalAgendar').on('hide.bs.modal', function (e) {
        listaClientes.classList.remove('show');
        listaClientes.innerHTML = ''; // Opcional: Limpa o conteúdo da lista
        // Adicione aqui qualquer lógica adicional para resetar o estado do modal/formulário
    });

    inputCliente.addEventListener('input', function() { // adiciona um ouvinte o 
            document.getElementById('frm-lb-telefone_cliente').textContent = "";
            document.getElementById('frm-lb-cpf_cliente').textContent = "";
            document.getElementById('frm-lb-aniversario_cliente').textContent = "";
            document.getElementById('frm-lb-email_cliente').textContent = "";
            document.getElementById('frm-id_cliente').value = "";
            document.getElementById('frm-telefone_cliente').value = "";
            document.getElementById('frm-aniversario_cliente').value = "";
            document.getElementById('img-foto_cliente').src = "../img/sem-foto.svg";
            document.getElementById('frm-email_cliente').value = "";
            document.getElementById('ico-cad').className = '';   
            document.getElementById('ico-cad').classList.add('bi', 'bi-person-plus');
           // document.getElementById('btn-cadastrar_cliente').hidden=false; bi bi-person-plus bi bi-eye-fill

            

           
            document.getElementById('divFotoCliente').hidden=true;
            var termoPesquisa = inputCliente.value.toLowerCase();
            listaClientes.innerHTML = '';

        if (termoPesquisa.trim() === '') {
            return;
        }

        // Realiza uma consulta dinâmica na matriz clientes para buscar os clientes
                var clientesFiltrados = clientes.filter(function(cliente) {
                    return cliente.nome.toLowerCase().includes(termoPesquisa);
                });

                // Ordena os clientes em ordem alfabética
                clientesFiltrados.sort(function(a, b) {
                    return a.nome.localeCompare(b.nome);
                });

                // Limita aos 10 primeiros clientes após a filtragem e ordenação
                clientesFiltrados = clientesFiltrados.slice(0, 15);

                clientesFiltrados.forEach(function(cliente) {
                    var option = document.createElement('div');
                    option.classList.add('dropdown-item');
                    option.textContent = cliente.nome;
                    option.setAttribute('tabindex', '0');
                    
                    // Adiciona o evento de 'keydown' aqui, fora e antes do evento de 'click'
                    option.addEventListener('keydown', function(event) {
                        if (event.key === "Enter") {
                            event.preventDefault(); // Previne a ação padrão do Enter
                            option.click(); // Dispara o evento de clique definido abaixo
                        }
                    });

                    option.addEventListener('click', function() {
                        inputCliente.value = cliente.nome;

                        document.getElementById('frm-id_cliente').value = cliente.id;
                        document.getElementById('frm-telefone_cliente').value = cliente.celular;
                        document.getElementById('frm-cpf_cliente').value = cliente.cpf;
                        document.getElementById('frm-aniversario_cliente').value = cliente.aniversario;
                        document.getElementById('frm-email_cliente').value = cliente.email;
                        document.getElementById('frm-lb-telefone_cliente').textContent = "Telefone: " + cliente.celular;
                        document.getElementById('frm-lb-cpf_cliente').textContent = "CPF: " + cliente.cpf;
                        document.getElementById('frm-lb-aniversario_cliente').textContent = "Nascimento: " + formatData(cliente.aniversario);
                        if (cliente.foto){
                        document.getElementById('img-foto_cliente').src= "../img/clientes/" + cliente.foto;
                        }else{
                            document.getElementById('img-foto_cliente').src= "../img/sem-foto.svg"    
                        }
                        document.getElementById('frm-lb-email_cliente').textContent = "E-mail: " + cliente.email;
                        //document.getElementById('btn-cadastrar_cliente').hidden = true;
                        //document.getElementById('ico-cad_cliente').hidden=false;
                        document.getElementById('ico-cad').className = '';   
                         document.getElementById('ico-cad').classList.add('bi', 'bi-eye-fill');
                         // document.getElementById('btn-cadastrar_cliente').hidden=false; bi bi-person-plus bi bi-eye-fill
                        document.getElementById('divFotoCliente').hidden=false;

                        listaClientes.innerHTML = '';
                        listaClientes.classList.remove('show');
                    });
                    
                    listaClientes.appendChild(option);
                });

                if (clientesFiltrados.length > 0) {
                    listaClientes.classList.add('show');
                } else {
                    listaClientes.classList.remove('show');
                }
            })
            
            // Código para manipulação do input de pesquisa
            document.getElementById('frm-nome_cliente').addEventListener('keydown', function(event) {
                var listaClientes = document.getElementById('lista-clientes');
                var itensVisiveis = listaClientes.querySelectorAll('.dropdown-item');
                var indexFocado = Array.from(itensVisiveis).findIndex(item => item === document.activeElement);

                if (event.key === 'ArrowDown' && itensVisiveis.length > 0) {
                    event.preventDefault();
                    if (indexFocado < 0 || indexFocado === itensVisiveis.length - 1) {
                        itensVisiveis[0].focus();
                    } else {
                        itensVisiveis[indexFocado + 1].focus();
                    }
                } else if (event.key === 'ArrowUp' && itensVisiveis.length > 0) {
                    event.preventDefault();
                    if (indexFocado > 0) {
                        itensVisiveis[indexFocado - 1].focus();
                    } else {
                        itensVisiveis[itensVisiveis.length - 1].focus();
                    }
                } else if (event.key === 'Enter' && indexFocado >= 0) {
                    event.preventDefault();
                    itensVisiveis[indexFocado].click();
                }
            });

            // Código para manipulação do foco nos itens do dropdown
            document.getElementById('lista-clientes').addEventListener('keydown', function(event) {
                var itensVisiveis = this.querySelectorAll('.dropdown-item');
                var indexFocado = Array.from(itensVisiveis).findIndex(item => item === document.activeElement);

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    if (indexFocado < itensVisiveis.length - 1) {
                        itensVisiveis[indexFocado + 1].focus();
                    } else {
                        itensVisiveis[0].focus();
                    }
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (indexFocado > 0) {
                        itensVisiveis[indexFocado - 1].focus();
                    } else {
                        itensVisiveis[itensVisiveis.length - 1].focus();
                    }
                }
            });


           

});    

</script>

<!--==================================================================-->


<script>
function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
    
    if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    
    return "$r, $g, $b"; // Retorna o resultado no formato 'r, g, b'
  }
</script>



<script>
function buscarCliente() {
    var clienteEncontrado = clientes.find(function(cliente) {
        return cliente.id === agendamentoEncontrado.id_cliente;
    });

    // Verifica se encontrou o cliente e faz algo com ele
    if (clienteEncontrado) {
        console.log('Cliente encontrado:', clienteEncontrado);
    } else {
        // Se o cliente não foi encontrado, busca os dados dos clientes
        fetch('endPoints/get_clientes_dados_principais.php')
        .then(response => response.json())
        .then(dados => {
            clientes = dados; // Armazena os clientes carregados
            buscarCliente(); // Tenta buscar o cliente novamente
        }).catch(error => {
            console.error('Erro ao buscar clientes:', error);
        });
    }
}
//buscarCliente();
</script>
























<!--=========================== TOOL TIP =====================-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltip = document.getElementById('tooltip');

    document.body.addEventListener('mouseover', function(event) {
        var agendamento = event.target.closest('.agendamento');
        if (!agendamento) return;

        var observacoes = agendamento.getAttribute('data-serv-observacoes');
        var cliente = agendamento.getAttribute('data-serv-cliente');
        var dataAgenda = agendamento.getAttribute('data-serv-dataAgenda');
        var servico = agendamento.getAttribute('data-serv-servico');
        var hora = agendamento.getAttribute('data-serv-hora');
        var telefone = agendamento.getAttribute('data-serv-telefone');
        var status = agendamento.getAttribute('data-serv-status');

        var dataFormatada = new Date(dataAgenda).toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        var horaFormatada = hora.substring(0, 5);

        var tooltipContent = `
            <p><b>Cliente:</b> ${cliente}</p>
            <p><b>Data de Agendamento:</b> ${dataFormatada}</p>
            <p><b>Hora:</b> ${horaFormatada}</p>
            <p><b>Serviço:</b> ${servico}</p>
            <p><b>Telefone:</b> ${telefone}</p>
            <p><b>Status:</b> ${status}</p>
            <p><b>Observações:</b> ${observacoes}</p>
        `;

        tooltip.innerHTML = tooltipContent;
        
        var posX = event.pageX - 80;
        var posY = event.pageY - 230; // Posiciona um pouco abaixo do cursor
        tooltip.style.left = posX + 'px';
        tooltip.style.top = posY + 'px';
        tooltip.style.display = 'block';

        setTimeout(function() {
            tooltip.style.opacity = '1';
        }, 30);
    });

    document.body.addEventListener('mouseout', function(event) {
        if (!tooltip) return;
        tooltip.style.opacity = '0';
        // Espera a transição terminar antes de definir display: none
        setTimeout(function() {
            if (tooltip.style.opacity === '0') {
                tooltip.style.display = 'none';
            }
        }, 300); // Este valor deve corresponder ao tempo da transição CSS
    });
});
</script>









<script>

const menu = document.getElementById('custom-menu');
let hideMenuTimeout = null;
let isMenuVisible = false;
let selectedAgendamentoId = null; // Guarda o ID do agendamento clicado

const offsetX = 15;
const offsetY = 15;

function showMenu(x, y) {
  clearTimeout(hideMenuTimeout);

  menu.style.display = 'block';
  menu.style.opacity = '1';
  menu.style.top = `${y + offsetY}px`;
  menu.style.left = `${x + offsetX}px`;

  isMenuVisible = true;

  hideMenuTimeout = setTimeout(hideMenuSmoothly, 3000);
}

function hideMenuSmoothly() {
  menu.style.opacity = '0';
  isMenuVisible = false;

  setTimeout(() => {
    menu.style.display = 'none';
  }, 300);
}

// Captura clique direito no agendamento
document.addEventListener('contextmenu', function(e) {
  const agendamento = e.target.closest('.agendamento');

  if (agendamento) {
    e.preventDefault();

    // Pega o ID do agendamento e guarda
    selectedAgendamentoId = agendamento.getAttribute('data-id_agendamento');
    console.log('Agendamento selecionado:', selectedAgendamentoId);

    showMenu(e.pageX, e.pageY);
  } else {
    hideMenuSmoothly();
  }
});

document.addEventListener('click', function(e) {
  if (!e.target.closest('#custom-menu')) {
    hideMenuSmoothly();
  }
});

menu.addEventListener('mouseenter', function() {
  clearTimeout(hideMenuTimeout);
});

menu.addEventListener('mouseleave', function() {
  hideMenuTimeout = setTimeout(hideMenuSmoothly, 3000);
});

// Clique nas opções do menu
menu.addEventListener('click', function(e) {
  const menuItem = e.target.closest('.menu-item');

  if (menuItem && selectedAgendamentoId) {
    const status = menuItem.getAttribute('data-status');
    console.log(`Alterando status do agendamento ${selectedAgendamentoId} para "${status}"`);

    // Chamada AJAX
    fetch('agenda_atendimentos/alterar_status.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id_agendamento=${encodeURIComponent(selectedAgendamentoId)}&status=${encodeURIComponent(status)}`
    })
    .then(response => response.text())
    .then(data => {
      console.log('Resposta do servidor:', data);
      hideMenuSmoothly();
      console.log('carregando Agenda');
      carregarAgenda(dataCalend, agCancelados);
      // Aqui tu pode adicionar um reload da tabela ou mensagem de sucesso
    })
    .catch(error => {
      console.error('Erro na requisição:', error);
    });
  }
});


</script>


