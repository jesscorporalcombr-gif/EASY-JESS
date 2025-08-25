<?php 
$pag = 'agenda_disponibilidades';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');
$corFontePad = $cor_fonte_profissional;
$corFundPad = $cor_fundo_profissional;


if(@$_GET['funcao'] == "editar"){

	$idContr = isset($_GET['id']) ? $_GET['id'] : 0; 
	$titulo_modal = 'Editar Registro';
	$query = $pdo->query("SELECT id, id_colaborador, nome, nome_agenda, ativo_agenda, foto_agenda, cor_fundo_agenda, cor_fonte_agenda, descricao_agenda, ordem_agenda, especialidade_agenda from colaboradores_contratos where id = '$idContr'");




	$contrato_prof = $query->fetch(PDO::FETCH_ASSOC);
	$total_reg = $query->rowCount();;

	

	$id_profissional = $contrato_prof['id_colaborador'];
	
	
	
	$query_cadastro = $pdo->query("SELECT id, nome, ativo_agenda, foto_agenda, foto_cadastro, cor_fundo_agenda, cor_fonte_agenda, descricao_agenda, ordem_agenda, especialidade_agenda, foto_sistema from ccolaboradores_cadastros where id = '$id_profissional'");
	$cadastro_prof = $query_cadastro->fetch(PDO::FETCH_ASSOC);


	

	if($total_reg > 0){ 
		

$deveSalvar = false;

// nome_agenda
if (!empty($contrato_prof['nome_agenda'])) {
    $nome_agenda = $contrato_prof['nome_agenda'];
} elseif (!empty($cadastro_prof['nome_agenda'])) {
    $nome_agenda = $cadastro_prof['nome_agenda'];
    $deveSalvar = true;
} elseif (!empty($cadastro_prof['nome'])) {
    $partes = explode(' ', trim($cadastro_prof['nome']));
    $nome_agenda = $partes[0];
    $deveSalvar = true;
} else {
    $nome_agenda = 'primeiro do nome';
    $deveSalvar = true;
}

// nome
if (!empty($contrato_prof['nome'])) {
    $nome = $contrato_prof['nome'];
} elseif (!empty($cadastro_prof['nome'])) {
    $nome = $cadastro_prof['nome'];
    $deveSalvar = true;
} else {
    $nome = '';
    $deveSalvar = true;
}

// foto_cadastro
$foto_cadastro = !empty($cadastro_prof['foto_cadastro']) ? $cadastro_prof['foto_cadastro'] : '';
$foto_sistema = !empty($cadastro_prof['foto_sistema']) ? $cadastro_prof['foto_sistema'] : '';
// id_contrato
$id_contrato = !empty($contrato_prof['id']) ? $contrato_prof['id'] : 0;

// ativo_agenda
if (isset($contrato_prof['ativo_agenda'])) {
    $ativo_agenda = $contrato_prof['ativo_agenda'];
} elseif (isset($cadastro_prof['ativo_agenda'])) {
    $ativo_agenda = $cadastro_prof['ativo_agenda'];
    $deveSalvar = true;
} else {
    $ativo_agenda = '';
    $deveSalvar = true;
}

$fotoCadastro='';
// foto_agenda_contrato
$fotoContratoAgendaExiste = is_file('../'.$pasta.'/img/colaboradores_cadastros/' . $contrato_prof['foto_agenda']) && $contrato_prof['foto_agenda'];
// foto_agenda_cadastro
$fotoCadastroAgendaExiste = is_file('../'.$pasta.'/img/colaboradores_cadastros/' . $cadastro_prof['foto_agenda']) && $cadastro_prof['foto_agenda'];
// foto_agenda_cadastro
$fotoCadastroExiste = is_file('../'.$pasta.'/img/colaboradores_cadastros/' . $cadastro_prof['foto_cadastro']) &&  $cadastro_prof['foto_cadastro'];

$fotoSistemaExiste = is_file('../'.$pasta.'/img/users/' . $cadastro_prof['foto_sistema']) &&  $cadastro_prof['foto_sistema'];





$foto_cadastro = ($cadastro_prof['foto_cadastro'])?($cadastro_prof['foto_cadastro']):'';



if ($fotoContratoAgendaExiste) {
    $foto_agenda = $contrato_prof['foto_agenda'];
	$fotocaminhoAgenda = 'colaboradores_cadastros/' . $foto_agenda;
} elseif ($fotoCadastroAgendaExiste) {
    $foto_agenda = $cadastro_prof['foto_agenda'];
	$fotocaminhoAgenda = 'colaboradores_cadastros/' . $foto_agenda;
	$nova_foto_agenda="cadastro_foto_agenda";
    $deveSalvar = true;
	//$fotoCadastro=$foto_agenda;
} elseif ($fotoSistemaExiste) {
    $foto_agenda = $cadastro_prof['foto_sistema'];
	$fotocaminhoAgenda = 'users/' . $foto_agenda;
    $deveSalvar = true;
	$nova_foto_agenda='cadastro_foto_sistema';
	//$fotoCadastro=$foto_agenda;
} elseif ($fotoCadastroExiste)  {
	$foto_agenda = $cadastro_prof['foto_cadastro'];
    $deveSalvar = true;
	$nova_foto_agenda='cadastro_foto';
	//$fotoCadastro=$foto_agenda;
}else{
  $foto_agenda = '';
  $deveSalvar = true;
}




// cor_fundo_agenda
if (!empty($contrato_prof['cor_fundo_agenda'])) {
    $cor_fundo_agenda = $contrato_prof['cor_fundo_agenda'];
} elseif (!empty($cadastro_prof['cor_fundo_agenda'])) {
    $cor_fundo_agenda = $cadastro_prof['cor_fundo_agenda'];
    $deveSalvar = true;
} else {
    $cor_fundo_agenda = '';
    $deveSalvar = true;
}

// cor_fonte_agenda
if (!empty($contrato_prof['cor_fonte_agenda'])) {
    $cor_fonte_agenda = $contrato_prof['cor_fonte_agenda'];
} elseif (!empty($cadastro_prof['cor_fonte_agenda'])) {
    $cor_fonte_agenda = $cadastro_prof['cor_fonte_agenda'];
    $deveSalvar = true;
} else {
    $cor_fonte_agenda = '';
    $deveSalvar = true;
}

// descricao_agenda
if (!empty($contrato_prof['descricao_agenda'])) {
    $descricao_agenda = $contrato_prof['descricao_agenda'];
} elseif (!empty($cadastro_prof['descricao_agenda']) && !empty($cadastro_prof['descricao_agenda'])) {
    $descricao_agenda = $cadastro_prof['descricao_agenda'];
    $deveSalvar = true;
} else {
    $descricao_agenda = '';
    $deveSalvar = false;
}

// ordem_agenda
if (isset($contrato_prof['ordem_agenda'])) {
    $ordem_agenda = $contrato_prof['ordem_agenda'];
} elseif (isset($cadastro_prof['ordem_agenda'])) {
    $ordem_agenda = $cadastro_prof['ordem_agenda'];
    $deveSalvar = true;
} else {
    $ordem_agenda = 0;
    $deveSalvar = true;
}

// especialidade_agenda
if (!empty($contrato_prof['especialidade_agenda'])) {
    $especialidade_agenda = $contrato_prof['especialidade_agenda'];
} elseif (!empty($cadastro_prof['especialidade_agenda'])) {
    $especialidade_agenda = $cadastro_prof['especialidade_agenda'];
    $deveSalvar = true;
} else {
    $especialidade_agenda = '';
    $deveSalvar = true;
}

		
	}


	try {
		// Suponha que você já validou e sanitizou $idProf.
		$idProf = isset($_GET['id']) ? $_GET['id'] : 0;
	
		// Consulta SQL para buscar dados de servicos e atualizar com servicos_profissional se as condições forem verdadeiras
		$sql = "SELECT s.id, s.servico, s.categoria, 
					   COALESCE(sp.tempo, s.tempo) AS tempo, 
					   COALESCE(sp.preco, s.valor_venda) AS valor_venda, 
					   COALESCE(sp.comissao, s.comissao) AS comissao, 
					   CASE 
						   WHEN s.agendamento_online <> 1 THEN 2 
						   ELSE COALESCE(sp.agendamento_online, s.agendamento_online) 
					   END AS agendamento_online, 
					   COALESCE(sp.executa, s.executa) AS executa
				FROM servicos s
				LEFT JOIN servicos_profissional sp ON s.id = sp.id_servico 
					AND sp.id_profissional = :idProf 
					AND sp.executa = 1
				WHERE s.excluido <> 1";
	
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':idProf', $idProf, PDO::PARAM_INT);
		$stmt->execute();
		$serv = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	} catch (PDOException $e) {
		die("Erro ao buscar dados: " . $e->getMessage());
	}
	
	



}else{
	$titulo_modal = 'Inserir Colaborador';
	try {
		$sql = "SELECT *
		FROM servicos
		WHERE excluido<> true
		ORDER BY categoria ASC";
		$serv= $pdo->prepare($sql);
		$serv->execute();
		

	} catch (PDOException $e) {
		die("Erro ao buscar dados: " . $e->getMessage());
	}
}

gerarMenu($pag, $grupos);
?>



<!-- estulo para a tabela de horários-->



<body>




<div class="container-md">

	<div class="mb-1"> <!--  -->
			
					
			<button onclick="abrirModal('modalProfissional')" type="button" class="btn-add">
				<i class="bi bi-person-down ico-add"></i>+ Profissional
			</button>
		
			<button hidden style="font-size: 12px;" class="btn btn-outline-primary active filter-button" data-field-cond="ativo_agenda" data-cond-search="1">Ativo agenda</button>
			<button hidden style="font-size: 12px;" class="btn btn-outline-primary active filter-button" data-field-cond="ativo" data-cond-search="1">Ativo contrato</button>
			<hr>
	</div>
</div>	


<div class="container-md">

	<div class="row mb-2">
		<div class="col-md-1">
			<select class ="form-select" id="rowsPerPage">
				<option value="10">10</option>
				<option value="25">50</option>
				<option value="100">100</option>
				
			</select>
		</div>

		<div class="col-md-7">
			<!-- espaço vazio-->
		</div>

		<div class="col-md-4">
			<input type="text" class="form-control" id="searchBox" placeholder="Pesquisar...">
		</div>
	</div>

	<div class="row" >
		<div class="mb-4">	
			<table id="dataTable" class="dataTable" data-table="colaboradores_contratos">
				<thead>
					<tr>
						
						<th data-sort="a-z" class="data-img data-modal" data-modal="modalProfissional" data-img="../<?= $pasta?>/img/cadastro_colaboradores/" data-field="foto_agenda">FOTO</th>
						<th hidden data-sort="num" data-field="id">ID</th>
						<th data-sort="a-z" class="data-modal" data-modal="modalProfissional" data-field="nome">NOME</th>
						<th data-sort="a-z" data-field="nome_agenda" class="data-modal" data-modal="modalProfissional">AGENDA</th>
						<th data-sort="a-z" data-field="ordem_agenda" class="data-modal" data-modal="modalProfissional">ORDEM</th>
						<th hidden data-sort="num" data-field="ativo_agenda">Situação</th>
						<th hidden data-sort="num" data-field="ativo">Situação</th>
						<th data-sort="a-z" class="data-color-circle" data-field="cor_fundo_agenda">COR</th>
						
					<!-- Adicione mais colunas conforme necessário -->
					</tr>
				</thead>
				<tbody>
					<!-- As linhas serão inseridas aqui dinamicamente -->
				</tbody>
			</table>

		</div>
	</div>

	<div class="row mb-8">
		
		<div class="col-md-6" id="pagination">
			<!-- Botões de navegação serão inseridos aqui -->
		</div>

		<div id="info-range" class="col-md-6" style="margin-bottom: 10px; text-align:right;">
			Exibindo de 0 a 0 de um total de 0 registros
		</div>
	</div>
</div>





</body>