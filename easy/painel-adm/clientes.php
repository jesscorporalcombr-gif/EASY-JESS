<?php 
$pag = 'clientes';
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

.status-label {
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
.status-lead             { background: #607d8b; } /* cinza-azulado */
.status-nao-ativado      { background: #9e9e9e; } /* cinza */
.status-em-ativacao      { background: #ffb300; } /* âmbar */
.status-ativo            { background: #4caf50; } /* verde */
.status-inativo          { background: #f44336; } /* vermelho */
.status-vencido          { background: #d32f2f; } /* vermelho-escuro */
.status-em-reativacao    { background: #03a9f4; } /* azul-claro */

.status-outro            { background: #757575; } /* fallback cinza-escuro */

td.sIgual{
	text-align: center;
	font-size: 0.9em;
}




/* deixa só o texto em vertical */


/* botão minimalista */
.btn-toggle-cols {
	padding: 2px 10px;
	cursor: pointer;
	font-size: 1.0em;
}

.header-consumidos {
  display: flex;
  flex-direction: column;   /* empilha um acima do outro */
  align-items: center;      /* centraliza horizontalmente */
  gap: 4px;                 /* espaço entre botão e texto */
}
.th-consumidos .header-consumidos {
  display: inline-flex;         /* garante que o wrapper respeite suas dimensões */
  flex-direction: column;
  align-items: center;
  gap: 4px;
  
  /* ====== rotação ====== */
  transform: rotate(-90deg);
  transform-origin: center center;
  
  /* ajuste de tamanho (troque pelos valores que couberem no seu layout) 
  width: 80px;  
  height: 40px;*/
}
  .span-consumidos{
font-size: 0.7em;

  }
/* opcional: remove o width:100% do botão para não ficar “esticado” */




</style>


<body>

   
	<div class="container-md listTabContainer mt-3">
		<h1> clientes </h1>
		<div class="table-container" id="agendamentos-container-modal">
			<div class="row mb-2">
				<div class="col-auto" style="width:350px;">
					<input type="text" class="form-control searchBox" placeholder="Pesquisar cliente...">
				</div>
				<div class="col-auto">
				<select class="form-select rowsPerPage" style="width: auto;">
					<option value="10">10</option>
					<option value="25">25</option>
					<option value="50">50</option>
				</select>
				</div>
			</div>
			<table 
				class="tablePagClientes dataTable dTlinhaFina"
				data-table="clientes"
				
				
				style="width:100%"><!-- Substitua 123 pelo id_cliente do cliente atual via JS -->
				<thead>
					<tr >
						<th data-modal="modalClientes" data-field="foto" class="data-foto" data-foto="../<?=$pasta?>/img/clientes/">FOTO</th>
						<th data-field="id" data-sort="num" hidden>ID</th>
						<th data-modal="modalClientes" data-field="nome" data-sort="a-z">Nome</th>
						<th data-modal="modalClientes" data-field="data_cadastro" data-classe-td="" data-sort="data">Desde</th>
						<th data-field="celular" class="data-whats" data-sort="num">Telefone</th>
						<th data-field="aniversario" data-classe="aniversario" data-sort="aniversario">Aniversario</th>
						<th data-field="compareceu_em" data-sort="data">Ultima</th>
						<th data-field="situacao" data-sort-init="ASC" data-sort="a-z">Status</th>
						<th data-field="proximo_agendamento" data-sort="data">Proximo</th>
						<th data-field="saldo"  data-classe-td="posNeg numero reais" data-sort="num">Saldo</th>
						<th data-field="acompanhamento_ag" >Acompanhamento</th>
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

 <script type="text/javascript" src="clientes/tabelaPagCliente.js?v=1.63"> </script>







