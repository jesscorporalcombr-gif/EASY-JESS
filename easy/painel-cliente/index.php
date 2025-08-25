<?php 

@session_start();
require_once('../conexao.php');
require_once('verificar-permissao.php');


//VARIAVEIS DO MENU ADMINISTRATIVO
$menu1 = 'home';
$menu2 = 'usuarios';
$menu3 = 'fornecedores';
$menu4 = 'categorias';
$menu5 = 'produtos';
$menu6 = 'compras';
$menu7 = 'caixas';
$menu8 = 'forma_pgtos';
$menu9 = 'vendas';
$menu10 = 'aberturas';
$menu11 = 'estoque';
$menu12 = 'servicos';
$menu13 = 'clientes';
$menu14 = 'documentos';
$menu15 = 'marketing';
$menu16 = 'acompanhamento_cliente';

$menu40 = 'avisos';

$menu19 = 'promocoes';
$menu70 = 'ver_comandas_por_cliente';
$menu72 = 'ver_img_cliente';
$menu73 = 'ver_creditos'; 
$menu74 = 'ver_agendar_conectado';
$menu82 = 'agendamento_remoto';


//RECUPERAR DADOS DO cliente
$query = $pdo->query("SELECT * from clientes WHERE id = '$_SESSION[id_usuario]'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

@$nome_usu_cli = $res[0]['nome'];
@$email_usu = $res[0]['email'];
@$senha_usu = $res[0]['senha'];
@$nivel_usu = $res[0]['nivel'];
@$nivel_usu_comprador = $res[0]['nivel_comprador'];
@$foto = $res[0]['foto'];
@$cpf_usu = $res[0]['cpf'];
@$id_usu = $res[0]['id'];

 ?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>

    	<title>Meu Painel</title>

    	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    	<link rel="stylesheet" type="text/css" href="../vendor/DataTables/datatables.min.css"/>
     
    	<script type="text/javascript" src="../vendor/DataTables/datatables.min.js"></script>

    	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    	<link rel="shortcut icon" href="../img/logo.png" />

      <!-- CSS template cliente  //////////////////////////////////////////////////////////////////////////////////-->

      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <!-- Custom Google fonts -->
      <link href='http://fonts.googleapis.com/css?family=Raleway:400,500,300,700' rel='stylesheet' type='text/css'>
      <link href="http://fonts.googleapis.com/css?family=Crimson+Text:400,600" rel="stylesheet" type="text/css">
      <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css">

      <!-- Bootstrap CSS Style -->
      <link rel="stylesheet" href="assets/css/bootstrap.min.css">

      <!-- Template CSS Style -->
      <link rel="stylesheet" href="assets/css/style.css">

      <!-- Animate CSS  -->
      <link rel="stylesheet" href="assets/css/animate.css">

      <!-- FontAwesome 4.3.0 Icons  -->
      <link rel="stylesheet" href="assets/css/font-awesome.min.css">

      <!-- et line font  -->
      <link rel="stylesheet" href="assets/css/et-line-font/style.css">

      <!-- BXslider CSS  -->
      <link rel="stylesheet" href="assets/css/bxslider/jquery.bxslider.css">

      <!-- Owl Carousel CSS Style -->
      <link rel="stylesheet" href="assets/css/owl-carousel/owl.carousel.css">
      <link rel="stylesheet" href="assets/css/owl-carousel/owl.theme.css">
      <link rel="stylesheet" href="assets/css/owl-carousel/owl.transitions.css">

      <!-- Magnific-Popup CSS Style -->
      <link rel="stylesheet" href="assets/css/magnific-popup/magnific-popup.css">

    </head>
<body>

  

	<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            
              <!-- logo, botão para o inicio -->
              <a href="index.php">  
                <img src="../img/logo.png" width="40"> 
              </a> 
            

            <li class="nav-item dropdown">
            
              <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Agenda
              </a>

              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">     


                 <li>
                    <a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php    echo $menu74 ?>" >Agendamentos
                    </a>
                 </li>

                 <li>
                    <a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php    echo $menu82 ?>" >Agendamentos Online
                    </a>
                 </li>


              </ul>

            </li>


            <li class="nav-item dropdown">
              <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Creditos
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">  

                  <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu73 ?>" >Créditos</a></li>      


              </ul>
            </li>


            <li class="nav-item dropdown">
              <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Galeria
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">  

                  <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu72 ?>" >Imagens</a></li>      


              </ul>
            </li>

            <li class="nav-item dropdown">
              <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Protocolos
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">  

                  <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu70 ?>" >Serviços</a></li>      


              </ul>
            </li>


            <li class="nav-item dropdown">
              <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Promos
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">  

                  <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu19 ?>" >Promoções Ativas</a></li> 

                  <!--

                  <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="" >Cartão Presente</a></li> 

                  -->     


              </ul>
            </li>



            <li class="nav-item dropdown">
              <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Prontuarios
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                   <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu16 ?>" >Acompanhamentos</a></li>    


              </ul>
            </li>

             <li class="nav-item dropdown">
              <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Sugestões
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">  

                  <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu40 ?>" >Enviar</a></li>      


              </ul>
            </li>


            <li class="nav-item dropdown"> 
              <a style="font-size: 13px; color:#31900c;  font-style:normal;" class="nav-link " 
              href="https://api.whatsapp.com/send/?phone=555196401579&text&app_absent=0" target="_blank" 
              id="navbarDropdown" role="button"  aria-expanded="false">
                Ajuda
              </a>
            </li>

           
          </ul>

        </div>

  </div>


  <div class="container-fluid">
       

   <left> <!-- alinha o grupo a direira -->
      <div  class="collapse navbar-collapse" id="navbarSupportedContent">
        <div style="text-align:left;" class="d-flex mx-3">
            <?php         
                //RECUPERAR quantidade de mensagens 

               //$query3 = $pdo->query("SELECT COUNT(id) as tot FROM `avisos` ");
               //$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
               //$qt_avisos = $res3[0]['tot'];    
            ?>

              


          <!-- numero de avisos salvos -->

            <!-- 
            <a style="font-size: 10px;  font-style:normal; " class="nav-link" href="index.php?pagina=ler_aviso">

              <span style="font-size: 10px;  font-style:normal; color:indianred; "><?php echo $qt_avisos ?></span>

            Avisos</a>
          -->
                 
          <!-- foto usuario  styles para deixa circular-->            
            <div>
              <?php  
              if ($foto == 'sem-foto.jpg' or $foto == 'sem-foto.jpg' or $foto == NULL) {  ?>
                  
                   <img   style=" border-radius: 50%; width: 35px; height: 35px;" 
                  src="../img/clientes/sem-foto.jpg ?>" width="35" height="35" >

              <?php
              } else {  ?>
                
                 <img   style=" border-radius: 50%; width: 35px; height: 35px;" 
                  src="../img/clientes/<?php echo $foto ?>" width="35" height="35" >
              
               <?php } ?>
            </div>
        

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
         </button>
      
          <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
            <ul class="navbar-nav">

              <li class="nav-item dropdown">

                <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <?php echo 'Nivel: '. $nivel_usu_comprador. ' - ' .$nome_usu_cli ?>
                </a>

                <ul class="dropdown-menu dropdown-menu" aria-labelledby="navbarDarkDropdownMenuLink">
                  
                  

                  <!--
                  <li>

                    <a  style="font-size: 14px; color: grey;  font-style:normal;" class="dropdown-item" 
                    href="" data-bs-toggle="modal" data-bs-target="#modalPerfil">
                    Editar Perfil
                   </a>

                 </li>        

                  --> 
                 
                   <li><hr class="dropdown-divider"></li>
                  
                  <li><a style="font-size: 14px; color: grey;  font-style:normal;" class="dropdown-item" href="../logout.php">Sair</a></li>
                  
                </ul>

              </li>
            </ul>
          </div>
         </div>
      </div>
    </left>
    </div>
</nav>





<div class="container-fluid mt-2 mx-3">
<?php 

if(@$_GET['pagina'] == $menu1){
	require_once($menu1. '.php');
}

else if(@$_GET['pagina'] == $menu2){
	require_once($menu2. '.php');
}

else if(@$_GET['pagina'] == $menu3){
	require_once($menu3. '.php');
}

else if(@$_GET['pagina'] == $menu4){
	require_once($menu4. '.php');
}

else if(@$_GET['pagina'] == $menu5){
  require_once($menu5. '.php');
}

else if(@$_GET['pagina'] == $menu6){
  require_once($menu6. '.php');
}

else if(@$_GET['pagina'] == $menu7){
  require_once($menu7. '.php');
}

else if(@$_GET['pagina'] == $menu8){
  require_once($menu8. '.php');
}

else if(@$_GET['pagina'] == $menu9){
  require_once($menu9. '.php');
}

else if(@$_GET['pagina'] == $menu10){
  require_once($menu10. '.php');
}


else if(@$_GET['pagina'] == $menu11){
  require_once($menu11. '.php');
}

else if(@$_GET['pagina'] == $menu12){
  require_once($menu12. '.php');
}

else if(@$_GET['pagina'] == $menu13){
  require_once($menu13. '.php');
}

else if(@$_GET['pagina'] == $menu14){
  require_once($menu14. '.php');
}

else if(@$_GET['pagina'] == $menu15){
  require_once($menu15. '.php');
}

else if(@$_GET['pagina'] == $menu74){
  require_once($menu74. '.php');
}

else if(@$_GET['pagina'] == $menu73){
  require_once($menu73. '.php');
}

else if(@$_GET['pagina'] == $menu72){
  require_once($menu72. '.php');
}

else if(@$_GET['pagina'] == $menu70){
  require_once($menu70. '.php');
}

else if(@$_GET['pagina'] == $menu19){
  require_once($menu19. '.php');
}

else if(@$_GET['pagina'] == $menu16){
  require_once($menu16. '.php');
}

else if(@$_GET['pagina'] == $menu82){
  require_once($menu82. '.php');
}

else if(@$_GET['pagina'] == $menu40){
  require_once($menu40. '.php');
}





else{
	require_once($menu1. '.php');
}

 ?>
</div>

</body>
</html>




<div class="modal fade" tabindex="-1" id="modalPerfil" data-bs-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Editar Perfil</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form method="POST" id="form-perfil">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Nome</label>
								<input type="text" class="form-control" id="nome-perfil" name="nome-perfil" placeholder="Nome" required="" value="<?php echo @$nome_usu_cli ?>">
							</div> 
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">CPF</label>
								<input type="text" class="form-control" id="cpf-perfil" name="cpf-perfil" placeholder="CPF" required="" value="<?php echo @$cpf_usu ?>">
							</div>  
						</div>
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Email</label>
						<input type="email" class="form-control" id="email-perfil" name="email-perfil" placeholder="Email" required="" value="<?php echo @$email_usu ?>">
					</div>  

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Senha</label>
						<input type="text" class="form-control" id="senha-perfil" name="senha-perfil" placeholder="Senha" required="" value="<?php echo @$senha_usu ?>">
					</div>  

					

					<small><div align="center" class="mt-1" id="mensagem-perfil">
						
					</div> </small>

				</div>
				<div class="modal-footer">
					<button type="button" id="btn-fechar-perfil" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar-perfil" id="btn-salvar-perfil" type="submit" class="btn btn-primary">Salvar</button>

					<input name="id-perfil" type="hidden" value="<?php echo @$id_usu ?>">


				</div>
			</form>
		</div>
	</div>
</div>







<!--  Modal Rel Compras-->
<div class="modal fade" tabindex="-1" id="ModalRelCompras" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Relatório de Compras</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
       <form action="../rel/relCompras_class.php" method="POST" target="_blank">
       
                <div class="modal-body">

                 <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label >Data Inicial</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataInicial" >
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group mb-3">
                            <label >Data Final</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataFinal" >
                        </div>


                    </div>

                    <div class="col-md-4">

                        <div class="form-group mb-3">
                            <label >Pago</label>
                            <select class="form-select mt-1"  name="status">
                                <option value="">Todas</option>
                                <option value="Sim">Sim</option>
                                <option value="Não">Não</option>
                               
                            </select>
                        </div>


                    </div>

                </div>     

            </div>
            <div class="modal-footer">
          <button type="submit" class="btn btn-primary" >Gerar Relatório</button>
         
        </div>
        </form>


    </div>
</div>
</div>






<!--  Modal Rel Vendas-->
<div class="modal fade" tabindex="-1" id="ModalRelVendas" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Relatório de Vendas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
       <form action="../rel/relVendas_class.php" method="POST" target="_blank">
       
                <div class="modal-body">

                 <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label >Data Inicial</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataInicial_venda" >
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group mb-3">
                            <label >Data Final</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataFinal_venda" >
                        </div>


                    </div>

                    <div class="col-md-4">

                        <div class="form-group mb-3">
                            <label >Status</label>
                            <select class="form-select mt-1"  name="status_venda">
                                <option value="">Todas</option>
                                <option value="Concluída">Concluída</option>
                                <option value="Cancelada">Cancelada</option>
                               
                            </select>
                        </div>


                    </div>

                </div>     

            </div>
            <div class="modal-footer">
          <button type="submit" class="btn btn-primary" >Gerar Relatório</button>
         
        </div>
        </form>


    </div>
</div>
</div>







<!--  Modal Rel Produtos Vendidos-->
<div class="modal fade" tabindex="-1" id="ModalRelProdVendidos" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Produtos Vendidos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
       <form action="../rel/relProdVendidos_class.php" method="POST" target="_blank">
       
                <div class="modal-body">

                 <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label >Data Inicial</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataInicial_venda" >
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group mb-3">
                            <label >Data Final</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataFinal_venda" >
                        </div>


                    </div>

                    <div class="col-md-4">

                       


                    </div>

                </div>     

            </div>
            <div class="modal-footer">
          <button type="submit" class="btn btn-primary" >Gerar Relatório</button>
         
        </div>
        </form>


    </div>
</div>
</div>





<!--  Modal Rel Quebras-->
<div class="modal fade" tabindex="-1" id="ModalRelQuebras" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Quebras de Caixa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
       <form action="../rel/relQuebras_class.php" method="POST" target="_blank">
       
                <div class="modal-body">

                 <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label >Data Inicial</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataInicial" >
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group mb-3">
                            <label >Data Final</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataFinal" >
                        </div>


                    </div>

                    <div class="col-md-4">

                       


                    </div>

                </div>     

            </div>
            <div class="modal-footer">
          <button type="submit" class="btn btn-primary" >Gerar Relatório</button>
         
        </div>
        </form>


    </div>
</div>
</div>






<!--  Modal Rel Lucro-->
<div class="modal fade" tabindex="-1" id="ModalRelLucro" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Relatório de Lucro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
       <form action="../rel/relLucro_class.php" method="POST" target="_blank">
       
                <div class="modal-body">

                 <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label >Data Inicial</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataInicial" >
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group mb-3">
                            <label >Data Final</label>
                            <input value="<?php echo date('Y-m-d') ?>" type="date" class="form-control mt-1"  name="dataFinal" >
                        </div>


                    </div>

                    <div class="col-md-4">

                       


                    </div>

                </div>     

            </div>
            <div class="modal-footer">
          <button type="submit" class="btn btn-primary" >Gerar Relatório</button>
         
        </div>
        </form>


    </div>
</div>
</div>





<script src="https://cdn.jsdelivr.net/npm/notiflix@2.6.0/dist/notiflix-aio-2.6.0.min.js"></script>

       <script> /right-top, right-bottom, left-top, left-bottom, center-top, center-bottom, center-center/
        var position = "left-bottom";
 
        /verde, azul, vermelho, amarelo/
        var color = "verde";
 
        /fade, zoom, from-right, from-left, from-top, from-bottom/
        var animation = "from-left";
 
        /nome do produto/
        var product_name = "";
 
        /frase depois do nome da pessoa/
        var phrase = "acabou de comprar";
        var phrase = "Se inscreveu!";
        var timeout = 4000;
 
        /masc, fem, any/
        var type_name = "masc, fem";
        var msg_final = "";
 
        var min_time = 30000;
        var max_time = 150000;
 
        var names_masc = ['Jose', 'Joao', 'Antonio', 'Francisco', 'Carlos', 'Paulo', 'Pedro', 'Lucas', 'Luiz', 'Marcos', 'Luis', 'Gabriel', 'Rafael', 'Daniel', 'Marcelo', 'Bruno', 'Eduardo', 'Felipe', 'Rodrigo', 'Manoel', 'Mateus', 'Andre', 'Fernando', 'Fabio', 'Leonardo', 'Gustavo', 'Guilherme', 'Leandro', 'Tiago', 'Anderson', 'Ricardo', 'Marcio', 'Jorge', 'Alexandre', 'Roberto', 'Edson', 'Diego', 'Vitor', 'Sergio', 'Claudio', 'Matheus', 'Thiago', 'Geraldo', 'Adriano', 'Luciano', 'Julio', 'Renato', 'Alex', 'Vinicius', 'Rogerio', 'Samuel', 'Ronaldo', 'Mario', 'Flavio', 'Douglas', 'Igor', 'Davi', 'Manuel', 'Jeferson', 'Cicero', 'Victor', 'Miguel', 'Robson', 'Mauricio', 'Danilo', 'Henrique', 'Caio', 'Reginaldo', 'Joaquim', 'Benedito', 'Gilberto', 'Marco', 'Alan', 'Nelson', 'Cristiano', 'Elias', 'Wilson', 'Valdir', 'Emerson', 'Luan', 'David', 'Renan', 'Severino', 'Fabricio', 'Mauro', 'Jonas', 'Gilmar', 'Jean', 'Fabiano', 'Wesley', 'Diogo', 'Adilson', 'Jair', 'Alessandro', 'Everton', 'Osvaldo', 'Gilson', 'Willian', 'Joel', 'Silvio', 'Helio', 'Maicon', 'Reinaldo', 'Pablo', 'Artur', 'Vagner', 'Valter', 'Celso', 'Ivan', 'Cleiton', 'Vanderlei', 'Vicente', 'Arthur', 'Milton', 'Domingos', 'Wagner', 'Sandro', 'Moises', 'Edilson', 'Ademir', 'Adao', 'Evandro', 'Cesar', 'Valmir', 'Murilo', 'Juliano', 'Edvaldo', 'Ailton', 'Junior', 'Breno', 'Nicolas', 'Ruan', 'Alberto', 'Rubens', 'Nilton', 'Augusto', 'Cleber', 'Osmar', 'Nilson', 'Hugo', 'Otavio', 'Vinicios', 'Italo', 'Wilian', 'Alisson', 'Aparecido'];
        
        var names_fem = ['Maria', 'Ana', 'Francisca', 'Antonia', 'Adriana', 'Juliana', 'Marcia', 'Fernanda', 'Patricia', 'Aline', 'Sandra', 'Camila', 'Amanda', 'Bruna', 'Jessica', 'Leticia', 'Julia', 'Luciana', 'Vanessa', 'Mariana', 'Gabriela', 'Vera', 'Vitoria', 'Larissa', 'Claudia', 'Beatriz', 'Rita', 'Luana', 'Sonia', 'Renata', 'Eliane', 'Helena', 'Alice','Laura','Maria Alice','Sophia','Manuela','Maitê','Liz','Cecília','Isabella','Luísa','Eloá','Heloísa','Júlia','Ayla','Maria Luísa','Isis','Elisa','Antonella','Valentina','Maya','Maria Júlia','Aurora','Lara','Maria Clara','Lívia','Esther','Giovanna','Sarah','Maria Cecília','Lorena','Beatriz','Rebeca','Luna','Olívia','Maria','Mariana','Isadora','Melissa','Catarina','Lavínia','Alícia','Maria Eduarda','Agatha','Ana','Liz','Yasmin','Emanuelly','Ana Clara','Clara','Ana Júlia','Marina','Stella','Jade','Ana Laura','Maria','Isis','Ana Luísa','Gabriela','Alana','Rafaela','Vitória','Isabelly','Bella','Milena','Clarice','Mirella','Ana','Emilly','Betina','Mariah','Zoe','Vitória','Nicole','Laís','Melina','Bianca','Louise','Ana','Beatriz','Heloíse','Malu','Melinda','Letícia','Valentina','Chloe','Elisa','Heloísa','Laura','Fernanda','Ana','Cecília','Hadassa','Vitória','Diana','Ayla','Sophia','Eduarda','Lívia','Isabel','Elis', 'Pérola'];
 
        var option = {
            position: position,
            cssAnimationStyle: animation,
            plainText: false,
            timeout: timeout
        };
 
        function show_notification() {
            if (type_name == "masc") {
                msg_final = "<strong>" + names_masc[Math.floor(Math.random() * names_masc.length)] + "</strong>";

            } else if (type_name == "fem") {
                msg_final = "<strong>" + names_fem[Math.floor(Math.random() * names_fem.length)] + "</strong>";

            } else {
                var array_aux = ["masc", "fem"];
 
                if (array_aux[Math.floor(Math.random() * array_aux.length)] == "masc") {
                    msg_final = "<strong>" + names_masc[Math.floor(Math.random() * names_masc.length)] + "</strong>";
                    
                } else {
                    msg_final = "<strong>" + names_fem[Math.floor(Math.random() * names_fem.length)] + "</strong>";
                }
            }
 
            msg_final += decodeURIComponent(escape(" " + phrase + " " + product_name));
 
            if (color == "verde") {
                Notiflix.Notify.Success(msg_final, option);
            }
 
            if (color == "azul") {
                Notiflix.Notify.Info(msg_final, option);
            }
 
            if (color == "vermelho") {
                Notiflix.Notify.Failure(msg_final, option);
            }
 
            if (color == "amarelo") {
                Notiflix.Notify.Warning(msg_final, option);
            }
 
            var rand = Math.floor(Math.random() * (max_time - min_time + 1) + min_time);
            setTimeout(show_notification, rand * 1000);
        }
            setTimeout(show_notification, 4 * 1000);
</script>




<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
<script type="text/javascript" src="../vendor/js/mascaras.js"></script>




<!--AJAX PARA INSERÇÃO E EDIÇÃO DOS DADOS COM IMAGEM -->
<script type="text/javascript">
	$("#form-perfil").submit(function () {
		
		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: "editar-perfil.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {

				$('#mensagem-perfil').removeClass()

				if (mensagem.trim() == "Salvo com Sucesso!") {

                    //$('#nome').val('');
                    //$('#cpf').val('');
                    $('#btn-fechar-perfil').click();
                    //window.location = "index.php?pagina="+pag;

                } else {

                	$('#mensagem-perfil').addClass('text-danger')
                }

                $('#mensagem-perfil').text(mensagem)

            },

            cache: false,
            contentType: false,
            processData: false,
            xhr: function () {  // Custom XMLHttpRequest
            	var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) { // Avalia se tem suporte a propriedade upload
                	myXhr.upload.addEventListener('progress', function () {
                		/* faz alguma coisa durante o progresso do upload */
                	}, false);
                }
                return myXhr;
            }
        });
	});
</script>



</br></br></br></br>
<footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">

          <div id="footer-section" class="text-center">
        <div class="container">
          <div class="row">
            <div class="col-sm-8 col-sm-offset-2">




<?php 

//RECUPERAR DADOS DO das redes sociais
$query5 = $pdo->query("SELECT * from informacoes_do_estabelecimento");
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);

@$instagram = $res5[0]['instagram'];
@$facebook = $res5[0]['facebook'];
@$site = $res5[0]['site'];

 ?>


              <ul class="footer-social-links">
                <li><a href="<?php echo $facebook?>" target="_blank">Facebook</a></li>
                <li><a href="<?php echo $instagram?>" target="_blank">Instagram</a></li>
                <li><a href="https://www.youtube.com/@jesscorporal" target="_blank">Youtube</a></li>
              </ul>

            </div> <!-- End col-sm-8 -->
          </div> <!-- End row -->
        </div> <!-- End container -->
      </div> <!-- End footer-section  -->

      
            <center><p>Copyright © <script>document.write(new Date().getFullYear());</script> OnlySoft Inst, All Rights Reserved. 
          <br>Design: <a href="https://onlysoftinstituto.com/onlysoft" target="_blank" title="Devs">OnlySoft Team</a></p>
        </center>
        </div>
      </div>
    </div>
  </footer>
  </br>


</body>


<!-- Icone flutuante do whatsapp /*alinhar a esquerda*/
    <a  href="https://api.whatsapp.com/send/?phone=555196401579&text&app_absent=0" target="_blank">
        <img 
        src="../img/wi.png" style="height:28px; width: 28px; position:fixed; bottom: 25px; right: 
        1350px; z-index:99999;" data-selector="img">  
    </a> --->



    <!-- Icone flutuante do whatsapp -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <a href="https://wa.me/5551985876345?text=Adorei%20seu%20artigo" 

          style="position:fixed;
            width:40px;
            height:40px;
            bottom:25px;
            left:26px;

            background-color:#;
            color:#403e3d;
            border-radius:50px;
            text-align:center;
            font-size:31px;
            box-shadow: 1px 1px 1px #b9b8b8;
            z-index:1000;" 

            target="_blank">

          <i style="margin-top:4px" class="fa fa-whatsapp"></i>
        </a>
</html>