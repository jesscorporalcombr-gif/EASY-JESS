<?php 
$pag = 'acompanhamento_cliente';
@session_start();

require_once('../conexao.php');
require_once('../conexao/conexao.php');
require_once('verificar-permissao.php');

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

<h4 style="font-size: 14px;  font-style:normal;" class="text-uppercase">Acompanhamentos</h4>



<!--
<a href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-secondary mt-2">Novo Produto</a>-->

<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from acompanhamento order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>
					<tr>
						<th>Nome</th>
						<th>Detalhes</th>
						<th>Avaliador</th>
						<th>Ações</th>
						
					</tr>
				</thead>
				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}

						
					    $id_usu = $res[$i]['profissional'];
						$query_p = $pdo->query("SELECT * from usuarios where id = '$id_usu'");
						$res_p = $query_p->fetchAll(PDO::FETCH_ASSOC);
						$nome_usu_pro = $res_p[0]['nome'];
						

						?>

						<tr>
							<td><?php echo $res[$i]['cpf'] ?></td>
							<td><?php echo $res[$i]['message'] ?></td>
							<td><?php echo $nome_usu_pro ?></td>

							<td>
								<!--
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res[$i]['id'] ?>" title="Editar Registro" style="text-decoration: none">
									<i class="bi bi-pencil-square text-primary"></i>
								</a>	-->						

								<a href="#" onclick="mostrarDados(
								'<?php echo $res[$i]['cpf'] ?>',
								'<?php echo $res[$i]['message'] ?>',
								'<?php echo $res[$i]['profissional'] ?>',
								

								)" title="Ver Detalhes" style="text-decoration: none">

									<i class="bi bi-card-text text-dark mx-1"></i>
								</a>



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
	$titulo_modal = 'Editar Produto';
	$query = $pdo->query("SELECT * from produtos where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 

		$cpf = $res[0]['cpf'];
		$message = $res[0]['message'];
		$profissional = $res[0]['profissional'];


	}
}else{
	$titulo_modal = 'Inserir Produto';
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

					<div class="row">
						<div class="col-md-5">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Código</label>
								<input type="number" class="form-control" id="codigo" name="codigo" placeholder="Código" required="" value="<?php echo @$codigo ?>">
							</div> 

						</div>
						<div class="col-md-7">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Nome</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required="" value="<?php echo @$nome ?>">
							</div> 
						</div>
						
					</div>


					<div class="row">
												
						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Valor Venda</label>
								<input type="text" class="form-control" id="valor_venda" name="valor_venda" placeholder="Valor Venda" required="" value="<?php echo @$valor_venda ?>">
							</div> 
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Valor Compra</label>
								<input type="text" class="form-control" id="valor_compra" name="valor_compra" placeholder="Valor Compra" required="" value="<?php echo @$valor_compra ?>">
							</div> 
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Estoque</label>
								<input type="text" class="form-control" id="estoque" name="estoque" placeholder="Estoque" required="" value="<?php echo @$estoque ?>">
							</div> 
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Estoque Mínimo</label>
								<input type="number" class="form-control" id="estoque_min" name="estoque_min" placeholder="Estoque Mínimo" required="" value="<?php echo @$estoque_min ?>">
							</div> 
						</div>

					
					</div>

					
					<div class="row">

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Local</label>



								<select class="form-select mt-1" aria-label="Default select example" name="local">
									<?php 
									$query = $pdo->query("SELECT * from locais_de_stoque order by local asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if($total_reg > 0){ 

										for($i=0; $i < $total_reg; $i++){
											foreach ($res[$i] as $key => $value){	}
												?>

											<option <?php if(@$local == $res[$i]['id']){ ?> selected <?php } ?>  value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['local'] ?></option>

										<?php }

									}else{ 
										echo '<option value="">Cadastre uma local</option>';

									} ?>
									

								</select>

							</div> 
						</div>

						<div class="col-md-4">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Categoria</label>
								
								<select class="form-select mt-1" aria-label="Default select example" name="categoria">
									<?php 
									$query = $pdo->query("SELECT * from categorias order by nome asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if($total_reg > 0){ 

										for($i=0; $i < $total_reg; $i++){
											foreach ($res[$i] as $key => $value){	}
												?>

											<option <?php if(@$categoria == $res[$i]['id']){ ?> selected <?php } ?>  value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['nome'] ?></option>

										<?php }

									}else{ 
										echo '<option value="">Cadastre uma Categoria</option>';

									} ?>
									

								</select>
							</div> 
						</div>


						<div class="col-md-5">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Sub Categoria</label>
								
								<select class="form-select mt-1" aria-label="Default select example" name="sub_categoria">
									<?php 
									$query = $pdo->query("SELECT * from sub_categorias order by nome asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if($total_reg > 0){ 

										for($i=0; $i < $total_reg; $i++){
											foreach ($res[$i] as $key => $value){	}
												?>

											<option <?php if(@$sub_categoria == $res[$i]['id']){ ?> selected <?php } ?>  value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['nome'] ?></option>

										<?php }

									}else{ 
										echo '<option value="">Cadastre uma Sub Categoria</option>';

									} ?>
									

								</select>
								</div>
							</div> 


						<div class="mb-3">
							<label for="exampleFormControlInput1" class="form-label">Descrição do Produto</label>
							<textarea type="text" class="form-control" id="descricao" name="descricao" maxlength="200"><?php echo @$descricao ?></textarea>
					    </div> 


					   

					<div class="row"> <!-- grupo -->
						<div class="col-md-6">
							 <div class="mb-3">
													
								<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_fornecedor = "SELECT * FROM fornecedores ORDER BY nome ASC";
				                 $sql_query_fornecedor = $conexao->query($sql_code_fornecedor) or die($conexao->error);                  
				               ?>
				               <label for="exampleFormControlInput1" class="form-label">Fornecedor</label>
				                <select data-width="100%" class="form-control mr-1" id="fornecedor"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="fornecedor" >
				                    
				                    <option class="form-control" value="<?php echo @$fornecedor ?>" ><?php echo @$nome_fornecedor_modal /*variavel nome!!*/?></option>

				                    <?php while($nome = $sql_query_fornecedor->fetch_assoc()) { ?> 

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
							
							<label for="exampleFormControlInput1" class="form-label">Ativo p/ Venda</label>
								<select class="form-select mt-1" aria-label="Default select example" name="ativo">
																	
									<option <?php if(@$ativo == 'Sim'){ ?> selected <?php } ?>  value="Sim">Sim</option>

									<option <?php if(@$ativo == 'Não'){ ?> selected <?php } ?>  value="Não">Não</option>	
																																					
							</select>	
							
						</div> 
					</div>	
				    

			    </div> <!-- fim grupo -->






				    
									

						<div class="col-md-4">
							<div class="form-group">
								<label >Foto</label>
								<input type="file" value="<?php echo @$foto ?>"  class="form-control-file" id="imagem" name="imagem" onChange="carregarImg();">
							</div>							
						</div> </br>

						
					<div class="row"> <!-- grupo -->
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
					</div> 

				</br>
					
					
					<div id="codigoBarra"></div>


					<small><div align="center" class="mt-1" id="mensagem">

					</div> </small>

				</div>
				<div class="modal-footer">
					<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>

					<input name="id" type="hidden" value="<?php echo @$_GET['id'] ?>">

					<input name="antigo" type="hidden" value="<?php echo @$nome ?>">

					<input name="antigo2" type="hidden" value="<?php echo @$codigo ?>">


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

				<b>Nome: </b>
				<span id="nome-registro"></span>
				<hr>

				<b>Detalhes: </b>
				<span id="codigo_"></span>
				<hr>

				<b>Profissional: </b>
				<span id="valor-venda"></span>
				<hr>

		

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
		gerarCodigo();
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
	function mostrarDados(cpf, message, profissional){
		event.preventDefault();

		if(nome_forn.trim() === ""){
			document.getElementById("div-forn").style.display = 'none';
		}else{
			document.getElementById("div-forn").style.display = 'block';
		}

		$('#nome-registro').text(cpf);
		$('#codigo_').text(message);
		$('#valor-venda').text(profissional);
		

		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {
			
		})

		myModal.show();
	}
</script>







<!--AJAX PARA EXCLUIR DADOS -->
<script type="text/javascript">
	$("#codigo").keyup(function () {
		gerarCodigo();
	});
</script>


<script type="text/javascript">
	var pag = "<?=$pag?>";
	function gerarCodigo(){
		$.ajax({
			url: pag + "/barras.php",
			method: 'POST',
			data: $('#form').serialize(),
			dataType: "html",

			success:function(result){
				$("#codigoBarra").html(result);
			}
		});
	}
</script>



<script type="text/javascript">
	function comprarProdutos(id, valor, lucro, valor_compra){
		event.preventDefault();

		console.log(valor)

		$('#id-comprar').val(id);
		$('#valor_v').val(valor);
		$('#lucro').val(lucro);
		$('#valor_compra').val(valor_compra);

		var myModal = new bootstrap.Modal(document.getElementById('modalComprar'), {
			
		})
		myModal.show();
	}
</script>







<!--AJAX PARA COMPRAR PRODUTO -->
<script type="text/javascript">
	$("#form-comprar").submit(function () {
		var pag = "<?=$pag?>";
		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: pag + "/comprar-produto.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {

				$('#mensagem-comprar').removeClass()

				if (mensagem.trim() == "Salvo com Sucesso!") {

                    //$('#nome').val('');
                    //$('#cpf').val('');
                    $('#btn-fechar').click();
                    window.location = "index.php?pagina="+pag;

                } else {

                	$('#mensagem-comprar').addClass('text-danger')
                }

                $('#mensagem-comprar').text(mensagem)

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

<script type="text/javascript">
	function calcularLucro(){
		console.log('chamou')

		valor_compra = $("#valor_compra").val();
		lucro = $("#lucro").val();

		valor_compra = valor_compra.replace(",",".");
		lucro = lucro.replace("%","");
		
		total = (valor_compra * lucro / 100);
		total = parseFloat(total) + parseFloat(valor_compra);
		$("#valor_v").val(total)

	}
</script>


</body>
</html>