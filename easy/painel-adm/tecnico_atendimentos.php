<?php 
$pag = 'tecnico_atendimentos';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);

$data_inicial = date('Y-m-d', strtotime('-10 days'));
$data_final = date('Y-m-d');

?>
<style>

.label {
  display: inline-block;
  padding: 2px 6px;
  border-radius: 4px;
  background: #f0f0f0;
  color: #333;
  font-size: 0.8em;
}

/* aniversário hoje */
.label-hoje {
  background:rgb(62, 154, 65);
  color:rgb(255, 255, 255);
  font-weight: 500;
}

.label-hoje:hover{
  background:rgb(255, 255, 255);
  color:rgb(5, 76, 15);
	
}

/* aniversário amanhã */
.label-amanha {
  background:rgb(23, 89, 202);
  color:rgb(255, 255, 255);
  font-weight: 500;
}

.status-Prof {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 6px;
  font-size: 0.85em;
  color: #fff;
}


.nao-agendado{
	display: inline-block;
	background: #d32f2f;
	color: #f0f0f0;
	border-radius: 6px;
	font-size: 0.7em;
	padding:2px 15px;
}






/*----------    style para o modal -----------------------*/

/* cores sugeridas para cada situação */
.status-finalizado           { background: #4caf50; } /* verde */
.status-concluido            { background: #10409bff; } /* vermelho */
.status-atendimento          { background: #dca628ff; } /* vermelho */




/* deixa só o texto em vertical */


</style>


<body>

   
	<div class="container-md listTabContainer mt-3">
		<h1> Atendimentos </h1>
		<div class="table-container" id="agendamentos-container-modal">
			<div class="row mb-2">
                    <div class="col-auto mb-3 position-relative">
                        <label class="form-group" for="filt-dt-inicio">De:</label>
                        <input id="dataInicial" type="text" data-type="date" class="form-control filtroExtrato datepicker" value="<?=$data_inicial?>" >
                        <i class="bi bi-calendar3 calendar-icon"></i>
                    </div>
                    <div class="col-auto mb-3 position-relative">
                        <label class="form-group" for="filt-dt-inicio">Até:</label>
                        <input id="dataFinal" type="text" data-type="date" class="form-control filtroExtrato datepicker" value="<?=$data_final?>" >
                        <i class="bi bi-calendar3 calendar-icon"></i>
                    </div>
                <div class="col-auto" style="width:350px;">
                </div>
				<div class="col-auto" style="width:350px;">
                    <label class="form-group" >Pesquisar:</label>
					<input type="text" class="form-control searchBox" placeholder="Pesquisar Atendimento...">
				</div>
				<div class="col-auto">
                    <label class="form-group" >Por Página:</label>
					<select class="form-select rowsPerPage" style="width: auto;">
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
                        <option value="500">500</option>
					</select>
				</div>
				<div class="col-auto" hidden>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="" id="mostrarInativos">
						<label class="form-check-label" for="mostrarInativos	">
							Mostrar Contratos Encerrados
						</label>
					</div>
				</div>

			</div>
			<table 
				class="tablePagAtendimentos dataTable dTlinhaFina"
				data-table="atendimentos"
				style="width:100%">
				<thead>
					<tr >
						<th data-modal="modalAtendimento" data-field="data" data-sort="data">Data</th>
                        <th data-modal="modalAtendimento" data-field="foto_cliente" class="data-foto" data-foto></th>
						<th data-field="atendimento_id" data-sort-init="ASC" data-sort="num" hidden>ID</th>
						<th data-field="id_cliente"  data-sort="num" hidden>ID_cliente</th>
						<th data-modal="modalAtendimento" data-field="nome_cliente" data-sort="a-z">Cliente</th>
						<th data-modal="modalAtendimento" data-field="servico" data-sort="a-z">Serviço</th>
						<th data-modal="modalAtendimento" data-field="profissional_1"  data-sort="a-z">Profissional</th>
						<th data-field="status"  data-sort="a-z">status</th>
					</tr>
				</thead>
				<tbody>
				<!-- Preenchido pelo JS -->
				</tbody>
			</table>
			<div class="pagination mt-2"></div>
				<span class="info-range"></span>
			</div>
		
	</div>
	

</body>

 <script type="text/javascript" src="tecnico/tabelaPagAtendimentos.js?v=0.11"> </script>







