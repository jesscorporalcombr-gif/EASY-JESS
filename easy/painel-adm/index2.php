<?php 
@session_start();
require_once('../conexao.php');
require_once('verificar-permissao.php');

require_once ('personalizacoes/personalizacao_agenda.php');
require_once ('personalizacoes/personalizacao_sistema.php');

require_once ('subMenu.php');
include('loadMenu.php');


$sem_foto = "../img/sem-foto.svg";
$sem_imagem = "../img/sem-foto2.jpg";


//RECUPERAR DADOS DO USUÁRIO QUE ESTÁ ACESSANDO

$query = $pdo->query("SELECT nickname, nome, email, senha_sistema, foto_sistema, cpf, id from cadastro_colaboradores WHERE id = '$_SESSION[id_usuario]'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nickname_user = $res[0]['nickname'];
$nome_user = $res[0]['nome'];
if (empty($nickname_user)){
  $nickname_user = $nome_user;
}


$email_user = $res[0]['email'];
$senha_user = $res[0]['senha_sistema'];
//$nivel_user = $res[0]['nivel'];
$foto_sistema_user = "../img/users/" . $res[0]['foto_sistema'];
$cpf_user = $res[0]['cpf'];
$id_user = $res[0]['id'];

?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Painel Administrativo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="shortcut icon" href="../img/logo.png" />
    
    <link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css"> <!-- MANTER    -->
    
    <link rel="stylesheet" href="../css/style.css?v=0.03">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js?v=1.0"></script>
    
    <!-- Fiz o meu e melhor <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script> -->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
     
    <script type="text/javascript" src="../js/numeros.js?v=0.10"></script> <!--formata os numeros por classe-->
    
    <script type="text/javascript" src="js/modalsOpen.js?v=1.09"></script>
    
    <script type="text/javascript" src="js/senha.js"></script>
    
    <script type="text/javascript" src="js/tabelas.js?v=1.05"></script>
    <script type="text/javascript" src="js/validacoes.js?v=0.15"></script> <!-- meu-->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="css/style.css?v=2.27">
    <script src="js/tabelasExample.js?v=0.12"></script>

<!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
ul.nav-tabs .tab-btn.active,
ul.nav-tabs .tab-btn.active:hover {
      color:<?php echo $cor_fonte_secundaria ?>;
}


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



/* ------MODAL ------*/
.modal-content{
  border: 1px solid <?php echo $cor_head_form?> !important;
}

.modal-title {
    color:<?php echo $cor_fonte_head_form?>; 
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

/*----------------------------------------------*/



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
                        //RECUPERAR quantidade de mensagens 
                       //$query3 = $pdo->query("SELECT COUNT(id) as tot FROM `avisos` ");
                       //$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
                       //$qt_avisos = $res3[0]['tot'];   
                       $query4 = $pdo->query("SELECT foto as icon_in FROM `informacoes_do_estabelecimento`");
                       $res4 = $query4->fetchAll(PDO::FETCH_ASSOC);
                       $icon_in4 = $res4[0]['icon_in']; 
                    ?>


                    <!-- Ajuda 
                    <a style="font-size: 12px; color:#31900c;  font-style:bold;" class="dropdown-item"  href="https://api.whatsapp.com/send/?phone=555197706312&text&app_absent=0" target="_blank">
                        <span style="font-size: 12px; font-style: bold; color: green; white-space: nowrap;"> Ajuda </span>
                    </a>
        
        
                     AVISOS 
                    <a style="font-size: 10px; font-style:normal;" class="nav-link" href="index.php?pagina=ler_aviso">
                      <span style="font-size: 10px; font-style: normal; color: green; white-space: nowrap;"><?php echo $qt_avisos . " Avisos"; ?></span>
                    </a>-->
       
                    <!-- foto usuario  styles para deixa circular-->
                    <div class="flex" style="display: flex; align-items: center;">
                      <div>
                        <img  class="ico-b1" style=" border-radius: 50%; margin-right: 10px;" 
                            src="../img/informacoes_do_estabelecimento/<?php echo $icon_in4 ?>"  >
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
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" style="font-size: 13px; text-transform:none;color: <?php echo $cor_fonte_barra_topo2?>;" data-bs-toggle="dropdown" aria-expanded="false">
                                      <?php echo $nickname_user ?>
                                    </a>
                    
                                    <ul class="dropdown-menu dropdown-menu left" style="right: 0;  left: auto;" aria-labelledby="navbarDarkDropdownMenuLink">
                                      <li><buttom  style="font-size: 12px; color: color: black;  font-style:normal;" id="abrirModalUser" onclick="abrirModal('modalUser', <?php echo $id_user ?>)" class="dropdown-item" href="" >Alterar Senha</buttom></li> <!-- data-bs-target="#modalPerfil"   data-bs-toggle="modal"     -->
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

<nav class="navbar navbar-expand-md" style="padding-top:12px; height:<?php echo ($size_icons + 35)?>px; background-color:<?php echo $cor_barra2?>;">
    <!-- Botão para dispositivos móveis -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedIcons" aria-controls="navbarSupportedIcons" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Conteúdo da navbar que será colapsado no mobile -->
    <div class="collapse navbar-collapse <?php echo $align_center ?>" id="navbarSupportedIcons">
        <div class="navbar-nav <?php echo $align_right ?>">

                  <div class="navbar-nav <?php echo $align_right?>">
                        <!-- Grupo Agenda -->
                        <div class="icon-group" >
                          <a class="nav-item nav-link" data-group="agenda"  href="index.php?pagina=<?php echo $menu[1] ?>">
                          <i class="bi bi-calendar4-week ico-bar"></i>
                          <span class="ico-font" >Agenda</span>
                        </a>
                        </div>

                        <!-- Grupo Clientes -->
                        <div class="icon-group">
                          <a class="nav-item nav-link" data-group="cliente"  href="index.php?pagina=<?php echo $menu[2] ?>">
                          <i class="bi bi-people-fill ico-bar"></i>
                            <span class="ico-font">Clientes</span>
                          </a>
                        </div>

                        <!-- Grupo Técnico -->
                        <div class="icon-group">
                          <a class="nav-item nav-link" data-group="tecnico"  href="index.php?pagina=<?php echo $menu[3] ?>"> <?php $pagina=$menu[3] ?>
                          <i class="bi bi-clipboard2-pulse-fill ico-bar"></i>
                            <span class="ico-font">Técnico</span>
                          </a>
                        </div>

                        <!--<div class="navbar-nav">-->
                        <!-- Grupo Financeiro-->
                        <div class="icon-group">
                          <a class="nav-item nav-link"  data-group="financeiro"  href="index.php?pagina=<?php echo $menu[4] ?>">
                          <i class="bi bi-currency-dollar ico-bar"></i>
                            <span class="ico-font">Financeiro</span>
                          </a>
                        </div>

                        <!-- Grupo Marketing -->
                        <div class="icon-group">
                          <a class="nav-item nav-link" data-group="marketing"  href="index.php?pagina=<?php echo $menu[5] ?>">
                          <i class="bi bi-megaphone-fill ico-bar"></i>
                            <span class="ico-font">Marketing</span>
                          </a>
                        </div>

                        <!-- Grupo Produtos -->
                        <div class="icon-group">
                          <a class="nav-item nav-link" data-group="produtos"  href="index.php?pagina=<?php echo $menu[6] ?>">
                          <i class="bi bi-box-seam-fill ico-bar"></i>
                            <span class="ico-font">Produtos</span>
                          </a>
                        </div>

                        <!-- Grupo Pessoal -->
                        <div class="icon-group">
                          <a class="nav-item nav-link" data-group="pessoal" h href="index.php?pagina=<?php echo $menu[7] ?>">
                          <i class="bi bi-person-badge-fill ico-bar"></i>
                            <span class="ico-font">Pessoal</span>
                          </a>
                        </div>
                        <!-- Grupo Comercial -->
                          <div class="icon-group">
                                    <a class="nav-item nav-link" data-group="comercial"  href="index.php?pagina=<?php echo $menu[8] ?>">
                                    <i class="fa fa-solid fa-handshake ico-bar"></i>
                                  <span class="ico-font">Comercial</span>
                                </a>
                          </div>
                          <!-- Grupo Administração -->
                          <div class="icon-group">
                                    <a class="nav-item nav-link" data-group="administracao"  href="index.php?pagina=<?php echo $menu[9] ?>">
                                    <i class="bi bi-graph-up-arrow ico-bar"></i>
                                  <span class="ico-font">Administração</span>
                                </a>
                          </div>



                          <!-- Grupo Sistema -->
                          <div class="icon-group">
                                    <a class="nav-item nav-link" data-group="sistema"  href="index.php?pagina=<?php echo $menu[10] ?>">
                                    <i  class="bi bi-gear-fill ico-bar"></i>
                                  <span class="ico-font">Sistema</span>
                                </a>
                          </div>
                        <!-- Mais ícones aqui -->
                           
              </div>
              
        </div>
</nav>



 


<script>
document.addEventListener('DOMContentLoaded', function () {
    const iconGroups = document.querySelectorAll('#navbarSupportedIcons .icon-group');

    iconGroups.forEach(function (group) {
        group.addEventListener('click', function () {
            // Armazenar o índice do grupo ativo no localStorage
            const index = Array.from(iconGroups).indexOf(group);
            localStorage.setItem('activeGroupIndex', index);
        });
    });

    // Recuperar o índice do grupo ativo do localStorage
    const activeGroupIndex = localStorage.getItem('activeGroupIndex');
    if (activeGroupIndex !== null) {
        iconGroups.forEach(function (g) {
            g.classList.remove('gp-ativo');
        });
        iconGroups[activeGroupIndex].classList.add('gp-ativo');
    }
});

</script>





</body>



<?php 

  if (isset($_GET['pagina']) && !empty($_GET['pagina'])) {
    foreach ($menu as $pagina) {
       
        if ($_GET['pagina'] == $pagina) {
            


            require_once($pagina . '.php');
            exit; // Encerra o script após encontrar e carregar a página desejada
        }
    }
  }

  // Se o loop terminar sem encontrar uma correspondência, ou se 'pagina' não estiver definido ou estiver vazio, carregue o padrão.
  require_once($menu[1].'.php');

?>



</html>