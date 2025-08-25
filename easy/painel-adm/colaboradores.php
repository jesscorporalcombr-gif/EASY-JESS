<?php 
$pag = 'colaboradores';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);



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
.status-profAtivo            { background: #4caf50; } /* verde */
.status-profInativo          { background: #f44336; } /* vermelho */





/* deixa só o texto em vertical */







</style>


<body>

   
	<div class="container-md listTabContainer mt-3">
		<h2> Colaboradores </h2>
		<div class="table-container" id="agendamentos-container-modal">
			<div class="row mb-2">
				<div class="col-auto" style="width:350px;">
					<input type="text" class="form-control searchBox" placeholder="Pesquisar Colaborador...">
				</div>
				<div class="col-auto">
					<select class="form-select rowsPerPage" style="width: auto;">
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
					</select>
				</div>
				<div class="col-auto">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="" id="mostrarInativos">
						<label class="form-check-label" for="mostrarInativos	">
							Mostrar Contratos Encerrados
						</label>
					</div>
				</div>

			</div>
			<table 
				class="tablePagColaboradores dataTable dTlinhaFina"
				data-table="colaboradores_contratos"
				style="width:100%">
				<thead>
					<tr >
						<th data-modal="modalColaborador" data-field="foto_cadastro" class="data-foto" data-foto>FOTO</th>
						<th data-field="contrato_id" data-sort-init="ASC" data-sort="num" hidden>ID</th>
						<th data-field="id_colaborador"  data-sort="num" hidden>ID_Colab</th>
						<th data-modal="modalColaborador" data-field="nome" data-sort="a-z">Nome</th>
						<th data-modal="modalColaborador" data-field="cargo" data-sort="a-z">Cargo</th>
						<th data-modal="modalColaborador" data-field="departamento"  data-sort="a-z">Departamento<th>
						<th data-field="telefone" class="data-whats" data-sort="num">Telefone</th>
						<th data-field="data_nascimento" data-classe="aniversario" data-classe="aniversario" data-sort="aniversario">Aniversario</th>
						<th data-field="tipo_contrato" data-sort="a-z">Contrato</th>
						<th data-field="data_inicio" data-sort="data">Início</th>
						<th data-field="ativo"  data-sort="a-z">Status</th>						
						<th data-field="situacao" data-sort="a-z" >Acompanhamento</th>
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

 <script type="text/javascript" src="colaboradores/tabelaPagColaborador.js?v=0.20"> </script>







