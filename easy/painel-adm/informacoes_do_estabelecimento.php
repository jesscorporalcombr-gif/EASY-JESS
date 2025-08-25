<?php 
$pag = 'informacoes_do_estabelecimento';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
require_once('../conexao/conexao.php');

?>

<link rel="stylesheet" type="text/css" href="../vendor/login/css/util.css">
<link rel="stylesheet" type="text/css" href="../vendor/login/css/main.css">

<?php  gerarMenu($pag, $grupos); ?>
<!--
<div class="col-md-7"> 
	<div class="col-md-3">
		</br>
			<a style="font-size: 13px;  " href="index.php?pagina=<?php echo $pag ?>&funcao=novo"
			 type="button" class="login100-form-btn"></a>
	</div>
</div>


<a href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-secondary mt-2">Novo Usuário</a>
--> 

<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from  informacoes_do_estabelecimento order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table  listDataTable my-4" style="width:100%">
				<thead>
					<tr>
						<th>Nome</th>
						<th>Inscricao Estadual</th>
						<th>Razao Social</th>
						<th>CPF/CNPJ</th>
						<th>Cidade</th>
						<th>Foto</th>
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
							<td>  <?php echo $res[$i]['nome'] ?> </td>
							<td>  <?php echo $res[$i]['inscricao_estadual'] ?> </td>
							<td> <?php echo $res[$i]['razao_social'] ?> </td>
							<td>  <?php echo $res[$i]['cpf_cnpj'] ?>  </td>
							<td> <?php echo $res[$i]['cidade'] ?>  </td>

							<td><img src="../img/<?php echo $pag ?>/<?php echo $arquivo_pasta ?>" width="40"></td>
							
							<td> <!-- duplica a linha bold -->
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
	$titulo_modal = 'Editar Registro';
	$query = $pdo->query("SELECT * from informacoes_do_estabelecimento where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		$nome = $res[0]['nome'];
		$email = $res[0]['email'];
		$inscricao_estadual = $res[0]['inscricao_estadual'];
		$inscricao_municipal = $res[0]['inscricao_municipal'];
		$razao_social = $res[0]['razao_social'];
		$foto = $res[0]['foto'];
		$cpf_cnpj = $res[0]['cpf_cnpj'];
		$categoria = $res[0]['categoria'];

		@$categoria_por_genero = $res[0]['categoria_por_genero']; 
        @$categoria_por_servico = $res[0]['categoria_por_servico'];

		$estado = $res[0]['estado'];
		$cidade = $res[0]['cidade'];
		$cep = $res[0]['cep'];
		$numero = $res[0]['numero'];
		$complemento = $res[0]['complemento'];
		$bairro = $res[0]['bairro'];
		$nome_responsavel = $res[0]['nome_responsavel'];
		$celular_responsavel = $res[0]['celular_responsavel'];
		$telefone = $res[0]['telefone'];
		$endereco = $res[0]['endereco'];
		$facebook = $res[0]['facebook'];
		$complemento = $res[0]['complemento'];
		$celular_agendamento = $res[0]['celular_agendamento'];
		$site = $res[0]['site'];
		$instagram = $res[0]['instagram'];

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
	$titulo_modal = 'Inserir Colaborador';
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

					<div class="row"> <!--  grupo  -->
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Nome</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="
								Qual o nome do estabelecimento? " required="" value="<?php echo @$nome ?>">
							</div> 
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Inscricao Estadual</label>
								<input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual" placeholder="inscricao_estadual" value="<?php echo @$inscricao_estadual ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Inscricao Municipal</label>
								<input type="text" class="form-control" id="inscricao_municipal" name="inscricao_municipal" placeholder="inscricao_municipal" value="<?php echo @$inscricao_municipal ?>">
							</div>  
						</div>
					</div>	

					<div class="row"> <!--  grupo  -->

							<div class="col-md-5">
								<div class="mb-3">
									<label for="exampleFormControlInput1" class="form-label">Razao Social</label>
									<input type="text" class="form-control" id="razao_social" name="razao_social" placeholder="Qual a razão social?" value="<?php echo @$razao_social ?>">
								</div>  
							</div>

							<div class="col-md-4">
								<div class="mb-3">
									<label for="exampleFormControlInput1" class="form-label">Nome Responsavel</label>
									<input type="text" class="form-control" id="nome_responsavel" name="nome_responsavel" placeholder="nome_responsavel" value="<?php echo @$nome_responsavel ?>">
								</div>  
							</div>

							<div class="col-md-3">
								<div class="mb-3">
									<label for="exampleFormControlInput1" class="form-label">CNPJ CPF</label>
									<input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" placeholder="cpf_cnpj" value="<?php echo @$cpf_cnpj ?>">
								</div>  
							</div>

					</div>


						<div class="row">   <!--   inicio grupo -->	

							<div class="col-md-3">
								<div class="mb-3">
									<label for="exampleFormControlInput1" class="form-label">Telefone</label>
									<input type="text" class="form-control" id="telefone" name="telefone" placeholder="telefone" value="<?php echo @$telefone ?>">
								</div>  
							</div>

							<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Categoria da Empresa</label>
								
								<select class="form-select mt-1" aria-label="Default select example" name="categoria">
									
									

									<option <?php if(@$categoria == 'Barbearia '){ ?> selected <?php } ?>  value="Barbearia ">Barbearia </option>

									<option <?php if(@$categoria == 'Clinica estética'){ ?> selected <?php } ?>  value="Clinica estética">Clinica estética</option>
									
									<option <?php if(@$categoria == 'Esmalteria '){ ?> selected <?php } ?>  value="Esmalteria ">Esmalteria </option>

									<option <?php if(@$categoria == 'Estúdio de Tatuagem'){ ?> selected <?php } ?>  value="Estúdio de Tatuagem">Estúdio de Tatuagem</option>

									<option <?php if(@$categoria == 'Salão de Beleza '){ ?> selected <?php } ?>  value="Salão de Beleza ">Salão de Beleza </option>
									
									<option <?php if(@$categoria == 'Salão de Beleza infantil'){ ?> selected <?php } ?>  value="Salão de Beleza infantil">Salão de Beleza infantil</option>

									<option <?php if(@$categoria == 'SPA'){ ?> selected <?php } ?>  value="SPA">SPA</option>

								
								</select>
						   </div>
						</div>



						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Categoria por Serviço</label>
								
								<select class="form-select mt-1" aria-label="Default select example" 
								name="categoria_por_servico">
									
									

									<option <?php if(@$categoria_por_servico == 'Diversos'){ ?> selected <?php } ?>  value="Diversos">Diversos</option>

									<option <?php if(@$categoria_por_servico == 'Estetica'){ ?> selected <?php } ?>  value="Estetica">Estetica</option>
									
		

								
								</select>
						   </div>
						</div>



						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Categoria por Genero</label>
								
							<select class="form-select mt-1" aria-label="Default select example" name="categoria_por_genero">
																

									<option <?php if(@$categoria_por_genero == 'Acupuntura'){ ?> selected <?php } ?>  value="Acupuntura">Acupuntura</option>

									<option <?php if(@$categoria_por_genero == 'Alongamento de unha'){ ?> selected <?php } ?>  value="Alongamento de unha">Alongamento de unha</option>
									
									<option <?php if(@$categoria_por_genero == 'Barba'){ ?> selected <?php } ?>  value="Barba">Barba</option>

									<option <?php if(@$categoria_por_genero == 'Botox'){ ?> selected <?php } ?>  value="Botox">Botox</option>

									<option <?php if(@$categoria_por_genero == 'Botox Capilar'){ ?> selected <?php } ?>  value="Botox Capilar">Botox Capilar</option>
									
									<option <?php if(@$categoria_por_genero == 'Cachos & Afro'){ ?> selected <?php } ?>  value="Cachos & Afro">Cachos & Afro</option>

									<option <?php if(@$categoria_por_genero == 'Cílios'){ ?> selected <?php } ?>  value="Cílios">Cílios</option>

									<option <?php if(@$categoria_por_genero == 'Coloração, Luzes e Mechas'){ ?> selected <?php } ?>  value="Coloração, Luzes e Mechas">Coloração, Luzes e Mechas</option>

									<option <?php if(@$categoria_por_genero == 'Corte'){ ?> selected <?php } ?>  value="Corte">Corte</option>

									<option <?php if(@$categoria_por_genero == 'Depilação'){ ?> selected <?php } ?>  value="Depilação">Depilação</option>

									<option <?php if(@$categoria_por_genero == 'Depilação a Laser'){ ?> selected <?php } ?>  value="Depilação a Laser">Depilação a Laser</option>

									<option <?php if(@$categoria_por_genero == 'Dread'){ ?> selected <?php } ?>  value="Dread">Dread</option>

									<option <?php if(@$categoria_por_genero == 'Drenagem'){ ?> selected <?php } ?>  value="Drenagem">Drenagem</option>

									<option <?php if(@$categoria_por_genero == 'Escova'){ ?> selected <?php } ?>  value="Escova">Escova</option>

									<option <?php if(@$categoria_por_genero == 'Finalização e Penteados'){ ?> selected <?php } ?>  value="Finalização e Penteados">Finalização e Penteados</option>

									<option <?php if(@$categoria_por_genero == 'Hidratação'){ ?> selected <?php } ?>  value="Hidratação">Hidratação</option>


									<option <?php if(@$categoria_por_genero == 'Limpeza de pele'){ ?> selected <?php } ?>  value="Limpeza de pele">Limpeza de pele</option>

									<option <?php if(@$categoria_por_genero == 'Mãos & Pés'){ ?> selected <?php } ?>  value="Mãos & Pés">Mãos & Pés</option>

									<option <?php if(@$categoria_por_genero == 'Maquiagem'){ ?> selected <?php } ?>  value="Maquiagem">Maquiagem</option>

									<option <?php if(@$categoria_por_genero == 'Mega Hair'){ ?> selected <?php } ?>  value="Mega Hair">Mega Hair</option>

									<option <?php if(@$categoria_por_genero == 'Microagulhamento'){ ?> selected <?php } ?>  value="Microagulhamento">Microagulhamento</option>

									<option <?php if(@$categoria_por_genero == 'Peeling'){ ?> selected <?php } ?>  value="Peeling">Peeling</option>


									<option <?php if(@$categoria_por_genero == 'Piercing'){ ?> selected <?php } ?>  value="Piercing">Piercing</option>

									<option <?php if(@$categoria_por_genero == 'Podologia'){ ?> selected <?php } ?>  value="Podologia">Podologia</option>

									<option <?php if(@$categoria_por_genero == 'Sobrancelha'){ ?> selected <?php } ?>  value="Sobrancelha">Sobrancelha</option>

									<option <?php if(@$categoria_por_genero == 'Tatuagem'){ ?> selected <?php } ?>  value="Tatuagem">Tatuagem</option>

								
								</select>
						   </div>
						</div>
					</div>    <!--   fim grupo -->

					<div class="row">   <!--   inicio grupo -->	

						<div class="col-md-5">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Endereço</label>
								<input type="text" class="form-control" id="endereco" name="endereco" placeholder="endereco" value="<?php echo @$endereco ?>">
							</div>  
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Numero</label>
								<input type="text" class="form-control" id="numero" name="numero" placeholder="numero" value="<?php echo @$numero ?>">
							</div>  
						</div>

						<div class="col-md-2">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Complemento</label>
								<input type="text" class="form-control" id="complemento" name="complemento" placeholder="complemento" value="<?php echo @$complemento ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Bairro</label>
								<input type="text" class="form-control" id="bairro" name="bairro" placeholder="bairro" value="<?php echo @$bairro ?>">
							</div>  
						</div>


						

					</div>


					<div class="row">   <!--   inicio grupo -->							
						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Cidade</label>
								<input type="text" class="form-control" id="cidade" name="cidade" placeholder="cidade" value="<?php echo @$cidade ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Estado</label>
								<input type="text" class="form-control" id="estado" name="estado" placeholder="estado" value="<?php echo @$estado ?>">
							</div>  
						</div>						

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">CEP</label>
								<input type="text" class="form-control" id="cep" name="cep" placeholder="cep" value="<?php echo @$cep ?>">
							</div>  
						</div>

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Celular do Responsavel</label>
								<input type="text" class="form-control" id="celular_responsavel" name="celular_responsavel" placeholder="celular_responsavel" value="<?php echo @$celular_responsavel ?>">
							</div>  
						</div>
					</div>
					

					<div class="row">   <!--   inicio grupo -->	

						<div class="col-md-3">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Celular para Agendamento</label>
								<input type="text" class="form-control" id="celular_agendamento" name="celular_agendamento" placeholder="celular_agendamento" value="<?php echo @$celular_agendamento ?>">
							</div>  
						</div>

						<div class="col-md-5">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Email</label>
								<input type="text" class="form-control" id="email" name="email" placeholder="email" value="<?php echo @$email ?>">
							</div>  
						</div>

						<div class="col-md-4">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Site</label>
								<input type="text" class="form-control" id="site" name="site" placeholder="site" value="<?php echo @$site ?>">
							</div>  
						</div>
					</div>



					<div class="row">   <!--   inicio grupo -->	
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Facebook</label>
								<input type="text" class="form-control" id="facebook" name="facebook" placeholder="facebook" value="<?php echo @$facebook ?>">
							</div>  
						</div>

		
						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Instagram</label>
								<input type="text" class="form-control" id="instagram" name="instagram" placeholder="Instagram" value="<?php echo @$instagram ?>">
							</div>  
						</div>					
	
					</div>


					<small><div align="center" class="mt-1" id="mensagem">
						
					</div> </small>


					<div class="col-md-4">
							<div class="form-group">
								<label >Foto/Logo</label>
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
		
						
		$('#imagem-registro').attr('src', '../img/informacoes_do_estabelecimento/' + foto);


		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {
			
		})

		myModal.show();
	}
</script>
