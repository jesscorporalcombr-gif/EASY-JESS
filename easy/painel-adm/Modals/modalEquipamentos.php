<?php
//modals/modalEquipamentos.php
$pag = 'equipamentos';
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
$pasta = $_SESSION['x_url'] ?? '';

$__salas = $pdo->query("
  SELECT id, nome
  FROM salas
  WHERE (excluido IS NULL OR excluido = 0)
  ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);



if(isset($_POST['id']) && !empty($_POST['id'])) {
    $id_equipamento = $_POST['id'];

    $query = $pdo->prepare("SELECT * FROM equipamentos WHERE id = :id");
    $query->execute([':id' => $id_equipamento]);
    $equipamento = $query->fetchAll(PDO::FETCH_ASSOC);
   

   $nome                = $equipamento[0]['nome']                ?? '';
    $foto_edit           = $equipamento[0]['foto']                ?? '';
    $descricao           = $equipamento[0]['descricao']           ?? '';
    $ag_paralelo         = isset($equipamento[0]['ag_paralelo']) ? (int)$equipamento[0]['ag_paralelo'] : 0;

    $marca               = $equipamento[0]['marca']               ?? '';
    $modelo              = $equipamento[0]['modelo']              ?? '';
    $anvisa              = $equipamento[0]['anvisa']              ?? '';
    $patrimonio          = isset($equipamento[0]['patrimonio'])  ? (int)$equipamento[0]['patrimonio']  : 1; // 1=próprio, 0=terceiro

    $data_compra         = $equipamento[0]['data_compra']         ?? '';
    $nota_fiscal_compra  = $equipamento[0]['nota_fiscal_compra']  ?? '';
    $numero_serie        = $equipamento[0]['numero_serie']        ?? ''; // se ainda não tiver no BD, pode criar depois
    $data_ultima_revisao = $equipamento[0]['data_ultima_revisao'] ?? '';
    $data_proxima_revisao= $equipamento[0]['data_proxima_revisao']?? '';

    $pag_fabricante      = $equipamento[0]['pag_fabricante']      ?? '';
    $site_referencia     = $equipamento[0]['site_referencia']     ?? '';

    $excluido            = isset($equipamento[0]['excluido']) ? (int)$equipamento[0]['excluido'] : 0;



    



        if ($excluido=='0'){
            $situacao='Ativa';
            $statusSit= 'status-ativo';
        }else{
            $situacao='Deletado';
            $statusSit= 'status-deletado';
        }



        $titulo_modal = $nome. '<span class="status-serv ' . $statusSit . '">' . $situacao . '</span>';
    


 $tipo_cadastro = "edit";
     
  
} else {
  $titulo_modal = "Novo Equipamento";
  $tipo_cadastro = "novo";
  // Como é um novo cadastro, os campos do formulário ficariam vazios
}

// Função para buscar os agendamentos



?>    


<div class="modal fade" tabindex="-1" style ="z-index: 95000;" id="modalCadEquipamento" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl modal-easy-xl">
        <div class="modal-content" >
            <div class="modal-header d-flex align-items-center">
            <!-- Wrapper de imagem + título -->
            <div class="d-flex align-items-center">
                <img
                src="<?= (@$foto_edit ? '../'.$pasta.'/img/equipamentos/'. $foto_edit  : '../img/sem-imagem.svg') ?>"
                id="img-foto_head"
                alt="Foto do Equipamento"
                class="rounded-circle me-2"
                style="cursor: pointer; height: 35px; width: 35px;"
                >
                <h5 class="modal-title mb-0"><?= $titulo_modal ?></h5>
            </div>

            <!-- Botão de fechar, empurrado ao máximo -->
            <button
                type="button"
                class="btn-close ms-auto"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
            </div>
            <form method="POST" id="formCadEquipamento" enctype="multipart/form-data">
                <div class="modal-body">
                                        
                    <ul style="<?= ($tipo_cadastro == "novo" ? 'display:none;' : '') ?>"  class="nav nav-tabs nav-tabs-md-easy-xl" id="v-tab" role="tablist">

                    
                        <li class="nav-link active tab-btn" id="cadastro-tab"  data-bs-toggle="tab" data-bs-target="#aba-cadastro" role="tab" aria-controls="cadastro" aria-selected="true">
                        Cadastro
                        </li>
                    
                        <li class="nav-link tab-btn "  id="servicos-tab" data-bs-toggle="tab" data-bs-target="#aba-servicos" role="tab" aria-controls="servicos" aria-selected="false">
                        Serviços
                        </li>
                      
                        <li class="nav-link tab-btn" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#aba-documentos" role="tab" aria-controls="documentos" aria-selected="false">
                        Documentos
                        </li>

                        
                        <li class="nav-link tab-btn" id="imagens-tab" data-bs-toggle="tab" data-bs-target="#aba-imagens" role="tab" aria-controls="imagens" aria-selected="false">
                        Imagens
                        </li>

                        <!-- Adicione outras abas conforme necessário -->
                    
                    </ul>
                        
                    <div class="tab-content mt-3" id="v-tabContent"> <!-- conteudo das tabs -->
                        <!-- TAB CADASTRO -->
                        <div class="tab-pane fade show active" id="aba-cadastro" role="tabpanel" aria-labelledby="cadastro-tab">
                            <div class="row mt-2">
                                <input type="hidden" id="frm-id" name="id" value="<?=$id_equipamento?>">
                                <div class="col-auto" style="min-width: 200px;">
                                    <!--<label for="img-foto_cad" >Foto</label>-->
                                    <input type="file"  accept="image/*" style="display:none;" class="form-control-file" id="input-foto_cadEquipamento" name="input-foto_cadEquipamento" onChange="carregarImg();">
                                            
                                    <div id="capdivImgConta" class="mt-3">
                                        <div id="divImgConta2"  style="padding-left: 15px;" >
                                            <img 
                                            style="margin-left:5px; border-radius:15px; width: 150px;"
                                            src="<?= (@$foto_edit ? '../'. $pasta.'/img/equipamentos/'. $foto_edit  : '../img/sem-imagem.svg') ?>"
                                            id="img-foto_cadEquipamento"
                                            name="img-foto_cadEquipamento"
                                            alt="Foto do Equipamento">
                                        </div>

                                        <!-- Área para crop (aparece só quando usuário seleciona uma imagem) -->
                                        <div id="cropper-area" style="display:none; margin-top:15px;">
                                            <button type="button" id="btn-crop-ok" class="btn btn-primary btn-sm mb-3" style="margin-top:8px;">Usar esta foto</button>
                                            <button type="button" id="btn-crop-cancel" class="btn btn-secondary btn-sm mb-3" style="margin-top:8px;">Cancelar</button>
                                            <img id="preview-crop" style=" max-height: 300px; border-radius:8px; border:1px solid #ddd;">
                                            <br>
                                            
                                        </div>

                                    </div>
                                    <!-------------------------------------------------------------------------------->   
                                    <div id="webcam-area" style="display:none; margin-top:15px;">
                                        <video id="webcam" width="300" height="225" autoplay style="border-radius:12px; border:1px solid #aaa;"></video>
                                        <br>
                                        <button type="button" id="btn-capturar" class="btn btn-success btn-sm" style="margin-top:10px;">Capturar Foto</button>
                                        <button type="button" id="btn-cancelar-webcam" class="btn btn-secondary btn-sm" style="margin-top:10px;">Cancelar</button>
                                    </div>
                                    <!-------------------------------------------------------------------------------->
                                </div>

                                <div class="col  mt-3" style="min-width: 420px;">
                                    <h3>Dados Principais</h3>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-group">Nome do Equipamento</label>
                                            <input type="text" class="form-control" id="frm-nome" style="max-width: 350px;" name="frm-nome" required value="<?= htmlspecialchars($nome) ?>">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-group">Marca</label>
                                            <input type="text" class="form-control" id="frm-marca" name="frm-marca" value="<?= htmlspecialchars($marca) ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-group">Modelo</label>
                                            <input type="text" class="form-control" id="frm-modelo" name="frm-modelo" value="<?= htmlspecialchars($modelo) ?>">
                                        </div>

                                        <div class="col-md-5">
                                            <label class="form-group">Registro ANVISA</label>
                                            <input type="text" class="form-control" id="frm-anvisa" name="frm-anvisa" value="<?= htmlspecialchars($anvisa) ?>">
                                        </div>
                                    </div>
                                        
                                </div>
                            </div>           
<hr>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-group d-block mb-1"><b>Este equipamento é patrimônio da clínica?</b></label>
                                    <span class="spanRadio me-3">
                                        <input type="radio" id="frm-patrimonio_1" name="frm-patrimonio" value="1" <?= $patrimonio==1?'checked':''; ?>> Sim (próprio)
                                    </span>
                                    <span class="spanRadio">
                                        <input type="radio" id="frm-patrimonio_0" name="frm-patrimonio" value="0" <?= $patrimonio==0?'checked':''; ?>> Não (terceiro/alugado)
                                    </span>
                                </div>
                            </div>
                            <div id="grupo-patrimonio" class="mt-3" style="<?= $patrimonio==1 ? '' : 'display:none;' ?>">
                                <h5 class="mb-4">Dados de Patrimônio</h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-group">Data da Compra</label>
                                        <input type="date" class="form-control" id="frm-data_compra" name="frm-data_compra" value="<?= htmlspecialchars($data_compra) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-group">Nota Fiscal (nº)</label>
                                        <input type="text" class="form-control numero-inteiro" id="frm-nota_fiscal_compra" name="frm-nota_fiscal_compra" value="<?= htmlspecialchars($nota_fiscal_compra) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-group">Número de Série</label>
                                        <input type="text" class="form-control" id="frm-numero_serie" name="frm-numero_serie" value="<?= htmlspecialchars($numero_serie) ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-group">Página do Fabricante</label>
                                        <input type="url" class="form-control" id="frm-pag_fabricante" name="frm-pag_fabricante" placeholder="https://..." value="<?= htmlspecialchars($pag_fabricante) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-group">Site de Referência / Manual</label>
                                        <input type="url" class="form-control" id="frm-site_referencia" name="frm-site_referencia" placeholder="https://..." value="<?= htmlspecialchars($site_referencia) ?>">
                                    </div>
                                </div>
                            </div>        
                                
<hr>
                            <div class="row">
                                <h3 class="mb-0">Descrição do Equipamento:</h3>
                                <div class="col" >
                                        
                                        <textarea style="min-height:100px;" class="form-control" id="frm-descricao" name="frm-descricao"> <?= $descricao ?></textarea>
                                    
                                </div>
                                       
                               
                            </div>
<hr>
                            <div class="row" style="margin-left: 15px;" > 
                                <div class="col">
                                    <label class="form-group d-block mb-1"><b>Agendamento em Paralelo</b></label>
                                    <span class="spanRadio me-3">
                                            <input type="radio" id="frm-paralelo_0" name="frm-paralelo" value="0" <?= $ag_paralelo==0?'checked':''; ?>> Não
                                        </span>
                                    <span class="spanRadio me-3">
                                        <input type="radio" id="frm-paralelo_1" name="frm-paralelo" value="1" <?= $ag_paralelo==1?'checked':''; ?>> Sim
                                    </span>
                                    <span class="spanRadio">
                                        <input type="radio" id="frm-paralelo_2" name="frm-paralelo" value="2" <?= $ag_paralelo==2?'checked':''; ?>> Com paralelos
                                    </span>
                                </div>
                            </div>
<hr>
                            <h5 class="mb-4">Manutenções / Revisões</h5>
                            <div class="row g-3" style="margin-left:25px;">
                                <div class="col-auto">
                                    <label class="form-group">Data da Última Revisão</label>
                                    <input type="date" class="form-control" style="width: 150px;" id="frm-data_ultima_revisao" name="frm-data_ultima_revisao" value="<?= htmlspecialchars($data_ultima_revisao) ?>">
                                </div>
                                <div class="col-auto">
                                    <label class="form-group">Próxima Revisão</label>
                                    <input type="date" class="form-control" style="width: 150px;"  id="frm-data_proxima_revisao" name="frm-data_proxima_revisao" value="<?= htmlspecialchars($data_proxima_revisao) ?>">
                                </div>
                            </div>
                            
<hr>




                            <div class="row">
                                <h5 class="mb-1">Status do Equipamento:</h5>
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <span class="spanRadio">
                                            <input type="radio" style ="margin-right: 5px;" id="frm-excluido" name="frm-excluido" value="0" <?= $excluido==0 ? 'checked' : ''; ?>>Ativo
                                        </span>
                                        <span class="spanRadio" style="margin-left: 10px;">
                                            <input type="radio" style ="margin-right: 5px;" id="frm-excluido_1" name="frm-excluido" value="1" <?= $excluido==1 ? 'checked' : ''; ?>>Deletado
                                        </span>
                                    </div>  
                                </div>
                            </div>
                            


                         
                            
                        </div>     <!-- fechamento da tab 1-->

                        


                        <!-- TAB SEERVICOS Inicio da TaB 3-->
                        <div class="tab-pane fade" id="aba-servicos" role="tabpanel" aria-labelledby="servicos-tab">
                             <h4 class="pt-2" style="margin-left: 20px; text-align:left;"> Serviços que utilizam esta equipamento:</h4>
                            <div class="container-md listTabContainer mt-3">
                                <div class="table-containerServ" id="servicos-container-modal">
                                    <div class="row mb-2">
                                        <div class="col-auto">
                                            <input type="text" style="min-width: 230px;"class="form-control searchBox" placeholder="Pesquisar Servico...">
                                        </div>
                                        <div class="col-auto">
                                            <select hidden class="form-select rowsPerPage" style="width: auto;">
                                                <option value="1000"></option>
                                            </select>
                                        </div>
                                    </div>
                                    <table 
                                        class="dataTable dTlinhaFina"
                                        id="tabelaAbaServ"
                                        data-table="servicos_equipamentos" data-minimized="false"
                                        
                                        style="width:100%"><!-- Substitua 123 pelo id_servico do servico atual via JS -->
                                        <thead>
                                        <tr>
                                            <th data-field="foto_servico" data-sort="a-z" data-foto></th>
                                            <th data-field="servico" data-sort="a-z" >Servico</th>
                                            <th data-field="categoria" data-sort="a-z" >Categoria</th>
                                            <th data-field="utiliza" data-sort="bool" data-sort-init="DESC" >executa</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Preenchido pelo JS -->
                                        </tbody>
                                    </table>
                                    <div hidden class="pagination mt-2"></div>
                                        <span class="info-range"></span>
                                    </div>
                             
                            </div>
                        </div>
                        
                        <!-- TAB Documentos Inicio da TaB 3 D O C U M E N T O S-->
                        <!-- Corrija o botão da aba -->
                        <!-- NOVA TAB: DOCUMENTOS -->
                        <div class="tab-pane fade" id="aba-documentos" role="tabpanel" aria-labelledby="documentos-tab">
                            <div class="container-md mt-3" id="documentos-container">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                <button type="button" class="btn btn-primary" id="btn-novo-doc">
                                    <i class="bi bi-file-earmark-plus"></i> Novo documento
                                </button>
                                <div class="ms-auto">
                                    <input type="text" class="form-control searchBox" style="width: 300px;" placeholder="Pesquisar...">
                                </div>
                                </div>

                                <!-- Form (inicialmente oculto) -->
                                <div id="form-doc" class="card p-3 mb-3" style="display:none;">
                                <input type="hidden" id="doc-id" value="">
                                <input type="hidden" id="doc-id_equipamento" value="<?= (int)$id_equipamento ?>">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                    <label class="form-group">Título</label>
                                    <input type="text" class="form-control"  id="doc-titulo" maxlength="200" >
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-secondary" id="doc-file-btn">
                                            <i class="bi bi-upload"></i> Escolher arquivo
                                            </button>
                                            <input type="file" class="form-control d-none" id="doc-file"
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.ppt,.pptx,.xls,.xlsx,.txt">
                                            <input type="text" class="form-control" id="doc-file-name" placeholder="Nenhum arquivo selecionado" readonly>
                                        </div>
                                        <small id="doc-file-current" class="text-muted" style="display:none;">
                                            Arquivo atual: <a href="#" target="_blank" id="doc-file-link"></a>
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-group">Tipo</label>
                                        <select id="doc-tipo" class="form-select">
                                            <option value="">Selecione...</option>
                                            <option value="Laudo">Laudo</option>
                                            <option value="Nota Fiscal">Nota Fiscal</option>
                                            <option value="Treinamento">Treinamento</option>
                                            <option value="Plano de Manutenção">Plano de Manutenção</option>
                                            <option value="Ocorrência">Ocorrência</option>
                                            <option value="Manual">Manual</option>
                                            <option value="Outros">Outros</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-group">Data do Arquivo</label>
                                        <input type="date" id="doc-data-arquivo" class="form-control">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-group">Descrição</label>
                                        <textarea id="doc-desc" class="form-control" rows="3" maxlength="2000"></textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <button type="button" class="btn btn-secondary" id="doc-cancelar">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                    <button type="button" class="btn btn-primary" id="doc-salvar">
                                    <i class="bi bi-save"></i> Salvar
                                    </button>
                                </div>
                                </div>

                                <!-- Tabela -->
                                <div class="table-container">
                                <table class="dataTable dTlinhaFina" id="tabela-documentos" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th style="width:40px;"></th>
                                        <th data-sort="a-z" style="width:150px;"data-sort-init="ASC">Título</th>
                                        <th data-sort="a-z" style="width:150px;">Tipo</th>
                                        <th data-sort="a-z"style="width:300px;">Descrição</th>
                                        <th data-sort="data" style="width:120px;">Data</th>
                                        <th data-sort="num" style="width:100px;">Tamanho</th>
                                        <th class="text-center" style="width:110px;">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody><!-- via JS --></tbody>
                                </table>
                                </div>
                            </div>
                        </div>

                        <!-- TAB GALERIA Inicio da TaB 5-->
                        <div class="tab-pane fade" id="aba-imagens" role="tabpanel" aria-labelledby="imagens-tab">
                           <div class="container-md mt-3">
                                <div class="container-foto-add p-3 mb-3" style="display:none; background-color:#f8f9fa;">
                                    <!-- Preview da imagem -->
                                    <div class="row">
                                        <div class="col-md-4 d-flex justify-content-center align-items-center">
                                            <img
                                                id="preview-foto"
                                                src="../img/sem-imagem.svg"
                                                alt="Preview Foto"
                                                class="img-thumbnail"
                                                style= "width:250px;
                                                        height:250px;
                                                        object-fit:contain;
                                                        object-position:center;
                                                        background:transparent;
                                                        border:none;
                                                        cursor:pointer;
                                                        "
                                            >                                
                                            <input
                                                type="file"
                                                id="fotoUpload"
                                                accept="image/*"
                                                style="display:none;"
                                            >
                                        </div>
                                        <!-- Campos do formulário -->
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="fotoTitulo" class="form-group"><i class="bi bi-card-text"></i> Título</label>
                                                <input type="text" id="fotoTitulo" class="form-control" placeholder="Título da foto">
                                            </div>
                                            <div class="mb-3 row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="fotoData" class="form-group"><i class="bi bi-calendar-event"></i> Data</label>
                                                    <input type="date" id="fotoData" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="fotoTipo" class="form-group"><i class="bi bi-list-ul"></i> Tipo de Foto</label>
                                                    <select id="fotoTipo" class="form-select">
                                                        <option value="">Selecione...</option>
                                                        <option value="Clínico">Clínico</option>
                                                        <option value="Antes e Depois">Antes e Depois</option>
                                                        <option value="Aleatório">Aleatório</option>
                                                        <option value="Redes Sociais">Redes Sociais</option>
                                                        <option value="Outras">Outras</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="fotoDescricao" class="form-group"><i class="bi bi-pencil-square"></i> Descrição</label>
                                                <textarea id="fotoDescricao" class="form-control" style="height:115px;" placeholder="Descrição da foto"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="mt-4" style="border-color:rgb(255, 255, 255);">
                                    <div class="row">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" id="btn-cancelar-foto" class="btn btn-secondary me-2">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="button" id="btn-salvar-foto" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Salvar Foto
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                 
                                <div class="table-container" id="galeria-container-modal">
                                    <div class="row mb-2">
                                        <div class="col-auto">
                                            <button 
                                                class="btn btn-primary" 
                                                type="button" 
                                                id="btn-nova-foto"
                                                title="Nova Foto"
                                                onclick="">
                                                <i class="bi bi-image"></i>Nova Foto +
                                            </button>
                                        </div>
                                        <div class="col-auto ms-auto d-flex align-items-center">
                                            <input type="text" class="form-control searchBox" style="width: 300px;" placeholder="Pesquisar...">
                                        </div>
                                    </div>

                                    <table 
                                        class="tablesModEquipamento dataTable dTlinhaFina"
                                        data-table="equipamentos_fotos"
                                        
                                         data-filtro='{
                                                        "logic":"AND", 
                                                        "conditions":[
                                                            {"field":"id_equipamento", "op":"=", "value":"<?= (int)$id_equipamento ?>"}
                                                            ]
                                                        }'
                                        style="width:100%"><!-- Substitua 123 pelo id_equipamento do equipamento atual via JS -->
                                        <thead>
                                        <tr>
                                            <th  
                                                data-field="arquivo_mini" 
                                                class="data-foto" 
                                                data-href-field="arquivo_ori"
                                                data-href-base="../<?=$pasta?>/img/equipamentos/galeria/" 
                                                data-foto="../<?=$pasta?>/img/equipamentos/galeria/mini/">Foto
                                            </th>
                                            <th data-field="id" data-sort-init="DESC" hidden>ID</th>
                                            <th data-field="arquivo_ori" hidden>–</th>
                                            <th data-field="titulo" data-sort="a-z">Título:</th>
                                            <th data-field="data_foto"  data-sort="data">Data</th>
                                            <th data-field="tipo_foto"  data-sort="a-z">Tipo de Foto</th>
                                            <th data-field="descricao">Descrição</th>
                                            <th data-field="acoes"   
                                                data-edit-func="abrirFormFoto"
                                                data-delete-func="excluirFoto"
                                                data-id-field="id"         
                                                class="text-center">Ações</th>
                                       
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Preenchido pelo JS -->
                                        </tbody>
                                    </table>
                                    <div class="row mt-2">
                                        <div class="col-auto">
                                            <select class="form-select rowsPerPage" style="width: 150px; height: 32px;">
                                                <option value="10">10 linhas por página</option>
                                                <option value="25">25 linhas por página</option>
                                                <option value="50">50 linhas por página</option>
                                            </select>
                                        </div>
                                        <div class="col">

                                            <div class="pagination "></div>
                                            
                                            <span style="font-size:0.9em;" class="info-range"></span>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                        </div>





                        
                        <!-- Adicionar outros painéis de conteúdo conforme necessário -->
                    </div>  <!--  FECHANDO O CONTEÚDO DAS TABS -->  
                </div><!--fechando o body do modal-->
                <div class="modal-footer">
                    <div class="footer-left">
                        <div>
                            <label class="form-group">
                            <?= (isset($cadastrado) && $cadastrado != '' ? 'cadastrado em : ' . date('d/m/Y', strtotime($cadastrado)) : '') ?> 
                             </label>
                        </div>
                        <div id="mensagem">

                        </div>
                        <div>
                        <button type="button" id="btn-editar-cadastro" class="btn btn-link" >Editar</button>
                        </div>
                    </div>

                    <div>
                        <button type="button" id="btn-fechar_equipamento" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" name="btn-salvar_equipamento" id="btn-salvar_equipamento"  class="btn btn-primary">Salvar</button>
                    </div> 
                </div>
            </form>
        </div>
    </div>
</div>




<script>
    $(document).on('hidden.bs.modal', '.modal', function () {
  $(this).remove();          // remove o modal em si
  $('.modal-backdrop').remove(); // remove a camada escura (se ainda existir)
});



tipo_cadastro= <?= json_encode($tipo_cadastro)?>;
   
</script>








<!-- ADICIONAR novos -->
<script type="text/javascript" src="equipamentos/tabModEquipamentoCadastro.js?v=0.20"></script>

<script type="text/javascript" src="equipamentos/tabModEquipamentoAbaServicos.js?v=0.66"></script> <!-- PROFISSIONAIS -->
<script type="text/javascript" src="equipamentos/tabModEquipamentoAbaDocumentos.js?v=0.17"></script>
<script type="text/javascript" src="equipamentos/tabModEquipamentoAbaGaleria.js?v=0.12"></script>

<!--<script type="text/javascript" src="equipamentos/tabModEquipamentoGaleria.js?v=0.14"></script>
<script type="text/javascript" src="equipamentos/tabModEquipamentoAbaProd.js?v=0.00"></script> PRODUTOS
<script type="text/javascript" src="equipamentos/tabModEquipamentoAbaRecursos.js?v=0.00"></script> RECUSRSOS
<script type="text/javascript" src="equipamentos/tabModEquipamentoAbaConteudos.js?v=0.00"></script>-- conteúdos -->



<script>

    document.addEventListener('input', function (e) {
        const el = e.target;
        if (e.target) {
            if (el.classList.contains('numero-virgula-financeiro') && el === document.activeElement) {
                    validarInput(el);
                    }
            if (el.classList.contains('numero-inteiro') && el === document.activeElement) {
                    validarInteiro(el);
                    }
            if (el.classList.contains('numero-porcento') && el === document.activeElement) {
                    validarPorcento(el);
                    }
        }
    });

</script>