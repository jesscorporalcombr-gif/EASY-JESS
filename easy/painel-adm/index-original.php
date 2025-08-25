<?php 

@session_start();

require_once('../conexao.php');
//require_once('verificar-permissao.php');


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
$menu16 = 'equipamentos';
$menu17 = 'salas';
$menu18 = 'pacotes';
$menu19 = 'promocoes';
$menu20 = 'contas_contabeis';
$menu21 = 'anamnese';
$menu22 = 'abrir_orcamentos';
$menu23 = 'comandas_por_cliente';
$menu24 = 'agenda_grupo';
$menu25 = 'contratos';
$menu26 = 'leads'; 
$menu27 = 'interesse'; 
$menu28 = 'lembrete';
$menu29 = 'lembrete_futuros';
$menu30 = 'lembrete_expirado';
$menu31 = 'lista_de_compras';
$menu32 = 'dicas'; 
$menu33 = 'preagendamento';
$menu34 = 'lembretes_consultas';
$menu35 = 'contratos_clientes';
$menu36 = 'termos_contratos_assinaturas';
$menu37 = 'comissoes';
$menu38 = 'ata_recepcao'; 
$menu39 = 'cortesias'; 
$menu40 = 'avisos'; 
$menu41 = 'ler_aviso';
$menu42 = 'agendar_conectado';  
$menu43 = 'ver_cliente'; 
$menu44 = 'documentos_clientes'; 
$menu45 = 'tutoriais'; 
$menu46 = 'galeria_resultados'; 
$menu47 = 'img_usuario'; 
$menu48 = 'img_cliente'; 
$menu49 = 'creditos'; 
$menu50 = 'lista_contratos_modelos'; 
$menu51 = 'informacoes_do_estabelecimento';
$menu52 = 'locais_de_stoque'; 
$menu53 = 'comanda_vale_presente';
$menu54 = 'contas_bancarias';
$menu55 = 'bandeira_cartao';
$menu56 = 'bandeira_cartao_credito';
$menu57 = 'agenda_grupo_por_profissional';
$menu58 = 'receitas';
$menu59 = 'despesas';
$menu60 = 'sub_cat_receitas';
$menu61 = 'sub_cat_despesas';
$menu62 = 'vender_servico';
$menu63 = 'ver_servico_por_cliente';
$menu64 = 'carta_de_clientes';
$menu65 = 'gravar_curriculo';
$menu66 = 'gravar_links';
$menu67 = 'ver_documentos_clientes';
$menu68 = 'pops_padrao';
$menu69 = 'padrao_atendimento';
$menu70 = 'ver_comandas_por_cliente';
$menu71 = 'ver_contratos_clientes';
$menu72 = 'ver_img_cliente';
$menu73 = 'ver_creditos';
$menu74 = 'ver_agendar_conectado'; 
$menu75 = 'proposta_de_vendas'; 
$menu76 = 'ver_proposta_de_venda_clientes';
$menu77 = 'cores_agendamento';
$menu78 = 'bloquear_horario';
$menu79 = 'aniversario_cliente';


//RECUPERAR DADOS DO USUÁRIO QUE ESTÁ ACESSANDO

$query = $pdo->query("SELECT * from usuarios WHERE id = '$_SESSION[id_usuario]'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_usu = $res[0]['nome'];
$email_usu = $res[0]['email'];
$senha_usu = $res[0]['senha'];
$nivel_usu = $res[0]['nivel'];
$foto = $res[0]['foto'];
$cpf_usu = $res[0]['cpf'];
$id_usu = $res[0]['id'];

 ?>

<!DOCTYPE html>
<html>
  <head>
  	<title>Painel Administrativo</title>

  	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

  	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  	<link rel="stylesheet" type="text/css" href="../vendor/DataTables/datatables.min.css"/>

  	<script type="text/javascript" src="../vendor/DataTables/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css"> 
    
      <!-- imagens icon-->    
    <link rel="shortcut icon" href="../img/logo.png" />

    <!--  <link rel="stylesheet" href="../css/style.css">  -->

  </head>

<body style = "background-color: #fafafa;">



<!-- menu logo, aviso e usuario -->
<nav class="navbar navbar-expand-lg navbar-light bg-light" ;>
  <div class="container-fluid">
       <!-- logo, botão para o inicio -->
    <a class="navbar-brand" href="index.php">&nbsp;&nbsp;&nbsp;&nbsp;
      
        <img src="../img/logo-texto-escuro.png" width="40">

            <?php
            // Para printar o nome da empresa no topo. podese por mais informaçoes da empresa se necessario
            $query_empresa = $pdo->query("SELECT * from informacoes_do_estabelecimento where id = 1");
            $res_empresa = $query_empresa->fetchAll(PDO::FETCH_ASSOC);
            $total_reg_empresa = @count($res_empresa);

              if($total_reg_empresa > 0){ 
                
                $nome_empresa = $res_empresa[0]['nome'];
                ?>
                <span class="modal-title" style=" font-size: 14px; text-transform: uppercase; text-align: left;"><?php echo $nome_empresa ?></span>
            <?php } ?>    
    </a>
 
   <left> <!-- alinha o grupo a direira -->
      <div  class="collapse navbar-collapse" id="navbarSupportedContent">
        <div style="text-align: left;" class="d-flex mx-10">
            <?php         
                //RECUPERAR quantidade de mensagens 

               $query3 = $pdo->query("SELECT COUNT(id) as tot FROM `avisos` ");
               $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
               $qt_avisos = $res3[0]['tot'];   


               $query4 = $pdo->query("SELECT foto as icon_in FROM `informacoes_do_estabelecimento`");
               $res4 = $query4->fetchAll(PDO::FETCH_ASSOC);
               $icon_in4 = $res4[0]['icon_in']; 

            ?>

             
<br></br>
           
            <a  style="font-size: 10px; color:#31900c;  font-style:normal;" class="dropdown-item"  href="https://api.whatsapp.com/send/?phone=555196401579&text&app_absent=0" target="_blank"  >Ajuda</a>


            <!-- numero de avisos salvos -->
            <a style="font-size: 10px; font-style:normal;" class="nav-link" href="index.php?pagina=ler_aviso">
              <span style="font-size: 10px; font-style: normal; color: green; white-space: nowrap;"><?php echo $qt_avisos . " Avisos                    teste  teste teste          teste  "; ?></span>
            </a>

           
             <hr class="dropdown-divider"> 
 
            <!-- foto usuario  styles para deixa circular-->
            <div>
               <img   style=" border-radius: 50%; width: 35px; height: 35px;" 
                  src="../img/usuarios/<?php echo $foto ?>" width="35" height="35" >
            </div>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="true" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
      
          <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
            <ul class="navbar-nav">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" style="font-size: 14px" data-bs-toggle="dropdown" aria-expanded="false">
                  <?php echo $nome_usu ?>
                </a>

                <ul class="dropdown-menu dropdown-menu" aria-labelledby="navbarDarkDropdownMenuLink">
                  
                  <li><a  style="font-size: 12px; color: black;  font-style:normal;" class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#modalPerfil">Editar Perfil</a></li>         
                 
                  <li><hr class="dropdown-divider"></li>
                  
                  <li><a style="font-size: 12px; color: black;  font-style:normal;" class="dropdown-item" href="../logout.php">Sair</a></li>
                  
                </ul>

              </li>
            </ul>
          </div> 
            <div>
               <img   style=" border-radius: 50%; width: 35px; height: 35px;" 
                  src="../img/informacoes_do_estabelecimento/<?php echo $icon_in4 ?>" width="25" height="25" >
            </div>
         </div>
      </div>
    </right>
    </div>
</nav>  <!-- fim menu, logo, aviso e usuario -->



<!--MENU de opções -->

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">


      <ul class="navbar-nav me-auto mb-2 mb-lg-0"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     

        
        
        <!--AGENDA-->
        <li class="nav-item dropdown"> <!-- link drop menu -->

          <center></center> <!-- <img src="../img/icones/agenda.png" width="40px" height="40px">img sobre o menu -->

          <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Agenda
          </a>

            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu57 ?>">Ver Agenda</a></li>

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu42 ?>">Agendar Atendimento</a></li>

              <!--
              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu34 ?>">Agendar Consultas/Avaliação</a></li>
              -->

               <!--
               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu24 ?>">Quadro Agenda por Horário</a></li>

               -->

               <hr class="dropdown-divider"> <!-- add linha -->

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="">Tele Atendimento/Consulta</a></li>


               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="../agenda/index.php" target="_blank">Calendario do Grupo</a></li>

               <!-- envia para outra parte de aplicação link para pasta--> 
               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="kanban/index.php" target="_blank">Kanban</a></li>

                <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="alarme/index.html" target="_blank">Alarme</a></li>  

                <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="#" target="_blank">Lista de Tarefas</a></li>     
            
            </ul>
        </li>

        <!--CADASTROS -->
        <li class="nav-item dropdown">

          <center></center> <!--<img src="../img/icones/Cadastro.png" width="40px" height="40px""> -->

          <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Cadastros
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">


            <!--
             <li><a style="font-size: 16px;  font-style:bold;" class="dropdown-item" href="abrir_orcamentos.php">obsoleto ATENDIMENTO / COMANDAS / VENDER</a></li>
           -->

                  

             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="../consultas/index.php" target="_blank">NOVA Anamnese / Prontuário</a></li>

             <li><a style="font-size: 13px;  font-style:bold;" class="dropdown-item" href="../consultas/consulta_ana.php" target="_blank">CONSULTAR Anamnese / Prontuário</a></li>

              <hr class="dropdown-divider">

              <!--
              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="../consultas/index.php" target="_blank">Nova&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; Bioimpedância</a></li>

             <li><a style="font-size: 13px;  font-style:bold;" class="dropdown-item" href="../consultas/consulta_ana.php" target="_blank">Consultar Bioimpedância</a></li>

              <hr class="dropdown-divider"> -->


              <!--
              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="../consultas/index.php" target="_blank">Nova&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; Avaliação Facial/Corporal</a></li>

             <li><a style="font-size: 13px;  font-style:bold;" class="dropdown-item" href="../consultas/consulta_ana.php" target="_blank">Consultar Avaliação Facial/Corporal</a></li>

              <hr class="dropdown-divider">
            -->



             <li><a style="font-size: 13px;  font-style:bold;" class="dropdown-item" href="../consultas/acompanhamento.php" target="_blank">NOVO Acompanhamento de Paciente</a></li>

             <li><a style="font-size: 13px;  font-style:bold;" class="dropdown-item" href="../consultas/consultar_acompanhamento.php" target="_blank">CONSULTAR Acompanhamento de Paciente</a></li>


              <hr class="dropdown-divider">


             <li><a style="font-size: 13px;  font-style:bold;" class="dropdown-item" href="../consultas/proposta_de_vendas.php" target="_blank">NOVA Proposta de Vendas</a></li>

             <li><a style="font-size: 13px;  font-style:bold;" class="dropdown-item" href="index.php?pagina=<?php echo $menu76 ?>" >CONSULTAR Propostas de Venda</a></li>

            <!-- 
            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu23 ?>">CONSULTAR Vendas por Cliente</a></li>
            -->

            <hr class="dropdown-divider">

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu2 ?>">Usuários</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu13 ?>">Clientes</a></li>
             
            <hr class="dropdown-divider"> <!-- linha de separação -->

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu3 ?>">Fornecedores</a></li>
         

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu16 ?>">Equipamentos</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu17 ?>">Salas</a></li> 

             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu52 ?>">Locais de Estoque</a></li>  

              <hr class="dropdown-divider"> <!-- linha de separação -->


              <!--<li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu36 ?>">Listar Termos e Assinaturas</a></li> -->

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu50 ?>">Lista de Modelos de Contratos</a></li> 
  


              <!-- <hr class="dropdown-divider">  linha de separação --> 

               <!--
               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu47 ?>">Imagem Colaborador</a></li>

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu14 ?>">Documentos Colaborador</a></li>

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu25 ?>">Contratos Colaboradores</a></li>

                <hr class="dropdown-divider"> -->

               <!--
               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu48 ?>">Imagem Clientes</a></li>

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu44 ?>">Documentos Clientes</a></li>

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu35 ?>">Contratos Clientes</a></li>

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu49 ?>">Creditos Clientes</a></li>

              <hr class="dropdown-divider"> -->

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu38 ?>">Atas Diarias Recepção</a></li>

  
         
          </ul>
        </li>
      
        <li class="nav-item dropdown">
          <center></center><!--<img src="../img/icones/produtos.png" width="40px" height="40px"> -->

          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px;  font-style:normal;">
            Produtos/Serviços
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

             

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu5 ?>">Cadastro de Produtos</a></li>

            <!--<li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="painel-operador/index.php">Venda de Produtos</a></li>-->
            

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu12 ?>">Cadastro de Seviço</a></li>

            <!--<li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="painel-operador/index.php">Venda de Seviço</a></li>-->
            

            <!-- 
            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" 

              href="index.php?pagina=<?php echo $menu22 ?>">Comandas</a></li>
            -->
            
        
            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu4 ?>">Cadastro de Categorias</a></li>
            <li>

            <hr class="dropdown-divider">

              <!--
            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu18 ?>">Cadastro de Pacotes</a></li>
            <li>
              -->

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu19 ?>">Cadastro de Promoções</a></li>
            <li>


            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu39 ?>">Cadastro Cortesias</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu37 ?>">Cadastro Comissões</a></li>

               <hr class="dropdown-divider">

            <li><a style="font-size: 13px;  font-style:bold;" class="dropdown-item" href="index.php?pagina=<?php echo $menu64 ?>">Cadastro Carta de Cliente</a></li>


            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu65 ?>">Cadastrar Curriculos</a></li>

            

          </ul>
        </li>


         <li class="nav-item dropdown">

          <center></center> <!--<img src="../img/icones/vendas.png" width="40px" height="40px""> -->

          <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Vendas / Compras
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">


            <li><a style="font-size: 16px;  font-style:bold;" class="dropdown-item" href="index.php?pagina=<?php echo $menu62 ?>">PRÉ VENDA SERVIÇOS</a></li>

             <li><a style="font-size: 16px;  font-style:bold;" class="dropdown-item" href="index.php?pagina=<?php echo $menu63 ?>">Consultar Vendas por cliente</a></li>

            <hr class="dropdown-divider">     


             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu9 ?>">Valores das Vendas</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu10 ?>">Valores nos Caixas</a></li>           

            <hr class="dropdown-divider">

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu31 ?>">Lista de Compras a Fazer</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu6 ?>">Lista de Compras Efetuadas</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu11 ?>">Estoque Baixo</a></li>

            


         
           
          </ul>
        </li>


        <li class="nav-item dropdown">
          
          <center></center><!--<img src="../img/icones/icone_0123.png" width="40px" height="40px""> -->

          <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Relatórios/ Infos
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="" target="_blank">Dash Bord (BI)</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu40 ?>">Editar Avisos</a></li>

            <!--
            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu41 ?>">Ler Avisos</a></li>
            -->

            <hr class="dropdown-divider"> <!-- linha de separação -->

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="../rel/relProdutos_class.php" target="_blank">Relatório de Produtos</a></li>

             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#ModalRelCompras">Relatório de Compras</a></li>

             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#ModalRelVendas">Relatório de Vendas</a></li>

             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#ModalRelProdVendidos">Relatório de Produtos Vendidos</a></li>

             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#ModalRelQuebras">Relatório de Quebras</a></li>

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#ModalRelLucro">Relatório de Lucro</a></li>

              <hr class="dropdown-divider"> <!-- linha de separação -->

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu68 ?>">POPs (procedimento operacional padrão)</a></li> 

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu69 ?>">Padrões de Atendimento</a></li> 
           
           
          </ul>
        </li>



        <li class="nav-item dropdown">

          <center></center><!--<img src="../img/icones/marketing.png" width="40px" height="40px""> -->

          <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Marketing
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu26 ?>">Leads</a></li>
             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu79 ?>">Aniversariantes</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="">Email Marketing</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu524 ?>">Agendamento Online</a></li>

             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu46 ?>">Galeria/Resultados</a></li>


             <!--<hr class="dropdown-divider">  


            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu26 ?>">Leads de Cadastro</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu27 ?>">Leads Interesses</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu28 ?>">Leads Lembretes Hoje</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu29 ?>">Leads Lembretes Futuros</a></li>

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu30 ?>">Leads Lembretes Expirados</a></li>
          -->

            <hr class="dropdown-divider"> <!-- linha de separação -->

           <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu32 ?>">Dicas/Perguntas e Resp.</a></li>

            <!--<li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu33 ?>">Dicas/Pré Agendamento</a></li> -->

            


          </ul>
        </li>

        <!-- ================================================================================================ --> 

        <li class="nav-item dropdown">

          <center></center> <!--<img src="../img/icones/link01.png" width="40px" height="40px""> -->

          <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Links
          </a>
          
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu66 ?>">Cadastrar Link</a></li>
       
             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu45 ?>">Tutoriais</a></li>

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="">Cartão Presente</a></li>

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="../pdfs/Termos_de_Uso_Plataforma.pdf" target="_blank">Termos de Uso</a></li>

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="../pdfs/PoliticaPrivacidade2023.pdf" target="_blank">Politica de Privacidade</a></li>

          </ul>
        </li>

        <!-- ================================================================================================ --> 

        <li class="nav-item dropdown">

          <center></center><!-- <img src="../img/icones/config.png" width="40px" height="40px""> -->

          <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Configurações
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

            <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu51 ?>">             

             <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu51 ?>">Informaçõs da Empresa</a></li>

               <hr class="dropdown-divider"> <!-- linha de separação --> 
             

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu53 ?>">Datas/Validades</a></li>      

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu54 ?>">Conta Bancarias</a></li> 

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu55 ?>">Bandeiras Débito</a></li> 

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu56 ?>">Bandeiras Crédito</a></li>

              <hr class="dropdown-divider"> <!-- linha de separação -->

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu7 ?>">Adicionar Caixas</a></li>

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu8 ?>">Formas de Pagamento</a></li> 

              <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu20 ?>">Contas Contábeis</a></li> 

              <hr class="dropdown-divider"> <!-- linha de separação --> 

              <center><p>Categorias </br>Receitas e Despesas</p></center>

              <hr class="dropdown-divider"> <!-- linha de separação --> 

               <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu58 ?>">Receitas</a></li>

                <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu59 ?>">Despesas</a></li>

                <hr class="dropdown-divider"> <!-- linha de separação --> 

                 <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu60 ?>">Sub Categoria de Receita</a></li>

                <li><a style="font-size: 13px;  font-style:normal;" class="dropdown-item" href="index.php?pagina=<?php echo $menu61 ?>">Sub Categoria de Despesa</a></li>



          </ul>
        </li>

        <!-- ================================================================================================ --> 

        <li class="nav-item dropdown">

          <center></center><!--<img src="../img/icones/paine01.png" width="40px" height="40px"> -->

          <a style="font-size: 13px;  font-style:normal;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Painéis
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

            <li class="nav-item">
          <a style="font-size: 13px;  font-style:normal;" class="nav-link" href="" >Painel Financeiro</a>
        </li>
      
        <li class="nav-item">
          <a style="font-size: 13px;  font-style:normal;" class="nav-link" href="" >Painel Operador</a>
        </li>

        <li class="nav-item">
          <a style="font-size: 13px;  font-style:normal;" class="nav-link" href="#" target="_blank">Painel Vendas</a>
        </li>

        <li class="nav-item">
          <a style="font-size: 13px;  font-style:normal;" class="nav-link" href="../painel-cliente" >Painel Cliente</a>
        </li>

        <li><a style="font-size: 13px;  font-style:normal;" class="nav-link" href="../loja/index.php" >Painel Loja Virtual</a></li>

        <li><a style="font-size: 13px;  font-style:normal;" class="nav-link" href="" >Painel Cursos (Em Andamento)</a></li>

          </ul>
        </li>

      </ul>

    </div>
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

else if(@$_GET['pagina'] == $menu16){
  require_once($menu16. '.php');
}

else if(@$_GET['pagina'] == $menu17){
  require_once($menu17. '.php');
}

else if(@$_GET['pagina'] == $menu18){
  require_once($menu18. '.php');
}

else if(@$_GET['pagina'] == $menu19){
  require_once($menu19. '.php');
}

else if(@$_GET['pagina'] == $menu20){
  require_once($menu20. '.php');
}

else if(@$_GET['pagina'] == $menu21){
  require_once($menu21. '.php');
}

else if(@$_GET['pagina'] == $menu22){
  require_once($menu22. '.php');
}

else if(@$_GET['pagina'] == $menu23){
  require_once($menu23. '.php');
}

else if(@$_GET['pagina'] == $menu24){
  require_once($menu24. '.php');
}

else if(@$_GET['pagina'] == $menu25){
  require_once($menu25. '.php');
}

else if(@$_GET['pagina'] == $menu26){
  require_once($menu26. '.php');
}

else if(@$_GET['pagina'] == $menu27){
  require_once($menu27. '.php');
}

else if(@$_GET['pagina'] == $menu28){
  require_once($menu28. '.php');
}

else if(@$_GET['pagina'] == $menu29){
  require_once($menu29. '.php');
}

else if(@$_GET['pagina'] == $menu30){
  require_once($menu30. '.php');
}

else if(@$_GET['pagina'] == $menu31){
  require_once($menu31. '.php');
}

else if(@$_GET['pagina'] == $menu32){
  require_once($menu32. '.php');
}

else if(@$_GET['pagina'] == $menu33){
  require_once($menu33. '.php');
}

else if(@$_GET['pagina'] == $menu34){
  require_once($menu34. '.php');
}

else if(@$_GET['pagina'] == $menu35){
  require_once($menu35. '.php');
}

else if(@$_GET['pagina'] == $menu36){
  require_once($menu36. '.php');
}

else if(@$_GET['pagina'] == $menu37){
  require_once($menu37. '.php');
}

else if(@$_GET['pagina'] == $menu38){
  require_once($menu38. '.php');
}

else if(@$_GET['pagina'] == $menu39){
  require_once($menu39. '.php');
}

else if(@$_GET['pagina'] == $menu40){
  require_once($menu40. '.php');
}

else if(@$_GET['pagina'] == $menu41){
  require_once($menu41. '.php');
}

else if(@$_GET['pagina'] == $menu42){
  require_once($menu42. '.php');
}

else if(@$_GET['pagina'] == $menu43){
  require_once($menu43. '.php');
}

else if(@$_GET['pagina'] == $menu44){
  require_once($menu44. '.php');
}

else if(@$_GET['pagina'] == $menu45){
  require_once($menu45. '.php');
}

else if(@$_GET['pagina'] == $menu46){
  require_once($menu46. '.php');
}

else if(@$_GET['pagina'] == $menu47){
  require_once($menu47. '.php');
}

else if(@$_GET['pagina'] == $menu48){
  require_once($menu48. '.php');
}

else if(@$_GET['pagina'] == $menu49){
  require_once($menu49. '.php');
}

else if(@$_GET['pagina'] == $menu50){
  require_once($menu50. '.php');
}

else if(@$_GET['pagina'] == $menu51){
  require_once($menu51. '.php');
}

else if(@$_GET['pagina'] == $menu52){
  require_once($menu52. '.php');
}

else if(@$_GET['pagina'] == $menu53){
  require_once($menu53. '.php');
}

else if(@$_GET['pagina'] == $menu54){
  require_once($menu54. '.php');
}

else if(@$_GET['pagina'] == $menu55){
  require_once($menu55. '.php');
}

else if(@$_GET['pagina'] == $menu56){
  require_once($menu56. '.php');
}

else if(@$_GET['pagina'] == $menu57){
  require_once($menu57. '.php');
}

else if(@$_GET['pagina'] == $menu58){
  require_once($menu58. '.php');
}

else if(@$_GET['pagina'] == $menu59){
  require_once($menu59. '.php');
}
else if(@$_GET['pagina'] == $menu60){
  require_once($menu60. '.php');
}

else if(@$_GET['pagina'] == $menu61){
  require_once($menu61. '.php');
}

else if(@$_GET['pagina'] == $menu62){
  require_once($menu62. '.php');
}

else if(@$_GET['pagina'] == $menu63){
  require_once($menu63. '.php');
}

else if(@$_GET['pagina'] == $menu64){
  require_once($menu64. '.php');
}

else if(@$_GET['pagina'] == $menu65){
  require_once($menu65. '.php');
}

else if(@$_GET['pagina'] == $menu66){
  require_once($menu66. '.php');
}

else if(@$_GET['pagina'] == $menu67){
  require_once($menu67. '.php');
}

else if(@$_GET['pagina'] == $menu68){
  require_once($menu68. '.php');
}

else if(@$_GET['pagina'] == $menu69){
  require_once($menu69. '.php');
}

else if(@$_GET['pagina'] == $menu70){
  require_once($menu70. '.php');
}

else if(@$_GET['pagina'] == $menu71){
  require_once($menu71. '.php');
}

else if(@$_GET['pagina'] == $menu72){
  require_once($menu72. '.php');
}

else if(@$_GET['pagina'] == $menu73){
  require_once($menu73. '.php');
}

else if(@$_GET['pagina'] == $menu74){
  require_once($menu74. '.php');
}

else if(@$_GET['pagina'] == $menu75){
  require_once($menu75. '.php');
}

else if(@$_GET['pagina'] == $menu76){
  require_once($menu76. '.php');
}

else if(@$_GET['pagina'] == $menu77){
  require_once($menu77. '.php');
}

else if(@$_GET['pagina'] == $menu78){
  require_once($menu78. '.php');
}

else if(@$_GET['pagina'] == $menu79){
  require_once($menu79. '.php');
}




else{
	require_once($menu1. '.php');
}

 ?>
</div>






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
						<div class="col-md-12">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Nome</label>
								<input type="text" class="form-control" id="nome-perfil" name="nome-perfil" placeholder="Nome" required="" value="<?php echo @$nome_usu ?>">
							</div> 
						</div>
					<div>



					

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Email</label>
						<input type="email" class="form-control" id="email-perfil" name="email-perfil" placeholder="Email" required="" value="<?php echo @$email_usu ?>">
					</div>  

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Senha</label>
						<input type="password" class="form-control" id="senha-perfil" name="senha-perfil" placeholder="Senha" required="" value="<?php echo @$senha_usu ?>">
					</div>  

					

					<small><div align="center" class="mt-1" id="mensagem-perfil">
						
					</div> </small>

				</div>
				<div class="modal-footer">
					<button type="button" id="btn-fechar-perfil" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar-perfil" id="btn-salvar-perfil" type="submit" class="btn btn-primary">Salvar</button>

					<input name="id-perfil" type="hidden" value="<?php echo @$id_usu ?>">

					<input name="antigo-perfil" type="hidden" value="<?php echo @$cpf_usu ?>">
					<input name="antigo2-perfil" type="hidden" value="<?php echo @$email_usu ?>">

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


<!-- script para mostrar a mensagem de cooks//// revisar revisar revisar revisar::: https://drcode.com.br/blog/jquery/aviso-de-cookies-para-meu-site-como-colocar/ -->
<script src="https://www.drcode.com.br/nofollow/aviso-cookies/drcode.cookies.js"></script>
<script>
  avisoCookiesDrcode({
    message:'Utilizamos cookies para que você tenha a melhor experiência em nossa página. Para saber mais acesse nossa página de Política de Privacidade nos Links',
    backgroundColor:'rgba(255,255,255,0.95)',
    textColor:'#666666',
    buttonBackgoundColor:'#0e9a20',
    buttonHoverBackgoundColor:'#0a6b16',
    buttonTextColor:'#ffffff'
})
</script>





<!-- /Notificação de compra/-->  <!-- https://www.youtube.com/watch?v=MuO4JmxlK34&ab_channel=DyeffMafra-EmpreendedorDigital -->

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
 
        var min_time = 40;
        var max_time = 2000;
 
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






<?php

//RECUPERAR DADOS DOS AVISOS

$query = $pdo->query("SELECT * from avisos ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$hora_inicio = $res[0]['hora_inicio'];
$interesse = $res[0]['interesse'];


 $agora = date('d-m-Y H:i:s');
 //$agora_final = 

  if ($hora_inicio > $agora) { echo  " ";?>


    <div class="modal fade" tabindex="-1" id="aviso_01" data-bs-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">

          <!-- inicio avisos por horario -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
              Launch static backdrop modal
            </button>

            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    ...
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Understood</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- fim avisos por horario -->

        </div>
       </div>
      </div>
    </div>


<?php  
  }
?>









<!--

</br></br></br></br>
<footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <center>

              <p>Copyright © <script>document.write(new Date().getFullYear());</script> OnlySoft Inst, All Rights Reserved. 
              <br>Design: <a href="https://onlysoftinstituto.com/onlysoft" target="_blank" title="Devs">OnlySoft Team</a></p>
              
          </center>
        </div>
      </div>
    </div>
  </footer>
  </br>

-->



</body>
</html>