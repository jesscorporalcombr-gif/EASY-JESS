<?php

$pag = 'colaboradores';
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
$pasta = $_SESSION['x_url'] ?? '';



if(isset($_POST['id']) && !empty($_POST['id'])) {
    $id_contrato = $_POST['id'];

    $query = $pdo->prepare("SELECT * FROM colaboradores_contratos WHERE id = :id_contrato");
    $query->execute([':id_contrato' => $id_contrato]);
    $contrato = $query->fetchAll(PDO::FETCH_ASSOC);
    $id_colaborador = $contrato[0]['id_colaborador'];

    $numero_contrato         = $contrato[0]['id'];
    $tipo_contrato           = $contrato[0]['tipo_contrato'];
    $data_contrato           = $contrato[0]['data_validade_1'];
    $experiencia             = $contrato[0]['experiencia'];
    $vencimento              = $contrato[0]['data_validade_2'];
    $renova_auto             = $contrato[0]['renova_auto'];

    $cargo                   = $contrato[0]['cargo'];
    $funcao                  = $contrato[0]['funcao'];
    $departamento            = $contrato[0]['departamento'];
    $atribuicao_funcao       = $contrato[0]['atribuicoes_funcao'];

    $situcao_contrato        = $contrato[0]['situacao'];
    $status_contrato         = $contrato[0]['status'];
    $anotacoes_contrato      = $contrato[0]['anotacao'];

    $periodo_pagamentos      = $contrato[0]['periodo_pagamentos'];
    $valor_pagamentos        = $contrato[0]['valor_pagamentos'];
    $observacoes_pagamentos  = $contrato[0]['observacoes_pagamentos'];

    $flexivel_jornada        = $contrato[0]['flexivel'];
    $jornada_diaria          = $contrato[0]['jornada_horas_diaria'];
    $jornada_semanal         = $contrato[0]['jornada_horas_semanais'];
    $banco_horas             = $contrato[0]['banco_horas'];
    $quadro_horario_id       = $contrato[0]['id_quadro_horario'];

    $va_vr                   = $contrato[0]['va_vr'];
    $va_vr_valor             = $contrato[0]['va_vr_valor'];
    $va_vr_obs               = $contrato[0]['va_vr_observacoes'];

    $vt_sim_nao              = $contrato[0]['tipo_vt'];
    $valor_dia_vt            = $contrato[0]['valor_vt_dia'];
    $vt_obs                  = $contrato[0]['vt_observacoes'];

    $alteracoes_contrato     = $contrato[0]['alteracoes'];

    $aviso_previo            = $contrato[0]['aviso_previo'];
    $data_aviso_previo       = $contrato[0]['data_aviso'];
    $motivo_termino          = $contrato[0]['motivo_termino'];
    $data_fim_contrato       = $contrato[0]['data_fim'];
    $ativo      = $contrato[0]['ativo'];
    if ($ativo){
        $contrato_encerrado=0;
        $contrato_ativo=1;
    }else{
        $contrato_encerrado=1;
        $contrato_ativo=0;
    }



//    $aba = $_POST['aba'];

    // Preparando a consulta PDO
    $query = $pdo->prepare("SELECT * FROM colaboradores_cadastros WHERE id = :id_colaborador");
    $query->execute([':id_colaborador' => $id_colaborador]);
    $cadastro = $query->fetchAll(PDO::FETCH_ASSOC);
    $total_reg_cad = @count($cadastro);
   
    $tipo_cadastro= "edicao";


    if($total_reg_cad= 1){ 
        $nome = $cadastro[0]['nome'];
		$data_nascimento = $cadastro[0]['data_nascimento'];
		$sexo = $cadastro[0]['sexo'];
		$cpf = $cadastro[0]['cpf'];
		$cpf = $cadastro[0]['cpf']; // Obtenção do CPF

		// Limpeza e preparação do CPF para garantir que apenas números são considerados
		$cpf = preg_replace('/\D/', '', $cpf); // Remove tudo que não é dígito
		
		// Preenchimento com zeros à esquerda para garantir que o CPF tenha 11 dígitos
		$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
		
		// Inserção dos pontos e traço no lugar correto
		$cpfFormatado = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
		$cpf = $cpfFormatado;

		$cnh = $cadastro[0]['cnh'];
		$cnh_categoria = $cadastro[0]['cnh_categoria'];
		$cnh_dt_validade = $cadastro[0]['cnh_dt_validade'];
		$rg = $cadastro[0]['rg'];
		$orgao = $cadastro[0]['orgao'];	
		$data_exp = $cadastro[0]['data_exp'];
		$e_social = $cadastro[0]['e_social'];
		$data_chegada_brasil = $cadastro[0]['data_chegada_brasil'];
		$etinia = $cadastro[0]['etinia'];
		$pis_dt_cadastro = $cadastro[0]['pis_dt_cadastro'];
		$conta_fgts = $cadastro[0]['conta_fgts'];
		$fgts_dt_opcao = $cadastro[0]['fgts_dt_opcao'];
		$cert_reservista= $cadastro[0]['cert_reservista'];
		$est_civil = $cadastro[0]['est_civil'];
		$nome_conj = $cadastro[0]['nome_conj'];
		$dados_conj = $cadastro[0]['dados_conj'];
		$ctps = $cadastro[0]['ctps'];
		$serie = $cadastro[0]['serie'];
		$pis = $cadastro[0]['pis'];
		$titulo = $cadastro[0]['titulo'];
		$zona = $cadastro[0]['zona'];
		$sessao = $cadastro[0]['sessao'];
		$cep = $cadastro[0]['cep'];
		$endereco = $cadastro[0]['endereco'];
		$numero = $cadastro[0]['numero'];
		$complemento = $cadastro[0]['complemento'];
		$bairro = $cadastro[0]['bairro'];
		$cidade = $cadastro[0]['cidade'];
		$uf_endereco = $cadastro[0]['uf_endereco'];
		$nome_mae = $cadastro[0]['nome_mae'];
		$nome_pai = $cadastro[0]['nome_pai'];
		$telefone = $cadastro[0]['telefone'];
		$telefone2 = $cadastro[0]['telefone2'];
		$escolaridade = $cadastro[0]['escolaridade'];
		$email_pessoal = $cadastro[0]['email_pessoal'];
		$situacao = $cadastro[0]['situacao'];
		$banco_if = $cadastro[0]['banco_if'];
		$agencia = $cadastro[0]['agencia'];
		$conta = $cadastro[0]['conta'];
		$pix = $cadastro[0]['pix'];
		$tipo_pix = $cadastro[0]['tipo_pix'];
		$tp_sanguineo = $cadastro[0]['tp_sanguineo'];
		$naturalidade = $cadastro[0]['naturalidade'];
		$uf_naturalidade = $cadastro[0]['uf_naturalidade'];
		$deficiente_sim_nao = $cadastro[0]['deficiente_sim_nao'];
		$deficiencia = $cadastro[0]['deficiencia'];
		$tp_deficiencia = $cadastro[0]['tp_deficiencia'];
		$nacionalidade = $cadastro[0]['nacionalidade'];
		$senha_sistema = $cadastro[0]['senha_sistema'];
		$ativo_agenda = $cadastro[0]['ativo_agenda'];
		$foto_cadastro= $cadastro[0]['foto_cadastro'];
        
        $instagram=$cadastro[0]['instagram'];
        $facebook=$cadastro[0]['facebook'];
        $linkedin=$cadastro[0]['linkedin'];
        $tiktok=$cadastro[0]['tiktok'];
        $outras_redes=$cadastro[0]['outras_redes'];



        $foto_edit=$foto_cadastro;

        if ($contrato_ativo=='1'){
            $situacao='Ativo';
            $statusSit= 'status-profAtivo';
        }else{
            $situacao='Encerrado';
            $statusSit= 'status-profInativo';
        }



        $titulo_modal = $nome. '<span class="status-Prof' . $statusSit . '">' . $situacao . '</span>';
    }



     
  
} else {
  $titulo_modal = "Novo Cadastro de Colaborador";
  $tipo_cadastro = "novo";
  // Como é um novo cadastro, os campos do formulário ficariam vazios
}

// Função para buscar os agendamentos

     // Conecta ao banco de dados



?>    


<div class="modal fade" tabindex="-1" style ="z-index: 95000; height: 900px;" id="modalCadColaborador" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl" >
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
            <!-- Wrapper de imagem + título -->
            <div class="d-flex align-items-center">
                <img
                src="<?= (@$foto_edit ? '../'.$pasta.'/img/cadastro_colaboradores/'. $foto_edit  : '../img/sem-foto.svg') ?>"
                id="img-foto_head"
                alt="Foto do Colaborador"
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
            <form method="POST" id="formCadColaborador">
                <div class="modal-body" style="max-height: 600px; min-height:420px;overflow-y:auto;">
                                        
                    <ul <?= ($tipo_cadastro == "novo" ? 'hidden' : '') ?> style="cursor:pointer; margin-top:-15px;" class="nav nav-tabs" id="v-tab" role="tablist">

                    
                        <li class="nav-link active tab-btn" id="cadastro-tab"  data-bs-toggle="tab" data-bs-target="#aba-cadastro" role="tab" aria-controls="cadastro" aria-selected="true">
                        Cadastro
                        </li>
                    
                        <li class="nav-link tab-btn "  id="contrato-tab" data-bs-toggle="tab" data-bs-target="#aba-contrato" role="tab" aria-controls="contrato" aria-selected="false">
                        Contrato
                        </li>
                         <li class="nav-link tab-btn" id="sistema-tab" data-bs-toggle="tab" data-bs-target="#aba-sistema" role="tab" aria-controls="sistema" aria-selected="false">
                        Sistema
                        </li>
    
                        <li class="nav-link tab-btn" id="beneficios-tab" data-bs-toggle="tab" data-bs-target="#aba-beneficios" role="tab" aria-controls="benecicios" aria-selected="false">
                        Benefícios
                        </li>
                        
                        <li class="nav-link tab-btn" id="pagamentos-tab" data-bs-toggle="tab" data-bs-target="#aba-pagamentos" role="tab" aria-controls="pagamentos" aria-selected="false">
                        Pagamentos
                        </li>

                        <li class="nav-link tab-btn" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#aba-documentos" role="tab" aria-controls="documentos" aria-selected="false">
                        Documentos
                        </li>

                        <li class="nav-link tab-btn" id="galeria-tab" data-bs-toggle="tab" data-bs-target="#aba-galeria" role="tab" aria-controls="galeria" aria-selected="false">
                        Galeria
                        </li>

                        <!-- Adicione outras abas conforme necessário -->
                    
                    </ul>
                        
                    <div class="tab-content" id="v-tabContent"> <!-- conteudo das tabs -->
                        <!-- TAB CADASTRO -->
                        <div class="tab-pane fade show active" id="aba-cadastro" role="tabpanel" aria-labelledby="cadastro-tab">
                            <div class="row mt-2">
                                <input type="hidden" id="frm-id" name="frm-id" value="<?=$id_colaborador?>">
                                <div class="col-auto" style="min-width: 150px;">
                                    <!--<label for="img-foto_cad" >Foto</label>-->
                                    <input type="file"  accept="image/*" style="display:none;" class="form-control-file" id="input-foto_cadColaborador" name="input-foto_cadColaborador" onChange="carregarImg();">
                                            
                                    <div id="capdivImgConta" class="mt-3">
                                        <div id="divImgConta2"  style="padding-left: 15px;" >
                                            <img 
                                            style="margin-left:5px; border-radius:50%; width: 100px;"
                                            src="<?= (@$foto_edit ? '../'. $pasta.'/img/cadastro_colaboradores/'. $foto_edit  : '../img/sem-foto.svg') ?>"
                                            id="img-foto_cadColaborador"
                                            name="img-foto_cadColaborador"
                                            alt="Foto do Colaborador">
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
                                                <label for="frm-nome" class="form-group">Nome</label>
                                                <input type="text" class="form-control" id="frm-nome" name="frm-nome"  required value="<?=$nome ?>">
                                            </div> 
                                        </div>

                                                

                                        <div class="col"  style="min-width: 130px;">
                                            <div class="mb-3">
                                                <label for="frm-data_nascimento" class="form-group">Data Nascimento </label>
                                                <input type="date" class="form-control" id="frm-data_nascimento" name="frm-data_nascimento"  value="<?php echo @$data_nascimento ?>">
                                            </div>					   						   
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="frm-cpf" class="form-group">CPF</label>
                                                <input type="text" class="form-control num-cpf" id="frm-cpf" name="frm-cpf"  required="" value="<?php echo @$cpf ?>">
                                                <div class="invalid-feedback" id="cpfError" style="display: none; color: red;">
                                                    CPF inválido!
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-group" for="frm-sexo">Gênero</label>
                                                <select class="form-select" aria-label="Default select example" id ="frm-sexo" name="frm-sexo">
                                                    <option <?php if(@$sexo == '--'){ ?> selected <?php } ?>  value="--">--</option>
                                                    <option <?php if(@$sexo == 'Feminino'){ ?> selected <?php } ?>  value="Feminino">Feminino</option>
                                                    <option <?php if(@$sexo == 'Masculino'){ ?> selected <?php } ?>  value="Masculino">Masculino</option>
                                                    <option <?php if(@$sexo == 'Outro'){ ?> selected <?php } ?>  value="Outro">Outro</option>
                                                    <option <?php if(@$sexo == 'Não Declarar'){ ?> selected <?php } ?>  value="Não Declarar">Não Declarar</option>
                                                </select>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="row">
                                
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                    <label for="frm-telefone" class="form-group">Telefone</label>
                                                    <input type="text" class="form-control" id="frm-telefone" name="frm-telefone"  value="<?php echo @$telefone ?>">
                                            </div> 
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                    <label for="frm-telefone2" class="form-group">Telefone 2</label>
                                                    <input type="text" class="form-control" id="frm-telefone2" name="frm-telefone2" value="<?php echo @$telefone2 ?>">
                                                </div> 
                                            </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                    <label for="frm-email_pessoal" class="form-group">Email Pessoal</label>
                                                    <input type="email" class="form-control" id="frm-email_pessoal" name="frm-email_pessoal" value="<?php echo @$email_pessoal ?>">
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
                            </div>
<hr>

                            <div class="row">
                                <h3> Redes Sociais</h3>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-instagram" class="form-group">Instagram</label>
                                        <input type="text" class="form-control" id="instagram" name="frm-instagram" value="<?php echo @$instagram ?>">
                                    </div> 
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-facebook" class="form-group">Facebook</label>
                                        <input type="text" class="form-control" id="frm-facebook" name="frm-facebook" value="<?php echo @$facebook ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-linkedin" class="form-group">LinkedIn</label>
                                        <input type="text" class="form-control" id="frm-linkedin" name="frm-linkedin" value="<?php echo @$linkedin ?>">
                                    </div> 
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-tiktok" class="form-group">TikTok</label>
                                        <input type="text" class="form-control" id="frm-tiktok" name="frm-tiktok" value="<?php echo @$tiktok ?>">
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="frm-outrasredes" class="form-group">Outras Redes</label>
                                        <input type="text" class="form-control" id="frm-outras_redes" name="frm-outras_redes" value="<?php echo @$outras_redes ?>">
                                    </div> 
                                </div>

                            </div>
<hr>

                            <div class="row">
                                <h3>Endereço:</h3>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-cep" class="form-group">CEP</label>
                                        <input type="text" class="form-control" id="cep" name="frm-cep" value="<?php echo @$cep ?>">
                                    </div> 
                                </div>

                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="frm-endereco" class="form-group">Endereço</label>
                                        <input type="text" class="form-control" id="frm-endereco" name="frm-endereco" value="<?php echo @$endereco ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-numero" class="form-group">Número</label>
                                        <input type="text" class="form-control" id="frm-numero" name="frm-numero" value="<?php echo @$numero ?>">
                                    </div> 
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="frm-complemento" class="form-group">Complemento</label>
                                        <input type="text" class="form-control" id="frm-complemento" name="frm-complemento"  value="<?php echo @$complemento ?>">
                                    </div>  
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-bairro" class="form-group">Bairro</label>
                                        <input type="text" class="form-control" id="bairro" name="frm-bairro" value="<?php echo @$bairro ?>">
                                    </div> 
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-cidade" class="form-group">Cidade</label>
                                        <input type="text" class="form-control" id="cidade" name="frm-cidade" value="<?php echo @$cidade ?>">
                                    </div>  
                                </div>
                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="frm-uf_endereco" class="form-group">Estado</label>
                                        <input type="text" class="form-control" id="estado" name="frm-uf_endereco"  value="<?php echo @$uf_endereco ?>">
                                    </div> 
                                </div>

                            </div>
<hr>
                            <div class="row">
                                <h3> Dados bancários </h3>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-banco_if" class="form-group">Banco</label>
                                        <input type="text" class="form-control" id="frm-banco_if" name="frm-banco_if"  value="<?php echo @$banco_if ?>">
                                    </div> 
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-agencia" class="form-group">Agência</label>
                                        <input type="text" class="form-control" id="frm-agencia" name="frm-agencia" value="<?php echo @$agencia ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-conta" class="form-group">Conta</label>
                                        <input type="text" class="form-control" id="frm-conta" name="frm-conta"  value="<?php echo @$conta ?>">
                                    </div> 
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-pix" class="form-group">Pix</label>
                                        <input type="text" class="form-control" id="frm-pix" name="frm-pix"  value="<?php echo @$pix ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-tipo_pix" class="form-group">Tipo Pix</label>
                                        
                                            <select class="form-select" id="frm-tipo_pix" aria-label="Default select example" name="frm-tipo_pix">
                                                
                                                <option <?php if(@$tipo_pix == '--'){ ?> selected <?php } ?>  value="--">--</option>

                                                <option <?php if(@$tipo_pix == 'CPF'){ ?> selected <?php } ?>  value="CPF">CPF</option>
                                                <option <?php if(@$tipo_pix == 'Telefone'){ ?> selected <?php } ?>  value="Telefone">Telefone</option>

                                                <option <?php if(@$tipo_pix == 'CNPJ'){ ?> selected <?php } ?>  value="CNPJ">CNPJ</option>
                                                
                                                <option <?php if(@$tipo_pix == 'Email'){ ?> selected <?php } ?>  value="Email">Email</option>

                                                <option <?php if(@$tipo_pix == 'Chave Aleatoria'){ ?> selected <?php } ?>  value="Chave Aleatoria">Chave Aleatoria</option>

                                                
                                            </select>
                                    </div>  
                                </div>

                            </div>
<hr >

                            <div class="row">
                                <h3> Informações Adicionais </h3>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-rg" class="form-group">RG</label>
                                        <input type="text" class="form-control" id="frm-rg" name="frm-rg"   value="<?php echo @$rg ?>">
                                    </div> 
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-orgao" class="form-group">Orgão</label>
                                        <input type="text" class="form-control" id="frm-orgao" name="frm-orgao" value="<?php echo @$orgao ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-data_exp" class="form-group">Data Emissão</label>
                                        <input type="date" class="form-control" id="frm-data_exp" name="frm-data_exp" value="<?php echo @$data_exp ?>">
                                    </div> 
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-nacionalidade" class="form-group">Nacionalidade</label>
                                        <input type="text" class="form-control" id="frm-nacionalidade" name="frm-nacionalidade" value="<?php echo @$nacionalidade ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-data_chegada_brasil" class="form-group">Chegada ao Brasil</label>
                                        <input type="date" class="form-control" id="frm-data_chegada_brasil" name="frm-data_chegada_brasil" value="<?php echo @$data_chegada_brasil ?>">
                                    </div>  
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-naturalidade" class="form-group">Naturalidade</label>
                                        <input type="text" class="form-control" id="frm-naturalidade" name="frm-naturalidade"   value="<?php echo @$naturalidade ?>">
                                    </div>  
                                </div>

                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="frm-uf_naturalidade" class="form-group">Estado</label>
                                        <input type="text" class="form-control" id="frm-uf_naturalidade" name="frm-uf_naturalidade" value="<?php echo @$uf_naturalidade ?>">
                                    </div> 
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-etinia" class="form-group">Etnia</label>
                                        <input type="text" class="form-control" id="frm-etinia" name="frm-etinia"  value="<?php echo @$etinia ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-tp_sanguineo" class="form-group">Sangue</label>
                                        <input type="text" class="form-control" id="frm-tp_sanguineo" name="frm-tp_sanguineo"  value="<?php echo @$tp_sanguineo ?>">
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-escolaridade" class="form-group">Escolaridade</label>
                                        <select class="form-select" aria-label="Default select example" id="frm-escolaridade" name="frm-escolaridade>
                                            <option <?php if(@$escolaridade == '--'){ ?> selected <?php } ?>  value="--">--</option>
                                            <option <?php if(@$escolaridade == 'Primário'){ ?> selected <?php } ?>  value="Primário">Primário</option>
                                            <option <?php if(@$escolaridade == 'Médio'){ ?> selected <?php } ?>  value="Médio">Médio</option>
                                            <option <?php if(@$escolaridade == 'Superior'){ ?> selected <?php } ?>  value="Superior">Superior</option>
                                            <option <?php if(@$escolaridade == 'Pôs graduação'){ ?> selected <?php } ?>  value="Pôs graduação">Pôs graduação</option>
                                            <option <?php if(@$escolaridade == 'Doutorado'){ ?> selected <?php } ?>  value="Doutorado">Doutorado</option>
                                            <option <?php if(@$escolaridade == 'Pôs Doutorado'){ ?> selected <?php } ?>  value="Pôs Doutorado">Pôs Doutorado</option>
                                        </select>
                                    </div> 
                                </div>

                            </div>


                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="frm-nome_mae" class="form-group">Nome da Mãe</label>
                                        <input type="text" class="form-control" id="frm-nome_mae" name="frm-nome_mae"  value="<?php echo @$nome_mae ?>">
                                    </div> 
                                </div>

                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="frm-nome_pai" class="form-group">Nome do Pai</label>
                                        <input type="text" class="form-control" id="frm-nome_pai" name="frm-nome_pai"   value="<?php echo @$nome_pai ?>">
                                    </div>  
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-group" for="frm-est_civil">Estado Civil</label>
                                        
                                            <select class="form-select" id= "frm-est_civil" aria-label="Default select example" name="frm-est_civil">
                                                
                                                <option <?php if(@$est_civil == '--'){ ?> selected <?php } ?>  value="--">--</option>

                                                <option <?php if(@$est_civil == 'Solteiro'){ ?> selected <?php } ?>  value="Solteiro">Solteiro</option>

                                                <option <?php if(@$est_civil == 'Solteiro'){ ?> selected <?php } ?>  value="União Estável">União Estável</option>

                                                <option <?php if(@$est_civil == 'Casado'){ ?> selected <?php } ?>  value="Casado">Casado</option>
                                                
                                                <option <?php if(@$est_civil == 'Separado'){ ?> selected <?php } ?>  value="Separado">Separado</option>

                                                <option <?php if(@$est_civil == 'Desquitado'){ ?> selected <?php } ?>  value="Desquitado">Desquitado</option>

                                                
                                            </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="frm-nome_conj" class="form-group">Nome Cônjuge</label>
                                        <input type="text" class="form-control" id="frm-nome_conj" name="frm-nome_conj"  value="<?php echo @$nome_conj ?>">
                                    </div> 
                                </div>

                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="frm-dados_conj" class="form-group">Dados Cônjuge</label>
                                        <input type="text" class="form-control" id="frm-dados_conj" name="frm-dados_conj"   value="<?php echo @$dados_conj ?>">

                                    </div>  
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-cnh" class="form-group">CNH</label>
                                        <input type="text" class="form-control" id="frm-cnh" name="frm-cnh"  value="<?php echo @$cnh ?>">
                                    </div> 
                                </div>
                                
                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="frm-cnh_categoria" class="form-group">Cat.</label>
                                        <input type="text" class="form-control" id="frm-cnh_categoria" name="frm-cnh_categoria"  value="<?php echo @$cnh_categoria ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-cnh_dt_validade" class="form-group">Validade CNH</label>
                                        <input type="date" class="form-control" id="frm-cnh_dt_validade" name="frm-cnh_dt_validade"  value="<?php echo @$cnh_validade ?>">
                                    </div>  
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-ctps" class="form-group">CTPS</label>
                                        <input type="text" class="form-control" id="frm-ctps" name="frm-ctps"  value="<?php echo @$ctps ?>">
                                    </div> 
                                </div>

                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="frm-serie" class="form-group">Série</label>
                                        <input type="text" class="form-control" id="frm-serie" name="frm-serie" value="<?php echo @$serie ?>">
                                    </div>  
                                </div>


                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-pis" class="form-group">PIS/NIS</label>
                                        <input type="text" class="form-control" id="frm-pis" name="frm-pis" p value="<?php echo @$pis ?>">
                                    </div> 
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-pis_dt_cadastro" class="form-group">Data Cad PIS</label>
                                        <input type="date" class="form-control" id="frm-pis_dt_cadastro" name="frm-pis_dt_cadastro"  value="<?php echo @$pis_dt_cadastro ?>">
                                    </div>  
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-e_social" class="form-group">E-social</label>
                                        <input type="text" class="form-control" id="frm-e_social" name="frm-e_social" value="<?php echo @$e_social ?>">
                                    </div>  
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-titulo" class="form-group">Título</label>
                                        <input type="text" class="form-control" id="frm-titulo" name="frm-titulo" value="<?php echo @$titulo ?>">
                                    </div>  
                                </div>

                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="frm-zona" class="form-group">Zona</label>
                                        <input type="text" class="form-control" id="frm-zona" name="frm-zona" value="<?php echo @$zona ?>">
                                    </div> 
                                </div>

                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="frm-sessao" class="form-group">Sessão</label>
                                        <input type="text" class="form-control" id="frm-sessao" name="frm-sessao" value="<?php echo @$sessao ?>">
                                    </div>  
                                </div>
                            
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-conta_fgts" class="form-group">FGTS</label>
                                        <input type="text" class="form-control" id="frm-conta_fgts" name="frm-conta_fgts"  value="<?php echo @$conta_fgts ?>">
                                    </div>  
                                </div>
                                

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-fgts_dt_opcao" class="form-group">Data de Opção</label>
                                        <input type="date" class="form-control" id="frm-fgts_dt_opcao" name="frm-fgts_dt_opcao"  value="<?php echo @$fgts_dt_opcao ?>">
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-cert_reservista" class="form-group">Cert. Reservista</label>
                                        <input type="text" class="form-control" id="frm-cert_reservista" name="frm-cert_reservista" value="<?php echo @$cert_reservista ?>">
                                    </div>  
                                </div>
                                
                            </div>
                           
<hr>

                            <div class="row">
                                <h3> Pessoa Com Deficiância </h3>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="frm-deficiente_sim_nao" style="padding-bottom: 3px;" class="form-group">Deficiente</label>
                                        <span class="spanRadio">
                                            <input type="radio" style ="margin-right: 5px;" id="frm-deficiente_sim" name="frm-deficiente_sim_nao" value="1" <?php echo $deficiente_sim_nao ? 'checked' : ''; ?>>Sim
                                        </span>
                                        <span class="spanRadio" style="margin-left: 10px;">
                                            <input type="radio" style ="margin-right: 5px;" id="frm-deficiente_nao" name="frm-deficiente_sim_nao" value="0" <?= !$deficiente_sim_nao ? 'checked' : ''; ?>>Não
                                        </span>

                                    </div>  
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-tp_deficiencia" class="form-group">Tipo de Deficiência</label>
                                        <input type="text" class="form-control" id="frm-tp_deficiencia" name="frm-tp_deficiencia"   value="<?php echo @$tp_deficiencia ?>">
                                    </div> 
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="frm-deficiencia" class="form-group">Deficiência</label>
                                        <input type="text" class="form-control" id="frm-deficiencia" name="frm-deficiencia" value="<?php echo @$deficiencia ?>">
                                    </div> 
                                </div>
                            </div>             
<hr>

                         
                            
                        </div>     <!-- fechamento da tab 1-->

                        <!-- TAB CONTRATOS - Inicio da TaB 2-->
                        <div class="tab-pane fade" id="aba-contrato" role="tabpanel" aria-labelledby="contrato-tab">

                            <div class="row pt-3">
                                <h3> Informações Principais do Contrato:</h3>
                                <div class="col-auto BlockItem ">
                                    <label for="numero_contrato" class="form-group">Contrato: <label>
                                    <input type="" readonly class="form-control blockItem" id="numero_contrato" value="<?=$numero_contrato?>">
                                </div>

                                <div class="col-auto">
                                    <label for="tipo_contrato" class="form-group">Tipo de Contrato: <label>
                                    <input type="text" class="form-control" id="tipo_contrato" value="<?=$tipo_contrato?>">
                                </div>
                                <div class="col-auto">
                                    <label for="data_contrato" class="form-group">Data Contrato: <label>
                                    <input type="date" class="form-control" id="data_contrato" value="<?=$data_contrato?>">
                                </div>
                                <div class="col-auto">
                                    <label for="experiencia_contrato" class="form-group">Experiência: <label>
                                    <input type="date" class="form-control" id="experiencia_contrato" value="<?=$experiencia?>">
                                </div>

                                <div class="col-auto">
                                    <label for="vencimento_contrato" class="form-group">Vencimento: <label>
                                    <input type="date" class="form-control" id="vencimento_contrato" value="<?=$vencimento?>">
                                </div>
                                <div class="col-auto">
                                    <label style="padding-bottom: 3px;" class="form-group">Renovação Autmática:</label>
                                    <span class="spanRadio">
                                        <input type="radio" style ="margin-right: 5px;" id="renova_auto_sim" name="rd_renovacao" value="1" <?php echo $renova_auto ? 'checked' : ''; ?>>Sim
                                    </span>
                                    <span class="spanRadio" style="margin-left: 10px;">
                                        <input type="radio" style ="margin-right: 5px;" id="renova_auto_nao" name="rd_renovacao" value="0" <?= !$renova_auto && $renova_auto? 'checked' : ''; ?>>Não
                                    </span>

                                </div>
                            </div>
                            <hr>
                            <div class="row mt-3">
                                <h3>Cargo e Função:</h3>
                                 <div class="col-auto">
                                    <label for="cargo_contrato" class="form-group">Cargo: <label>
                                    <input type="text" class="form-control" id="cargo_contrato" value="<?=$cargo?>">
                                </div>

                                <div class="col-auto">
                                    <label for="funcao_contrato" class="form-group">Função: <label>
                                    <input type="text" class="form-control" id="funcao_contrato" value="<?=$funcao?>">
                                </div>

                                <div class="col-auto">
                                    <label for="departamento_contrato" class="form-group">Departamento: <label>
                                    <input type="text" class="form-control" id="departamento_contrato" value="<?=$departamento?>">
                                </div>
                                <div class="col">
                                    <label for="atribuicao_funcao_contrato" class="form-group">Atribuições da Função: <label>
                                    <textarea type="text" class="form-control" id="atribuicao_funcao_contrato" value="<?=$atribuicao_funcao?>"></textarea>
                                </div>

                            </div>
                            <hr>
                            <div class="row mt-3">
                                <h3>Atualizações:</h3>
                                <div class="col-auto">
                                    <label for="situacao_contrato" class="form-group">Situação: <label>
                                    <input type="text" class="form-control" id="situacao_contrato" value="<?=$situcao_contrato?>">
                                </div>
                                <div class="col-auto">
                                    <label for="status_contrato" class="form-group">Status: <label>
                                    <input type="text" class="form-control" id="status_contrato" value="<?=$status_contrato?>">
                                </div>
                                <div class="col">
                                    <label for="anotacao_contrato" class="form-group">Anotações: <label>
                                    <input type="text" class="form-control" id="anotacao_contrato" value="<?=$anotacoes_contrato?>">
                                </div>
                             </div>
                             <hr>
                             <div class="row mt-3">
                                <h3>Pagamentos:</h3>
                                <div class="col-auto">
                                    <label for="periodo_pagamentos_contrato" class="form-group">Período Pagamentos: <label>
                                    <input type="text" class="form-control" id="periodo_pagamentos_contrato" value="<?=$periodo_pagamentos?>">
                                </div>
                                <div class="col-auto">
                                    <label for="valor_pagamentos_contrato" class="form-group">Valor Pagamentos: <label>
                                    <input type="text" class="form-control" id="valor_pagamentos_contrato" value="<?=$valor_pagamentos?>">
                                </div>
                                <div class="col">
                                    <label for="observacoes_pagamentos_contrato" class="form-group">Observações Pagamentos: <label>
                                    <input type="text" class="form-control" id="observacoes_pagamentos_contrato" value="<?=$observacoes_pagamentos?>">
                                </div>
                            </div>
                            <hr>
                            <h3>Jornada de Trabalho:</h3>
                            <div class="row mt-3">
                                <div class="col-auto">
                                    <label for="" style="padding-bottom: 3px;" class="form-group">Flexivel?</label>
                                    <span class="spanRadio">
                                        <input type="radio" style ="margin-right: 5px;" id="flexivel_jornada_sim" name="flexivel_jornava" value="1" <?php echo $flexivel_jornada ? 'checked' : ''; ?>>Sim
                                    </span>
                                    <span class="spanRadio" style="margin-left: 10px;">
                                        <input type="radio" style ="margin-right: 5px;" id="flexivel_jornada_nao" name="flexivel_jornada" value="0" <?= !$flexivel_jornada ? 'checked' : ''; ?>>Não
                                    </span>

                                </div> 
                                <div class="col-auto">
                                    <label for="jornada_diaria_contrato" class="form-group">Jornada Diária: <label>
                                    <input type="text" class="form-control" id="jornada_diaria_contrato" value="<?=$jornada_diaria?>">
                                </div>
                                <div class="col-auto">
                                    <label for="jornada_semanal_contrato" class="form-group">Jornada Semanal: <label>
                                    <input type="text" class="form-control" id="flexivel_contrato" value="<?=$jornada_semanal?>">
                                </div>
                                <div class="col-auto">
                                    <label  style="padding-bottom: 3px;" class="form-group">Banco de Horas:</label>
                                    <span class="spanRadio">
                                        <input type="radio" style ="margin-right: 5px;" id="banco_horas_sim" name="banco_horas" value="1" <?php echo $banco_horas ? 'checked' : ''; ?>>Sim
                                    </span>
                                    <span class="spanRadio" style="margin-left: 10px;">
                                        <input type="radio" style ="margin-right: 5px;" id="banco_horas_nao" name="banco_horas" value="0" <?= !$banco_horas ? 'checked' : ''; ?>>Não
                                    </span>
                                </div> 
                                <div class="col-auto">
                                    <label for="jornada_semanal_contrato" class="form-group">Quadro Horário: <label>
                                    <select class="form-select" id="quadro_horario">
                                        <?php
                                        foreach ($quadroHorarios as $quadroHorario){
                                            echo '<option value="'.$quadrohorario['id'] .'" ' . ($quadroHorario['id']==$quadro_horario_id)?"checked":"" . ' >'.$quadroHorario['nome'].'</option>
                                            ';

                                        }
                                        ?>
                                        
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h3>Benefícios:</h3>
                            <div class="row mt-3">
                                <div class="col-auto">
                                    <label style="padding-bottom: 3px;" class="form-group">Vale Alimentação/Refeição</label>
                                    <span class="spanRadio">
                                        <input type="radio" style ="margin-right: 5px;" id="va_vr_sim" name="va_vr" value="1" <?php echo $va_vr ? 'checked' : ''; ?>>Sim
                                    </span>
                                    <span class="spanRadio" style="margin-left: 10px;">
                                        <input type="radio" style ="margin-right: 5px;" id="va_vr_nao" name="va_vr" value="0" <?= !$va_vr ? 'checked' : ''; ?>>Não
                                    </span>
                                </div>
                                 <div class="col-auto">
                                    <label for="va_vr_valor" class="form-group">Valor: <label>
                                    <input type="text" class="form-control" id="va_vr_valor" value="<?=$va_vr_valor?>">
                                </div>
                                 <div class="col">
                                    <label for="va_vr_obs" class="form-group">Observações:<label>
                                    <input type="text" class="form-control" id="va_vr_obs" value="<?=$va_vr_obs?>">
                                </div>
                                
                            </div>
                            <div class="row mt-3"> 
                                <div class="col-auto">
                                    <label for="" style="padding-bottom: 3px;" class="form-group">Vale Transporte:</label>
                                    <span class="spanRadio">
                                        <input type="radio" style ="margin-right: 5px;" id="vt_sim" name="vt_sim_nao" value="0" <?php echo $vt_sim_nao==0? 'checked' : ''; ?>>Não
                                    </span>
                                    <span class="spanRadio" style="margin-left: 10px;">
                                        <input type="radio" style ="margin-right: 5px;" id="vt_nao" name="vt_sim_nao" value="1" <?= !$vt_sim_nao==1 ? 'checked' : ''; ?>>Sim
                                    </span>
                                    <span class="spanRadio" style="margin-left: 10px;">
                                        <input type="radio" style ="margin-right: 5px;" id="vt_ajuda_custo" name="vt_sim_nao" value="2" <?= !$vt_sim_nao==2 ? 'checked' : ''; ?>>Ajuda de Custo
                                    </span>
                                </div>
                                <div class="col-auto">
                                        <label for="valor_dia_vt" class="form-group">Valor Diário: <label>
                                        <input type="text" class="form-control" id="valor_dia_vt" value="<?=$valor_dia_vt?>">

                                </div>
                                <div class="col">
                                    <label for="vt_obs" class="form-group">Observações<label>
                                    <textarea type="text" class="form-control" id="vt_obs" ><?=$vt_obs?></textarea>
                                </div>

                            </div>
                            <hr>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="vt_obs" class="form-group">Alterações:<label>
                                    <textarea type="text" class="form-control" id="alteracoes_contrato" value="<?=$alteracoes_contrato?>"></textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-3">
                                <h3> Dados do Encerramento:</h3>
                                <div class="col-auto">
                                    <label for="aviso_previo" class="form-group">Aviso Previo: <label>
                                    <input type="text" class="form-control" id="aviso_previo" value="<?=$aviso_previo?>">
                                </div>
                                <div class="col-auto">
                                    <label for="data_aviso_previo" class="form-group">Data Aviso: <label>
                                    <input type="date" class="form-control" id="data_aviso_previo" value="<?=$data_aviso_previo?>">
                                </div>
                                <div class="col-auto">
                                    <label for="motivo_termino" class="form-group">Motivo Término: <label>
                                    <input type="text" class="form-control" id="motivo_termino" value="<?=$motivo_termino?>">
                                </div>
                                <div class="col-auto">
                                    <label for="data_fim_contrato" class="form-group">Data Encerramento: <label>
                                    <input type="text" class="form-control" id="data_fim_contrato" value="<?=$data_fim_contrato?>">
                                </div>
                                <div class="col-auto">
                                    <label for="" style="padding-bottom: 3px;" class="form-group">Encerrado:</label>
                                    <span class="spanRadio">
                                        <input type="radio" style ="margin-right: 5px;" id="contrato_encerrado_sim" name="contrato_encerrado" value="0" <?php echo !$contrato_encerrado? 'checked' : ''; ?>>Não
                                    </span>
                                    <span class="spanRadio" style="margin-left: 10px;">
                                        <input type="radio" style ="margin-right: 5px;" id="contrato_encerrado_nao" name="contrato_encerrado" value="1" <?= $contrato_encerrado? 'checked' : ''; ?>>Sim
                                    </span>
                                </div>
                            </div>

                           
                        </div>
                        <!-- TAB SEERVICOS Inicio da TaB 3-->
                        <div class="tab-pane fade" id="aba-servicos" role="tabpanel" aria-labelledby="servicos-tab">
                            <div class="container-md listTabContainer mt-3">
                                <div class="table-container" id="servicos-container-modal">
                                    <div class="row mb-2">
                                        <div class="col">
                                            <input type="text" class="form-control searchBox" placeholder="Pesquisar agendamento...">
                                        </div>
                                        <div class="col">
                                            <button type="button" id="btnMinServicos" class="btn-toggle-cols btn btn-primary" title="Mostrar/ocultar colunas">
                                                Detalhar Consumo:
                                                <i class="bi bi-chevron-right"></i>
                                            </button>
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
                                        class="tablesModColaborador dataTable dTlinhaFina"
                                        data-table="venda_itens" data-minimized="false"
                                        
                                        data-filtro='{
                                                    "logic":"AND",
                                                    "conditions":[
                                                        {"field":"id_colaborador", "op":"=", "value":<?= $id_colaborador ?>},
                                                        {"field":"tipo_item", "op":"=", "value":"servico"},
                                                        {"field":"venda", "op":"=", "value":"1"}
                                                        ]
                                                    }'
                                        style="width:100%"><!-- Substitua 123 pelo id_colaborador do colaborador atual via JS -->
                                        <thead>
                                        <tr>
                                            <th data-field="data_venda" data-sort="data" >Data</th>
                                            <th data-field="tipo_venda" data-sort="a-z">Operação</th>
                                            <th data-field="id" hidden>ID</th>
                                            <th data-field="id_venda" hidden>ID</th> 
                                            <th data-field="quantidade" class="texto-vertical" data-classe-td="sMais" data-sort="num">Adquiridos</th>
                                            <th data-field="item" data-sort="a-z">Serviço</th>
                                            <th data-field="realizados" class="texto-vertical minimizar" data-classe-td="num-negativo minimizar"   data-sort="num">Realizados</th>
                                            <th data-field="convertidos" class="texto-vertical minimizar" data-classe-td="num-negativo minimizar"  data-sort="num">Convertidos</th>
                                            <th data-field="transferidos" class="texto-vertical minimizar" data-classe-td="num-negativo minimizar" data-sort="num">Transferidos</th>
                                            <th data-field="descontados" class="texto-vertical minimizar" data-classe-td="num-negativo minimizar"  data-sort="num">Descontados</th>
                                            <th data-field="consumidos" data-classe-td="text-center" data-sort="num" class="th-consumidos">
                                                <div class="header-consumidos">
                                                     <span class="span-consumidos">Consumidos</span>
                                                </div>
                                            </th>
                                            <th data-field="saldo" class="" data-sort="num" data-sort-init="DESC" data-classe-td="sIgual" style="text-align:center;">Saldo</th>
                                            <th data-field="data_validade"  data-sort="num">Validade</th>
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
                        </div>
                        <!-- TAB FINANCEIRO Inicio da TaB 4-->
                        <div class="tab-pane fade" id="aba-financeiro" role="tabpanel" aria-labelledby="financeiro-tab">
                            <div class="container-md listTabContainer mt-3">
                                <div class="table-container" id="financeiro-container-modal">
                                    <div class="row mb-2">
                                        <div class="col-auto">
                                            <input type="text" class="form-control searchBox" placeholder="Pesquisar...">
                                        </div>

                                                                                
                                        <div class="col-auto ms-auto d-flex align-items-center">
                                            <span class="me-2" style="font-size:0.9em;">Saldo do Colaborador:</span>
                                            <input 
                                                type="text" 
                                                class="form-control blockItem" 
                                                style="width: auto; color:<?=$saldo_colaborador<0?'red':'green'?>;" 
                                                value="R$ <?= number_format($saldo_colaborador, 2, ',', '.') ?>">
                                        </div>
                                    </div>
                                    <table 
                                        class="tablesModColaborador dataTable dTlinhaFina"
                                        data-table="venda"
                                        
                                        data-filtro='{
                                                        "logic":"AND",
                                                        "conditions":[
                                                            {"field":"id_colaborador", "op":"=", "value":<?= $id_colaborador ?>},
                                                            {"field":"tipo_venda", "op":"IN", "value":[
                                                                                                        "conversao", 
                                                                                                        "debito",
                                                                                                        "venda", 
                                                                                                        "enviado",
                                                                                                        "recebido"
                                                                                                    ]
                                                            }
                                                        ]
                                                     }'                                           
                                        style="width:100%"><!-- Substitua 123 pelo id_colaborador do colaborador atual via JS -->
                                        <thead>
                                        <tr>
                                            <th data-field="data_venda" data-sort="data" >Data</th>
                                            <th data-field="tipo_venda" data-sort="a-z">Origem</th>
                                            <th data-field="id" data-sort="num" data-sort-init="DESC" hidden>ID</th>
                                            <th data-field="valor_final"  data-sort="num" class="text-end" data-classe-td="posNeg numero reais text-end">Débito</th>
                                            <th data-field="total_pagamentos"  class="text-end" data-classe-td="posNeg numero reais text-end" data-sort="num">Crédito</th>
                                            <th data-field="saldo"  class="text-end"data-classe-td="posNeg numero reais text-end" data-sort="num">Saldo</th>
                                            <th data-field="saldo_na_data" class="text-end" data-classe-td ="posNeg numero reais text-end" data-sort="num" >Saldo na Data</th>
                                        </tr>
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
                        <!-- TAB DOCUMENTOS Inicio da TaB 4-->
                        <div class="tab-pane fade" id="aba-documentos" role="tabpanel" aria-labelledby="documentos-tab">
                           <div class="container-md mt-3">
                                <div class="container-documento-add p-3 mb-3" style="display:none; background-color:#f8f9fa;">
                                    <!-- Preview da imagem -->
                                    
                                            <div class="mb-3 row">
                                                <div class="col-auto">
                                                    <i
                                                        id="preview-documento"
                                                        class="bi bi-cloud-plus"
                                                        style= "font-size:25px;
                                                                margin-top: 15px;
                                                                background:transparent;
                                                                border:none;
                                                                cursor:pointer;
                                                                "
                                                    ></i>                                
                                                    <input
                                                        type="file"
                                                        id="documentoUpload"
                                                        accept=""
                                                        style="display:none;"
                                                    >
                                                </div>
                                                <div class="col mb-3">
                                                <label for="documentoTitulo" class="form-group">
                                                    <i class="bi bi-card-text"></i> Título
                                                </label>
                                                <input type="text" id="documentoTitulo" class="form-control" placeholder="Título do Documento">
                                                <!-- Coloque este span aqui -->
                                                <span id="preview-documento-nome" class="text-muted small mt-1 d-block"></span>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <div class="col-md-6">
                                                    <label for="documentoData" class="form-group"><i class="bi bi-calendar-event"></i> Data</label>
                                                    <input type="date" id="documentoData" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="documentoTipo" class="form-group"><i class="bi bi-list-ul"></i> Tipo de documento</label>
                                                    <select id="documentoTipo" class="form-select">
                                                        <option value="">Selecione...</option>
                                                        <option value="Termo">Termo</option> 
                                                        <option value="Autorização">Autorização</option>
                                                        <option value="Anamnese">Anamnese</option>
                                                        <option value="Contrato">Contrato</option>
                                                        <option value="Atestado">Atestado</option>
                                                        <option value="Acordo">Acordo</option>
                                                        <option value="Extrato">Extrato</option>
                                                        <option value="Comprovante">Comprovante</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="documentooDescricao" class="form-group"><i class="bi bi-pencil-square"></i> Descrição</label>
                                                <textarea id="documentoDescricao" class="form-control" style="height:115px;" placeholder="Descrição do Documento"></textarea>
                                            </div>

                                    <hr class="mt-4" style="border-color:rgb(255, 255, 255);">
                                    <div class="row">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" id="btn-cancelar-documento" class="btn btn-secondary me-2">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="button" id="btn-salvar-documento" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Salvar Doc
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                 
                                <div class="table-container" id="documentos-container-modal">
                                    <div class="row mb-2">
                                        <div class="col-auto">
                                            <button 
                                                class="btn btn-primary" 
                                                type="button" 
                                                id="btn-novo-documento"
                                                title="Novo Documento"
                                                onclick="">
                                                <i class="bi bi-file-earmark-text"></i>
                                                Novo Documento +
                                            </button>
                                        </div>
                                        <div class="col-auto ms-auto d-flex align-items-center">
                                            <input type="text" class="form-control searchBox" placeholder="Pesquisar...">
                                        </div>
                                    </div>

                                    <table 
                                        class="tablesModColaborador dataTable dTlinhaFina"
                                        data-table="colaboradores_arquivos"
                                        
                                         data-filtro='{
                                                    "logic":"AND",
                                                    "conditions":[
                                                        {"field":"id_colaborador", "op":"=", "value":<?= $id_colaborador ?>}
                                                    ]
                                                    }'
                                        style="width:100%"><!-- Substitua 123 pelo id_colaborador do colaborador atual via JS -->
                                        <thead>
                                        <tr>
                                            <th  
                                                data-field="extensao" 
                                                class="data-documento" 
                                                data-href-field="arquivo"
                                                data-href-base="../<?=$pasta?>/documentos/colaboradores/" 
                                                data-documento="../<?=$pasta?>/documentos/colaboradores/galeria/mini/">Arquivo
                                            </th>
                                            <th data-field="id" data-sort-init="DESC" hidden>ID</th>
                                            <th data-field="arquivo" hidden>–</th>
                                            <th data-field="titulo" data-sort="a-z">Título:</th>
                                            <th data-field="data_arquivo"  data-sort="data">Data</th>
                                            <th data-field="tipo_arquivo"  data-sort="a-z">Tipo de Documento</th>
                                            <th data-field="descricao">Descrição</th>
                                            <th data-field="acoes"   
                                                data-edit-func="abrirFormDocumento"
                                                data-delete-func="excluirDocumento"
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
                        <!-- TAB GALERIA Inicio da TaB 5-->
                        <div class="tab-pane fade" id="aba-galeria" role="tabpanel" aria-labelledby="galeria-tab">
                           <div class="container-md mt-3">
                                <div class="container-foto-add p-3 mb-3" style="display:none; background-color:#f8f9fa;">
                                    <!-- Preview da imagem -->
                                    <div class="row">
                                        <div class="col-md-4 d-flex justify-content-center align-items-center">
                                            <img
                                                id="preview-foto"
                                                src="../img/sem-foto.svg"
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
                                        class="tablesModColaborador dataTable dTlinhaFina"
                                        data-table="colaboradores_fotos"
                                        
                                         data-filtro='{
                                                        "logic":"AND", 
                                                        "conditions":[
                                                            {"field":"id_colaborador", "op":"=", "value":"<?= $id_colaborador ?>"}
                                                            ]
                                                        }'
                                        style="width:100%"><!-- Substitua 123 pelo id_colaborador do colaborador atual via JS -->
                                        <thead>
                                        <tr>
                                            <th  
                                                data-field="arquivo_mini" 
                                                class="data-foto" 
                                                data-href-field="arquivo_ori"
                                                data-href-base="../<?=$pasta?>/img/colaboradores/galeria/" 
                                                data-foto="../<?=$pasta?>/img/colaboradores/galeria/mini/">Foto
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
                        <button type="button" id="btn-fechar_colaborador" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" name="btn-salvar_colaborador" id="btn-salvar_colaborador"  class="btn btn-primary">Salvar</button>
                    </div> 
                    <input name="id" type="hidden" value="<?=  $id_colaborador?>">
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




    var saldoColaborador = parseFloat("<?= $saldo_colaborador ?>") || 0;

</script>


<script type="text/javascript" src="colaboradores/tabelasModalColaboradores.js?v=0.01"></script> <!-- meu-->

<script type="text/javascript" src="colaboradores/modalColaboradores.js?v=0.06"></script>