<?php
// Início do script PHP
//$pag = 'financeiro';
$pag = 'agenda_disponibilidades';
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
require_once ('../personalizacoes/personalizacao_agenda.php');
require_once ('../personalizacoes/personalizacao_sistema.php');



$corFontePad = $cor_fonte_profissional;
$corFundPad = $cor_fundo_profissional;
$idContr = isset($_POST['id']) ? $_POST['id'] : 0; 
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : "sem tipo";


$pasta= $_SESSION['x_url'];






function montarDetalhesProfissional(PDO $pdo, int $idContr, string $corFundPadN, string $corFontePadN) {
	$pasta= $_SESSION['x_url'];
    // 1) Busca contrato (com id_colaborador)
    $stmt = $pdo->prepare(
        "SELECT id, id_colaborador, nome, nome_agenda, ativo_agenda, foto_agenda, 
                cor_fundo_agenda, cor_fonte_agenda, descricao_agenda, ordem_agenda, especialidade_agenda
         FROM colaboradores_contratos
         WHERE id = :id"
    );
    $stmt->execute([':id' => $idContr]);
    $contrato_prof = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$contrato_prof) { return null; }

    $id_profissionalN = (int)$contrato_prof['id_colaborador'];

    // 2) Busca cadastro do colaborador (AGORA incluindo nome_agenda!)
    $stmt2 = $pdo->prepare(
        "SELECT id, nome, nome_agenda, ativo_agenda, foto_agenda, foto_cadastro, foto_sistema,
                cor_fundo_agenda, cor_fonte_agenda, descricao_agenda, ordem_agenda, especialidade_agenda
         FROM colaboradores_cadastros
         WHERE id = :id"
    );
    $stmt2->execute([':id' => $id_profissionalN]);
    $cadastro_prof = $stmt2->fetch(PDO::FETCH_ASSOC) ?: [];

    $deveSalvarN = false;

    // 3) Nome de agenda
    if (!empty($contrato_prof['nome_agenda'])) {
        $nome_agendaN = $contrato_prof['nome_agenda'];
    } elseif (!empty($cadastro_prof['nome_agenda'])) {
        $nome_agendaN = $cadastro_prof['nome_agenda']; $deveSalvarN = true;
    } elseif (!empty($cadastro_prof['nome'])) {
        $partes = explode(' ', trim($cadastro_prof['nome']));
        $nome_agendaN = $partes[0]; $deveSalvarN = true;
    } else {
        $nome_agendaN = 'primeiro do nome'; $deveSalvarN = true;
    }

    // 4) Nome completo
    if (!empty($contrato_prof['nome'])) {
        $nomeN = $contrato_prof['nome'];
    } elseif (!empty($cadastro_prof['nome'])) {
        $nomeN = $cadastro_prof['nome']; $deveSalvarN = true;
    } else {
        $nomeN = ''; $deveSalvarN = true;
    }

    // 5) Ativo agenda
    if (isset($contrato_prof['ativo_agenda'])) {
        $ativo_agendaN = $contrato_prof['ativo_agenda'];
    } elseif (isset($cadastro_prof['ativo_agenda'])) {
        $ativo_agendaN = $cadastro_prof['ativo_agenda']; $deveSalvarN = true;
    } else {
        $ativo_agendaN = 0; $deveSalvarN = true; // default mais seguro
    }

    // 6) Fotos – inicializa defaults para evitar “undefined”
    $foto_agendaN = 'sem-foto.svg';
    $fotoCaminhoAgendaN = 'sem-foto.svg';
    $nova_foto_agendaN = null;

    $fotoContratoAgendaExisteN = !empty($contrato_prof['foto_agenda']) && is_file('../../'.$pasta.'/img/cadastro_colaboradores/'.$contrato_prof['foto_agenda']);
    $fotoCadastroAgendaExisteN = !empty($cadastro_prof['foto_agenda']) && is_file('../../'.$pasta.'/img/cadastro_colaboradores/'.$cadastro_prof['foto_agenda']);
    $fotoCadastroExisteN       = !empty($cadastro_prof['foto_cadastro']) && is_file('../../'.$pasta.'/img/cadastro_colaboradores/'.$cadastro_prof['foto_cadastro']);
    $fotoSistemaExisteN        = !empty($cadastro_prof['foto_sistema'])   && is_file('../../'.$pasta.'/img/users/'.$cadastro_prof['foto_sistema']);

    if ($fotoContratoAgendaExisteN) {
        $foto_agendaN = $contrato_prof['foto_agenda'];
        $fotoCaminhoAgendaN = 'cadastro_colaboradores/'.$foto_agendaN;
    } elseif ($fotoCadastroAgendaExisteN) {
        $foto_agendaN = $cadastro_prof['foto_agenda'];
        $fotoCaminhoAgendaN = 'cadastro_colaboradores/'.$foto_agendaN;
        $nova_foto_agendaN = 'cadastro_foto_agenda'; $deveSalvarN = true;
    } elseif ($fotoSistemaExisteN) {
        $foto_agendaN = $cadastro_prof['foto_sistema'];
        $fotoCaminhoAgendaN = 'users/'.$foto_agendaN;
        $nova_foto_agendaN = 'cadastro_foto_sistema'; $deveSalvarN = true;
    } elseif ($fotoCadastroExisteN) {
        $foto_agendaN = $cadastro_prof['foto_cadastro'];
        $fotoCaminhoAgendaN = 'cadastro_colaboradores/'.$foto_agendaN;
        $nova_foto_agendaN = 'cadastro_foto'; $deveSalvarN = true;
    }
    $foto_cadastroN = $cadastro_prof['foto_cadastro'] ?? '';
    $foto_sistemaN  = $cadastro_prof['foto_sistema']  ?? '';

    // 7) Cores
    $cor_fundo_agendaN = !empty($contrato_prof['cor_fundo_agenda']) ? $contrato_prof['cor_fundo_agenda']
                          : (!empty($cadastro_prof['cor_fundo_agenda']) ? $cadastro_prof['cor_fundo_agenda'] : $corFundPadN);
    if (empty($contrato_prof['cor_fundo_agenda']) && empty($cadastro_prof['cor_fundo_agenda'])) $deveSalvarN = true;

    $cor_fonte_agendaN = !empty($contrato_prof['cor_fonte_agenda']) ? $contrato_prof['cor_fonte_agenda']
                          : (!empty($cadastro_prof['cor_fonte_agenda']) ? $cadastro_prof['cor_fonte_agenda'] : $corFontePadN);
    if (empty($contrato_prof['cor_fonte_agenda']) && empty($cadastro_prof['cor_fonte_agenda'])) $deveSalvarN = true;

    // 8) Descrição, ordem, especialidade
    $descricao_agendaN   = !empty($contrato_prof['descricao_agenda']) ? $contrato_prof['descricao_agenda'] : ($cadastro_prof['descricao_agenda'] ?? '');
    $ordem_agendaN       = isset($contrato_prof['ordem_agenda']) ? (int)$contrato_prof['ordem_agenda'] : (int)($cadastro_prof['ordem_agenda'] ?? 0);
    $especialidade_agendaN = !empty($contrato_prof['especialidade_agenda']) ? $contrato_prof['especialidade_agenda'] : ($cadastro_prof['especialidade_agenda'] ?? '');

    if (empty($contrato_prof['descricao_agenda']) && !empty($cadastro_prof['descricao_agenda'])) $deveSalvarN = true;
    if (!isset($contrato_prof['ordem_agenda']) && isset($cadastro_prof['ordem_agenda'])) $deveSalvarN = true;
    if (empty($contrato_prof['especialidade_agenda']) && !empty($cadastro_prof['especialidade_agenda'])) $deveSalvarN = true;

    return [
        'nome_agenda'        => $nome_agendaN,
        'nome'               => $nomeN,
        'cor_fundo_agenda'   => $cor_fundo_agendaN,
        'cor_fonte_agenda'   => $cor_fonte_agendaN,
        'descricao_agenda'   => $descricao_agendaN,
        'ordem_agenda'       => $ordem_agendaN,
        'especialidade_agenda'=> $especialidade_agendaN,
        'foto_agenda'        => $foto_agendaN,
        'foto_cadastro'      => $foto_cadastroN,
        'foto_sistema'       => $foto_sistemaN,
        'deve_salvar'        => $deveSalvarN,
        'foto_caminho_agenda'=> $fotoCaminhoAgendaN,
        'nova_foto_agenda'   => $nova_foto_agendaN,
        'ativo_agenda'       => $ativo_agendaN,
        'id_profissional'    => $id_profissionalN
    ];
}












if ($idContr > 0) {
    $titulo_modal = 'Editar Registro';

    $stmtI = $pdo->prepare("SELECT id_colaborador FROM colaboradores_contratos WHERE id = :id");
    $stmtI->execute([':id' => $idContr]);
    $idProfRow = $stmtI->fetch(PDO::FETCH_ASSOC);
    if (!$idProfRow) { die("Contrato não encontrado."); }

    $detalhesProf = montarDetalhesProfissional($pdo, $idContr, $corFundPad, $corFontePad);





    if (!$detalhesProf) { die("Falha ao montar detalhes do profissional."); }

    // destructuring (como você já faz)
    $nome_agenda       = $detalhesProf['nome_agenda'];
    $nome              = $detalhesProf['nome'];
    $cor_fundo_agenda  = $detalhesProf['cor_fundo_agenda'];
    $cor_fonte_agenda  = $detalhesProf['cor_fonte_agenda'];
    $descricao_agenda  = $detalhesProf['descricao_agenda'];
    $ordem_agenda      = $detalhesProf['ordem_agenda'];
    $especialidade_agenda = $detalhesProf['especialidade_agenda'];
    $foto_agenda       = $detalhesProf['foto_agenda'];
    $foto_cadastro     = $detalhesProf['foto_cadastro'];
    $foto_sistema      = $detalhesProf['foto_sistema'];
    $fotoCaminhoAgenda = $detalhesProf['foto_caminho_agenda'];
    $nova_foto_agenda  = $detalhesProf['nova_foto_agenda'];
    $ativo_agenda      = $detalhesProf['ativo_agenda'];
    $id_profissional   = $detalhesProf['id_profissional'];

    // Consulta de serviços (mantém sua lógica, mas OK de performance/segurança)
    $sql = "SELECT s.id, s.servico, s.categoria, 
                   COALESCE(sp.tempo, s.tempo) AS tempo, 
                   COALESCE(sp.preco, s.valor_venda) AS valor_venda, 
                   COALESCE(sp.comissao, s.comissao) AS comissao, 
                   CASE WHEN s.agendamento_online <> 1 THEN 2 
                        ELSE COALESCE(sp.agendamento_online, s.agendamento_online) 
                   END AS agendamento_online, 
                   COALESCE(sp.executa, s.executa) AS executa
            FROM servicos s
            LEFT JOIN servicos_profissional sp 
              ON s.id = sp.id_servico 
             AND sp.id_contrato = :idContr 
             AND sp.executa = 1
            WHERE s.excluido <> 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':idContr' => $idContr]);
    $serv = $stmt->fetchAll(PDO::FETCH_ASSOC);

	
} else {
    $query = $pdo->query("SELECT id, nome 
                          FROM colaboradores_contratos 
                          WHERE ativo = 1 AND ativo_agenda <> 1");
    $profs_agenda = $query->fetchAll(PDO::FETCH_ASSOC);

    $selectProfissionalProfs = '<select class="form-select selectProfAgProf" id="frm-nome" name="frm-nome">
    <option selected data-idcontr_prof="0"> Selecione o novo profissional</option>';

    foreach ($profs_agenda as $prof_agenda) {
        $selectProfissionalProfs .= '<option value="' . htmlspecialchars($prof_agenda['nome']) . 
                                    '" data-idcontr_prof="' . (int)$prof_agenda['id'] . '">' . 
                                    htmlspecialchars($prof_agenda['nome']) . '</option>';
    }
    $selectProfissionalProfs .= '</select>';

    $titulo_modal = 'Inserir Profissional na Agenda';
    $NovoAgenda = true;

    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE excluido <> true ORDER BY categoria ASC");
    $stmt->execute();
    $serv = $stmt->fetchAll(PDO::FETCH_ASSOC); // **consistência**

    // Monta detalhes de cada contrato
    $profsDetalhes = [];
    foreach ($profs_agenda as $prof) {
        $id_contrato = (int)$prof['id'];
        $detalhes = montarDetalhesProfissional($pdo, $id_contrato, $corFundPad, $corFontePad);
        if ($detalhes) {
            $profsDetalhes[$id_contrato] = $detalhes;
        }
    }
}



?>



<div class="modal fade" tabindex="-1" id="modalCadastrar" data-bs-backdrop="static"  style = "z-index: 16000; ">
	<div class="modal-dialog modal-lg" >
		<div class="modal-content" >
		

		
			<div class="modal-header">
				<h3 class="modal-title"> <?php echo $titulo_modal . ' ' . $tipo ?></h3>
							
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
														<label class="form-label">Foto na Agenda</label>
														<div class="mt-2" id="bloco_cor_agenda" style="background-color: <?= ($cor_fundo_agenda) ? $cor_fundo_agenda : $corFundPad ?>; width: 150px; height:180px; border-radius: 15px;">
															<!-- Foto clicável -->
															<img
																style="border-radius:50%; cursor:pointer; margin-top:10px; width: 130px;"
																src="<?= (@$foto_agenda) ?'../'.$pasta.'/img/'. $fotoCaminhoAgenda : '../img/sem-foto.svg'; ?>"
																id="img-foto_agenda"
																alt="Foto Profissional Agenda"
															>
															<p id="p_nome_agenda" style="font-weight:500; color: <?= ($cor_fonte_agenda) ? $cor_fonte_agenda : $corFontePad ?> "><?= $nome_agenda ?></p>
															<input type="hidden" name="foto-agenda" id="foto-agenda-aba1" value="<?= $foto_agenda ?>">
															<input type="hidden" name="nova-foto-agenda" id="nova-foto-agenda-aba1" value="<?= $nova_foto_agenda ?>">
															<!-- input file escondido -->
															<input type="file" style="display:none;" accept="image/*" id="input-foto_agenda" name="input-foto_agenda">
														</div>
													</div>
													<!-- Área de crop (só aparece ao selecionar imagem) -->
													<div id="cropper-area-agenda" style="display:none; margin-top:15px;">
														<img id="preview-crop-agenda" style="max-width: 250px; max-height:250px; border-radius:8px; border:1px solid #ddd;">
														<br>
														<button type="button" id="btn-crop-ok-agenda" class="btn btn-primary btn-sm" style="margin-top:8px;">Usar esta foto</button>
														<button type="button" id="btn-crop-cancel-agenda" class="btn btn-secondary btn-sm" style="margin-top:8px;">Cancelar</button>
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
														<?php
														if ($NovoAgenda){
															echo $selectProfissionalProfs;
														}else{
															echo'
														<input type="text" style="border: 1px solid #F7F7F7; padding: 3px; min-width: 380px;" readonly  id="frm-nome" name="frm-nome"  required value="'. $nome.'">';
														}?>
													
													</div> 



												</div>
												<div class="col-md-3">

												</div>
												<div class="col-md-2">
													<div id="divImgConta" >
														
															<img style="border-radius:50%;" src="<?=($foto_cadastro)? '../'.$pasta.'/img/cadastro_colaboradores/' . $foto_cadastro : '../img/sem-foto.svg' ?>"  width="60px" id="frm-foto_cadastro">
															<input  type="hidden" name="foto-cadastro" value="<?=$foto_cadastro ?>">
													
													</div>
												</div>
										</div>
										<div class="row">
												<div class="col-md-5">
													<div class="mb-3">
														<label for="frm-nome_agenda" class="form-group">Nome na Agenda</label>
														<input type="text" class="form-control"  id="frm-nome_agenda" name="frm-nome_agenda"  required value="<?= $nome_agenda  ?>">
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

									<input type="hidden" name="frm-id"  id="frm-id" value="<?php echo @$_POST['id'] ?>">
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
						
						<div>
							
							<div class="container-md">
								<div class="row mb-2">

									<div class="input-group mb-2">
										
										<input type="text" class="form-control" id="searchInput" style="max-width: 350px;"onkeyup="filterTable()" placeholder="Buscar serviço ou categoria">
										<input type="hidden" name="id-contrato_aba3" value="<?= $idContr ?>">
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
														<input type="hidden" id="id_prof3" name="id_prof3" value="<?= $id_profissional?>"> <!-- id do profissional -->
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
								<div id="mensagemRodape3" class="text-start small text-muted">
									
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
var detalhesProfs = <?= json_encode($profsDetalhes); ?>;
var pasta = <?= json_encode($pasta); ?>;
console.log('detalhes profissionais aqui agora', detalhesProfs);

var selectProfissionalProfs = document.getElementById('frm-nome');
	

    selectProfissionalProfs.addEventListener('change', function() {
		console.log('ALTEROU O PROFISSIOLAL');
		console.log('arry de detalhes; ', detalhesProfs);
        
        const idContr = this.options[this.selectedIndex].getAttribute('data-idcontr_prof');
		//data-idcontr_prof
        const detalhes = detalhesProfs[idContr];

        if (!detalhes) {
            console.error("Detalhes não encontrados para o ID: ", idContr);
            return;
        }

		document.getElementById('frm-id').value = idContr;
        // Atualiza nome_agenda
        document.getElementById('frm-nome_agenda').value = detalhes.nome_agenda;

        // Atualiza nome (hidden ou input visível)
        //const nomeInput = document.getElementById('frm-nome');
        //if(nomeInput) nomeInput.value = detalhes.nome;

        // Atualiza especialidade_agenda
        document.getElementById('frm-especialidade_agenda').value = detalhes.especialidade_agenda;

        // Atualiza ordem_agenda
        document.getElementById('frm-ordem_agenda').value = detalhes.ordem_agenda;

        // Atualiza descricao_agenda
        document.getElementById('frm-descricao_agenda').value = detalhes.descricao_agenda;

        // Atualiza cores (fundo e fonte)
        document.getElementById('cor_fundo_agenda').value = detalhes.cor_fundo_agenda;
        document.getElementById('cor_fonte_agenda').value = detalhes.cor_fonte_agenda;

        document.getElementById('bloco_cor_agenda').style.backgroundColor = detalhes.cor_fundo_agenda;

        document.getElementById('p_nome_agenda').style.color = detalhes.cor_fonte_agenda;
		document.getElementById('p_nome_agenda').textContent = detalhes.nome_agenda;
        // Atualiza foto
        document.getElementById('img-foto_agenda').src = (detalhes.foto_caminho_agenda!='sem-foto.svg')
            ? '../'+pasta+'/img/' + detalhes.foto_caminho_agenda 
            : '../img/sem-foto.svg';

			document.getElementById('foto-agenda-aba1').value = detalhes.foto_agenda;
			document.getElementById('nova_foto-agenda-aba1').value = detalhes.nova_foto_agenda

        // Atualiza foto cadastro menor (caso exista esse elemento)
        if (document.getElementById('frm-foto_cadastro')) {
            document.getElementById('frm-foto_cadastro').src = detalhes.foto_agenda 
                ? '../'+pasta+'/img/cadastro_colaboradores/' + detalhes.foto_agenda 
                : '../img/sem-foto.svg';
        }

        document.getElementById('id_prof3').value = detalhes.id_profissional;

    });

    // Se precisar, pode forçar o primeiro elemento do select já disparar o evento no load:
    selectProfissionalProfs.dispatchEvent(new Event('change'));

	
</script>


<script>
var tipo= '<?php echo $tipo; ?>';





console.log(tipo);



(function (){    
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
})();

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
if(@$_POST['funcao'] == "editar"){ ?>
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

		
		idGrava= document.getElementById('frm-id').value;


		var formData = new FormData(this);

		$.ajax({
			url: pag + "/inserir.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {

				$('#mensagem').removeClass()

				if (mensagem.trim() == "Salvo com Sucesso!") {
                	$('#btn-fechar').click();
					
					if(tipo=='agenda'){                        
						trocaDia=false;
						updateProfissionais(); 
                        carregarAgenda(dataCalend, agCancelados);
						
					}else{
						reloadDataTable();
					}


                    
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
					
					if(tipo=='agenda'){                        
						trocaDia=false;
                        carregarAgenda(dataCalend, agCancelados);
					}else{
						reloadDataTable();
					}
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



function croopAgenda(){
	let cropperAgenda = null;

			// Clique na foto dispara o file picker
			document.getElementById("img-foto_agenda").addEventListener("click", function() {
				document.getElementById("input-foto_agenda").click();
			});

			// Ao selecionar imagem, exibe área de crop
			document.getElementById("input-foto_agenda").addEventListener("change", function(e) {
				const file = e.target.files[0];
				if (!file) return;

				const cropperArea = document.getElementById('cropper-area-agenda');
				cropperArea.style.display = 'block';

				const preview = document.getElementById('preview-crop-agenda');
				preview.src = URL.createObjectURL(file);

				if (cropperAgenda) cropperAgenda.destroy();

				preview.onload = function() {
					cropperAgenda = new Cropper(preview, {
						aspectRatio: 1,
						viewMode: 1,
						autoCropArea: 1,
						responsive: true
					});
				}
			});

			// Botão "Usar esta foto"
			document.getElementById("btn-crop-ok-agenda").addEventListener("click", function() {
				if (!cropperAgenda) return;
				const canvas = cropperAgenda.getCroppedCanvas({ width: 400, height: 400, imageSmoothingQuality: 'high' });

				canvas.toBlob(function(blob) {
					// Atualiza preview final
					const url = URL.createObjectURL(blob);
					document.getElementById('img-foto_agenda').src = url;

					// Cria novo File para anexar no form
					const newFile = new File([blob], "foto_agenda.jpg", { type: "image/jpeg" });

					// Substitui no input file via DataTransfer
					const dataTransfer = new DataTransfer();
					dataTransfer.items.add(newFile);
					document.getElementById('input-foto_agenda').files = dataTransfer.files;

					// Esconde área de crop
					document.getElementById('cropper-area-agenda').style.display = 'none';

					// Limpa cropper
					cropperAgenda.destroy();
					cropperAgenda = null;
				}, 'image/jpeg', 0.8);
			});

			// Botão cancelar crop
			document.getElementById("btn-crop-cancel-agenda").addEventListener("click", function() {
				document.getElementById('cropper-area-agenda').style.display = 'none';
				if (cropperAgenda) { cropperAgenda.destroy(); cropperAgenda = null; }
				document.getElementById('input-foto_agenda').value = '';
			});
}

croopAgenda();

</script>


