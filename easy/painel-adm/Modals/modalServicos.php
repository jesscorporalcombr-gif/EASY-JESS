<?php

$pag = 'servicos';
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
$pasta = $_SESSION['x_url'] ?? '';


function decimalBR($valor) {
  return number_format((float)$valor, 2, ',', '');
}


$query = $pdo->prepare("SELECT * FROM servicos_categorias WHERE excluido = 0");
    $query->execute();
    $categorias = $query->fetchAll(PDO::FETCH_ASSOC);


if(isset($_POST['id']) && !empty($_POST['id'])) {
    $id_servico = $_POST['id'];

    $query = $pdo->prepare("SELECT * FROM servicos WHERE id = :id");
    $query->execute([':id' => $id_servico]);
    $servico = $query->fetchAll(PDO::FETCH_ASSOC);
   

    $titulo        = $servico[0]['servico'];
    $categoria_id           = $servico[0]['id_categoria'];
    $descricao          = $servico[0]['descricao'];
    $descricao_cliente          = $servico[0]['descricao_cliente'];
    $tempo            = $servico[0]['tempo'];
    $preco             = $servico[0]['valor_venda'];
    $custo             = $servico[0]['valor_custo'];
    $agendamento_online       = $servico[0]['agendamento_online'];
    $tipo_valor      = $servico[0]['tipo_valor'];
    $intervalo      = $servico[0]['folga_necess'];
    $tipo_valor      = $servico[0]['tipo_valor'];
    $comissao      = $servico[0]['comissao'];
    $tipo_valor      = $servico[0]['tipo_valor'];
    $retorno      = $servico[0]['retorno'];
   
    $fidelidade   = $servico[0]['fidelidade'];

    $foto_edit = $servico[0]['foto'];

$retorno   = $servico[0]['retorno'];
$site   = $servico[0]['site'];
$ref1   = $servico[0]['ref1'];
$ref2   = $servico[0]['ref2'];
$ag_paralelo = $servico[0]['nivel_paralelo'];

    $excluido      = $servico[0]['excluido'];



        if ($excluido=='0'){
            $situacao='Ativo';
            $statusSit= 'status-ativo';
        }else{
            $situacao='Deletado';
            $statusSit= 'status-deletado';
        }



        $titulo_modal = $titulo. '<span class="status-serv ' . $statusSit . '">' . $situacao . '</span>';
    



     $tipo_cadastro = "edit";
  
} else {
  $titulo_modal = "Novo Serviço";
  $tipo_cadastro = "novo";
  // Como é um novo cadastro, os campos do formulário ficariam vazios
}

// Função para buscar os agendamentos

     // Conecta ao banco de dados
$__salas = $pdo->query("SELECT id, nome, foto FROM salas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$__equips = $pdo->query("SELECT id, nome, foto FROM equipamentos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);


?>    


<div class="modal fade" tabindex="-1" style ="z-index: 95000;" id="modalCadServico" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl modal-easy-xl" >
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
            <!-- Wrapper de imagem + título -->
            <div class="d-flex align-items-center">
                <img
                src="<?= (@$foto_edit ? '../'.$pasta.'/img/servicos/'. $foto_edit  : '../img/sem-imagem.svg') ?>"
                id="img-foto_head"
                alt="Foto do Servico"
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
            <form method="POST" enctype="multipart/form-data" id="formCadServico">
                <div class="modal-body">
                                        
                    <ul  style="<?= ($tipo_cadastro == "novo" ? 'display:none;' : '') ?>" class="nav nav-tabs nav-tabs-md-easy-xl" id="v-tab" role="tablist">

                    
                        <li class="nav-link active tab-btn" id="cadastro-tab"  data-bs-toggle="tab" data-bs-target="#aba-cadastro" role="tab" aria-controls="cadastro" aria-selected="true">
                        Cadastro
                        </li>
                    
                        <li class="nav-link tab-btn "  id="produtos-tab" data-bs-toggle="tab" data-bs-target="#aba-produtos" role="tab" aria-controls="produtos" aria-selected="false">
                        Produtos
                        </li>
                         
                        <li class="nav-link tab-btn" id="profissionais-tab" data-bs-toggle="tab" data-bs-target="#aba-profissionais" role="tab" aria-controls="profissionais" aria-selected="false">
                        Profissionais
                        </li>
    
                        <li class="nav-link tab-btn" id="recursos-tab" data-bs-toggle="tab" data-bs-target="#aba-recursos" role="tab" aria-controls="recursos" aria-selected="false">
                        Recusros
                        </li>
                        
                        <li class="nav-link tab-btn" id="conteudos-tab" data-bs-toggle="tab" data-bs-target="#aba-conteudos" role="tab" aria-controls="conteudos" aria-selected="false">
                        Conteúdos
                        </li>

                        

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
                                <input type="hidden" id="frm-id" name="id" value="<?=$id_servico?>">
                                <div class="col-auto" style="min-width: 150px;">
                                    <!--<label for="img-foto_cad" >Foto</label>-->
                                    <input type="file"  accept="image/*" style="display:none;" class="form-control-file" id="input-foto_cadServico" name="input-foto_cadServico">
                                            
                                    <div id="capdivImgConta" class="mt-3">
                                        <div id="divImgConta2"  style="padding-left: 15px;" >
                                            <img 
                                            style="margin-left:5px; border-radius:50%; width: 100px;"
                                            src="<?= (@$foto_edit ? '../'. $pasta.'/img/servicos/'. $foto_edit  : '../img/sem-imagem.svg') ?>"
                                            id="img-foto_cadServico"
                                            name="img-foto_cadServico"
                                            alt="Foto do Servico">
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

                                <div class="col" style="min-width: 380px;">
                                    <h3> Dados Principais: </h3>
                                    <div class="row">
                                        <div class="col-auto" style="min-width: 300px;">
                                            <div class="mb-3">
                                                <label for="frm-nome" class="form-group">Título do Serviço:</label>
                                                <input type="text" class="form-control" id="frm-nome" name="frm-nome"  required value="<?=$titulo ?>">
                                            </div> 
                                        </div>

                                                

                                        <div class="col" style="min-width: 130px;">
                                            <div class="mb-3">
                                                <label for="frm-categoria" class="form-group">Categoria: </label>
                                                <select class="form-select" id="frm-categoria" name="frm-categoria">
                                                    <?php
                                                    foreach ($categorias as $categoriaE) {
                                                        echo '
                                                        <option value="' . $categoriaE['id'] . '" ' . ($categoria_id == $categoriaE['id'] ? 'selected' : '') . '>' . $categoriaE['nome'] . '</option>
                                                        ';
                                                    }
                                                    ?>
                                                </select>
                                            </div>					   						   
                                        </div>

                                        
                                        <div class="col-auto">
                                            <div class="mb-3">
                                                <label for="frm-tempo" class="form-group">Tempo(minutos):</label>
                                                <input type="text" class="form-control numero-inteiro" style = "max-width:100px;" id="frm-tempo" name="frm-tempo"  required="" value="<?= $tempo ?>">
                                            </div>
                                        </div>

                                        <div class="col-auto mb-3" style="min-width: 150px;">
                                            <label for="frm-preco" class="form-group"><b>PREÇO:</b></label>
                                            <div class="input-group flex-nowrap">
                                            <span class="input-group-text" id="addon-wrapping">R$</span>
                                            <input type="text" name="frm-preco" id="frm-preco" class="form-control numero-virgula-financeiro" value="<?=(decimalBR($preco)!='0,00')?decimalBR($preco):''  ?>">
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="row">
                                
                                        <div class="col-auto">
                                            <div class="mb-3">
                                                    <label for="frm-intervalo" class="form-group">Intervalo(dias):</label>
                                                    <input type="text" class="form-control numero-inteiro" style="max-width:130px;" id="frm-intervalo" name="frm-intervalo"  value="<?= $intervalo ?>">
                                            </div> 
                                        </div>
                                        <div class="col-auto">
                                            <div class="mb-3">
                                                    <label for="frm-retorno" class="form-group">Retorno Sugerido(dias):</label>
                                                    <input type="text" class="form-control numero-inteiro" style="max-width:130px;" id="frm-retorno" name="frm-retorno"  value="<?= $retorno ?>">
                                            </div> 
                                        </div>


                                         <div class="col-auto mb-3" style="min-width: 150px;">
                                            <label for="frm-custo" class="form-group">Custo Total:</label>
                                            <div class="input-group flex-nowrap">
                                            <span class="input-group-text" id="addon-wrapping">R$</span>
                                            <input type="text" name="frm-custo" id="frm-custo" class="form-control numero-virgula-financeiro" value="<?=(decimalBR($custo)!='0,00')?decimalBR($custo):''  ?>">
                                            </div>  
                                        </div>
                                        <div class="col-auto mb-3" style="min-width: 150px;">
                                            <label for="frm-comissao" class="form-group">Comissão Padrão:</label>
                                            <div class="input-group flex-nowrap">
                                            
                                            <input style="max-width: 80px;" type="text" name="frm-comissao" id="frm-comissao" class="form-control numero-porcento" value="<?=decimalBR($comissao)!='0,00'?decimalBR($comissao):'0,00'  ?>">
                                            <span class="input-group-text" id="addon-wrapping">%</span>
                                        </div>  
                                </div>
                                    
                                    </div>
                                </div>
                            </div>
<hr>
                            <div class="row mb-2">
                                <h3 class="mb-1"> Outros Detalhes: </h3>
                                <div class="col-auto" style="margin-left:15px;">
                                    <span class="spanRadio">
                                        <input type="checkbox" style="margin-right: 7px; margin-top:7px;" class="form-check-input" id="frm-agendamento_online" name="frm-agendamento_online" <?= $agendamento_online?'checked':'' ?>> Agendamento Online
                                    </span> 
                                </div>
                                <div class="col">
                                    <span class="spanRadio">
                                        <input type="checkbox" style="margin-right: 7px; margin-top: 7px;" class="form-check-input" id="frm-fidelidade" name="frm-fidelidade"  <?= $fidelidade?'checked':'' ?>>Fidelidade
                                    </span>
                                </div>

                            </div>
                            <div class="row" style="margin-left: 15px;" >
                                <h5 class="mb-1">Agendamento em Paralelo:</h5>
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <span class="spanRadio">
                                            <input type="radio" style ="margin-right: 5px;" id="frm-paralelo_0" name="frm-paralelo" value="0" <?= $ag_paralelo==0 ? 'checked' : ''; ?>>Não
                                        </span>
                                        <span class="spanRadio" style="margin-left: 10px;">
                                            <input type="radio" style ="margin-right: 5px;" id="frm-paralelo_1" name="frm-paralelo" value="1" <?= $ag_paralelo==1 ? 'checked' : ''; ?>>Sim
                                        </span>
                                        <span class="spanRadio" style="margin-left: 10px;">
                                            <input type="radio" style ="margin-right: 5px;" id="frm-paralelo_2" name="frm-paralelo" value="2" <?= $ag_paralelo==2 ? 'checked' : ''; ?>>Somente Com Paralelos
                                        </span>


                                    </div>  
                                </div>
                            </div>
                            <div class="row" style="margin-left: 15px;" >

                            </div>

<hr>
                            
                            <div class="row">
                                <h3 class="mb-0">Descrição Interna:</h3>
                                <div class="col" >
                                        
                                        <textarea style="min-height:100px;" class="form-control" id="frm-desc_interna" name="frm-desc_interna"> <?= $descricao ?></textarea>
                                    
                                </div>

                               
                            </div>


<hr>
                            <div class="row">
                                <h3 class="mb-0">Descrição ao Cliente:</h3>
                                <div class="col" >
                                        <textarea style="min-height:100px;" class="form-control" id="frm-desc_cliente" name="frm-desc_cliente"> <?= $descricao_cliente ?></textarea>
                                </div>
                            </div>


<hr>
                            <div class="row">
                                <h3>Referências Online</h3>
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <label for="frm-site" class="form-group">Site:</label>
                                        <input type="text" class="form-control"  style="min-width:280px;" id="instagram" name="frm-site" value="<?= $site?>">
                                    </div> 
                                </div>

                                <div class="col-auto">
                                    <div class="mb-3">
                                        <label for="frm-ref1" class="form-group">Referência 1:</label>
                                        <input type="text" class="form-control" style="min-width:208px;" id="frm-ref1" name="frm-ref1" value="<?= $ref1 ?>">
                                    </div>  
                                </div>

                                <div class="col-auto">
                                    <div class="mb-3">
                                        <label for="frm-ref2" class="form-group">referência 2:</label>
                                        <input type="text" class="form-control"  style="min-width:280px;" id="frm-ref2" name="frm-ref2" value="<?= $ref2 ?>">
                                    </div> 
                                </div>
                            </div>

                                
<hr>
                            <div class="row" style="margin-left: 15px;" >
                                <h5 class="mb-1">Status do Serviço:</h5>
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

                        <!-- TAB PRODUTOS - Inicio da TaB 2-->
                        <div class="tab-pane fade" id="aba-produtos" role="tabpanel" aria-labelledby="produtos-tab">
                                
                            <h4 class="pt-2" style="text-align:left;">Produtos utilizados no serviço</h4>

                            <div class="container-md mt-3" id="aba-produtos-container">
                                <!-- Form de edição/adição -->
                                <div class="card p-3 mb-3">
                                <div class="row g-2 align-items-end">
                                    <input type="hidden" id="prod-id_servico" value="<?= $id_servico ?>">
                                    <input type="hidden" id="prod-id_serv_prod" value="">
                                    <input type="hidden" id="prod-id_produto" value="">

                                    <div class="col-md-4">
                                    <label class="form-group">Produto</label>
                                    <input type="text" id="prod-busca" class="form-control" autocomplete="off" placeholder="Buscar produto pelo nome..." list="prod-sugestoes">
                                    <datalist id="prod-sugestoes"></datalist>
                                    </div>

                                    <div class="col-md-2">
                                    <label class="form-group">Unidade</label>
                                    <input type="text" id="prod-unidade" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-2">
                                    <label class="form-group">Quantidade</label>
                                    <input type="text" id="prod-quantidade" class="form-control numero-uma-virgula" placeholder="0">
                                    </div>

                                    <div class="col-md-2">
                                    <label class="form-group">Custo Unit.</label>
                                    <div class="input-group flex-nowrap">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" id="prod-custo_unit" class="form-control numero-uma-virgula" placeholder="0,00">
                                    </div>
                                    </div>

                                    <div class="col-md-2">
                                    <label class="form-group">Total</label>
                                    <div class="input-group flex-nowrap">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" id="prod-total" class="form-control numero-virgula-financeiro" readonly>
                                    </div>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex gap-2">
                                    <button type="button" id="btn-prod-salvar" class="btn btn-primary">Adicionar / Atualizar</button>
                                    <button type="button" id="btn-prod-cancelar" class="btn btn-secondary">Cancelar</button>
                                </div>
                                </div>

                                <!-- Tabela -->
                                <div class="table-responsive">
                                    
                                <table id="tabelaAbaProd" class="dataTable dTlinhaFina" >
                                    <thead>
                                    <tr>
                                        <th style="width:48px;"></th>
                                        <th>Produto</th>
                                        <th>Unid.</th>
                                        <th class="text-end">Qtd</th>
                                        <th class="text-end">Custo Unit.</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center" style="width:120px;">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody><!-- preenchido via JS --></tbody>
                                </table>
                                </div>
                            </div>
                            

                        </div>








                        <!-- TAB PROFISSIONAIS Inicio da TaB 3-->
                        <div class="tab-pane fade" id="aba-profissionais" role="tabpanel" aria-labelledby="profissionais-tab">
                             <h4 class="pt-2" style="text-align:left;"> Profissionais:</h4>
                            <div class="container-md listTabContainer mt-3">
                                <div class="table-containerProf" id="servicos-container-modal">
                                    <div class="row mb-2">
                                        <div class="col-auto">
                                            <input type="text" style="min-width: 230px;"class="form-control searchBox" placeholder="Pesquisar profissional...">
                                        </div>
                                        <div class="col-auto">
                                            <select hidden class="form-select rowsPerPage" style="width: auto;">
                                                <option value="1000"></option>
                                            </select>
                                        </div>
                                    </div>
                                    <table 
                                        class="dataTable dTlinhaFina"
                                        id="tabelaAbaProf"
                                        data-table="profissionais_servicos" data-minimized="false"
                                        
                                        data-filtro='{
                                                    "logic":"AND",
                                                    "conditions":[
                                                        {"field":"id_servico", "op":"=", "value":<?= $id_servico ?>}                                                        
                                                        ]
                                                    }'
                                        style="width:100%"><!-- Substitua 123 pelo id_servico do servico atual via JS -->
                                        <thead>
                                        <tr>
                                            <th data-field="foto_profissional" data-foto></th>
                                            <th data-field="profissional" data-sort="a-z" >Profissional</th>
                                            <th data-field="executa"  data-sort="a-z">executa</th>
                                            <th data-field="id_profissional" hidden data-sort="num" >Profissional</th>
                                            <th data-field="id_contrato" data-classe-td="numero-porcento" hidden data-sort="num" >Profissional</th>
                                            <th data-field="id" hidden>ID</th>
                                            <th data-field="tempo" >Tempo</th> 
                                            <th data-field="comissao" data-classe-td="numero-porcento" data-sort="num">Comissão</th>
                                            <th data-field="preco" data-sort="a-z">Preço</th>
                                            <th data-field="agendamento_online" class="" data-sort="num" data-sort-init="DESC" data-classe-td="sIgual" style="text-align:center;">Ag Online</th>
                                            

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
                        <!-- TAB RECURSOS Inicio da TaB 4 - R E C U R S O S-->
                        <div class="tab-pane fade" id="aba-recursos" role="tabpanel" aria-labelledby="recursos-tab">
                            <h4 class="pt-2" style="text-align:left;">Recursos vinculados ao serviço</h4>

                            <div class="container-md mt-3" id="aba-recursos-container">
                                <div class="row g-3">
                                <!-- BLOCO SALAS -->
                                <div class="col-12 col-lg-6" id="aba-recursos-salas">
                                    <div class="card p-3">
                                    <h4 class="mb-3">Salas</h4>

                                    <input type="hidden" id="rec-salas-id_servico" value="<?= $id_servico ?>">
                                    <input type="hidden" id="rec-salas-id_link" value="">
                                    <input type="hidden" id="rec-salas-id_recurso" value="">

                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-group">Sala</label>
                                            <select id="rec-salas-select" class="form-select">
                                               <option value="">Selecione a sala...</option>
                                            </select>

                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="d-grid gap-2 d-md-flex flex-md-row">
                                                <button type="button" id="rec-salas-salvar" class="btn btn-primary flex-fill">Salvar</button>
                                                <button type="button" id="rec-salas-cancelar" class="btn btn-secondary">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="dataTable dTlinhaFina">
                                        <thead>
                                            <tr>
                                            <th style="width:48px;"></th>
                                            <th>Sala</th>
                                            <th class="text-center" style="width:84px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="rec-rows"><!-- via JS --></tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>

                                <!-- BLOCO EQUIPAMENTOS -->
                                <div class="col-12 col-lg-6" id="aba-recursos-equip">
                                    <div class="card p-3">
                                    <h4 class="mb-3">Equipamentos</h4>

                                    <input type="hidden" id="rec-equip-id_servico" value="<?= $id_servico ?>">
                                    <input type="hidden" id="rec-equip-id_link" value="">
                                    <input type="hidden" id="rec-equip-id_recurso" value="">

                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-group">Equipamento</label>
                                            <select id="rec-equip-select" class="form-select">
                                                <option value="">Selecione o equipamento...</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="d-grid gap-2 d-md-flex flex-md-row">
                                                <button type="button" id="rec-equip-salvar" class="btn btn-primary flex-fill">Salvar</button>
                                                <button type="button" id="rec-equip-cancelar" class="btn btn-secondary">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="dataTable dTlinhaFina">
                                        <thead>
                                            <tr>
                                            <th style="width:48px;"></th>
                                             <th>Equipamento</th>
                                            <th class="text-center" style="width:84px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="rec-rows"><!-- via JS --></tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>

                        <!-- TAB CONTEUDOS Inicio da TaB 4  C O N T E U D O S-->
                        <div class="tab-pane fade" id="aba-conteudos" role="tabpanel" aria-labelledby="conteudos-tab">
                            <div class="container-md mt-3" id="conteudos-container">
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                    <div class="btn-group" role="group" aria-label="Filtro">
                                        <button type="button" class="btn btn-outline-secondary btn-filter active" data-tipo="">Todos</button>
                                        <button type="button" class="btn btn-outline-secondary btn-filter" data-tipo="TERMO">Termos</button>
                                        <button type="button" class="btn btn-outline-secondary btn-filter" data-tipo="POP">POPs</button>
                                        <button type="button" class="btn btn-outline-secondary btn-filter" data-tipo="TREINAMENTO">Treinamentos</button>
                                        <button type="button" class="btn btn-outline-secondary btn-filter" data-tipo="ARQUIVO">Arquivos</button>
                                        <button type="button" class="btn btn-outline-secondary btn-filter" data-tipo="LINK">Links</button>
                                    </div>
                                    <button type="button" class="btn btn-primary ms-auto" id="btn-novo-conteudo">
                                        <i class="bi bi-plus-circle"></i> Novo conteúdo
                                    </button>
                                </div>

                                <!-- Form (oculto) -->
                                <div id="form-conteudo" class="card p-3 mb-3" style="display:none;">
                                    <input type="hidden" name="id" id="ct-id" value="">
                                    <input type="hidden" name="id_servico" id="ct-id_servico" value="<?= $id_servico ?>">

                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-group">Tipo</label>
                                            <select class="form-select" name="tipo" id="ct-tipo" required>
                                                <option value="TERMO">Termo</option>
                                                <option value="POP">POP</option>
                                                <option value="TREINAMENTO">Treinamento</option>
                                                <option value="ARQUIVO">Arquivo</option>
                                                <option value="LINK">Link</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-group">Título</label>
                                            <input type="text" class="form-control" name="titulo" id="ct-titulo">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-group">Data</label>
                                            <input type="date" class="form-control" name="data_referencia" id="ct-data">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" id="ct-obrigatorio" name="obrigatorio" value="1">
                                                <label class="form-check-label" for="ct-obrigatorio">Obrigatório</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-0">
                                        <div class="col-12">
                                            <label class="form-group">Descrição</label>
                                            <textarea class="form-control" name="descricao" id="ct-desc" rows="3"></textarea>
                                        </div>
                                    </div>

                                    <!-- Campos condicionais -->
                                    <div class="row g-3 mt-0 ct-bloco file-required" style="display:none;">
                                        <div class="col-md-8">
                                            <label class="form-group">Arquivo (obrigatório)</label>
                                            <div class="input-group">
                                            <button type="button" class="btn btn-outline-secondary" id="ct-file-btn">
                                                <i class="bi bi-upload"></i> Escolher arquivo
                                            </button>
                                            <input type="file" class="form-control d-none" id="ct-file"
                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.ppt,.pptx,.xls,.xlsx">
                                            <input type="text" class="form-control" id="ct-file-name"
                                                    placeholder="Nenhum arquivo selecionado" readonly>
                                            </div>
                                            <small id="ct-file-current" class="text-muted" style="display:none;">
                                            Arquivo atual: <a href="#" target="_blank" id="ct-file-link"></a>
                                            </small>
                                        </div>
                                    </div>
                                    <!-- Somente para LINK -->
                                    <div class="row g-3 mt-0 ct-bloco link" style="display:none;">
                                        <div class="col-md-8">
                                            <label class="form-group">URL</label>
                                            <input type="url" class="form-control" name="url" id="ct-url" placeholder="https://...">
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-0 ct-bloco treinamento" style="display:none;">
                                        <div class="col-md-3">
                                            <label class="form-group">Carga horária (h)</label>
                                            <input type="number" step="0.5" class="form-control" name="carga_horaria" id="ct-carga">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-group">Validade (dias)</label>
                                            <input type="number" class="form-control" name="validade_dias" id="ct-validade">
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-0">
                                        <div class="col-md-6">
                                            <label class="form-group">Tags (opcional)</label>
                                            <input type="text" class="form-control" name="tags" id="ct-tags" placeholder="ex: biossegurança, lavieen">
                                        </div>
                                        <div class="col-12 d-flex gap-2 justify-content-end">
                                            <button type="button" class="btn btn-secondary" id="ct-cancelar">Cancelar</button>
                                            <button type="button" class="btn btn-primary" id="ct-salvar">Salvar</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabela -->
                                <div class="table-container">
                                <table class="dataTable dTlinhaFina" id="tabela-conteudos">
                                    <thead>
                                    <tr>
                                        <th style="width:40px;"></th>
                                        <th>Título</th>
                                        <th>Tipo</th>
                                        <th>Data</th>
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
                                                <div class="col-md-6">
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
                                            <input type="text" class="form-control searchBox" placeholder="Pesquisar...">
                                        </div>
                                    </div>

                                    <table 
                                        class="tablesModServico dataTable dTlinhaFina"
                                        data-table="servicos_fotos"
                                        
                                         data-filtro='{
                                                        "logic":"AND", 
                                                        "conditions":[
                                                            {"field":"id_servico", "op":"=", "value":"<?= (int)$id_servico ?>"}
                                                            ]
                                                        }'
                                        style="width:100%"><!-- Substitua 123 pelo id_servico do servico atual via JS -->
                                        <thead>
                                        <tr>
                                            <th  
                                                data-field="arquivo_mini" 
                                                class="data-foto" 
                                                data-href-field="arquivo_ori"
                                                data-href-base="../<?=$pasta?>/img/servicos/galeria/" 
                                                data-foto="../<?=$pasta?>/img/servicos/galeria/mini/">Foto
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
                                                class="text-center"class="text-center">Ações</th>
                                       
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
                        <button type="button" id="btn-fechar_servico" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" name="btn-salvar_servico" id="btn-salvar_servico"  class="btn btn-primary">Salvar</button>
                    </div> 
                    <input name="id" type="hidden" value="<?=  $id_servico?>">
                    <input name="antigo" type="hidden" value="<?=  @$cpf ?>">
                    <input name="antigo2" type="hidden" value="<?=  @$email ?>">
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





</script>




<script>
  window.CATALOGO_SALAS = <?= json_encode($__salas, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
  window.CATALOGO_EQUIPAMENTOS = <?= json_encode($__equips, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

tipo_cadastro= <?= json_encode($tipo_cadastro)?>;

</script>


<!-- //<script type="text/javascript" src="servicos/tabelasModalServicos.js?v=0.00"></script> meu-->

<!-- <script type="text/javascript" src="servicos/modalServicos.js?v=0.06"></script>-->


<!-- ADICIONAR novos -->
<script type="text/javascript" src="servicos/tabModServCadastro.js?v=0.41"></script>
<script type="text/javascript" src="servicos/tabModServAbaProf.js?v=0.32"></script> <!-- PROFISSIONAIS -->
<script type="text/javascript" src="servicos/tabModServAbaProd.js?v=0.32"></script><!-- PRODUTOS -->



<script type="text/javascript" src="servicos/tabModServAbaRecursos.js?v=0.27"></script> <!-- RECUSRSOS -->

<script type="text/javascript" src="servicos/tabModServAbaConteudos.js?v=0.46"></script> <!-- conteúdos -->

<script type="text/javascript" src="servicos/tabModServGaleria.js?v=0.36"></script>


<script>
// Propaga o novo ID para os campos hidden de todas as abas
document.addEventListener('servico:salvo', function (e) {
  const id = e.detail?.id;
  if (!id) return;

  const modal = document.getElementById('modalCadServico');
  if (!modal) return;

  [
    '#frm-id',               // cadastro
    '#prod-id_servico',      // produtos
    '#rec-salas-id_servico', // recursos – salas
    '#rec-equip-id_servico', // recursos – equipamentos
    '#ct-id_servico'         // conteúdos
  ].forEach(sel => {
    const el = modal.querySelector(sel);
    if (el) el.value = id;
  });
});




</script>