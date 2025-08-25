<?php 
$pag = 'aniversario_cliente';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');

/*salva a data do dia*/
$data_vigente1 = date('d-m-Y');
$data_vigente = implode('/', explode('-', $data_vigente1));

$data_vigente2 = implode('-', array_reverse(explode('-', $data_vigente1)));


?>



<link rel="stylesheet" type="text/css" href="../vendor/login/css/util.css">
<link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css">

<?php  gerarMenu($pag, $grupos); ?>

<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from clientes where aniversario = '$data_vigente2' order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>
					<tr>
						<th>Aniversario</th>
						<th>Nome</th>
						<th>CPF</th>
						<th>Email</th>
						<!--<th>Senha</th>-->
						<th>Foto</th>
						<th></th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}
							?>
					
							<tr>
							<td> <h6 style="font-size: 14px;  "> <?php echo $res[$i]['aniversario'] ?> </h6> </td>
							<td> <h6 style="font-size: 14px;  "> <?php echo $res[$i]['nome'] ?> </h6> </td>
							<td> <h6 style="font-size: 12px;  "> <?php echo $res[$i]['cpf'] ?> </h6> </td>
							<td> <h6 style="font-size: 12px;  "><?php echo $res[$i]['email'] ?> </h6> </td>
							<!--<td <h6 style="font-size: 12px;  "> >   <?php /*echo $res[$i]['senha'] */  ?> </h6> </td>-->
							
							<td><img src="../img/clientes/<?php echo $res[$i]['foto'] ?>" width="40"></td>
							
							<td>
							<td>



								<a href="index.php?pagina=ver_cliente&id=<?php echo $res[$i]['id']?> " title="Ver Registro">
									<i class="bi bi-clipboard-check text-dark mx-1"></i>
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
	$titulo_modal = 'Editar Cliente';
	$query = $pdo->query("SELECT * from clientes where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		$nome = $res[0]['nome'];
		$email = $res[0]['email'];
		$cpf = $res[0]['cpf'];
		$senha = $res[0]['senha'];
		$nivel = $res[0]['nivel'];

		$aniversario = $res[0]['aniversario'];
		$telefone = $res[0]['telefone'];
		$celular = $res[0]['celular'];
		$sexo = $res[0]['sexo'];
		$como_conheceu = $res[0]['como_conheceu'];
		$cep = $res[0]['cep'];
		$endereco = $res[0]['endereco'];
		$numero = $res[0]['numero'];
		$estado = $res[0]['estado'];
		$cidade = $res[0]['cidade'];
		$bairro = $res[0]['bairro'];
		$profissao = $res[0]['profissao'];
		$cadastrado = $res[0]['cadastrado'];
		$obs = $res[0]['obs'];
		$rg = $res[0]['rg'];
		$complemento = $res[0]['complemento'];

		$foto_edit = $res[0]['foto']; // mostra a foto no editar

	}
}else{
	$titulo_modal = 'Inserir Cliente';
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
									<label for="exampleFormControlInput1" class="form-label">Nome</label>
									<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required="" value="<?php echo @$nome ?>">
								</div> 
							</div>

							<div class="col-md-3">
								<div class="mb-3">
									<label for="exampleFormControlInput1" class="form-label">CPF</label>
									<input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF" required="" value="<?php echo @$cpf ?>">
								</div>  
							</div>

							<div class="col-md-3">
								<div class="mb-3">
									<label for="exampleFormControlInput1" class="form-label">RG</label>
									<input type="text" class="form-control" id="rg" name="rg" placeholder="RG"  value="<?php echo @$rg ?>">
						    	</div>
						    </div>


					</div>

					


					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Telefone</label>
								<input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone"  value="<?php echo @$telefone ?>">
							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Celular</label>
								<input type="text" class="form-control" id="celular" name="celular" placeholder="Celular"  value="<?php echo @$celular ?>">
							</div>  
						</div>						
					</div>




					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Email</label>
							<input type="email" class="form-control" id="email" name="email" placeholder="Email"  value="<?php echo @$email ?>">
							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Senha</label>
							<input type="text" class="form-control" id="senha" name="senha" placeholder="Senha"  value="<?php echo @$senha ?>">
							</div>  
						</div>						
					</div>




					<div class="row">
						<div class="col-md-4">
							<div class="mb-3">

								<label for="exampleFormControlInput1" class="form-label">Aniversario</label>
								<input type="date" class="form-control" id="aniversario" name="aniversario" placeholder="Aniversario " value="<?php echo @$aniversario ?>">


							</div> 
						</div>

						<div class="col-md-4">
							<div class="mb-3">

								<label for="exampleFormControlInput1" class="form-label">Genero</label>															
								<select class="form-select mt-1" aria-label="Default select example" name="sexo">
									
									<option <?php if(@$sexo == 'Feminino'){ ?> selected <?php } ?>  value="Feminino">Feminino</option>

									<option <?php if(@$sexo == 'Masculino'){ ?> selected <?php } ?>  value="Masculino">Masculino</option>
									
									<option <?php if(@$sexo == 'Não Informado'){ ?> selected <?php } ?>  value="Não Informado">Não Informado</option>									
									
								</select>


							</div>  
						</div>	

						<div class="col-md-4">
							<div class="mb-3">
				
								<label for="exampleFormControlInput1" class="form-label">Data Cadasto </label>
								<input type="date" class="form-control" id="cadastrado" name="cadastrado" placeholder="Data Cadasto " value="<?php echo @$cadastrado ?>">

							</div>  
						</div>	


					</div>


					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Profissão</label>
								<input type="text" class="form-control" id="profissao" name="profissao" placeholder="Profissão"  value="<?php echo @$profissao ?>">
							</div>
						</div>

						<div class="col-md-6">
							
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Como Conheceu</label>
								<input type="text" class="form-control" id="como_conheceu" name="como_conheceu" placeholder="Como Conheceu"  value="<?php echo @$como_conheceu ?>">
							</div>  
						</div>						
					</div>


					<hr class="dropdown-divider"></li> <!-- linha de separação -->
					<hr class="dropdown-divider"></li> <!-- linha de separação -->  


					<div class="row">
						<div class="col-md-9">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Endereço</label>
								<input type="text" class="form-control" id="endereco" name="endereco" placeholder="Endereçso"  value="<?php echo @$endereco ?>">
							</div>
						</div>

						<div class="col-md-3">
							
							<div class="mb-2">
								<label for="exampleFormControlInput1" class="form-label">Numero</label>
								<input type="text" class="form-control" id="numero" name="numero" placeholder="Num."  value="<?php echo @$numero ?>">
							</div>  
						</div>		

								
					</div>

					<div class="row">

						<div class="col-md-4">
							
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Complemento</label>
								<input type="text" class="form-control" id="complemento" name="complemento" placeholder="complemento"  value="<?php echo @$complemento ?>">
							</div>  
						</div>	

						<div class="col-md-4">
							
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Bairro</label>
								<input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro"  value="<?php echo @$bairro ?>">
							</div>  
							
						</div>

						<div class="col-md-4">

							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Cidade</label>
								<input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade"  value="<?php echo @$cidade ?>">
							</div>
							
							
						</div>						
					</div>

					<div class="row">
						<div class="col-md-6">
							

							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Estado</label>
								<input type="text" class="form-control" id="estado" name="estado" placeholder="Estado"  value="<?php echo @$estado ?>">
							</div>
						</div>

						<div class="col-md-6">
							
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Cep</label>
								<input type="text" class="form-control" id="cep" name="cep" placeholder="Cep"  value="<?php echo @$cep ?>">
							</div>  
						</div>						
					</div>

					<div class="row">
						

						<div class="col-md-15">
													
							<div class="mb-5">

								<label for="exampleFormControlInput1" class="form-label">Observações</label>																
								<textarea type="text" class="form-control" id="obs" name="obs"  placeholder="Observações"  maxlength="200"><?php echo @$obs ?></textarea>

							</div>
						</div>						
					</div>


					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Nível</label>
						<select class="form-select mt-1" aria-label="Default select example" name="nivel">
							
							<option <?php if(@$nivel == 'Cliente'){ ?> selected <?php } ?>  value="Cliente">Cliente</option>

														
						</select>
					</div> 

					

					<small><div align="center" class="mt-1" id="mensagem">
						
					</div> </small>

						<div class="col-md-4">
							<div class="form-group">
								<label >Foto</label>
								<input type="file" value="<?php echo @$foto_edit ?>"  class="form-control-file" id="imagem" name="imagem" onChange="carregarImg();">
							</div>							
						</div>

						<div class="col-md-4">
							<div id="divImgConta" class="mt-4">
								<?php if(@$foto_edit != ""){ ?>
									<img src="../img/clientes/<?php echo $foto_edit ?>"  width="150px" id="target">
								<?php  }else{ ?>
									<img src="../img/clientes/sem-foto.jpg" width="150px" id="target">
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
	<div class="modal-dialog modal-lg">
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
		
						
		$('#imagem-registro').attr('src', '../img/clientes/' + foto);


		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {
			
		})

		myModal.show();
	}
</script>
