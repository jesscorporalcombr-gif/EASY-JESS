<?php 
$pag = 'agendar_conectado';
@session_start();

require_once('../conexao.php');
require_once('../conexao/conexao.php');
//include('../consultas/conexao_busca_nome.php');
require_once('verificar-permissao.php');

?>



<link rel="stylesheet" type="text/css" href="../vendor/login/css/util.css">
<link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css">

<nav class="navbar navbar-expand-lg navbar-light bg-light">

	 <h4 style="font-size: 14px;  font-style:normal;" class="text-uppercase">Agendamento</h4> &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 12px;  " href="index.php?pagina=agenda_grupo_por_profissional"
	 type="button" class="">Ver Agenda em Quadro</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	  <a style="font-size: 12px;  " href="index.php?pagina=cores_agendamento"
	 type="button" class="">Cores Agendamento</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	  <a style="font-size: 12px;  " href="index.php?pagina=bloquear_horario" type="button" class="">Bloquear Horário</a> &nbsp;&nbsp;&nbsp;&nbsp;


	 <!--- /////////////// inicio busca por cliente /////////////// -->   
	  <div  id="conteudoNavbarSuportado">
	      <ul class="navbar-nav mr-auto"></ul>
	      
		      <!--- /////////////// busca por cliente/////////////// -->
		      <form class="btn-group" method="POST" action="index.php?pagina=ver_agendar_conectado"> <!--Classe p/ a busca-->    
			        <select style=" width: 170px; height: 25px; font-size: 12px; " class="mt-2" data-width="100%"  id="selec_cli" name="txt_cli"> <!--- Variavel p/ a busca -->         
			            <?php
			              $query = "SELECT DISTINCT cliente FROM agendar_conectado ORDER BY cliente asc";
			              $result = mysqli_query($conexao, $query);

			              if(mysqli_num_rows($result)){
			                while($res_1 = mysqli_fetch_array($result)){
			                 ?>                                             
			                 <option value="<?php echo $res_1['cliente']; ?>"> <!-- valor da variavel txt_cli -->

			                  <?php echo $res_1['cliente']; ?> </option>  <!-- valor mostrado --> <?php      
				               }
				             }
			             ?>
			          </select> </br>

		          <button style=" width: 90px; height: 25px; font-size: 11px; " name="buttonPesquisar" class="mt-2 btn btn-outline-success" type="submit">
		          	Pesquisar
		          </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 

		     </form>
	   </div>
	  <!--- /////////////// Fim busca por cliente/////////////// -->   


</nav>


<div class="col-md-7"> <!--  Botão -->
	<div class="col-md-3">
		</br>
		 <a style="font-size: 12px;  " href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-outline-primary">Novo Agendamento</a>
	</div>
</div>

<!--<a href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-secondary mt-2">Nova documento</a> -->

<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from agendar_conectado order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>
					<tr>					
						<th>Data Atendimento</th>
						<th>Inicio</th>
						<th>Fim</th>												
						<!-- <th>Agendado Por</th> -->
						<th>Cliente</th>
						<th>Tel. Cli.</th>
						<th>Profissional</th>
						<th>Protocolo</th>
						<th>Equipamento</th>
						<!--<th>Produtos</th> -->
						<th>Sala.</th>
						<th>Status</th>
						<th>Situação</th>
						<!--<th>Obs.</th>-->												
						<th>Arquivo</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}


						$id_tel = $res[$i]['cliente'];
						$query_tel = $pdo->query("SELECT celular from clientes where nome = '$id_tel' limit 1");
						$res_tel = $query_tel->fetchAll(PDO::FETCH_ASSOC);
						@$nome_tel = $res_tel[0]['celular'];


						$id_usu = $res[$i]['usuario'];
						$query_p = $pdo->query("SELECT * from usuarios where id = '$id_usu' limit 1");
						$res_p = $query_p->fetchAll(PDO::FETCH_ASSOC);
						@$nome_usu = $res_p[0]['nome'];


						$id_proc = $res[$i]['procedimento'];
						$query_proc = $pdo->query("SELECT * from servicos where id = '$id_proc' limit 1");
						$res_proc = $query_proc->fetchAll(PDO::FETCH_ASSOC);
						@$nome_procedimento = $res_proc[0]['nome'];



						$id_produtos = $res[$i]['produtos'];
						$query_produtos = $pdo->query("SELECT * from produtos where id = '$id_produtos' limit 1");
						$res_produtos = $query_produtos->fetchAll(PDO::FETCH_ASSOC);
						@$nome_produtos = $res_produtos[0]['nome'];


						$id_equipamento = $res[$i]['equipamento'];
						$query_equipamento = $pdo->query("SELECT * from equipamento where id = '$id_equipamento' limit 1");
						$res_equipamento = $query_equipamento->fetchAll(PDO::FETCH_ASSOC);
						@$nome_equipamento = $res_equipamento[0]['nome'];

						$id_sala = $res[$i]['sala'];
						$query_sala = $pdo->query("SELECT * from salas where id = '$id_sala' limit 1");
						$res_sala = $query_sala->fetchAll(PDO::FETCH_ASSOC);
						@$nome_sala = $res_sala[0]['nome'];


						$id_profissional = $res[$i]['profissional'];
						$query_profissional = $pdo->query("SELECT * from usuarios where id = '$id_profissional' limit 1");
						$res_profissional = $query_profissional->fetchAll(PDO::FETCH_ASSOC);
						@$nome_profissional = $res_profissional[0]['nome'];

						

						$extensao = strchr($res[$i]['arquivo'], '.'); //para mostrar os icones de extenção
						if($extensao == '.pdf'){
							$arquivo_pasta = 'pdf.png';

						}elseif ($extensao == '.PDF') {
							$arquivo_pasta = 'pdf.png';

						}elseif ($extensao == '.docx') {
							$arquivo_pasta = 'doc.png';

						}
						elseif ($extensao == '.doc') {
							$arquivo_pasta = 'doc.png';

						}elseif ($extensao == '.DOCX') {
							$arquivo_pasta = 'doc.png';
							
						}elseif ($extensao == '.DOC') {
							$arquivo_pasta = 'doc.png';

						}elseif ($extensao == '.txt') {
							$arquivo_pasta = 'txt.png';

						}elseif ($extensao == '.TXT') {
							$arquivo_pasta = 'txt.png';

						}elseif ($extensao == '.xlsx') {
							$arquivo_pasta = 'xlsx.png';

						}elseif ($extensao == '.XLSX') {
							$arquivo_pasta = 'xlsx.png';

						}elseif ($extensao == '.pptx') {
							$arquivo_pasta = 'ppt.png';

						}elseif ($extensao == '.PPTX') {
							$arquivo_pasta = 'ppt.png';

						}

						else{
							$arquivo_pasta = $res[$i]['arquivo'];
						}
						
						

						?>

						<tr>

							
							<td><?php echo implode('/', array_reverse(explode('-', $res[$i]['data']))); ?></td>	

							<td><?php echo implode(':', str_split(sprintf('%04s', $res[$i]['inicio']), 2)); ?></td>
							<td><?php echo implode(':', str_split(sprintf('%04s', $res[$i]['fim']), 2)); ?></td>
							
							<!--<td><?php echo $nome_usu ?></td> -->

							<td><?php echo $res[$i]['cliente'] ?></td>
							<td><?php echo @$nome_tel ?></td>
							<td><?php echo $nome_profissional ?></td>
							<td><?php echo $nome_procedimento ?></td>
							<td><?php echo $nome_equipamento ?></td>
							<!--<td><?php echo $nome_produtos ?></td>-->		
							<td><?php echo $nome_sala ?></td>
							<td><?php echo $res[$i]['status'] ?></td>	
							<td><?php echo $res[$i]['situacao'] ?></td>	
							<!--<td><?php echo $res[$i]['descricao'] ?></td>-->						
							<td><a href="../img/<?php echo $pag ?>/<?php echo $res[$i]['arquivo'] ?>" title="Ver Arquivo" style="text-decoration: none" target="_blank">
								<img src="../img/<?php echo $pag ?>/<?php echo $arquivo_pasta ?>" width="40">
							</a>
						</td>
						<td>
							<?php if($res[$i]['data'] != 'Sim'){ ?>
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res[$i]['id'] ?>" title="Editar Registro" style="text-decoration: none">
									<i class="bi bi-pencil-square text-primary"></i>
								</a>

								<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res[$i]['id'] ?>" title="Excluir Registro" style="text-decoration: none">
									<i class="bi bi-archive text-danger mx-1"></i>
								</a>


								<!--
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=baixar&id=<?php echo $res[$i]['id'] ?>" title="Baixar Registro" style="text-decoration: none">
									<i class="bi bi-check-square-fill text-success mx-1"></i>

								</a> 
							-->

							<?php } ?>

						</td>
					</tr>

				<?php } ?>

			</tbody>

		</table>
	</small>
<?php }else{
	echo '<p>Não existem dados para serem exibidos!!';
} ?>
</div>


<?php 
if(@$_GET['funcao'] == "editar"){
	$titulo_modal = 'Editar Agendamento';
	$query = $pdo->query("SELECT * from agendar_conectado where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 

		@$valor = $res[0]['valor'];
		$descricao = $res[0]['descricao'];
		$arquivo = $res[0]['arquivo'];
		$data = $res[0]['data'];
		$status = $res[0]['status'];
		$inicio = $res[0]['inicio'];
		$fim = $res[0]['fim'];
		$sala = $res[0]['sala'];
		$equipamento = $res[0]['equipamento'];
		$equipamento_2 = $res[0]['equipamento_2'];
		$produtos = $res[0]['produtos'];
		$produtos_2 = $res[0]['produtos_2'];
		$cliente = $res[0]['cliente'];
		$procedimento = $res[0]['procedimento'];
		$procedimento_2 = $res[0]['procedimento_2'];
		$tel_cliente = $res[0]['tel_cliente'];
		$profissional = $res[0]['profissional'];
		$situacao = $res[0]['situacao'];
		$eh_avaliacao = $res[0]['eh_avaliacao'];



		$id_proc_modal = $procedimento;
		$query_proc_modal = $pdo->query("SELECT * from servicos where id = '$id_proc_modal' limit 1");
		$res_proc_modal = $query_proc_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_procedimento_modal = $res_proc_modal[0]['nome'];



		$id_produtos_modal = $produtos;
		$query_produtos_modal = $pdo->query("SELECT * from produtos where id = '$id_produtos_modal' limit 1");
		$res_produtos_modal = $query_produtos_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_produtos_modal = $res_produtos_modal[0]['nome'];


		$id_equipamento_modal = $equipamento;
		$query_equipamento_modal = $pdo->query("SELECT * from equipamento where id = '$id_equipamento_modal' limit 1");
		$res_equipamento_modal = $query_equipamento_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_equipamento_modal = $res_equipamento_modal[0]['nome'];

		$id_sala_modal = $sala;
		$query_sala_modal = $pdo->query("SELECT * from salas where id = '$id_sala_modal' limit 1");
		$res_sala_modal = $query_sala_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_sala_modal = $res_sala_modal[0]['nome'];

		$id_profissional_modal = $profissional;
		$query_profissional_modal = $pdo->query("SELECT * from usuarios where id = '$id_profissional_modal' limit 1");
		$res_profissional_modal = $query_profissional_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_profissional_modal = $res_profissional_modal[0]['nome'];




$extensao2 = strchr($arquivo, '.'); //busca a palavra apos o ponto

		if($extensao2 == '.pdf'){
			$arquivo_extensao2 = 'pdf.png';

		}elseif($extensao2 == '.PDF'){
			$arquivo_extensao2 = 'pdf.png';

		}elseif($extensao2 == '.docx'){
			$arquivo_extensao2 = 'doc.png';

		}elseif($extensao2 == '.doc'){
			$arquivo_extensao2 = 'doc.png';

		}elseif($extensao2 == '.DOCX'){
			$arquivo_extensao2 = 'doc.png';

		}elseif($extensao2 == '.DOC'){
			$arquivo_extensao2 = 'doc.png';

		}elseif($extensao2 == '.txt'){
			$arquivo_extensao2 = 'txt.png';

		}elseif($extensao2 == '.TXT'){
			$arquivo_extensao2 = 'txt.png';

		}elseif($extensao2 == '.xlsx'){
			$arquivo_extensao2 = 'xlsx.png';

		}elseif($extensao2 == '.XLSX'){
			$arquivo_extensao2 = 'xlsx.png';

		}elseif($extensao2 == '.pptx'){
			$arquivo_extensao2 = 'ppt.png';

		}elseif($extensao2 == '.PPTX'){
			$arquivo_extensao2 = 'ppt.png';

		}

		else{
			$arquivo_extensao2 = $arquivo;
		}

	}
}else{
	$titulo_modal = 'Inserir Agendamento';
}
?>


<div class="modal fade" tabindex="-1" id="modalCadastrar" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl">
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
											
						<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
			              <?php /*busca pacientes na tabela orcamentoe join agendados conectados*/
			              
			                 $sql_code_cliente = "SELECT * FROM `clientes`";
			                 $sql_query_cliente = $conexao->query($sql_code_cliente) or die($conexao->error);                  
			               ?>
			               <label for="exampleFormControlInput1" class="form-label">Paciente</label>
			                <select data-width="100%" class="form-control mr-1" id="cliente"
			                
			                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="cliente" >
			                    
			                    <option class="form-control" value="<?php echo @$cliente ?>" ><?php echo @$cliente ?></option>

			                    <?php while($nome_cli = $sql_query_cliente->fetch_assoc()) { ?> 

			                    <option 
								<?php if(isset($_GET['nome']) && $_GET['nome'] == $nome_cli['id']) echo "selected"; ?> value="<?php echo $nome_cli['nome']; ?>"><?php echo $nome_cli['nome'];?>		                      
			                    </option>
			                    <?php } ?>
			                </select>

			                 
			                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->
						</div>

					</div>

					<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Data Atendimento</label>
								<input type="date" class="form-control" id="data" name="data" value="<?php echo @$data ?>">
							</div> 

					</div>	

					


					<div class="col-md-3">
						 <div class="mb-3">											
							<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_states = "SELECT * FROM salas ORDER BY nome ASC";
				                 $sql_query_states = $conexao->query($sql_code_states) or die($conexao->error);                  
				               ?>
				               <label for="exampleFormControlInput1" class="form-label">Sala</label>
				                <select data-width="100%" class="form-control mr-1" id="sala"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="sala" >
				                    
				                    <option class="form-control" value="<?php echo @$sala ?>" ><?php echo @$nome_sala_modal ?></option>

				                    <?php while($nome = $sql_query_states->fetch_assoc()) { ?> 
				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->

						</div> 
				    </div>


				</div>


				<div class="row"> <!-- grupo -->
					<div class="col-md-3">
						 <div class="mb-3">
												
								<label for="exampleFormControlInput1" class="form-label">Horário Inicial</label>
								<select class="form-select mt-1" aria-label="Default select example" name="inicio" >
									
						<option <?php if(@$inicio == '0800'){ ?> selected <?php } ?>  value="0800" >08:00
						</option>
						<option <?php if(@$inicio == '0810'){ ?> selected <?php } ?>  value="0810">08:10</option>
						<option <?php if(@$inicio == '0820'){ ?> selected <?php } ?>  value="0820">08:20</option>
						<option <?php if(@$inicio == '0830'){ ?> selected <?php } ?>  value="0830">08:30</option>
						<option <?php if(@$inicio == '0840'){ ?> selected <?php } ?>  value="0840">08:40</option>
						<option <?php if(@$inicio == '0850'){ ?> selected <?php } ?>  value="0850">08:50</option>

						<option <?php if(@$inicio == '0900'){ ?> selected <?php } ?>  value="0900">09:00</option>
						<option <?php if(@$inicio == '0910'){ ?> selected <?php } ?>  value="0910">09:10</option>
						<option <?php if(@$inicio == '0920'){ ?> selected <?php } ?>  value="0920">09:20</option>
						<option <?php if(@$inicio == '0930'){ ?> selected <?php } ?>  value="0930">09:30</option>
						<option <?php if(@$inicio == '0940'){ ?> selected <?php } ?>  value="0940">09:40</option>
						<option <?php if(@$inicio == '0950'){ ?> selected <?php } ?>  value="0950">09:50</option>
						
						<option <?php if(@$inicio == '1000'){ ?> selected <?php } ?>  value="1000">10:00</option>
						<option <?php if(@$inicio == '1010'){ ?> selected <?php } ?>  value="1010">10:10</option>
						<option <?php if(@$inicio == '1020'){ ?> selected <?php } ?>  value="1020">10:20</option>
						<option <?php if(@$inicio == '1030'){ ?> selected <?php } ?>  value="1030">10:30</option>
						<option <?php if(@$inicio == '1040'){ ?> selected <?php } ?>  value="1040">10:40</option>
						<option <?php if(@$inicio == '1050'){ ?> selected <?php } ?>  value="1050">10:50</option>
						

						<option <?php if(@$inicio == '1100'){ ?> selected <?php } ?>  value="1100">11:00</option>
						<option <?php if(@$inicio == '1110'){ ?> selected <?php } ?>  value="1110">11:10</option>
						<option <?php if(@$inicio == '1120'){ ?> selected <?php } ?>  value="1120">11:20</option>
						<option <?php if(@$inicio == '1130'){ ?> selected <?php } ?>  value="1130">11:30</option>
						<option <?php if(@$inicio == '1140'){ ?> selected <?php } ?>  value="1140">11:40</option>
						<option <?php if(@$inicio == '1150'){ ?> selected <?php } ?>  value="1150">11:50</option>


						<option <?php if(@$inicio == '1200'){ ?> selected <?php } ?>  value="1200">12:00</option>
						<option <?php if(@$inicio == '1210'){ ?> selected <?php } ?>  value="1210">12:10</option>
						<option <?php if(@$inicio == '1220'){ ?> selected <?php } ?>  value="1220">12:20</option>
						<option <?php if(@$inicio == '1230'){ ?> selected <?php } ?>  value="1230">12:30</option>
						<option <?php if(@$inicio == '1240'){ ?> selected <?php } ?>  value="1240">12:40</option>
						<option <?php if(@$inicio == '1250'){ ?> selected <?php } ?>  value="1250">12:50</option>

						<option <?php if(@$inicio == '1300'){ ?> selected <?php } ?>  value="1300">13:00</option>
						<option <?php if(@$inicio == '1310'){ ?> selected <?php } ?>  value="1310">13:10</option>
						<option <?php if(@$inicio == '1320'){ ?> selected <?php } ?>  value="1320">13:20</option>
						<option <?php if(@$inicio == '1330'){ ?> selected <?php } ?>  value="1330">13:30</option>
						<option <?php if(@$inicio == '1340'){ ?> selected <?php } ?>  value="1340">13:40</option>
						<option <?php if(@$inicio == '1350'){ ?> selected <?php } ?>  value="1350">13:50</option>

						<option <?php if(@$inicio == '1400'){ ?> selected <?php } ?>  value="1400">14:00</option>
						<option <?php if(@$inicio == '1410'){ ?> selected <?php } ?>  value="1410">14:10</option>
						<option <?php if(@$inicio == '1420'){ ?> selected <?php } ?>  value="1420">14:20</option>
						<option <?php if(@$inicio == '1430'){ ?> selected <?php } ?>  value="1430">14:30</option>
						<option <?php if(@$inicio == '1440'){ ?> selected <?php } ?>  value="1440">14:40</option>
						<option <?php if(@$inicio == '1450'){ ?> selected <?php } ?>  value="1450">14:50</option>

						<option <?php if(@$inicio == '1500'){ ?> selected <?php } ?>  value="1500">15:00</option>
						<option <?php if(@$inicio == '1510'){ ?> selected <?php } ?>  value="1510">15:10</option>
						<option <?php if(@$inicio == '1520'){ ?> selected <?php } ?>  value="1520">15:20</option>
						<option <?php if(@$inicio == '1530'){ ?> selected <?php } ?>  value="1530">15:30</option>
						<option <?php if(@$inicio == '1540'){ ?> selected <?php } ?>  value="1540">15:40</option>
						<option <?php if(@$inicio == '1550'){ ?> selected <?php } ?>  value="1550">15:50</option>

						<option <?php if(@$inicio == '1600'){ ?> selected <?php } ?>  value="1600">16:00</option>
						<option <?php if(@$inicio == '1610'){ ?> selected <?php } ?>  value="1610">16:10</option>
						<option <?php if(@$inicio == '1620'){ ?> selected <?php } ?>  value="1620">16:20</option>
						<option <?php if(@$inicio == '1630'){ ?> selected <?php } ?>  value="1630">16:30</option>
						<option <?php if(@$inicio == '1640'){ ?> selected <?php } ?>  value="1640">16:40</option>
						<option <?php if(@$inicio == '1650'){ ?> selected <?php } ?>  value="1650">16:50</option>

						<option <?php if(@$inicio == '1700'){ ?> selected <?php } ?>  value="1700">17:00</option>
						<option <?php if(@$inicio == '1710'){ ?> selected <?php } ?>  value="1710">17:10</option>
						<option <?php if(@$inicio == '1720'){ ?> selected <?php } ?>  value="1720">17:20</option>
						<option <?php if(@$inicio == '1730'){ ?> selected <?php } ?>  value="1730">17:30</option>
						<option <?php if(@$inicio == '1740'){ ?> selected <?php } ?>  value="1740">17:40</option>
						<option <?php if(@$inicio == '1750'){ ?> selected <?php } ?>  value="1750">17:50</option>

						<option <?php if(@$inicio == '1800'){ ?> selected <?php } ?>  value="1800">18:00</option>
						<option <?php if(@$inicio == '1810'){ ?> selected <?php } ?>  value="1810">18:10</option>
						<option <?php if(@$inicio == '1820'){ ?> selected <?php } ?>  value="1820">18:20</option>
						<option <?php if(@$inicio == '1830'){ ?> selected <?php } ?>  value="1830">18:30</option>
						<option <?php if(@$inicio == '1840'){ ?> selected <?php } ?>  value="1840">18:40</option>
						<option <?php if(@$inicio == '1850'){ ?> selected <?php } ?>  value="1850">18:50</option>

						<option <?php if(@$inicio == '1900'){ ?> selected <?php } ?>  value="1900">19:00</option>
						<option <?php if(@$inicio == '1910'){ ?> selected <?php } ?>  value="1910">19:10</option>
						<option <?php if(@$inicio == '1920'){ ?> selected <?php } ?>  value="1920">19:20</option>
						<option <?php if(@$inicio == '1930'){ ?> selected <?php } ?>  value="1930">19:30</option>
						<option <?php if(@$inicio == '1940'){ ?> selected <?php } ?>  value="1940">19:40</option>
						<option <?php if(@$inicio == '1950'){ ?> selected <?php } ?>  value="1950">19:50</option>

						<option <?php if(@$inicio == '2000'){ ?> selected <?php } ?>  value="2000">20:00</option>
						<option <?php if(@$inicio == '2010'){ ?> selected <?php } ?>  value="2010">20:10</option>
						<option <?php if(@$inicio == '2020'){ ?> selected <?php } ?>  value="2020">20:20</option>
						<option <?php if(@$inicio == '2030'){ ?> selected <?php } ?>  value="2030">20:30</option>
						<option <?php if(@$inicio == '2040'){ ?> selected <?php } ?>  value="2040">20:40</option>
						<option <?php if(@$inicio == '2050'){ ?> selected <?php } ?>  value="2050">20:50</option>



						<option <?php if(@$inicio == '2100'){ ?> selected <?php } ?>  value="2100">21:00</option>
						<option <?php if(@$inicio == '2110'){ ?> selected <?php } ?>  value="2110">21:10</option>
						<option <?php if(@$inicio == '2120'){ ?> selected <?php } ?>  value="2120">21:20</option>
						<option <?php if(@$inicio == '2130'){ ?> selected <?php } ?>  value="2130">21:30</option>
						<option <?php if(@$inicio == '2140'){ ?> selected <?php } ?>  value="2140">21:40</option>
						<option <?php if(@$inicio == '2150'){ ?> selected <?php } ?>  value="2150">21:50</option>

						<option <?php if(@$inicio == '2200'){ ?> selected <?php } ?>  value="2200">22:00</option>
						<option <?php if(@$inicio == '2210'){ ?> selected <?php } ?>  value="2210">22:10</option>
						<option <?php if(@$inicio == '2220'){ ?> selected <?php } ?>  value="2220">22:20</option>
						<option <?php if(@$inicio == '2230'){ ?> selected <?php } ?>  value="2230">22:30</option>
						<option <?php if(@$inicio == '2240'){ ?> selected <?php } ?>  value="2240">22:40</option>
						<option <?php if(@$inicio == '2250'){ ?> selected <?php } ?>  value="2250">22:50</option>
																			
						</select>

						</div> 
					</div>
				
					<div class="col-md-3">
						 <div class="mb-3">				
							<label for="exampleFormControlInput1" class="form-label">Horário Final</label>

							<select class="form-select mt-1" aria-label="Default select example" name="fim">
									

						<option <?php if(@$fim == '0810'){ ?> selected <?php } ?>  value="0810">08:10</option>
						<option <?php if(@$fim == '0820'){ ?> selected <?php } ?>  value="0820">08:20</option>
						<option <?php if(@$fim == '0830'){ ?> selected <?php } ?>  value="0830">08:30</option>
						<option <?php if(@$fim == '0840'){ ?> selected <?php } ?>  value="0840">08:40</option>
						<option <?php if(@$fim == '0850'){ ?> selected <?php } ?>  value="0850">08:50</option>

						<option <?php if(@$fim == '0900'){ ?> selected <?php } ?>  value="0900">09:00</option>
						<option <?php if(@$fim == '0910'){ ?> selected <?php } ?>  value="0910">09:10</option>
						<option <?php if(@$fim == '0920'){ ?> selected <?php } ?>  value="0920">09:20</option>
						<option <?php if(@$fim == '0930'){ ?> selected <?php } ?>  value="0930">09:30</option>
						<option <?php if(@$fim == '0940'){ ?> selected <?php } ?>  value="0940">09:40</option>
						<option <?php if(@$fim == '0950'){ ?> selected <?php } ?>  value="0950">09:50</option>
						
						<option <?php if(@$fim == '1000'){ ?> selected <?php } ?>  value="1000">10:00</option>
						<option <?php if(@$fim == '1010'){ ?> selected <?php } ?>  value="1010">10:10</option>
						<option <?php if(@$fim == '1020'){ ?> selected <?php } ?>  value="1020">10:20</option>
						<option <?php if(@$fim == '1030'){ ?> selected <?php } ?>  value="1030">10:30</option>
						<option <?php if(@$fim == '1040'){ ?> selected <?php } ?>  value="1040">10:40</option>
						<option <?php if(@$fim == '1050'){ ?> selected <?php } ?>  value="1050">10:50</option>
						

						<option <?php if(@$fim == '1100'){ ?> selected <?php } ?>  value="1100">11:00</option>
						<option <?php if(@$fim == '1110'){ ?> selected <?php } ?>  value="1110">11:10</option>
						<option <?php if(@$fim == '1120'){ ?> selected <?php } ?>  value="1120">11:20</option>
						<option <?php if(@$fim == '1130'){ ?> selected <?php } ?>  value="1130">11:30</option>
						<option <?php if(@$fim == '1140'){ ?> selected <?php } ?>  value="1140">11:40</option>
						<option <?php if(@$fim == '1150'){ ?> selected <?php } ?>  value="1150">11:50</option>


						<option <?php if(@$fim == '1200'){ ?> selected <?php } ?>  value="1200">12:00</option>
						<option <?php if(@$fim == '1210'){ ?> selected <?php } ?>  value="1210">12:10</option>
						<option <?php if(@$fim == '1220'){ ?> selected <?php } ?>  value="1220">12:20</option>
						<option <?php if(@$fim == '1230'){ ?> selected <?php } ?>  value="1230">12:30</option>
						<option <?php if(@$fim == '1240'){ ?> selected <?php } ?>  value="1240">12:40</option>
						<option <?php if(@$fim == '1250'){ ?> selected <?php } ?>  value="1250">12:50</option>

						<option <?php if(@$fim == '1300'){ ?> selected <?php } ?>  value="1300">13:00</option>
						<option <?php if(@$fim == '1310'){ ?> selected <?php } ?>  value="1310">13:10</option>
						<option <?php if(@$fim == '1320'){ ?> selected <?php } ?>  value="1320">13:20</option>
						<option <?php if(@$fim == '1330'){ ?> selected <?php } ?>  value="1330">13:30</option>
						<option <?php if(@$fim == '1340'){ ?> selected <?php } ?>  value="1340">13:40</option>
						<option <?php if(@$fim == '1350'){ ?> selected <?php } ?>  value="1350">13:50</option>

						<option <?php if(@$fim == '1450'){ ?> selected <?php } ?>  value="1400">14:50</option>
						<option <?php if(@$fim == '1400'){ ?> selected <?php } ?>  value="1410">14:00</option>
						<option <?php if(@$fim == '1410'){ ?> selected <?php } ?>  value="1420">14:10</option>
						<option <?php if(@$fim == '1420'){ ?> selected <?php } ?>  value="1430">14:20</option>
						<option <?php if(@$fim == '1430'){ ?> selected <?php } ?>  value="1440">14:30</option>
						<option <?php if(@$fim == '1440'){ ?> selected <?php } ?>  value="1450">14:40</option>
						

						<option <?php if(@$fim == '1500'){ ?> selected <?php } ?>  value="1500">15:00</option>
						<option <?php if(@$fim == '1510'){ ?> selected <?php } ?>  value="1510">15:10</option>
						<option <?php if(@$fim == '1520'){ ?> selected <?php } ?>  value="1520">15:20</option>
						<option <?php if(@$fim == '1530'){ ?> selected <?php } ?>  value="1530">15:30</option>
						<option <?php if(@$fim == '1540'){ ?> selected <?php } ?>  value="1540">15:40</option>
						<option <?php if(@$fim == '1550'){ ?> selected <?php } ?>  value="1550">15:50</option>

						<option <?php if(@$fim == '1600'){ ?> selected <?php } ?>  value="1600">16:00</option>
						<option <?php if(@$fim == '1610'){ ?> selected <?php } ?>  value="1610">16:10</option>
						<option <?php if(@$fim == '1620'){ ?> selected <?php } ?>  value="1620">16:20</option>
						<option <?php if(@$fim == '1630'){ ?> selected <?php } ?>  value="1630">16:30</option>
						<option <?php if(@$fim == '1640'){ ?> selected <?php } ?>  value="1640">16:40</option>
						<option <?php if(@$fim == '1650'){ ?> selected <?php } ?>  value="1650">16:50</option>

						<option <?php if(@$fim == '1700'){ ?> selected <?php } ?>  value="1700">17:00</option>
						<option <?php if(@$fim == '1710'){ ?> selected <?php } ?>  value="1710">17:10</option>
						<option <?php if(@$fim == '1720'){ ?> selected <?php } ?>  value="1720">17:20</option>
						<option <?php if(@$fim == '1730'){ ?> selected <?php } ?>  value="1730">17:30</option>
						<option <?php if(@$fim == '1740'){ ?> selected <?php } ?>  value="1740">17:40</option>
						<option <?php if(@$fim == '1750'){ ?> selected <?php } ?>  value="1750">17:50</option>

						<option <?php if(@$fim == '1800'){ ?> selected <?php } ?>  value="1800">18:00</option>
						<option <?php if(@$fim == '1810'){ ?> selected <?php } ?>  value="1810">18:10</option>
						<option <?php if(@$fim == '1820'){ ?> selected <?php } ?>  value="1820">18:20</option>
						<option <?php if(@$fim == '1830'){ ?> selected <?php } ?>  value="1830">18:30</option>
						<option <?php if(@$fim == '1840'){ ?> selected <?php } ?>  value="1840">18:40</option>
						<option <?php if(@$fim == '1850'){ ?> selected <?php } ?>  value="1850">18:50</option>

						<option <?php if(@$fim == '1900'){ ?> selected <?php } ?>  value="1900">19:00</option>
						<option <?php if(@$fim == '1910'){ ?> selected <?php } ?>  value="1910">19:10</option>
						<option <?php if(@$fim == '1920'){ ?> selected <?php } ?>  value="1920">19:20</option>
						<option <?php if(@$fim == '1930'){ ?> selected <?php } ?>  value="1930">19:30</option>
						<option <?php if(@$fim == '1940'){ ?> selected <?php } ?>  value="1940">19:40</option>
						<option <?php if(@$fim == '1950'){ ?> selected <?php } ?>  value="1950">19:50</option>

						<option <?php if(@$fim == '2000'){ ?> selected <?php } ?>  value="2000">20:00</option>
						<option <?php if(@$fim == '2010'){ ?> selected <?php } ?>  value="2010">20:10</option>
						<option <?php if(@$fim == '2020'){ ?> selected <?php } ?>  value="2020">20:20</option>
						<option <?php if(@$fim == '2030'){ ?> selected <?php } ?>  value="2030">20:30</option>
						<option <?php if(@$fim == '2040'){ ?> selected <?php } ?>  value="2040">20:40</option>
						<option <?php if(@$fim == '2050'){ ?> selected <?php } ?>  value="2050">20:50</option>



						<option <?php if(@$fim == '2100'){ ?> selected <?php } ?>  value="2100">21:00</option>
						<option <?php if(@$fim == '2110'){ ?> selected <?php } ?>  value="2110">21:10</option>
						<option <?php if(@$fim == '2120'){ ?> selected <?php } ?>  value="2120">21:20</option>
						<option <?php if(@$fim == '2130'){ ?> selected <?php } ?>  value="2130">21:30</option>
						<option <?php if(@$fim == '2140'){ ?> selected <?php } ?>  value="2140">21:40</option>
						<option <?php if(@$fim == '2150'){ ?> selected <?php } ?>  value="2150">21:50</option>

						<option <?php if(@$fim == '2200'){ ?> selected <?php } ?>  value="2200">22:00</option>
						<option <?php if(@$fim == '2210'){ ?> selected <?php } ?>  value="2210">22:10</option>
						<option <?php if(@$fim == '2220'){ ?> selected <?php } ?>  value="2220">22:20</option>
						<option <?php if(@$fim == '2230'){ ?> selected <?php } ?>  value="2230">22:30</option>
						<option <?php if(@$fim == '2240'){ ?> selected <?php } ?>  value="2240">22:40</option>
						<option <?php if(@$fim == '2250'){ ?> selected <?php } ?>  value="2250">22:50</option>
						<option <?php if(@$fim == '2300'){ ?> selected <?php } ?>  value="2300">23:00</option>
																					
							</select>
						</div> 
				    </div>

				    <div class="col-md-6">
						 <div class="mb-3">

							<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_states2 = "SELECT * FROM equipamento ORDER BY nome ASC";
				                 $sql_query_states2 = $conexao->query($sql_code_states2) or die($conexao->error);                  
				               ?>
				               <label for="exampleFormControlInput1" class="form-label">Equipamento</label>
				                <select data-width="100%" class="form-control mr-1" id="equipamento"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="equipamento" >
				                    
				                    <option class="form-control" value="<?php echo @$equipamento ?>" ><?php echo @$nome_equipamento_modal ?></option>

				                    <?php while($nome = $sql_query_states2->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->

						</div> 
					</div>

				    
				    
			    </div> <!-- fim grupo -->

				

				<div class="row"> <!-- grupo -->

					<div class="col-md-4">
						 <div class="mb-3">			
							<label for="exampleFormControlInput1" class="form-label">Status</label>
								<select class="form-select mt-1" aria-label="Default select example" name="status">
																	
									<option <?php if(@$status == '--'){ ?> selected <?php } ?>  value="--">--</option>

									<option <?php if(@$status == 'Inativo'){ ?> selected <?php } ?>  value="Inativo">Inativo</option>

									<option <?php if(@$status == 'Confirmado'){ ?> selected <?php } ?>  value="Confirmado">Confirmado</option>

									<!--<option <?php if(@$status == 'Pronto'){ ?> selected <?php } ?>  value="Pronto">Pronto</option> -->
									
									<option <?php if(@$status == 'Cancelado'){ ?> selected <?php } ?>  value="Cancelado">Cancelado</option>

									<option <?php if(@$status == 'Faltou'){ ?> selected <?php } ?>  value="Faltou">Faltou</option>

									<option <?php if(@$status == 'Pago'){ ?> selected <?php } ?>  value="Pago">Pago</option>

									<option <?php if(@$status == 'BLOQUEADO'){ ?> selected <?php } ?>  value="BLOQUEADO">BLOQUEADO</option>

							</select>
						</div> 
					</div>

					<div class="col-md-4">
						 <div class="mb-3">
							
							<label for="exampleFormControlInput1" class="form-label">Situação do Atendimento</label>
								<select class="form-select mt-1" aria-label="Default select example" name="situacao">
																	
									<option <?php if(@$situacao == '--'){ ?> selected <?php } ?>  value="--">--</option>

									<option <?php if(@$situacao == 'Em Espera'){ ?> selected <?php } ?>  value="Em Espera">Em Espera</option>

									<option <?php if(@$situacao == 'Em Atendimento'){ ?> selected <?php } ?>  value="Em Andamento">Em Andamento</option>

									<option <?php if(@$situacao == 'Concluido'){ ?> selected <?php } ?>  value="Concluido">Concluido</option>

									<option <?php if(@$situacao == 'INTERVALO'){ ?> selected <?php } ?>  value="INTERVALO">INTERVALO</option>
																																		
							</select>	
							
						</div> 
					</div>	


					<!--
					<div class="col-md-4">
						 <div class="mb-3">			
							<label for="exampleFormControlInput1" class="form-label">Avaliação</label>
								<select class="form-select mt-1" aria-label="Default select example" name="eh_avaliacao">

									<option <?php if(@$eh_avaliacao == 'Não'){ ?> selected <?php } ?>  value="Não">Não</option>	

									<option <?php if(@$eh_avaliacao == 'Sim'){ ?> selected <?php } ?>  value="Sim">Sim</option>																													
							</select>
						</div> 
					</div>
				    -->

					

				


			    </div> <!-- fim grupo -->


			    <div class="row"> <!-- grupo -->


			    	<div class="col-md-4">
						 <div class="mb-3">				
							
							
				              <?php
				                 $sql_code_states3 = "SELECT * FROM servicos ORDER BY nome ASC";
				                 $sql_query_states3 = $conexao->query($sql_code_states3) or die($conexao->error);

				                 echo @$id_consulta_intens_clientes;

				               ?>
				               <label for="exampleFormControlInput1" class="form-label">Procedimento</label>
				                <select data-width="100%" class="form-control mr-1" id="procedimento"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="procedimento" >
				                    
				                    <option class="form-control" value="<?php echo @$procedimento ?>" ><?php echo @$nome_procedimento_modal ?></option>

				                    <?php while($nome = $sql_query_states3->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>


				                


						</div> 
				    </div>
					
				
					<div class="col-md-4">
						 <div class="mb-3">										
							 
				              <?php
				                 $sql_code_states4 = "SELECT * FROM produtos ORDER BY id ASC";
				                 $sql_query_states4 = $conexao->query($sql_code_states4) or die($conexao->error);                  
				               ?>
				               <label for="exampleFormControlInput1" class="form-label">Produto</label>
				                <select data-width="100%" class="form-control mr-1" id="produtos"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?> name="produtos" >
				                    
				                    <option class="form-control" value="<?php echo @$produtos ?>" ><?php echo @$nome_produtos_modal ?></option>

				                    <?php while($nome = $sql_query_states4->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                 


						</div> 
				    </div>

				    	<div class="col-md-4">
						 <div class="mb-3">											
							
							<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
			              <?php
			                 $sql_code_profissional = "SELECT * FROM usuarios where ativo_na_agenda = 'Ativo' and situacao = 'Ativo' ORDER BY nome ASC";
			                 $sql_query_profissional = $conexao->query($sql_code_profissional) or die($conexao->error);                  
			               ?>
			               <label for="exampleFormControlInput1" class="form-label">Profissional</label>

			                <select data-width="100%" class="form-control mr-1" id="profissional"  required="" 
			                
			                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="profissional" >
			                    
			                    <option class="form-control" value="<?php echo @$profissional ?>" ><?php echo @$nome_profissional_modal ?></option>

			                    <?php while($nome = $sql_query_profissional->fetch_assoc()) { ?> 

			                    <option 
			                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
			                      
			                    </option>
			                    <?php } ?>
			                </select>
			                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->

						</div> 
				    </div>


			    </div> <!-- fim grupo -->



			    <div class="row"> <!-- grupo -->
	

					<!--<div class="col-md-4">
						 <div class="mb-3">
												
							<label for="exampleFormControlInput1" class="form-label">Valor</label>
							<input type="text" class="form-control" id="valor" name="valor" placeholder="Valor" value="<?php echo @$valor ?>">	

						</div> 
					</div>-->
				
					<div class="col-md-4">
						 <div class="mb-3">

						 <!--<label for="exampleFormControlInput1" class="form-label">Tel Cliente</label>
							<input type="text" class="form-control" id="tel_cliente" name="tel_cliente" placeholder="Tel Cliente" value="<?php echo @$tel_cliente ?>">	
							-->								
						</div> 
				    </div>

				    

			    </div> <!-- fim grupo -->

			     


		

			    <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Obs.</label>					
						<textarea type="text" class="form-control" id="descricao" name="descricao"  placeholder="Descrição" maxlength="200"><?php echo @$descricao ?></textarea>
				</div> 
							

					
					<div class="form-group">
						<label >Arquivo</label>
						<input type="file" value="<?php echo @$foto ?>"  class="form-control-file" id="imagem" name="imagem" onChange="carregarImg();">
					</div>

					<div id="divImgConta" class="mt-4">
						<?php if(@$arquivo != ""){ ?>
							<img src="../img/<?php echo $pag ?>/<?php echo @$arquivo_extensao2 ?>"  width="200px" id="target">
						<?php  }else{ ?>
							<img src="../img/<?php echo $pag ?>/sem-foto.jpg" width="200px" id="target">
						<?php } ?>
					</div>
					
					


					<small><div align="center" class="mt-1" id="mensagem">

					</div> </small>

				</div>
				<div class="modal-footer">
					<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>

					<input name="id" type="hidden" value="<?php echo @$_GET['id'] ?>">

					

				</div>
			</form>
		</div>
	</div>
</div>






<div class="modal fade" tabindex="-1" id="modalDeletar" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Excluir Agendamento</h5>
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





<div class="modal fade" tabindex="-1" id="modalBaixar" >
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Baixar Registro</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form method="POST" id="form-baixar">
				<div class="modal-body">

					<p>Deseja Realmente confirmar o Recebimento do pagamento desta agendamento?</p>

					<small><div align="center" class="mt-1" id="mensagem-baixar">
						
					</div> </small>

				</div>
				
				<div class="modal-footer">

					<button type="button" id="btn-fechar-baixar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>

					<button name="btn-baixar" id="btn-excluir" type="submit" class="btn btn-success">Baixar</button>

					<input name="id" type="hidden" value="<?php echo @$_GET['id'] ?>">

				</div>
			</form>
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



<?php 
if(@$_GET['funcao'] == "baixar"){ ?>
	<script type="text/javascript">
		var myModal = new bootstrap.Modal(document.getElementById('modalBaixar'), {
			
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






<!--AJAX PARA EXCLUIR DADOS -->
<script type="text/javascript">
	$("#form-baixar").submit(function () {
		var pag = "<?=$pag?>";
		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: pag + "/baixar.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {

				$('#mensagem-baixar').removeClass()

				if (mensagem.trim() == "Baixado com Sucesso!") {

					$('#mensagem-baixar').addClass('text-success')

					$('#btn-fechar-baixar').click();
					window.location = "index.php?pagina="+pag;

				} else {

					$('#mensagem-baixar').addClass('text-danger')
				}

				$('#mensagem-baixar').text(mensagem)

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

		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);
        //console.log(resultado[1]);

        if(resultado[1] === 'pdf'){
        	$('#target').attr('src', "../img/agendar_conectado/pdf.png");
        	return;
        }

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