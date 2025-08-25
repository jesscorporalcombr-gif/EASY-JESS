<?php 
$pag = 'ver_proposta_de_venda_clientes';
@session_start();

@$txt_cli = $_POST['txt_cli'];

require_once('../conexao.php');
require_once('../conexao/conexao.php');
require_once('verificar-permissao.php');

?>


<?php  gerarMenu($pag, $grupos); ?>


</br>

	<div class="row"> <!-- inicio grupo  Mostra os totais do cliente -->
		<?php 
			// busca o nome do clientes referente desse documento
			@$id_clientes0 = $txt_cli;
			@$query_clientes0 = $pdo->query("SELECT * from clientes where id = '$id_clientes0'");
			@$res_clientes0 = $query_clientes0->fetchAll(PDO::FETCH_ASSOC);
			@$nome_clientes0 = $res_clientes0[0]['nome'];  
		?>	
		<div class="col-md-3">
			<div class="mb-3">
				 <h6>Nome:  <?php echo $nome_clientes0 ?></h6>
			</div>
		</div>
		 <hr class="dropdown-divider"> <!-- add linha -->
	</div> <!-- fim grupo  -->



<link rel="stylesheet" type="text/css" href="../vendor/login/css/util.css">
<link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css">

<!--
<div class="col-md-7">
	<div class="col-md-4">
		</br>

		 <a style="font-size: 12px;  " href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-outline-primary">Novo Contrato</a>
	</div>
</div>
 -->

<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from proposta_de_vendas where cliente = '$txt_cli' order by created desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>
					<tr>
						
						<th>Cliente</th>
						
						<th>Serviço</th>
						<th>Profissional</th>
						<th>Data</th>						
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}




						/*$extensao = strchr($res[$i]['arquivo'], '.');
						if($extensao == '.pdf'){
							$arquivo_pasta = 'pdf.png';
						}else{
							$arquivo_pasta = $res[$i]['arquivo'];
						}*/
						

						?>

						<tr>
							

							<td><?php echo $res[$i]['cliente'] ?></td>
							<td><?php echo $res[$i]['nome'] ?></td> <!--  serviço  -->
							<td><?php echo $res[$i]['id_usuario'] ?></td>
				
							<td><?php echo implode('/', array_reverse(explode('-', $res[$i]['created']))); ?></td>
							
							<!--<td>
								<a href="../img/contratos_clientes/<?php //echo $res[$i]['arquivo'] ?>" title="Ver Arquivo" style="text-decoration: none" target="_blank">
									<img src="../img/contratos_clientes/<?php echo $arquivo_pasta ?>" width="40">
								</a>
						    </td> -->
						<td>
						
								
								<!-- <a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res[$i]['id'] ?>" title="Editar Registro" style="text-decoration: none">
									<i class="bi bi-pencil-square text-primary"></i>
								</a>  

								<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res[$i]['id'] ?>" title="Excluir Registro" style="text-decoration: none">
									<i class="bi bi-archive text-danger mx-1"></i>
								</a>
	
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=baixar&id=<?php echo $res[$i]['id'] ?>" title="Baixar Registro" style="text-decoration: none">
									<i class="bi bi-check-square-fill text-success mx-1"></i>

								</a> 
							-->

							

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
	$titulo_modal = 'Editar Contrato do Cliente';
	$query = $pdo->query("SELECT * from contratos_clientes where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		$valor = $res[0]['valor'];
		$descricao = $res[0]['descricao'];
		$arquivo = $res[0]['arquivo'];
		$categoria = $res[0]['categoria'];
		$data = $res[0]['data'];
		$usuario_referente_id = $res[0]['usuario_referente_id'];



		// busca o nome do usuario atraves do id
		$id_profissional_modal = $usuario_referente_id;
		$query_profissional_modal = $pdo->query("SELECT * from clientes where id = '$id_profissional_modal' limit 1");
		$res_profissional_modal = $query_profissional_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_profissional_modal = $res_profissional_modal[0]['nome'];



		$extensao2 = strchr($arquivo, '.');
		if($extensao2 == '.pdf'){
			$arquivo_pasta2 = 'pdf.png';
		}else{
			$arquivo_pasta2 = $arquivo;
		}

	}
}else{
	$titulo_modal = 'Inserir Contrato de Cliente';
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

					<div class="col-md-12">
						<div class="mb-3">
							<label for="exampleFormControlInput1" class="form-label">Cliente Referente</label>


								<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_profissional = "SELECT * FROM clientes ORDER BY nome ASC";
				                 $sql_query_profissional = $conexao->query($sql_code_profissional) or die($conexao->error);                  
				               ?>
				                <select data-width="100%" class="form-control mr-1" id="usuario_referente_id"  
				                
				                    <?php if(isset($_GET['nome'])) echo "disabled"; ?>  name="usuario_referente_id" >
				                    
				                    <option class="form-control" 
				                    value="<?php echo @$usuario_referente_id ?>" >
				                    <?php echo @$nome_profissional_modal ?></option>

				                    <?php while($nome = $sql_query_profissional->fetch_assoc()) { ?> 

				                    <option 
				                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) echo "selected"; ?> value="<?php echo $nome['id']; ?>"><?php echo $nome['nome']; ?>
				                      
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->

						</div>
					</div>



					<div class="col-md-12">
						<div class="mb-3"><!-- Arrumar o salvamento em tabela-->
							
							<label for="exampleFormControlInput1" class="form-label">Descrição do Contrato</label>	

						<textarea type="text" class="form-control" id="descricao" name="descricao"  placeholder="Descrição" maxlength="200"><?php echo @$descricao ?></textarea>

						</div>
					</div>



					<div class="row">

						<!--
						<div class="col-md-12">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Data</label>
								<input type="date" class="form-control" id="data" name="data" value="<?php echo @$data ?>">
							</div> 
					    </div>
					-->


					<div class="col-md-6">
						<div class="mb-3">
					
							<!--<label for="exampleFormControlInput1" class="form-label">Valor</label>
							<input type="text" class="form-control" id="valor" name="valor" placeholder="Valor" value="<?php echo @$valor ?>">  -->
						</div> 
					</div>


					<!--
					<div class="col-md-6">
						<div class="mb-3">
							<label for="exampleFormControlInput1" class="form-label">Categoria</label>
							
							<select class="form-select mt-1" aria-label="Default select example" name="categoria">
								
								<option <?php if(@$categoria == '--'){ ?> selected <?php } ?>  value="--">--</option>

								<option <?php if(@$categoria == 'Diversos'){ ?> selected <?php } ?>  value="Diversos">Diversos</option>

								<option <?php if(@$categoria == 'Termos'){ ?> selected <?php } ?>  value="Termos">Termos</option>
								
								<option <?php if(@$categoria == 'Contratos'){ ?> selected <?php } ?>  value="Contratos">Contratos</option>

								<option <?php if(@$categoria == 'Fotos'){ ?> selected <?php } ?>  value="Fotos">Fotos</option>

							
							</select>
					   </div>
					</div> -->
				
				</div>
							

					
					<div class="form-group">
						<label >Arquivo do Contrato</label>
						<input type="file" value="<?php echo @$arquivo ?>"  class="form-control-file" id="imagem" name="imagem" onChange="carregarImg();">
					</div>

					<div id="divImgConta" class="mt-4">
						<?php if(@$arquivo != ""){ ?>
							<img src="../img/contratos_clientes/<?php echo @$arquivo_pasta2 ?>"  width="200px" id="target">
						<?php  }else{ ?>
							<img src="../img/contratos_clientes/sem-foto.jpg" width="200px" id="target">
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
				<h5 class="modal-title">Excluir Contrato Cliente</h5>
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
        	$('#target').attr('src', "../img/contratos_clientes/pdf.png");
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