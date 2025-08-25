<?php
// Início do script PHP
$pag = 'financeiro';
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

$id_usuario    = $_SESSION['id_usuario'];    // user_criacao ou user_alteracao
$nome_usuario  = $_SESSION['nome_usuario'];


// Recebe dados via POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$tipo_lancamento = isset($_POST['tipo']) ? $_POST['tipo'] : ''; //receita, despesa ou transferência





if ($tipo_lancamento){
    $novo_lancamento = true;

  if ($tipo_lancamento=='despesa'){
    $operacao=1;
    $query_formas_pagamentos = $pdo->prepare("SELECT * FROM pagamentos_forma WHERE operacao = :operacao or operacao = 2");
    $query_formas_pagamentos->execute([':operacao' => $operacao]);
    $formas_pagamentos = $query_formas_pagamentos->fetchAll(PDO::FETCH_ASSOC);

    $query_tipos_pagamentos = $pdo->prepare("SELECT id, nome FROM pagamentos_tipo WHERE operacao = :operacao or operacao = 2");
    $query_tipos_pagamentos->execute([':operacao' => $operacao]);
    $tipos_pagamentos = $query_tipos_pagamentos->fetchAll(PDO::FETCH_ASSOC);

  }elseif($tipo_lancamento=='receita'){
    $operacao=0;
    $query_formas_pagamentos = $pdo->prepare("SELECT * FROM pagamentos_forma WHERE operacao = :operacao or operacao = 2");
    $query_formas_pagamentos->execute([':operacao' => $operacao]);
    $formas_pagamentos = $query_formas_pagamentos->fetchAll(PDO::FETCH_ASSOC);

    $query_tipos_pagamentos = $pdo->prepare("SELECT id, nome FROM pagamentos_tipo WHERE operacao = :operacao or operacao = 2");
    $query_tipos_pagamentos->execute([':operacao' => $operacao]);
    $tipos_pagamentos = $query_tipos_pagamentos->fetchAll(PDO::FETCH_ASSOC);
  
  }elseif($tipo_lancamento=='transferencia'){

    $query_formas_pagamentos = $pdo->prepare("SELECT * FROM pagamentos_forma ");
    $query_formas_pagamentos->execute();
    $formas_pagamentos = $query_formas_pagamentos->fetchAll(PDO::FETCH_ASSOC);

    $query_tipos_pagamentos = $pdo->prepare("SELECT id, nome, operacao FROM pagamentos_tipo");
    $query_tipos_pagamentos->execute();
    $tipos_pagamentos = $query_tipos_pagamentos->fetchAll(PDO::FETCH_ASSOC);
 
  }



  //$query_categorias->execute([':categoria' => $categ]);
}



$valor_bruto = $lancamento['valor_principal'];


$titulo_modal = $tipo_lancamento;






//AND (interna IS NULL OR interna = 0)"
$query_contas_correntes = $pdo->prepare("SELECT * FROM contas_correntes WHERE ativa = 1");
$query_contas_correntes->execute();
$contas_correntes = $query_contas_correntes->fetchAll(PDO::FETCH_ASSOC);
//consulta os feriados
$query_feriados = $pdo->prepare("SELECT * FROM feriados");
$query_feriados->execute();
$feriados = $query_feriados->fetchAll(PDO::FETCH_ASSOC);



if ($tipo_lancamento=='receita'){
$query_clientes = $pdo->prepare("SELECT id, nome FROM clientes");
$query_clientes->execute();
$clientes = $query_clientes->fetchAll(PDO::FETCH_ASSOC);
} elseif($tipo_lancamento=='despesa'){
$query_fornecedores = $pdo->prepare("SELECT id, nome FROM fornecedores");
$query_fornecedores->execute();
$fornecedores= $query_fornecedores->fetchAll(PDO::FETCH_ASSOC);
}
//=============   FIM DOS ARRAYS      ===================










$bloquearCampos = ($id > 0); 

if ($id > 0) {

    
     // Consulta principal para obter os dados da venda
     $query = $pdo->prepare("SELECT * FROM financeiro_extrato WHERE id = :id");
     $query->execute([':id' => $id]);
     $lancamento = $query->fetch(PDO::FETCH_ASSOC);
     
     
    if(!$lancamento){
      throw new Exception("Lançamento não encontrada.");
    }


    if ($lancamento['transferencia']==1) {

       $registro1 = $lancamento; 
       
       $descricao = $registro1['descricao'];
       $data_transferencia   =  $registro1['data_pagamento'];
       $valor_transferencia = $registro1['valor_principal'];
       $id_transferencia =  $registro1['id_transferencia'];
       $observacoes = $registro1['observacoes'];
       
       if ($valor_transferencia<0){
        $valor_transferencia = $valor_transferencia *-1;
        
        $valor_saida = $valor_transferencia;
        $valor_entrada = $valor_transferencia;
        $id_transf_saida =$registro1['id'];
        $id_tipo_pg_saida =$registro1['id_tipo_pagamento'];
        $id_forma_pg_saida =$registro1['id_forma_pagamento'];
        $id_conta_saida = $registro1['id_conta'];
       } else{
        $valor_transferencia = $valor_transferencia;

        $valor_entrada = $valor_transferencia;
        $valor_saida = $valor_transferencia *-1;
        $id_transf_entrada = $registro1['id'];
        $id_tipo_pg_entrada =$registro1['id_tipo_pagamento'];
        $id_forma_pg_entrada =$registro1['id_forma_pagamento'];
        $id_conta_entrada = $registro1['id_conta'];
       }

       $query_formas_pagamentos = $pdo->prepare("SELECT * FROM pagamentos_forma ");
       $query_formas_pagamentos->execute();
       $formas_pagamentos = $query_formas_pagamentos->fetchAll(PDO::FETCH_ASSOC);
   
       $query_tipos_pagamentos = $pdo->prepare("SELECT id, nome, operacao FROM pagamentos_tipo");
       $query_tipos_pagamentos->execute();
       $tipos_pagamentos = $query_tipos_pagamentos->fetchAll(PDO::FETCH_ASSOC);

       $query = $pdo->prepare("SELECT * FROM financeiro_extrato WHERE id_transferencia = :id_transferencia and id <> :id");
       $query->execute([':id_transferencia' => $id_transferencia, ':id' => $id]);
       $registro2 = $query->fetch(PDO::FETCH_ASSOC);

       if ($id_transf_saida>0){    // quer dizer que o registro 1 é a saída
        $id_transf_entrada = $registro2['id'];
        $id_tipo_pg_entrada =$registro2['id_tipo_pagamento'];
        $id_forma_pg_entrada =$registro2['id_forma_pagamento'];
        $id_conta_entrada = $registro2['id_conta'];

       }else{
        $id_transf_saida =$registro2['id'];
        $id_tipo_pg_saida =$registro2['id_tipo_pagamento'];
        $id_forma_pg_saida =$registro2['id_forma_pagamento'];
        $id_conta_saida = $registro2['id_conta'];
       }
      



    }

    if(!$lancamento['transferencia']){
  
        $data_competencia          = $lancamento['data_competencia'];
        $data_vencimento           = $lancamento['data_vencimento'];
        $data_pagamento            = $lancamento['data_pagamento'];
        $descricao                 = $lancamento['descricao'];
        $categoria                 = $lancamento['categoria'];
        $id_categoria              = $lancamento['id_categoria'];
        $conta                     = $lancamento['conta'];
        $id_conta_corrente         = $lancamento['id_conta'];
        $valor_bruto               = $lancamento['valor_principal'];
        $multas_juros              = $lancamento['multa_juros'];
        $desconto_taxa             = $lancamento['desconto_taxa'];
        $valor_liquido             = $lancamento['valor_liquido'];
        $centro_custo              = $lancamento['centro_custo'];
        $id_centro_custo           = $lancamento['id_centro_custo'];
        $tipo_pagamento            = $lancamento['tipo_pagamento'];
        $id_tipo_pagamento         = $lancamento['id_tipo_pagamento'];
        $forma_pagamento           = $lancamento['forma_pagamento'];
        $id_forma_pagamento        = $lancamento['id_forma_pagamento'];
        $fornecedor                = $lancamento['fornecedor'];
        $id_fornecedor             = $lancamento['id_fornecedor'];
        $observacoes               = $lancamento['observacoes'];
        
        
        
        $id_venda                  = $lancamento['id_venda'];
        $id_pagamento_venda        = $lancamento['id_pagamento_venda'];
        $id_user_criacao           = $lancamento['id_user_criacao'];
        $user_criacao              = $lancamento['user_criacao'];
        $data_criacao              = $lancamento['data_criacao'];
        $id_user_alteracao         = $lancamento['id_user_alteracao'];
        $user_alteracao            = $lancamento['user_alteracao'];
        $data_alteracao            = $lancamento['data_alteracao'];
        $nota_fiscal               = $lancamento['nota_fiscal'];
        $id_comum                  = $lancamento['id_comum'];
        $id_cliente                = $lancamento['id_cliente'];
        $nome_cliente = '';

        



        $id_filial                 = $lancamento['id_filial'];
        $excluido                  = $lancamento['excluido'];
        $imagem                    = $lancamento['imagem'];
        $recorrencia               = $lancamento['recorrencia'];
        $id_recorrencia            = $lancamento['id_recorrencia'];
        $pago                      = $lancamento['pago'];
        $transferencia             = $lancamento['transferencia'];


        $novo_lancamento = false;

       
          if (floatval($valor_bruto)<0){
            $tipo_lancamento = 'despesa';
            $valor_bruto = $valor_bruto * -1;
            $valor_liquido = $valor_liquido * -1;
            $multas_juros = $multas_juros * -1;
          }else{
          $tipo_lancamento = 'receita';
          $desconto_taxa = $desconto_taxa * -1;
          }
        






        if ($tipo_lancamento=='receita'){
          $operacao=0;
          $query_clientes = $pdo->prepare("SELECT id, nome FROM clientes");
          $query_clientes->execute();
          $clientes = $query_clientes->fetchAll(PDO::FETCH_ASSOC);
        } else{
          $operacao=1;
          $query_fornecedores = $pdo->prepare("SELECT id, nome FROM fornecedores");
          $query_fornecedores->execute();
          $fornecedores= $query_fornecedores->fetchAll(PDO::FETCH_ASSOC);
        }


              
        $query_formas_pagamentos = $pdo->prepare("SELECT * FROM pagamentos_forma WHERE operacao = :operacao");
        $query_formas_pagamentos->execute([':operacao' => $operacao]);
        $formas_pagamentos = $query_formas_pagamentos->fetchAll(PDO::FETCH_ASSOC);

        $query_tipos_pagamentos = $pdo->prepare("SELECT id, nome FROM pagamentos_tipo WHERE operacao = :operacao or operacao = 2");
        $query_tipos_pagamentos->execute([':operacao' => $operacao]);
        $tipos_pagamentos = $query_tipos_pagamentos->fetchAll(PDO::FETCH_ASSOC);

        // Verifica se $id_cliente é válido
        if (!empty($id_cliente) && $id_cliente > 0) {
            foreach ($clientes as $cliente) {
                if ($cliente['id'] === $id_cliente) {
                    $nome_cliente = $cliente['nome'];
                    //echo 'o cliente é '. $nome_cliente;
                    break;
                }
            }
        }
      } elseif($lancamento['transferencia']==1){

        $tipo_lancamento='transferencia';
      }

    
} else {


   $id_fornecedor='';
    
}







$sigla='';

switch ($tipo_lancamento) {
  case 'receita':
      $sigla = 'RECEITA';
      $categ='1';
      break;
  case 'despesa':
      $sigla = 'DESPESA';
      $categ='2';
      break;
  case 'transferencia':
      $sigla = 'TRANSFERENCIA';
      $categ='3';
      break;
  default:
      $sigla = '';
}



$query_categorias = $pdo->prepare("SELECT * FROM categorias_contabeis WHERE categoria = :categoria");
$query_categorias->execute([':categoria' => $categ]);
$categorias = $query_categorias->fetchAll(PDO::FETCH_ASSOC);




if ($tipo_lancamento=='receita'){
  $txtE = 'O';
}else{
  $txtE = 'A';
}



if ($novo_lancamento){
$txtMod1 = 'NOV' . $txtE;
}else{
$txtMod1 = 'EDITAR';

}

$titulo_modal = $txtMod1 . ' ' . strtoupper($tipo_lancamento);


function decimalBR($valor) {
  return number_format((float)$valor, 2, ',', '');
}

//id-tipo_pagamento =15
if($tipo_lancamento !='transferencia'){
  $optionsFormaPgt = '';
  foreach($formas_pagamentos as $forma_pg):
      foreach($tipos_pagamentos as $tp_pg):
          if($id_tipo_pagamento == $forma_pg['tipo_id']):
              $selected = ($forma_pg['id'] == $id_forma_pagamento) ? 'selected' : '';
              $optionsFormaPgt .= '<option value="' . $forma_pg['id'] . '" ' . $selected . '>' . $forma_pg['nome'] . '</option>';
              break;
          endif;
      endforeach;
  endforeach;

  $optionsTipoPgt = '';
  foreach ($tipos_pagamentos as $tp_pg):
      $optionsTipoPgt .= '<option value="'. $tp_pg['id'] . '"';
      if (isset($tp_pg['id'])) {
          $optionsTipoPgt .= ($tp_pg['id'] === $id_tipo_pagamento) ? ' selected' : '';
      } else {
          $optionsTipoPgt .= ($tipo_pagamento === $tp_pg['nome']) ? ' selected' : '';
      }
      $optionsTipoPgt .= '>' . $tp_pg['nome'] . '</option>';
  endforeach;
  
  $optionsConta = '';
  foreach($contas_correntes as $cc){
    if ($cc['id']=== $id_conta_corrente){
    $optionsConta .= '<option selected value="'. $cc['id'] . '">' . $cc['nome'] . '</option>';
    } 
  }
  
  if (!$optionsConta){
    foreach($contas_correntes as $cc){
    $optionsConta .= '<option value="'.  $cc['id'] . '">' . $cc['nome'] . '</option>';
    }
  
  }

}else{

    $options_forma_pagamento_entrada='';
    $options_forma_pagamento_saida= '';
   
   
    foreach($formas_pagamentos as $forma_pg):
      
        foreach($tipos_pagamentos as $tp_pg):
            if($id_tipo_pg_entrada == $forma_pg['tipo_id']):
                $selected = ($forma_pg['id'] == $id_forma_pg_entrada) ? 'selected' : '';
                $options_forma_pagamento_entrada.= '<option value="' . $forma_pg['id'] . '" ' . $selected . '>' . $forma_pg['nome'] . '</option>';
                break;
            endif;
            if($id_tipo_pg_saida == $forma_pg['tipo_id']):
              $selected = ($forma_pg['id'] == $id_forma_pg_saida) ? 'selected' : '';
              $options_forma_pagamento_saida.= '<option value="' . $forma_pg['id'] . '" ' . $selected . '>' . $forma_pg['nome'] . '</option>';
              break;
          endif;

      endforeach;
    endforeach;



    //$options_forma_pagamento_saida='';
    $options_tipo_pagamento_entrada = '';
    $options_tipo_pagamento_saida = '';

    foreach ($tipos_pagamentos as $tp_pg):
        // Opções para ENTRADA (operacao = 0 ou 2)
        if ($tp_pg['operacao'] == 0 || $tp_pg['operacao'] == 2) {
            $selected = ($tp_pg['id'] === $id_tipo_pg_entrada) ? ' selected' : '';
            $options_tipo_pagamento_entrada .= '<option value="' . $tp_pg['id'] . '"' . $selected . '>' . $tp_pg['nome'] . '</option>';
        }

        // Opções para SAÍDA (operacao = 1 ou 2)
        if ($tp_pg['operacao'] == 1 || $tp_pg['operacao'] == 2) {
            $selected = ($tp_pg['id'] === $id_tipo_pg_saida) ? ' selected' : '';
            $options_tipo_pagamento_saida .= '<option value="' . $tp_pg['id'] . '"' . $selected . '>' . $tp_pg['nome'] . '</option>';
        }
    endforeach;





    //Contas corrente transferencia
    $options_conta_entrada = '';
    $options_conta_saida = '';

    foreach($contas_correntes as $cc){
      if ($cc['id']=== $id_conta_entrada){
        $options_conta_entrada .= '<option selected value="'. $cc['id'] . '">' . $cc['nome'] . '</option>';
      } else{
        $options_conta_entrada .= '<option value="'. $cc['id'] . '">' . $cc['nome'] . '</option>';
      }


      if ($cc['id']=== $id_conta_saida){
        $options_conta_saida .= '<option selected value="'. $cc['id'] . '">' . $cc['nome'] . '</option>';
      }else{
        $options_conta_saida .= '<option value="'. $cc['id'] . '">' . $cc['nome'] . '</option>';
      }
      
      


    }

 

  





    }




















  



$tipos_pagamentos_json = json_encode($tipos_pagamentos);
$formas_pagamentos_json = json_encode($formas_pagamentos);

$datas_feriados = array_column($feriados, 'dia');
$feriados_json = json_encode($datas_feriados);


$novoLancamento = ($id <= 0); // Supondo que, se $id==0, é novo lançamento




$cor_receita='green';
$cor_despesa='red';
$cor_transferencia='rgb(252, 168, 1)';

if ($tipo_lancamento=='receita'){
  $cor_lancamento=$cor_receita;
  $cor_btn_out_lanc='btn-outline-success';
  $cor_btn_lanc = 'btn-success';
}
if ($tipo_lancamento=='despesa'){
  $cor_lancamento=$cor_despesa;
  $cor_btn_out_lanc='btn-outline-danger';
  $cor_btn_lanc = 'btn-danger';
}
if ($tipo_lancamento=='transferencia'){
  $cor_lancamento=$cor_transferencia;
  $cor_btn_out_lanc='bbtn-outline-warning';
  $cor_btn_lanc = 'btn-warning';
}



?>


<div class="modal fade" tabindex="-1" style="z-index: 95000; " id="modalLancamento" data-bs-backdrop="static">
  <div class="modal-dialog  <?= ($tipo_lancamento=='transferencia')?'modal-lg':'modal-xl'?>" >
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title"><?= $titulo_modal ?></h5>
          <button type="button" class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
      </div>  
        <form method="POST" id="formVenda">
          <div class="modal-body" id="modal-body" style="min-height: 200px; overflow-y: auto; background-color:#F8F8F8;">
            <input hidden type="text" name="tipo-lancamento" value="<?= $tipo_lancamento?>">
            <input hidden type="text" name="id" value="<?= $id?>">

            <div class="container-fluid" id="container-des-rec-tra" style="border: 1px solid <?=$cor_lancamento?>; border-radius: 5px;">
              <div class="row ">
                <div class="col-md-12 mb-3 mt-3">
                  <label for="nome" class="form-group">Descrição</label>
                  <input type="text" name="descricao" id="descricao" class="form-control" value="<?= $descricao ?>">
                </div>
              </div>
              <!----------------------- CONTEINER SOMENTE para receita ou despesa ------------------------------------->
              <div id="cont-rec-desp-1" <?=($tipo_lancamento=='transferencia')?'hidden':'' ?>>
                <div class="row">
                  <div class="col-auto mb-3 position-relative" style="min-width: 120px;" >
                    <label for="nome" class="form-group">Competência</label>
                    <input  type="text" data-type="date" name="data-competencia" id="data-competencia" class="form-control datepicker" value="<?=$data_competencia?>">
                    <i style="padding-top: 4px; color:<?=$cor_lancamento?>;" class="bi bi-calendar3 calendar-icon"></i>
                  </div>
                  <div class="col-auto mb-3 position-relative" style="min-width: 120px;">
                    <label for="data-vencimento" class="form-group">Vencimento</label>
                    <input type="text" data-type="date" name="data-vencimento" class="form-control datepicker" id="data-vencimento" value="<?= $data_vencimento ?>">
                    <i style="padding-top: 4px; color:<?=$cor_lancamento?>;" class="bi bi-calendar3 calendar-icon"></i>
                  </div>

                  <div class="col-auto mb-3" >
                    <label for="nome" class="form-group">Categoria</label>
                    <select name="categoria" id="categoria" data-live-search="true"  data-style="btn btn-outline-primary btn-sm" title=" " type="text" class="selectpicker">
                      <?php foreach($categorias as $categoria): ?>
                        <option value="<?=$categoria['id']?>" <?=($id_categoria===$categoria['id'])?'selected':''?> > <?= $categoria['nome'] ?> </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <!--<div class="col-auto mb-3" style="min-width:120px;">
                    <label for="centro-custo" class="form-group">Centro de Custo:</label>
                    <Select type="text" name="centro-custo" id="centro-custo" class="form-select">
                      <option></option>
                    </Select>
                  </div>-->
                </div>
              
                <div class="row">
                  <div class="col-auto mb-3" style="min-width: 150px;">
                    <label for="valor" class="form-group"><b>VALOR:</b></label>
                    <div class="input-group flex-nowrap">
                      <span class="input-group-text" id="addon-wrapping">R$</span>
                      <input type="text" name="valor-bruto" id="valor-bruto" class="form-control numero-virgula-financeiro" value="<?=(decimalBR($valor_bruto)!='0,00')?decimalBR($valor_bruto):''  ?>">
                    </div>  
                  </div>
                  <div class="col-auto mb-3" style="min-width: 180px;">
                        <label class="form-group">Forma de Pagamento:</label>
                        <select name="tipo-pagamento" id="tipo-pagamento" class="form-select">
                          <option>selecione</option>
                          <?=$optionsTipoPgt?>
                        </select>
                  </div>
                  <div class="col-auto mb-3" id="col-forma-pagamento" style="display:<?=(!$optionsFormaPgt)?'none':'block'?>; min-width: 280px;">
                      <label class="form-group">Modo de Pagamento:</label>
                      <select name="forma-pagamento" id="forma-pagamento" data-live-search="true"  data-style="btn btn-outline-primary btn-sm" title=" " type="text" class="selectpicker">
                          <option>Selecione</option>
                          <?=$optionsFormaPgt?>
                      </select>
                  
                  </div>
                  <div class="col-auto mb-3" style=" min-width: 320px;" >
                    <label for="nome" class="form-group">Conta Corrente</label>
                    <select name="conta-corrente" id="conta-corrente" class="form-select">
                      <?= $optionsConta ?>
                    </select>
                  </div>

                  <div <?=($tipo_lancamento=='receita')?'hidden':'hidden'?> class="col-auto mb-3" style=" min-width: 280px;" ><!-- reativar com as condições-->
                  <label for="fornecedor" class="form-group">Fornecedor (opcional)</label>
                    <select name="fornecedor" id="fornecedor" class="form-select">
                      <option selected value=""></option>
                      <?php foreach($fornecedores as $fornecedor): ?>
                        <option value="<?=$fornecedor['id']?>" <?=($id_fornecedor===$fornecedor['id'])?'selected':''?> > <?= $fornecedor['nome'] ?> </option>
                      <?php endforeach; ?>
                    </select>
                  </div> 
                  <div <?=($tipo_lancamento=='despesa')?'hidden':''?> class="col-auto mb-3" style=" min-width: 280px;" >
                    <div class="client-search">
                      <input hidden name="id-cliente" id="id-cliente" type="text" value="<?= isset($id_cliente) ? $id_cliente : '' ?>">
                      <label for="nome" class="form-group" style="margin-left:10px;" >Cliente</label>
                          <div class="input-group">
                            <input 
                              type="text" 
                                class="form-control nome-cliente"
                                id="nome-cliente" 
                                autocomplete="off"   
                                name="nome-cliente" 
                                placeholder="Nome" 
                                value="<?= isset($nome_cliente) ? $nome_cliente : '' ?>">  
                            <button 
                                  class="btn btn-outline-secondary" 
                                  type="button" 
                                  id="btn-adicionar-cliente"
                                  style="width:38px;border: none;"
                                  <?=isset($id) ? 'style="border:none;"' : '' ?>
                                  title="<?= isset($id_cliente) ? 'Visualizar cliente' : 'Adicionar novo cliente' ?>"
                                  onclick="abrirModal('modalClientes', document.getElementById('id-cliente').value)">
                                  <i class="bi <?= ($id_cliente) ? 'bi-eye"' : 'bi-person-plus' ?>" id= "ico-inputCliente"></i>
                            </button>
                            
                                
                              
                          </div>
                          <ul id="lista-clientes" class="sugestoes lista-clientes"></ul>
                    </div>
                  </div> 
                  
                </div>
              </div> <!-----------------FECHAMENTO CONT Despesa/receita----------------------------->

              
              
              <!-------------------------- CONTAINER de Transferência----------------->

              <div id="cont-transf" <?=($tipo_lancamento!='transferencia')?'hidden':''?> >     
                <input hidden type="text" name="id-transferencia" value="<?= $id_transferencia ?>" >
                <!--LINHA DA DATA E VALOR-->
                <div class="row">
                  <div class="col-auto mb-3" style="min-width: 150px;">
                    <label class="form-group"><b>VALOR:</b></label>
                    <div class="input-group flex-nowrap">
                      <span class="input-group-text" id="addon-wrapping">R$</span>
                      <input type="text" name="tra-valor-transferencia" id="tra-valor-transferencia" class="form-control numero-virgula" value="<?=decimalBR($valor_transferencia) ?>">
                    </div>  
                  </div>

                  <div class="col-auto mb-3 position-relative" style="min-width: 120px;">
                    <label for="data-transferencia" class="form-group">Data da Transferência</label>
                      <input type="text" data-type="date" name="data-transferencia" class="form-control datepicker" id="data-transferencia" value="<?= $data_transferencia ?>"  >
                      <i style="color:<?=$cor_lancamento?>;" class="bi bi-calendar3 calendar-icon"></i>
                    
                  </div>
                </div>

                    <!--LINHA de saída-->
                  
                    <div class="container-fluid" style="border: 1px solid <?=$cor_despesa?>; border-radius: 5px;">
                      <input hidden type="text" name="id-tra-saida" value="<?= $id_transf_saida ?>" >
                      <div class="row mt-3">
                        <div class="col-auto mb-3" style="min-width: 180px;">
                            <label class="form-group">Forma de saída:</label>
                            <select name="tra-tipo-pagamento-saida" id="tra-tipo-pagamento-saida" class="form-select">
                              <?=$options_tipo_pagamento_saida?>                            
                            </select>
                        </div>
                        <div class="col-auto mb-1" id="col-forma-pagamento" style="display:<?=(!$optionsFormaPgt)?'none':'block'?>; min-width: 280px;">
                          <label class="form-group">Modo de Saída:</label>
                          <select class="form-select" name="tra-forma-pagamento-saida" id="tra-forma-pagamento-saida">
                              <?=$options_forma_pagamento_saida?>
                          </select>
                        </div>
                        <div class="col-auto mb-1" style=" min-width: 280px;" >
                          <label for="nome" class="form-group">Conta de Saída</label>
                          <select type="select" name="tra-conta-corrente-saida" id="tra-conta-corrente-saida" data-live-search="true"  data-style="btn btn-outline-primary btn-sm" title=" " type="text" class="selectpicker">
                            <?= $options_conta_saida ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <!--LINHA Entrada-->
                    
                    <div class="container-fluid mt-1 mb-3" style="border: 1px solid <?=$cor_receita?>; border-radius: 5px;">
                      <input hidden type="text" name="id-tra-entrada" value="<?= $id_transf_entrada?>" >
                      <div class="row mt-3">
                        <div class="col-auto mb-3" style="min-width: 180px;">
                              <label class="form-group">Forma de Entrada:</label>
                              <select name="tra-tipo-pagamento-entrada" id="tra-tipo-pagamento-entrada" class="form-select">
                              <?=$options_tipo_pagamento_entrada?>
                              </select>
                        </div>
                        <div class="col-auto mb-3" id="col-forma-pagamento" style="display:<?=(!$optionsFormaPgt)?'none':'block'?>; min-width: 280px;">
                            <label class="form-group">Modo de Entrada:</label>
                            <select class="form-select" name="tra-forma-pagamento-entrada" id="tra-forma-pagamento-entrada">
                                <?=$options_forma_pagamento_entrada?>
                            </select>
                        
                        </div>
                        <div class="col-auto mb-3" style=" min-width: 280px;" >
                          <label for="nome" class="form-group">Conta Entrada</label>
                          <select type="select" name="tra-conta-corrente-entrada" id="tra-conta-corrente-entrada" data-live-search="true"  data-style="btn btn-outline-primary btn-sm" title=" " type="text" class="selectpicker">
                            <?=  $options_conta_entrada ?>
                          </select>
                        </div>
                      </div>
                    </div>
                 
                

                
              </div>

            


            </div>

            

            <div <?=($tipo_lancamento !='transferencia')?'':'hidden'?>>
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-3 mb-1 mt-2">
                      <button
                        class="btn <?=($pago==1)? (($tipo_lancamento=='despesa')?'btn-danger':'btn-success'):(($tipo_lancamento=='despesa')?'btn-outline-danger':'btn-outline-success') ?>" 
                        type="button" 
                        id="btn-pagamento"
                        title="Contrato"
                        onclick="">
                        <i class="bi bi-currency-dollar"></i><?= ($tipo_lancamento=='despesa')?'Pagamento':'Recebimento'?>
                      </button>
                  </div>
                </div>
                <input type="text" id="booPag" name="pago" hidden value="<?=$pago?>" >
              </div>



              <!------------------------------------container de pagamento ------------------------------>
              <div class="container-fluid mt-3"<?=($pago===1)?'':'hidden'?> id="container-pagamento">
                <div class="row">
                  <div class="col-auto mb-3 position-relative" style="min-width: 120px;" >
                      <label class="form-group">Data de Pagamento:</label>
                      <input  type="text" data-type="date" name="data-pagamento" class="form-control datepicker" id="data-pagamento" value="<?=$data_pagamento?>">
                      <i style="padding-top: 4px; color:<?=$cor_lancamento?>; "class="bi bi-calendar3 calendar-icon"></i>
                  </div>

                  <div class="col-auto mb-3">
                    <label class="form-group">Multas e Encargos</label>
                    <input type="text" name="multa-encargo" id="multa-encargo" class="form-control numero-virgula-financeiro" value="<?=decimalBR($multas_juros)?>">
                  </div>
                  <div class="col-auto mb-3">
                    <label class="form-group">Juros e Tarifas</label>
                    <input type="text" name="juros-tarifas" id="juros-tarifas" class="form-control numero-virgula-financeiro" value="<?=decimalBR($desconto_taxa)?>">
                  </div>
                  <div class="col-auto mb-3">
                    <label class="form-group"><b>VALOR LÍQUIDO</b></label>
                    <input type="text" name="valor-liquido"  id="valor-liquido" class="form-control numero-virgula-financeiro" value="<?=decimalBR($valor_liquido)?>">
                  </div>
                </div>
              </div>

              <!-- Se for um novo lançamento, inserir o checkbox de recorrência -->
              <?php if($novoLancamento){ ?>
              <div class="container-fluid mt-3" id="cont-chk-recorrencia">
                <div class="form-check mb-3" style="width:fit-content; cursor:pointer;">
                  <input class="form-check-input" style="cursor:pointer;" type="checkbox" id="check-recorrencia">
                  <label class="form-check-label" style="cursor:pointer;" for="check-recorrencia">Aplicar Recorrência</label>
                </div>
              </div>
              <?php } ?>

              <!-- Container de Recorrência: personalize as opções conforme necessário -->



              <!-- Container de Recorrência (inicialmente oculto, controlado pelo checkbox geral que já existe) -->
              <div id="container-recorrencia"  class="container-fluid" hidden>
                <div class="row">
                    <div class="col-md-5">
                          <div class="row">
                            <div class="col-md-7 mb-3">
                              <label class="form-group">Tipo de Recorrência</label>
                              <select id="recorrencia-tipo" class="form-select">
                                <option value="">Selecione</option>
                                <option value="mensal">Mensal</option>
                                <option value="diario">Diário</option>
                                <option value="semanal">Semanal</option>
                                <option value="quinzenal">Quinzenal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="anual">Anual</option>
                                <option value="periodo_definido">Período Definido</option>
                              </select>
                            </div>
                            <div class="col-md-5">
                              <label class="form-group">quantidade</label>
                              <input class ="form-control" style="text-align: center; max-width: 55px;" id="qtd-recorrencia" name="qtd-recorrencia" type="number" min="2" max="240" value="2">
                            </div>
                          </div>
                          <!-- Mensal / Trimestral / Anual -->
                          <div class="recorrencia-mensal d-none">
                            <div class="row">
                              <div class="col-md-5 mb-1" style="width: 95px;">
                                <label class="form-group">Dia do Mês</label>
                                <input type="number" id="recorrencia-dia-mes" class="form-control" style="text-align: center; max-width: 55px;" min="1" max="31">
                              </div>
                              <div class="col-md-7 mb-1 form-check">
                                <input class="form-check-input" type="checkbox" id="recorrencia-dia-util">
                                <label class="form-check-label" for="recorrencia-dia-util">Utilizar dia útil</label>
                              </div>
                            </div>
                            <div id="container-ultimo-dia" class="form-check mb-1 d-none">
                              <input class="form-check-input" type="checkbox" id="ultimo-dia">
                              <label class="form-check-label" for="ultimo-dia">Último dia do mês?</label>
                            </div>                    
                            <div id="recorrencia-sabado-util" style="display: block;" class="form-check d-none mb-1">
                                <input class="form-check-input" type="checkbox" id="recorrencia-sabado">
                                <label class="form-check-label" for="recorrencia-sabado">Considerar sábado como dia útil</label>
                            </div>

                            <div id="recorrencia-dia-util-opcoes" class="mb-2">
                              <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" id="recorrencia-postergar">
                                <label class="form-check-label" for="recorrencia-postergar">Postergar vencimento se não útil</label>
                              </div>
                              <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" id="recorrencia-antecipar">
                                <label class="form-check-label" for="recorrencia-antecipar">Antecipar vencimento se não útil</label>
                              </div>

                            </div>
                          </div>

                          <!-- Diário -->
                          <div class="recorrencia-diario d-none">
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" id="recorrencia-somente-util">
                              <label class="form-check-label" for="recorrencia-somente-util">Somente dias úteis</label>
                            </div>
                            <div id="diario-sabado" class="form-check d-none">
                              <input class="form-check-input" type="checkbox" id="recorrencia-diario-sabado">
                              <label class="form-check-label" for="recorrencia-diario-sabado">Considerar sábado como dia útil</label>
                            </div>
                          </div>

                          <!-- Semanal -->
                          <div class="recorrencia-semanal d-none">
                            <div class="row">
                              <div class="col-auto mb-1">
                                <label class="form-group">Dia da Semana</label>
                                <select id="recorrencia-semana-dia" class="form-select">
                                  <option value="0">Domingo</option>
                                  <option value="1">Segunda</option>
                                  <option value="2">Terça</option>
                                  <option value="3">Quarta</option>
                                  <option value="4">Quinta</option>
                                  <option value="5">Sexta</option>
                                  <option value="6">Sábado</option>
                                </select>
                              </div>
                            </div>
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" id="semanal-antecipar">
                              <label class="form-check-label" for="semanal-antecipar">Antecipar se for feriado</label>
                            </div>
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" id="semanal-postergar">
                              <label class="form-check-label" for="semanal-postergar">Postergar se for feriado</label>
                            </div>
                            <div id="semanal-sabado" class="form-check d-none">
                              <input class="form-check-input" type="checkbox" id="recorrencia-semanal-sabado">
                              <label class="form-check-label" for="recorrencia-semanal-sabado">Considerar sábado como dia útil</label>
                            </div>
                        </div>

                          <!-- Quinzenal -->
                        <div class="recorrencia-quinzenal d-none">
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" id="quinzenal-util-antecipar">
                              <label class="form-check-label" for="quinzenal-util-antecipar">Antecipar se não útil</label>
                            </div>
                            <div class="form-check mb-1">
                              <input class="form-check-input" type="checkbox" id="quinzenal-util-postergar">
                              <label class="form-check-label" for="quinzenal-util-postergar">Postergar se não útil</label>
                            </div>
                            <div class="form-check mb-1 d-none" id="container-quinzenal-sabado">
                              <input class="form-check-input" type="checkbox" id="quinzenal-sabado">
                              <label class="form-check-label" for="quinzenal-sabado">Considerar sábado como dia útil</label>
                            </div>
                        </div>

                          <!-- Fixo Competência -->
                          <div class="mb-1 form-check">
                            <input class="form-check-input" type="checkbox" id="fixar-data-competencia">
                            <label class="form-check-label" for="fixar-data-competencia">Fixar a data de competência</label>
                          </div>
                  </div>  

                  <div class="col-md-7">
                    <div class="row mt-3" style="margin-top: -45px; position:sticky;">
                      <p style="font-size: 16px;">Escolha o Modelo de Descrição das Recorrências</p>
                      <div class="col-md-2 mb-3 d-flex align-items-center">
                          <input class="form-check-input me-2" id="rec-rd-1-de-10" name="radio-rec" type="radio">
                          <label class="form-radio-label mt-1" style="font-size: 10px;"> Ex: 1 de 10</label>
                      </div> 
                      <div class="col-md-2 mb-3 d-flex align-items-center">
                          <input class="form-check-input me-2" group="rec" id="rec-rd-1-b-10" name="radio-rec" type="radio">
                          <label class="form-radio-label mt-1" style="font-size: 10px;"> Ex: 1/10</label>
                      </div>
                      <div class="col-md-5 mb-3 d-flex align-items-center">
                          <input class="form-check-input me-2" group="rec"  id="rec-rd-rf-m-y" name="radio-rec" type="radio">
                          <label class="form-radio-label mt-1" style="font-size: 10px;">Mes/Ano Competência (ex: Ref: Jan/2025)</label>
                      </div>

                    </div>
                  
                    <div class="row" style="max-height: 350px; overflow-y: auto;" id="tabela-array-lançamentos">
                  
                    </div>
                  </div>
                
                
                
                
                
                
                
                </div><!--fim da linha row da recorrencia-->
              </div> <!-- fim do container recorrencia-->


              <!-- Fim das alterações no HTML -->
            </div>

            <!-- container de informações -->
            <div class="container-fluid" >
              <div class="row mt-3" hidden>
                <div class="col-md-12">
                  <label class="form-label">Arquivo</label>
                  <div class="input-group">
                    <button type="button" class="btn <?=($tipo_lancamento=="receita")?'btn-outline-success':'btn-outline-danger'?>" id="btn-upload">
                      <i class="bi bi-upload"></i> Enviar Arquivo
                    </button>
                    <span id="nome-arquivo" class="form-control"></span>
                  </div>
                  <input type="file" name='nome-arquivo' id="custom-file-input" style="display: none;">
                </div>
              </div>
              <!-- Se desejar manter o textarea logo abaixo -->
              <div class="row mt-3">
                <div class="col-md-12">
                  <textarea class="form-control" name="observacoes"style="width: 100%; min-height: 100px;"><?= $observacoes ?></textarea>
                </div>
              </div>
            </div>
              

          </div>  <!-- Fechameto do body do modal-->

          <div class="modal-footer" id="footer" >
          
              <div class="footer-left">
                <div id="mensagem" style="color:red;">
                </div>
              </div>
            <button type="button" id="btn-fechar_venda" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="submit" id="btn-salvar_venda" class="btn btn btn-info">Salvar</button>
          </div>

        

        </form>
    </div>
  </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>






<script>


isModalOpen = false;




document.body.addEventListener("shown.bs.modal", e => {
  // 'e.target' é o elemento do modal recém-aberto
  initDatepickers(e.target);
});



 
function sanitizeNumber(value) {
  return Number(value) || 0;
}



var feriados = <?php echo $feriados_json?> ;
var tiposPagamentos = <?php echo $tipos_pagamentos_json ?>;
var formasPagamentos = <?php echo $formas_pagamentos_json ?>;
var clientes    =<?php echo json_encode($clientes) ?>;

var cliente =  <?php echo json_encode($nome_cliente)?>;
var novoLancamento = <?php echo json_encode($novoLancamento) ?>;

var contasCorrentes = <?php echo json_encode($contas_correntes) ?>;
var idContaPagamento = <?php echo json_encode($id_conta_corrente) ?>;


var pago = <?php echo json_encode($pago); ?>;
var tipoLancamento = <?php echo json_encode($tipo_lancamento) ?>;
var valBruto =  sanitizeNumber(<?php echo json_decode($valor_bruto) ?>);
var valLiquido =  sanitizeNumber(<?php echo json_decode($valor_liquido) ?>);
var ValMulta  =  sanitizeNumber(<?php echo json_decode($multas_juros) ?>);
var ValTaxa  =  sanitizeNumber(<?php echo json_decode($desconto_taxa) ?>);
var novoLancamento = <?php echo json_encode($novo_lancamento)?>;

var idVenda = <?php echo json_encode($id_venda)?>;
console.log('O ID DA VENDA É:' + idVenda);
var observacoes = <?php echo json_encode($observacoes)?>;

console.log('observações: '+observacoes);

      // ----- 3. Validação numérica e mínimo de 0,01 -----
      // Função para exibir mensagens de erro por 3 segundos
      function showErrorMessage(msg, elemento) {
        var mensagem = document.getElementById('mensagem');
        
        elemento.classList.add('input-error');
        mensagem.innerHTML = '<span style="color: red;">' + msg + '</span>';

        setTimeout(function(){
            mensagem.innerHTML = '';
            elemento.classList.remove('input-error');
        }, 3000);
      }

</script>





<script>
if (tipoLancamento!='transferência'){
(function (){
  console.log('novoLancamento:', novoLancamento);

 flatpickr('[data-type="date"]', {
        dateFormat: "Y-m-d",
        altInput: true,              // exibe outro input visual
        altFormat: "d/m/Y",          // formato bonitinho que o usuário vê
        locale: "pt"
      });

    const btnPagamento = document.getElementById('btn-pagamento');
    const chkPag = document.getElementById('booPag');
    const contPagamento = document.getElementById('container-pagamento');

    const chkRecorrencia = document.getElementById('check-recorrencia');
    const qtd = document.getElementById('qtd-recorrencia');
    const contRecorrencia = document.getElementById('container-recorrencia');
    if (novoLancamento==true){
      const contChkRecorrencia = document.getElementById('cont-chk-recorrencia');
    }
    


  
    var inptQtd = document.getElementById('qtd-recorrencia');
    inptQtd.removeAttribute('min');
    inptQtd.value=0;
   

  //OUVINTE DO CHECK RECORRENCIAS
  if (novoLancamento==true){
    chkRecorrencia.addEventListener('change', e => {
      
      console.log('recorrencia clicada');
      
      if (!e.target.checked){ 
        contRecorrencia.setAttribute('hidden', 'true');
        recorrenciaOnOff('off')
        recebimentoOnOff('on');
      }else{
        
        
        contRecorrencia.removeAttribute('hidden');
        btnPagamento.setAttribute('hidden', 'true');
        recebimentoOnOff('off');
        recorrenciaOnOff('on')
      }
    });
  }
        //-----------------------------------------


  //OUVINTE DO BOTÃO PAGAMENTO------------------

  btnPagamento.addEventListener('click', function() {

    if (contPagamento.hasAttribute('hidden')){
      contPagamento.removeAttribute('hidden');

      chkPag.value=1;
      if (tipoLancamento=='despesa'){
        btnPagamento.classList.remove('btn-outline-danger');
        btnPagamento.classList.add('btn-danger');
      }else{
        btnPagamento.classList.remove('btn-outline-success');
        btnPagamento.classList.add('btn-success');
      }
      
      if (novoLancamento==true){
        document.getElementById('cont-chk-recorrencia').setAttribute('hidden', 'true');
      }
      console.log ('calculando totais');
      
      recorrenciaOnOff('off');
      calculaTotais('valor-bruto','valor-bruto' );

    }else{
      contPagamento.setAttribute('hidden', 'true');
      chkPag.value=0;
      if (tipoLancamento=='despesa'){
        btnPagamento.classList.add('btn-outline-danger');
        btnPagamento.classList.remove('btn-danger');
      }else{
        btnPagamento.classList.add('btn-outline-success');
        btnPagamento.classList.remove('btn-success');
        
      }
      if(novoLancamento==true){
        document.getElementById('cont-chk-recorrencia').removeAttribute('hidden');
      }

      recebimentoOnOff('off');

    }
  });
  //----------------------------------------------------



    function recorrenciaOnOff(onOff) {
      console.log('recorrencia onOff = ' + onOff);
      if (onOff=='off'){
                // Limpar todos os checkboxes
         clearRecurrenceCheckboxes();
         document.querySelectorAll('[class^=recorrencia-]').forEach(el=>el.classList.add('d-none'));
        const inputs = contPagamento.querySelectorAll('input:not([type="checkbox"])');
        inputs.forEach(input => input.value = '');
              // Limpar todos os selects
        const selects = contPagamento.querySelectorAll('select');
        selects.forEach(select => select.value = '');
      }else{
        
        qtd.setAttribute('min', '2');
        qtd.value = '2';
      }
   }


    function recebimentoOnOff(onOff){
      
      if (onOff =='off'){
        const checkboxes = contRecorrencia.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => checkbox.checked = false);
              // Limpar todos os inputs (menos checkbox)
        const inputs = contRecorrencia.querySelectorAll('input:not([type="checkbox"])');
        inputs.forEach(input => input.value = '');
              // Limpar todos os selects
        const selects = contRecorrencia.querySelectorAll('select');
        selects.forEach(select => select.value = '');
       
        if (tipoLancamento=='despesa'){
          btnPagamento.classList.add('btn-outline-danger');
          btnPagamento.classList.remove('btn-danger');
        }else{
          btnPagamento.classList.add('btn-outline-success');
          btnPagamento.classList.remove('btn-success');
        }
        contPagamento.setAttribute('hidden', 'true');

      }else {
        console.log('removendo o atributo hidden em onoff');
        btnPagamento.removeAttribute('hidden');
      }
    }


    // Ouvindo a mudança no select "tipo-pagamento"
      document.getElementById('tipo-pagamento').addEventListener('change', function() {
          const selectedTypeId = parseInt(this.value);
          const formaSelect = document.getElementById('forma-pagamento');
          
          
          const colFormaSelect = document.getElementById('col-forma-pagamento');
          const contaSelect = document.getElementById('conta-corrente');
          
          //contaSelect.classList.remove('blockItem');
          contaSelect.innerHTML = '';


          colFormaSelect.style.display='none';
          formaSelect.innerHTML = '';
          console.log('adicionou uma opção');
          
          formasPagamentos.forEach(function(form) {
              if (parseInt(form.tipo_id) === selectedTypeId) {
                colFormaSelect.style.display='block';
                  formaSelect.innerHTML += '<option value="' + form.id + '">' + form.nome + '</option>';
              }
          });

          if (formaSelect.innerHTML){
            formaSelect.innerHTML += '<option selected></option>';
            formaSelect.dispatchEvent(new Event('change'));
          }else{

            
          
            
          }

        $(formaSelect).selectpicker('refresh');
       



      });



      document.getElementById('forma-pagamento').addEventListener('change', function () {


        const valueD = document.querySelector('#forma-pagamento option:checked').textContent;
        console.log('ativado o change '+ valueD);

          const selectedFormaId = parseInt(this.value);
          const contaSelect = document.getElementById('conta-corrente');

          // Limpa as opções anteriores
          
         // contaSelect.classList.remove('blockItem');
          contaSelect.innerHTML = '';
          // Busca a forma de pagamento selecionada
          const forma = formasPagamentos.find(fp => fp.id === selectedFormaId);
          if (!forma) return;

          const idContaPagto = parseInt(forma.id_conta_pagamento);

          // Filtra as contas correntes que correspondem ao id_conta_pagamento
          let contasFiltradas = contasCorrentes.filter(cc => cc.id === idContaPagto);

          // Se não encontrar nenhuma conta vinculada, usa todas
          if (contasFiltradas.length === 0) {
              contasFiltradas = contasCorrentes;
              console.log('sem contas filtradas');
          }

          // Popula o select de contas
          contasFiltradas.forEach(cc => {
              const option = document.createElement('option');
              option.value = cc.id;
              option.textContent = cc.nome;

              // Se for exatamente a conta vinculada, deixa marcada
              if (cc.id === idContaPagto) option.selected = true;

              contaSelect.appendChild(option);
          });

          //contaSelect.classList.add('blockItem');
      });








   

    
    const inBrut =document.getElementById('valor-bruto');
    const inMult =document.getElementById('multa-encargo');
    const inTaxa = document.getElementById('juros-tarifas');//taxa
    const inLiqu =document.getElementById('valor-liquido');

    function calculaTotais(idCalc, inputField) {
      // Impede que qualquer input tenha valor negativo
      [inBrut, inMult, inTaxa, inLiqu].forEach(function(input) {
        var val = DecimalIngles(input.value);
        if(val < 0){
          input.value = "";
        }
      });

      // Efetua os cálculos conforme o campo alterado
      if (idCalc === 'valor-bruto'){
        console.log('calculando valor-bruto...: ' + inBrut.value);

        var novoLiquido = DecimalIngles(inBrut.value) - DecimalIngles(inTaxa.value) + DecimalIngles(inMult.value);
        // Se o resultado for menor que o mínimo permitido, zera o campo que disparou a entrada
        if(novoLiquido < 0.01){
          //inputField.value = "0,00";
          showErrorMessage("Valor líquido mínimo permitido é 0,01. Corrija o valor digitado.", inputField);
          // Ajusta o valor líquido para 0,01 (opcionalmente, pode ser zero ou outro valor)
          inLiqu.value = DecimalBr(0.01);
          return;
        }
        console.log('novo liquido: ' + novoLiquido);
        inLiqu.value = DecimalBr(novoLiquido);
      }
      else if (idCalc === 'multa-encargo' || idCalc === 'juros-tarifas'){
        var novoLiquido = DecimalIngles(inBrut.value) - DecimalIngles(inTaxa.value) + DecimalIngles(inMult.value);
        if(novoLiquido < 0.01){
          inputField.value = "0,00";
          showErrorMessage("Valor líquido mínimo permitido é 0,01. Corrija o valor digitado.", inputField);
          inLiqu.value = DecimalBr( DecimalIngles(inBrut.value) - DecimalIngles(inTaxa.value) + DecimalIngles(inMult.value) );
          return;
        }
        inLiqu.value = DecimalBr(novoLiquido);
        var novoBruto = DecimalIngles(inLiqu.value) + DecimalIngles(inTaxa.value) - DecimalIngles(inMult.value);
        inBrut.value = DecimalBr(novoBruto);
      }
      else if (idCalc === 'valor-liquido'){
        console.log('disparou a função do liquido');
        var novoBruto = DecimalIngles(inLiqu.value) + DecimalIngles(inTaxa.value) - DecimalIngles(inMult.value);
        if(DecimalIngles(inLiqu.value) < 0.01){
          inputField.value = "0,00";
          showErrorMessage("Valor líquido mínimo permitido é 0,01. Corrija o valor digitado.", inputField);
          inLiqu.value = DecimalBr( DecimalIngles(inBrut.value) - DecimalIngles(inTaxa.value) + DecimalIngles(inMult.value) );
          return;
        }
        inBrut.value = DecimalBr(novoBruto);
      }
      
      // Garante, de forma final, que os campos "bruto" e "líquido" não fiquem abaixo de 0,01
      if (DecimalIngles(inBrut.value) < 0.01){
          inBrut.value = DecimalBr(0.01);
          showErrorMessage("Valor Bruto mínimo permitido é 0,01", inBrut);
      }
      if (DecimalIngles(inLiqu.value) < 0.01){
          inLiqu.value = DecimalBr(0.01);
          showErrorMessage("Valor Líquido mínimo permitido é 0,01", inLiqu);
      }
    }






   // var inputsCalc = document.querySelectorAll('.numero-virgula-financeiro');
    
    //inputsCalc.forEach(function(el) {
     // el.addEventListener('input', function(event) {

        
      //});
    //});


    
    
    
    document.addEventListener('input', function (e) {
      const el = e.target;
      console.log('Elemento é :'+ document.activeElement.id + '  e o id de calculo é: '+ el.id)
      
      if (e.target) {
        if (el.classList.contains('numero-virgula-financeiro') && el === document.activeElement) {
              validarInput(el);
              calculaTotais(event.target.id, event.target);
        }
      }
    });



    // ----- 4. Customização do Upload de Arquivo -----
    document.getElementById('btn-upload').addEventListener('click', function(){
        document.getElementById('custom-file-input').click();
    });
    document.getElementById('custom-file-input').addEventListener('change', function(){
        var filename = this.files[0] ? this.files[0].name : '';
        document.getElementById('nome-arquivo').innerText = filename;
    });
  







  // 1. Limpa checkboxes e exibições
  function clearRecurrenceCheckboxes() {
    var c = document.getElementById('container-recorrencia');
    c.querySelectorAll('input[type=checkbox]').forEach(ch => {
      ch.checked = false;
      ch.disabled = false;
    });
    ['recorrencia-dia-util-opcoes','recorrencia-sabado-util','diario-sabado','container-ultimo-dia']
      .forEach(id => {
        var el = document.getElementById(id);
        if(el) el.classList.add('d-none');
      });
  }


  document.getElementById('data-vencimento').onchange = function () {
    handleMonthlyRecurrenceControls();
  };
  // 2. Configura controles mensais
  function handleMonthlyRecurrenceControls() {
    var util = document.getElementById('recorrencia-dia-util');
    var post = document.getElementById('recorrencia-postergar');
    var ant = document.getElementById('recorrencia-antecipar');
    var sab = document.getElementById('recorrencia-sabado-util');
    var dia = document.getElementById('recorrencia-dia-mes');
    var ult = document.getElementById('container-ultimo-dia');
    var chkUlt = document.getElementById('ultimo-dia');
    var dtVenc = document.getElementById('data-vencimento');

    const diaNum = parseInt(dtVenc.value.split('-')[2], 10);
    
    dia.value=diaNum;

    function updateSabado() {
      if(util.checked||post.checked||ant.checked) sab.classList.remove('d-none');
      else { sab.classList.add('d-none'); document.getElementById('recorrencia-sabado').checked=false; }
    }


    function updateOpcoes() {
      document.getElementById('recorrencia-dia-util-opcoes')
        .classList.toggle('d-none', util.checked);
      updateSabado();
    }

    util.addEventListener('change', updateOpcoes);
    post.addEventListener('change', ()=>{ if(post.checked) ant.checked=false; updateSabado(); });
    ant.addEventListener('change', ()=>{ if(ant.checked) post.checked=false; updateSabado(); });

    dia.addEventListener('input', ()=>{
      if(parseInt(dia.value,10)===31) ult.classList.remove('d-none');
      else { ult.classList.add('d-none'); chkUlt.checked=false; post.disabled=false; }
    });
    chkUlt.addEventListener('change', ()=>{
      post.checked=false;
      post.disabled = chkUlt.checked;
    });
  }

 
 
 
 
  // 3. Configura controles diários
  function handleDailyRecurrenceControls() {
    var somenteUtil = document.getElementById('recorrencia-somente-util');
    var diarioSab = document.getElementById('diario-sabado');
    var chkDiarioSab = document.getElementById('recorrencia-diario-sabado');
    somenteUtil.addEventListener('change', function() {
      if(this.checked) {
        diarioSab.classList.remove('d-none');
      } else {
        chkDiarioSab.checked = false;
        diarioSab.classList.add('d-none');
      }
    });
  }






  function handleWeeklyRecurrenceControls() {
    const selDia = document.getElementById('recorrencia-semana-dia');
    const chkAnt = document.getElementById('semanal-antecipar');
    const chkPost = document.getElementById('semanal-postergar');
    const semanaSab = document.getElementById('semanal-sabado');
    const chkSab = document.getElementById('recorrencia-semanal-sabado');

    // Atualiza a visibilidade do checkbox de sábado conforme os checks de antecipar/postergar
    function updateSemanaSab() {
        const val = parseInt(selDia.value, 10);
        if ((chkAnt.checked || chkPost.checked) && val >= 1 && val <= 5) {
            semanaSab.classList.remove('d-none');
        } else {
            chkSab.checked = false;
            semanaSab.classList.add('d-none');
        }
    }

    // Liga ouvintes somente uma vez
    chkAnt.addEventListener('change', () => {
        if (chkAnt.checked) chkPost.checked = false;
        updateSemanaSab();
    });
    chkPost.addEventListener('change', () => {
        if (chkPost.checked) chkAnt.checked = false;
        updateSemanaSab();
    });

    // Quando o dia da semana mudar, ajusta visibilidade e zera estados
    selDia.addEventListener('change', function() {
        const val = parseInt(this.value, 10);
        // Reset de estados
        chkAnt.checked = false;
        chkPost.checked = false;
        chkSab.checked = false;

        // Exibição dos controles de antecipar/postergar em todos os dias úteis e sábado, ocultar apenas no domingo
        const hideFer = (val === 0);
        chkAnt.parentNode.classList.toggle('d-none', hideFer);
        chkPost.parentNode.classList.toggle('d-none', hideFer);

        // Sempre oculta o checkbox de sábado útil até que uma opção seja marcada (updateSemanaSab)
        semanaSab.classList.add('d-none');
    });

    // Inicializa visibilidade conforme o valor atual no carregamento
    (function initWeekly() {
        const val = parseInt(selDia.value, 10);
        const hideFer = (val === 0);
        chkAnt.parentNode.classList.toggle('d-none', hideFer);
        chkPost.parentNode.classList.toggle('d-none', hideFer);
        semanaSab.classList.add('d-none');
    })();
}

function handleBiweeklyRecurrenceControls() {
    const chkUtilAntecipar = document.getElementById('quinzenal-util-antecipar');
    const chkUtilPostergar = document.getElementById('quinzenal-util-postergar');
    const chkSab = document.getElementById('quinzenal-sabado');

    // Reset inicial: desmarca e habilita todos
    [chkUtilAntecipar, chkUtilPostergar, chkSab].forEach(chk => {
        chk.checked = false;
        chk.disabled = false;
    });

    // Esconde o checkbox de sábado até que seja necessário
    const sabContainer = chkSab.closest('.form-check');
    sabContainer.classList.add('d-none');

    // Função que atualiza a exibição do sábado útil
    function updateSabado() {
        if (chkUtilAntecipar.checked || chkUtilPostergar.checked) {
            sabContainer.classList.remove('d-none');
        } else {
            chkSab.checked = false;
            sabContainer.classList.add('d-none');
        }
    }

    // Listeners: mutuamente exclusivos e atualizam sábado
    chkUtilAntecipar.addEventListener('change', () => {
        if (chkUtilAntecipar.checked) chkUtilPostergar.checked = false;
        updateSabado();
    });
    chkUtilPostergar.addEventListener('change', () => {
        if (chkUtilPostergar.checked) chkUtilAntecipar.checked = false;
        updateSabado();
    });
}

  // 4. Muda seção conforme tipo
  document.getElementById('recorrencia-tipo')
    .addEventListener('change', function() {
      clearRecurrenceCheckboxes();
      document.querySelectorAll('[class^=recorrencia-]').forEach(el=>el.classList.add('d-none'));
      var t=this.value;

        
      if(t==='mensal'||t==='trimestral'||t==='anual'){
        document.querySelector('.recorrencia-mensal').classList.remove('d-none');
        handleMonthlyRecurrenceControls();
      }
      else if(t==='diario'){
        //document.querySelector('.')
        document.querySelector('.recorrencia-diario').classList.remove('d-none');
        handleDailyRecurrenceControls();
      }

      else if(t==='semanal'){  document.querySelector('.recorrencia-semanal').classList.remove('d-none'); handleWeeklyRecurrenceControls();}
      //else if(t==='semanal') document.querySelector('.recorrencia-semanal').classList.remove('d-none');
      else if(t==='quinzenal') {document.querySelector('.recorrencia-quinzenal').classList.remove('d-none');handleBiweeklyRecurrenceControls();}
      
  });


 
  
})();
 
}//fim do if !transferência


</script>

<script>


if(tipoLancamento!='transferência'){
// ——————— Debounce helper ———————
function debounce(fn, delay) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  }
}

// —————— Formata Date para BR ——————
function formatDateBR(d) {
  const date = new Date(d);
  const dd = String(date.getDate()).padStart(2,'0');
  const mm = String(date.getMonth()+1).padStart(2,'0');
  const yyyy = date.getFullYear();
  return `${dd}/${mm}/${yyyy}`;
}





function parseYMD(s) {
  const [y, m, d] = s.split('-').map(Number);
  return new Date(y, m - 1, d);
}




// ——— Recria a tabela de recorrências ———


function carregarClientes(){
  //var clientes = (<?php //echo json_encode($clientes); ?>);

  const idCliente = document.querySelector("#id-cliente");
  const inputCliente = document.querySelector("#nome-cliente");
  const listaCliente = document.querySelector("#lista-clientes");
  let selecionadoIndex = -1;
  let resultadosFiltrados = [];

  const propostaBloco = document.getElementById('proposta-vendas');
  const icoInputCliente = document.getElementById('ico-inputCliente');

  // Limpa o idCliente sempre que o nome é alterado
  inputCliente.addEventListener("input", () => {
    
    
    if (inputCliente.value==''){
      icoInputCliente.classList.remove('bi-eye');
      icoInputCliente.classList.add('bi-person-plus');
    }
    
    idCliente.value = "";
    const termo = inputCliente.value.toLowerCase();
    listaCliente.innerHTML = "";
    selecionadoIndex = -1;

    if (termo.length === 0) {
      listaCliente.style.display = "none";
      return;
    }

    function removerAcentos(str) {
      return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    resultadosFiltrados = clientes.filter(cliente =>
      removerAcentos(cliente.nome.toLowerCase()).includes(removerAcentos(termo))
    );

    resultadosFiltrados.forEach((cliente, index) => {
      const li = document.createElement("li");
      li.textContent = cliente.nome;
      li.addEventListener("click", () => carregarCliente(cliente));
      listaCliente.appendChild(li);
    });

    listaCliente.style.display = resultadosFiltrados.length ? "block" : "none";
  });

  inputCliente.addEventListener("keydown", (e) => {
    const itens = listaCliente.querySelectorAll("li");



    if (e.key === "ArrowDown") {
      if (selecionadoIndex < itens.length - 1) {
        selecionadoIndex++;
        atualizarSelecao(itens);
      }
      e.preventDefault();
    }

    if (e.key === "ArrowUp") {
      if (selecionadoIndex > 0) {
        selecionadoIndex--;
        atualizarSelecao(itens);
      }
      e.preventDefault();
    }

    if (e.key === "Enter") {
      if (selecionadoIndex >= 0) {
        carregarCliente(resultadosFiltrados[selecionadoIndex]);
      } else if (resultadosFiltrados.length === 1) {
        carregarCliente(resultadosFiltrados[0]);
      }
      listaCliente.style.display = "none";
      e.preventDefault();
    }
  });

  function atualizarSelecao(itens) {
    itens.forEach((item, index) => {
      const isSelected = index === selecionadoIndex;
      item.classList.toggle("selecionado", isSelected);
      if (isSelected) {
        item.scrollIntoView({ block: "nearest" });
      }
    });
  }

  function carregarCliente(cliente) {
    inputCliente.value = cliente.nome;
    idCliente.value = cliente.id;

    if (cliente.id != "") {
      icoInputCliente.classList.remove('bi-person-plus');
      icoInputCliente.classList.add('bi-eye');
    } else {
      icoInputCliente.classList.remove('bi-eye');
      icoInputCliente.classList.add('bi-person-plus');
    }

    listaCliente.innerHTML = "";
    listaCliente.style.display = "none";

    console.log("Cliente carregado:", cliente);
  }

  idCliente.addEventListener("change", () => {
    console.log('ouvido inputcliente');
  });
}



if (tipoLancamento==='receita'){
  carregarClientes();
}

} //fim do if de transferencia
</script>




<script>
// ——————— Debounce helper ———————

if (tipoLancamento!='transferencia'){
  function debounce(fn, delay) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    }
  }

  // —————— Formata Date para BR ——————
  function formatDateBR(d) {
    const date = new Date(d);
    const dd = String(date.getDate()).padStart(2,'0');
    const mm = String(date.getMonth()+1).padStart(2,'0');
    const yyyy = date.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
  }

  function parseYMD(s) {
    const [y, m, d] = s.split('-').map(Number);
    return new Date(y, m - 1, d);
  }

  var MONTH_ABBRn = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

  var tipoRecEl = document.getElementById('recorrencia-tipo');
  var rfMy      = document.getElementById('rec-rd-rf-m-y');

  function toggleRfMy() {
    const t = tipoRecEl.value;
    const ok = ['mensal','trimestral','anual'].includes(t);
    rfMy.disabled = !ok;
    if (!ok) rfMy.checked = false;
  }

  tipoRecEl.addEventListener('change', () => {
    toggleRfMy();
    rebuildRecurrences(); // refaz tabela ao mudar tipo
  });

  // inicializa estado do rádio
  toggleRfMy();


  function computeCompDate(baseComp, tipo, index, fixComp) {
    const comp0 = parseYMD(baseComp);

    // 1ª ocorrência ou “fixar” → sempre data inicial
    if (index === 0 || fixComp) return comp0;

    // senão → incremento puro
    switch (tipo) {
      case 'mensal':
        // mantém dia ou último dia do mês
        return addMonthsKeepLastDay(comp0, index);
      case 'trimestral':
        return addMonthsKeepLastDay(comp0, index * 3);
      case 'anual':
        return new Date(comp0.getFullYear() + index, comp0.getMonth(), comp0.getDate());
      case 'semanal': {
        const d = new Date(comp0);
        d.setDate(d.getDate() + 7 * index);
        return d;
      }
      case 'quinzenal': {
        const d = new Date(comp0);
        d.setDate(d.getDate() + 14 * index);
        return d;
      }
      case 'diario': {
        const d = new Date(comp0);
        d.setDate(d.getDate() + index);
        return d;
      }
      default:
        return comp0;
    }
  }

  function addMonthsKeepLastDay(orig, months) {
    const year  = orig.getFullYear();
    const month = orig.getMonth() + months;
    const day   = orig.getDate();
    const d     = new Date(year, month, day);
    // se “passou” pra outro mês, ajusta pra último dia
    if (d.getDate() !== day) {
      // último dia do mês anterior em que d ficou
      return new Date(d.getFullYear(), d.getMonth()+1, 0);
    }
    return d;
  }

  // ——— Gera array completo de recorrências ———
  function generateRecurrenceArray(params) {
    const {
      baseVenc, baseComp, tipo, qtd,
      descricao, categoria, valor,
      flags: { diaUtil, ultimoDia, sabadoUtil, postergar, antecipar, feriados }
    } = params;

    const recs = [];
    let vencDate = parseYMD(baseVenc);
    let compDate = parseYMD(baseComp);

    for (let i = 0; i < qtd; i++) {
      // semente: lançamento atual para i=0, ou data anterior para i>0
      let venc = i === 0 ? parseYMD(baseVenc) : new Date(vencDate);
      let comp = i === 0 ? parseYMD(baseComp) : new Date(compDate);

      if (i > 0) {
        switch (tipo) {
          case 'mensal': {
            const dayOfMonth = parseInt(document.getElementById('recorrencia-dia-mes').value, 10);
            const useBizDay = diaUtil;
            const lastDayFlag = ultimoDia;
            const sabUtil = sabadoUtil;

            // avança um mês mantendo o day
            const originalDay = venc.getDate();
            const nextMonth = new Date(venc.getFullYear(), venc.getMonth()+1, originalDay);
            if (nextMonth.getMonth() !== (venc.getMonth()+1) % 12) {
              // mês seguinte não tem o dia, cai para último dia
              venc = new Date(venc.getFullYear(), venc.getMonth()+2, 0);
            } else {
              venc = nextMonth;
            }
            // aplica último dia ou dia útil ou fixo
            if (lastDayFlag) {
              venc = new Date(venc.getFullYear(), venc.getMonth()+1, 0);
            } else if (useBizDay) {
              let count = 0;
              let d = new Date(venc.getFullYear(), venc.getMonth(), 1);
              while (true) {
                const ymd = d.toISOString().slice(0,10);
                const wk = d.getDay();
                if (wk !== 0 && (sabUtil || wk !== 6) && !feriados.includes(ymd)) {
                  count++;
                  if (count === dayOfMonth) break;
                }
                d.setDate(d.getDate()+1);
              }
              venc = d;
            } else {
              venc.setDate(dayOfMonth);
            }
            break;
          }
          case 'trimestral': {
            // lógica semelhante a mensal, mas pula 3 meses
            const dayOfMonth = parseInt(document.getElementById('recorrencia-dia-mes').value, 10);
            const useBizDay = diaUtil;
            const lastDayFlag = ultimoDia;
            const sabUtil = sabadoUtil;

            // avança três meses preservando o dia
            const originalDayT = venc.getDate();
            const nextTriple = new Date(venc.getFullYear(), venc.getMonth() + 3, originalDayT);
            if (nextTriple.getMonth() !== (venc.getMonth() + 3) % 12) {
              venc = new Date(venc.getFullYear(), venc.getMonth() + 4, 0);
            } else {
              venc = nextTriple;
            }
            // aplica último dia ou dia útil ou fixo
            if (lastDayFlag) {
              venc = new Date(venc.getFullYear(), venc.getMonth() + 1, 0);
            } else if (useBizDay) {
              let countQ = 0;
              let dQ = new Date(venc.getFullYear(), venc.getMonth(), 1);
              while (true) {
                const ymdQ = dQ.toISOString().slice(0,10);
                const wkQ = dQ.getDay();
                if (wkQ !== 0 && (sabUtil || wkQ !== 6) && !feriados.includes(ymdQ)) {
                  countQ++;
                  if (countQ === dayOfMonth) break;
                }
                dQ.setDate(dQ.getDate() + 1);
              }
              venc = dQ;
            } else {
              venc.setDate(dayOfMonth);
            }
            break;
          }
          case 'anual': {
            // lógica semelhante a mensal, mas pula 1 ano
            const dayOfMonthA = parseInt(document.getElementById('recorrencia-dia-mes').value, 10);
            const useBizDayA = diaUtil;
            const lastDayFlagA = ultimoDia;
            const sabUtilA = sabadoUtil;

            // avança um ano preservando o dia
            const originalDayA = venc.getDate();
            const nextYear = new Date(venc.getFullYear() + 1, venc.getMonth(), originalDayA);
            if (nextYear.getFullYear() !== venc.getFullYear() + 1) {
              venc = new Date(venc.getFullYear() + 1, venc.getMonth() + 1, 0);
            } else {
              venc = nextYear;
            }
            // aplica último dia ou dia útil ou fixo
            if (lastDayFlagA) {
              venc = new Date(venc.getFullYear(), venc.getMonth() + 1, 0);
            } else if (useBizDayA) {
              let countA = 0;
              let dA = new Date(venc.getFullYear(), venc.getMonth(), 1);
              while (true) {
                const ymdA = dA.toISOString().slice(0,10);
                const wkA = dA.getDay();
                if (wkA !== 0 && (sabUtilA || wkA !== 6) && !feriados.includes(ymdA)) {
                  countA++;
                  if (countA === dayOfMonthA) break;
                }
                dA.setDate(dA.getDate() + 1);
              }
              venc = dA;
            } else {
              venc.setDate(dayOfMonthA);
            }
            break;
          }
          case 'quinzenal':
            venc = new Date(parseYMD(baseVenc));
            venc.setDate(venc.getDate() + 14 * i);
            break;
          case 'semanal': {
            const el = document.getElementById('recorrencia-semana-dia');
            const diaSemana = el ? parseInt(el.value, 10) : NaN;
            if (isNaN(diaSemana)) {
              console.warn('Semana: dia inválido, pulando');
              break;
            }
            // 1) achar primeiro occurrence depois de baseVenc
            const base = parseYMD(baseVenc);
            let diff = (diaSemana - base.getDay() + 7) % 7;
            if (diff === 0) diff = 7;
          const first = new Date(base);
            first.setDate(base.getDate() + diff);
            // 2) agora puxa 7*(i-1) dias pra cada iteração i
            venc = new Date(first);
            venc.setDate(first.getDate() + 7 * (i - 1));
            break;
          }
          case 'diario': {
            // avança até o próximo dia (sempre avança pelo menos 1 dia)
            do {
              venc.setDate(venc.getDate() + 1);
            } while (
              // enquanto estiver em fim de semana ou feriado, e se 'somente dias úteis' estiver marcado
              diaUtil && (
                venc.getDay() === 0 ||                                      // domingo
                (!sabadoUtil && venc.getDay() === 6) ||                     // sábado (se não for dia útil)
                feriados.includes(venc.toISOString().slice(0,10))           // feriado
              )
            );
            break;
          }

        }
        // atualiza competência
        const fixComp = document.getElementById('fixar-data-competencia').checked;

        // e aqui dentro do loop, depois de todo o cálculo de 'venc':
        comp = computeCompDate(baseComp, tipo, i, fixComp);

        
        // ajustes de fim de semana e feriados (sem diaUtil)
            const ymd = venc.toISOString().slice(0,10);

            let wk = venc.getDay();
            if ((wk===0 || (!sabadoUtil && wk===6) || feriados.includes(ymd)) && !diaUtil) {
              if (postergar) {
                do {
                  venc.setDate(venc.getDate()+1);
                  wk = venc.getDay();
                } while (wk===0 || (!sabadoUtil && wk===6) || feriados.includes(venc.toISOString().slice(0,10)));
              } else if (antecipar) {
                do {
                  venc.setDate(venc.getDate()-1);
                  wk = venc.getDay();
                } while (wk===0 || (!sabadoUtil && wk===6) || feriados.includes(venc.toISOString().slice(0,10)));
              }
            }
            // pula se somente dia útil e não for dia útil verdadeiro
            if (diaUtil && (venc.getDay() === 0 || (!sabadoUtil && venc.getDay() === 6) || feriados.includes(ymd))) {
              continue;
            }

      }

      

      recs.push({
        vencimento:  venc.toISOString(),
        competencia: comp.toISOString(),
        valor,
        descricao,
        categoria,
        
      });
      // atualiza sementes
      vencDate = new Date(venc);
      compDate = new Date(comp);
    }
    return recs;
  }

  // ——— Recria a tabela de recorrências ———
  function rebuildRecurrences() {
    const apply = document.getElementById('check-recorrencia').checked;
    const target = document.getElementById('tabela-array-lançamentos');
    target.innerHTML = '';
    if (!apply) return;

    const venc  = document.getElementById('data-vencimento').value;
    const comp  = document.getElementById('data-competencia').value;
    const desc  = document.getElementById('descricao').value.trim();
    const cat   = document.getElementById('categoria').value;
    
    const valBr = parseFloat(document.getElementById('valor-bruto').value.replace(',', '.'));

    const qtd   = parseInt(document.getElementById('qtd-recorrencia').value,10);
    if (!venc||!comp||!desc||!cat||isNaN(valBr)||isNaN(qtd)||qtd<1) return;

    const flags = {
      diaUtil: !!(
        document.getElementById('recorrencia-dia-util')?.checked ||
        document.getElementById('recorrencia-somente-util')?.checked
      ),
      // mensal
      ultimoDia: !!document.getElementById('ultimo-dia')?.checked,
      // considerar sábado como útil em qualquer recorrência
      sabadoUtil: !!(
        document.getElementById('recorrencia-sabado')?.checked ||
        document.getElementById('recorrencia-diario-sabado')?.checked ||
        document.getElementById('recorrencia-semanal-sabado')?.checked ||
        document.getElementById('quinzenal-sabado')?.checked
      ),
      // antecipar/postergar para mensal e quinzenal e semanal
      postergar: !!(
        document.getElementById('recorrencia-postergar')?.checked ||
        document.getElementById('quinzenal-util-postergar')?.checked ||
        document.getElementById('semanal-postergar')?.checked
      ),
      antecipar: !!(
        document.getElementById('recorrencia-antecipar')?.checked ||
        document.getElementById('quinzenal-util-antecipar')?.checked ||
        document.getElementById('semanal-antecipar')?.checked
      ),
      // lista global de feriados em 'YYYY-MM-DD'
      feriados
    };
    const params = { baseVenc: venc, baseComp: comp, tipo: document.getElementById('recorrencia-tipo').value, qtd, descricao: desc, categoria: cat, valor: valBr, flags };
    const recs = generateRecurrenceArray(params);

      // --- prefixa a descrição conforme rádio selecionado ---
      const radios = document.getElementsByName('radio-rec');
      let mode   = null;
      radios.forEach(r=> { if(r.checked) mode = r.id; });

      const total = recs.length;
      const baseDesc = document.getElementById('descricao').value.trim();

      recs.forEach((r, idx) => {
        const i = idx + 1;
        let prefix = '';

        switch(mode) {
          case 'rec-rd-1-de-10':
            prefix = `${i} de ${total}`;
            break;
          case 'rec-rd-1-b-10':
            prefix = `${i}/${total}`;
            break;
          case 'rec-rd-rf-m-y':
            const d = new Date(r.competencia);
            const m = MONTH_ABBRn[d.getMonth()];
            const y = d.getFullYear();
            prefix = `Ref: ${m}/${y}`;
            break;
          default:
            prefix = '';
        }

        r.descricao = prefix
          ? `${prefix} – ${baseDesc}`
          : baseDesc;
      });


    if (!recs.length) return;

    const tbl = document.createElement('table');
    tbl.classList.add('alt-table', 'table-striped');
    tbl.innerHTML = `
      <thead>
        <tr>
          <th>Vencimento</th>
          <th>Competência</th>
          <th>Valor</th>
          <th>Descrição</th>
          <th>Categoria</th>
          
        </tr>
      </thead>`;
    const body = document.createElement('tbody');
    recs.forEach(r => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${formatDateBR(r.vencimento)}</td>
        <td>${formatDateBR(r.competencia)}</td>
        <td>${r.valor.toFixed(2).replace('.',',')}</td>
        <td>${r.descricao}</td>
        <td>${r.categoria}</td>`;
      body.appendChild(tr);
    });
    tbl.appendChild(body);
    target.appendChild(tbl);
  }

  // —— dispara em toda mudança relevante ——
  // só executa recurrences se valor e tipo forem válidos
  function conditionalRebuild() {
    const valBrStr = document.getElementById('valor-bruto')?.value.replace(',', '.');
    const valBr = parseFloat(valBrStr);
    const tipoRec = document.getElementById('recorrencia-tipo')?.value;

    console.log('valBr:', valBr, 'tipoRec:', tipoRec);
    if (!isNaN(valBr) && valBr > 0 && tipoRec) {
      rebuildRecurrences();
    } else {
      console.log('Condições não atendidas para rebuild');
      document.getElementById('tabela-array-lançamentos').innerHTML = '';
    }
    }


  var debouncedRebuild = debounce(conditionalRebuild, 150);
  [
    '#data-vencimento','#data-competencia','#descricao',
    '#categoria','#valor-bruto',
    '#check-recorrencia','#qtd-recorrencia','#recorrencia-tipo'
  ].forEach(sel =>
    document.querySelector(sel)?.addEventListener('change', debouncedRebuild)
  );
  document.querySelectorAll('#container-recorrencia input, #container-recorrencia select')
    .forEach(el => el.addEventListener('change', debouncedRebuild));
  // dispara ao carregar
  debouncedRebuild();


  function verificaEnvio(){
    const pago = document.getElementById('booPag');
    const descricao = document.getElementById('descricao');
    const dataCompetencia = document.getElementById('data-competencia');
    const dataVencimento = document.getElementById('data-vencimento');
    const valorBruto = document.getElementById('valor-bruto');
    const categoria = document.getElementById('categoria');
    const dataPagamento = document.getElementById('data-pagamento');
    const contaCorrente = document.getElementById('conta-corrente');
    const tipoPagamento = document.getElementById('tipo-pagamento');
    const valorLiquido = document.getElementById('valor-liquido');

    
      let mensagem = '';

      if (descricao.value.trim() === '') {
        mensagem = 'Preencha a descrição';
      showErrorMessage(mensagem, descricao);
        return;
      }

      if (dataCompetencia.value.trim() === '') {
        mensagem = 'Preencha a data de competência';
      showErrorMessage(mensagem, dataCompetencia);
        return;
      }

      if (dataVencimento.value.trim() === '') {
        mensagem = 'Preencha a data de vencimento';
      showErrorMessage(mensagem, dataVencimento);
        return;
      }

      if (valorBruto.value.trim() === '' || parseFloat(valorBruto.value.replace(',', '.')) <= 0) {
        mensagem = 'Informe um valor bruto válido';
      showErrorMessage(mensagem, valorBruto);
        return;
      }

      if (categoria.value.trim() === '') {
        mensagem = 'Selecione uma categoria';
      showErrorMessage(mensagem, categoria);
        return;
      }
      if (pago.value=='1'){
          if (dataPagamento.value.trim() === '') {
            mensagem = 'Preencha a data de pagamento';
          showErrorMessage(mensagem, dataPagamento);
            return;
          }

          if (contaCorrente.value.trim() === '') {
            mensagem = 'Selecione uma conta corrente';
          showErrorMessage(mensagem, contaCorrente);
            return;
          }

          if (tipoPagamento.value.trim() === '') {
            mensagem = 'Selecione o tipo de pagamento';
          showErrorMessage(mensagem, tipoPagamento);
            return;
          }

          if (valorLiquido.value.trim() === '' || parseFloat(valorLiquido.value.replace(',', '.')) <= 0) {
            mensagem = 'Informe um valor líquido válido';
          showErrorMessage(mensagem, valorLiquido);
            return;
          }
      }

      // Se chegou aqui, passou em todas as validações
      return true;
  }

} else{ // se o tipo de lançamento é transferência
  function verificaEnvioTransferencia(){
   
   const descricao = document.getElementById('descricao');
   const dataTransf = document.getElementById('data-transferencia');    
   const valorTransf = document.getElementById('tra-valor-transferencia');

   const tpPagSaida= document.getElementById('tra-tipo-pagamento-saida');
   const contaSaida = document.getElementById('tra-conta-corrente-saida');

   const tpPagEntrada= document.getElementById('tra-tipo-pagamento-saida');
   const contaEntrada = document.getElementById('tra-conta-corrente-entrada');

   
     let mensagem = '';

     if (descricao.value.trim() === '') {
       mensagem = 'Preencha a descrição';
     showErrorMessage(mensagem, descricao);
       return;
     }

     if (valorTransf.value.trim() === '' || parseFloat(valorTransf.value.replace(',', '.')) <= 0) {
       mensagem = 'Informe um valor bruto válido';
     showErrorMessage(mensagem, valorTransf);
       return;
     }


     if (dataTransf.value.trim() === '') {
       mensagem = 'Preencha a data da Transferência';
     showErrorMessage(mensagem, dataTransf);
       return;
     }



     if (tpPagSaida.value.trim() === '') {
       mensagem = 'Selecione uma tipo de pagamento de saída';
     showErrorMessage(mensagem, tpPagSaida);
       return;
     }
     
     if (tpPagEntrada.value.trim() === '') {
       mensagem = 'Selecione uma tipo de pagamento de entrada';
     showErrorMessage(mensagem, tpPagEntrada);
       return;
     }
     
     if (contaSaida.value.trim() === '') {
       mensagem = 'Selecione uma conta de saída';
     showErrorMessage(mensagem, contaSaida);
       return;
     }
     
     if (contaEntrada.value.trim() === '') {
       mensagem = 'Selecione uma conta de entrada';
     showErrorMessage(mensagem, contaEntrada);
       return;
     }

     if (contaEntrada.value ==contaSaida.value){
      mensagem = 'A conta de saida não pode ser a mesma conta de entrada';
      showErrorMessage(mensagem, contaSaida);
      showErrorMessage(mensagem, contaEntrada);

      return;

     }



     

     // Se chegou aqui, passou em todas as validações
     return true;



 }

}

</script>


<script>
// ——————— Envio AJAX de Recorrências ———————


var form = document.getElementById('formVenda');
form.addEventListener('submit', async function(e) {


  e.preventDefault();

  //if (idVenda){
    //const elemento = document.getElementById('modal-body');
    //showErrorMessage("Não é possível alterar uma venda pelo funanceiro", elemento);
    //return;
 // }






  if(tipoLancamento=='transferencia'){
    // 1) pega todos os campos do form (incluindo selects)
    const prosseguir =  (verificaEnvioTransferencia());
    if (!prosseguir)return;


    const formEntries = Object.fromEntries(new FormData(form).entries());

    // 2) itera cada <select> dentro de #cont-transf
    document.querySelectorAll('#cont-transf select').forEach(select => {
      // use o name (ou o id) do select para compor a chave
      const key = select.name;         // ex: 'conta_pg_saida'
      const textKey = `${key}-text`;   // ex: 'conta_pg_saida-text'
      formEntries[textKey] = select.options[select.selectedIndex]?.text || '';
    });

    // 3) garanta que recurrences é um array (mesmo vazio)
    const recurrences = []; // ou seu array real

    // 4) monte o payload
    const payload = {
      form: formEntries,
      recurrences
    };

  

    try {
      const response = await fetch('./entradas-saidas/banco_gravar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const result = await response.json();
      if (result.success) {
        $('#modalLancamento').modal('hide');
        document.getElementById('btn-buscar').click();
        
      } else if (!result.success) {
        //alert(result.message || 'Erro ao salvar');
        console.log(result.message);
        showErrorMessage(result.message);
      }
    } catch (err) {
      console.error(err);
      alert('Erro de comunicação com o servidor');
    }


  }


  
if(tipoLancamento!='transferencia'){
  
  const prosseguir = verificaEnvio();
  if (!prosseguir)return;

  // 1) Monta o objeto com TODOS os campos do form
  const formEntries = Object.fromEntries(new FormData(form).entries());

  // 2) Lista de selects cujos textos queremos incluir
  const selectIds = [
    'categoria',
    'conta-corrente',
    'tipo-pagamento',
    'forma-pagamento',
    'centro-custo',
    'fornecedor'
  ];
  selectIds.forEach(id => {
    const sel = document.getElementById(id);
    if (sel) {
      // injeta formEntries["<id>-text"] = texto
      formEntries[`${id}-text`] = sel.options[sel.selectedIndex]?.text ?? '';
    }
  });


  
    
  let recurrences = [];
  if (novoLancamento){
    const applyRec = document.getElementById('check-recorrencia').checked;
    if (applyRec) {
      // mesmos parâmetros de antes
      const venc = document.getElementById('data-vencimento').value;
      const comp = document.getElementById('data-competencia').value;
      const desc = document.getElementById('descricao').value.trim();
      const cat  = document.getElementById('categoria').value;
      
      const val  = document.getElementById('valor-bruto').value;
      const qtd  = document.getElementById('qtd-recorrencia').value;
      const tipo = document.getElementById('recorrencia-tipo').value;
      const flags = {
        diaUtil:    !!document.getElementById('recorrencia-dia-util')?.checked
                  || !!document.getElementById('recorrencia-somente-util')?.checked,
        ultimoDia:  !!document.getElementById('ultimo-dia')?.checked,
        sabadoUtil: !!(
                      document.getElementById('recorrencia-sabado')?.checked ||
                      document.getElementById('recorrencia-diario-sabado')?.checked ||
                      document.getElementById('recorrencia-semanal-sabado')?.checked ||
                      document.getElementById('quinzenal-sabado')?.checked
                    ),
        postergar:  !!(
                      document.getElementById('recorrencia-postergar')?.checked ||
                      document.getElementById('quinzenal-util-postergar')?.checked ||
                      document.getElementById('semanal-postergar')?.checked
                    ),
        antecipar:  !!(
                      document.getElementById('recorrencia-antecipar')?.checked ||
                      document.getElementById('quinzenal-util-antecipar')?.checked ||
                      document.getElementById('semanal-antecipar')?.checked
                    ),
        feriados    // global array
      };

      const params = {
        baseVenc: venc,
        baseComp: comp,
        tipo,
        qtd: parseInt(qtd, 10),
        descricao: desc,
        categoria: cat,
        
        valor: parseFloat(val.replace(',', '.')),
        flags
      };
      console.log('APLICANDO RECORRENCIAS');
      recurrences = generateRecurrenceArray(params);
    }
  }

  const radios = document.getElementsByName('radio-rec');
  let mode = Array.from(radios).find(r=>r.checked)?.id;
  const total = recurrences.length;
  const baseDesc = document.getElementById('descricao').value.trim();

  recurrences = recurrences.map((r, idx) => {
    const i = idx + 1;
    let prefix = '';
    switch(mode) {
      case 'rec-rd-1-de-10':
        prefix = `${i} de ${total}`;
        break;
      case 'rec-rd-1-b-10':
        prefix = `${i}/${total}`;
        break;
      case 'rec-rd-rf-m-y':
        const d = new Date(r.competencia);
        const m = MONTH_ABBRn[d.getMonth()];
        const y = d.getFullYear();
        prefix = `Ref: ${m}/${y}`;
        break;
    }
    return {
      competencia: r.competencia,
      vencimento:  r.vencimento,
      valor:       r.valor,
      descricao:   prefix ? `${prefix} – ${baseDesc}` : baseDesc
    };
  });

  // 4) Monta payload
  const payload = {
    form: formEntries,
    recurrences
  };

  try {
    const response = await fetch('./entradas-saidas/banco_gravar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const result = await response.json();
    if (result.success) {
     $('#modalLancamento').modal('hide');
      // atualizar listagem, se quiser

      document.getElementById('btn-buscar').click();
     
    } else {
      alert(result.message || 'Erro ao salvar');
    }
  } catch (err) {
    console.error(err);
    alert('Erro de comunicação com o servidor');
  }
}// fim do if !transferencia
});


</script>
