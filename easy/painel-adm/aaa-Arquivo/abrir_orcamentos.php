<?php 
$pag = 'abrir_orcamentos';

@session_start();
#include('verificar_login.php');
include('../conexao/conexao.php');
require_once('../conexao.php');
require_once('verificar-permissao.php');

$id_usuario = $_SESSION['id_usuario'];
gerarMenu($pag, $grupos);

?>



<!DOCTYPE html>
<html>
<head>
  <title>Venda de Serviços</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
  <!-- CDN PARA O SELECT 2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
<?php  gerarMenu($pag, $grupos); ?>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">   
      <a class="btn btn-primary btn-sm" href="index.php">Voltar</a>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#conteudoNavbarSuportado" aria-controls="conteudoNavbarSuportado" aria-expanded="false" aria-label="Alterna navegação">
        <span class="navbar-toggler-icon"></span>
      </button>

  <div class="collapse navbar-collapse" id="conteudoNavbarSuportado">
      <ul class="navbar-nav mr-auto"></ul>

      <!--- /////////////// busca por cliente/////////////// -->
      <form class="form-inline my-2 my-lg-0"  method="POST" action="registra_comanda.php">

        <label for="">Buscar Por Vendas &nbsp;</label>    
        <select data-width="100%" class="form-control mr-1" id="selec_cli" name="txt_cli">
            
            <?php
              $query = "SELECT DISTINCT cliente, cliente_nome FROM orcamentos ORDER BY cliente asc";
              $result = mysqli_query($conexao, $query);

              if(mysqli_num_rows($result)){
                while($res_1 = mysqli_fetch_array($result)){
                 ?>                                             
                 <option value="<?php echo $res_1['cliente']; ?>"> <!-- valor da variavel -->
                  <?php echo $res_1['cliente_nome']; ?> </option>  <!-- valor mostrado -->
                 <?php      
               }
             }
             ?>
          </select>

          <button name="buttonPesquisar" class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fa fa-search"></i></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               
     </form>
     <!--- /////////////// Fim busca por cliente/////////////// -->

     <form class="form-inline my-2 my-lg-0">
        <select class="form-control mr-2" id="category" name="status">
         <option value="Todos">Todos</option> 
         <option value="Aberto">Aberto</option> 
         <option value="Aguardando">Aguardando</option> 
         <option value="Aprovado">Aprovado</option>
          <!---<option value="Aprovado">Pago</option>-->
         <option value="Aprovado">Pendente</option> 
          <!---<option value="Cancelado">Cancelado</option> -->
       </select>

       <input name="txtpesquisar" class="form-control mr-sm-2" type="date" placeholder="Pesquisar" aria-label="Pesquisar">

       <button name="buttonPesquisar" class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fa fa-search"></i></button>

     </form>
   </div>
 </nav>

 <div class="container">
  <br>
    <div class="row">
       <div class="col-sm-6">
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalExemplo">Inserir Novo </button>
      </div>
    </div>
 </div>

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Vendas Abertas</h4>
        </div>

        <div class="card-body">
          <div class="table-responsive">

            <!--LISTAR TODOS OS ORÇAMENTOS -->
          <?php
            if(isset($_GET['buttonPesquisar']) and $_GET['txtpesquisar'] != '' and $_GET['status'] != 'Todos' ){
              
              $data = $_GET['txtpesquisar'] . '%';
              $statusOrc = $_GET['status'];

              $query = "select o.data_abertura, o.id, o.cliente, o.tecnico, o.produto, o.valor_total as valor_total, o.qtd, o.obs, o.status, fp.nome as for_pag, s.valor_venda as ser_valor, s.nome as ser_nome, c.nome as cli_nome, c.id as cli_id, f.nome as func_nome, o.desconto as desconto_final from 
                orcamentos as o 
                INNER JOIN clientes as c on o.cliente = c.id 
                INNER JOIN usuarios as f on o.tecnico = f.id
                INNER JOIN servicos as s on o.produto = s.id
                INNER JOIN forma_pgtos as fp on o.id_form_pag = fp.id 
                where data_abertura = '$data' and status = '$statusOrc' and status != 'Pago' and status != 'Cancelado' order by id asc";

            }else if(isset($_GET['buttonPesquisar']) and $_GET['txtpesquisar'] == '' and $_GET['status'] != 'Todos'){
             $statusOrc = $_GET['status'];
             $query = "select o.data_abertura, o.id, o.cliente, o.tecnico, o.produto, o.valor_total as valor_total, o.qtd, o.obs, o.status, fp.nome as for_pag, s.valor_venda as ser_valor, s.nome as ser_nome, c.nome as cli_nome, c.id as cli_id, f.nome as func_nome, o.desconto as desconto_final 
             from orcamentos as o 
             INNER JOIN clientes as c on o.cliente = c.id 
             INNER JOIN usuarios as f on o.tecnico = f.id
             INNER JOIN servicos as s on o.produto = s.id
             INNER JOIN forma_pgtos as fp on o.id_form_pag = fp.id 
             where data_abertura = curDate() and status = '$statusOrc' and status != 'Pago' and status != 'Cancelado' order by id asc"; 

           }else if(isset($_GET['buttonPesquisar']) and $_GET['txtpesquisar']!= '' and $_GET['status'] == 'Todos'){
             $data = $_GET['txtpesquisar'] . '%';
             $query = "select o.data_abertura, o.id, o.cliente, o.tecnico, o.produto, o.valor_total as valor_total, o.qtd, o.obs, o.status, fp.nome as for_pag, s.valor_venda as ser_valor, s.nome as ser_nome, c.nome as cli_nome, c.id as cli_id, f.nome as func_nome, o.desconto as desconto_final from 
             orcamentos as o 
             INNER JOIN clientes as c on o.cliente = c.id 
             INNER JOIN usuarios as f on o.tecnico = f.id
             INNER JOIN servicos as s on o.produto = s.id
             INNER JOIN forma_pgtos as fp on o.id_form_pag = fp.id 
             where data_abertura = '$data' and status != 'Pago' and status != 'Cancelado' order by id asc"; 

           }else{
             $query = "select o.data_abertura, o.id, o.cliente, o.tecnico, o.produto, o.valor_total as valor_total, o.qtd, o.obs, o.status, fp.nome as for_pag, s.valor_venda as ser_valor, s.nome as ser_nome, c.nome as cli_nome, c.id as cli_id, f.nome as func_nome, o.desconto as desconto_final from 
             orcamentos as o 
             INNER JOIN clientes as c on o.cliente = c.id 
             INNER JOIN usuarios as f on o.tecnico = f.id
             INNER JOIN servicos as s on o.produto = s.id
             INNER JOIN forma_pgtos as fp on o.id_form_pag = fp.id  
             where data_abertura = curDate() and status != 'Pago' and status != 'Cancelado' order by id asc"; 
           }

           $result = mysqli_query($conexao, $query);
           //$dado = mysqli_fetch_array($result);
           $row = mysqli_num_rows($result);

            if($row == '' or $row == 0){

            echo "<h5> Não existem dados cadastrados nesta data</h5>";
          }else{
           ?>

    <table class="table">
        <thead class=" text-primary">

          <th>
            Data Abertura
          </th>
          <!--<th>
            Cliente id
          </th>-->
          <th>
            Cliente
          </th>
          
          <th>
            Serviço
          </th>
          <th>
            Valor Uni.
          </th>          
          <th>
            Qtd
          </th>
          <th>
            Desc.
          </th>
          <th>
            Total 
          </th>
          <th>
            Valor Final.
          </th>
          <th>
            forma Pag.
          </th>
          <th>
            Status
          </th>         
          <th>
            Obs.
          </th>
        
          <th>
            Ações
          </th>
        </thead>

        <tbody>
             <?php /*Busca no banco os dados para mostrar na lista. cria as variaveis*/
               while($res_1 = mysqli_fetch_array($result)){

                $data_abertura = $res_1["data_abertura"];
                $cliente = $res_1["cli_id"];
                $clienteid = $res_1["cli_nome"];
                //$tecnico = $res_1["func_nome"];
                $produto = $res_1["ser_nome"]; 

                $valor_fechado_vendedor_1 = $res_1["valor_total"];
                $valor_venda_uni_1 = $res_1["ser_valor"]; /*recebe o valor unitario do serviço*/
                $ser_valor1 = $res_1["ser_valor"];/*recebe o valor unitario do serviço*/
                
                $qtd = $res_1["qtd"];
                $desc = $res_1["desconto_final"];
                $for_pag = $res_1["for_pag"];
                $status = $res_1["status"];
       
                $obs = $res_1["obs"];
                $id = $res_1["id"];

                /*Formata os valores para moeda*/
                $ser_valor = number_format($ser_valor1, 2, ',', '.');
                $valor_venda_uni = number_format($valor_venda_uni_1, 2, ',', '.');
                $valor_fechado_vendedor = number_format($valor_fechado_vendedor_1, 2, ',', '.');

                // multiplica o valor pela quantidade para mostrar na tela
                $total_1 = doubleval($ser_valor1 * $qtd);
                $total_original = number_format($total_1, 2, ',', '.'); 
              ?>

               <!-- Tabela, Plota na tela as informaçoes salvas nas variaveis acima -->
               <tr>
                 <td> <!-- muda a data para a forma brasil  -->
                    <?php echo implode('/', array_reverse(explode('-', $data_abertura))); ?>
                 </td>

                 <!--<td><?php echo $cliente; ?></td>-->
                 <td><?php echo $clienteid; ?></td>

                 <td><?php echo $produto; ?></td>
                 <td><?php echo $valor_venda_uni; ?></td>           
                 <td><?php echo $qtd; ?></td> 
                 <td><?php echo '%'.$desc; ?>

                 <td>R$ <?php echo $total_original; ?></td>
                 <td>R$ <?php echo $valor_fechado_vendedor; ?></td>

                 <td><?php echo $for_pag; ?></td>
                 <td><?php echo $status; ?></td>          
                 <td><?php echo $obs; ?></td>

                 <td> <!-- botões de acão -->
                   <a class="btn btn-info" href="abrir_orcamentos.php?func=edita&id=<?php echo $id; ?>"><i class="fa fa-pencil-square-o"></i></a>

                   <!--<a class="btn btn-danger" href="abrir_orcamentos.php?func=deleta&id=<?php echo $id; ?>"><i class="fa fa-minus-square"></i></a> -->                        
                 </td>
             </tr>

          <?php 
            }                        
          ?>
        </tbody>
   </table>
   <?php 
     }                        
   ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<!-- Modal -->
<!-- Modal -->
<div id="modalExemplo" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
   <!-- Modal content-->
     <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Novo Orçamento</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <form method="POST" action="">
            <div class="row">
              <div class="col-md-6">
               <div class="form-group">

                 <label for="fornecedor">Cliente</label>
                 <select data-width="100%" class="form-control mr-2" id="select2cli" name="txt_id_cliente">
                  <?php

                    $query = "SELECT * FROM clientes ORDER BY nome asc";
                    $result = mysqli_query($conexao, $query);

                    if(mysqli_num_rows($result)){
                      while($res_1 = mysqli_fetch_array($result)){
                       ?>                                             
                       <option value="<?php echo $res_1['id']; ?>"><!--valor da variavel-->
                        <?php echo $res_1['nome']; ?> </option><!--o que mostra na opção de escolha-->
                       <?php      
                     }
                   }
                 ?>
               </select>

             </div>
           </div>

           <div class="col-md-6">
            <div class="form-group"> 

             <label for="fornecedor">Profissional</label>
             <select data-width="100%" class="form-control mr-2 select2" id="cat" name="funcionario">
              <?php
                $query = "SELECT * FROM usuarios where ativo_na_agenda = 'Ativo' ORDER BY nome asc";
                $result = mysqli_query($conexao, $query);

                if(mysqli_num_rows($result)){
                  while($res_1 = mysqli_fetch_array($result)){
                   ?>                                             
                   <option value="<?php echo $res_1['id']; ?>"><!--valor da variavel-->
                    <?php echo $res_1['nome']; ?></option> <!--o que mostra na opção de escolha-->
              <?php      
                 }
               }
              ?>
           </select>

         </div>
       </div>
     </div>


     <div class="row">
      <div class="col-md-4">
       <div class="form-group">
        
        <label for="quantidade">Serviço</label>
            <select data-width="100%" class="form-control mr-2" id="select2cli" name="txtproduto">
              <?php

                  $query = "SELECT * FROM servicos ORDER BY nome asc";
                  $result = mysqli_query($conexao, $query);

                  if(mysqli_num_rows($result)){
                    while($res_1 = mysqli_fetch_array($result)){
                     ?>                                             
                     <option value="<?php echo $res_1['id']; ?>"><!--valor da variavel-->
                      <!--o que mostra na opção de escolha dois itens -->
                      <?php echo $res_1['nome']; ?> : R$<?php echo $res_1['valor_venda']; ?></option> 
                     <?php      
                   }
                 }
              ?>
            </select>
         
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">

          <label for="quantidade">Qtd.</label>
          <input type="number" class="form-control mr-2" name="txtserie" placeholder="Qtd" required>

        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          
            <label for="quantidade">Status</label>
              <select data-width="100%" class="form-control mr-2" id="select2cli" name="txtdefeito">
                <?php

                  $query = "SELECT * FROM status ORDER BY id asc";
                  $result = mysqli_query($conexao, $query);

                  if(mysqli_num_rows($result)){
                    while($res_1 = mysqli_fetch_array($result)){
                     ?>                                             
                     <option value="<?php echo $res_1['status']; ?>"><!--valor da variavel-->
                      <?php echo $res_1['status']; ?> </option><!--o que mostra na opção de escolha--> 
                     <?php      
                   }
                 }
               ?>
             </select>
         
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
            <label for="quantidade">Forma Pag.</label>
            <select data-width="100%" class="form-control mr-2" id="select2cli" name="txtfor_pag">
              <?php

              $query = "SELECT * FROM forma_pgtos ORDER BY id asc";
              $result = mysqli_query($conexao, $query);

              if(mysqli_num_rows($result)){
                while($res_1 = mysqli_fetch_array($result)){
                 ?>                                             
                 <option value="<?php echo $res_1['id']; ?>"><!--valor da variavel-->
                  <?php echo $res_1['nome']; ?> </option><!--o que mostra na opção de escolha-->
                 <?php      
               }
             }
             ?>
           </select>    
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">

          <label for="quantidade">Desconto %</label>
           <input type="text" class="form-control mr-2" name="desconto" placeholder="%" > 

        </div>  
      </div>

    </div> <!-- pula coluna. desce na modal -->


      <div class="form-group">

        <label for="quantidade">Observações</label>
        <input type="text" class="form-control mr-2" name="txtobs" placeholder="Observações">

      </div>

    </div>

      <div class="modal-footer"><!-- Botões -->
         <button type="submit" class="btn btn-success mb-3" name="button">Salvar </button>
         <button type="button" class="btn btn-danger mb-3" data-dismiss="modal">Cancelar </button>
      </div>

        </form>

        <?php clearstatcache(); /*Apaga os caches da modal*/?> 

      </div>
    </div>
  </div>
</div>

<!-- Fim Modal -->
<!-- Fim Modal -->
<!-- Fim Modal -->    

</body>
</html>



<!--CADASTRAR -->
<!--CADASTRAR -->
<!--CADASTRAR -->

<?php

  if(isset($_POST['button'])){ /*SE tem informaçoes vidas do botao da modal continua*/

    /*Recebe as informações vindas da modal para inserir no banco*/

    $salva_id_cliente = $_POST['txt_id_cliente']; // ID cliente vinda da modal

    $produto = $_POST['txtproduto'];  //ID servicos vindo da modal
    $for_pag = $_POST['txtfor_pag'];  // ID Forma de pagamento selecionada na modal

    @$valor_total = $_POST['txtvalor_total']; //Valor firmado pelo vendedor com ou sem desconto
    $serie = $_POST['txtserie'];   // quantidade em unidades
    $defeito = $_POST['txtdefeito']; //status
    $obs = $_POST['txtobs'];
    $created = date('d-m-Y H:i:s');
    $desconto = (float)$_POST['desconto'];




  /*BUSCA DE DADOS APARTIR DOS IDs. Para os calculos*/

  if(true){ /*busca o nome do cliente*/
    
    $query = "SELECT nome FROM clientes where id = $salva_id_cliente ORDER BY nome asc";
    $result = mysqli_query($conexao, $query);

    if(mysqli_num_rows($result)){
      while($res_1 = mysqli_fetch_array($result)){
                                                
       $salva_id_cliente1 = $res_1['nome'];  //Salva o nome atraves do ID         
     }
   }
  }

  if(true){ /*busca o valor unitario do produto*/
    
    $query2 = "SELECT * FROM servicos where id = $produto";
    $result2 = mysqli_query($conexao, $query2);

    if(mysqli_num_rows($result2)){
      while($res_1 = mysqli_fetch_array($result2)){
                                                
       $salva_valor_produto = $res_1['valor_venda'];  //Salva o valor unitario atualizado 
       $salva_nome_produto = $res_1['nome'];  /*Salva nome atual do produto no periodo da venda*/     
     }
   }
  }

  if(true){ /*Busca nome do tipo de pagamento*/
    
    $query3 = "SELECT * FROM forma_pgtos where id = $for_pag";
    $result3 = mysqli_query($conexao, $query3);

    if(mysqli_num_rows($result3)){
      while($res_1 = mysqli_fetch_array($result3)){
                                                
       $salva_nome_pagamento = $res_1['nome'];  /*Salva nome atual pgmt no periodo da venda*/     
     }
   }
  }

  /* CALCULOS */

  /*qt * preço*/
  $tot_valor_uni_mult_quantidade =  (float) $salva_valor_produto * $serie;
  
  /*quanto se deu de desconto*/
  $tot_de_desconto =   (float) $tot_valor_uni_mult_quantidade * ($desconto / 100);

  /*Total Final já com os descontos*/
  $tot_depois_do_desconto =  (float) $tot_valor_uni_mult_quantidade - $tot_de_desconto;

  $id_usuario = $_SESSION['id_usuario'];


  /*Grava no banco de dados a nova comanda*/

  $query = "INSERT into orcamentos (foto, pgto, item, valor_servico_sem_desconto, valor_em_desconto, valor_pecas, desconto, criado, cliente, cliente_nome, produto, id_form_pag, qtd, status, obs, valor_total, data_abertura) 

  VALUES ('sem-foto.jpg', '$salva_nome_pagamento','$salva_nome_produto','$tot_valor_uni_mult_quantidade','$tot_de_desconto','$salva_valor_produto','$desconto','$created', '$salva_id_cliente', '$salva_id_cliente1',  '$produto', '$for_pag', '$serie', '$defeito', '$obs', '$tot_depois_do_desconto',  curDate() )";

  $result = mysqli_query($conexao, $query);


  $query2 = "INSERT into creditos (usuario_referente_id, usuario_referente_nome, status, situacao, descricao, valor, usuario, data, created) 

  VALUES ('$salva_id_cliente', '$salva_id_cliente1', '$defeito', 'Não Utilizado', 'Vindo da Comanda', '$tot_depois_do_desconto', '$id_usuario', curDate(),  curDate() )";

  $result2 = mysqli_query($conexao, $query2);


  if($result == ''){
    echo "<script language='javascript'> window.alert('Ocorreu um erro ao Cadastrar!'); </script>";
  }else{
    echo "<script language='javascript'> window.alert('Salvo com Sucesso!'); </script>";
    echo "<script language='javascript'> window.location='abrir_orcamentos.php'; </script>";
  }

}
?>
 <!--Grava no banco de dados  a nova comanda-->
  <!-- FIM CADASTRAR -->
  <!-- FIM CADASTRAR -->
  <!-- FIM CADASTRAR -->




<!--EXCLUIR -->
<!--EXCLUIR -->
<!--EXCLUIR -->
<?php

  if(@$_GET['func'] == 'deleta'){

    $id = $_GET['id'];
    $query = "DELETE FROM orcamentos where id = '$id'";

    mysqli_query($conexao, $query);
    echo "<script language='javascript'> window.location='abrir_orcamentos.php'; </script>";
  }
?>
<!-- FIM EXCLUIR -->
<!-- FIM EXCLUIR -->
<!-- FIM EXCLUIR -->




<!--EDITAR -->
<!--EDITAR -->
<!--EDITAR -->

<?php

  if(@$_GET['func'] == 'edita'){  

    $id = $_GET['id']; /* Recebe o ID do cliente selecionado*/

    /* busca as informaçoes no banco*/
    $query = "select * from orcamentos where id = '$id'"; 
    $result = mysqli_query($conexao, $query);

    while($res_1 = mysqli_fetch_array($result)){

?>
    <!-- Modal para editar campos salvos no banco de dados -->
    <!-- Modal para editar campos salvos no banco de dados -->

    <div id="modalEditar" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
       <!-- Modal content-->
       <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title">Editar Orçamento</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>


      <div class="modal-body">
      <form method="POST" action="">
        <div class="row">
          <div class="col-md-6">
           <div class="form-group">
             
             <label for="fornecedor">Cliente</label>
             <select data-width="100%" class="form-control mr-2" id="select2cli2" name="txt_id_cliente">
              <?php

                $query = "SELECT * FROM clientes ORDER BY nome asc";
                $result = mysqli_query($conexao, $query);

                if(mysqli_num_rows($result)){
                  while($res_1c = mysqli_fetch_array($result)){
                   ?>                                             
                   <option <?php if($res_1c['id'] == $res_1['cliente']){ ?> selected <?php } ?> value="<?php echo $res_1c['id']; ?>">
                   <?php echo $res_1c['nome']; ?></option> 
                   <?php      
                 }
               }
             ?>
           </select>
         </div>
       </div>

      
 </div>


 <div class="row">
  <div class="col-md-4">
   <div class="form-group">

      <label for="quantidade">Serviços</label>
        <select data-width="100%" class="form-control mr-2 select2edit" id="cat2" name="txtproduto" required>
              <?php

              $query = "SELECT * FROM servicos ORDER BY nome asc";
              $result = mysqli_query($conexao, $query);

              if(mysqli_num_rows($result)){
                while($res_1t = mysqli_fetch_array($result)){
                 ?>                                             
                 <option <?php if($res_1t['id'] == $res_1['produto']){ ?> selected <?php } ?> value="<?php echo $res_1t['id']; ?>"><?php echo $res_1t['nome']; ?> : R$<?php echo $res_1t['valor_venda']; ?></option> 
                 <?php      
               }
             }
             ?>
           </select>

  </div>
</div>

<div class="col-md-4">
  <div class="form-group">
    <label for="quantidade">Qtd</label>
    <input type="text" class="form-control mr-2" name="txtserie" placeholder="Qtd" required value="<?php echo $res_1['qtd']; ?>">   
  </div>
</div>

<div class="col-md-4">
  <div class="form-group">
    
    <label for="quantidade">Status</label>
      <select data-width="100%" class="form-control mr-2" id="select2cli" name="txtdefeito">
                
            <?php

            $query = "SELECT * FROM status ORDER BY id asc";
            $result = mysqli_query($conexao, $query);

            if(mysqli_num_rows($result)){
              while($res_1t = mysqli_fetch_array($result)){
               ?>                                             
               <option <?php if($res_1t['status'] == $res_1['status']){ ?> selected <?php } ?> value="<?php echo $res_1t['status']; ?>"><?php echo $res_1t['status']; ?></option> 
               <?php      
             }
           }
           ?>
        </select>

  </div>
</div>

<div class="col-md-4">
  <div class="form-group">
    <label for="quantidade">Forma Pag.</label>


    <select data-width="100%" class="form-control mr-2" id="select2cli" name="txtfor_pag">
              <?php

          $query = "SELECT * FROM forma_pgtos ORDER BY nome asc";
          $result = mysqli_query($conexao, $query);

          if(mysqli_num_rows($result)){
            while($res_1t = mysqli_fetch_array($result)){
             ?>                                             
             
             <!--  primeio $res_1t['id'] vem do orcamento, segundo res_1[] é o campo da tabela pra cmpara com o segundo $res_1t['id'] para mostror o $res_1t['nome'] -->
             <option <?php if($res_1t['id'] == $res_1['id_form_pag']){ ?> selected <?php } ?> value="<?php echo $res_1t['id']; ?>"><?php echo $res_1t['nome']; ?></option> 
             <?php      
           }
         }
         ?>
      </select>
  </div>
</div>

<!--<div class="col-md-4">

  <div class="form-group">
    <label for="quantidade">Total Final R$</label>

    <input type="text" class="form-control mr-2" name="txtvalor_total" placeholder="" required value="<?php echo $res_1['valor_total']; ?>">
   
  </div>
</div>-->

<div class="col-md-4">
  <div class="form-group">
    <label for="quantidade">Desconto %</label>
    <input type="text" class="form-control mr-2" name="desconto" placeholder=""  value="<?php echo $res_1['desconto']; ?>">   
  </div>
</div>


</div>


  <div class="form-group">
    <label for="quantidade">Observações</label>
    <input type="text" class="form-control mr-2" name="txtobs" placeholder="Observações" value="<?php echo $res_1['obs']; ?>">
  </div>

</div>

      <div class="modal-footer">
          <button type="submit" class="btn btn-success mb-3" name="buttonEditar">Salvar </button>
          <button type="button" class="btn btn-danger mb-3" data-dismiss="modal">Cancelar </button>
      </div>

     </form>
     </div>
    </div>
  </div>
</div>    


<script> $("#modalEditar").modal("show"); </script> 
    <!-- Variaveis para editar os dados UPDATE -->
    <!-- Variaveis para editar os dados UPDATE -->
    <?php

      if(isset($_POST['buttonEditar'])){ /*SE tem informaçoes vidas do botao da modal continua*/

        
        $salva_id_cliente = $_POST['txt_id_cliente']; // ID cliente vinda da modal

        //$tecnico = $_POST['funcionario']; // ID usuario vinda da modal
        $produto = $_POST['txtproduto'];  //ID servicos vindo da modal
        $for_pag = $_POST['txtfor_pag'];  // ID Forma de pagamento selecionada na modal
        $serie = $_POST['txtserie'];   // quantidade em unidades
        $defeito = $_POST['txtdefeito']; //status 
        $obs = $_POST['txtobs'];

        $modificado = date('d-m-Y H:i:s');
        $desconto = $_POST['desconto'];
        
        /*BUSCA DE DADOS APARTIR DOS IDs. Para os calculos*/

        if(true){ /*Busca o nome do cliente*/
          
          $query = "SELECT nome FROM clientes where id = $salva_id_cliente ORDER BY nome asc";
          $result = mysqli_query($conexao, $query);

          if(mysqli_num_rows($result)){
            while($res_1 = mysqli_fetch_array($result)){
                                                      
             $salva_id_cliente1 = $res_1['nome'];  //Salva o nome atraves do ID         
           }
         }
        }

        if(true){ /*Busca o valor unitario do produto*/
          
          $query2 = "SELECT * FROM servicos where id = $produto";
          $result2 = mysqli_query($conexao, $query2);

          if(mysqli_num_rows($result2)){
            while($res_1 = mysqli_fetch_array($result2)){
                                                      
             $salva_valor_produto = $res_1['valor_venda'];  //Salva o valor unitario atualizado 
             $salva_nome_produto = $res_1['nome'];  /*Salva nome atual do produto no periodo da venda*/     
           }
         }
        }

        if(true){ /*Busca nome do tipo de pagamento*/
          
          $query3 = "SELECT * FROM forma_pgtos where id = $for_pag";
          $result3 = mysqli_query($conexao, $query3);

          if(mysqli_num_rows($result3)){
            while($res_1 = mysqli_fetch_array($result3)){
                                                      
             $salva_nome_pagamento = $res_1['nome'];  /*Salva nome atual pgmt no periodo da venda*/     
           }
         }
        }



        if(true){ /*Busca o nome do cliente pelo ID*/

          $query = "SELECT nome FROM clientes where id = $salva_id_cliente ORDER BY nome asc";
          $result = mysqli_query($conexao, $query);

          if(mysqli_num_rows($result)){
            while($res_1 = mysqli_fetch_array($result)){
                                                      
             $salva_id_cliente1 = $res_1['nome']; /*nome completo do cliente*/
                 
           }
         }

        }


        /* CALCULOS */

      /*qt * preço*/
      $tot_valor_uni_mult_quantidade = $salva_valor_produto * $serie;
      
      /*quanto se deu de desconto*/
      $tot_de_desconto = $tot_valor_uni_mult_quantidade * ($desconto / 100);

      /*Total Final já com os descontos*/
      $tot_depois_do_desconto = $tot_valor_uni_mult_quantidade - $tot_de_desconto;





          $query_editar = "UPDATE orcamentos set 
          pgto = '$salva_nome_pagamento', 
          item = '$salva_nome_produto', 
          valor_servico_sem_desconto = '$tot_valor_uni_mult_quantidade', 
          valor_em_desconto = '$tot_de_desconto', 
          valor_pecas = '$salva_valor_produto', 
          desconto = '$desconto', 
          cliente = '$salva_id_cliente',   
          cliente_nome = '$salva_id_cliente1',  
          produto = '$produto', 
          id_form_pag = '$for_pag', 
          qtd = '$serie', 
          status = '$defeito', 
          obs = '$obs', 
          valor_total = '$tot_depois_do_desconto', 
          modificado = '$modificado' where id = '$id' ";

          $result_editar = mysqli_query($conexao, $query_editar);

          if($result_editar == ''){
            echo "<script language='javascript'> window.alert('Ocorreu um erro ao Editar!'); </script>";
          }else{
            echo "<script language='javascript'> window.alert('Editado com Sucesso!'); </script>";
            echo "<script language='javascript'> window.location='abrir_orcamentos.php'; </script>";
          }
        }
    ?>
<?php } }  ?>

    <!-- FIM EDITAR -->
    <!-- FIM EDITAR -->
    <!-- FIM EDITAR -->


<!--MASCARAS -->

<script type="text/javascript">
  $(document).ready(function(){
    $('#txttelefone').mask('(00) 00000-0000');

    $('.select2').select2({
     dropdownParent: $('#modalExemplo')
   });
    $('#select2cli').select2({
     dropdownParent: $('#modalExemplo')
   });

    $('.select2edit').select2({
     dropdownParent: $('#modalEditar')
   });
    $('#select2cli2').select2({
     dropdownParent: $('#modalEditar')
   });

  });
</script>






<style type="text/css">
  .select2-selection__rendered {
    line-height: 40px !important;
  }

  .select2-selection {
    height: 40px !important;
  }
</style>  