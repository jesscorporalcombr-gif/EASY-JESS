<?php

$pag = 'clientes';
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
$pasta = $_SESSION['x_url'] ?? '';


if(isset($_POST['id']) && !empty($_POST['id'])) {
    $id_cliente = $_POST['id'];
    $aba = $POST['aba'];

    // Preparando a consulta PDO
    $query = $pdo->prepare("SELECT * FROM clientes WHERE id = :id_cliente");
    $query->execute([':id_cliente' => $id_cliente]);
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    $total_reg = @count($res);
   
    $tipo_cadastro= "edicao";
    
    if($total_reg > 0){ 
            $nome = $res[0]['nome'];
            $email = $res[0]['email'];
            $cpf = $res[0]['cpf'];
            $senha = $res[0]['senha'];
            $nivel = $res[0]['nivel'];
            $birth = $res[0]['aniversario'];
            $saldo_cliente= $res[0]['saldo'];
            
            

            $formatos = [
                'Y-m-d',    // 2025-07-05
                'd/m/Y',    // 05/07/2025
                'j/n/Y',    // 5/7/2025
                'd-m-Y',    // 05-07-2025
                'j-n-y',    // 5-7-25
                'm/d/Y',    // 07/05/2025
            ];

            /**
            * Converte várias representações de data para 'Y-m-d'.
            *
            * @param string   $data      String de data (ex: '05/07/2025', '5/7/25', '2025-07-05' etc.).
            * @param string[] $formatos  Array de formatos para testar.
            * @return string             Data no padrão 'Y-m-d' ou '' se nenhum formato casar.
            */
            function converterDataParaISO(string $data, array $formatos): string
            {
                $data = trim($data);
                foreach ($formatos as $fmt) {
                    // tenta criar a data no formato atual
                    $dt = DateTime::createFromFormat($fmt, $data);
                    if ($dt !== false) {
                        // deu certo -> normaliza para ISO
                        return $dt->format('Y-m-d');
                    }
                }
                // nenhum formato válido
                return '';
            }
            // --- uso ---


            /**
            * @param  string|int  $cpf  Sequência de dígitos do CPF (podem vir menos de 11).
            * @return string           CPF formatado no padrão NNN.NNN.NNN-NN
            */
            function formatarCPF($cpf): string
            {
                // 1) converte pra string e remove tudo que não for dígito
                $digitos = preg_replace('/\D/', '', (string)$cpf);

                // 2) preenche zeros à esquerda até ter exatamente 11 dígitos
                $digitos = str_pad($digitos, 11, '0', STR_PAD_LEFT);

                // 3) separa em 4 partes: 3, 3, 3 e 2 dígitos
                $p1 = substr($digitos, 0, 3);
                $p2 = substr($digitos, 3, 3);
                $p3 = substr($digitos, 6, 3);
                $p4 = substr($digitos, 9, 2);

                // 4) monta a string formatada
                return "{$p1}.{$p2}.{$p3}-{$p4}";
            }
            function formatarCEP($cep): string
            {
                // 1) converte pra string e remove tudo que não for dígito
                $digitos = preg_replace('/\D/', '', (string)$cep);

                // 2) preenche zeros à esquerda até ter exatamente 8 dígitos
                $digitos = str_pad($digitos, 8, '0', STR_PAD_LEFT);

                // 3) separa em duas partes: 5 dígitos + 3 dígitos
                $parte1 = substr($digitos, 0, 5);
                $parte2 = substr($digitos, 5, 3);

                // 4) monta a string formatada
                return "{$parte1}-{$parte2}";
            }


            /**
            * Formata um número de telefone no estilo internacional/brasileiro:
            * - elimina tudo que não for dígito
            * - detecta DDI +55 + DDD brasileiro ou só DDD
            * - para internacional, assume últimos 11 dígitos como DDI+DDD+num
            * - coloca parênteses no DDD e espaço antes dos últimos 4 dígitos
            * - retorna "NI-<raw>" se não houver dígitos suficientes
            *
            * @param  string|int  $input  Qualquer string ou número contendo o telefone.
            * @return string              Telefone formatado ou "NI-<raw>" se inválido.
            */
            function formatarTelefone($input): string
            {
                // 1) Só dígitos
                $raw = preg_replace('/\D/', '', (string)$input);

                // 2) Lista oficial de DDDs brasileiros
                $BRAZIL_DDDS = [
                    11,12,13,14,15,16,17,18,19,
                    21,22,24,27,28,
                    31,32,33,34,35,37,38,
                    41,42,43,44,45,46,47,48,49,
                    51,53,54,55,
                    61,62,63,64,65,66,67,68,69,
                    71,73,74,75,77,79,
                    81,82,83,84,85,86,87,88,89,
                    91,92,93,94,95,96,97,98,99
                ];

                // 3) Função auxiliar para montar "(DD) resto últimos4"
                $montaFormato = function(string $ddd, string $numero): string {
                    $last4 = substr($numero, -4);
                    $rest  = substr($numero, 0, -4);
                    return "({$ddd}) {$rest} {$last4}";
                };

                // 4) Detecta nacional: começa com "55DD" ou só "DD"
                $prefix = '';
                if (strlen($raw) >= 4
                    && substr($raw, 0, 2) === '55'
                    && in_array((int)substr($raw, 2, 2), $BRAZIL_DDDS, true)
                ) {
                    // Veio com DDI 55 no início
                    $prefix     = '+55';
                    $ddd        = substr($raw, 2, 2);
                    $numberPart = substr($raw, 4);
                }
                elseif (strlen($raw) >= 2
                    && in_array((int)substr($raw, 0, 2), $BRAZIL_DDDS, true)
                ) {
                    // Código DDD no início, mas sem DDI
                    $prefix     = '+55';
                    $ddd        = substr($raw, 0, 2);
                    $numberPart = substr($raw, 2);
                }
                else {
                    // 5) Internacional ou ambíguo
                    if (strlen($raw) > 11) {
                        $ddiLen     = strlen($raw) - 11;
                        $prefix     = '+' . substr($raw, 0, $ddiLen);
                        $ddd        = substr($raw, $ddiLen, 2);
                        $numberPart = substr($raw, $ddiLen + 2);
                    } else {
                        // Número muito curto ou indefinido
                        return 'NI-' . $raw;
                    }
                }

                // 6) Monta e retorna
                return "{$prefix} " . $montaFormato($ddd, $numberPart);
            }

            $cpf = formatarCPF($cpf);
            if ($birth){
            $aniversario = converterDataParaISO($birth, $formatos);
            }else{
            $aniversario='';
            }
       
  
            $telefone = formatarTelefone($res[0]['telefone']);
            $celular = formatarTelefone($res[0]['celular']);
            $sexo = $res[0]['sexo'];
            $string = $res[0]['sexo'];

            $stringMinuscula = strtolower($string); 
            $primeiraLetra = substr($stringMinuscula, 0, 1); 
            if ($primeiraLetra == "f"){$sexo = "Feminino";}
            elseif($primeiraLetra=="m"){$sexo = "Masculino";}
            elseif($primeiraLetra==""){$sexo != "";}
            else{ "Não Informado";}




            $como_conheceu = $res[0]['como_conheceu'];
            $cep = formatarCEP($res[0]['cep']);
            $endereco = $res[0]['endereco'];
            $numero = $res[0]['numero'];
            $estado = $res[0]['estado'];
            $cidade = $res[0]['cidade'];
            $bairro = $res[0]['bairro'];
            $profissao = $res[0]['profissao'];
            $cadastrado = $res[0]['data_cadastro'];
            $obs = $res[0]['observacoes'];
            $rg = $res[0]['rg'];
            $complemento = $res[0]['complemento'];
            $nome_usuario = $res[0]['nome_usuario'];
            $situacao = $res[0]['situacao'];



                $foto_edit = $res[0]['foto']; // mostra a foto no editar

                if ($situacao=='Lead'){
                    $statusSit=' status-lead';
                }
                
                if ($situacao=='Em Ativação'){
                    $statusSit=' status-em-ativacao';
                }
                
                if ($situacao=='Não Ativado'){
                    $statusSit=' status-nao-ativado';
                }
                
                if ($situacao=='Ativo'){
                    $statusSit=' status-ativo';
                }
                
                if ($situacao=='Inativo'){
                    $statusSit=' status-inativo';
                }
                
                if ($situacao=='Vencido'){
                    $statusSit=' status-vencido';
                }
                
                if ($situacao=='em reativação'){
                    $statusSit=' status-em-reativacao';
                }

                 $titulo_modal = $nome. '<span class="status-label' . $statusSit . '">' . $situacao . '</span>';
    
    }   



  
} else {
  $titulo_modal = "Novo Cadastro de Cliente";
  $tipo_cadastro = "novo";
  // Como é um novo cadastro, os campos do formulário ficariam vazios
}

// Função para buscar os agendamentos

     // Conecta ao banco de dados



?>    

<div class="modal fade" tabindex="-1" style ="z-index: 95000;" id="modalCadClientes" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl modal-easy-xl">
        <div class="modal-content" >
            <div class="modal-header d-flex align-items-center">
            <!-- Wrapper de imagem + título -->
            <div class="d-flex align-items-center">
                <img
                src="<?= (@$foto_edit ? '../'. $pasta.'/img/'. $pag .'/'. $foto_edit  : '../img/sem-foto.svg') ?>"
                id="img-foto_head"
                alt="Foto do Cliente"
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
            <form method="POST" id="formCadCliente"  enctype="multipart/form-data">
                <div class="modal-body">
                                        
                    <ul <?= ($tipo_cadastro == "novo" ? 'hidden' : '') ?> style="cursor:pointer;" class="nav nav-tabs" id="v-tab" role="tablist">

                    
                        <li class="nav-link active tab-btn" id="cadastro-tab"  data-bs-toggle="tab" data-bs-target="#aba-cadastro" role="tab" aria-controls="cadastro" aria-selected="true">
                        Cadastro
                        </li>
                    
                        <li class="nav-link tab-btn "  id="atendimentos-tab" data-bs-toggle="tab" data-bs-target="#aba-atendimentos" role="tab" aria-controls="atendimentos" aria-selected="false">
                        Atendimentos
                        </li>

                        
                        <li class="nav-link tab-btn" id="servicos-tab" data-bs-toggle="tab" data-bs-target="#aba-servicos" role="tab" aria-controls="servicos" aria-selected="false">
                        Serviços
                        </li>
                        
                        <li class="nav-link tab-btn" id="financeiro-tab" data-bs-toggle="tab" data-bs-target="#aba-financeiro" role="tab" aria-controls="financeiro" aria-selected="false">
                        Financeiro
                        </li>

                        <li class="nav-link tab-btn" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#aba-documentos" role="tab" aria-controls="documentos" aria-selected="false">
                        Documerntos
                        </li>

                        <li class="nav-link tab-btn" id="galeria-tab" data-bs-toggle="tab" data-bs-target="#aba-galeria" role="tab" aria-controls="galeria" aria-selected="false">
                        Galeria
                        </li>

                        <!-- Adicione outras abas conforme necessário -->
                    
                    </ul>
                        
                    <div class="tab-content mt-3" id="v-tabContent"> <!-- conteudo das tabs -->
                        <!-- TAB CADASTRO -->
                        <div class="tab-pane fade show active" id="aba-cadastro" role="tabpanel" aria-labelledby="cadastro-tab">
                            <div class="row">
                                        <!--  -->
                                <div class="col-auto" style="min-width: 150px;">
                                    <!-- Input file escondido, dispara ao clicar na imagem -->
                                    <input type="file" accept="image/*" id="input-foto_cadCliente" name="input-foto_cadCliente" style="display:none">

                                    <!-- Imagem de perfil (aparece sempre) -->
                                    <div id="divImgConta" class="mt-2" style="padding-left:10px;">
                                        <img 
                                            style="margin-left:5px; cursor: pointer; border-radius:50%; width: 100px;"
                                            src="<?= (@$foto_edit ? '../'. $pasta.'/img/'. $pag .'/'. $foto_edit  : '../img/sem-foto.svg') ?>"
                                            id="img-foto_cadCliente"
                                            name="img-foto_cadCliente"
                                            alt="Foto do Cliente"
                                        >
                                    </div>

                                    <!-- Área para crop (aparece só quando usuário seleciona uma imagem) -->
                                    <div id="cropper-area" style="display:none; margin-top:15px;">
                                        <button type="button" id="btn-crop-ok" class="btn btn-primary btn-sm mb-3" style="margin-top:8px;">Usar esta foto</button>
                                        <button type="button" id="btn-crop-cancel" class="btn btn-secondary btn-sm mb-3" style="margin-top:8px;">Cancelar</button>
                                        <img id="preview-crop" style=" max-height: 300px; border-radius:8px; border:1px solid #ddd;">
                                        <br>
                                        
                                    </div>
                                </div>
                                <div id="webcam-area" style="display:none; margin-top:15px;">
                                    <video id="webcam" width="300" height="225" autoplay style="border-radius:12px; border:1px solid #aaa;"></video>
                                    <br>
                                    <button type="button" id="btn-capturar" class="btn btn-success btn-sm" style="margin-top:10px;">Capturar Foto</button>
                                    <button type="button" id="btn-cancelar-webcam" class="btn btn-secondary btn-sm" style="margin-top:10px;">Cancelar</button>
                                </div>

                                <div class="col"  style="min-width: 380px;">
                                    <div class="row " style = "padding-top: 15px;">
                                        <div class="col-md-5 mb-3">
                                            
                                            <label for="nome" class="form-group">
                                                Nome
                                            </label>
                                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required="" value="<?=  @$nome ?>">
                                        </div>		
                                        
                                        <div class="col-md-3 mb-3">
                                            
                                            <label  class="form-group">
                                                Nascimento
                                            </label>
                                            <input type="date" class="form-control" id="aniversario" name="aniversario" value="<?=  @$aniversario ?>"> 
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            
                                            <label class="form-group">
                                            Gênero
                                            </label>															
                                            <select class="form-select" aria-label="Default select example" name="sexo">
                                                    <option></option>
                                                    <option <?= (@$sexo == 'Feminino' ? 'selected' : '') ?> value="Feminino">Feminino</option>
                                                    <option <?= (@$sexo == 'Masculino' ? 'selected' : '') ?> value="Masculino">Masculino</option>
                                                    <option <?= (@$sexo == 'Não Informado' ? 'selected' : '') ?> value="Não Informado">Não Informado</option>									
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row ">
                                        <div class="col-md-3 mb-3">
                                            <label for="celular" class="form-group">Celular</label>
                                            <input type="text" class="form-control" id="celular" name="celular" placeholder="Celular"  value="<?=  @$celular ?>">
                                        </div> 
                                        <div class="col-md-3 mb-3">
                                            <label for="telefone" class="form-group">Telefone</label>
                                            <input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone"  value="<?=  @$telefone ?>">
                                        </div> 
                                        <div class="col-md-5 mb-3">
                                            <label for="email" class="form-group">Email</label>
                                            <input type="email" autocomplete="email" class="form-control" id="email" name="email" placeholder="Email"  value="<?=  @$email ?>">
                                        </div>
                                        
                                    </div> 

                                </div>    
                            </div> 
                            <div class="row">
                                <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="cpf" class="form-group">CPF</label>
                                            <input type="text" class="form-control num-cpf" id="cpf" name="cpf" placeholder="CPF"  value="<?=  @$cpf ?>">
                                            <div class="invalid-feedback" id="cpfError" style="display: none; color: red;">CPF inválido!</div>
                                        </div>  
                                </div>
        
                                <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="rg" class="form-group">RG</label>
                                            <input type="text" class="form-control" id="rg" name="rg" placeholder="RG"  value="<?=  @$rg ?>">
                                        </div>
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label for="profissao" class="form-group">Profissão</label>
                                    <input type="text" class="form-control" id="profissao" name="profissao" placeholder="Profissão"  value="<?=  @$profissao ?>">
                                </div>
                            </div>

                            <!-- linha-->
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="cep" class="form-group">Cep</label>
                                        <input type="text" class="form-control" id="cep" name="cep" placeholder="Cep"  value="<?=  @$cep ?>">
                                    </div>  
                                </div>	
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="rua" class="form-group">Endereço</label>
                                        <input type="text" class="form-control" id="rua" name="endereco" placeholder="Endereço"  value="<?=  @$endereco ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="numero" class="form-group">Número</label>
                                        <input type="text" class="form-control numVirg" id="numero" name="numero" placeholder="nº"  value="<?=  @$numero ?>">
                                    </div>  
                                </div>		
                            </div>
                        
                            <!-- linha-->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="complemento" class="form-group">Complemento</label>
                                        <input type="text" class="form-control" id="complemento" name="complemento" placeholder="complemento"  value="<?=  @$complemento ?>">
                                    </div>  
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="bairro" class="form-group">Bairro</label>
                                        <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro"  value="<?=  @$bairro ?>">
                                    </div>  
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="cidade" class="form-group">Cidade</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade"  value="<?=  @$cidade ?>">
                                    </div>
                                </div>						
                        
                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="estado" class="form-group">Estado</label>
                                        <input type="text" class="form-control" id="estado" maxlength="2" name="estado" placeholder="UF"  value="<?=  @$estado ?>">
                                    </div>
                                </div>
                                                    
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="como_conheceu" class="form-group">Como nos Conheceu?</label>
                                            <select class="form-select " aria-label="Default select example" id="como_conheceu" name="como_conheceu">
                                                <option <?= (@$como_conheceu == 'Pesquisa no google' ? 'selected' : '') ?> value="Pesquisa Google">Pesquisa no google</option>
                                                <option <?= (@$como_conheceu == 'Anúncio em um site- rede de Display' ? 'selected' : '') ?> value="Anúncio em um site- rede de Display">Anúncio em um site- rede de Display</option>
                                                <option <?= (@$como_conheceu == 'Email Marketing' ? 'selected' : '') ?> value="Email Marketing">Email Marketing</option>
                                                <option <?= (@$como_conheceu == 'Instagram' ? 'selected' : '') ?> value="Instagram">Instagram</option>
                                                <option <?= (@$como_conheceu == 'Facebook' ? 'selected' : '') ?> value="Facebook">Facebook</option>
                                                <option <?= (@$como_conheceu == 'Indicação' ? 'selected' : '') ?> value="Indicação">Indicação</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="nivel" class="form-group">Nível</label>
                                            <select class="form-select " aria-label="Default select" id="nivel" name="nivel">
                                                <option <?= (@$nivel == 'Cliente' ? 'selected' : '') ?> value="Cliente">Cliente</option>

                                            </select>
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="nome_usuario" class="form-group">Nome de Usuário:</label>
                                                    <input type="text" class="form-control" id="nome_usuario" name="nome_usuario" autocomplete="new-username" maxlength="30" placeholder="nome de usuário" value="<?= ($nome_usuario && $nome_usuario != '') ? $nome_usuario : '' ?>">
                                                </div> 
                                            </div>  
                                            <div class="row">
                                                <div class="col-md-12 ">
                                                    <label for="senha" class="form-group">Senha</label>
                                                    <div class="mb-3 input-group">
                                                        <input type="password" class="form-control" autocomplete="new-password" id="senha" name="senha" placeholder="Senha">

                                                        <button class="btn btn-outline-secondary btn-span" type="button" id="toggleSenhaAtual"><i class="bi bi-eye-slash" id="iconeSenha"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="observacoes" class="form-group">Observações</label>																
                                                <textarea style= "height: 83px;" class="form-control" id="observacoes" name="observacoes" placeholder="Observações" maxlength="255"><?= @$obs ?></textarea>

                                            </div>
                                        </div>
                                    </div>						
                                </div>
                            </div>
                        </div>     <!-- fechamento da tab 1-->

                        <!-- TAB ATENDIMENTOS - Inicio da TaB 2-->
                        <div class="tab-pane fade" id="aba-atendimentos" role="tabpanel" aria-labelledby="atendimentos-tab">
                            <div class="container-md listTabContainer mt-3">
                                <div class="table-container" id="agendamentos-container-modal">
                                    <div class="row mb-2">
                                        <div class="col">
                                            <input type="text" class="form-control searchBox" placeholder="Pesquisar agendamento...">
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
                                        class="tablesModCliente dataTable dTlinhaFina"
                                        data-table="agendamentos"
                                        
                                        data-filtro='{
                                                        "logic": "AND",
                                                        "conditions": [
                                                            {"field":"id_cliente", "op":"=", "value":<?= $id_cliente ?>}
                                                            ]
                                                        }'
                                        style="width:100%"><!-- Substitua 123 pelo id_cliente do cliente atual via JS -->
                                        <thead>
                                        <tr>
                                            <th data-field="data" data-sort="data" data-sort-init="DESC">Data</th>
                                            <th data-field="id" hidden>ID</th>
                                            <th data-field="hora" data-classe-td="horaMinuto" data-sort="a-z">Hora</th>
                                            <th data-field="servico" data-sort="a-z">Serviço</th>
                                            <th data-field="profissional_1" data-sort="a-z">Profissional</th>
                                            <th data-field="status" data-classe-td="statusTooltip" data-sort="a-z">Status</th>
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
                                        class="tablesModCliente dataTable dTlinhaFina"
                                        data-table="venda_itens" data-minimized="false"
                                        
                                        data-filtro='{
                                                    "logic":"AND",
                                                    "conditions":[
                                                        {"field":"id_cliente", "op":"=", "value":<?= $id_cliente ?>},
                                                        {"field":"tipo_item", "op":"=", "value":"servico"},
                                                        {"field":"venda", "op":"=", "value":"1"}
                                                        ]
                                                    }'
                                        style="width:100%"><!-- Substitua 123 pelo id_cliente do cliente atual via JS -->
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
                                            <span class="me-2" style="font-size:0.9em;">Saldo do Cliente:</span>
                                            <input 
                                                type="text" 
                                                class="form-control blockItem" 
                                                style="width: auto; color:<?=$saldo_cliente<0?'red':'green'?>;" 
                                                value="R$ <?= number_format($saldo_cliente, 2, ',', '.') ?>">
                                        </div>
                                    </div>
                                    <table 
                                        class="tablesModCliente dataTable dTlinhaFina"
                                        data-table="venda"
                                        
                                        data-filtro='{
                                                        "logic":"AND",
                                                        "conditions":[
                                                            {"field":"id_cliente", "op":"=", "value":<?= $id_cliente ?>},
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
                                        style="width:100%"><!-- Substitua 123 pelo id_cliente do cliente atual via JS -->
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
                                        class="tablesModCliente dataTable dTlinhaFina"
                                        data-table="clientes_arquivos"
                                        
                                         data-filtro='{
                                                    "logic":"AND",
                                                    "conditions":[
                                                        {"field":"id_cliente", "op":"=", "value":<?= $id_cliente ?>}
                                                    ]
                                                    }'
                                        style="width:100%"><!-- Substitua 123 pelo id_cliente do cliente atual via JS -->
                                        <thead>
                                        <tr>
                                            <th  
                                                data-field="extensao" 
                                                class="data-documento" 
                                                data-href-field="arquivo"
                                                data-href-base="../<?=$pasta?>/documentos/clientes/" 
                                                data-documento="../<?=$pasta?>/documentos/clientes/galeria/mini/">Arquivo
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
                                        class="tablesModCliente dataTable dTlinhaFina"
                                        data-table="clientes_fotos"
                                        
                                         data-filtro='{
                                                        "logic":"AND", 
                                                        "conditions":[
                                                            {"field":"id_cliente", "op":"=", "value":"<?= $id_cliente ?>"}
                                                            ]
                                                        }'
                                        style="width:100%"><!-- Substitua 123 pelo id_cliente do cliente atual via JS -->
                                        <thead>
                                        <tr>
                                            <th  
                                                data-field="arquivo_mini" 
                                                class="data-foto" 
                                                data-href-field="arquivo_ori"
                                                data-href-base="../<?=$pasta?>/img/clientes/galeria/" 
                                                data-foto="../<?=$pasta?>/img/clientes/galeria/mini/">Foto
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
                        <button type="button" id="btn-fechar_cliente" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" name="btn-salvar_cliente" id="btn-salvar_cliente"  class="btn btn-primary">Salvar</button>
                    </div> 
                    <input name="id" type="hidden" value="<?=  $id_cliente?>">
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




    var saldoCliente = parseFloat("<?= $saldo_cliente ?>") || 0;

</script>


<script type="text/javascript" src="clientes/tabelasModalClientes.js?v=0.33"></script> <!-- meu-->

<script type="text/javascript" src="clientes/modalClientes.js?v=0.18"></script>