<?php 
$pag = 'promocoes';
@session_start();

require_once('../conexao.php');
require_once('../conexao/conexao.php');
require_once('verificar-permissao.php')
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
	 	<h4 style="font-size: 14px;  font-style:normal;" class="text-uppercase">&nbsp;&nbsp;&nbsp;&nbsp;Promoções</h4> 
    </nav>


<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from promocoes where ativo = 'Sim' order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>
					<tr>
						<th>Nome da Promoção</th>
						<th>Produto</th>
						<th>Valor de Promoção</th>
						<th>Data de Inicio</th>
						<th>Data final</th>
						<th>Ativa</th>						
						<th>Img.</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}

							@$id_usu = $res[$i]['usuario'];
							@$query_p = $pdo->query("SELECT * from usuarios where id = '$id_usu'");
							@$res_p = $query_p->fetchAll(PDO::FETCH_ASSOC);
							@$nome_usu = $res_p[0]['nome'];


							@$id_produtos = $res[$i]['id_produto'];
							@$query_produtos = $pdo->query("SELECT * from produtos where id = '$id_produtos'");
							@$res_produtos = $query_produtos->fetchAll(PDO::FETCH_ASSOC);
							@$nome_produtos = $res_produtos[0]['nome'];

							?>

							<tr>
							<td> <h6 style="font-size: 12px;  "> <?php echo $res[$i]['nome'] ?> </h6> </td>
							<td> <?php echo @$nome_produtos ?> </td>

							<td>R$ <?php echo number_format($res[$i]['valor'], 2, ',', '.'); ?></td>
							<td>  <?php echo $res[$i]['data_inicio'] ?>  </td>
							<td>  <?php echo $res[$i]['data_final'] ?>  </td>
							
							<td>  <?php echo $res[$i]['ativo'] ?>  </td>
					
							<td><img src="../img/<?php echo $pag ?>/<?php echo $res[$i]['foto'] ?>" width="40"></td>

						
							<td>
								<!--
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res[$i]['id'] ?>" title="Editar Registro">
									<i class="bi bi-pencil-square text-primary"></i>
								</a>

								<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res[$i]['id'] ?>" title="Excluir Registro">
									<i class="bi bi-archive text-danger mx-1"></i>
								</a> -->

								
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
	$titulo_modal = 'Editar Registro';
	$query = $pdo->query("SELECT * from promocoes where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 

		$nome = $res[0]['nome'];
		$foto = $res[0]['foto'];
		$id_promo = $res[0]['id'];

		$id_produto = $res[0]['id_produto'];
		$valor = $res[0]['valor'];
		$data_inicio = $res[0]['data_inicio'];
		$data_final = $res[0]['data_final'];
		$usuario = $res[0]['usuario'];
		$foto = $res[0]['foto'];
		$ativo = $res[0]['ativo'];


		$id_produtos2 = $res[0]['id_produto'];
		$query_produtos2 = $pdo->query("SELECT * from produtos where id = '$id_produtos2'");
		$res_produtos2 = $query_produtos2->fetchAll(PDO::FETCH_ASSOC);
		$nome_produtos_modal = $res_produtos2[0]['nome'];





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

						<div class="col-md-12">
							<div class="mb-6">
								<label for="exampleFormControlInput1" class="form-label">Nome Promoção</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Promoção"  value="<?php echo @$nome ?>">
							</div> 
						</div>

	
					</div>


					<div class="mb-3">
						

						<div class="col-md-12">
						 <div class="mb-3">										
							 
				              <?php
				                 $sql_code_states4 = "SELECT * FROM produtos ORDER BY id ASC";
				                 $sql_query_states4 = $conexao->query($sql_code_states4) or die($conexao->error);                  
				               ?>
				               <label for="exampleFormControlInput1" class="form-label">Produto</label>
				                <select data-width="100%" class="form-control mr-1" id="id_produto"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?> name="id_produto" >
				                    
				                    <option class="form-control" value="<?php echo @$id_produto ?>" >
				                    	<?php echo @$nome_produtos_modal ?></option>

				                    <?php while($nome = $sql_query_states4->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>				                 

						</div> 
				    </div>

					</div>  

			
					 <div class="row"> <!-- grupo -->
				    	<div class="col-md-6">
							 <div class="mb-3">					
								<label for="exampleFormControlInput1" class="form-label">Valor Uni.</label>
								<input type="text" class="form-control" id="valor" name="valor" placeholder="Valor da Promoção" required="" value="<?php echo @$valor ?>">
							</div> 
						</div> 

						<div class="col-md-6">
						    <div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Promoção Ativa?</label>
								<select class="form-select mt-1" aria-label="Default select example" name="ativo">
									
									<option <?php if(@$ativo == 'Não'){ ?> selected <?php } ?>  value="Não">Não</option>

									<option <?php if(@$ativo == 'Sim'){ ?> selected <?php } ?>  value="Sim">Sim</option>
			
								</select>
							</div>
						</div> 
					</div>








					 <div class="row"> <!-- grupo -->
				    	<div class="col-md-6">
							 <div class="mb-3">					
								<label for="exampleFormControlInput1" class="form-label">Data Inicial</label>
								<input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?php echo @$data_inicio ?>">
							</div> 
						</div> 

						<div class="col-md-6">
						    <div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Data Final</label>
								<input type="date" class="form-control" id="data_final" name="data_final" value="<?php echo @$data_final ?>">
							</div>
						</div> 
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


</body>
</html>