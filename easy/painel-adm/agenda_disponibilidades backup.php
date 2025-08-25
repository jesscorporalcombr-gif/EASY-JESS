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
	$query = $pdo->query("SELECT id, id_colaborador, nome, nome_agenda, ativo_agenda, foto_agenda, cor_fundo_agenda, cor_fonte_agenda, descricao_agenda, ordem_agenda, especialidade_agenda from contratos_colaboradores where id = '$idContr'");




	$contrato_prof = $query->fetch(PDO::FETCH_ASSOC);
	$total_reg = $query->rowCount();;

	

	$id_profissional = $contrato_prof['id_colaborador'];
	
	
	
	$query_cadastro = $pdo->query("SELECT id, nome, ativo_agenda, foto_agenda, foto_cadastro, cor_fundo_agenda, cor_fonte_agenda, descricao_agenda, ordem_agenda, especialidade_agenda, foto_sistema from cadastro_colaboradores where id = '$id_profissional'");
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
$fotoContratoAgendaExiste = is_file('../img/cadastro_colaboradores/' . $contrato_prof['foto_agenda']) && $contrato_prof['foto_agenda'];
// foto_agenda_cadastro
$fotoCadastroAgendaExiste = is_file('../img/cadastro_colaboradores/' . $cadastro_prof['foto_agenda']) && $cadastro_prof['foto_agenda'];
// foto_agenda_cadastro
$fotoCadastroExiste = is_file('../img/cadastro_colaboradores/' . $cadastro_prof['foto_cadastro']) &&  $cadastro_prof['foto_cadastro'];

$fotoSistemaExiste = is_file('../img/users/' . $cadastro_prof['foto_sistema']) &&  $cadastro_prof['foto_sistema'];





$foto_cadastro = ($cadastro_prof['foto_cadastro'])?($cadastro_prof['foto_cadastro']):'';



if ($fotoContratoAgendaExiste) {
    $foto_agenda = $contrato_prof['foto_agenda'];
	$fotocaminhoAgenda ='cadastro_colaboradores/' . $foto_agenda;
} elseif ($fotoCadastroAgendaExiste) {
    $foto_agenda = $cadastro_prof['foto_agenda'];
	$fotocaminhoAgenda ='cadastro_colaboradores/' . $foto_agenda;
	$nova_foto_agenda="cadastro_foto_agenda";
    $deveSalvar = true;
	//$fotoCadastro=$foto_agenda;
} elseif ($fotoSistemaExiste) {
    $foto_agenda = $cadastro_prof['foto_sistema'];
	$fotocaminhoAgenda ='users/' . $foto_agenda;
    $deveSalvar = true;
	$nova_foto_agenda="cadastro_foto_sistema";
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
			
					
			<a href="index.php?pagina=<?php echo $pag ?>&funcao=novo" type="button" class="btn-add">
				<i class="bi bi-person-down ico-add"></i> Incluir Profissional na Agenda
			</a>
		
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
				<option value="500">500</option>
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

		<table id="dataTable" class="dataTabele" data-table="contratos_colaboradores">
			<thead>
				<tr>
					<th hidden data-sort="num" data-field="id">ID</th>
					<th data-sort="a-z" class="data-img"  data-img="../img/cadastro_colaboradores/" data-field="foto_agenda">FOTO</th>
					<th data-sort="a-z" class="data-modal" data-modal="modalProfissional" data-field="nome">NOME</th>
					<th data-sort="a-z" data-field="nome_agenda" class="data-get" data-get= "index.php?pagina=agenda_disponibilidades&funcao=editar&id=">AGENDA</th>
					<th data-sort="a-z" data-field="ordem_agenda">ORDEM</th>
					<th data-sort="a-z" class="data-color-circle" data-field="cor_fundo_agenda">COR</th>
					
					<th hidden data-sort="num" data-field="ativo_agenda">Situação</th>
					<th hidden data-sort="num" data-field="ativo">Situação</th>
					
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







<div class="modal fade" tabindex="-1" id="modalCadastrar" data-bs-backdrop="static" >
	<div class="modal-dialog modal-lg" >
		<div class="modal-content" >
		

		
			<div class="modal-header">
				<h3 class="modal-title"> <?php echo $titulo_modal ?></h3>
							
				<a type="button"  class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-lg"></i></a>
			</div>

			<div class="modal-body" style = "max-height: 650px; overflow-x: auto;">

					<ul class="nav nav-tabs mb-3" style="margin-top:-13px; height: 30px;" id="minhasAbas" role="tablist">
							<li class="nav-item" role="presentation">
								<button class="nav-link active tab-btn"  id="aba1-tab" data-bs-toggle="tab" data-bs-target="#aba1" type="button" role="tab" aria-controls="aba1" aria-selected="true">Principal</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link tab-btn" id="aba2-tab"  data-bs-toggle="tab" data-bs-target="#aba2" type="button" role="tab" aria-controls="aba2" aria-selected="false">Horários</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link  tab-btn" id="aba3-tab" data-bs-toggle="tab" data-bs-target="#aba3" type="button" role="tab" aria-controls="aba3" aria-selected="false">Serviços</button>
							</li>
					</ul>


				<div class="tab-content tab-cont-form"  id="minhasAbasContent">
				
					
					<div class="tab-pane fade show active" id="aba1" role="tabpanel" aria-labelledby="aba1-tab">
						<form method="POST" id="form-aba1">		
							<div class="row mb-4">
								
								<div class="col-auto" style=" min-width: 150px; ">
										
										<div class="row justify-content-center">
											
												<div class="col-md-12 text-center">  
													<div class="form-group">
													<label class="form-label" >Foto na Agenda</label>
														<div class="mt-2" id="bloco_cor_agenda" style="background-color: <?= ($cor_fundo_agenda)? $cor_fundo_agenda : $corFundPad ?>; width: 150px; height:180px; border-radius: 15px;" >
															<img style="border-radius:50%; cursor:pointer; margin-top:10px; width: 130px;" src="../img/<?= (@$foto_agenda) ? $fotocaminhoAgenda : 'sem-foto.svg'; ?>"  id="img-foto_agenda" >
															<p id="p_nome_agenda" style="font-weight:500; color: <?= ($cor_fonte_agenda)? $cor_fonte_agenda : $corFontePad ?> "><?= $nome_agenda ?></p>
															<input hidden name="foto-agenda" value="<?=$foto_agenda?>">
															<input hidden name="nova-foto-agenda" value="<?=$nova_foto_agenda?>">
															

															<input onChange="carregarImg()" type="file" style="display:none;" class="form-control-file" id="input-foto_agenda" name="input-foto_agenda">
														</div>
													</div>
												</div>
										</div>
									
									<div class= "row">					
										<div class="col-auto d-flex" style="height: 50px; padding-top: 10px;">
  <!-- círculo com o input color -->
											<div class="me-2" style="border-radius: 50%; overflow: hidden; width: 30px; height: 30px; border: 1px solid grey;">
												<input 
												type="color" 
												class="form-control form-control-color p-0 border-0" 
												id="cor_fundo_agenda" 
												name="cor_fundo_agenda" 
												value="<?= ($cor_fundo_agenda)? $cor_fundo_agenda : $corFundPad ?>" 
												style="width: 100%; height: 100%; cursor: pointer;"
												>
											</div>
											<!-- label à direita -->
											<label for="cor_fundo_agenda" class="mb-0">Cor na Agenda</label>
										</div>
										
									</div>
									<div class= "row ">					
										<div class="col-auto d-flex " style="height: 50px; padding-top: 10px;">
  <!-- círculo com o input color -->
											<div class="me-2" style="border-radius: 50%; overflow: hidden; width: 30px; height: 30px; border: 1px solid grey;">
												<input 
												type="color" 
												class="form-control form-control-color p-0 border-0" 
												id="cor_fonte_agenda" 
												name="cor_fonte_agenda" 
												value="<?= ($cor_fonte_agenda)?$cor_fonte_agenda : $corFontePad ?>" 
												style="width: 100%; height: 100%; cursor: pointer;"
												>
											</div>
											<!-- label à direita -->
											<label for="cor_fonte_agenda" class="mb-0">Cor da Fonte</label>
										</div>
										
									</div>


								</div>

								<div class="col" >
										<div class="row mb-2">
												<div class="col-md-6">
													<div class="mb-3">
														<label for="frm-nome" class="form-group">Nome</label>
														<input type="text" style="border: 1px solid #F7F7F7; padding: 3px; min-width: 380px;" readonly  id="frm-nome" name="frm-nome"  required value="<?= $nome ?>">
													
													</div> 



												</div>
												<div class="col-md-3">

												</div>
												<div class="col-md-2">
													<div id="divImgConta" >
														
															<img style="border-radius:50%;" src="../img/<?=($foto_cadastro)? 'cadastro_colaboradores/' . $foto_cadastro : 'sem-foto.svg' ?>"  width="60px" id="frm-foto_cadastro">
															<input hidden name="foto-cadastro" value="<?=$foto_cadastro ?>">
													
													</div>
												</div>
										</div>
										<div class="row">
												<div class="col-md-5">
													<div class="mb-3">
														<label for="frm-nome_agenda" class="form-group">Nome na Agenda</label>
														<input type="text" class="form-control"  id="frm-nome_agenda" name="frm-nome_agenda"  required value="<?= @$nome_agenda ?>">
													</div> 
												</div>
												<div class="col-md-4">
													<div class="mb-3">
														<label for="frm-especialidade_agenda" class="form-group">Especialidade</label>
														<input type="text" class="form-control"  id="frm-especialidade_agenda" name="frm-especialidade_agenda"  required value="<?php echo @$especialidade_agenda ?>">
													</div> 
												</div>
												<div class="col-md-2">
													<div class="mb-3">
														<label for="frm-ordem_agenda" class="form-group">Ordem</label>
														<input type="number" class="form-control"  id="frm-ordem_agenda" name="frm-ordem_agenda"  required value="<?php echo @$ordem_agenda ?>">
													</div> 
												</div>

										</div>

										<div class="row">
												<div class="col-md-11">
													<div>
														<label for="frm-descricao_agenda" class="form-group">Descrição do Profissional</label>
														<textarea type="text" style="height: 80px;" class="form-control"  id="frm-descricao_agenda" name="frm-descricao_agenda"  ><?php echo @$descricao_agenda ?></textarea>
													</div> 
												</div>
										</div>
									

								</div>
							</div>
						


							<hr >												
								

							<div class="row">
								<div class="col-md-4">
									<div class="mb-3">
										<label for="frm-ativo_agenda" class="form-group">Ativo na Agenda</label>
											<select class="form-select" if="frm-ativo_agenda" aria-label="Default select example" name="frm-ativo_agenda">
												
												<option <?php if(@$ativo_agenda == ''){ ?> selected <?php } ?>  value="--">--</option>

												<option <?php if(@$ativo_agenda == true){ ?> selected <?php } ?>  value="Ativo">Ativo</option>

												<option <?php if(@$ativo_agenda == false){ ?> selected <?php } ?>  value="Inativo">Inativo</option>
																									
											</select>
										
									</div>  
								</div>
							</div>

							<small><div align="center" class="mt-1" id="mensagem"></div> </small>

							<div class="modal-footer d-flex justify-content-between align-items-center" style="background-color: <?php echo $cor_fundo_form ?>;">
								<div id="mensagemRodape" class="text-start small text-muted">
									<?=($deveSalvar)? 
									'<span style="color:red;">Salve as informações para ter efeito na agenda!</span>':'' ?>  <!-- Aqui aparece a mensagem -->
								</div>

							
							
							
								<div>
									<button type="button" id="btn-fechar" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
									<button name="btn-salvar" id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>

									<input type="hidden" name="frm-id"  id="frm-id" value="<?php echo @$_GET['id'] ?>">
								</div>
							</div>
						</form>	
					</div><!-- FECHA ABA1 -->
					

						<!-------------------------- ABA 2 HORARIOS------------>
					
					<div class="tab-pane fade"  height="600px" id="aba2" role="tabpanel" aria-labelledby="aba2-tab">
						<form method="POST" id="form-aba2">	
							<p>HORÁRIOS NORMAIS:</p>
							<div class= "row mt-4" style="padding-left: 30px;">
								<table class="tabela-h-prof">
									<tr class=" tr-h-prof tr-th">
										<th class="th-h-prof dia-th">Dia</th>
										<th class="th-h-prof" >Entrada</th>
										<th class="th-h-prof">Saída</th>
										<th class="th-h-prof">Entrada</th>
										<th class="th-h-prof">Saída</th>
										<th class="th-h-prof">Entrada</th>
										<th class="th-h-prof">Saída</th>
										<th class="th-h-prof">Entrada</th>
										<th class="th-h-prof">Saída</th>
									</tr>

									<!-- PHP loop para preencher os dias da semana -->
									<?php
									$dias = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];

									foreach ($dias as $dia) {
										echo '<tr class="tr-h-prof"> ';
										echo '<td class="td-h-prof dia-td">' . $dia . '</td>';

										for ($i = 0; $i < 8; $i++) {
											echo '<td class="td-h-prof"><input type="time" class="input-horario"></td>';
										}
										echo "</tr>";
									}
									?>
								</table>


							</div>
							<div class="modal-footer"  style="background-color: <?php echo $cor_fundo_form ?>;">
								<button type="button" id="btn-fechar2" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button name="btn-salvar2" id="btn-salvar2" type="submit" class="btn btn-primary">Salvar</button>

							</div>
						</form>
					</div><!-- FECHA ABA 2-->
						
					

						<!-------------------------- ABA 3 SERVICOS------------>
					<div class="tab-pane fade" id="aba3" role="tabpanel" aria-labelledby="aba3-tab">
						<form method="POST" id="form-aba3">	
						
						<div  >
							
							<div class="container-md">
								<div class="row mb-2">

									<div class="input-group mb-2">
										
										<input type="text" class="form-control" id="searchInput" style="max-width: 350px;"onkeyup="filterTable()" placeholder="Buscar serviço ou categoria">
										
									</div>
								</div>
							</div>

							<div  style = "max-height: 350px; overflow-y: auto;">
									<div >
										<table id="tableServProf">
											<thead style="font-size: 12px; border-radius:10px;">
												<tr style="cursor: pointer; border-bottom:1px solid #D9D9D9; color:white; background-color: <?php echo $cor_icon_menu ?>; height: 50px;">
													<th style="text-align:center;"><input id="checkAll" type="checkbox" onclick="toggleCheckboxes(this)"></th>
													<th  style="cursor: pointer; width: 20px;" onclick="sortTable(1)">SERVIÇO</th>
													<th  style="cursor: pointer;" onclick="sortTable(2)">CATEGORIA</th>
													<th style="text-align:center;">TEMPO</th>
													<th style="text-align:center;">PRECO</th>
													<th style="text-align:center;">COMISSAO</th>
													<th style="text-align:center;">AG ONLINE</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($serv as $servico): ?>
													<tr style="font-size: 12px; border-bottom: 1px solid #f3f3f3; height:25px;">
														<input type="hidden" name="id_prof3" value="<?= @$_GET['id']?>"> <!-- id do profissional -->
														<input type="hidden" name="servico[<?= $servico['id'] ?>][id]" value="<?= $servico['id'] ?>">
														<td style="width: 50px; padding:8px 2px 2px 10px;"><input class="chk-exec" type="checkbox" name="servico[<?= $servico['id'] ?>][executa]" <?php echo $servico['executa'] ? ' checked ' : ''; ?>> </td>
														<td style="width: 350px;"><?php echo htmlspecialchars($servico['servico']); ?></td>
														<td style="width: 150px;"><?php echo htmlspecialchars($servico['categoria']); ?></td>
														<td><input style="text-align:center; width:60px; height:20px;" type="number" name="servico[<?= $servico['id'] ?>][tempo]" class="form-control" value="<?= htmlspecialchars($servico['tempo']); ?>"></td>
														<td style="width: 70px;"><input style="width: 55px; padding-left:2px; margin-left:5px; text-align:center; height:20px;" type="text" name="servico[<?= $servico['id'] ?>][venda]" class="form-control numVirg-2c" value="<?= htmlspecialchars($servico['valor_venda']); ?>"></td>
														<td><input style="width: 70px; text-align:center; height:20px;"type="text" name="servico[<?= $servico['id'] ?>][comissao]" class="numVirgPerc form-control" value="<?= htmlspecialchars($servico['comissao']); ?>"></td> <!-- A COMISSAO deve ser preenchida conforme você tem os dados matriz $serv_profissional -->
														<td style="width: 60px; text-align:center;"><input type="checkbox" name="servico[<?= $servico['id'] ?>][ag_online]" <?= ($servico['agendamento_online'] == 1) ? 'checked' : (($servico['agendamento_online'] == 2) ? 'disabled' : '') ?>></td>

													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>

							</div>
									
							<div class="modal-footer d-flex justify-content-between align-items-center" style="background-color: <?php echo $cor_fundo_form ?>;">
								
								<!-- Mensagens do lado esquerdo -->
								<div id="mensagemRodape" class="text-start small text-muted">
									
									<?=($deveSalvar3)? 
									'<span style:"font:red">Salve as informações para ter efeito na agenda!</span>':'' ?>  <!-- Aqui aparece a mensagem -->
								</div>

								<!-- Botões do lado direito -->
								<div>
									<button type="button" id="btn-fechar3" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
									<button name="btn-salvar3" id="btn-salvar3" type="submit" class="btn btn-primary">Salvar</button>
								</div>

							</div>

							</div>
						</form>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>





<script>
document.addEventListener('input', handlerCor, true);  // true = captura, pega até inputs escondidos
document.addEventListener('change', handlerCor, true);
console.log('Alterando a cor do fundo da agenda');
function handlerCor(e) {
  const el = e.target;

  // #cor_fundo_agenda  --> altera BG do #bloco_cor_agenda
  if (el.id === 'cor_fundo_agenda') {
	
    const bloco = document.getElementById('bloco_cor_agenda');
    if (bloco) bloco.style.backgroundColor = el.value;
    return;
  }

  // #cor_fonte_agenda  --> altera cor da fonte de #p_nome_agenda
  if (el.id === 'cor_fonte_agenda') {
    const pNome = document.getElementById('p_nome_agenda');
    if (pNome) pNome.style.color = el.value;
  }
}


function filterTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("tableServProf");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        tdService = tr[i].getElementsByTagName("td")[1]; // Ajuste o índice conforme necessário
        tdCategory = tr[i].getElementsByTagName("td")[2];
        if (tdService || tdCategory) {
            txtValueService = tdService ? tdService.textContent || tdService.innerText : "";
            txtValueCategory = tdCategory ? tdCategory.textContent || tdCategory.innerText : "";
            if (txtValueService.toUpperCase().indexOf(filter) > -1 || txtValueCategory.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}


function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir = "asc", switchcount = 0;
  table = document.getElementById("tableServProf");
  switching = true;
  
  // Continue com o processo de ordenação até que não haja mais trocas
  while (switching) {
    switching = false;
    rows = table.getElementsByTagName("tr");
    
    // Loop através das linhas da tabela
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      // Obtenha os elementos <td> que você deseja comparar
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      
      // Decida se deve trocar com base na direção atual
      if (dir === "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (dir === "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      // Realize a troca e continue o loop
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount++;
    } else {
      // Se não houve trocas e a direção é ascendente, inverta a direção e teste novamente
      if (switchcount === 0 && dir === "asc") {
        dir = "desc";
        switching = true;
      } else {
        dir = "asc";
      }
    }
  }
}


function toggleCheckboxes(source) {
  var checkboxes = document.querySelectorAll('.chk-exec'); // Seleciona todos os checkboxes com a classe 'chk-exec'
  
  // Itera sobre eles
  for (var i = 0, n = checkboxes.length; i < n; i++) {
    // Altera o estado do checkbox apenas se ele estiver visível
    if (checkboxes[i].type === 'checkbox' && checkboxes[i].offsetParent !== null) {
      checkboxes[i].checked = source.checked;
    }
  }
}




</script>









<?php 
if(@$_GET['funcao'] == "editar"){ ?>
	<script type="text/javascript">
		var myModal = new bootstrap.Modal(document.getElementById('modalCadastrar'), {
			backdrop: 'static'
		})
		
        
		myModal.show();
				
	</script>
<?php } ?>



<!--AJAX PARA INSERÇÃO E EDIÇÃO DOS DADOS COM IMAGEM -->
<script type="text/javascript">
	$("#form-aba1").submit(function () {
		var pag = "<?=$pag?>";
		event.preventDefault();

		idReal = (<?= $_GET['id'] ?>);
		idGrava= document.getElementById('frm-id').value;
		console.log(idReal);
		console.log(idGrava);

		if (idReal!=idGrava) return;

		var formData = new FormData(this);

		$.ajax({
			url: pag + "/inserir.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {

				$('#mensagem').removeClass()

				if (mensagem.trim() == "Salvo com Sucesso!") {
                	//$('#btn-fechar').click();
                    //window.location = "index.php?pagina="+pag;
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




<script type="text/javascript">
	$('#form-aba3').submit(function(event) {
    event.preventDefault(); // Interrompe o envio padrão do formulário.
    var formData = new FormData(this); // 'this' refere-se ao formulário.

    $.ajax({
			url: "agenda_disponibilidades/inserir3.php",
			type: "POST",
        data: formData,
        processData: false, // Impede que o jQuery transforme os dados em string de consulta.
        contentType: false, // Impede que o jQuery defina um tipo de conteúdo incorreto; o navegador irá definir o tipo de conteúdo adequado para FormData.
        success: function(response) {
			// Primeiro, verifique se 'response' é uma string.
			if (typeof response === 'string') {
				// Agora é seguro usar 'trim()' porque 'response' é uma string.
				if (response.trim() === "Salvo com Sucesso!") {
					$('#btn-fechar').click();
                    window.location = "index.php?pagina="+pag;
				}
			} else {
				// Se 'response' não for uma string, você precisa tratar diferentemente.
				// Talvez você precise fazer 'JSON.parse' ou investigar mais o que é 'response'.
				console.log("Resposta não é uma string", response);
			}
		},
        error: function(xhr, status, error) {
            // Lógica de erro, pode colocar um console.log aqui para ver o erro.
            console.error(error);
        }
    });
});
</script>




<!--SCRIPT PARA CARREGAR IMAGEM -->
<script type="text/javascript">
	// para substuir o clique no botao pelo clique na imagem-->
	document.getElementById("img-foto_agenda").addEventListener("click", function() {
	document.getElementById("input-foto_agenda").click();
	});
	function carregarImg() {

		var target = document.getElementById('img-foto_agenda');
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


</body>