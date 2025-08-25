<?php 
$pag = 'leads';
@session_start();

//RECUPERAR DADOS DO USUÁRIO logado
$query = $pdo->query("SELECT * from usuarios WHERE id = '$_SESSION[id_usuario]'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_usu_l = $res[0]['nome'];
$email_usu = $res[0]['email'];
$senha_usu = $res[0]['senha'];
$nivel_usu = $res[0]['nivel'];
$cpf_usu = $res[0]['cpf'];
$id_usu = $res[0]['id'];

require_once('../conexao.php');
#require_once('verificar-permissao.php')

//echo $nome_usu = $res[0]['nome']; 

?>

<h2>Leads</h2>

	<div class="col-md-7"> <!--  Botão  -->
			<div class="col-md-4">
				</br>

			<a style="font-size: 12px;  " href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-outline-primary">Novo Lead</a>

			</div>
	</div>


<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from leads order by id DESC");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>

		

		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>

					<tr>
						<th>Status</th>
						<th>Data Cad.</th>
						<th>Nome</th>
						<th>Whatsapp</th>
						<th>Email</th>
						<th>Interesse</th>
						<th>Agendou</th>
						<th>Qualificação</th>
						<th>Quem Cadastrou</th>
						<!-- <th>DATA LEMBRETE <i class="bi bi-alarm text-danger mx-1"></i></th> -->
						<th>Ações</th>
					</tr>

				</thead>
				<tbody>

					<?php 


					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}


						if($res[$i]['status'] == 'Concluída'){
							$classe = 'text-success';
						}
						elseif($res[$i]['status'] == 'Perdido'){					
							$classe = 'text-danger';
						}
						else{
							$classe = 'text-secondary';
						}

							?>

						<tr>
							<td>
								<i class="bi bi-square-fill <?php echo $classe ?>"></i>
								</td>

							<td><?php echo $res[$i]['data'] ?></td>
							<td><?php echo $res[$i]['nome'] ?></td>
							<td><?php echo $res[$i]['whatsapp'] ?></td>
							<td><?php echo $res[$i]['email'] ?></td>
							<td><?php echo $res[$i]['interesse'] ?></td>
							<td><?php echo $res[$i]['agendou'] ?></td>
							<td><?php echo $res[$i]['qualificacao_do_lead'] ?></td>
							<td><?php echo $res[$i]['nome_usu'] ?> </td>
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

// Editar a planilha de leads
// Editar a planilha de leads

if(@$_GET['funcao'] == "editar"){
	$titulo_modal = 'Editar Registro';
	$query = $pdo->query("SELECT * from leads where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		
		$data = $res[0]['data'];
		$id_cliente = $res[0]['id'];
		$nome = $res[0]['nome'];
		$whatsapp = $res[0]['whatsapp'];
		$email = $res[0]['email'];
		$origem = $res[0]['origem'];
		$id_protocolo = $res[0]['id_protocolo'];
		$interesse = $res[0]['interesse'];
		$qualificacao_do_lead = $res[0]['qualificacao_do_lead'];
		$agendou = $res[0]['agendou'];
		$status = $res[0]['status'];	
		$compareceu = $res[0]['compareceu'];
		$fechou = $res[0]['fechou'];
		$data_do_fechamento = $res[0]['data_do_fechamento'];
		$valor = $res[0]['valor'];
		$observacoes = $res[0]['observacoes'];
		$data_l = $res[0]['data_l'];
		$nome_usu = $res[0]['nome'];
		$modificado_por = $res[0]['modificado_por']; 


	}
}else{
	$titulo_modal = 'Inserir Registro';
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
								<label for="exampleFormControlInput1" class="form-label">Nome (Obrigatorio)</label>
								<input type="text" class="form-control" id="nome" name="nome" required placeholder="Nome" value="<?php echo @$nome ?>">
							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Whatsapp (Obrigatorio)</label>
								<input type="text" class="form-control" id="whatsapp" name="whatsapp" required placeholder="Whatsapp"  value="<?php echo @$whatsapp ?>">
							</div>  
						</div>
					</div>

					


					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Email</label>
						
							<!--	<input type="email" class="form-control" id="email" name="email" placeholder="Email" placeholder="sophie@example.com" value="<?php echo @$email ?>"> -->
				    
							    <input type="email" id="email" class="form-control" name="email" placeholder="sophie@example.com" pattern=+@globex\.com size="30"  value="<?php echo @$email ?>">
							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">

								<label for="exampleFormControlInput1" class="form-label">Como Conheceu (Obrigatorio)</label>
								

						<select class="form-select mt-1" aria-label="Default select example" name="origem">
							
							<option <?php if(@$origem == 'Facebook'){ ?> selected <?php } ?>  value="Facebook">Facebook</option>

							<option <?php if(@$origem == 'Instagram'){ ?> selected <?php } ?>  value="Instagram">Instagram</option>	

							<option <?php if(@$origem == 'Indicação'){ ?> selected <?php } ?>  value="Indicação">Indicação</option>

							<option <?php if(@$origem == 'Google'){ ?> selected <?php } ?>  value="Google">Google</option>

							<option <?php if(@$origem == 'Outro'){ ?> selected <?php } ?>  value="Outro">Outro</option>							
																				
						</select>
							
							</div>  
						</div>
					</div>




					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">

								<label for="exampleFormControlInput1" class="form-label">Data do Lembrete</label>
								<input type="date" class="form-control" id="data_l" name="data_l" placeholder="Data do Lembrete" value="<?php echo @$data_l ?>">

							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Qualificação?</label>
						<select class="form-select mt-1" aria-label="Default select example" name="qualificacao_do_lead">
							
							<option <?php if(@$qualificacao_do_lead == 'Quente'){ ?> selected <?php } ?>  value="Quente">Quente</option>

							<option <?php if(@$qualificacao_do_lead == 'Morno'){ ?> selected <?php } ?>  value="Morno">Morno</option>	

							<option <?php if(@$qualificacao_do_lead == 'Frio'){ ?> selected <?php } ?>  value="Frio">Frio</option>							
																				
						</select>
							</div>  
						</div>
					</div>

					<hr />

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Agendou?</label>
						<select class="form-select mt-1" aria-label="Default select example" name="agendou">
							
							<option <?php if(@$agendou == 'Não'){ ?> selected <?php } ?>  value="Não">Não</option>

							<option <?php if(@$agendou == 'Sim'){ ?> selected <?php } ?>  value="Sim">Sim</option>	

							<option <?php if(@$agendou == 'Aguardando'){ ?> selected <?php } ?>  value="Aguardando">Aguardando</option>							
																				
						</select>
							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Compareceu?</label>
								<select class="form-select mt-1" aria-label="Default select example" name="compareceu">
									
									<option <?php if(@$compareceu == 'Não'){ ?> selected <?php } ?>  value="Não">Não</option>

									<option <?php if(@$compareceu == 'Sim'){ ?> selected <?php } ?>  value="Sim">Sim</option>

									<option <?php if(@$compareceu == 'Aguardando'){ ?> selected <?php } ?>  value="Aguardando">Aguardando</option>						
																
								</select>
							</div>  
						</div>
					</div>


					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Fechou?</label>
						<select class="form-select mt-1" aria-label="Default select example" name="fechou">
							
							<option <?php if(@$fechou == 'Não'){ ?> selected <?php } ?>  value="Não">Não</option>

							<option <?php if(@$fechou == 'Sim'){ ?> selected <?php } ?>  value="Sim">Sim</option>
														
						</select>
							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Status</label>
						<select class="form-select mt-1" aria-label="Default select example" name="status">
							
							<option <?php if(@$status == '-'){ ?> selected <?php } ?>  value="">-</option>

							<option <?php if(@$status == 'Concluída'){ ?> selected <?php } ?>  value="Concluída">
								<i class="bi bi-square-fill text-success"></i> Concluída</option>

							<option <?php if(@$status == 'Andamento'){ ?> selected <?php } ?>  value="Andamento">
								<i class="bi bi-square-fill text-secondary"></i> Andamento</option>	

							<option <?php if(@$status == 'Perdido'){ ?> selected <?php } ?>  value="Perdido">
								<i class="bi bi-square-fill text-danger"></i> Perdido</option>							
																				
						</select>
							</div>  
						</div>
					</div>





					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Data do Fechamento</label>
								<input type="date" class="form-control" id="data_do_fechamento" name="data_do_fechamento" placeholder="Data do Fechamento" value="<?php echo @$data_do_fechamento ?>">
							</div> 
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Valor</label>
								<input type="text" class="form-control" id="valor" name="valor" placeholder="R$"  value="<?php echo @$valor ?>">
							</div>  
						</div>
					</div>


				

					   <div class="col-md-12">
							<div class="mb-10">

								<label for="exampleFormControlInput1" class="form-label">Interesse</label>
						    <input type="INTERESSE" class="form-control" id="interesse" name="interesse" placeholder="Interesse" value="<?php echo @$interesse ?>">


							</div> 
						</div>

						<div class="col-md-12">
							<div class="mb-10">
								<label for="exampleFormControlInput1" class="form-label">Observações</label>
								<textarea type="text" class="form-control" id="observacoes" name="observacoes" placeholder="observacoes"  maxlength="7000"><?php echo @$observacoes ?></textarea>
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
					<input type="hidden" name="nome_usu_l" value="<?=$nome_usu_l?>" />

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


