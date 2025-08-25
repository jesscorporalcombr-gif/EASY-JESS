<?php 
$pag = 'ver_comandas_por_cliente';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
require_once('../conexao/conexao.php');


//RECUPERAR DADOS DO USUÁRIO
$query = $pdo->query("SELECT * from clientes WHERE id = '$_SESSION[id_usuario]'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
@$nome_usu_cli = $res[0]['nome'];
@$email_usu = $res[0]['email'];
@$senha_usu = $res[0]['senha'];
@$nivel_usu = $res[0]['nivel'];
@$foto = $res[0]['foto'];
@$cpf_usu = $res[0]['cpf'];
@$id_cliente_sessao = $res[0]['id'];

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">	

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	<link rel="stylesheet" type="text/css" href="../vendor/DataTables/datatables.min.css"/>
 
	<script type="text/javascript" src="../vendor/DataTables/datatables.min.js"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

	<link rel="shortcut icon" href="../img/logo.png" />

  <!-- CSS template cliente  //////////////////////////////////////////////////////////////////////////////////-->

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

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

	 <h4 style="font-size: 14px;  font-style:normal;" class="text-uppercase">&nbsp;&nbsp;&nbsp;&nbsp;Protocolos</h4> 

</nav>

</br>


	<div class="row"> <!--  -->
		<?php 
			// busca o nome do clientes referente desse documento
			@$id_clientes0 = $id_cliente_sessao;
			@$query_clientes0 = $pdo->query("SELECT * from clientes where id = '$id_clientes0'");
			@$res_clientes0 = $query_clientes0->fetchAll(PDO::FETCH_ASSOC);
			@$nome_clientes0 = $res_clientes0[0]['nome']; 
			@$nome_clientes_id = $res_clientes0[0]['id']; 
		?>	
		<div class="col-md-3">
			<div class="mb-3">
				 <h6><?php echo $nome_clientes0 ?></h6>
			</div>
		</div>
		
	</div> <!-- fim grupo  -->

	<hr class="dropdown-divider" > <!-- add linha -->


<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from vender_servico where cliente = '$nome_clientes_id' order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>
					<tr>
						<th>Data Pré-venda</th>
						<th>Cliente</th>
						<th>Serviço</th>
						<th>Qt. Comprado</th>
						<th>Qt. Usados</th>
						<th>Forma Pag.</th>
						<th>Desconto</th>
						<th>Status</th>		
						<th>Situação</th>				
						<th>Arquivo</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}

						@$situacao_ = @$res[$i]['situacao'];

						@$id_usu = $res[$i]['tecnico'];
						@$query_p = $pdo->query("SELECT * from usuarios where id = '$id_usu'");
						@$res_p = $query_p->fetchAll(PDO::FETCH_ASSOC);
						@$nome_usu = $res_p[0]['nome'];

						// busca o nome do clientes referente desse documento
						@$id_clientes = $res[$i]['cliente_nome'];
						@$query_clientes = $pdo->query("SELECT * from clientes where nome = '$id_clientes'");
						@$res_clientes = $query_clientes->fetchAll(PDO::FETCH_ASSOC);
						@$nome_clientes = $res_clientes[0]['nome'];

						@$id_id_form_pag = $res[$i]['id_form_pag'];
						@$query_id_form_pag = $pdo->query("SELECT * from forma_pgtos where id = '$id_id_form_pag'");
						@$res_id_form_pag = $query_id_form_pag->fetchAll(PDO::FETCH_ASSOC);
						@$nome_id_form_pag = $res_id_form_pag[0]['nome'];
					
						@$id_item = $res[$i]['item'];
						@$query_item = $pdo->query("SELECT * from servicos where id = '$id_item'");
						@$res_item = $query_item->fetchAll(PDO::FETCH_ASSOC);
						@$nome_item = $res_item[0]['nome'];


						$extensao = strchr($res[$i]['foto'], '.');
						if($extensao == '.pdf'){
							$arquivo_pasta = 'pdf.png';
						}else{
							$arquivo_pasta = $res[$i]['foto'];
						}
						
						?>

						<tr>
							

							<td><?php echo implode('/', explode('-', $res[$i]['data_abertura'])); ?></td>
							<td><?php echo $nome_clientes ?></td>
							<td><?php echo $nome_item ?></td>
							<td><?php echo $res[$i]['qtd'] ?></td>
							<td><?php echo $res[$i]['qtd_usados'] ?></td>
							<td><?php echo $nome_id_form_pag ?></td>
							<td><?php echo $res[$i]['desconto'] . '%' ?></td>
							<td><?php echo $res[$i]['status'] ?></td>
							<td><?php echo $res[$i]['situacao'] ?></td>
											
							
							<td><a href="../img/vender_servico/<?php echo $res[$i]['foto'] ?>" title="Ver Arquivo" style="text-decoration: none" target="_blank">
								<img src="../img/vender_servico/<?php echo $arquivo_pasta ?>" width="40">
							    </a>
						    </td>
						<td>
							
								

							<?php if (@$situacao_ == 'Fechado' or @$situacao_ == 'Cancelado') { echo ''; ?>
							   	
						   <?php } else {  ?>

						   		<!--
						   		<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res[$i]['id'] ?>" title="Editar Registro" style="text-decoration: none">
									<i class="bi bi-pencil-square text-primary"></i>
								</a>

								
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res[$i]['id'] ?>" title="Excluir Registro" style="text-decoration: none">
									<i class="bi bi-archive text-danger mx-1"></i>
								</a>  -->


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
	$titulo_modal = 'Editar Venda';
	$query = $pdo->query("SELECT * from vender_servico where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 

		$id = $res[0]['id'];
		$item = $res[0]['item'];
		$cliente = $res[0]['cliente']; //ID do cliente
		$cliente_nome = $res[0]['cliente_nome'];  // Nome do cliente
		$qtd = $res[0]['qtd'];
		$qtd_usados = $res[0]['qtd_usados'];
		$desconto = $res[0]['desconto'];
		$valor_em_desconto = $res[0]['valor_em_desconto'];
		$valor_pecas = $res[0]['valor_pecas'];
		$valor_servico_sem_desconto = $res[0]['valor_servico_sem_desconto'];
		$valor_total = $res[0]['valor_total'];
		$status_nome = $res[0]['status'];
		$situacao = $res[0]['situacao'];
		$produto = $res[0]['produto'];
		$obs = $res[0]['obs'];
		$profissional = $res[0]['profissional'];
		$tecnico = $res[0]['tecnico'];
		$id_form_pag = $res[0]['id_form_pag'];
		$pgto = $res[0]['pgto'];
		$serie = $res[0]['serie'];		
		$problema = $res[0]['problema'];
		$laudo = $res[0]['laudo'];
		$modificado = $res[0]['modificado'];
		$usuario = $res[0]['usuario'];
		$data_abertura = $res[0]['data_abertura'];
		$data_geracao = $res[0]['data_geracao'];
		$data_aprovacao = $res[0]['data_aprovacao'];
		$ultima_modificacao = $res[0]['ultima_modificacao'];
		$foto = $res[0]['foto'];
		$arquivo = $res[0]['foto'];
				


		// busca o nome do usuario atraves do id
		$id_profissional_modal = $tecnico;
		$query_profissional_modal = $pdo->query("SELECT * from usuarios where id = '$id_profissional_modal' limit 1");
		$res_profissional_modal = $query_profissional_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_profissional_modal = $res_profissional_modal[0]['nome'];


		// busca o nome do forma pagamento atraves do id
		$id_forma_pgtos_modal = $id_form_pag;
		$query_forma_pgtos_modal = $pdo->query("SELECT * from forma_pgtos where id = '$id_forma_pgtos_modal' limit 1");
		$res_forma_pgtos_modal = $query_forma_pgtos_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_forma_pgtos_modal = $res_forma_pgtos_modal[0]['nome'];


		// busca o nome do item atraves do id
		$id_item_modal = $item;
		$query_item_modal = $pdo->query("SELECT * from servicos where id = '$id_item_modal' limit 1");
		$res_item_modal = $query_item_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_item_modal = $res_item_modal[0]['nome'];

			// busca o nome do status atraves do id
		//$id_nome_status_modal = $status_nome;
		//$query_nome_status_modal = $pdo->query("SELECT * from status where id = '$id_nome_status_modal' limit 1");
		//$res_nome_status_modal = $query_nome_status_modal->fetchAll(PDO::FETCH_ASSOC);
		//@$nome_status = $res_nome_status_modal[0]['status'];


		$extensao2 = strchr($arquivo, '.');
		if($extensao2 == '.pdf'){
			$arquivo_pasta2 = 'pdf.png';
		}else{
			$arquivo_pasta2 = $arquivo;
		}

	}
}else{
	$titulo_modal = 'Inserir Nova Venda';
}
?>

<div class="modal fade" tabindex="-1" id="modalCadastrar" data-bs-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php echo $titulo_modal ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form method="POST" id="form">
				<div class="modal-body">

					<div class="row"> <!-- inicio grupo  -->

					  <div class="col-md-6">
						<div class="mb-3">
							<label for="exampleFormControlInput1" class="form-label">Cliente Referente</label>


								<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_clientes = "SELECT * FROM clientes ORDER BY nome ASC";
				                 $sql_query_clientes = $conexao->query($sql_code_clientes) or die($conexao->error);                  
				               ?>
				                <select data-width="100%" class="form-control mr-1" id="usuario_referente_id"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="usuario_referente_id" >
				                    
				                    <option class="form-control" 

				                    value="<?php echo @$cliente //variavel que volta para o banco ID cliente?>" >
				                    <?php echo @$cliente_nome //variavel que mostra na tela nome ?></option>

				                    <?php while($Clientes_dados_nome = $sql_query_clientes->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $Clientes_dados_nome['id']) 

				                    echo "selected"; ?> value="<?php echo $Clientes_dados_nome['id']; ?>">

				                    <?php echo $Clientes_dados_nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->

							</div>
						</div>

						<div class="col-md-6">
							<div class="mb-3">
						
								<label for="exampleFormControlInput1" class="form-label">Profissional Comissionado</label>
								<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_usuarios = "SELECT * FROM usuarios ORDER BY nome ASC";
				                 $sql_query_usuarios = $conexao->query($sql_code_usuarios) or die($conexao->error);                  
				               ?>
				                <select data-width="100%" class="form-control mr-1" id="tecnico"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="tecnico" >
				                    
				                    <option class="form-control" 
				                    value="<?php echo @$tecnico ?>" >
				                    <?php echo @$nome_profissional_modal ?></option>

				                    <?php while($nome = $sql_query_usuarios->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->
							</div> 
						</div>

					</div> <!-- fim grupo  -->


					<div class="row"> <!-- inicio grupo  -->

						<div class="col-md-6">
							<div class="mb-3">
						
								<label for="exampleFormControlInput1" class="form-label">Protocolo</label>
								
								<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_servicos = "SELECT * FROM servicos ORDER BY nome ASC";
				                 $sql_query_servicos = $conexao->query($sql_code_servicos) or die($conexao->error);                  
				               ?>
				                <select data-width="100%" class="form-control mr-1" id="item"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="item" >
				                    
				                    <option class="form-control" 
				                    value="<?php echo @$item ?>" >
				                    <?php echo @$nome_item_modal ?></option>

				                    <?php while($nome = $sql_query_servicos->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->
							</div> 
						</div>

						<div class="col-md-3">
							 <div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Qtd. Comprados</label>
								<input type="number" class="form-control" id="qtd" name="qtd" placeholder="Qtd." value="<?php echo @$qtd ?>">
							</div> 
					    </div>

					    <div class="col-md-3">
							 <div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Qtd. Usados</label>
								<input type="number" class="form-control" id="qtd_usados" name="qtd_usados" placeholder="Qtd. Usados" value="<?php echo @$qtd_usados ?>">
							</div> 
					    </div>

					</div> <!-- fim grupo  -->



					<div class="row"> <!-- inicio grupo  -->
				
						<div class="col-md-5">
							<div class="mb-3">
						
								<label for="exampleFormControlInput1" class="form-label">Forma de Pagamento</label>
								
								<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_forma_pgtos = "SELECT * FROM forma_pgtos ORDER BY nome ASC";
				                 $sql_query_forma_pgtos = $conexao->query($sql_code_forma_pgtos) or die($conexao->error);                  
				               ?>
				                <select data-width="100%" class="form-control mr-1" id="id_form_pag"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="id_form_pag" >
				                    
				                    <option class="form-control" 
				                    value="<?php echo @$id_form_pag ?>" >
				                    <?php echo @$nome_forma_pgtos_modal ?></option>

				                    <?php while($nome = $sql_query_forma_pgtos->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->
							</div> 
						</div>

						<div class="col-md-2">
							 <div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Desconto %</label>
								<input type="number" class="form-control" id="desconto" name="desconto" placeholder="Desconto %" value="<?php echo @$desconto ?>">
							</div> 
					    </div>

					     <div class="col-md-5">
							 <div class="mb-3">

					            <label for="exampleFormControlInput1" class="form-label">Status do Pagamento</label>
					              <!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_status = "SELECT * FROM status ORDER BY status ASC";
				                 $sql_query_status = $conexao->query($sql_code_status) or die($conexao->error);                  
				               ?>
				                <select data-width="100%" class="form-control mr-1" id="status"  
				                
				                    <?php if(isset($_GET['status'])) echo "disabled"; ?>  name="status" >
				                    
				                    <option class="form-control" 
				                    value="<?php echo @$status_nome ?>" >
				                    <?php echo @$status_nome ?></option>

				                    <?php while($status = $sql_query_status->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['status']) && $_GET['status'] == $status['status']) echo "selected"; ?> value="<?php echo $status['status']; ?>"><?php echo $status['status']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->
					        </div>
					      </div>
					
					</div> <!-- fim grupo  -->

					<div class="col-md-4">
						 <div class="mb-3">
							
							<label for="exampleFormControlInput1" class="form-label">Situação da Venda</label>
								<select class="form-select mt-1" aria-label="Default select example" name="situacao">
																	
									<option <?php if(@$situacao == '--'){ ?> selected <?php } ?>  value="--">--</option>

									<option <?php if(@$situacao == 'Aberto'){ ?> selected <?php } ?>  value="Aberto">Aberto</option>

									<option <?php if(@$situacao == 'Fechado'){ ?> selected <?php } ?>  value="Fechado">Fechado</option>	

									<option <?php if(@$situacao == 'Cancelado'){ ?> selected <?php } ?>  value="Cancelado">Cancelado</option>			
																																					
							</select>	
							
						</div> 
					</div>	



					<div class="col-md-12">
						<div class="mb-3"><!-- Arrumar o salvamento em tabela-->
							
							<label for="exampleFormControlInput1" class="form-label">Observação</label>	

						<textarea type="text" class="form-control" id="obs" name="obs"  placeholder="obs." maxlength="200"><?php echo @$obs ?></textarea>

						</div>
					</div>


					<hr class="dropdown-divider"> <!-- linha de separação --> 
					<div class="row"> <!-- inicio grupo  -->
				
						<div class="col-md-12">
							 <div class="mb-3">

								   <label for="exampleFormControlInput1" class="form-label">
										<span style="font-size: 14px; ">Valor Unitário: R$ </span>
										<span style="font-size: 19px; color: #257BFF;"><?php echo @$valor_pecas ?></span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

										<span style="font-size: 14px; ">Valor Dado em desc: R$ </span>
										<span style="font-size: 19px; color: #257BFF;"><?php echo @$valor_em_desconto ?> </span>&nbsp;&nbsp;&nbsp;&nbsp;
									</label>

									<label for="exampleFormControlInput1" class="form-label">
										<span style="font-size: 14px; ">Valor Total Sem desc: R$ </span>

										<span style="font-size: 19px;color: #257BFF;"><?php echo @$valor_servico_sem_desconto ?></span> &nbsp;&nbsp;&nbsp;&nbsp;

										<span style="font-size: 14px; ">Valor Total c/ desc: R$ </span>
										<span style="font-size: 19px;  color: #257BFF;"><?php echo @$valor_total ?></span> &nbsp;&nbsp;&nbsp;&nbsp;			

									</label>				

							</div> 
					    </div>
			    
					</div> <!-- fim grupo  -->
					<hr class="dropdown-divider"> <!-- linha de separação --> 



					



					
							

					
					<div class="form-group">
						<label >Arquivo</label>
						<input type="file" value="<?php echo @$foto ?>"  class="form-control-file" id="imagem" name="imagem" onChange="carregarImg();">
					</div>

					<div id="divImgConta" class="mt-4">
						<?php if(@$arquivo != ""){ ?>
							<img src="../img/vender_servico/<?php echo @$arquivo_pasta2 ?>"  width="200px" id="target">
						<?php  }else{ ?>
							<img src="../img/vender_servico/sem-foto.jpg" width="200px" id="target">
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
				<h5 class="modal-title">Excluir Venda</h5>
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

					<p>Deseja Realmente confirmar o Recebimento do pagamento?</p>

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
        	$('#target').attr('src', "../img/vender_servico/pdf.png");
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




</body>
</html>