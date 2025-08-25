<?php 
$pag = 'ver_cliente';

$id_cliente = $_GET['id'];

require_once('../conexao.php');

gerarMenu($pag, $grupos);

	if(true){

	$query = $pdo->query("SELECT * from clientes where id = '$id_cliente'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);

	if($total_reg > 0){ 
		$nome = $res[0]['nome'];
		$email = $res[0]['email'];
		$cpf = $res[0]['cpf'];
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
		$foto = $res[0]['foto'];

		$query2 = $pdo->query("SELECT * from clientes where cpf = '$cpf'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);

		$id_cliente = $res2[0]['id'];

		?>




<nav class="navbar navbar-expand-lg navbar-light bg-light">

	<h4 style="font-size: 14px;  font-style:normal;" class="text-uppercase">Dados do Cliente</h4>
	&nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=documentos_clientes"
	 type="button" class="">Documentos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href=""
	 type="button" class="">Agendamentos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=abrir_orcamentos"
	 type="button" class="">Comandas</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	  <a style="font-size: 13px;  " href="index.php?pagina=contratos_clientes"
	 type="button" class="">Contratos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href="index.php?pagina=img_cliente"
	 type="button" class="">Imagens</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href=""
	 type="button" class="">Créditos</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px; " href="../consultas/consultar_por_cliente.php?id=<?php echo $res2[0]['id'];?>"
	 type="button" target="_blank" class="">Anamnese/Prontuario</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;

	 <a style="font-size: 13px;  " href=""
	 type="button" class="">Compras</a>
	 &nbsp;&nbsp;&nbsp;&nbsp;


</nav>

<?php
	}
}else{
	echo 'nada';
}
?>

<div class="col-md-7"> <!--  Botão  roxo e rosa -->
	<div class="col-md-4">
		</br>
		
		 <a style="font-size: 12px;  " href="index.php?pagina=clientes" type="button" class="btn btn-outline-primary">Colsultar Cliente</a> 
		 </br></br>

	</div>
</div>

<!--
<a href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn btn-secondary mt-2">Novo Produto</a>-->
	

<div style="width: 100vw;
height: 100vh;
display: flex;
flex-direction: row;
justify-content: center;
align-items: center;" class=""  id="" >
	<div class="">
		<div class="">
			<small>			
			<thead>				
				<tbody>
			


									<h4 class=""><?php echo @$nome ?></h4><br>
									<h5>

									<label for="" class="form-label">RG: <?php echo @$rg ?></label>
									<label for="" class="form-label">CPF: <?php echo @$cpf ?></label>

									<label for="" class="form-label">Email: <?php echo @$email ?></label>							

									<label for="" class="form-label">Aniversario: <?php echo @$aniversario ?></label>
									<label for="" class="form-label">Telefone: <?php echo @$telefone ?></label>
									<label for="" class="form-label">Celular: <?php echo @$celular ?></label>

									<label for="" class="form-label">Sexo: <?php echo @$sexo ?></label>
									<label for="" class="form-label">Como_conheceu: <?php echo @$como_conheceu ?></label>

									<label for="" class="form-label">Cep: <?php echo @$cep ?></label>
									<label for="" class="form-label">Endereco: <?php echo @$endereco ?></label>

									<label for="" class="form-label">Numero: <?php echo @$numero ?></label>
									<label for="" class="form-label">Estado: <?php echo @$estado ?></label>

									<label for="" class="form-label">Cidade: <?php echo @$cidade ?></label>
									<label for="" class="form-label">Bairro: <?php echo @$bairro ?></label>
									<label for="" class="form-label">Complemento: <?php echo @$complemento ?></label>

									<label for="" class="form-label">Profissao: <?php echo @$profissao ?></label>
									<label for="" class="form-label">Nivel: <?php echo @$nivel ?></label>
									<label for="" class="form-label">Cadastrado: <?php echo @$cadastrado ?></label>
								</h5>
								

								 

					</div>
				</div>
			</div>

		</div>
	</div>
</div>





