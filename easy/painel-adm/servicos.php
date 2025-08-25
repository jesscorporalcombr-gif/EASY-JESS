<?php 
$pag = 'servicos';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
gerarMenu($pag, $grupos);



?>
<style>
.status-serv{
	  display: inline-block;
	font-size:12px;
	color:white;
	border-radius:20px;
	padding: 3px 10px;
}

.status-agonline{
	display: inline-block;
	font-size:10px;
	color:white;
	border-radius:20px;
	padding: 3px 10px;
}

.status-ativo            { background: #4caf50; } /* verde */
.status-deletado          { background: #f44336; } /* vermelho */
.status-online          { background: #3669f4ff; } /* vermelho */
.status-interno          { background: #727272ff; } /* vermelho */
</style>

<body>

   
	<div class="container-md listTabContainer mt-3">
		<h1>Serviços</h1>
		<button id="btn-novo-servico" class="btn btn-primary">
			<i class="bi bi-plus-circle"></i> Novo Serviço
		</button>

		<div class="table-container mt-3" id="servicos-container-modal">
			<div class="row mb-2">
				<div class="col-auto" style="width:350px;">
					<input type="text" class="form-control searchBox" placeholder="Pesquisar Serviço...">
				</div>
			</div>
			<table 
				class="tablePagServicos dataTable dTlinhaFina"
				data-table="servicos"
				style="width:100%">
				<thead>
					<tr>
						<th data-modal="modalServicos" data-foto="datafoto" data-field="foto" data-sort="a-z"></th>
						<th data-modal="modalServicos" data-field="nome" data-sort="a-z">Nome</th>
						<th data-field="servico_id" data-sort-init="DESC" data-sort="num" hidden>ID</th>
						<th data-modal="modalServicos" data-field="categoria"  data-sort="a-z">Categoria<th>
						<th data-field="tempo" data-sort="num">Tempo(min)</th>
						<th data-field="preco" data-classe-td="reais" data-sort="num">Preço</th>
						<th data-field="custo_total" data-classe-td="reais" data-sort="num">custo total</th>
						<th data-field="agendamento_online" style="min-width: 160px;" data-sort="a-z" >Online</th>
						<th data-field="status"  data-sort="a-z">Status</th>						
						
					</tr>
				</thead>
				<tbody>
				<!-- Preenchido pelo JS -->
				</tbody>
			</table>
			<div class="row">
				<div class="col-auto">
					<div class="pagination mt-2"></div>
						<span class="info-range"></span>
				</div>
				<div class="col-auto mt-3">
					<select class="form-select rowsPerPage" style="width: auto;">
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
				</div>
				<div class="col-auto mt-3">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="" id="mostrarInativos">
						<label class="form-check-label" for="mostrarInativos">
							Mostrar Serviços Inativos
						</label>
					</div>
				</div>
			</div>
		</div>
	

</body>


 <script type="text/javascript" src="servicos/tabelaPagServico.js?v=0.27"> </script>


<script>
  $(document).on('click', '#btn-novo-servico', function () {
    $.ajax({
      url: 'Modals/modalServicos.php',
      type: 'POST',
      data: {}, // sem id => entra no fluxo "Novo Serviço"
      success: function (html) {
        // injeta o modal no body
        $('body').append(html);

        const $modal = $('#modalCadServico');
        // mostra
        $modal.modal('show');

        // quando abrir, já habilita edição na aba Cadastro
        $modal.on('shown.bs.modal', function () {
          try {
            window.editar = true;                    // usa sua flag global
            if (window.habilitarEditaCadastro) {
              window.habilitarEditaCadastro();      // habilita inputs/botões
            }
            // garante que a aba Cadastro está ativa
            const tab = document.querySelector('#cadastro-tab');
            if (tab) tab.click();
          } catch(e){}
        });

        // limpeza ao fechar (defensivo; você já remove no próprio modal, mas reforça)
        $modal.on('hidden.bs.modal', function () {
          $(this).remove();
          $('.modal-backdrop').remove();
        });
      },
      error: function (xhr) {
        alert('Erro ao abrir o modal: ' + (xhr.statusText || xhr.status));
      }
    });
  });
</script>




