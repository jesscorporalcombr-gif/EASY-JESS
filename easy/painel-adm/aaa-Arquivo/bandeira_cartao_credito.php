<?php 
$pag = 'bandeira_cartao_credito'; //Débito
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php')
?>

<h4 style="font-size: 14px;  font-style:normal;" class="text-uppercase">Bandeiras Cartões Crédito</h4>

<link rel="stylesheet" type="text/css" href="../vendor/login/css/util.css">
<link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css">




<div class="col-md-9">  
	<div class="col-md-3">
		</br>
		<a style="font-size: 12px;  " href="index.php?pagina=<?php echo $pag ?>&funcao=novo"
		 type="button" class="btn btn-outline-primary">Nova Bandeira Crédito</a>
	</div>
</div>  

<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from bandeira_cartao_credito order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				
				<thead>
					<tr>
						<th>Bandeira</th>
						<th>Conta Bancaria</th>
						<th>Taxa Cartão de Crédito</th>	
						<th>Taxa Antecipação</th>						
						<th>Prazo</th>
						<th>Parcelas</th>	
						<!--<th>Gerente</th>
						<th>Tel:</th>
						<th>Banco</th>
						<th>Tipo de Conta</th>
						<th>Agencia</th>
						<th>Conta</th>
						<th>Dig.</th>
						<th>Nome Favorecido</th>
						<th>Documento Favorecido</th>						
						<th>Criado Por</th>
					    -->
						<th>Img</th>
						<th></th>
						<th>Ações</th>
					</tr>
				</thead>

				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}

							$extensao = strchr($res[$i]['foto'], '.'); //para mostrar os icones de extenção

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
							$arquivo_pasta = $res[$i]['foto'];
						}
							
							?>
						

						<tr>
							<td> <h6 style="font-size: 14px;  "> <?php echo $res[$i]['bandeira'] ?> </h6> </td>
							<td> <h6 style="font-size: 14px;  "> <?php echo $res[$i]['conta_bancaria'] ?> </h6> </td>
							<td> <h6 style="font-size: 14px;  "> <?php echo $res[$i]['taxa_cartao_de_credito'] ?> </h6> </td>
							<td> <h6 style="font-size: 14px;  "> <?php echo $res[$i]['taxa_antecipacao'] ?> </h6> </td>
							<td> <h6 style="font-size: 14px;  "> <?php echo $res[$i]['prazo'] ?> </h6> </td>
							<td> <h6 style="font-size: 14px;  "> <?php echo $res[$i]['parcelas'] ?> </h6> </td>

							<td><a href="../img/<?php echo $pag ?>/<?php echo $res[$i]['foto'] ?>" title="Ver Arquivo" style="text-decoration: none" target="_blank">

								<img src="../img/<?php echo $pag ?>/<?php echo $arquivo_pasta ?>" width="40">
								
							</a>
						    </td>

											
							<td>
							<td>
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res[$i]['id'] ?>" title="Editar Registro">
									<i class="bi bi-pencil-square text-primary"></i>
								</a>

								
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res[$i]['id'] ?>" title="Excluir Registro">
									<i class="bi bi-archive text-danger mx-1"></i>
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
	$titulo_modal = 'Editar Bandeira Crédito';
	$query = $pdo->query("SELECT * from bandeira_cartao_credito where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		
		$bandeira = $res[0]['bandeira'];	
		$conta_bancaria = $res[0]['conta_bancaria'];
		$taxa_cartao_de_credito = $res[0]['taxa_cartao_de_credito'];	
		$prazo = $res[0]['prazo'];
		$taxa_antecipacao = $res[0]['taxa_antecipacao'];
		$parcelas = $res[0]['parcelas'];
		$foto = $res[0]['foto'];
		$id = $res[0]['id'];

		$arquivo = $res[0]['foto'];

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
	$titulo_modal = 'Adicionar Bandeira Crédito';
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

					<div class="row"> <!-- inicio do grupo  -->
						<div class="col-md-8">
							<div class="mb-5">
								<label for="exampleFormControlInput1" class="form-label">Nome Bandeira</label>
								<input type="text" class="form-control" id="bandeira" name="bandeira" placeholder="Nome Bandeira" required="" value="<?php echo @$bandeira ?>">
							</div> 
						</div>

						<div class="col-md-4">
							<div class="mb-5">
								<label for="exampleFormControlInput1" class="form-label">Conta Bancaria</label>
								<input type="text" class="form-control" id="conta_bancaria" name="conta_bancaria" placeholder="Nome Conta Bancaria"  value="<?php echo @$conta_bancaria?>">
							</div> 
						</div>

					</div>  <!-- fim do grupo  -->

					<div class="row"> <!-- inicio do grupo  s2 -->
						<div class="col-md-8">
							<div class="mb-5">
								<label for="exampleFormControlInput1" class="form-label">Taxa Cartao de Crédito</label>
								<input type="text" class="form-control" id="taxa_cartao_de_credito" name="taxa_cartao_de_credito" placeholder="Taxa Cartao de Crédito"  value="<?php echo @$taxa_cartao_de_credito ?>">
							</div> 
						</div>

						<div class="col-md-8">
							<div class="mb-5">
								<label for="exampleFormControlInput1" class="form-label">Taxa de Antecipação</label>
								<input type="text" class="form-control" id="taxa_antecipacao" name="taxa_antecipacao" placeholder="Taxa de Antecipação"  value="<?php echo @$taxa_antecipacao ?>">
							</div> 
						</div>

						<div class="col-md-4">
							<div class="mb-5">
								<label for="exampleFormControlInput1" class="form-label">Prazo</label>
								<input type="text" class="form-control" id="prazo" name="prazo" placeholder="Dias"  value="<?php echo @$prazo?>">
							</div> 
						</div>

						<div class="col-md-4">
							<div class="mb-5">
								<label for="exampleFormControlInput1" class="form-label">Parcelas</label>
								<input type="text" class="form-control" id="parcelas" name="parcelas" placeholder="Parcelas"  value="<?php echo @$parcelas?>">
							</div> 
						</div>

					</div>  <!-- fim do grupo  -->



					<div class="row"> <!-- inicio do grupo  6 -->

						<div class="col-md-12">
							<div class="mb-5">
								
							<div class="col-md-4">
								<div class="form-group">
									<label >Arquivo</label>
									<input type="file" value="<?php echo @$foto ?>"  class="form-control-file" id="imagem" name="imagem" onChange="carregarImg();">
								</div>							
							</div>

							<div class="col-md-4">
								<div id="divImgConta" class="mt-4">
									<?php if(@$foto != ""){ ?>
										<img src="../img/<?php echo $pag ?>/<?php echo $arquivo_extensao2 ?>"  width="150px" id="target">
									<?php  }else{ ?>
										<img src="../img/<?php echo $pag ?>/sem-foto.jpg" width="150px" id="target">
									<?php } ?>

								</div>
							</div>

							</div> 
						</div>
						
					</div>  <!-- fim do grupo  -->



					<small><div align="center" class="mt-1" id="mensagem">
						
					</div> </small>


				</div>


				<small><div align="center" class="mt-1" id="mensagem">

					</div> </small>
				


				<div class="modal-footer">
					<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>

					<input name="id" type="hidden" value="<?php echo @$_GET['id'] ?>">

					<input name="antigo" type="hidden" value="<?php echo @$cpf ?>">
					

				</div>
			</form>
		</div>
	</div>
</div>






<div class="modal fade" tabindex="-1" id="modalDeletar" >
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Excluir Bandeira</h5>
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
		
						
		$('#imagem-registro').attr('src', '../img/bandeira_cartao_credito/' + foto);


		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {
			
		})

		myModal.show();
	}
</script>
