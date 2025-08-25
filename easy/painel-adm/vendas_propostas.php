<?php 
$pag = 'vendas_propostas';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);

?>

<head>


</head>
<body>




<div class="container-md">
	<div class="col-md-6"> <!--  Botão -->
		<div class="mb-4">
			</br>
			<a  onclick= "abrirModal('modalVendas', '0', 'proposta')"
			type="button" class="btn-add"><i class="bi bi-person-fill-add ico-add"></i>Nova Proposta</a>
		</div>
	</div>
	
	
	
</div>


<script>

//document.addEventListener('DOMContentLoaded', function() {
    // ...
    
    // Supondo que a coluna/field no banco seja "status" e o valor que você quer filtrar seja "ativo"
  //  activeConditions['tipo_venda'] = 'proposta';

    // Agora, quando chamar o fetchData(), ele já aplica esse filtro
    //fetchData(); // exibe somente status=ativo
//});

	</script>




<div class="container-md table-container">

	<div class="row mb-2">
		<div class="col-md-1">
			<select class ="form-select rowsPerPage" id="rowsPerPage" >
				<option value="10">10</option>
				<option value="25">50</option>
				<option value="100">100</option>
				<option value="500">500</option>
			</select>
		</div>

		<div class="col-md-7">
			<!-- espaço vazio-->
		</div>

		<div class="col-md-4">
			<input type="text" class="form-control searchBox" id="searchBox" placeholder="Pesquisar...">
		</div>
	</div>

	<div class="row" >
		<div class="mb-4">
		

		<table id="dataTableee" class="dataTable" data-table="venda" data-filtro='{"tipo_venda": "proposta"}'  data-tipo="proposta">
			<thead>
				<tr class="data-modal" data-modal="modalVendas">
					<th  data-sort-init="DES"  data-sort="num" data-field="id">ID</th>
					<!--<th data-sort="data" data-field="dt_cadastro">Data Cadastro</th>-->
					<th  data-classe-td="td-destacado td-vermelho" data-classe-tr="tr-avisos"  data-sort="a-z" data-field="cliente">Cliente</th>
					<th  data-sort="a-z"   data-modal="modalVendas" data-field="cpf">CPF</th>
					<th  data-sort="data" data-field="dataHora_criacao">Data</th>
					<th   data-sort="num" data-classe-td="numVirg2c"  data-field="valor_final">Valor</th>
					<th   data-sort="data" data-field="validade">Validade</th>
					<th   data-sort="a-z"  data-field="vendedor_nome">Vendedor</th>
					<th   data-sort="a-z" >Status</th>
					<!-- Adicionar mais colunas conforme necessário -->
				</tr>
			</thead>

			<tbody>
				<!-- As linhas vao ser inseridas aqui dinamicamente -->
			</tbody>
			</table>
		</div>
	</div>

	<div class="row mb-8">
		
		<div class="col-md-6 pagination" id="pagination">
			<!-- Botões de navegação serão inseridos aqui -->
		</div>

		<div id="info-range" class="col-md-6 info-range" style="margin-bottom: 10px; text-align:right;">
			Exibindo de 0 a 0 de um total de 0 registros
		</div>
	</div>
</div>

 <script type="text/javascript" src="js/tabelas2.js?v=0.23"></script>

</body>



