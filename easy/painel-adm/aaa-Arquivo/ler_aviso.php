<?php 
$pag = 'ler_aviso';
@session_start();

//RECUPERAR DADOS DO USUÁRIO
$query = $pdo->query("SELECT * from usuarios WHERE id = '$_SESSION[id_usuario]'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_usu = $res[0]['nome'];
$email_usu = $res[0]['email'];
$senha_usu = $res[0]['senha'];
$nivel_usu = $res[0]['nivel'];
$cpf_usu = $res[0]['cpf'];
$id_usu = $res[0]['id'];

require_once('../conexao.php');
#require_once('verificar-permissao.php')

echo $nome_usu = $res[0]['nome']; 
gerarMenu($pag, $grupos);
?>


<!DOCTYPE html>
<html lang="">
<head>
  <meta charset="UTF-8">
  <title>Avisos</title>

  <style type="text/css">
  	
  	.container { text-align: center; }

  	.container { 
    width: 700px; 
    margin-left: auto;
    margin-right: auto; 
}

  </style>

</head>

</br><center><h2 class="text-center wow fadeInUp">Avisos</h2> </center>
 <hr class="dropdown-divider"></li> <!-- linha de separação -->
<!--
<a href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-secondary mt-2">Novo Aviso</a>
-->

<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from avisos order by id DESC");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>

		
 <div class="container"> 
		<small>			
			<thead>				
				<tbody>
					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}
							?>
						<center><h5>

									<tr>						
									   <td><h5 style="text-align: center; font-size: 15px; "><?php echo $res[$i]['nome'] ?></h5></td>
									</tr>

									<tr>							
									    <td><p style=" font-size: 13px; text-align: center; color: #666666;"> <?php echo $res[$i]['interesse'] ?></p></td></br>
									</tr>
								
										<?php 
											
											//RECUPERAR DADOS DO USUÁRIO

										   $id_criador = $res[$i]['criado'];
											$query2 = $pdo->query("SELECT * from usuarios WHERE id = '$id_criador'");
											
											$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
											$nome_usu2 = $res2[0]['nome'];														
										?>

									<tr>
									     <td>Criado  Por: <?php echo $nome_usu2 ?></td>

									</br></br>
									 <hr class="dropdown-divider"></li> <!-- linha de separação -->
									 <hr class="dropdown-divider"></li> <!-- linha de separação -->
								       </br>
									</tr>

						</h5></center>

					<?php } ?>

				</tbody>		
		</small>
	<?php }else{
		echo '<p>Não existem dados para serem exibidos!!';
	} ?>
</div>

</div>


<?php 

// Editar a planilha de leads
// Editar a planilha de leads

if(@$_GET['funcao'] == "editar"){
	$titulo_modal = 'Editar Registro';
	$query = $pdo->query("SELECT * from avisos where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		
		$data = $res[0]['data'];
		$nome = $res[0]['nome'];
		$interesse = $res[0]['interesse'];
		$observacoes = $res[0]['observacoes'];
		$nome_usu = $res[0]['nome']; 


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
								<label for="exampleFormControlInput1" class="form-label">Assunto</label>
								<input type="text" class="form-control" id="nome" name="nome" required placeholder="Assunto" value="<?php echo @$nome ?>">
							</div> 
						</div>
						
					</div>



					<div class="col-md-10">
							<div class="mb-5">
								<label for="exampleFormControlInput1" class="form-label">Aviso</label>
								<textarea type="text" class="form-control" id="interesse" name="interesse"  placeholder="descricao" required="" maxlength="400"><?php echo @$interesse ?></textarea>

							</div>  
					</div> 





					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								
							</div> 
						</div>

						

						<div class="mb-3"> 
						
						
					</div>




					</div>



					<small><div align="center" class="mt-1" id="mensagem">
						
					</div> </small>

				</div>
				<div class="modal-footer">
					<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>

					<input name="id" type="hidden" value="<?php echo @$_GET['id'] ?>">

					<input name="antigo" type="hidden" value="<?php echo @$WHATSAPP ?>">
					<input name="antigo2" type="hidden" value="<?php echo @$EMAIL ?>">
					<input type="hidden" name="nome_usu" value="<?=$nome_usu?>" />

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

<script src="style/js/jquery-3.5.1.min.js"></script>

<script src="style/js/bootstrap.bundle.min.js"></script>

<script src="style/vendor/owl-carousel/js/owl.carousel.min.js"></script>

<script src="style/vendor/wow/wow.min.js"></script>

<script src="style/js/theme.js"></script>

</html>