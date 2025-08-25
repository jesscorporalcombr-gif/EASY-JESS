<?php 
$pag = 'vendas';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);
$data_inicial = date('Y-m-d', strtotime('-30 days'));
$data_final = date('Y-m-d');

?>

<head>


</head>
<body>




<div class="container-md">
	<div class="col-md-6"> <!--  Botão -->
		<div class="mb-4">
			</br>
			<a  onclick= "abrirModal('modalVendas', '0', 'venda')"
			type="button" class="btn-add">
				<i class="bi bi-person-fill-add ico-add"></i>
			Nova Venda
		</a>
		</div>
	</div>
	
	
	
</div>


<script>

//document.addEventListener('DOMContentLoaded', function() {
    // ...
    
    // Supondo que a coluna/field no banco seja "status" e o valor que você quer filtrar seja "ativo"
  //  activeConditions['tipo_venda'] = 'venda';

    // Agora, quando chamar o fetchData(), ele já aplica esse filtro
    //fetchData(); // exibe somente status=ativo
//});

	</script>




<div class="container-md table-container">

	<div class="row mb-2" id="filtroData">
		<div class="col-md-2">
			<input type="date" class="form-control filtroData" id="dataInicial" value="<?= $data_inicial?>">
		</div>
		<div class="col-md-2">
			<input type="date" class="form-control filtroData" id="dataFinal" value="<?= $data_final?>">
		</div>



	</div>

	<div class="row mb-2">
		<div class="col-md-1">
			<select class ="form-select rowsPerPage" id="rowsPerPage">
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
		

		<table id="tabelaVendas" class="dataTable" data-table="venda" data-filtro='{"tipo_venda": "venda", "data_venda":"<?= $data_inicial?><-><?= $data_final?>"}'  data-tipo="venda">
			<thead>
				<tr class="data-modal" data-modal="modalVendas">
					<th data-sort="num" data-sort-init="DES" data-field="id">ID</th>
					<th  data-sort="data"  data-field="data_venda">Data</th>
					<!--<th data-sort="data" data-field="dt_cadastro">Data Cadastro</th>-->
					<th  data-classe-td="td-destacado td-vermelho" data-classe-tr="tr-avisos"  data-sort="a-z" data-field="cliente">Cliente</th>
					<th  data-sort="a-z"   data-modal="modalVendas" data-field="cpf">CPF</th>

					<th   data-sort="num" data-classe-td="numVirg2c"  data-field="valor_final">Valor</th>
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

 

</body>

<script type="text/javascript" src="js/tabelas2.js?v=0.23"></script>


<script>

const dataInicial = document.getElementById('dataInicial');
const dataFinal = document.getElementById('dataFinal');
const tabela = document.getElementById('tabelaVendas');
const filtroData = document.querySelectorAll('.filtroData'); // inputs das datas
  console.log('ouvinte ok data inicial');

filtroData.forEach(function(input) {
    input.addEventListener('change', function(e) {
	console.log('data inicial' + dataInicial.value);	
      
        tabela.setAttribute('data-filtro', '{"tipo_venda": "venda", "data_venda":"' + dataInicial.value + '<->' + dataFinal.value + '"}');
    });
});



</script>


