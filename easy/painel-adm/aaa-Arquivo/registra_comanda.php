 <!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comandas Por Cliente</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <meta name="viewport" content="width=device-width, initial-scale=1">

  </head>
  <body>
      
    <div class="col-md-12">       
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="btn btn-primary btn-sm" href="abrir_orcamentos.php">Voltar</a>
        </nav>
    </div> </br>
  
<div class="container">
<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">

    <?php 
    require_once('../conexao/conexao.php');
    require_once('../conexao.php');

    

    @$id = $_POST['txt_cli']; // Recebe o id da classe Abrir_orcamentos

    //echo $id;

    /*busca os dados do cliente*/
    $query = "SELECT * FROM orcamentos where cliente = $id ORDER BY cliente_nome asc";
              $result = mysqli_query($conexao, $query);

              if(mysqli_num_rows($result)){
                while($res_1 = mysqli_fetch_array($result)){                 
               
                 //$dt_abertura = $res_1['dt_abertura'];
                 $nome = $res_1['cliente_nome'];
                 $tecnico = $res_1['tecnico'];
                 $qtd = $res_1['qtd'];
                 $produto = $res_1['produto'];
                 $valor = $res_1['valor_total']; 
                 $status = $res_1['status']; 
                 $id_form_pag = $res_1['id_form_pag'];
               
               }
             }

   /*busca total vendido paga ou não*/
    $query = "SELECT sum(qtd * valor_total) as tot_vend FROM orcamentos where cliente = $id";
              $result = mysqli_query($conexao, $query);

              if(mysqli_num_rows($result)){
                while($res_1 = mysqli_fetch_array($result)){                 
               
                 $tot_comp1 = $res_1['tot_vend'];
                 $tot_comp = number_format($tot_comp1, 2, ',', '.');
                 
               
               }
             }

    /*busca total pago*/
    $query = "SELECT sum(qtd * valor_total) as tot_pag FROM orcamentos where cliente = $id and status = 'Pago' ";
              $result = mysqli_query($conexao, $query);

              if(mysqli_num_rows($result)){
                while($res_1 = mysqli_fetch_array($result)){                 
               
                 $tot_pago1 = $res_1['tot_pag'];
                 $tot_pago = number_format($tot_pago1, 2, ',', '.');
                 
               
               }
             }

    /*busca total pendente*/
    $query = "
    SELECT sum(qtd * valor_total) as tot_pen FROM orcamentos where cliente = $id and status = 'Aberto' or cliente = $id and status = 'Pendente' or cliente = $id and status = 'Aguardando' or cliente = $id and status = 'Aprovado'
    ";
              $result = mysqli_query($conexao, $query);

              if(mysqli_num_rows($result)){
                while($res_1 = mysqli_fetch_array($result)){                 
               
                 $tot_pend1 = $res_1['tot_pen'];
                 $tot_pend = number_format($tot_pend1, 2, ',', '.');
                 
               
               }
             }

              /*busca total cancelado*/
    $query = "SELECT sum(qtd * valor_total) as tot_canc FROM orcamentos where cliente = $id and status = 'Cancelado' ";
              $result = mysqli_query($conexao, $query);

              if(mysqli_num_rows($result)){
                while($res_1 = mysqli_fetch_array($result)){                 
               
                 $tot_canc1 = $res_1['tot_canc'];
                 $tot_canc = number_format($tot_canc1, 2, ',', '.');
                 
               
               }
             }

            
?>


    <!--<label for=""><h2>Informações da Compra</h2></label>
    <?php echo 'qtd: ', $qtd; ?></br>
    <?php echo 'produto: ', $produto; ?></br>
    <?php echo 'valor: R$', $valor; ?></br>
    <?php echo 'status: ', $status; ?></br>
    <?php echo 'id form pag: ', $id_form_pag; ?></br>-->


    <!--- Revisar daqui pra cima. nao tem utilidade resto de codigo!!!!!!!! -->



<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title"> Comandas de <?php echo $nome; ?></h4>
          <h5 class="card-title"> ID: <?php echo $id; ?></h5>

        </div>
        <div class="card-body">
          <div class="table-responsive">

            <!--LISTAR TODOS OS ORÇAMENTOS -->

            <?php


            if(isset($_POST['txt_cli']) ){
              $statusOrc = $_POST['txt_cli'];

              $query = "select o.id, o.cliente, o.tecnico, o.produto, o.valor_total as valor_total, o.qtd, o.obs, o.status, fp.nome as for_pag, s.valor_venda as ser_valor, s.nome as ser_nome, o.data_abertura as dt_abertura, c.nome as cli_nome, c.id as cli_id, f.nome as func_nome from orcamentos as o 
             INNER JOIN clientes as c on o.cliente = c.id 
             INNER JOIN usuarios as f on o.tecnico = f.id
             INNER JOIN servicos as s on o.produto = s.id
             INNER JOIN forma_pgtos as fp on o.id_form_pag = fp.id 
             where c.id = '$statusOrc' order by id asc";


            }else if(isset($_POST['txt_cli'])){
             $statusOrc = $_POST['txt_cli'];

             $query = "select o.id, o.cliente, o.tecnico, o.produto, o.valor_total as valor_total, o.qtd, o.obs, o.status, fp.nome as for_pag, s.valor_venda as ser_valor, s.nome as ser_nome, o.data_abertura as dt_abertura, c.nome as cli_nome, c.id as cli_id, f.nome as func_nome from orcamentos as o 
             INNER JOIN clientes as c on o.cliente = c.id 
             INNER JOIN usuarios as f on o.tecnico = f.id
             INNER JOIN servicos as s on o.produto = s.id
             INNER JOIN forma_pgtos as fp on o.id_form_pag = fp.id 
             where status = '$statusOrc' order by id asc"; 

           }else{
             $query = "select o.id, o.cliente, o.tecnico, o.produto, o.valor_total as valor_total, o.qtd, o.obs, o.status, fp.nome as for_pag, s.valor_venda as ser_valor, s.nome as ser_nome, o.data_abertura as dt_abertura, c.nome as cli_nome, c.id as cli_id, f.nome as func_nome from orcamentos as o 
             INNER JOIN clientes as c on o.cliente = c.id 
             INNER JOIN usuarios as f on o.tecnico = f.id
             INNER JOIN servicos as s on o.produto = s.id
             INNER JOIN forma_pgtos as fp on o.id_form_pag = fp.id  
             where data_abertura = curDate()  order by id asc"; 
           }

           $result = mysqli_query($conexao, $query);
                        //$dado = mysqli_fetch_array($result);
           $row = mysqli_num_rows($result);

            if($row == '' or $row == 0){

            echo "<h5> Não existem dados cadastrados
            </h5>";

          }else{

           ?>

           <table class="table">
            <thead class=" text-primary">

              <th>
                Data
              </th>
              <th>
                Profissional
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
                Total 
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
            </th>

          </th>

        </thead>
        <tbody>

         <?php 

         while($res_1 = mysqli_fetch_array($result)){

          $dt_abertura = $res_1["dt_abertura"];
          $cliente = $res_1["cli_id"];
          $clienteid = $res_1["cli_nome"];
          $tecnico = $res_1["func_nome"];
          $produto = $res_1["ser_nome"]; 

          $ser_valor1 = $res_1["valor_total"];
          $qtd = $res_1["qtd"];

          $for_pag = $res_1["for_pag"];
          $status = $res_1["status"];
 
          $obs = $res_1["obs"];

          $id = $res_1["id"];
          $ser_valor = number_format($ser_valor1, 2, ',', '.');

          // multiplica o valor pela quantidade
          // multiplica o valor pela quantidade
          $total_1 = doubleval($ser_valor1 * $qtd);
          $total_TT = number_format($total_1, 2, ',', '.'); 

          ?>

          <tr>
           <td> <!-- muda a data para a forma brasil  -->
            <?php echo implode('/', array_reverse(explode('-', $dt_abertura))); ?>           
           </td>

           <td><?php echo $tecnico; ?></td> 
           <td><?php echo $produto; ?></td>
           <td>R$ <?php echo $ser_valor; ?></td>
           <td><?php echo $qtd; ?></td>
           <td>R$ <?php echo $total_TT; ?></td>
           <td><?php echo $for_pag; ?></td>
           <td><?php echo $status; ?></td>          
           <td><?php echo $obs; ?></td>

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
</br>
   <h5 class="card-title">
    
    Total Pago:      R$ <?php echo  @$tot_pago; ?> &nbsp;&nbsp; |  &nbsp;&nbsp;
    Total Pendente:  R$ <?php echo  @$tot_pend; ?> &nbsp;&nbsp; |  &nbsp;&nbsp;
    <a class="bi bi-alarm text-danger mx-1">
    Total Cancelado: R$ <?php echo  @$tot_canc; ?> &nbsp;&nbsp; 
    </a>

   </h5>

    <h4 class="card-title"> Total Comandas: R$ <?php echo  @$tot_comp; ?></h4>

  </div>
  </div>
  </div>
  </div>
  </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
  </body>
</html>


