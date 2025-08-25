<?php 
$pag = 'agendar_conectado';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php')
?>

<link rel="stylesheet" type="text/css" href="../vendor/login/css/util.css">
<link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css">

<?php  gerarMenu($pag, $grupos); ?>



	<div class="col-md-7"> <!--  Botão -->
		<div class="col-md-3">
			</br>
				<a style="font-size: 12px;  " href="index.php?pagina=<?php echo $pag ?>&funcao=novo"
				 type="button" class="login100-form-btn">Novo Agendamento</a> 
			</br>

		</div>
	</div>


	<?php 
    /*salva a data do dia*/
	$data_vigente = date('Y-m-d');

	/*$txtdata = $data_vigente; echo $data_vigente;*/
	?>

	<td><?php //echo 'Data: '. implode('/', array_reverse(explode('-', $data_vigente))); ?></td>

      <form class="form-inline my-2 my-lg-0" method="POST" action="#"> <!--envia a data oa o isset() -->
	       <input name="txtdata" class="form-control mr-sm-2" type="date" placeholder="Pesquisar" aria-label="Pesquisar">
	       <button name="button1" class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fa fa-search"></i></button>
     </form>


     <?php if(isset($_POST['button1'])){ //rece a data do botao acima

     		$data_vigente = $_POST['txtdata']; /*data escolhida que ira para os a querys*/
     } ?>

      <td><?php // echo 'Data vinda do botao: '. $data_vigente; ?></td>
      <td><?php echo 'Data: '. implode('/', array_reverse(explode('-', $data_vigente))); ?></td>


<!--    Inicio Bloco de apresentação 7:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 700 AND fim <= 800 AND data = '$data_vigente' order by inicio desc");

	

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 

	?>

		
			<hr />
			<!-- <p>07:00</p> -->
			<table id="0700" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;
												
			
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];


						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];

					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 07:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>
			<hr />	
	<?php }else{
		echo '<p>7:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 7:00         -->





<!--    Inicio Bloco de apresentação 8:00         -->
<div class="mt-4" style="margin-right:25px">

	<?php 
    
		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 800 AND fim <= 900 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 

	?>
	
			<!-- <p>08:00</p> -->
			<table  id="0800" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;
												
			
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];


						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];

					?>

				<div align="left">
					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 08:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
				</div>
					<?php } ?>							   
			</table>
	<?php }else{
		echo '<p>8:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 8:00         -->




<!--    Inicio Bloco de apresentação 9:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 900 AND fim <= 1000 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

	<hr />
			<!-- <p>09:00</p> -->
			<table id="0900" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 09:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>
	<?php }else{
		echo '<p>9:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 9:00         -->



<!--    Inicio Bloco de apresentação 10:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1000 AND fim <= 1100 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<!-- <p>10:00</p> -->
			<hr />
			<table id="1000" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 10:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>
	<?php }else{
		echo '<p>10:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 10:00         -->




<!--    Inicio Bloco de apresentação 11:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1100 AND fim <= 1200 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

	<hr />
			<!-- <p>10:00</p> -->
			<table id="1100" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 11:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>11:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 12:00         -->




<!--    Inicio Bloco de apresentação 12:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1200 AND fim <= 1300 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

	<hr />
			<!-- <p>12:00</p> -->
			<table id="1200" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 12:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>
			<hr />	
	<?php }else{
		echo '<p>12:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 12:00         -->




<!--    Inicio Bloco de apresentação 13:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1300 AND fim <= 1400 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<!-- <p>13:00</p> -->
			<table id="1300" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 13:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>13:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 13:00         -->





<!--    Inicio Bloco de apresentação 14:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1400 AND fim <= 1500 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<!-- <p>14:00</p> -->
			<hr />
			<table id="1400" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 14:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>14:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 14:00         -->



<!--    Inicio Bloco de apresentação 15:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1500 AND fim <= 1600 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<hr />
			<table id="1500" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 15:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>15:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 15:00         -->




<!--    Inicio Bloco de apresentação 16:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1600 AND fim <= 1700 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<hr />
			<table id="1600" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 16:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>16:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 16:00         -->






<!--    Inicio Bloco de apresentação 17:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1700 AND fim <= 1800 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<hr />
			<table id="1700" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 17:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>17:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 17:00         -->



<!--    Inicio Bloco de apresentação 18:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1800 AND fim <= 1900 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<hr />
			<table id="1800" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 18:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>18:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 19:00         -->



<!--    Inicio Bloco de apresentação 19:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 1900 AND fim <= 2000 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<hr />
			<table id="1900" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 19:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>19:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 19:00         -->




<!--    Inicio Bloco de apresentação 20:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 2000 AND fim <= 2100 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<hr />
			<table id="2000" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 20:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>20:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 20:00         -->



<!--    Inicio Bloco de apresentação 21:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 2100 AND fim <= 2200 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<hr />
			<table id="2100" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 21:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>21:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 21:00         -->




<!--    Inicio Bloco de apresentação 22:00         -->
<div class="mt-4" style="margin-right:25px">
   
	<?php

		/*busca banco o intervalo de horario*/
	$query7 = $pdo->query("SELECT * from agendar_conectado where inicio >= 2200 AND fim <= 2300 AND data = '$data_vigente' order by inicio desc");

	$res7 = $query7->fetchAll(PDO::FETCH_ASSOC);
	$total_reg7 = @count($res7);
	if($total_reg7 < 100){ 
	?>

			<hr />
			<table id="2200" class="" style="width:100%">
				 					
					<?php 
					for($i=0; $i < $total_reg7; $i++){
						foreach ($res7[$i] as $key => $value){	}

					    /*Formata a data inicial com o separador : */
					    $data_parte1 = substr($res7[$i]['inicio'], 0, 2);
    					$data_parte2 = substr($res7[$i]['inicio'], 2, 4);
    					$data_inicio_formatada =  $data_parte1 .':'. $data_parte2;

    					/*Formata a data final com o separador : */
					    $data_parte3 = substr($res7[$i]['fim'], 0, 2);
    					$data_parte4 = substr($res7[$i]['fim'], 2, 4);
    					$data_final_formatada =  $data_parte3 .':'. $data_parte4;															
						$id_sala = $res7[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];

						$id_proc = $res7[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc'");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];

						$id_profissional = $res7[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional'");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						$id_celular = $res7[$i]['cliente'];
						$query_celular = $pdo->query("SELECT * from clientes where nome = '$id_celular' limit 1");
						$res_celular = $query_celular->fetchAll(PDO::FETCH_ASSOC);
						@$nome_celular = $res_celular[0]['celular'];
					?>

					<td> <p style="font-size: 16px; color: darkgray; ">Periodo 22:00</p>

						 <!--<img src="../img/<?php echo $pag ?>/<?php echo $res7[$i]['id'] ?>" width="40">-->
						<h6 style="font-size: 16px;  "> <?php echo $res7[$i]['cliente'] ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Tel.: '. @$nome_celular ?> </h6> 

						 <!--<h6 style="font-size: 12px;  "> <?php echo $res7[$i]['data'] ?> </h6>-->		
						 <h6 style="font-size: 14px;  "><?php echo 'Profissional: '. @$nome_profissional ?> </h6>
						  <h6 style="font-size: 14px;  "><?php echo 'Protocolo: '. @$nome_procedimento ?> </h6>
						<h6 style="font-size: 14px;  "><?php echo 'Sala: '. @$nome_sala ?> </h6> 

						<h6 style="font-size: 16px;  "> <?php echo 'Inicio: '. $data_inicio_formatada ?> </h6>
						<h6 style="font-size: 16px;  "> <?php echo 'Termino: '. $data_final_formatada ?> </h6>
						<h6 style="font-size: 14px;  "> <?php echo 'Situacao: '. $res7[$i]['situacao'] ?> </h6>
						<h5 style="font-size: 16px;  "> <?php echo 'Avaliação: '. $res7[$i]['eh_avaliacao'] ?> </h5>
						
						 <!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res7[$i]['id'] ?>" title="Editar Registro">
								<i class="bi bi-pencil-square text-primary"></i>
							</a>

							<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res7[$i]['id'] ?>" title="Excluir Registro">
								<i class="bi bi-archive text-danger mx-1"></i>
							</a> -->
					</td>
					<?php } ?>							   
			</table>	
	<?php }else{
		echo '<p>22:00';
	} ?>
</div>
<!--    Fim Bloco de apresentação 22:00         -->







<?php
	//header("Refresh:30"); // para manter a apagina atualizada a cada 30 segundos
?>

<hr />

<!--    -----------------------------------------------------------------------------------------------        -->

<?php 
if(@$_GET['funcao'] == "editar"){
	$titulo_modal = 'Editar Registro';
	$query = $pdo->query("SELECT * from usuarios where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		$nome = $res[0]['nome'];
		$email = $res[0]['email'];
		$cpf = $res[0]['cpf'];
		$senha = $res[0]['senha'];
		$nivel = $res[0]['nivel'];
		$foto = $res[0]['foto'];

	}
}else{
	$titulo_modal = 'Inserir Registro';
}
?>


<div class="modal fade" tabindex="-1" id="modalCadastrar" data-bs-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php echo $titulo_modal ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form method="POST" id="form">
				<div class="modal-body">

					<div class="row">

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Nome</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required="" value="<?php echo @$nome ?>">
							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">CPF</label>
								<input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF" required="" value="<?php echo @$cpf ?>">
							</div>  
						</div>
					</div>



					

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Email</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="Email" required="" value="<?php echo @$email ?>">
					</div>  

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Senha</label>
						<input type="text" class="form-control" id="senha" name="senha" placeholder="Senha" required="" value="<?php echo @$senha ?>">
					</div>  

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Nível</label>
						<select class="form-select mt-1" aria-label="Default select example" name="nivel">
							
							<option <?php if(@$nivel == 'Operador'){ ?> selected <?php } ?>  value="Operador">Operador</option>

							<option <?php if(@$nivel == 'Administrador'){ ?> selected <?php } ?>  value="Administrador">Administrador</option>
							
							<option <?php if(@$nivel == 'Financeiro'){ ?> selected <?php } ?>  value="Financeiro">Financeiro</option>

							<option <?php if(@$nivel == 'Vendedor'){ ?> selected <?php } ?>  value="Vendedor">Vendedor</option>

							
						</select>
					</div> 

					<small><div align="center" class="mt-1" id="mensagem">
						
					</div> </small>


					<div class="col-md-4">
							<div class="form-group">
								<label >Foto</label>
								<input type="file" value="<?php echo @$foto ?>"  class="form-control-file" id="imagem" name="imagem" onChange="carregarImg();">
							</div>							
						</div>

						<div class="col-md-4">
							<div id="divImgConta" class="mt-4">
								<?php if(@$foto != ""){ ?>
									<img src="../img/<?php echo $pag ?>/<?php echo $foto ?>"  width="150px" id="target">
								<?php  }else{ ?>
									<img src="../img/<?php echo $pag ?>/sem-foto.jpg" width="150px" id="target">
								<?php } ?>
							</div>
						</div>

				</div>


				<small><div align="center" class="mt-1" id="mensagem">

					</div> </small>
				


				<div class="modal-footer">
					<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>

					<input name="id" type="hidden" value="<?php echo @$_GET['id'] ?>">

					<input name="antigo" type="hidden" value="<?php echo @$cpf ?>">
					<input name="antigo2" type="hidden" value="<?php echo @$email ?>">

				</div>
			</form>
		</div>
	</div>
</div>






<div class="modal fade" tabindex="-1" id="modalDeletar" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Excluir Registro</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form method="POST" id="form-excluir">
				<div class="modal-body">

					<p>Deseja Realmente Excluir o Registro?</p>

					<small><div align="center" class="mt-1" id="mensagem-excluir">
						
					</div> </small>

				</div>
				<div class="modal-footer">
					<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-excluir" id="btn-excluir" type="submit" class="btn btn-danger">Excluir</button>

					<input name="id" type="hidden" value="<?php echo @$_GET['id'] ?>">

				</div>
			</form>
		</div>
	</div>
</div>



<!--  mostrar a modal com a descrição -->

<div class="modal fade" tabindex="-1" id="modalDados" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">

				<h5 class="modal-title"><span id="nome-registro"></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body mb-4">

				<b>Codigo: </b>
				<span id="codigo_"></span>
				<hr>

					<span class="mr-4">
						<b>Telefone: </b>
						<span id="tel-forn-registro"></span>
					</span>
					<hr>
				</div>


				
				<b>Descrição: </b>
				<span id="descricao-registro"></span>
				<hr>
				<img id="imagem-registro" src="" class="mt-4" width="200">

				

			</div> 

		</div>
	</div>
</div>




<?php 
if(@$_GET['funcao'] == "novo"){ ?>
	<script type="text/javascript">
		var myModal = new bootstrap.Modal(document.getElementById('modalCadastrar'), {
			backdrop: 'static'
		})

		myModal.show();
	</script>
<?php } ?>



<?php 
if(@$_GET['funcao'] == "editar"){ ?>
	<script type="text/javascript">
		var myModal = new bootstrap.Modal(document.getElementById('modalCadastrar'), {
			backdrop: 'static'
		})

		myModal.show();
	</script>
<?php } ?>



<?php 
if(@$_GET['funcao'] == "deletar"){ ?>
	<script type="text/javascript">
		var myModal = new bootstrap.Modal(document.getElementById('modalDeletar'), {
			
		})

		myModal.show();
	</script>
<?php } ?>




<!--AJAX PARA INSERÇÃO E EDIÇÃO DOS DADOS COM IMAGEM -->
<script type="text/javascript">
	$("#form").submit(function () {
		var pag = "<?=$pag?>";
		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: pag + "/inserir.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {

				$('#mensagem').removeClass()

				if (mensagem.trim() == "Salvo com Sucesso!") {

                    //$('#nome').val('');
                    //$('#cpf').val('');
                    $('#btn-fechar').click();
                    window.location = "index.php?pagina="+pag;

                } else {

                	$('#mensagem').addClass('text-danger')
                }

                $('#mensagem').text(mensagem)

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




<!--AJAX PARA EXCLUIR DADOS -->
<script type="text/javascript">
	$("#form-excluir").submit(function () {
		var pag = "<?=$pag?>";
		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: pag + "/excluir.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {

				$('#mensagem').removeClass()

				if (mensagem.trim() == "Excluído com Sucesso!") {

					$('#mensagem-excluir').addClass('text-success')

                    $('#btn-fechar').click();
                    window.location = "index.php?pagina="+pag;

                } else {

                	$('#mensagem-excluir').addClass('text-danger')
                }

                $('#mensagem-excluir').text(mensagem)

            },

            cache: false,
            contentType: false,
            processData: false,
            
        });
	});
</script>



<script type="text/javascript">
	$(document).ready(function() {
		$('#example').DataTable({
			"ordering": false
		});
	} );
</script>




<!--SCRIPT PARA CARREGAR IMAGEM -->
<script type="text/javascript">

	function carregarImg() {

		var target = document.getElementById('target');
		var file = document.querySelector("input[type=file]").files[0];
		var reader = new FileReader();

		reader.onloadend = function () {
			target.src = reader.result;
		};

		if (file) {
			reader.readAsDataURL(file);


		} else {
			target.src = "";
		}
	}

</script>

<script type="text/javascript">
	function mostrarDados(nome, foto){
		event.preventDefault();

		if(nome_forn.trim() === ""){
			document.getElementById("div-forn").style.display = 'none';
		}else{
			document.getElementById("div-forn").style.display = 'block';
		}

		$('#nome-registro').text(nome);
		
						
		$('#imagem-registro').attr('src', '../img/usuarios/' + foto);


		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {
			
		})

		myModal.show();
	}
</script>
