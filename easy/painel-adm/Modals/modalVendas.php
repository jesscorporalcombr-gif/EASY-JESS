<?php
// Início do script PHP
$pag = 'venda';
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

$id_usuario    = $_SESSION['id_usuario'];    // user_criacao ou user_alteracao
$nome_usuario  = $_SESSION['nome_usuario'];
$pasta = $_SESSION['x_url'];
?>



<?php

// Recebe dados via POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$tipo_venda = isset($_POST['tipo']) ? $_POST['tipo'] : ''; //proposta ou venda

// serviços



$inputRaw = file_get_contents('php://input');
$postJson = json_decode($inputRaw, true);

// Se for POST de agenda via JSON
if (isset($postJson['origem']) && $postJson['origem'] === 'agenda') {
    $origemAgenda = true;
    $id_cliente   = $postJson['id_cliente'];
    $nome_cliente = $postJson['nome_cliente'] ?? '';
    $servicosAg     = $postJson['servicos']; // array com id_servico, preco
    // ...pegar outros dados do cliente se enviados...
} else {
    $origemAgenda = false;
    // ... segue fluxo tradicional ...
}



if ($origemAgenda) {
    // Monte o array de itens da venda baseado nos serviços vindos do POST
        $cliente = buscar_cliente_por_id_cpf_ou_nome($pdo, $id_cliente, '', '');
    if ($cliente) {
        $nome_cliente = $cliente['nome'];
        $cpf = $cliente['cpf'];
        $id_cliente = $cliente['id'];
        $sexo = $cliente['sexo'];
        $foto_cliente = $cliente['foto'];
        $saldo_cliente = $cliente['saldo'];
        $celular = $cliente['celular'] ?? $celular;
        $email = $cliente['email'];
        // ...e assim por diante
    }
    $tipo_venda = 'venda';
    $id=(-1);

    $venda_itens = [];
    foreach ($servicosAg as $srv) {
        $venda_itens[] = [
            'id'            => null,                 // Ainda não existe na tabela
            'id_venda'      => null,                 // Ainda não existe na tabela
            'id_cliente'    => $id_cliente,          // Do POST ou sessão
            'tipo_item'     => 'servico',
            'id_item'       => $srv['id_servico'],
            'quantidade'    => 1,
            'precoUn'       => (float)$srv['preco'],
            'precoUn_efetivo'=> (float)$srv['preco'],
            'preco_total'   => (float)$srv['preco'],
                    // Ou outro valor padrão se desejar
            // Adicione outros campos da tabela como null/padrão se necessário
        ];
    }

    // Se quiser, monte outros arrays de dados do cliente, etc
}






$query_servicos = $pdo->prepare("SELECT id, servico, categoria, descricao, tempo, valor_venda, valor_custo, tipo, folga_necess FROM servicos WHERE excluido <> 1");
$query_servicos->execute();
$servicos = $query_servicos->fetchAll(PDO::FETCH_ASSOC);





$titulo_modal = $tipo_venda;

//echo 'tipo de proposta' . $tipo;

$sigla='';

switch ($tipo_venda) {
  case 'proposta':
      $sigla = 'P';
      break;
  case 'venda':
      $sigla = 'V';
      break;
  case 'cortesia':
      $sigla = 'C';
      break;
  default:
      $sigla = '';
}

function buscar_cliente_por_id_cpf_ou_nome($pdo, $id_cliente = null, $cpf_venda = '', $nome_venda = '') {
    // 1. Busca prioritária por ID, se fornecido
    if (!empty($id_cliente)) {
        $query = $pdo->prepare("
            SELECT id, nome, aniversario, celular, email, sexo, cpf, foto, saldo
            FROM clientes
            WHERE id = ?
            LIMIT 1
        ");
        $query->execute([$id_cliente]);
        $cliente = $query->fetch(PDO::FETCH_ASSOC);
        if ($cliente) return $cliente;
    }

    // 2. Busca por CPF ou nome
    $cpf_venda  = ltrim(str_replace(['.', '-', ' '], '', $cpf_venda ?? ''), '0');
    $nome_venda = strtolower(trim($nome_venda ?? ''));

    $query = $pdo->prepare("
        SELECT id, nome, aniversario, celular, email, sexo, cpf, foto, saldo
        FROM clientes
        WHERE REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ?
           OR LOWER(TRIM(nome)) = ?
        LIMIT 2
    ");
    $query->execute([$cpf_venda, $nome_venda]);
    $clientes_encontrados = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($clientes_encontrados) {
        // Prioridade: CPF
        foreach ($clientes_encontrados as $c) {
            $cpf_cliente = ltrim(str_replace(['.', '-', ' '], '', $c['cpf'] ?? ''), '0');
            if ($cpf_cliente == $cpf_venda) {
                return $c;
            }
        }
        // Se não achou pelo CPF, retorna o primeiro pelo nome
        return $clientes_encontrados[0];
    }
    // Se não achou nada, retorna null
    return null;
}



//=================ARRAYS=====================

//clientes


if (!$id>0){
  $query_clientes = $pdo->prepare("SELECT id, nome, aniversario, celular, email, sexo, cpf, foto, saldo FROM clientes");
  $query_clientes->execute();
  $clientes = $query_clientes->fetchAll(PDO::FETCH_ASSOC);
}






$query_tipos_pagamentos = $pdo->prepare("SELECT id, nome FROM pagamentos_tipo ");
$query_tipos_pagamentos->execute();
$tipos_pagamentos = $query_tipos_pagamentos->fetchAll(PDO::FETCH_ASSOC);


$query_formas_pagamentos = $pdo->prepare("SELECT * FROM pagamentos_forma ");
$query_formas_pagamentos->execute();
$formas_pagamentos = $query_formas_pagamentos->fetchAll(PDO::FETCH_ASSOC);

$query_impostos = $pdo->prepare("SELECT * FROM impostos WHERE valido=1");
$query_impostos->execute();
$impostos = $query_impostos->fetchAll(PDO::FETCH_ASSOC);

$query_vendedores = $pdo->prepare("SELECT id, nome FROM cadastro_colaboradores WHERE contrato_ativo=1");
$query_vendedores->execute();
$vendedores= $query_vendedores->fetchAll(PDO::FETCH_ASSOC);

$query_venda_custos = $pdo->prepare("SELECT * FROM venda_custos");
$query_venda_custos->execute();
$venda_custos = $query_venda_custos->fetchAll(PDO::FETCH_ASSOC);


//contas correntes
$query_contas_correntes = $pdo->prepare("SELECT * FROM contas_correntes WHERE ativa = 1 AND (interna IS NULL OR interna = 0)");
$query_contas_correntes->execute();
$contas_correntes = $query_contas_correntes->fetchAll(PDO::FETCH_ASSOC);

//produtos
$query_produtos = $pdo->prepare("SELECT id, nome, valor, valor_compra FROM produtos");
$query_produtos->execute();
$produtos = $query_produtos->fetchAll(PDO::FETCH_ASSOC);


//=============   FIM DOS ARRAYS      ===================



foreach($venda_custos as $custo){

  if ($custo['nome']=='custo_hora_clinica'){
    $custo_hora += $custo['valor'];
  }

}


$dias_validade=5;
$imposto_venda=0;
$imposto_mostra=0;

//echo 'Chegou aqui';


$situacao = 'ativo';



foreach($impostos as $imposto){
   if ($imposto['mostrar_na_venda']){
      $imposto_mostra += $imposto['taxa'];
    }
    if ($imposto['calcular_na_venda']){
      $imposto_venda += $imposto['taxa'];
    }
}






$bloquearCampos = ($id > 0); 

if ($id > 0) {
    $novo=0;
    try {
        // Consulta principal para obter os dados da venda
        $query = $pdo->prepare("SELECT * FROM venda WHERE id = :id");
        $query->execute([':id' => $id]);
        $venda = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$venda) {
            throw new Exception("Venda não encontrada.");
        }

        $id_cliente           = $venda['id_cliente'];
        $nome_cliente             = $venda['cliente'];
        $cpf                  = $venda['cpf'];
        $email                = $venda['email'];
        $celular              = $venda['celular'];
        $informacoes          = $venda['informacoes'];

        $valor_final          = $venda['valor_final'];
        $tipo_venda_banco     = $venda['tipo_venda'];

        $cliente = buscar_cliente_por_id_cpf_ou_nome($pdo, $id_cliente, $cpf_venda, $nome_venda);
        if (empty($id_cliente) || $id_cliente == 0) {
          $id_cliente_encontrado = null;
      
          // Normaliza o CPF da venda
          $cpf_venda = ltrim(str_replace(['.', '-', ' '], '', $cpf), '0');
          $nome_venda = strtolower(trim($cliente));
          
          //echo "Comparando: CPF venda = $cpf_venda<br>";
   
          
          $foto_cliente='';
          if ($cliente) {
              $nome_cliente = $cliente['nome'];
              $id_cliente_encontrado = $cliente['id'];
              $sexo_cliente = $cliente['sexo'];
              
              $saldo_cliente = $cliente['saldo'];
              $celular = $cliente['celular'] ?? $celular;
              // ...e assim por diante
          }
              
          if (!$id_cliente_encontrado) {
            // throw new Exception("Cliente não encontrado com base em CPF ou nome.");
          }
          $sexo = $sexo_cliente;
          $id_cliente = $id_cliente_encontrado; 
          
        }
        
       
$foto_cliente = $cliente['foto'];
        
        $id_vendedor = $venda['vendedor_user_id'];
        $nome_vendedor = $venda['vendedor_nome'];
        $validade             = $venda['validade'];
        
        if (!$validade){
          $validade = date('Y-m-d',  strtotime("+{$dias_validade} days"));
        }
        $hora                 = $venda['hora'];
        $contratada           = $venda['contratada'];
        $assinada             = $venda['assinada'];
        $scaner               = $venda['scaner'];
        $excluida             = $venda['excluida'];
        $vendedor_nome        = $venda['vendedor_nome'];
        $vendedor_user_id     = $venda['vendedor_user_id'];
        $contrato             = $venda['contrato'];
        $data_contrato        = $venda['data_contrato'];
       // $data_scaner          = $venda['data_scaner'];
        //$user_scaner          = $venda['user_scanner'];
        $data_vencimento      = $venda['data_vencimento'];
        $bloqueada            = $venda['bloqueada'];
        $custo_total          = $venda['custo_total'];
        $dataHora_criacao     = $venda['dataHora_criacao'];
        $id_user_criacao      = $venda['id_user_criacao'];
        $user_criacao         = $venda['user_criacao'];
        $devolvido            = $venda['devolvido'];
        $dataHora_alteracao   = $venda['dataHora_alteracao'];
        $id_user_alteracao    = $venda['id_user_alteracao'];
        $user_alteracao       = $venda['user_alteracao'];
        $arquivoContrato_assinado = $venda['arquivoContrato_assinado'];
        $nota_fiscal          = $venda['nota_fiscal'];

        $data_venda = !empty($venda['data_venda']) ? $venda['data_venda'] : '';



        $data_hora  = date('d/m/Y - H:i:s', strtotime($venda['dataHora_criacao']));
        $data_proposta_ing = date('Y-m-d', strtotime($venda['dataHora_criacao']));
        $data_proposta_pt = date('d/m/Y', strtotime($venda['dataHora_criacao']));

        if ($tipo_venda=='venda' && $data_venda>0){
          $dt_tit_modal = date('d/m/Y', strtotime($data_venda));
          $tit_data = $dt_tit_modal;
        }  
        else{
          $dt_tit_modal=$data_proposta_pt;
          $tit_data = $dt_tit_modal;
        }

        $titulo_modal = $tipo_venda . ": ". $sigla . $id . " - Cliente: " . $nome_cliente . " - Data da " . $tipo_venda . ": " . $dt_tit_modal;
        $tipo_execucao = "edicao";

        // Consulta dos itens relacionados à venda
        $query_itens = $pdo->prepare("SELECT * FROM venda_itens WHERE id_venda = :id_venda");
        $query_itens->execute([':id_venda' => $id]);
        $venda_itens = $query_itens->fetchAll(PDO::FETCH_ASSOC);
        
        $totValTabela = 0;
        $totUnItens = 0;
        $totValItens = 0;
        $totValDesc = 0;
        $totPercDesc = 0;
        $totCuOp = 0;
        $toCuAdm = 0;
        $totImp = 0;
        $totTaxa = 0;
        $totLB = 0;
        $totLL = 0;
        $totPercMa = 0;

        $totPagamentos = 0;
        $totUnPagamentos = 0;
        $saldo_venda = 0;

        $totUnServ = 0;
        $totSessServ = 0;
        $totValServ = 0;

        $totUnProd = 0;
        $totQtProd = 0;
        $totValProd = 0;

        $totUnCartPres = 0;
        $totValCartPres = 0;

        foreach ($venda_itens as $item_ori) {
            $totValTabela += $item_ori['precoUn'] * $item_ori['quantidade'];
            $totUnItens += 1 ;
            $totValItens += $item_ori['preco_total'];
            $totCuOp += $item_ori['custo_1'];
            $totCuAdm += $item_ori['custo_2'];
            $totImp += $item_ori['custo_3'];
            $totTaxa += $item_ori['custo_4'];


            if($item_ori['tipo_item']=="servico"){
              $totUnServ += 1;
              $totValServ += $item_ori['preco_total'];
              $totSessServ +=$item_ori['quantidade'];
            }

            if($item_ori['tipo_item']=="produto"){
              $totUnProd += 1;
              $totQtProd += $item_ori['quantidade'];
              $totValProd +=$item_ori['preco_total'];
            }
            
            $CustoTotal = ($totCuOp+$totCuAdm+$totImp+$totTaxa);

            $totValDesc= $totValTabela-$totValItens;
            $totPercDesc = 100-($totValItens/$totValTabela*100);
            $totPercMa = 100-($CustoTotal/$totValItens*100);
            
            $totLB = $totValItens - $totCuOp;
            $totLL = $totValItens - $CustoTotal;
            
           
            //$totPagamentos depois
            //$saldo_venda depois
    
          }
        
        


        $total_itens = count($venda_itens);

        // Consulta dos pagamentos relacionados à venda
        $query_pagamentos = $pdo->prepare("SELECT * FROM venda_pagamentos WHERE id_venda = :id_venda");
        $query_pagamentos->execute([':id_venda' => $id]);
        $venda_pagamentos = $query_pagamentos->fetchAll(PDO::FETCH_ASSOC);
        $total_pagamentos = count($venda_pagamentos);
        


        $taxaMedia = 0;

        foreach ($venda_pagamentos as $pagamento_ori) {
          $totPagamentos += $pagamento_ori['valor'];
          $calcTxTt += $pagamento_ori['valor']*$pagamento_ori['taxa']/100;
          $totUnPagamentos += 1 ;
        }
        if ($totPagamentos){
        $taxaMedia = $calcTxTt/$totPagamentos*100;
        } else{
          $taxaMedia = 0;
        }
        $saldo_venda = $totPagamentos-$totValItens;



        $saldo_venda = floatval($saldo_venda);

          // display: block se diferente de zero, senão none
          $display = ($saldo_venda != 0) ? 'block' : 'none';

          // cor verde se positivo, vermelha se negativo
          $cor = ($saldo_venda > 0) ? 'green' : 'red';

          // formata com vírgula e 2 casas
          $saldoFormatado = 'R$ ' . number_format($saldo_venda, 2, ',', '.');


          $params = [
              'id_cliente'   => $id_cliente,
          ];
          if ($id)     $params['id_venda']     = $id;
          if ($data_venda) $params['data_posicao'] = $data_venda;

          $url = 'https://easyclinicas.com.br/easy/painel-adm/endPoints/consultar_saldo_cliente.php'
              . '?' . http_build_query($params);

          // faz a requisição
          $ch = curl_init($url);
          curl_setopt_array($ch, [
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_TIMEOUT        => 5,
          ]);
          $json = curl_exec($ch);
          if ($json === false) {
              throw new Exception('cURL error: '.curl_error($ch));
          }
          curl_close($ch);

          // decodifica o JSON e pega o saldo
          $data = json_decode($json, true);
          if (isset($data['error'])) {
              throw new Exception('API error: '.$data['error']);
          }

          $saldo_cliente = (float) ($data['saldo'] ?? 0); // aqui está o saldo retornado


          if($saldo_cliente<0){
            $classeSaldoCliente='num-negativo';
          }else{
            $classeSaldoCliente='num-positivo';

          }

          $saldo_final = $saldo_cliente+$saldo_venda;





    } catch (PDOException $e) {
        exit("Erro no banco de dados: " . htmlspecialchars($e->getMessage()));
    } catch (Exception $e) {
        exit(htmlspecialchars($e->getMessage()));
    }
} else {

    $novo = 1;
    $totPercMa=0;

    // Para nova venda
    $titulo_modal = $tipo_venda;
    $tp_execucao = "criacao";
   
    $display='none';
    // Inicializa as variáveis para evitar erros no HTML
    $venda = [];
    if ($id!=-1){
    $venda_itens = [];
    $total_itens = 0;
    $total_pagamentos = 0;
  }
    $venda_pagamentos = [];
 





    
}



function corMargem($valor) {
  if ($valor < 2) {
      return 'rgb(128, 0, 128)';   // roxo
  } elseif ($valor < 7) {
      return 'rgb(255, 0, 0)';     // vermelho
  } elseif ($valor < 12) {
      return 'rgb(255, 192, 203)'; // rosa
  } elseif ($valor < 17) {
      return 'rgb(255, 165, 0)';   // laranja
  } elseif ($valor < 20) {
      return 'rgb(255, 255, 0)';   // amarelo
  } elseif ($valor < 25) {
      return 'rgb(0, 128, 0)';     // verde
  } else {
      return 'rgb(46, 136, 209)';     // azul
  }
}



$corMargem= corMargem($totPercMa);

if (!$total_itens){
  $corMargem = 'rgb(255,255,255)';
}



//$linha1_bl_principal - montando todo o html
$texto = '<b>'. strtoupper($tipo_venda) . ': </b>';
 //$texto = $texto . $sigla . $id . '     |    
if ($id>0) {
  $texto = '
  <div class="col-auto" style="margin-bottom:-5px; max-width: 150px">
    <div class="input-group ">
      <span style="padding-top: 4px;">Data:</span>
      <input type="text" value="'.$tit_data.'" class="form-control blockItem data-venda" name="data_venda" id="data-venda">
    </div>
  </div>'

  ;
}
else {
  $texto ='
  <div class="col-auto" style="margin-bottom:-5px; max-width: 150px">
    <div class="input-group ">
      <span style="padding-top: 4px;">Data:</span>
      <input type="date" value="'.date('Y-m-d').'" class="form-control data-validade" name="data_venda" id="data-venda">
      
    </div>
  </div>'
   ;
}

//$linha da validade
if ($tipo_venda!='venda'){
    $texto_validade = '<b> VALIDADE: </b>';
    if ($id>0) {
            $texto_validade = $texto_validade . '<input type="text" value="'.date('d/m/Y',strtotime($validade)).'" style="display: inline-block; width: auto;" class="form-control input-bloqueado data-validade" name="data-validade" id="data-validade"' ;
    }
    else {
          $texto_validade= $texto_validade .'     |     <input type="date" value="'.date('Y-m-d', strtotime("+{$dias_validade} days")).'" style="display: inline-block; width: auto;" class="form-control blockItem data-validade" name="data-validade" id="data-validade"' ;
    }
} 
else{
    $texto_validade = '';
}

$texto_vendedor = '
          <div class="col-auto mb-3" style="max-width: 240px;">
            <div class="input-group ">
              <span style="padding-top: 4px;">Vendedor:</span>
              <select class="form-control vendedor-selecionado blockItem" id="nome-vendedor" name="nome-vendedor">
                              <option>Selecione o Vendedor</option>
                              
            ';

foreach ($vendedores as $saler) {
    $selected = '';

    if ($id > 0) {
        $selected = ($id_vendedor == $saler['id']) ? ' selected value="'. $saler['nome'] . '"' : '';
    } else {
        $selected = ($id_usuario == $saler['id']) ? 'selected value="'. $saler['nome'] . '"' : '';
        $id_vendedor = $id_usuario;
    }

    $texto_vendedor .= '<option id-vendedor="' . $saler['id'] . '" ' . $selected . '>'
        . $saler['nome']
        . '</option>';
}

$texto_vendedor .= '
                      </select>
                    </div>
                  </div>
                  ';


?>


<div class="modal fade" tabindex="-1" style="z-index: 95000;" id="modalVenda" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= $titulo_modal ?></h5>
        
          <button id="btnToggleAvancado" onclick="toggleAvancado()" type="button" style="height:15px; width:auto; font-size: smaller;" class="btn btn-light centBt">
            Avançado
          </button>
          <button type="button" class="btn-fecha-modal" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
      </div>  
      <form method="POST" id="formVenda">
        <div class="modal-body" style="min-height: 200px; overflow-y: auto; background-color:#F8F8F8;">
          <div class="row justify-content-end" id="mod-venda-ft-ln" style="margin-top:-15px; min-height:48px;">
                    
                      <div class="col mb-2" style="display:<?= $id>0?'block' :'none'?>" >
                          <button 
                                id="btnHabilitaEdicao" 
                                type="button" 
                                class="btn btn-primary btn-top-venda" 
                                >
                            <i class="bi bi-pencil-square"></i>
                            Habilitar Edição
                          </button>
                      </div>
                     
             

                    <div class="col-auto mb-2"<?=($id > 0) ? '' : 'hidden' ?> id="coluna-direita" >
                      <button 
                                    class="btn btn-primary btn-top-venda " 
                                    type="button" 
                                    id="btn-gerar-contrato"
                                    style= " background-color:blueviolet;"
                                    title="Contrato"
                                    onclick="">
                                    <i class="bi bi-file-earmark-text"></i> Contrato
                      </button>
                    </div>
                    <input type="hidden" type="text" id="data-contrato">
                    <input type="hidden" id="dias-validade">
                    <?php
                    if ($id>0 && $tipo_venda=='proposta'){echo '
                    <div class="col-auto mb-2">
                      <button 
                                    class="btn btn-success btn-top-venda " 
                                    type="button" 
                                    id="btn-gerar-proposta"
                                    title="Contrato"
                                    onclick="">
                                    <i class="bi bi-file-earmark-text"></i> Proposta
                      </button>
                    </div>';
                    }
                    if ($tipo_venda=='venda'){echo '
                      <div class="col-auto" style="display:'; if($id>0){echo'bock';}else{echo 'none';} echo'">
                        <button 
                                      class="btn btn-warning  btn-top-venda" 
                                      type="button" 
                                      id="btn-imprimir-recibo"
                                      title="Recibo"
                                      
                                      onclick="">
                                    <i class="bi bi-receipt"> </i>Recibo
                        </button>
                      </div>';
                     }
                     
                    if ($tipo_venda=='proposta' && $id>0){echo '
                      <div class="col-auto">
                            <button 
                                          class="btn btn-info btn-top-venda " 
                                          type="button" 
                                          id="btn-vender"
                                          title="Vender"
                                          style="color:#F8F8F8;"
                                          onclick="">
                                        <i class="bi bi-cash-coin"></i>Vender
                            </button>
                      </div>';
                    }
                    ?>

                   <?=$texto. $texto_vendedor?>





              <hr style="border:none; border-bottom: 1px solid rgb(195, 195, 195); margin-top:-17px;">
          </div> 
              
          <div class="row">
            <div class="col-md-12" style="min-height:40px; ">
              <div class="row" id="linha-cliente-mod-vendas">
                <input type="hidden" name="id-venda" id="id-venda" value="<?= isset($id) ? $id : '' ?>">
                <input type="hidden" name="tipo-venda"  value="<?= isset($tipo_venda) ? $tipo_venda : 'proposta' ?>">
                <input type="hidden" name="id-cliente" id="id-cliente"  value="<?= isset($id_cliente) ? $id_cliente : '' ?>">
                <input type="hidden" id="sexo-cliente"  value="<?= isset($sexo) ? $sexo : '' ?>">
                
                <div class="col-auto" id="col-img-foto-cliente" style=" margin-top: -10px; padding-left: 10px; width: 40px; display:<?= (!$foto_cliente) ? 'none' : 'block' ?>"> <!-- Foto do cliente -->
                  <div id="divImgConta">
                    <img style="width: 35px; border-radius: 50%;" id="img-foto-cliente-modVendas" <?=$foto_cliente?'src="../'.$pasta.'/img/clientes/'. $foto_cliente . '"':'' ?>>
                  </div>
                </div>
                <div class="col-auto mb-3">
                  <label for="nome" class="form-group" style="margin-left:10px;" >Nome</label>
                  <div class="input-group">
                    <input 
                      type="text" 
                        class="form-control nome-cliente input-nome-cliente <?= ($id > 0 || $origemAgenda) ? 'blockItem' : '' ?>"
                        style="min-width:185px; max-width:max-content;"
                        id="nome-cliente" 
                        autocomplete="off"   
                        name="nome-cliente" 
                        placeholder="Nome" 
                        required 
                        value="<?= isset($nome_cliente) ? $nome_cliente : '' ?>">  
                    <button 
                          class="btn btn-outline-secondary" 
                          type="button" 
                          id="btn-adicionar-cliente"
                          <?=isset($id) ? 'style="border:none;"' : '' ?>
                          title="<?= isset($id_cliente) ? 'Visualizar cliente' : 'Adicionar novo cliente' ?>"
                          onclick="abrirModal('modalClientes', document.getElementById('id-cliente').value)">
                          <i class="bi <?= ($id_cliente) ? 'bi-eye" style="border: none;"' : 'bi-person-plus' ?>" id= "ico-inputCliente"></i>
                    </button>
                  </div>
                  <ul id="lista-clientes" class="sugestoes lista-clientes"></ul>
                </div>
                <div class="col-auto mb-3">
                  <label for="cpf" class="form-group " style="margin-left:10px;" >CPF</label>
                  <input type="text" class="form-control input-cpf-cliente <?= isset($id)? ' blockItem':'' ?>" id="cpf-cliente" name="cpf-cliente" placeholder=""  value="<?= isset($cpf) ? $cpf : '' ?>">
                  <div class="invalid-feedback" id="cpfError" style="display: none; color: red;">
                    CPF inválido!
                  </div>
                </div>
                <div class="col-auto mb-3">
                  <label for="celular" class="form-group" style="margin-left:10px;" >Celular</label>
                  <input type="text" class="form-control input-celular-cliente<?= isset($id)? ' blockItem':'' ?>" id="celular-cliente" name="celular-cliente" placeholder="" required value="<?= isset($celular) ? $celular : '' ?>">
                </div>
                <div class="col-auto mb-3">
                  <label for="email" class="form-group" style="margin-left:10px;" >Email</label>
                  <input type="text" class="form-control input-email-cliente <?= isset($id)? ' blockItem':'' ?>" id="email-cliente" name="email-cliente" placeholder="" value="<?= isset($email) ? $email : '' ?>">
                </div>
              </div>
            </div> <!--fechamento da col 10-->
            <hr style=" border:none; border-bottom: 1px solid #000; margin-top:-18px;">
          </div> <!-- fechamento da linha-->

          <div id="proposta-vendas" style="<?= ($id) ? 'display:block' : 'display:none' ?> ;" >        <!--- encapsulamento do conteudo da proposta-->
            <div class="row">
              <div class="col-md-12" id="coluna-esquerda">
                <!--DADOS DA PROPOSTA-->
                  <div class="container px-1" id="resumoProposta" style="padding: 3px; border-radius:5px; ">
                    <div class="row gx-1 gy-3"> <!-- Adicionado gy-3 para espaçamento vertical -->
                      <div class="row row-cols-1 row-cols-lg-5">
                        <div class="col p-3" id="bloco-saldo-cliente" class="">
                          <div class="bloco-easy bl-easy-modVendas">
                            <label  class="form-group label-bl-fin">Saldo Cliente:</label>
                            <span id="sp-saldo-cliente" class="<?=$classeSaldoCliente?> span-modVendas">R$ <?= ' '. number_format($saldo_cliente, 2, ",", ".") ?> </span>
                          </div>
                        </div>
                        <div class="col p-3" >
                          <div class="bloco-easy bl-easy-modVendas" id="bloco-total-venda">
                              <label  id="label-valor-final"class="form-group label-bl-fin">Total Venda:</label>
                              <span id="bl-valor-final" class="num-positivo span-modVendas">R$ <?= ' '. number_format($valor_final, 2, ",", ".") ?> </span>
                          </div>
                        </div>
                        <div class="col p-3" id="bl-total-pagamentos">
                          <div class="bloco-easy bl-easy-modVendas">
                            <label  class="form-group label-bl-fin">Pagamentos:</label>
                            <span class="num-negativo span-modVendas" id="sp-total-pagamentos">R$ <?= ' '. number_format($totPagamentos, 2, ",", ".") ?></span>
                          </div>
                        </div>
                        <div class="col p-3" id="bloco-valor-saldo">
                          <div class="bloco-easy bl-easy-modVendas">
                            <label  class="form-group label-bl-fin">Saldo da Venda:</label>
                              <span  id="sp-valor-saldo" class="span-modVendas <?= ($saldo_venda <0)?'num-negativo':'num-negativo'?>">
                                R$ <?= $saldo_venda != 0 ? number_format($saldo_venda, 2,",", ".") : '0,00' ?>
                              </span>
                          </div>
                        </div>
                        <div class="col p-3" id="bloco-saldo-final">
                          <div class="bloco-easy bl-easy-modVendas">
                              <label  class="form-group label-bl-fin">Saldo Final Cliente:</label>
                              <span  id="sp-saldo-final" class="span-modVendas <?=($saldo_final>0)?'num-positivo':'num-negativo'?>">R$ <?= $saldo_final? number_format($saldo_final, 2, ",", ".") : '0,00'?> </span>                              
                              <div class="form-check" id="bl-chk-liberaSaldo" style="display:none; margin-top:-6px; margin-left:10px;">
                                  <input type="checkbox" class="form-check-input" style="width:12px; height:12px;"  id="chck-libera-saldo">
                                  <label class="form-check-label" style="font-size: 0.65em; margin-left:-8px; padding-top: 2px;" for="chck-libera-saldo">
                                    Liberar Saldo
                                  </label>
                              </div>
                          </div>
                        </div>
                      </div>
                      <!-- Bloco Principal -->
                      <div  class="col-auto" id="col-bloco-principal" style="margin-top:-20px; display:<?=($totValDesc!=0)?'block':'none'?>" >
                        <div class="p-3 bloco-easy" id="bloco-principal" style="border: 1px solid <?=$corMargem?>;">
                          <div class="row" >
                            <p><b>Preço Original: </b>
                              <s id="sp-valor-original">R$ <?= number_format($totValTabela, 2, ",", ".") ?></s>
                            </p>
                          
                          <p><b>Descontos: </b> 
                              <span id="sp-valor-desconto">R$ -<?= number_format($totValDesc, 2, ",", ".") ?> | 
                              -<?= number_format($totPercDesc, 2, ",", ".") ?>%</span>
                          </div>

                          <input type="hidden"   id="txt-valor-original" value="<?=$totValTabela?>" >
                          <input  type="hidden" id="txt-valor-desconto" value="<?=$totValDesc?>" >
                          <input  type="hidden" id="txt-valor-final" value="<?=$totValItens?>" >
                          <input  type="hidden" id="valor-original" value="<?=$totValTabela?>" >
                          <input  type="hidden" id="valor-desconto" value="<?=$totValDesc?>" >
                          <input  type="hidden" id="valor-final" value="<?=$totValItens?>" >
                          <input type="hidden" name="saldo-final" id="saldo-final" value="<?=$saldo_final?>">
                          <input type="hidden" name="saldo-venda" id="saldo-venda" value="<?=$saldo_venda?>">
                          <input type="hidden" name="total-pagamentos" id="total-pagamentos" value="<?=$totPagamentos?>">
                          <input  type="hidden" name="id-vendedor" id="id-vendedor" value="<?=$id_vendedor?>">
                          <input  type="hidden" id="custo-total" name="custo-total" value="<?= $CustoTotal ?>">
                          <p><?=$texto_validade?></p>
                        </div>
                      </div>
                      <!-- Bloco Avançado -->
                      <div class="col-12 col-md-6 col-avancada hidden-col">
                        <div class="p-3 bloco-easy" >
                          <p><b>LUCRO LÍQUIDO: </b> R$ <a id="valor-lucro-liquido"> <?= number_format($totLL, 2, ",", ".") ?></a></p>
                            <p><b>MARGEM: </b> 
                              <span style="color: <?=$corMargem?>;" id="percentual-margem"><?= number_format($totPercMa, 2, ",", ".") ?>%</span>
                            </p>
                            <p><b>CUSTO TOTAL: </b>
                              <span id="valor-custo-total">R$ <?= number_format($CustoTotal, 2, ",", ".") ?></span>
                            </p>
                            
                                <p style="text-indent: 30px;"><b>Custo Administrativo: </b>
                                  <span id="valor-custo-adm">R$ <?= number_format($totCuAdm, 2, ",", ".") ?> </span>
                                </p>
                                <p style="text-indent: 30px;"><b>Custo Operacional: </b>
                                  <span id="valor-custo-op">R$ <?= number_format($totCuOp, 2, ",", ".") ?> </span>
                                </p>
                                <p style="text-indent: 30px;"><b>Impostos: </b>
                                  <span id="valor-custo-imp">R$ <?= number_format($totImp, 2, ",", ".") ?> </span>
                                </p>
                                <p style="text-indent: 30px;"><b>Taxas de Cartão: </b>
                                  <span id="valor-custo-taxa">R$ <?= number_format($totTaxa, 2, ",", ".") ?> </span>
                                </p>
                          </div>
                        </div>
                      </div>
                  </div>

                  <!--navegação das abas -->
                  <ul style="cursor:pointer; height:30px;" class="nav nav-tabs" id="v-tab" role="tablist">
                    <li class="nav-link active tab-btn" id="itens-tab" data-bs-toggle="tab" data-bs-target="#aba-itens" role="tab" aria-controls="itens" aria-selected="true">
                      itens
                    </li>
                    <li hidden class="nav-link tab-btn listTable" id="pagamentos-tab" data-bs-toggle="tab" data-bs-target="#aba-pagamentos" role="tab" aria-controls="pagamentos" aria-selected="false">
                      pagamentos
                    </li>
                    <li class="nav-link tab-btn listTable" id="informacoes-tab" data-bs-toggle="tab" data-bs-target="#aba-informacoes" role="tab" aria-controls="informacoes" aria-selected="false">
                      Informações
                    </li>
                  </ul> 


                  <!--Contuúdo das TABS -->
                  <div class="tab-content"  id="v-tabContent">
                    
                  <!--TAB ITENS -->
                    <div class="tab-pane fade show active" id="aba-itens" role="tabpanel" aria-labelledby="itens-tab" style="padding:10px;">
                      <div class="table-responsive" style="overflow-x:auto;">
                        <table id="tabela-itensVenda" class="table table-striped">
                          <thead>
                            <tr>
                              <th <?= $bloquearCampos ? 'hidden' : '' ?>><button type="button" id="adicionar-item-modVenda" class="btn btn-success centBt">+</button></th>
                              <th style="display: none;">ID</th>
                              <th style= "min-width: 100px;">Tipo</th>
                              <th style= "min-width: 100px;">Item</th>
                              <th style="display:none;"></th>
                              <th style= "min-width: 100px;" class="col-avancada hidden-col">$ Original</th>
                              <th style= "min-width: 100px;">Preço</th>
                              <th style= "min-width:70px;">Un</th>
                              <th style= "min-width: 100px;">Total</th>
                              <th style= "min-width: 80px;">% Desc</th>
                              <th style= "min-width: 100px;" >Desconto</th>
                              <th style= "min-width: 100px;" class="col-avancada hidden-col" >Custo Operacional</th>
                              <th style= "min-width: 100px;" class="col-avancada hidden-col" >Custo ADM</th>
                              <th style= "min-width: 100px;" class="col-avancada hidden-col" >Imposto</th>
                              <th style= "min-width: 100px;" class="col-avancada hidden-col" >Taxa Cartão</th>
                              <th style= "min-width: 100px;" class="col-avancada hidden-col" >Lucro Bruto</th>
                              <th style= "min-width: 100px;" class="col-avancada hidden-col" >Lucro Líquido</th>
                              <th style= "min-width: 100px;" class="col-avancada hidden-col" >% Margem</th>
                            </tr>
                          </thead>
                          <tbody id="itens-body">

                            
                            <?php if (!empty($venda_itens)): ?>
                              <?php foreach ($venda_itens as $it): ?>
                                <tr>
                                  <!-- Botão de remover à esquerda -->
                                  <td <?= $bloquearCampos ? 'hidden' : '' ?>>
                                    <button  type="button" class="btn btn-danger remover-item centBt">-</button>
                                    
                                  </td>
                                
                                  <td style="display: none;">
                                    <input type="hidden" name="item_id[]" value="<?= $it['id'] ?>">
                                  </td>
                                  <td >
                                    <select name="tipo_item[]" class="tipo-item form-control input-bloqueado" readOnly>
                                      <option value="servico" <?= ($it['tipo_item'] == 'servico') ? 'selected' : '' ?>>Serviço</option>
                                      <option value="produto" <?= ($it['tipo_item'] == 'produto') ? 'selected' : '' ?>>Produto</option>
                                      <option value="cartao_presente" <?= ($it['tipo_item'] == 'cartao_presente') ? 'selected' : '' ?>>Cartão Presente</option>
                                    </select>
                                  </td>

                                  <td>
                                    <select <?= $bloquearCampos ? 'bloqueado' : '' ?>
                                      readOnly
                                      name="item[]"
                                      class="item-select form-control input-bloqueado"
                                      style="min-width:150px;"
                                      selected-data-item-id="<?= $it['id_item'] ?? '' ?>"
                                      selected-data-item-nome="<?= $it['item'] ?? '' ?>">
                                    </select>
                                  </td>
                                  <td style="display:none;">
                                    <input name="id_item[]"  type="text" class="id-item"  value="<?= $it['id_item'] ?? '' ?>">
                                  </td>

                                  <td class="col-avancada hidden-col"> <!--coluna oculta-->
                                    <input <?= $bloquearCampos ? 'bloqueado' : '' ?> 
                                    type="text" class="form-control preco-tabela blockItem" readOnly name="preco_tabela[]" value="<?= number_format($it['precoUn'], 2, ',', '.')?>">
                                  </td>
                                  <td>
                                    <input <?= $bloquearCampos ? 'bloqueado' : '' ?>
                                    type="text" class="form-control preco-cobrado numero-virgula-calc input-bloqueado" name="preco_cobrado[]" readOnly value="<?= number_format($it['precoUn_efetivo'], 2, ',', '.') ?>">
                                  </td>
                                  <td>
                                    <input <?= $bloquearCampos ? 'bloqueado' : '' ?>
                                    type="number" class="form-control quantidade input-bloqueado" min="1" step="1" readOnly name="quantidade[]" value="<?= $it['quantidade'] ?>">
                                  </td>
                                  <td>
                                    <input <?= $bloquearCampos ? 'bloqueado' : '' ?>
                                    type="text" class="form-control preco-total numero-virgula-calc input-bloqueado" readOnly name="preco_total[]" value="<?= number_format($it['preco_total'], 2, ',', '.') ?>">
                                  </td>
                                  <td>
                                    <input <?= $bloquearCampos ? 'bloqueado' : '' ?>
                                    type="text" name="perc_desc[]" style="border-color: <?=corMargem($it['margem']) ?>;" readOnly class="form-control perc-desc numero-virgula-calc porcento input-bloqueado"
                                          value="<?= number_format($it['perc_desconto'], 2, ',', '.') ?? '' ?>">
                                  </td>
                                  
                                  
                                  <td  >
                                    <input type="text" readOnly name="valor_desconto[]" class="form-control valor-desconto numero-virgula blockItem"
                                          value="<?= number_format($it['valor_desconto'], 2, ',', '.') ?? '' ?>">
                                  </td >


                                  <!-- Colunas Ocultas (margem, impostos, etc.) -->
                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="custo_operacional[]" class="form-control custo-operacional blockItem"
                                          value="<?= number_format($it['custo_1'], 2, ',', '.') ?? '' ?>">
                                  </td>

                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="custo_adm[]" class="form-control custo-adm blockItem"
                                          value="<?= number_format($it['custo_2'], 2, ',', '.') ?? '' ?>">
                                  </td>

                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="imposto[]" class="form-control imposto blockItem"
                                          value="<?= number_format($it['custo_3'], 2, ',', '.') ?? '' ?>">
                                  </td>

                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="taxa_cartao[]" class="form-control taxa-cartao blockItem"
                                          value="<?= number_format($it['custo_4'], 2, ',', '.') ?? '' ?>">
                                  </td>

                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="lucro_bruto[]" class="form-control lucro-bruto blockItem"
                                          value="<?= number_format($it['lucro_bruto'], 2, ',', '.') ?? '' ?>">
                                  </td>

                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="lucro_liquido[]" class="form-control lucro-liquido blockItem"
                                          value="<?=   number_format($it['lucro_liquido'], 2, ',', '.') ?? '' ?>">
                                  </td>

                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="margem[]" class="form-control margem numero-virgula blockItem"
                                          value="<?= number_format($it['margem'], 2, ',', '.') ?? '' ?>">
                                  </td >
                                  
                                </tr>
                              <?php endforeach; ?>
                            <?php endif; ?>
                            <tr style="background-color:#000; border-top: <?= $corMargem ?> solid 2px;" >
                                  
                                  <td <?= $bloquearCampos ? 'hidden' : '' ?>></td>
                                
                                  <td style="display: none;"></td>
                                  
                                  <td ></td>

                                  <td></td>

                                  <td class="col-avancada hidden-col"></td>
                                  
                                  <td></td>
                                  
                                  <td style="text-align: right; vertical-align: middle;"><b>TOTAIS</b></td>
                                  <td>
                                    <input 
                                    type="text" class="form-control total-preco-total totItens" name="valor-final" readonly name="total_preco_total" value="<?= number_format($totValItens, 2, ',', '.') ?>">
                                  </td>
                                  <td>
                                    <input 
                                    type="text" name="total_perc_desc" ReadOnly class="form-control total-perc-desc numero-virgula totItens"
                                          value="<?= number_format($totPercDesc, 2, ',', '.') ?? '' ?>">
                                  </td>
                                  
                                  
                                  <td  >
                                    <input type="text" readOnly name="total_valor_desconto" class="form-control total-valor-desconto numero-virgula totItens"
                                          value="<?= number_format($totValDesc, 2, ',', '.') ?? '' ?>">
                                  </td >


                                  <!-- Colunas Ocultas (margem, impostos, etc.) -->
                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="total_custo_operacional" class="form-control total-custo-operacional totItens"
                                          value="<?= number_format($totCuOp, 2, ',', '.') ?? '' ?>" >
                                  </td>
                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="total_custo_adm" class="form-control total-custo-adm totItens"
                                          value="<?= number_format($totCuAdm, 2, ',', '.') ?? '' ?>" >
                                  </td>
                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="total_imposto" class="form-control total-imposto totItens"
                                          value="<?= number_format($totImp, 2, ',', '.') ?? '' ?>" >
                                  </td>
                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="total_taxa_cartao" class="form-control total-taxa-cartao totItens"
                                          value="<?= number_format($totTaxa, 2, ',', '.') ?? '' ?>" >
                                  </td>
                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="total_lucro_bruto" class="form-control total-lucro-bruto totItens"
                                          value="<?= number_format($totLB, 2, ',', '.') ?? '' ?>" >
                                  </td>
                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="total_lucro_liquido" class="form-control total-lucro-liquido totItens"
                                          value="<?=   number_format($totLL, 2, ',', '.') ?? '' ?>" >
                                  </td>
                                  <td class="col-avancada hidden-col" >
                                    <input type="text" readOnly name="total_margem" class="form-control total-margem numero-virgula totItens"
                                          value="<?= number_format($totPercMa, 2, ',', '.') ?? '' ?>" >
                                  </td >
                                </tr>
                          </tbody>
                        </table>
                        
                      </div>
                        <!--</div>

                        Tab Pagamentos 
                        <div class="tab-pane fade show" id="aba-pagamentos" role="tabpanel" aria-labelledby="pagamentos-tab"> -->
                    
                        <table id="tabela-pagamentos" class="table table-striped">
                            <thead>
                              <tr>
                                <th <?= $bloquearCampos ? 'hidden' : '' ?>><button type="button" id="adicionar-pagamento"  class="btn btn-success centBt">+</button></th>
                                <th  style="display: none;">ID</th>
                                <th style="min-width: 200px;">Forma</th>
                                <th>Condição</th>
                                <th style="display: none;">id condi</th>
                                <th style= "width:fit-content;">Valor</th>
                                <th style="display: none;">qt parc</th>
                                <th>Parcela</th>
                                <th class="col-avancada hidden-col">Valor taxa</th>
                                <th class="col-avancada hidden-col">perc taxa</th>
                                <th style="display: none;">dias pag</th>
                                <th style="display: none;">c corrente</th>
                                <th style="display: none;">pago</th>
                                
                              </tr>
                            </thead>
                            <tbody id="pagamentos-body">
                              <?php if (!empty($venda_pagamentos)): ?>
                                <?php foreach ($venda_pagamentos as $pg): ?>
                                  <tr>
                                    <!-- Botão de remover à esquerda -->
                                    <td <?= $bloquearCampos ? 'hidden' : '' ?>>
                                      <button type="button" class="btn btn-danger remover-pagamento centBt">-</button>
                                    </td>

                                    <td style="display:none;">
                                      <input  name="pagamento_id[]" value="<?= $pg['id'] ?>">
                                    </td>
                                    
                                  
                                    <td  style="min-width: 200px;">
                                        <select <?= $bloquearCampos ? 'bloqueado' : '' ?> name="tipo_pagamento[]" class="tipo-pagamento form-select input-bloqueado">
                                        <?php
                                        $formaEncontrada = false;

                                        foreach ($tipos_pagamentos as $pgTipo):
                                          if ($pg['forma'] == $pgTipo['nome']) {
                                            $formaEncontrada = true;
                                          }
                                        ?>
                                          <option 
                                            value="<?= $pgTipo['nome'] ?>" 
                                            tipo-pg-id="<?= $pgTipo['id'] ?>" 
                                            <?= ($pg['forma'] == $pgTipo['nome']) ? 'selected' : '' ?>>
                                            <?= $pgTipo['nome'] ?>
                                          </option>
                                        <?php endforeach; ?>

                                        <?php if (!$formaEncontrada && !empty($pg['forma'])): ?>
                                          <option value="<?= $pg['forma'] ?>" selected><?= $pg['forma'] ?> (desatualizado)</option>
                                        <?php endif; ?>
                                        </select>
                                    </td>

                                    <td>
                                      <select 
                                      <?= $bloquearCampos ? 'bloqueado' : '' ?>
                                        name="pagamento[]"
                                        class="form-control forma-pagamento input-bloqueado"
                                        style="width:auto;"
                                        selected-id_forma-pagamento="<?= $pg['id'] ?? '' ?>"
                                        selected-nome_forma-pagamento="<?= $pg['condicao'] ?? '' ?>"
                                        selected-taxa-pagamento = "<?= $pg['perc_taxa'] ?? '' ?> "
                                        selected-dias-pagamento="<?= $pg['dias_pagamento'] ?? '' ?>"
                                        selected-conta-pagamento_id="<?= $pg['id_conta_corrente'] ?? '' ?>">
                                        <option><?= $pg['condicao'] ?></option>


                                      </select>
                                    </td>
                                    <td style="display: none;">
                                      <input type="text" <?= $bloquearCampos ? 'bloqueado' : '' ?> class="form-control id-pagamento" name="id_pagamento[]" value="<?= $pg['id_condicao'] ?>">
                                    </td>

                                    <td>
                                      <input type="text" <?= $bloquearCampos ? 'bloqueado' : '' ?> class="numero-virgula-calc form-control valor-pagamento input-bloqueado" readonly name="valor_pagamento[]" value="<?= number_format($pg['valor'], 2, ',', '.') ?>">
                                    </td>

                                    <td  style="display: none;">
                                      <input readonly type="text" <?= $bloquearCampos ? 'bloqueado' : '' ?> class="form-control qt-parcelas " name="qt_parcelas[]" value="<?= (int) preg_replace('/\D/', '', $pg['condicao'])?>">
                                    </td>

                                    <td>
                                    <input <?= $bloquearCampos ? 'bloqueado' : '' ?>  name="parcelas[]" type="text" class="parcela-pagamento form-control blockItem numero-virgula" readonly value="<?php
                                        $divisor = (int) preg_replace('/\D/', '', $pg['condicao']);
                                        echo $divisor > 0 ? number_format(($pg['valor'] / $divisor), 2, ',', '.') : '';
                                      ?>">
                                    </td>
                                    <td class="col-avancada hidden-col">
                                      <input readonly type="text" <?= $bloquearCampos ? 'bloqueado' : '' ?> class="numero-virgula form-control valor-taxa blockItem" name="valor_taxa[]" value="<?= number_format($pg['valor_taxa'], 2, ',', '.') ?>">

                                    </td>
                                    <td class="col-avancada hidden-col">
                                      <input readonly type="text" <?= $bloquearCampos ? 'bloqueado' : '' ?> class="numero-virgula form-control perc-taxa blockItem" name="perc_taxa[]" value="<?= number_format($pg['perc_taxa'], 2, ',', '.') ?>">

                                    </td>
                                    <td style="display: none;">
                                      <input readonly type="text" <?= $bloquearCampos ? 'bloqueado' : '' ?> class=" form-control dias-pagamento" name="dias_pagamento[]" value="<?=$pg['dias_pagamento']?>">
                                    </td>
                                    
                                    <td  style="display: none;">
                                      <input readonly type="text" <?= $bloquearCampos ? 'bloqueado' : '' ?> class="form-control id-conta-corrente" name="id_conta_corrente[]" value="<?=$pg['id_conta_corrente'] ?>">
                                    </td>
                                    <td  style="display: none;"n>
                                      <input readonly type="text" <?= $bloquearCampos ? 'bloqueado' : '' ?> class="form-control pago" name="pago[]" value="<?= $pg['pago'] ?>">
                                    </td>

                                  
                                  </tr>
                                <?php endforeach; ?>
                              <?php endif; ?>
                            </tbody>
                          </table>
                    
                    </div>

                  
                  


                  <!-- Tab Informações -->
                  <div class="tab-pane fade show" id="aba-informacoes" role="tabpanel" aria-labelledby="informacoes-tab">
                      <!-- Conteúdo para informações -->
                    <textarea class="form-control" name="informacoes" style = "height:200px;" ><?= $informacoes ?></textarea>
                  </div>

                
                
                </div> <!-- fechamnento do conteudo das abas -->


  
              
              
             
              </div>
            
            </div>

           

        
          </div> <!--fechamento do encapsulamento do conteudo da proposta menos os dados do cliente -->

            

        </div>  <!-- Fechameto do body do modal-->

        <div class="modal-footer" <?=($id>0)? 'hidden' : '' ?> id="footer" >
          
          <div class="footer-left">
            <div id="mensagem"></div>
          </div>
          
          <div>
            <button type="button" id="btn-fechar_venda" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="submit" id="btn-salvar_venda" class="btn btn-primary"><?=($id>0)? 'Atualizar ' : 'Gravar '  . $tipo_venda?></button>
          </div>

        </div>

      </form>
    </div>
  </div>
</div>



















<script>

$('#modalVenda').on('hidden.bs.modal', function () {
  $(this).remove(); // remove o HTML
});
</script>






<!-- Arrays de produtos e serviços -->
<script>

  // ARRAYS //
  var produtosArray = <?php echo json_encode($produtos); ?>;
  var servicosArray = <?php echo json_encode($servicos); ?>;
  var tiposPagamentosArray = <?php echo json_encode($tipos_pagamentos); ?>;
  var formasPagamentosArray = <?php echo json_encode($formas_pagamentos); ?>;
  var contasCorrentesArray = <?php echo json_encode($contas_correntes); ?>;
  var clientes    = (<?php echo json_encode($clientes); ?>);

  var impostoVenda = <?php echo json_encode($imposto_venda); ?>;
  var custoHora = <?php echo json_encode($custo_hora); ?>;
  var taxaCobrada = 0;

  var evInicia = true;

  
  //$var Variaveis dos totais
  function sanitizeNumber(value) {
    return Number(value) || 0;
  }

  var totValTabela    = sanitizeNumber(<?php echo json_encode($totValTabela); ?>);
  var totUnItens      = sanitizeNumber(<?php echo json_encode($totUnItens); ?>);
  var totValItens     = sanitizeNumber(<?php echo json_encode($totValItens); ?>);
  var totPercDesc     = sanitizeNumber(<?php echo json_encode($totPercDesc); ?>);
  var totValDesc      = sanitizeNumber(<?php echo json_encode($totValDesc); ?>);
  var totCuOp         = sanitizeNumber(<?php echo json_encode($totCuOp); ?>);
  var totCuAdm        = sanitizeNumber(<?php echo json_encode($totCuAdm); ?>);
  var totImp          = sanitizeNumber(<?php echo json_encode($totImp); ?>);
  var totTaxa         = sanitizeNumber(<?php echo json_encode($totTaxa); ?>);
  var totLB           = sanitizeNumber(<?php echo json_encode($totLB); ?>);
  var totLL           = sanitizeNumber(<?php echo json_encode($totLL); ?>);
  var totPercMa       = sanitizeNumber(<?php echo json_encode($totPercMa); ?>);
    

  var totPagamentos   = sanitizeNumber(<?php echo json_encode($totPagamentos); ?>);
  var totUnPagamentos   = sanitizeNumber(<?php echo json_encode($totUnPagamentos); ?>);
  var saldoVenda        = sanitizeNumber(<?php echo json_encode($saldo_venda); ?>);
  var saldoCliente    = sanitizeNumber(<?php echo json_encode($saldo_cliente); ?>);
  var saldoFinal    = sanitizeNumber(<?php echo json_encode($saldo_final); ?>);
  var totUnServ       = sanitizeNumber(<?php echo json_encode($totUnServ); ?>);
  var totValServ      = sanitizeNumber(<?php echo json_encode($totValServ); ?>);
  var totSessServ     = sanitizeNumber(<?php echo json_encode($totSessServ); ?>);
  var totUnProd       = sanitizeNumber(<?php echo json_encode($totUnProd); ?>);
  var totQtProd       = sanitizeNumber(<?php echo json_encode($totQtProd); ?>);
  var totValProd      = sanitizeNumber(<?php echo json_encode($totValProd); ?>);
  var totUnCartPres   = sanitizeNumber(<?php echo json_encode($totUnCartPres); ?>);
  var totValCartPres  = sanitizeNumber(<?php echo json_encode($totValCartPres); ?>);
  var taxaMedia       = sanitizeNumber(<?php echo json_encode($taxaMedia); ?>);
  var dataValidade   = <?php echo json_encode($validade); ?>;

  var data_venda = <?php echo json_encode($data_venda); ?>;

  var tipo_venda = <?php echo json_encode($tipo_venda); ?>;

  var novo = <?php echo json_encode($novo)?>;

  var selecionadoIndexV = -1;
  var resultadosFiltrados = [];

  var avancadoVisivel = false;

  var origemAgenda = <?php echo json_encode($origemAgenda);?>;

</script>

<?php
  // Caminho absoluto até o seu JS
  $path = __DIR__ . '/Modals/modalVendas.js';
  // Timestamp da última modificação
  $version = file_exists($path) ? filemtime($path) : time();
?>
<script src="Modals/modalVendas.js?v=<?= $version ?>" defer></script>