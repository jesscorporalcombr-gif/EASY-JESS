<?php 
@session_start();
require_once('../conexao.php');
require_once('verificar-permissao.php');

require_once ('personalizacoes/personalizacao_agenda.php');
require_once ('personalizacoes/personalizacao_sistema.php');

require_once ('subMenu.php');
include('loadMenu.php');

$pasta = $_SESSION['x_url'] ?? '';
$sem_foto = "../img/sem-foto.svg";
$sem_imagem = "../img/sem-foto2.jpg";


//RECUPERAR DADOS DO USUÁRIO QUE ESTÁ ACESSANDO

$query = $pdo->query("SELECT nickname, nome, email, senha_sistema, foto_sistema, cpf, id from colaboradores_cadastros WHERE id = '$_SESSION[id_usuario]'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nickname_user = $res[0]['nickname'];
$nome_user = $res[0]['nome'];
if (empty($nickname_user)){
  $nickname_user = $nome_user;
}


$email_user = $res[0]['email'];
$senha_user = $res[0]['senha_sistema'];
//$nivel_user = $res[0]['nivel'];
$foto_sistema_user = '../'.$pasta.'/img/users/' . $res[0]['foto_sistema'];
$cpf_user = $res[0]['cpf'];
$id_user = $res[0]['id'];

?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Painel Administrativo</title>
    <script>
       const pastaFiles = <?php echo json_encode($pasta) ?>;
    </script>

    <!-- ------------------------------------------------------------------------------------------- -->

    <!-- ------------------------------------------------------------------------------------------- -->



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="shortcut icon" href="../img/logo.png" />
    
    <link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css?v=0.01"> <!-- MANTER    -->
    
    <link rel="stylesheet" href="../css/style.css?v=0.06">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
     
    <script type="text/javascript" src="../js/numeros.js?v=0.10"></script> <!--formata os numeros por classe-->
    
    <script type="text/javascript" src="js/modalsOpen.js?v=1.10"></script>
    
    <script type="text/javascript" src="js/senha.js"></script>
    
    <script type="text/javascript" src="js/tabelas.js?v=1.14"></script>
    <!--<script type="text/javascript" src="js/tabelas2.js?v=0.23"></script>-->


    <script type="text/javascript" src="js/validacoes.js?v=0.86"></script> <!-- meu-->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
   <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
   
 
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    
  <link href="https://cdn.jsdelivr.net/npm/vanilla-calendar-pro/styles/index.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vanilla-calendar-pro/index.js" defer></script>
    
    <script src="js/tabelasExample.js?v=0.13"></script>


    <!-- SweetAlert2 JS -->
      <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
   <script src="js/ext-icon-map.js?v=0.1"></script>

   <link  href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<link rel="stylesheet" href="css/style.css?v=5.17">

<style>

/* ------SUB MENU BARRA 3 -------*/
.sub-menu .ativo {
    font-size: 14px;
    font-weight: 600;
    color: <?php echo $cor_fonte_barra3 ?>;
}

.sub-menu .inativo{
    font-size: 12px;
    font-weight: normal;
    color: <?php echo $cor_fonte_barra3 ?>;
}

.sub-menu a:hover{
  font-weight: bold;
}

.sub-menu {
    height: 30px;
    background-color: <?php echo $cor_barra3 ?>;
    padding-left: 35px;
}

/*-------------------------------*/
.btn-fecha-modal {
    color:<?php echo $cor_fonte_head_form?>;
   
}
.btn-head-modal{
  color:<?php echo $cor_fonte_head_form?>;
}

@media (max-width: 768px) {
    .navbar {
       display: none; /* Esconde a barra de submenu em telas pequenas */
    }
    .navbar-toggler {
        display: block; /* Mostra o botão de colapso em telas pequenas */
    }
}


ul.nav-tabs .tab-btn {
       color:<?php echo $cor_fonte_fundo_form ?>;
}

/* Estilo para o estado ativo */


@media (max-width: 380px) { /* Ajusta o ponto de quebra conforme necessário */
    .icon-group {
        margin-bottom: 10px; /* Espaçamento vertical em telas menores */
        margin-right: 10; /* Remove o espaçamento lateral em telas pequenas */

    }
    .navbar-toggler {
        margin-left: 20px; /* Alinha o botão toggler à direita em telas menores */
       
    }
}

.navbar-toggler {
        margin-left: 20px; /* Alinha o botão toggler à direita em telas menores */
        background-color: <?php echo $cor_background ?>;
        z-index: 50000;
}

#navbarSupportedIcons.show {
    background-color: <?php echo $cor_background ?>; /* Cor de fundo cinza claro */
    z-index: 50000;
}

.nav.nav-tabs{
  background-color: <?= $cor_fundo_form?>;
}

/* ------MODAL ------*/
.modal-content{
  border: 1px solid <?php echo $cor_head_form?> !important;
}

.modal-title {
    color:<?php echo $cor_fonte_head_form?>; 
}

.btn-fecharl{
 color: <?php echo $cor_fonte_head_form?>;
}

.modal-header {
    background-color: <?php echo $cor_head_form ?>;
    color: <?php echo $cor_fonte_head_form ?>; 
}

.modal-body{
  background-color: <?php echo $cor_fundo_form ?>;

}
.modal-title {
    color:<?php echo $cor_fonte_head_form ?>; 
}
.modal-footer{
  background-color: <?php echo $cor_rodape_form ?>;
  color:<?php echo $cor_fonte_rodape_form ?>; 
}

/*----------------------------------------*/



.tab-btn{
  height:30px; padding: 2px 12px 2px 12px;
}

.btn-primary{
  background-color: <?php echo $cor_btn_enviar?>;
  color: <?php echo $cor_fonte_btn_enviar?>;
  border-color:<?php echo $cor_btn_enviar?>
}
.btn-primary:hover{
  background-color: <?php echo $cor_btn_enviar?>;
  /*color: <?php echo $cor_fonte_btn_enviar?>;*/
  border-color:<?php echo $cor_fonte_btn_enviar?>
}

.btn-secondary{
  background-color: <?php echo $cor_btn_fechar?>;
  color: <?php echo $cor_fonte_btn_fechar?>;
  border-color:<?php echo $cor_btn_fechar?>
}
.btn-secondary:hover{
  background-color: <?php echo $cor_btn_fechar?>;
  /*color: <?php echo $cor_fonte_btn_fechar?>;*/
  border-color:<?php echo $cor_fonte_btn_fechar?>
}

.btn-add{
  background-color: <?php echo $cor_btn_add?>;
  color: <?php echo $cor_fonte_btn_add?>;
  border-color:<?php echo $cor_btn_add?>
}
.btn-add:hover{
  background-color: <?php echo $cor_btn_add?>;
  /*color: <?php echo $cor_fonte_btn_add?>;*/
  border-color:<?php echo $cor_fonte_btn_add?>
}



/*----BARRA DOS ICONES -------*/

.med-bar{
  background-color: <?php echo $cor_barra2?>; 
  height: calc(<?php echo $size_icons . 'px';?> + 10px);
  display: flex;          /* Flexbox para alinhar os itens horizontalmente */
  flex-direction: column; /* Define a direção dos itens como coluna se eles devem ser empilhados verticalmente */
  align-items: stretch;   /* Faz com que os itens filhos estiquem para corresponder ao maior filho */
  padding: 10px; 
  
}
.icon-group {
  padding-left: <?php echo $espaco_entre_icons ?>px; /* Ajuste de acordo com o layout */
  padding-right: <?php echo $espaco_entre_icons ?>px;
  margin-top: 5px; /* Espaçamento entre ícones para evitar sobreposição */
}

.icon-group .bi, .icon-group .fa {
  font-size: <?php echo $size_icons ?>px; /* Tamanho do ícone */
  margin-bottom: 0px; /* Espaço entre o ícone e o texto */
  line-height: <?php echo $size_icons ?>px;
  color: <?php echo $cor_icons ?>;
}

.gp-ativo .ico-bar {
  transform: rotate(10deg); /* Gira 45 graus */
  transition: transform 0.3s ease; /* Adiciona uma transição suave */
  filter: drop-shadow(0 0 3px rgba(255, 255, 255, 0.9));
  font-size: <?php echo $size_icons *120/100 ?>px;

}

.ico-bar:hover {
     filter: drop-shadow(0 0 7px  <?php echo $cor_fonte_icons?>99);
}

.ico-font {
color: <?php echo $cor_fonte_icons?>;
}

.gp-ativo .ico-font {
/*transform: scale(1.3);*/
border-bottom: 2px solid <?php echo $cor_fonte_icons?>;
font-weight: 500;
}

/*-------------------------------------------*/


/*-------BARRA DO TOPO---------------*/
.top-bar {
background-color: <?php echo $cor_barra_topo ?>;
height: calc(<?php echo $size_icons_barra_topo . 'px'; ?> + 15px);
}

.med-line{
  border:none;
  border-top:1px solid <?php echo $cor_linha_barra?>; 
  opacity: 1;
  margin-top:-3px;
}

/*-------------------------------------------*/



.listTabContainer table {
border-collapse: collapse;
border-radius: 15px;
border-spacing: 0px;
box-shadow: -1px -1px 10px rgba(10, 53, 146, 0.5);
}

.listDataTable th {
background-color:<?php echo $cor_head_tabelas?>;
color: <?php echo $cor_fonte_head_tabelas?>;
cursor:pointer;
padding-left:10px;
font-size: 14px;
}
.backGround-dataTable-head{
background-color:<?php echo $cor_head_tabelas?>;

}
.text-dataTable-head{
  color: <?php echo $cor_fonte_head_tabelas?>;
}


.listDataTable td {
font-size: 12px;
padding: 5px 10px 3px 10px;
border:1px solid  <?php echo $cor_linha_par?>;
}

.listDataTable th .listDataTable td {
  border-radius: 15px;
  text-align: left; 
}

.listTabContainer .form-control {
height: 25px;
}

.listTabContainer .form-select {
height: 25px;
min-width: 60px;
}
 
.listDataTable tr:nth-of-type(odd) {
    background-color: <?php echo $cor_linha_impar?>; /* Insira a cor de fundo desejada */
 }

.listDataTable tr:nth-of-type(even) {
    background-color: <?php echo $cor_linha_par?>;
}

.listDataTable tr:hover {
  background-color:<?php echo $cor_secundaria?>;
}



#dataTable tr:last-of-type {
    border-bottom: 2px solid <?php echo $cor_head_tabelas?>;
}

#dataTable tr:nth-of-type(odd) {
    background-color: <?php echo $cor_linha_impar?>; /* Insira a cor de fundo desejada */
}

#dataTable tr:nth-of-type(even) {
    background-color: <?php echo $cor_linha_par?>;
}

#dataTable th {
    background-color:<?php echo $cor_head_tabelas?>;
    color: <?php echo $cor_fonte_head_tabelas?>;
}

#dataTable tr {
    color: <?php echo $cor_fonte_tabela?>;
}
#dataTable a {
    color: <?php echo $cor_fonte_tabela?>;
}
#dataTable tr:hover {
    background-color:<?php echo $cor_secundaria?>;
}

/*---------------tabelas2.js-------------------*/

.dataTable tr:last-of-type {
    border-bottom: 2px solid <?php echo $cor_head_tabelas?>;
}

.dataTable tr:nth-of-type(odd) {
    background-color: <?php echo $cor_linha_impar?>; /* Insira a cor de fundo desejada */
}

.dataTable tr:nth-of-type(even) {
    background-color: <?php echo $cor_linha_par?>;
}

.dataTable th {
    background-color:<?php echo $cor_head_tabelas?>;
    color: <?php echo $cor_fonte_head_tabelas?>;
}

.dataTable tr {
    color: <?php echo $cor_fonte_tabela?>;
}
.dataTable a {
    color: <?php echo $cor_fonte_tabela?>;
}
.dataTable tr:hover {
    background-color:<?php echo $cor_secundaria?>;
}








#example tr:last-of-type {
    border-bottom: 2px solid <?php echo $cor_head_tabelas?>;
}

#example tr:nth-of-type(odd) {
    background-color: <?php echo $cor_linha_impar?>; /* Insira a cor de fundo desejada */
}
#example tr:nth-of-type(even) {
    background-color: <?php echo $cor_linha_par?>;
}

#example th {
    background-color:<?php echo $cor_head_tabelas?>;
    color: <?php echo $cor_fonte_head_tabelas?>;
}

#example tr {
    color: <?php echo $cor_fonte_tabela?>;
}
#example a {
    color: <?php echo $cor_fonte_tabela?>;
}
#example tr:hover {
    background-color:<?php echo $cor_secundaria?>;
}







.alt-table tr:last-of-type {
    border-bottom: 2px solid <?php echo $cor_head_tabelas?>;
}

.alt-table tr:nth-of-type(odd) {
    background-color: <?php echo $cor_linha_impar?>; /* Insira a cor de fundo desejada */
}
.alt-table tr:nth-of-type(even) {
    background-color: <?php echo $cor_linha_par?>;
}

.alt-table th {
    background-color:<?php echo $cor_head_tabelas?>;
    color: <?php echo $cor_fonte_head_tabelas?>;
}

.alt-table tr {
    color: <?php echo $cor_fonte_tabela?>;
}
.alt-table a {
    color: <?php echo $cor_fonte_tabela?>;
}
.alt-table tr:hover {
    background-color:<?php echo $cor_secundaria?>;
}









#tableServProf th {
    background-color:<?php echo $cor_head_tabelas?>;
    color: <?php echo $cor_fonte_head_tabelas?>;
}



.page-number-button {
  color: <?php echo $cor_btn_padrao?>;}

.active-page {
    background-color: <?php echo $cor_btn_padrao?>;
    color: #ffffff;
   
  }




.form-group{
  color: <?php echo $cor_fonte_fundo_form?>;

}
.ico-b1{
height: <?php echo $size_icons_barra_topo ?>px; 
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

</style>

  </head>






<!-- ============================ CORPO ============================================= -->


<body style = "background-color:<?php echo $cor_background?>;">



<!--================ PARTE SUPERIOR  menu logo, aviso e usuario ======================== -->

<div class="top-bar">
<nav class="navbar navbar-expand-sm navbar-light" style="height: calc(<?php echo $size_icons_barra_topo . 'px'; ?> + 15px);" >
      <div class="container-fluid">
           <!-- logo, botão para o inicio -->
              <a class="navbar-brand"  href="index.php">&nbsp;&nbsp;&nbsp;&nbsp;
              
                <img src="../img/logo-texto-escuro.png" style="width:<?php echo $size_icons_barra_topo *180/100?>px;"> <!-- Logo Easy Clinicas -->
                        <?php
                            // Para printar o nome da empresa no topo. podese por mais informaçoes da empresa se necessario
                            $query_empresa = $pdo->query("SELECT * from informacoes_do_estabelecimento where id = 1");
                            $res_empresa = $query_empresa->fetchAll(PDO::FETCH_ASSOC);
                            $total_reg_empresa = @count($res_empresa);
                
                              if($total_reg_empresa > 0){ 
                            $nome_empresa = $res_empresa[0]['nome'];
                            ?>
              
                            <span style="color:<?php echo $cor_fonte_barra_topo ?>; font-size: 13px;"><?php echo $nome_empresa ?></span>
                        <?php } ?>    
            </a>

     
           <left> <!-- alinha o grupo a direira -->
              
              <div  class="collapse navbar-collapse" id="navbarSupportedContent">
               
                <div style="text-align: left;"   class="d-flex mx-10">
                    <?php         
                       $query4 = $pdo->query("SELECT foto as icon_in FROM `informacoes_do_estabelecimento`");
                       $res4 = $query4->fetchAll(PDO::FETCH_ASSOC);
                       $icon_in4 = $res4[0]['icon_in']; 
                    ?>


       
                    <!-- foto usuario  styles para deixa circular-->
                    <div class="flex" style="display: flex; align-items: center;">
                      <div>
                        <img  class="ico-b1" style=" border-radius: 50%; margin-right: 10px;" 
                            src="../<?=$pasta?>/img/informacoes_do_estabelecimento/<?=$icon_in4 ?>"  >
                      </div>
                      <div>
                        <img  class="ico-b1"  style=" border-radius: 50%;" 
                            src="<?php echo !$foto_sistema_user ?  $sem_foto : $foto_sistema_user ?>" >
                      </div>
                    </div>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="true" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                   
                   
                    
                    <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                        <ul class="navbar-nav">
                              <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" style="font-size: 13px; text-transform:none; color: <?php echo $cor_fonte_barra_topo2?>;" data-bs-toggle="dropdown" aria-expanded="false">
                                      <?php echo $nickname_user ?>
                                    </a>
                    
                                    <ul class="dropdown-menu dropdown-menu left" style="right: 0;  left: auto;" aria-labelledby="navbarDarkDropdownMenuLink">
                                      <li><buttom  style="font-size: 12px; color: color: black;  font-style:normal;" id="abrirModalUser" onclick="abrirModal('modalUser', <?php echo $id_user ?>)" class="dropdown-item" href="" >Perfil</buttom></li> <!-- data-bs-target="#modalPerfil"   data-bs-toggle="modal"     -->
                                      <li><hr class="dropdown-divider"></li>
                                      <li><a style="font-size: 12px; color: black;  font-style:normal;" class="dropdown-item" href="../logout.php">Sair</a></li>
                                    </ul>
                              </li>
                        </ul>
                    </div> 
           
                 </div>
              </div>
          </left>
      </div> 
</nav>
 <!-- fim menu, logo, aviso e usuario -->
</div>

<hr class= "med-line">

<!--MENU dos Setores ICONES -->

<!-- MENU DOS ÍCONES - Versão responsiva -->
<nav class="navbar navbar-expand-md" style="height: <?= $size_icons +40?>px; padding-top:5px; background-color: <?php echo $cor_barra2; ?>;">
  <div class="container-fluid">
    <!-- Botão para dispositivos móveis -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarIcons"
      aria-controls="navbarIcons" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Conteúdo do menu que colapsa -->
    <div class="collapse navbar-collapse <?php echo $align_center; ?>" id="navbarIcons">
      <ul class="navbar-nav <?php echo $align_right; ?>">
        <!-- Grupo Agenda -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="agenda" href="index.php?pagina=<?php echo $menu[1]; ?>">
            <i class="bi bi-calendar4-week ico-bar"></i>
            <span class="ico-font">Recepção</span>
          </a>
        </li>
        <!-- Grupo Clientes -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="cliente" href="index.php?pagina=<?php echo $menu[2]; ?>">
            <i class="bi bi-people-fill ico-bar"></i>
            <span class="ico-font">Clientes</span>
          </a>
        </li>
        <!-- Grupo Técnico -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="tecnico" href="index.php?pagina=<?php echo $menu[3]; ?>">
            <i class="bi bi-clipboard2-pulse-fill ico-bar"></i>
            <span class="ico-font">Técnico</span>
          </a>
        </li>
        <!-- Grupo Financeiro -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="financeiro" href="index.php?pagina=<?php echo $menu[4]; ?>">
            <i class="bi bi-currency-dollar ico-bar"></i>
            <span class="ico-font">Financeiro</span>
          </a>
        </li>
        <!-- Grupo Marketing -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="marketing" href="index.php?pagina=<?php echo $menu[5]; ?>">
            <i class="bi bi-megaphone-fill ico-bar"></i>
            <span class="ico-font">Marketing</span>
          </a>
        </li>
        <!-- Grupo Produtos -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="produtos" href="index.php?pagina=<?php echo $menu[6]; ?>">
            <i class="bi bi-box-seam-fill ico-bar"></i>
            <span class="ico-font">Produtos</span>
          </a>
        </li>
        <!-- Grupo Pessoal -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="pessoal" href="index.php?pagina=<?php echo $menu[7]; ?>">
            <i class="bi bi-person-badge-fill ico-bar"></i>
            <span class="ico-font">Pessoal</span>
          </a>
        </li>
        <!-- Grupo Comercial -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="comercial" href="index.php?pagina=<?php echo $menu[8]; ?>">
            <i class="fa fa-solid fa-handshake ico-bar"></i>
            <span class="ico-font">Comercial</span>
          </a>
        </li>
        <!-- Grupo Administração -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="administracao" href="index.php?pagina=<?php echo $menu[9]; ?>">
            <i class="bi bi-graph-up-arrow ico-bar"></i>
            <span class="ico-font">Administração</span>
          </a>
        </li>
        <!-- Grupo Sistema -->
        <li class="nav-item icon-group">
          <a class="nav-link" data-group="sistema" href="index.php?pagina=<?php echo $menu[10]; ?>">
            <i class="bi bi-gear-fill ico-bar"></i>
            <span class="ico-font">Sistema</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>




 











<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>
<script>
  $(function(){
    $('#filtro-selecionado').selectpicker();
  });




 
</script>





</body>



<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
<script type="text/javascript" src="js/datePickerMask.js?v=0.07"></script>
<!--<script  src="js/validacoes.js?v=0.22"></script>-->

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Inicializa TODOS os inputs .datepicker de uma vez
  initDatepickers();
 
});

const observer = new MutationObserver(muts => {
  muts.forEach(m => {
    m.addedNodes.forEach(node => {
      if (node.nodeType === 1 && node.matches('.selectpicker')){
        $(node).selectpicker();
      }
      // e se vierem vários selects dentro de um fragmento:
      $(node).find('.selectpicker').each(function(){
        $(this).selectpicker();
      });
    });
  });
});

observer.observe(document.body, { childList: true, subtree: true });






document.addEventListener('change', function(e) {
      // 1) Só continua se quem disparou for um <select> dentro de um wrapper .bootstrap-select
      if (!e.target.matches('.bootstrap-select select')) return;

      // 2) Encontra o wrapper que engloba o select e o botão de toggle
      const wrapper = e.target.closest('.bootstrap-select.dropdown');
      if (!wrapper) return;

      // 3) Dentro dele, busca o botão que dispara o dropdown
      //    - no BS5: [data-bs-toggle="dropdown"]
      //    - no BS4: .dropdown-toggle
      const toggleBtn = wrapper.querySelector('[data-bs-toggle="dropdown"], .dropdown-toggle');
      if (!toggleBtn) return;

      // 4) Pega (ou cria) a instância do dropdown e manda fechar
      const dd = bootstrap.Dropdown.getInstance(toggleBtn)
              || bootstrap.Dropdown.getOrCreateInstance(toggleBtn);
      dd.hide();
});







</script>



<?php 

  if (isset($_GET['pagina']) && !empty($_GET['pagina'])) {
    foreach ($menu as $pagina) {
       
        if ($_GET['pagina'] == $pagina) {
            


            require_once($pagina . '.php');
            exit; // Encerra o script após encontrar e carregar a página desejada
        }
    }


  }
      $pagina_ir = $_GET['pagina']??'';
      if($pagina_ir==''){
      
        require_once($menu[1].'.php');
         exit;
      }
      if (substr($pagina_ir, -4) !== '.php') {
          $pagina_ir .= '.php';
      }
      require_once($pagina_ir);
  // Se o loop terminar sem encontrar uma correspondência, ou se 'pagina' não estiver definido ou estiver vazio, carrega o padrão.
  //require_once($menu[1].'.php');
  //require_once($pagina.'.php');

?>



</html>