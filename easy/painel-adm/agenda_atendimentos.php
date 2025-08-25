<?php 
	$pag = 'agenda_atendimentos';
	@session_start();

	require_once('../conexao.php');
	require_once('verificar-permissao.php');
	$dataAgenda = date('Y-m-d');

function hexToRgba($hex, $alpha = 0.5) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "rgba($r, $g, $b, $alpha)";
}

// exemplo de uso
$corHex = "#FF6600";
$corSAgendadoBx = hexToRgba($corSAgendado, 0.7); // 50% de opacidade
?>

<script>
  // Converte a data PHP para uma string JSON e atribui a uma variável JS
let dataCalend = <?php echo json_encode(date('Y-m-d')); ?>;

</script>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<head>


<link rel="stylesheet" href="agenda_atendimentos/agenda.css?v=4.23" type="text/css">

<style>
/*______________AGENDA_______________*/

.celula.out-agenda{
    background-color:<?=$cor_n_atende?>;
}
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
        
        
#meuCalendario{
  border:1px solid <?php echo $cor_linha_horizontal?>;
  position: sticky;
  top: 0;
  width: 250px;
  height: 250px;
  padding-left:1px;
}

.janela-cliente-dados:hover p{
  font-style: italic;
  font-size: 12px;
  color:#716EF6;
  
}

.janela-cliente-dados p{
  font-size: 12px;
  margin: 1px 2px 1px;
  height: 15px;
  font-weight: 400;
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

.celula-hover { /* somente para o js */
                background-color: <?php echo $cor_celula_selecionada?>;
                color:white;
}
.agenda-easy-td-horario:hover{
    background-color: <?php echo $cor_celula_selecionada?>;/*#6bd3d1;*/
    color:#fff;
    border-radius: 3px;
}



.btn-minimizar{
  color:<?php echo $cor_fonte_head_form?>;
}
/*________________STATUS___________________________*/

/*-------------PARA A AGENDA GERAL 'não funciona dentro do css'----------------*/

            /* Estilos específicos para cada status */
            .statusAgendado {  background-color: <?php echo $corSAgendado ?>; }
            .statusConfirmado {  background-color: <?php echo  $corSConfirmado?>; }
           
            .statusAguardando {  background-color: <?php echo  $corSAguardando?>; }
            .statusNRealizado {  background-color: <?php echo  $corSNaoRealizado?>; }
            .statusAtendimento {  background-color: <?php echo  $corSAtendimento?>; }
            .statusConcluido {  background-color: <?php echo  $corSConcluido?>; }

            .statusFinalizado { background-color: <?php echo  $corSFinalizado?>; }
            .statusFaltou { background-color: <?php echo  $corSFaltou?>; }
            .statusCancelado {
               background-color: <?php echo  $corSCancelado?>;
            
                background-image: repeating-linear-gradient(
                    120deg,           /* ângulo da diagonal */
                   rgb(175, 175, 175),            /* cor da linha */
                   rgb(175, 175, 175) 12px,        /* fim da linha */
                    transparent 8px, /* início do espaço */
                    transparent 20px  /* fim do espaço */
                );
            }


/* CSS JANELA TESTE AGENDAMENTOS*/

.bloqueio{background-color: <?php echo $cor_bloqueio ?>;}



.input-group.flex-nowrap{
min-width: 110px;


}


.agendamento-virtual {
  box-shadow: 0 0 20px <?php echo $corSAgendado ?>;
  background-color : <?php echo $corSAgendadoBx ?>;
}


.cliente-click-card:hover{
cursor: pointer;
font-style: italic;
}


.etiqueta-agenda-sala {
  position: absolute;
  bottom: 3px;
  right: 3px;
  background: #fff;
  color: #000;
  font-size: 8px;
  padding: 1px 4px;
  margin-bottom: 1px;
  border-radius: 3px;
  pointer-events: none;
  display: flex;
  align-items: center;
  height: 12px; /* ou ajuste conforme o seu card */
  /* opcional, só para não ficar estreito demais */
  font-weight: 500; /* opcional, se quiser mais destacado */
}



.etiqueta-agenda-pagamentos {
    position: absolute !important;
    top: 0; /* desloca para cima do topo */
    right: -5px; /* desloca para a direita fora do container */
     /* cor de fundo (vermelho exemplo) */
    
    font-size: 8px;
    font-weight:500;
    padding: 1px 5px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    z-index: 4100; /* maior que o z-index do agendamento */
    pointer-events: none; /* não interfere nos cliques */
    
}

.etiqueta-agenda-lembrete {
    /*position: absolute !important;*/
    top: 0; /* desloca para cima do topo */
    right: -5px; /* desloca para a direita fora do container */
     /* cor de fundo (vermelho exemplo) */
    
    font-size: 10px;
    font-weight:500;
    padding: 1px 5px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    z-index: 4100; /* maior que o z-index do agendamento */
    pointer-events: none; /* não interfere nos cliques */
      display: inline-block;
}


.etiquetaStatusPago{
    background-color:rgb(4, 112, 44);
    color: #fff;
     border:1px solid rgb(255, 255, 255);
}
.etiquetaStatusPendente{
    background-color:rgb(255, 255, 255);
    color:red;
    border:1px solid red;
}
.etiquetaStatusFinalizado{
    background-color:rgb(0, 97, 233);
    color:rgb(255, 255, 255);
}
.etiquetaStatusNRealizado{
  background-color:rgb(0, 0, 0);
  color:rgb(255, 255, 255);
}
.etiquetaStatusFaltou{
  background-color:rgb(0, 0, 0);
  color:rgb(255, 255, 255);
}
.etiquetaStatusCancelado{
  background-color:rgb(0,0,0);
  color:rgb(255, 255, 255);
}

.ico-menuAg{

padding: 1px 3px;
border-radius: 5px;
color:#fff;


}

.etiqueta-bloqueio {
    position: absolute !important;
    top: 5px; /* desloca para cima do topo */
    right: 5px; /* desloca para a direita fora do container */
    background:rgb(11, 11, 11); /* cor de fundo (vermelho exemplo) */
    color: #fff;
    font-size: 8px;
    font-weight:500;
    padding: 1px 5px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    z-index: 4100; /* maior que o z-index do agendamento */
    pointer-events: none; /* não interfere nos cliques */
    
}

.bloqueio-titulo {
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
  font-size: 11px;
  text-align: center;
  height: 100%; /* Ocupa o bloco inteiro se quiser centralizar vertical e horizontal */
  padding: 4px 2px 2px 2px;
  color: <?=$cor_fonte_bloqueio?>;
  word-break: break-word;
}


.celula{
    overflow: visible !important;
    position: relative;
}

.agendamento{
  overflow: visible;
}

.erro-modal-msg {
  color: #fff;
  background: #dc3545;
  font-size: 13px;
  border-radius: 3px;
  padding: 4px 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  white-space: nowrap;
  display: none;
}

.erro-modal-msg.visivel {
  display: block;
}

.bloqueio-virtual {
  opacity: 0;
  transition: opacity 0.3s;
}

.bloqueio-virtual.visivel {
  opacity: 1;
}

.label-semana{

  font-size: 8px;
}
.col-semana{
  width:31px;
}

.form-control.input{
  cursor:pointer;
}

.prof-bl-card {
  display: inline-block;
  width: 65px;
  text-align: center;
  margin: 0 8px 12px 0;
  position: relative;
}

.prof-bl-img-wrapper {
  position: relative;
  width: 40px;
  height: 40px;
  margin: 0 auto;
}

.prof-bl-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 2px 8px #0002;
  display: block;
}

.prof-bl-checkbox {
  position: absolute;
  top: -7px;
  right: -7px;
  z-index: 2;
  width: 13px;
  height: 13px;
  /*//background: #fff;*/
  border-radius: 50%;
  /*box-shadow: 0 2px 6px #0001;*/
  
  cursor: pointer;
  transition: border-color 0.2s;
}

.prof-bl-nome {
  font-size: 11px;
  margin-top: 5px;
  line-height: 1.2;
  word-break: break-word;
}


        .bloqueio {
                color: <?= $cor_fonte_bloqueio ?>;
                border-radius: 8px;
                
                cursor: pointer;
                opacity: <?=$opacicidade_bloqueio?>;
                overflow: hidden;
                box-shadow: inset 0 0 55px 8px rgba(255, 255, 255, 0.1);
                box-shadow: 1px 15px 25px rgba(248, 248, 248, 0.3);
                transition: box-shadow 0.6s ease-in-out, opacity 0.6s ease-in-out;
                border : <?=$size_borda_bloqueio?>px dashed <?=$cor_borda_bloqueio?>;
        }

            .bloqueio:hover {
            
               /* color: black;*/
                /*border: 1px solid green; /* Corrigido de 'margin' para 'border' */
                opacity:<?=$opacicidade_bloqueio + 0.2?>;
               /* z-index: 5000;*/
                /*width: 90px;*/
                box-shadow: -1px -1px 10px rgba(10, 53, 146, 0.5);
            }
                
            .bloqueio-p {
                
                text-align: left;
                color: red;
                font-size: 10.5px;
                margin: 3px;
                position: static;
                text-align: left;
               
            }


.contador-label {
  display: block;
  font-size: 0.65em;
  color: #888;
  margin-top: 2px;
  text-align: right;
}

.input-borda-vermelha {
  border: 2px solid #e53935 !important;
  transition: border 0.2s;
}


.menu-whatsapp {
  position: relative;
}

.menu-whatsapp > .submenu-whatsapp {
  display: none;
  position: absolute;
  top: 0;
  left: 100%; /* padrão: abre à direita */
  min-width: 170px;
  background: #fff;
  border: 1px solid #e1e1e1;
  box-shadow: 0 2px 14px rgba(0,0,0,.14);
  z-index: 10;
  border-radius: 8px;
  padding: 5px 0;
  transition: opacity .15s;
}

.menu-whatsapp.open-left > .submenu-whatsapp {
  left: auto;
  right: 100%;
}

.menu-whatsapp:hover > .submenu-whatsapp,
.menu-whatsapp:focus-within > .submenu-whatsapp {
  display: block;
}

.menu-status {
  position: relative;
}

.menu-status > .submenu-status {
  display: none;
  position: absolute;
  top: 0;
  left: 100%; /* padrão: abre à direita */
  min-width: 170px;
  background: #fff;
  border: 1px solid #e1e1e1;
  box-shadow: 0 2px 14px rgba(0,0,0,.14);
  z-index: 10;
  border-radius: 8px;
  padding: 5px 0;
  transition: opacity .15s;
}

.menu-status.open-left > .submenu-status {
  left: auto;
  right: 100%;
}

.menu-status:hover > .submenu-status,
.menu-status:focus-within > .submenu-status {
  display: block;
}


.statusTooltip{
  padding: 3px;
  color:#fff;
  border-radius: 4px;
  font-size: 12px;

}

.submenu-item {
  padding: 10px 22px;
  font-size: 15px;
  cursor: pointer;
  transition: background .15s;
}

.submenu-item:hover {
  background: #ebfbf1;
}




.glow-on-hover {
    width: 40px;
    height: 32px;
    border: none;
    outline: none;
    color: #fff;
    background: #111;
    cursor: pointer;
    position: relative;
    z-index: 0;
    border-radius: 8px;
}

.glow-on-hover:before {
    content: '';
    background: linear-gradient(45deg, #ff0000, #ff7300, #fffb00, #48ff00, #00ffd5, #002bff, #7a00ff, #ff00c8, #ff0000);
    position: absolute;
    top: -2px;
    left:-2px;
    background-size: 400%;
    z-index: -1;
    filter: blur(5px);
    width: calc(100% + 4px);
    height: calc(100% + 4px);
    animation: glowing 20s linear infinite;
    opacity: 0;
    transition: opacity .3s ease-in-out;
    border-radius: 8px;
}

.glow-on-hover:active {
    color: #000
}

.glow-on-hover:active:after {
    background: transparent;
}

.glow-on-hover:hover:before {
    opacity: 1;
}

.glow-on-hover:after {
    z-index: -1;
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background: #111;
    left: 0;
    top: 0;
    border-radius: 8px;
}


.ico-receber-agenda{

height: 25px;

}
@keyframes glowing {
    0% { background-position: 0 0; }
    50% { background-position: 400% 0; }
    100% { background-position: 0 0; }
}


.vc-week__day{ width: 25px;}
.vc-date{ width:25px; height:25px;}
.vc-date__btn{width:25px; height:25px;}


#data-extenso-agenda{
  font-weight: 700; 
  font-size:12px; 
  text-align: center;
}


.btn-data-hoje{
    width:25px;
    height:25px;
}
#botaoHoje{
  background-color: <?php echo $cor_icons ?> ;
  color:#fff;
  width: 80px;
  height: 32px;
  border-radius: 8px;
padding-bottom:1px;
}



.cal-number {
  position: absolute;
  top: 10px;    /* Ajuste conforme necessário para centralizar */
  left: 13px;   /* Ajuste conforme necessário para centralizar */
  font-size: .65rem;
  font-weight: 500;
  color: #fff;
  pointer-events: none;
  text-align: left;

}




.fadeIn {
  transition: opacity 1.1s ease;
  opacity: 1;
}

.hidden {
  opacity: 0;
  pointer-events: none;
}






button.vc-date__btn{
  min-width: 10px; 
  min-height: 10px;
  border-radius: 5px;

  /*font-size: 0.5rem;*/
}
div.vc-date{
  min-width: 15px; min-height: 15px;
}
b.vc-week__day{
  min-width:10px;
  max-width:10px;
}




</style>


<meta name="viewport" content="width=device-width, initial-scale=1.0">
  
</head>



<body>

   <!--====================== SUBMENU =======================================-->
    
    <?php  gerarMenu($pag, $grupos); ?>

       
                                   
  <div class="container-fluid">
    <div class="row"> 
      <div id="calend-principal" class = "col-auto" >
        <div id="meuCalendario">
         
        </div>
          <script>
           
              document.addEventListener('DOMContentLoaded', () => {
                function calendarioHoje(){
                const { Calendar } = window.VanillaCalendarPro;
                const options = {
                  firstWeekday: 0,
                  selectedWeekends: [0],
                  selectedTheme: 'light',
                  locale: 'pt-br',
                };
                const calendar = new Calendar('#meuCalendario', options);
                calendar.init();

                setTimeout(() => {
                  // Encontra a div do dia atual
                  const divHoje = document.querySelector('#meuCalendario .vc-date[data-vc-date-today]');
                  if (divHoje) {
                    // Marca como selecionado
                    if (!divHoje.hasAttribute('data-vc-date-selected')) {
                      divHoje.setAttribute('data-vc-date-selected', '');
                    }
                    // Pega o texto por extenso do botão filho
                    const btnHoje = divHoje.querySelector('.vc-date__btn');
                    const dataHojeExt = btnHoje ? btnHoje.getAttribute('aria-label') : '';
                    const diaIcone = document.getElementById('dia-data-hoje');
                    diaIcone.textContent= btnHoje.textContent;
                    // Mostra na div de baixo
                    document.getElementById('data-extenso-agenda').innerText = 'Hoje - '+ dataHojeExt;

                  }
                }, 300); // Aguarda renderizar
              }

              calendarioHoje();
              $('#botaoHoje').on('click', function() {
                  calendarioHoje();
              });

              });
              
          </script>



        <div class="" id="data-extenso-agenda" style="text-align: center;">
             
        </div>
        

      </div>


      <div class="col" style="min-width: 480px;">
        <div class="row" >
          <div class="col mb-2 " id="calend-opcional" style="max-width:180px; min-width:130px;">
            <input  class="form-control"   id="calendario" name="calendario" value="<?php echo $dataAgenda ?>" type="date" >
          </div>
           <div class="col-auto mb-2">
            <button id="botaoHoje" onclick="carregarAgenda(dataHoje, agCancelados)" style="position: relative;">
              <i class="bi bi-calendar" style="position:relative; right: 21px; top: 2px; font-size: 1.2rem;"></i>
              <span class="cal-number" style="text-align:center; width:11px;" id="dia-data-hoje"></span>
              <span class="" style="position:absolute; font-size: 14px; right:15px; top:7px;" id="">Hoje</span>
            </button>
          </div>
          <div class="col mb-2" style="max-width:280px; min-width:200px;">
            <div class="input-group" >
              <a id="agTabShow" class="btn btn-secondary btn-span" >
                <i class="bi bi-card-list" style="padding-left:6px; padding-top: 2px;"></i>
              </a>
              <input placeholder="pesquisar na agenda" type="text" id="searchAgenda" class="form-control">
            </div>
          </div>
          <div class="col mb-2" style="max-width: 60px;">
            <button class="glow-on-hover" id="botaoVenderAgenda" onclick="abrirModal('modalVendas', '0', 'venda')">
              <i class="bi bi-credit-card-fill ico-receber-agenda" id= "icoReceber"></i>
            </button>
          </div>
          <div class="col mb-2">
            <button class="btn btn-secondary" id="btn-lembrete-agenda" style=" width:40px; height: 32px; padding: 2px; background-color:#716EF6; align-items: center">
             <i class="bi bi-calendar-heart" style="margin-left: 4px; color:#fff; text-align:center;"></i><!-- Ícone de adição do Bootstrap -->
            </button>
          </div>

          
          <div class="col-auto mb-2 ms-auto">
            <button class="btn btn-secondary" id="botaoCancelados" onClick="mostrarAgCancelados()" style="width:40px; height: 32px; padding: 2px;">
              <i class="bi bi-eye-slash" id= "icoBtCancelados"></i> <!-- Ícone de adição do Bootstrap -->
            </button>
          </div>

		


        </div>
        <div class="row">
          <div id="agenda-container" class= "agenda-container fade" style="align-content: flex-start; width: 100%;">          
          <!-- =====AGENDA CARREGA AQUI ======-->
        </div>
      </div>


      <div class="container my-4">
        <div class="row g-3 hidden" id="cards-container">
          <!-- Os cards vão ser inseridos pelo JS -->
        </div>
      </div>

      <div id="tabelaAgendamentos" class="hidden" style="align-content: flex-start; width: 100%;">
        <table class="dataTable">
          <thead>
            <tr>
              <th  scope="col" class="sortable" data-coluna="hora">Hora</th>
              <th  scope="col" class="sortable" data-coluna="nome_cliente">Cliente</th>
              <th  scope="col" class="sortable" data-coluna="servico">Serviço</th>
              <th  scope="col"class="sortable" data-coluna="profissional_1">Profissional</th>
              <th scope="col">Observações</th>
              <th scope="col" class="sortable" data-coluna="status">Status</th>
              <th  scope="col"  class="sortable" data-coluna="preco">Preço</th>
              
            </tr>
          </thead>
          <tbody id="agendaBody">
          <!-- Os dados serão inseridos aqui via JavaScript -->
          </tbody>
        </table>
      </div>
      <div id="tooltip-ag" class = "tooltipAgenda tooltipAgenda-agenda">
      <!-- =====tolltip com o js ======-->
      </div>

      <div id="tooltip-prof" class = "tooltipAgenda">
      <!-- =====tolltip com o js ======-->
      </div>
          <input type="text" id="agendamentosDoDia" hidden>
      </div>
    </div>
  </div>
</body> <!--=======================================Fim da página===================--> 



<!--===================================MODAL EQUIPAMENTOS======================================-->
 <div class="modal fade" tabindex="-1" style="z-index:25000;"role="dialog" id="modEquipamentos">
  <div class="modal-dialog" style="top:40%; left:40%; position:fixed; "  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Selecione o Equipamento</h5>
        <button type="button"  class="btn-close" id = "fechaModalEquipamentos" data-bs-dismiss="modal" aria-label="Fechar">
         
        </button>
      </div>
      <?php
      $query = $pdo->query("SELECT* from equipamentos");
        $equipamentos = $query->fetchAll(PDO::FETCH_ASSOC);
   
      ?>
      <div class="modal-body">
        <div class="conteiner-fluid">
          <input type="hidden" id="idAgendamentoModEquipamentos">
          <select class="form-select" id="selectEquipamento">
            <?php foreach ($equipamentos as $equipamento){
                echo '<option value="'.$equipamento['id'].'">'.$equipamento['nome'].'</option>

            '; }?>
            
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary"  id="gravaEquipamento">Grava</button>
        <button type="button" class="btn btn-secondary"  id="cancelChange" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

 

<!--===================================MODAL SALAS======================================-->
 <div class="modal fade" tabindex="-1" style="z-index:25000;"role="dialog" id="modSalas">
  <div class="modal-dialog" style="top:40%; left:40%; position:fixed; "  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Selecione o a Sala</h5>
        <button type="button"  class="btn-close" id = "fechaModalSalas" data-bs-dismiss="modal" aria-label="Fechar">
         
        </button>
      </div>
      <?php
      $query = $pdo->query("SELECT* from salas");
        $salas = $query->fetchAll(PDO::FETCH_ASSOC);
   
      ?>
      <div class="modal-body">
        <div class="conteiner-fluid">
          <input type="hidden" id="idAgendamentoModSalas">
          <select class="form-select" id="selectSala">
            <?php foreach ($salas as $sala){
                echo '<option value="'.$sala['id'].'">'.$sala['nome'].'</option>

            '; }?>
            
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary"  id="gravaSala">Grava</button>
        <button type="button" class="btn btn-secondary"  id="cancelaModSala" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>



<!--===================================MODAL CANCELAMENTO/FALTA======================================-->
 <div class="modal fade" tabindex="-1" style="z-index:25000;" id="modCancel">
  <div class="modal-dialog modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="headModCancel" class="modal-title">Informe o Motivo</h5>
        <button type="button"  class="btn-close" id = "fechaModalCancel" data-bs-dismiss="modal" aria-label="Fechar">
         
        </button>
      </div>
      <div class="modal-body">
        <div class="conteiner-fluid">
          <input type="hidden" id="idAgendamentoModCancel">
          <input type="hidden" id="statusAgendamentoModCancel">
          <textarea class="form-control"  autocomplete="false" style="height:90px;" id="textAgendamentoModCancel" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <div id="erroModalMsg" class="erro-modal-msg"></div>
        <button type="submit" class="btn btn-primary"  id="gravaCancel">Grava</button>
        <button type="button" class="btn btn-secondary"  id="cancelaModCancel" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>



<!--===================================MODAL CONFIRMAÃO CANCELAMENTO/FALTA QUANDO POSSUI ATENDIMENTO ABERTO ======================================-->
 <div class="modal fade" tabindex="-1" style="z-index:25000;" id="modConfirmExc">
  <div class="modal-dialog modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="headModConfirmExc" class="modal-title">ATENÇÃO</h5>
        <button type="button"  class="btn-close" id = "fechaModalConfirmExc" data-bs-dismiss="modal" aria-label="Fechar">
         
        </button>
      </div>
      <div class="modal-body">
        <div class="conteiner-fluid">
          <p>Já existe atendimento aberto para este agendamento, ao Confirmar, <b>todos os arquivos e informações serão apagados.<b></p>
        </div>
      </div>
      <div class="modal-footer">
        <div id="erroModalMsg" class="erro-modal-msg"></div>
        <button type="submit" class="btn btn-primary"  id="gravaConfirmExc">Confirmar</button>
        <button type="button" class="btn btn-secondary"  id="cancelaModConfirmExc" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>



<!--===================================MODAL ATENDIMENTO AGENDA======================================-->
 <div class="modal fade" tabindex="-1" style="z-index:25000;" id="modAtAgenda">
  <div class="modal-dialog modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="headModAendimento" class="modal-title">PRONTUÁRIO DO ATENDIMENTO</h5>
        <button type="button"  class="btn-close" id = "fechaModalAtendimento" data-bs-dismiss="modal" aria-label="Fechar">
         
        </button>
      </div>
      <div class="modal-body">
        <div class="conteiner-fluid">
          <input type="hidden" id="IHinddenModAtAg">
          <div class = "row">
            <div class="col-auto" style="width: 100px;">
              <div class="row mb-3" id="row-hora_iniAt">
                  <label class="form-group">Inicio:</label>
                  <input id="inputAtHIni" class="form-control inputHora">
              </div>
              <div class="row mb-3"id="row-hora_fimAt">
                  <label class="form-group">Fim:</label>
                  <input id="inputAtHFim" class="form-control inputHora">
              </div>
            </div>
            <div class="col" >
              <label class="form-group">Evolução</label>
              <textarea id="textoProntuario" class="form-control" style="height:100px;"></textarea>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <div id="erroModalMsg" class="erro-modal-msg"></div>
        <button type="submit" class="btn btn-primary"  id="gravaAtendimento">Grava</button>
        <button type="button" class="btn btn-secondary"  id="cancelaModAtendimento" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>





<script>




document.querySelectorAll('.inputHora').forEach(function(input) {
  input.addEventListener('input', function(e) {
   maskHora(e.target); // Chama a função passando o input
  });
});

</script>













<!--================================== Menu Agendamentol===============================-->
<div id="custom-menu" class="menu-agenda" style="min-width:240px">
  <div class="menu-item menu-status" style="display:flex; align-items:center; justify-content:space-between; position:relative;">
    <span style="display:flex; align-items:center;">
      <i class="bi bi-person-fill-check" style="margin-right:10px;"></i>
      Alterar Status
    </span>
    <i class="fa-solid fa-angle-right" style="margin-left:10px;"></i>
    <!-- Submenu lateral -->
    <div class="submenu-status">
      <div data-status="Agendado" class="menu-item status-confirmado">
        <i class="bi bi-calendar-plus statusAgendado ico-menuAg" style="margin-right:8px;"></i> Agendado
      </div>
      <div data-status="Confirmado" class="menu-item status-confirmado">
        <i class="bi bi-patch-check  statusConfirmado ico-menuAg" style="margin-right:8px;"></i> Confirmado
      </div>
      <div data-status="Cancelado" class="menu-item status-cancelado">
        <i class="bi bi-x-circle  statusCancelado ico-menuAg" style="margin-right:8px;"></i> Cancelar
      </div>
      <hr>
      <div data-status="Aguardando" class="menu-item status-aguardando">
        <i class="bi bi-hourglass-split statusAguardando ico-menuAg" style="margin-right:8px;"></i> Aguardando
      </div>
      <div data-status="Faltou" class="menu-item status-faltou">
        <i class="bi bi-exclamation-circle statusFaltou ico-menuAg" style="margin-right:8px;"></i> Faltou
      </div>
      <div data-status="NRealizado" class="menu-item status-n-realizado">
        <i class="bi bi-slash-circle  statusNRealizado ico-menuAg" style="margin-right:8px;"></i> Não Realizado
      </div>
    </div>
  </div>
  <hr>
  <div data-status="Atendimento" class="menu-item status-atendimento" style="display:flex; align-items:center;">
    <i class="bi bi-activity  statusAtendimento ico-menuAg" style="margin-right:10px;"></i> Iniciar Atendimento
  </div>
  <div data-status="Concluido" class="menu-item status-concluido" style="display:flex; align-items:center;">
    <i class="bi bi-check2-square statusConcluido ico-menuAg" style="margin-right:10px;"></i> Atendimento Concluído
  </div>
  <div data-status="Finalizado" class="menu-item status-finalizado" style="display:flex; align-items:center;">
    <i class="bi bi-trophy statusFinalizado ico-menuAg" style="margin-right:10px;"></i> <b>Finalizar</b>
  </div>
  <hr>
  <div data-vender="venda" class="menu-item" style="display:flex; align-items:center;">
    <i class="fa-sharp-duotone fa-solid fa-credit-card" style="margin-right:10px;"></i> <b>Pagamento</b>
  </div>
  <hr>
  <div class="menu-item menu-whatsapp" style="display:flex; align-items:center; justify-content:space-between; position:relative;">
    <span style="display:flex; align-items:center;">
      <i class="bi bi-whatsapp" style="color:#25d366; margin-right:10px;"></i> WhatsApp
    </span>
    <i class="fa-solid fa-angle-right" style="margin-left:10px;"></i>
    <!-- Submenu lateral -->
      <div class="submenu-whatsapp">
        <?php
          $query = $pdo->query("SELECT id, nome FROM agenda_mensagens WHERE mostrar_menu = 1 ORDER BY nome");
          while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $nome = htmlspecialchars($row['nome']);
            echo "<div class='menu-item' data-id-mensagem='{$id}'>{$nome}</div>";
          }
        ?>
      </div>
    
  </div>
  <hr>
  <div data-equipamento="mEquipamento" class="menu-item menu-equipamento" style="display:flex; align-items:center;">
    <i class="bi bi-hdd-network" style="margin-right:10px;"></i> Informar Equipamento
  </div>
  <div data-sala="mSala" class="menu-item menu-sala" style="display:flex; align-items:center;">
    <i class="bi bi-door-open" style="margin-right:10px;"></i> Informar Sala
  </div>
</div>



<!--================================== Menu Topo profissional===============================-->
<div id="menu-prof" class="menu-agenda" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; z-index:10000;">
  <div data-option="Bloquear" class="menu-item bloquear-agenda">Bloquear Horário</div>
  <hr>
  <div data-option="Editar" class="menu-item">Propriedades</div>
  <div data-option="Mensagem" class="menu-item">Enviar Mensagem</div>
</div>

<!--================================== Menu linha de horário===============================-->
<div id="menu-linha" class="menu-agenda" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; z-index:10000;">
  <div data-option="bloquear" class="menu-item">Bloquear Horários</div>
  <hr>
 </div>

<!--================================== Menu celula da agenda===============================-->
<div id="menu-celula" class="menu-agenda" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; z-index:10000;">
  <div data-option="novo-agendamento" class="menu-item">Novo Agendamento</div>
  <hr>
  <div data-option="bloquear-horario"class="menu-item">Bloquear Horário</div>
</div>

<!--================================== Menu Bloqueio===============================-->
<div id="menu-bloqueio" class="menu-agenda" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; z-index:10000;">
  <div data-option="alterar-bloqueio" class="menu-item">Alterar</div>
  <hr>
  <div data-option="excluir" class="menu-item">Excluir Bloqueio</div>
</div>







<script type="text/javascript" src="agenda_atendimentos/efeitos_agenda.js?v=0.06"></script>



<!--------============= MODAL AGENDAMENTOS - CARREGAMENTO - TABELA   ===============-->







<div class="janela janela-agendamento" id="janela-agendamentos" hidden >
    <div class="modal-header" id="janela-header">
        <span class="modal-title">Agendamentos do Paciente</span>
        <div>
            <button class="btn-minimizar btn-head-modal" style="padding-right: 20px;" id="btn-minimizar-janela"><i class="bi bi-dash-lg"></i></button>
            <button class="btn-head-modal" id="btn-fechar-janela"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>
    <div id="corpo-janela">
        <div class="janela-body" id="janela-body">
            <div class="container">
                <div class="row" style="min-height:30px; margin-left:25px; border-radius: 8px;" id="dados-cliente">
                    <div class="col-auto mb-2 mt-2" id="div-foto-cliente" style="width: 80px;">
                        <img style="cursor: pointer; border-radius:50%;  width:70px; margin-left:-8px; padding-top:3px;" src="" id="janela_foto_cliente" name="img-foto_cadCliente">
                    </div>

                    <div class="col  mb-2 mt-2" id="div-dados-cliente" style="min-width:280px; max-width:330px;">
                        <div class="client-search">
                            <input type="hidden" class="client-search__id" id="janela_id_cliente" name="janela_id_cliente">
                            
                            <div class="input-group client-search__wrapper">
                                <input 
                                    type="text" 
                                    class="form-control client-search__input"
                                    id="janela_nome_cliente" 
                                    autocomplete="off"   
                                    name="nome-cliente" 
                                    placeholder="Nome" 
                                    value="">  
                                <button 
                                        class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="btn-adicionar-cliente"
                                        style="width:38px;border: none;"
                                        onclick="abrirModal('modalClientes', document.getElementById('janela_id_cliente').value)">
                                        <i class="bi client-search__icon bi-person-plus" id="ico-abrir-cliente"></i>
                                </button>
                            </div>
                            <ul id="lista-clientes-janela" class="client-search__list"></ul>
                        </div>
                    </div>
                    

                </div>
                <div class="row" >
                    
                        <div class="tab-pane fade show active mb-3" id="aba-itens" role="tabpanel" aria-labelledby="itens-tab" >
                        
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table id="tabela-itens" class="table table-striped">
                                    <thead>
                                        <tr>
                                          <th ><button <?= $bloquearCampos ? 'hidden' : '' ?>type="button" id="adicionar-item" class="btn btn-success centBt">+</button></th>
                                          <th style="display: none;">ID</th>
                                          <th style= "min-width: 100px; padding-left: 15px;">Data:</th>
                                          <th style= "min-width: 100px;">Profissional:</th>
                                          <th style= "min-width: 100px;">Serviço:</th>
                                          <th style= "min-width: 100px;">Preço:</th>
                                          <th style= "min-width:70px;">Início:</th>
                                          <th style= "min-width:70px;">Tempo:</th>
                                          <th style= "min-width:70px;">Fim:</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabela-agendamentos-janela">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="container-fluid">
                          <label class="form-group" >Descrição:</label>

                          <textarea id="agendamento-descricao" style="min-height:90px;" name="agendamento-descricao"class="form-control"></textarea>

                        </div>


                    
                </div>
            </div>
        </div>
    
                        
        <div class="modal-footer mb-4">
          <div class="container-fluid">
            <div class="d-flex w-100 justify-content-between align-items-center">
              <!-- Mensagem à esquerda -->
              <div id="mensagem-janela" class="text-danger" style="border-radius: 5px;"></div>
              <!-- Botões à direita -->
              <div>
                <button id="btn-cancelar-janela" class="btn btn-cancelar me-2">Cancelar</button>
                <button class="btn btn-salvar" id="btn-salvarAgendamento">Salvar</button>
              </div>
            </div>
          </div>
        </div>


    </div>

</div>











<!--=======================================JANELA DE BLOQUEIOR ===============================-->

<div class="janela janela-bloqueio" id="janela-bloqueios" hidden>
    <div class="modal-header" id="bloqueios-header">
        <div class="header-left">
            <i class="bi bi-ban"></i>
            <span class="modal-title" style="margin-left: 15px;">Bloqueios</span>
        </div>
        <div class="header-right">
            <button class="btn-minimizar btn-head-modal" style="padding-right: 20px;" id="btn-minimizar-janelaBl">
                <i class="bi bi-dash-lg"></i>
            </button>
            <button class="btn-fechar-modal" id="btn-fechar-janelaBl">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div id="corpo-janelaBl">
        <div class="janela-body" id="janela-bodyBl">
            <div class="container">
                <div class="row">
                    <div class="col-auto mb-3" id="div-Prof-bl">
                      <input type="hidden" id="id-bloq-car">
                      <label class="form-group">Profissional</label>
                      <select id="select-profissionalBl" style="min-width: 200px;" class="form-select">
                        
                      </select>
                
                  </div>
                  <div class="col-auto" id="foto-prof-container">

                  </div>
                </div>

                 <hr class="mt-1 mb-3">

                 <div class="row" id="controles-tempo-bl" >                    
                    <div class="col-auto mb-3 position-relative" id="coluna-data-bloqueio">
                        <label class="form-group">Data</label>
                        <input id="periodo-bloqueio" type="text" class="form-control">
                        <i class="bi bi-calendar3 calendar-icon"></i>
                      </div>
                    
                    <div class="col-auto">
                      <label class="form-group">Inicio:</label>
                      <input type="text" id="bl-h-ini" class="form-control input-hora" >
                    </div>
                    <div class="col-auto">
                      <label class="form-group">Fim:</label>
                      <input type="text" id="bl-h-fim" class="form-control input-hora" >
                    </div>
                    <div class="col-auto">
                      <label class="form-group">Tempo(min):</label>
                      <input type="text" id="bl-h-tempo" style="min-width:60; width:70px;" class="form-control" >
                    </div>
                    <div class="col-auto">
                      <div class="form-check" style="margin-top: 15px;">
                        <input class="form-check-input" type="checkbox" value="" id="chk-bl-dia">
                        <label class="form-check-label" for="chk-bl-dia">
                          Todo dia
                        </label>
                      </div>
                    </div>
                

                </div>

                <div class="row" hidden="true" id="linha-semana-block"style="width: 230px;">
                    <div class="col col-semana">
                      
                       <label class="label-semana" for="chk-bl-dia">
                          Dom
                        </label>
                        <input class="form-check-input" type="checkbox" value="" id="chk-bl-diaDom">
                    </div>
                   
                    <div class="col col-semana">
                        <label class="label-semana" for="chk-bl-dia">
                          Seg
                        </label>
                        <input class="form-check-input" type="checkbox" value="" id="chk-bl-diaSeg">
                    </div>

                    <div class="col col-semana">
                       <label class="label-semana" for="chk-bl-dia">
                          Ter
                        </label>
                        <input class="form-check-input" type="checkbox" value="" id="chk-bl-diaTer">
                    </div>

                    <div class="col col-semana">
                        <label class="label-semana" for="chk-bl-dia">
                          Qua
                        </label>
                        <input class="form-check-input" type="checkbox" value="" id="chk-bl-diaQua">
                    </div>

                    <div class="col col-semana">
                       <label class="label-semana" for="chk-bl-dia">
                          Qui
                        </label>
                        <input class="form-check-input" type="checkbox" value="" id="chk-bl-diaQui">
                    </div>

                    <div class="col col-semana">
                        <label class="label-semana" for="chk-bl-dia">
                          Sex
                        </label>
                        <input class="form-check-input" type="checkbox" value="" id="chk-bl-diaSex">
                    </div>
                    <div class="col col-semana">
                        <label class="label-semana" for="chk-bl-dia">
                          Sab
                        </label>
                        <input class="form-check-input" type="checkbox" value="" id="chk-bl-diaSab">
                    </div>
                </div>
                <hr class="mt-1">
                  <div class="row">
                    <div class="col-auto mb-3">
                      <label class="form-group" for="titulo-bloqueio">Título:</label>
                      <input type="text" class="form-control" style="max-width:500px; min-width:320px;" id="titulo-bloqueio">
                      <span id="contagem-titulo" class="contador-label">0/50</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <label class="form-group" for="descricao-bloqueio">Descrição:</label>
                      <textarea class="form-control" style="width:100%; min-width:320px; height:60px;" id="descricao-bloqueio"></textarea>
                      <span id="contagem-descricao" class="contador-label">0/300</span>
                    </div>
                  </div>
                <hr class="mt-2">






              </div>
            </div>
        </div>
    
                        
        <div class="modal-footer mb-4" style="background-color: <?php echo $cor_fundo_form ?>;">
          <div class="container-fluid">
            <div class="d-flex w-100 justify-content-between align-items-center">
              <!-- Mensagem à esquerda -->
              <div id="mensagem-janelaBl" class="text-danger">

              </div>
              <!-- Botões à direita -->
              <div>
                <button id="btn-cancelar-janelaBl" class="btn btn-cancelar me-2">Cancelar</button>
                <button class="btn btn-salvar" id="gravar-bloqueio">Salvar</button>
              </div>
            </div>
          </div>
        </div>


    </div>

</div>


<!--=========TABELA DE PESQUISA DA AGENDA ===================-->
<script src="js/agenda_tabela.js?v=0.48" defer></script>


<!--============= ATUALIZA PROFISSIONAIS DO DIA =========================-->
<script src="js/agenda_atualiza_profissionais.js?v=0.03" defer></script>

<!--=============CARREGA AGENDA =========================-->
<script src="js/agenda_carregar.js?v=0.48" defer></script>

<!--======FUNÇÕES DOS MENUS DO AGENDAMENTO =======-->
<script src="js/agenda_menus.js?v=2.17" defer></script>

<!--=================== TOOL TIPS  ===========================-->
<script src="js/agenda_tooltips.js?v=0.43" defer></script>

<!--=========== ARRASTA E SOLTA =====================-->
<script src="js/agenda_drag_drop.js?v=0.55  " defer></script>



<!--=FUNÇÕES DEABERTURA E FECHAMENTO DA JANELA DO AGENDAMENTO ==-->
<script src="js/agenda_janela.js?v=0.90" defer></script>


<script src="js/chamaCliente.js?v=0.34" defer></script>


<script src="js/janelaClienteInfo.js?v=0.13" defer></script>


<!--================AGENDAMENTO VIRTUAL ===============-->
<script> 
      const alturaLinha = <?php echo json_encode($altura_linha_agenda); ?>;
      const intervaloMin = <?php echo json_encode($intervalo_tempo_agenda) ?>;
</script>
<script src="js/agenda_agendamento_virtual.js?v=0.06" defer></script>
<!--============================================-->





<!--=====FUNÇÕES MARCADOR DE TEMPO ==-->
<script src="js/agenda_marca_tempo.js?v=0.03" defer></script>


<!--======================BLOQUEIOS ===========================-->
<script src="js/agenda_bloqueios.js?v=0.02" defer></script>








<script>


document.querySelectorAll('.menu-whatsapp').forEach(item => {
  item.addEventListener('mouseenter', function(e) {
    item.classList.remove('open-left', 'open-right');
    const rect = item.getBoundingClientRect();
    const submenu = item.querySelector('.submenu-whatsapp');
    const submenuWidth = submenu.offsetWidth || 180;
    const spaceRight = window.innerWidth - rect.right;
    const spaceLeft = rect.left;

    if (spaceRight < submenuWidth + 10 && spaceLeft > submenuWidth + 10) {
      item.classList.add('open-left');
    } else {
      item.classList.remove('open-left');
    }
  });
  item.addEventListener('mouseleave', function(e) {
    item.classList.remove('open-left', 'open-right');
  });
});


document.querySelectorAll('.menu-status').forEach(item => {
  item.addEventListener('mouseenter', function(e) {
    item.classList.remove('open-left', 'open-right');
    const rect = item.getBoundingClientRect();
    const submenu = item.querySelector('.submenu-status');
    const submenuWidth = submenu.offsetWidth || 180;
    const spaceRight = window.innerWidth - rect.right;
    const spaceLeft = rect.left;

    if (spaceRight < submenuWidth + 10 && spaceLeft > submenuWidth + 10) {
      item.classList.add('open-left');
    } else {
      item.classList.remove('open-left');
    }
  });
  item.addEventListener('mouseleave', function(e) {
    item.classList.remove('open-left', 'open-right');
  });
});







function tempoFormatado(min) {
  if (!min || min <= 0) return "";
  if (min >= 60) {
    const horas = Math.floor(min / 60);
    const minutos = min % 60;
    return minutos ? `${horas}h ${minutos}min` : `${horas}h`;
  }
  return `${min}min`;
}


function calcularPeriodo(procedimentos) {
  if (!procedimentos.length) return "";

  procedimentos.sort((a, b) => a.hora.localeCompare(b.hora));
  const inicio = procedimentos[0].hora;
  const ultimo = procedimentos[procedimentos.length - 1];
  const fim = calcularHoraFim(ultimo.hora, ultimo.tempo_min);

  const [hIni, mIni] = inicio.split(':').map(Number);
  const [hFim, mFim] = fim.split(':').map(Number);
  const minutos = (hFim * 60 + mFim) - (hIni * 60 + mIni);

  const duracao = minutos > 0 ? tempoFormatado(minutos) : "";

  return { inicio, fim, duracao };
}

























async function getLembretesEnviados(dataCalend) {
  const resposta = await fetch('agenda_atendimentos/get_lembretes.php?data=' + dataCalend);
  return resposta.json();
}
async function getLembreteConfig() {
  const mensagem = await fetch('agenda_atendimentos/get_lembretes_config.php?');
  return mensagem.json();
}

let lembretes;
let lembreteConfig;

async function renderCards(agendamentos) {
 
 
  const clientesAgrupados = agruparAgendamentosPorCliente(agendamentos);
 
  const container = document.getElementById('cards-container');
  container.innerHTML = "";

  const exibeBotao = dataCalend >= dataHoje;

  lembretes = await getLembretesEnviados(dataCalend);
  lembreteConfig = await getLembreteConfig();
 
  const lembretesEnviados = {};
  lembretes.forEach(l => lembretesEnviados[l.id_cliente] = l.enviado);

  clientesAgrupados.forEach(ag => {

        const { inicio, fim, duracao } = calcularPeriodo(ag.procedimentos);
        const enviado = lembretesEnviados[ag.id_cliente] ? 'checked' : '';

          let procedimentosHTML = '';
          ag.procedimentos.forEach(proc => {
            procedimentosHTML += `
              <li class="list-group-item px-2 py-1 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <span class="badge bg-light text-dark me-2">${proc.hora}</span>
                  <strong class="me-2">${proc.servico}</strong>
                  <span class="etiqueta-agenda-lembrete ${getClasseStatus(proc)} ms-1">${getStatusEtiqueta(proc)}</span>
                  <small class="text-muted fst-italic ms-2">${tempoFormatado(proc.tempo_min)}</small>
                </div>
                <span class="small text-muted">${proc.profissional}</span>
              </li>
                `;
          });


          container.innerHTML += `
            <div class="masonry-item">
              <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                  <div class="d-flex align-items-center mb-3">
                    <img src="../${pastaFiles}/img/clientes/${ag.foto_cliente || 'sem-foto.svg'}" alt="${ag.nome_cliente}" class="rounded-circle me-3" width="48" height="48" style="object-fit:cover;">
                    <div>
                      <h5 class="card-title mb-0 cliente-click-card" onclick="abrirModal('modalClientes', ${ag.id_cliente})">${ag.nome_cliente}</h5>
                      <small class="text-muted">${inicio} - ${fim} ${duracao ? `(${duracao})` : ""}</small>
                    </div>
                  </div>
                  <ul class="list-group list-group-flush mb-3">
                    ${procedimentosHTML}
                  </ul>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                      ${exibeBotao ? `
                        <button class="btn btn-success btn-sm btn-enviar-whatsapp"
                                data-id_agendamento="${ag.id_cliente}">
                          <i class="bi bi-whatsapp me-1"></i>Enviar Lembrete
                        </button>
                      ` : `<span class="text-muted small">Agenda Concluída</span>`}
                      <div class="form-check mb-0">
                        <input class="form-check-input enviado-checkbox" type="checkbox" value="" id="enviado-${ag.id_cliente}" data-id_cliente="${ag.id_cliente}" ${enviado}>
                        <label class="form-check-label small" for="enviado-${ag.id_cliente}">Enviado</label>
                      </div>
                    </div>
                </div>
              </div>
            </div>
          `;
      });

      document.querySelectorAll('.btn-enviar-whatsapp').forEach(btn => {
        btn.addEventListener('click', function() {
          const id_cliente = this.getAttribute('data-id_agendamento');
          // Busca o cliente pelo id_cliente no array agrupado
          const cliente = clientesAgrupados.find(c => c.id_cliente == id_cliente);
          if (cliente) {
            enviarLembreteCliente(cliente);
          } else {
            alert('Cliente não encontrado!');
          }
        });
      });

      document.querySelectorAll('.enviado-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
          const id_cliente = this.getAttribute('data-id_cliente');
          const enviado = this.checked ? 1 : 0;
          fetch('agenda_atendimentos/set_lembretes.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id_cliente, data: dataCalend, enviado})
          });
        });
      });


}

// Inicializa os cards

function getStatusEtiqueta(proc) {
  // Copia a lógica do PHP para JS
  if(proc.status === 'Finalizado') return 'Finalizado';
  if(proc.status === 'Faltou') return 'Faltou';
  if(proc.status === 'NRealizado') return 'NRealizado';
  if(proc.status === 'Cancelado') return 'Cancelado';

  const quantidade = proc.quantidade;

  if (quantidade && quantidade > 0) return 'Pago';
  if (quantidade && quantidade < 1) return 'Pendente';
  if (!quantidade) return 'Pendente';
  return '';
}

function getClasseStatus(proc) {
  const status = getStatusEtiqueta(proc);
  // Mapeie para as classes CSS (personalize ao seu gosto)
  switch (status) {
    case 'Pago': return 'etiquetaStatusPago';
    case 'Pendente': return 'etiquetaStatusPendente';
    case 'Finalizado': return 'etiquetaStatusFinalizado';
    case 'Faltou': return 'etiquetaStatusFaltou';
    case 'NRealizado': return 'etiquetaStatusNRealizado';
    case 'Cancelado': return 'etiquetaStatusCancelado';
    default: return '';
  }
}



function agruparAgendamentosPorCliente(agendamentos) {
    const clientes = {};

    agendamentos.forEach(ag => {

        if (ag.status != 'Cancelado' && ag.status!='Faltou' && ag.status!='NRealizado' && !ag.bloqueio){
            const id = ag.id_cliente;
            if (!clientes[id]) {
                clientes[id] = {
                    id_cliente: ag.id_cliente,
                    nome_cliente: ag.nome_cliente,
                    foto_cliente: ag.foto_cliente,
                    telefone_cliente: ag.telefone_cliente,
                    email_cliente: ag.email_cliente,
                    sexo: ag.sexo,
                    procedimentos: []
                };
            }
            clientes[id].procedimentos.push({
                id: ag.id,
                hora: ag.hora,
                tempo_min: ag.tempo_min,
                servico: ag.servico,
                profissional: ag.profissional_1,
                status: ag.status,
                sala: ag.sala,
                preco: ag.preco,
                quantidade: ag.quantidade
            });


      }
    });

    // Ordena os procedimentos de cada cliente pela hora
    Object.values(clientes).forEach(cliente => {
        cliente.procedimentos.sort((a, b) => a.hora.localeCompare(b.hora));
    });

    // Retorna como array para fácil iteração
    return Object.values(clientes);
}



// Exemplo de uso do atributo ao clicar no botão











function gerarListaProcedimentos(procedimentos, config) {
  let lista = '';

  procedimentos.forEach(proc => {
    let linha = '';

    if (config.mostrar_horario_procedimento) {
      linha += (proc.hora || '').slice(0, 5) + ' - ';
    }

    linha += proc.servico;

    if (proc.quantidade > 1) {
      linha += ` (x${proc.quantidade})`;
    }

    if (config.mostrar_profissional) {
        const primeiroNome = proc.profissional.split(' ')[0];
        linha += `, com ${primeiroNome}`;

    }

    if (config.mostrar_tempo_procedimento) {
      linha += ` - ${proc.tempo_min}min`;
    }

    if (config.mostrar_preco && proc.preco) {
      linha += ` - R$ ${DecimalBr(proc.preco)}`;
    }

    if (config.mostrar_status && proc.status) {
      linha += ` [${proc.status}]`;
    }

    if (config.mostrar_etiqueta_pagamento && typeof getStatusEtiqueta === 'function') {
      linha += ` (${getStatusEtiqueta(proc)})`;
    }

    lista += '• ' + linha.trim() + '\n';
  });

  return lista.trim();
}








function enviarLembreteCliente(cliente) {
  const listaProcedimentos = gerarListaProcedimentos(cliente.procedimentos, lembreteConfig);
  const periodo = calcularPeriodo(cliente.procedimentos);
  
  const msgTemplate = lembreteConfig.mensagem; // Ex: "Olá {nome}, ... {lista_procedimentos} ..."

            var nomeCompletoMsg = cliente.nome_cliente;
            var primeiroNomeMsg = nomeCompletoMsg.trim().split(' ')[0];
            var dataMsg = formatarDataBr(dataCalend);
            var dataMsgExt = dataPorExtenso(dataCalend, true);
            //var horaAgendamentoMsg = agendamento.getAttribute('data-serv-hora').slice(0,5);
            //var servicoMsg = agendamento.getAttribute('data-serv-servico');
            //var ProfMsg = agendamento.getAttribute('data-serv-nome_profissional').trim().split(' ')[0];
            //var precoServMsg = DecimalBr(agendamento.getAttribute('data-serv-preco'));
            var telefoneMsg = cliente.telefone_cliente;
            var sexoCliente = cliente.sexo;

            
            if (sexoCliente =='m'){
              sexoMsg1='o';
              sexoMsg2='ao';
              sexoMsg3='ele';
            }else{
              sexoMsg1='a';
              sexoMsg2='a';
              sexoMsg3='ela';
            }
            let plural='';

            if (cliente.procedimentos.length > 1){
              plural='s'
            }

            
          // Tags principais
          const variaveis = {
                    nome: primeiroNomeMsg,
                    nomecompleto: nomeCompletoMsg,
                    sexo: sexoMsg1,
                    sexo2: sexoMsg2,
                    sexo3: sexoMsg3,
                    telefone: telefoneMsg,
                    dataextenso: dataMsgExt,
                    data: dataMsg,
                    nomeclinica: 'Jess Corporal',
                    enderecoclinica: 'Av. Assis Brasil, 4550',
                    whatsappclinica: '51985706133',
                    instagramclinica: '@jesscorporal',
                    lista_procedimentos: listaProcedimentos,
                    s: plural,
                    entrada: periodo.inicio,   // hora início do 1º procedimento
                    saida: periodo.fim,         // hora de término do último
                    duracao: periodo.duracao 
          };

    let mensagemPronta = prepararMensagem(lembreteConfig.mensagem, variaveis);

    if (!lembreteConfig.mensagem.includes('{lista_procedimentos}')) {
      mensagemPronta += '\n\n' + listaProcedimentos;
    }

    enviarWhatsapp(cliente.telefone_cliente, mensagemPronta);
}






function prepararMensagem(template, variaveis) {
  return template.replace(/\{(\w+)\}/g, (match, key) => variaveis[key] ?? '');
}











function enviarWhatsapp(numero, mensagem) {
  let numeroLimpo = numero.replace(/\D/g, '');
  if (!numeroLimpo.startsWith('55')) {
    numeroLimpo = '55' + numeroLimpo;
  }
  const mensagemUrl = encodeURIComponent(mensagem);
  const url = `https://api.whatsapp.com/send?phone=${numeroLimpo}&text=${mensagemUrl}`;
  window.open(url, '_blank');
}




</script>


































