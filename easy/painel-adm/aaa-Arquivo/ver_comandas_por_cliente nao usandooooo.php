<?php 
$pag = 'ver_comandas_por_cliente';
@session_start();

@$txt_cli = $_POST['txt_cli']; 

require_once('../conexao.php');
require_once('verificar-permissao.php');
require_once('../conexao/conexao.php');
gerarMenu($pag, $grupos);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">

    <h4 style="font-size: 14px;  font-style:normal;" class="text-uppercase">Vendas Clientes</h4>
	&nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=documentos_clientes"
	 type="button" class="">Documentos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=comandas_por_cliente"
	 type="button" class="">Vendas</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=contratos_clientes"
	 type="button" class="">Contratos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=img_cliente"
	 type="button" class="">Imagens</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=creditos"
	 type="button" class="">Créditos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px; " href="../consultas/consulta_ana.php"
	 type="button" target="_blank" class="">Anamnese/Prontuario</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="../consultas/consultar_acompanhamento.php"
	 type="button" target="_blank" class="">Acompanhamentos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=agendar_conectado"
	 type="button" class="">Agendamentos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <!--- /////////////// inicio busca por cliente /////////////// -->   
	  <div  id="conteudoNavbarSuportado">
	      <ul class="navbar-nav mr-auto"></ul>
		      <!--- /////////////// busca por cliente/////////////// -->
		      <form class="btn-group" method="POST" action="index.php?pagina=ver_comandas_por_cliente"> <!--Classe p/ a busca-->    
			        <select style=" width: 170px; height: 25px; font-size: 12px; " class="mt-2" data-width="100%"  id="selec_cli" name="txt_cli"> <!--- Variavel p/ a busca -->         
			            <?php
			              $query = "SELECT DISTINCT cliente FROM orcamentos ORDER BY cliente asc";
			              $result = mysqli_query($conexao, $query);

			              if(mysqli_num_rows($result)){
			                while($res_1 = mysqli_fetch_array($result)){
			                 ?>                                             
			                 <option value="<?php echo $res_1['cliente']; ?>"> <!-- valor da variavel txt_cli -->

		                 	 <?php
								$id_usu = $res_1['cliente']; // recebe o id
								$query_p = $pdo->query("SELECT * from clientes where id = '$id_usu'");
								$res_p = $query_p->fetchAll(PDO::FETCH_ASSOC);
								$nome_usu = $res_p[0]['nome'];
							 ?>

			                  <?php echo $nome_usu ?> </option>  <!-- valor mostrado --> <?php      
				               }
				             }
			             ?>
			          </select> </br>

		          <button style=" width: 90px; height: 25px; font-size: 11px; " name="buttonPesquisar" class="mt-2 btn btn-outline-success" type="submit">
		          	Pesquisar
		          </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 

		     </form>
	   </div>
	  <!--- /////////////// Fim busca por cliente/////////////// -->   

</nav>
</br>


	<div class="row"> <!-- inicio grupo  Mostra os totais do cliente -->
		
		<div class="col-md-3">
			<div class="mb-3">
				 <h6>Nome:  <?php echo $txt_cli ?></h6>
			</div>
		</div>

		 <hr class="dropdown-divider"> <!-- add linha -->

	</div> <!-- fim grupo  -->


<div class="mt-4" style="margin-right:25px">
	<?php 
	$query = $pdo->query("SELECT * from orcamentos where cliente = '$txt_cli' order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 
		?>
		<small>
			<table id="example" class="table table-hover my-4" style="width:100%">
				<thead>
					<tr>					
						<th>Data Compra</th>	
						<th>ID venda</th>
						<th>Cliente</th>						
						<!--<th>Profissional</th> -->
						<th>Última Modificação</th>
						<th>Serviço </th>
						<th>Qtd</th>						
						<th>Status</th>
						<th>Situação</th>
						<th>Forma Pag</th>						
						<th>Desconto Original</th>
						<th>Valor Total</th>
						<th>Obs.</th>
						<th>Arquivo</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>

					<?php 
					for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}

						@$id_usu = $res[$i]['tecnico'];
						@$query_p = $pdo->query("SELECT * from usuarios where id = '$id_usu'");
						@$res_p = $query_p->fetchAll(PDO::FETCH_ASSOC);
						@$nome_usu = $res_p[0]['nome'];

						@$id_usu2 = $res[$i]['ultima_modificacao'];
						@$query_p2 = $pdo->query("SELECT * from usuarios where id = '$id_usu2'");
						@$res_p2 = $query_p2->fetchAll(PDO::FETCH_ASSOC);
						@$nome_ultima_modificacao = $res_p2[0]['nome'];


						@$id_produto = $res[$i]['produto'];
						@$query_produto = $pdo->query("SELECT * from servicos where id = '$id_produto'");
						@$res_produto = $query_produto->fetchAll(PDO::FETCH_ASSOC);
						@$nome_produto = $res_produto[0]['nome'];

						// busca o nome do form_pag atraves do id
						@$id_form_pag1 = $res[$i]['id_form_pag'];
						$query_forma_pgtos_modal1 = $pdo->query("SELECT * from forma_pgtos where id = '$id_form_pag1' limit 1");
						$res_forma_pgtos_modal1 = $query_forma_pgtos_modal1->fetchAll(PDO::FETCH_ASSOC);
						$nome_forma_pgtos_modal1 = $res_forma_pgtos_modal1[0]['nome'];


						$extensao = strchr($res[$i]['foto'], '.');
						if($extensao == '.pdf'){
							$arquivo_pasta = 'pdf.png';
						}else{
							$arquivo_pasta = $res[$i]['foto'];
						}					

						?>
						<tr>
							
							<!--<td><?php echo implode('/', array_reverse(explode('-', $res[$i]['criado']))); ?></td>-->
							<td ><?php echo $res[$i]['criado'] ?></td>
							<td ><?php echo $res[$i]['id'] ?></td>
							<td ><?php echo $res[$i]['cliente_nome'] ?></td>
							<!--<td><?php echo $nome_usu ?></td>-->
							<td><?php echo $nome_ultima_modificacao ?></td>
							<td><?php echo $nome_produto ?></td>
							<td><?php echo $res[$i]['qtd'] ?></td>
							<td><?php echo $res[$i]['status'] ?></td>
							<td><?php echo $res[$i]['situacao'] ?></td>
							<td><?php echo $nome_forma_pgtos_modal1 ?></td>
							<td><?php echo $res[$i]['desconto'] ?></td>
							<td><?php echo $res[$i]['valor_total'] ?></td>
							<td><?php echo $res[$i]['obs'] ?></td>

							<td>
								<a href="../img/<?php echo $pag ?>/<?php echo $res[$i]['foto'] ?>" title="Ver Arquivo" style="text-decoration: none" target="_blank">
								<img src="../img/<?php echo $pag ?>/<?php echo $arquivo_pasta ?>" width="40">
							    </a>
						   </td>

							
							
							
						<td>
							<?php if($res[$i]['criado'] != 'Sim'){ ?>
								<a href="index.php?pagina=<?php echo $pag ?>&funcao=editar&id=<?php echo $res[$i]['id'] ?>" title="Editar Registro" style="text-decoration: none">
									<i class="bi bi-pencil-square text-primary"></i>
								</a>

								<!--

								<a href="index.php?pagina=<?php echo $pag ?>&funcao=deletar&id=<?php echo $res[$i]['id'] ?>" title="Excluir Registro" style="text-decoration: none">
									<i class="bi bi-archive text-danger mx-1"></i>
								</a>


								
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
	$titulo_modal = 'Editar Comanda do Cliente';
	$query = $pdo->query("SELECT * from orcamentos where id = '$_GET[id]'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){ 

		@$valor = $res[0]['valor'];
		@$descricao = $res[0]['descricao'];
		@$foto = $res[0]['foto'];
		@$categoria = $res[0]['categoria'];
		@$data = $res[0]['data'];
		@$obs = $res[0]['obs'];
		@$valor_total = $res[0]['valor_total'];
		@$status = $res[0]['status'];
		@$produto = $res[0]['produto'];
		@$cliente_nome = $res[0]['cliente_nome'];
		@$tecnico = $res[0]['tecnico'];
		@$id_form_pag = $res[0]['id_form_pag'];
		@$qtd = $res[0]['qtd'];
		@$situacao = $res[0]['situacao'];


		// busca o nome do usuario atraves do id
		$id_profissional_modal = $tecnico;
		$query_profissional_modal = $pdo->query("SELECT * from clientes where id = '$id_profissional_modal' limit 1");
		$res_profissional_modal = $query_profissional_modal->fetchAll(PDO::FETCH_ASSOC);
		@$nome_profissional_modal = $res_profissional_modal[0]['nome'];


		// busca o nome do produto atraves do id
		$id_produto_modal = $produto;
		$query_produto_modal = $pdo->query("SELECT * from servicos where id = '$id_produto_modal' limit 1");
		$res_produto_modal = $query_produto_modal->fetchAll(PDO::FETCH_ASSOC);
		$nome_produto_modal = $res_produto_modal[0]['nome'];


		// busca o nome do form_pag atraves do id
		$query_forma_pgtos_modal = $pdo->query("SELECT * from forma_pgtos where id = '$id_form_pag' limit 1");
		$res_forma_pgtos_modal = $query_forma_pgtos_modal->fetchAll(PDO::FETCH_ASSOC);
		$nome_forma_pgtos_modal = $res_forma_pgtos_modal[0]['nome'];


		$extensao2 = strchr($foto, '.');
		if($extensao2 == '.pdf'){
			$arquivo_pasta2 = 'pdf.png';
		}else{
			$arquivo_pasta2 = $foto;
		}

	}
}else{
	$titulo_modal = 'Inserir Imagem de Cliente';
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

					<div class="col-md-6">
						<div class="mb-3">
							<div class="mb-3">		
								<label for="exampleFormControlInput1" class="form-label">Cliente</label>

								<label style="font-size: 22px;"  for="exampleFormControlInput1" class="form-label"><?php echo @$cliente_nome ?></label>

							</div> 
						</div>
					</div>

				


					<div class="row">
					    <div class="col-md-6">
							<div class="mb-3">

								<label for="exampleFormControlInput1" class="form-label">Status</label>
								<select class="form-select mt-1" aria-label="Default select example" name="status">
								
								<option <?php if(@$status == 'Pendente'){ ?> selected <?php } ?>  value="Pendente">Pendente</option>

								<option <?php if(@$status == 'Pago'){ ?> selected <?php } ?>  value="Pago">Pago</option>
								
								<option <?php if(@$status == 'Aguardando'){ ?> selected <?php } ?>  value="Aguardando">Aguardando</option>

								<option <?php if(@$status == 'Aberto'){ ?> selected <?php } ?>  value="Aberto">Aberto</option>

								<option <?php if(@$status == 'Aprovado'){ ?> selected <?php } ?>  value="Aprovado">Aprovado</option>

								<option <?php if(@$status == 'Cancelado'){ ?> selected <?php } ?>  value="Cancelado">Cancelado</option>
				
							</select>
					        </div>
					    </div>
					

					    <div class="col-md-6">
							<div class="mb-3">

								<label for="exampleFormControlInput1" class="form-label">Situação</label>
								<select class="form-select mt-1" aria-label="Default select example" name="situacao">
								
								<option <?php if(@$situacao == 'Aberta'){ ?> selected <?php } ?>  value="Aberta">Aberta</option>

								<option <?php if(@$situacao == 'Fechada'){ ?> selected <?php } ?>  value="Fechada">Fechada</option>
								
								
				
							</select>
					        </div>
					    </div>

					</div>




					<div class="col-md-12">
						<div class="mb-3">
							<div class="mb-3"></br>					
								<label for="exampleFormControlInput1" class="form-label">Serviço</label>
								
							<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_code_produtos = "SELECT * FROM servicos ORDER BY nome ASC";
				                 $sql_query_produtos = $conexao->query($sql_code_produtos) or die($conexao->error);?>

				                <select data-width="100%" class="form-control mr-1" id="produto"  
				                
				                    <?php 
				                    if(isset($_GET['nome'])) echo "disabled"; ?>  name="produto" >
				                    
				                    <option class="form-control" 
					                    value="<?php echo @$produto /*a variavel que voltará a ser salva*/ ?>" >
					                    <?php echo @$nome_produto_modal?> <!-- a que mostra na tela -->    	
					                </option>

					                    <?php while($nome = $sql_query_produtos->fetch_assoc()) { ?> 

				                    <option 
					                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) 
					                    echo "selected"; ?> 
					                    value="<?php echo $nome['id'];/*o que vai pro banco*/ ?>">
					                    <?php echo $nome['nome']; /*o que mostra no select*/?>
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->


							</div> 

						</div>
					</div>

					


					<div class="row">

					    <div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Qtd.</label>
								<input type="text" class="form-control" id="qtd" name="qtd" placeholder="Valor Total" value="<?php echo @$qtd ?>"> 
							</div> 
						</div>						

						<div class="col-md-6">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">Valor Total</label>
								<input type="text" class="form-control" id="valor_total" name="valor_total" placeholder="Valor Total" value="<?php echo @$valor_total ?>"> 
							</div> 
						</div>

				    </div>

				 

				    <div class="col-md-12">
						<div class="mb-3">
							<div class="mb-3"></br>					
								<label for="exampleFormControlInput1" class="form-label">Forma de Pagamento</label>
								
							<!-- Busca do banco de dados para uma  select. Escolha dinamica -->
				              <?php
				                 $sql_forma_pgtos = "SELECT * FROM forma_pgtos ORDER BY nome ASC";
				                 $sql_forma_pgtos2 = $conexao->query($sql_forma_pgtos) or die($conexao->error);?>

				                <select data-width="100%" class="form-control mr-1" id="id_form_pag"  
				                
				                    <?php 
				                    if(isset($_GET['nome'])) echo "disabled"; ?>  name="id_form_pag" >
				                    
				                    <option class="form-control" 
					                    value="<?php echo @$id_form_pag /*a variavel que voltará a ser salva*/ ?>" >
					                    <?php echo @$nome_forma_pgtos_modal?> <!-- a que mostra na tela -->    	
					                </option>

					                    <?php while($nome = $sql_forma_pgtos2->fetch_assoc()) { ?> 

				                    <option 
					                    <?php if(isset($_GET['nome']) && $_GET['nome'] == $nome['id']) 
					                    echo "selected"; ?> 
					                    value="<?php echo $nome['id'];/*o que vai pro banco*/ ?>">
					                    <?php echo $nome['nome']; /*o que mostra no select*/?>
				                    </option>
				                    <?php } ?>
				                </select>
				                <!-- Busca do banco de dados para uma  select. Escolha dinamica -->


							</div> 

						</div>
					</div>



				    <div class="col-md-12">
						<div class="mb-3">
							
						<label for="exampleFormControlInput1" class="form-label">Obs.</label>	
						<textarea type="text" class="form-control" id="obs" name="obs"  placeholder="obs" maxlength="200"><?php echo @$obs ?></textarea>

						</div>
					</div>
											
					<div class="form-group">
						<label >Arquivo</label>
						<input type="file" value="<?php echo @$foto ?>"  class="form-control-file" id="imagem" name="imagem" onChange="carregarImg();">
					</div>

					<div id="divImgConta" class="mt-4">
						<?php if(@$arquivo != ""){ ?>
							<img src="../img/<?php echo $pag ?>/<?php echo @$arquivo_pasta2 ?>"  width="200px" id="target">
						<?php  }else{ ?>
							<img src="../img/<?php echo $pag ?>/sem-foto.jpg" width="200px" id="target">
						<?php } ?>
					</div>
					

					<small><div align="center" class="mt-1" id="mensagem">

					</div> </small>

				</div>
				<div class="modal-footer">
					<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>

					<input name="id" type="hidden" value="<?php echo @$_GET['id'] ?>">
					<input name="id" type="hidden" value="<?php echo @$cliente_nome ?>">

					

				</div>
			</form>
		</div>
	</div>
</div>






<div class="modal fade" tabindex="-1" id="modalDeletar" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Excluir Imagem Cliente</h5>
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
        	$('#target').attr('src', "../img/comanda/pdf.png");
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