<?php 
$pag = 'cores_agendamento';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
require_once('../conexao/conexao.php');

?>

<link rel="stylesheet" type="text/css" href="../vendor/login/css/util.css">
<link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css">


<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from  cores_agendamento order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>
					<tr>
						<th>Confirmado</th>
						<th>Concluido</th>
						<th>Pago</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}
							?>				

							<tr>
							<td>  <?php echo $res[$i]['cor_confirmado'] ?> </td>
							<td>  <?php echo $res[$i]['cor_concluido'] ?> </td>
							<td>  <?php echo $res[$i]['cor_pago'] ?> </td>
							<td>
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res[$i]['id'] ?>" title="Editar Registro">
									<i class="bi bi-pencil-square text-primary"></i>
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
	$titulo_modal = 'Editar Cores';
	$query = $pdo->query("SELECT * from cores_agendamento where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 

		$cor_confirmado = $res[0]['cor_confirmado'];
		$cor_em_espera = $res[0]['cor_em_espera'];
		$cor_em_andamento = $res[0]['cor_em_andamento'];
		$cor_inativo = $res[0]['cor_inativo'];
		$cor_faltou = $res[0]['cor_faltou'];
		$cor_concluido = $res[0]['cor_concluido'];
		$cor_cancelado = $res[0]['cor_cancelado'];
		$cor_pago = $res[0]['cor_pago'];
		$cor_branco = $res[0]['cor_branco'];

		$cor_intervalo = $res[0]['cor_intervalo'];
		$cor_bloqueado = $res[0]['cor_bloqueado'];

		$modificado = $res[0]['modificado'];
		$modificado_por = $res[0]['modificado_por'];



		
				

	}
}else{
	$titulo_modal = 'Inserir Colaborador';
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


					<div class="row"> <!--  grupo  -->

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Confirmado</label>
								<input type="color" class="form-control" id="cor_confirmado" name="cor_confirmado" placeholder="cor_confirmado" value="<?php echo @$cor_confirmado ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Em Espera</label>
								<input type="color" class="form-control" id="cor_em_espera" name="cor_em_espera" placeholder="cor_em_espera" value="<?php echo @$cor_em_espera ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Em Andamento</label>
								<input type="color" class="form-control" id="cor_em_andamento" name="cor_em_andamento" placeholder="cor_em_andamento" value="<?php echo @$cor_em_andamento ?>">
							</div>  
						</div>


					</div>	



					<div class="row"> <!--  grupo  -->

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Inativo</label>
								<input type="color" class="form-control" id="cor_inativo" name="cor_inativo" placeholder="cor_inativo" value="<?php echo @$cor_inativo ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Faltou</label>
								<input type="color" class="form-control" id="cor_faltou" name="cor_faltou" placeholder="cor_faltou" value="<?php echo @$cor_faltou ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Concluido</label>
								<input type="color" class="form-control" id="cor_concluido" name="cor_concluido" placeholder="cor_concluido" value="<?php echo @$cor_concluido ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Intervalo</label>
								<input type="color" class="form-control" id="cor_intervalo" name="cor_intervalo" placeholder="cor_intervalo" value="<?php echo @$cor_intervalo ?>">
							</div>  
						</div>


					</div>	


					<div class="row"> <!--  grupo  -->

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Cancelado</label>
								<input type="color" class="form-control" id="cor_cancelado" name="cor_cancelado" placeholder="cor_cancelado" value="<?php echo @$cor_cancelado ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Branco</label>
								<input type="color" class="form-control" id="cor_branco" name="cor_branco" placeholder="cor_branco" value="<?php echo @$cor_branco ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Pago</label>
								<input type="color" class="form-control" id="cor_pago" name="cor_pago" placeholder="cor_pago" value="<?php echo @$cor_pago ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Bloqueado</label>
								<input type="color" class="form-control" id="cor_bloqueado" name="cor_bloqueado" placeholder="cor_bloqueado" value="<?php echo @$cor_bloqueado ?>">
							</div>  	
						</div>


					</div>	


					



				</div>

				


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
				<h5 class="modal-title">Excluir Colaborador</h5>
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
	<div class="modal-dialog modal-xl">
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
		
						
		$('#imagem-registro').attr('src', '../img/cores_agendamento/' + foto);


		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {
			
		})

		myModal.show();
	}
</script>
