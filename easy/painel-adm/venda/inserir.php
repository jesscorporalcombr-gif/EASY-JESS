<?php
require_once("../../conexao.php");

// Inicie a sessão (caso ainda não esteja iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function converterParaDecimal($valorBR) {
    $valorSemPonto = str_replace('.', '', $valorBR);
    $valorDecimal = str_replace(',', '.', $valorSemPonto);
    return $valorDecimal;
}



    // Função auxiliar pra validar a data
function validarData($data, $formato) {
    $dt = DateTime::createFromFormat($formato, $data);
    return $dt && $dt->format($formato) === $data;
}

if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data_validade) && validarData($data_validade, 'd/m/Y')) {
    // Formato BR e data válida
    $dataFormatada = DateTime::createFromFormat('d/m/Y', $data_validade)->format($formatoFinal);
} elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_validade) && validarData($data_validade, 'Y-m-d')) {
    // Formato ISO e data válida
    $dataFormatada = $data_validade;
} else {
    // Data inválida
    $dataFormatada = null;
}






// Variáveis de sessão
$id_usuario    = $_SESSION['id_usuario'];    // user_criacao ou user_alteracao
$nome_usuario  = $_SESSION['nome_usuario'];  // id_user_criacao ou id_user_alteracao

// Recuperando dados do POST
$id_venda       = isset($_POST['id-venda']) ? $_POST['id-venda'] : null;
$tipo_venda     = isset($_POST['tipo-venda']) ? $_POST['tipo-venda'] : null;

if ($tipo_venda =='venda'){
    $venda = 1;
}else{
    $venda= 0;
}





if ($tipo_venda=='venda'){
    $data_venda = isset($_POST['data_venda']) ? $_POST['data_venda'] : null;
}else{
    $data_proposta = isset($_POST['data_venda']) ? $_POST['data_venda'] : null;
}


$id_cliente     = isset($_POST['id-cliente']) ? $_POST['id-cliente'] : null;
$cpf_cliente    = isset($_POST['cpf-cliente']) ? $_POST['cpf-cliente'] : null;
$nome_cliente   = isset($_POST['nome-cliente']) ? $_POST['nome-cliente'] : null;
$celular_cliente= isset($_POST['celular-cliente']) ? $_POST['celular-cliente'] : null;
$informacoes    = isset($_POST['informacoes']) ? $_POST['informacoes'] : null;
$email_cliente  = isset($_POST['email-cliente']) ? $_POST['email-cliente'] : null;
$valor_final    = isset($_POST['valor-final']) ? $_POST['valor-final'] : null;
$custo_total    = isset($_POST['custo-total']) ? $_POST['custo-total'] : null;
$saldo_venda    = isset($_POST['saldo-venda']) ? $_POST['saldo-venda'] : null;
$saldo_final    = isset($_POST['saldo-final']) ? $_POST['saldo-final'] : null; //NOVO
$total_pagamentos  = isset($_POST['total-pagamentos']) ? $_POST['total-pagamentos'] : null; //NOVO
$data_validade = isset($_POST['data-validade']) ? $_POST['data-validade'] : null;
$nome_vendedor  = isset($_POST['nome-vendedor']) ? $_POST['nome-vendedor'] : null;
$id_vendedor    = isset($_POST['id-vendedor']) ? $_POST['id-vendedor'] : null;




// 2.2) Definir os arrays que vêm do formulário
$array_item_id           = isset($_POST['item_id'])           ? $_POST['item_id']           : [];
$array_tipo_item         = isset($_POST['tipo_item'])         ? $_POST['tipo_item']         : [];
$array_item             = isset($_POST['item'])              ? $_POST['item']              : [];
$array_id_item           = isset($_POST['id_item'])           ? $_POST['id_item']           : [];
$array_preco_tabela      = isset($_POST['preco_tabela'])      ? $_POST['preco_tabela']      : [];
$array_preco_cobrado     = isset($_POST['preco_cobrado'])     ? $_POST['preco_cobrado']     : [];
$array_quantidade        = isset($_POST['quantidade'])        ? $_POST['quantidade']        : [];
$array_preco_total       = isset($_POST['preco_total'])       ? $_POST['preco_total']       : [];
$array_perc_desc         = isset($_POST['perc_desc'])         ? $_POST['perc_desc']         : [];
$array_valor_desconto    = isset($_POST['valor_desconto'])    ? $_POST['valor_desconto']    : [];
$array_custo_operacional = isset($_POST['custo_operacional']) ? $_POST['custo_operacional'] : [];
$array_custo_adm         = isset($_POST['custo_adm'])         ? $_POST['custo_adm']         : [];
$array_imposto           = isset($_POST['imposto'])           ? $_POST['imposto']           : [];
$array_taxa_cartao       = isset($_POST['taxa_cartao'])       ? $_POST['taxa_cartao']       : [];
$array_margem            = isset($_POST['margem'])            ? $_POST['margem']            : [];
$array_lucro_bruto       = isset($_POST['lucro_bruto'])       ? $_POST['lucro_bruto']       : [];
$array_lucro_liquido     = isset($_POST['lucro_liquido'])     ? $_POST['lucro_liquido']     : [];



// 3.2) Ler os arrays vindos do formulário
$array_pagamento_id      = $_POST['pagamento_id']      ?? [];
$array_tipo_pagamento    = $_POST['tipo_pagamento']    ?? [];
$array_pagamento         = $_POST['pagamento']         ?? [];
$array_id_pagamento      = $_POST['id_pagamento']      ?? [];
$array_qt_parcelas       = $_POST['qt_parcelas']       ?? [];
$array_valor_pagamento   = $_POST['valor_pagamento']   ?? [];
$array_valor_taxa        = $_POST['valor_taxa']        ?? [];
$array_perc_taxa         = $_POST['perc_taxa']         ?? [];
$array_dias_pagamento    = $_POST['dias_pagamento']    ?? [];
$array_id_conta_corrente = $_POST['id_conta_corrente'] ?? [];




$total_preco_tabela = $total_val_desconto=0;
$total_custo_1 = $total_custo_2 = $total_custo_3 = $total_custo_4 = 0;
$total_lucro_bruto = $total_lucro_liquido= 0;


// --- 2) Soma cada tipo de custo


foreach ($array_preco_tabela as $i => $preco) {
    // pega a quantidade no mesmo índice (ou zero se não existir)
    $quantidade = isset($array_quantidade[$i])
        ? $array_quantidade[$i]
        : 0;

    // converte e acumula preço × quantidade
    $total_preco_tabela += converterParaDecimal($preco)
                         * $quantidade;
}





foreach ($array_valor_desconto as $v) { $total_val_desconto += converterParaDecimal($v); }
foreach ($array_custo_operacional    as $v) { $total_custo_1 += converterParaDecimal($v); }
foreach ($array_custo_adm            as $v) { $total_custo_2 += converterParaDecimal($v); }
foreach ($array_imposto              as $v) { $total_custo_3 += converterParaDecimal($v); }
foreach ($array_taxa_cartao          as $v) { $total_custo_4 += converterParaDecimal($v); }
foreach ($array_lucro_liquido        as $v) { $total_lucro_bruto += converterParaDecimal($v); }
foreach ($array_lucro_bruto          as $v) { $total_lucro_liquido += converterParaDecimal($v); }

// Se existirem outros arrays de custo, repita o padrão para custo_5..8
if (!empty($valor_final)) {
    $valor_final = converterParaDecimal($valor_final);
}

if (!empty($custo_total)) {
    $custo_total = converterParaDecimal($custo_total);
}
// - 3) Define variáveis que vao para a tabela venda
$custo_1       = $total_custo_1;
$custo_2       = $total_custo_2;
$custo_3       = $total_custo_3;
$custo_4       = $total_custo_4;
$lucro_bruto = $total_lucro_bruto;
$lucro_liquido = $total_lucro_liquido;
$preco_tabela = $total_preco_tabela;
$valor_desconto = $total_val_desconto;

$custo_5 = $custo_6 = $custo_7 = $custo_8 = 0;

$custo_total  = $custo_1 + $custo_2 + $custo_3 + $custo_4
               + $custo_5 + $custo_6 + $custo_7 + $custo_8;

// -- 5) Lucros e margem

if ($valor_final!=0){
$margem        = $valor_final > 0
                 ? ($lucro_bruto / $valor_final) * 100
                 : 0;


$perc_desconto = ($valor_desconto / $preco_tabela) * 100;
}else{

    $margem = 0;
    $perc_desconto = 0;
}



$quitado_total=false;
$formatoFinal = 'Y-m-d';
$dataFormatada = null;
$data_validade = $dataFormatada;
$agora = date('Y-m-d H:i:s');





if($venda==1){
$url  = 'https://easyclinicas.com.br/easy/painel-adm/endPoints/consultar_saldo_cliente.php'
      . '?id_cliente=' . urlencode($id_cliente)
      . '&id_venda='   . urlencode($id_venda);
      //. '&id_venda='   . urlencode($data_venda);
  
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$json = curl_exec($ch);
$err  = curl_error($ch);
curl_close($ch);

if ($json === false || empty($json)) {
    throw new Exception("Não foi possível consultar o saldo do cliente: {$err}");
}

$data = json_decode($json, true);
if (isset($data['error'])) {
    throw new Exception("Erro na consulta de saldo: " . $data['error']);
}

$saldo_cliente = (float)$data['saldo'] + (float)$saldo_venda;

}


//echo 'o saldo do cliente é '. $saldo_cliente;

// Verifica se é INSERT ou UPDATE
if (empty($id_venda) || $id_venda < 1) {
    // *** INSERT ***
    $sql = "INSERT INTO venda 
                (tipo_venda, 
                 id_cliente, 
                 cpf, 
                 cliente, 
                 celular, 
                 email,
                 data_venda,
                 data_proposta, 
                 informacoes,
                 valor_final, 
                 custo_total,
                 vendedor_nome,
                 vendedor_user_id,
                 validade,
                 dataHora_criacao, 
                 id_user_criacao, 
                 user_criacao,
                 saldo,
                 total_pagamentos,
                 quitado_total,
                 custo_1,
                 custo_2,
                 custo_3,
                 custo_4,
                 margem,
                 lucro_liquido,
                 lucro_bruto,
                 valor_tabela,
                 perc_desconto,
                 valor_desconto
                 -- demais colunas que não serão preenchidas permanecem com valor padrão
                 ) 
            VALUES 
                (:tipo_venda, 
                 :id_cliente, 
                 :cpf, 
                 :cliente, 
                 :celular, 
                 :email, 
                 :data_venda,
                 :data_proposta,
                 :informacoes,
                 :valor_final,
                 :custo_total,
                 :vendedor_nome,
                 :vendedor_user_id,
                 :validade, 
                 :data_criacao, 
                 :id_user_criacao, 
                 :user_criacao,
                 :saldo,
                 :total_pagamentos,
                 :quitado_total,
                 :custo_1,
                 :custo_2,
                 :custo_3,
                 :custo_4,
                 :margem,
                 :lucro_liquido,
                 :lucro_bruto,
                 :valor_tabela,
                 :perc_desconto,
                 :valor_desconto
                )";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':tipo_venda',      $tipo_venda);
    $stmt->bindValue(':id_cliente',      $id_cliente);
    $stmt->bindValue(':cpf',             $cpf_cliente);
    $stmt->bindValue(':cliente',         $nome_cliente);
    $stmt->bindValue(':celular',         $celular_cliente);
    $stmt->bindValue(':informacoes',     $informacoes);
    $stmt->bindValue(':email',           $email_cliente);
    $stmt->bindValue(':data_venda',      $data_venda);
    $stmt->bindValue(':data_proposta',   $data_proposta);
    $stmt->bindValue(':valor_final',     $valor_final);
    $stmt->bindValue(':custo_total',     $custo_total);
    $stmt->bindValue(':vendedor_nome',   $nome_vendedor);
    $stmt->bindValue(':vendedor_user_id',$id_vendedor);
    $stmt->bindValue(':validade',        $data_validade);
    $stmt->bindValue(':data_criacao',    $agora);
    $stmt->bindValue(':id_user_criacao', $id_usuario); // conforme pedido
    $stmt->bindValue(':user_criacao',    $nome_usuario);   // conforme pedido
    $stmt->bindValue(':saldo',          $saldo_venda); 
    $stmt->bindValue(':total_pagamentos', $total_pagamentos); 
    $stmt->bindValue(':quitado_total', $quitado_total);

    $stmt->bindValue(':custo_1', $custo_1);
    $stmt->bindValue(':custo_2', $custo_2);
    $stmt->bindValue(':custo_3', $custo_3);
    $stmt->bindValue(':custo_4', $custo_4);
    $stmt->bindValue(':margem', $margem);
    $stmt->bindValue(':lucro_liquido', $lucro_liquido);
    $stmt->bindValue(':lucro_bruto', $lucro_bruto);

    $stmt->bindValue(':valor_tabela', $preco_tabela);
    $stmt->bindValue(':perc_desconto', $perc_desconto);
    $stmt->bindValue(':valor_desconto', $valor_desconto);

    try {
        $stmt->execute();
        
        $errorInfo = $stmt->errorInfo();
            if ($errorInfo[0] !== '00000') {
                echo "Erro (INSERT Venda dados): ";
                print_r($errorInfo);
            } else {
               $id_venda= $pdo->lastInsertId() ;
            }


       // echo "Salvo com Sucesso!";
    } catch (Exception $e) {
        echo "Erro ao Inserir em Vendas: " . $e->getMessage();
    }


} else {


 //recuperando o saldo da venda antes de atualizar
   // $stmtVend = $pdo->prepare("
    //    SELECT saldo
     //   FROM venda
      //  WHERE id = :id_venda
       // FOR UPDATE
    //");
   // $stmtVend->execute([':id_venda' => $id_venda]);
   // $saldo_venda_antigo = (float) $stmtVend->fetchColumn();

    // Subtrai do saldo do cliente o valor que já estava lançado para esta venda
    //$saldo_cliente -= $saldo_venda_antigo;

    // *** UPDATE ***
    $sql = "UPDATE venda SET tipo_venda   = :tipo_venda,
                            id_cliente   = :id_cliente,
                            cpf          = :cpf,
                            cliente      = :cliente,
                            celular      = :celular,
                            informacoes  = :informacoes,
                            email        = :email,
                            data_venda   = :data_venda,
                            data_proposta   = :data_proposta,
                            valor_final  = :valor_final,
                            custo_total  = :custo_total,
                            vendedor_nome = :vendedor_nome,
                            vendedor_user_id = :vendedor_user_id,
                            validade = :validade,
                            dataHora_alteracao = :data_alteracao,
                            id_user_alteracao  = :id_user_alteracao,
                            user_alteracao     = :user_alteracao,
                            saldo              = :saldo,
                            total_pagamentos    = :total_pagamentos,
                            quitado_total = :quitado_total,
                            custo_1 = :custo_1,
                            custo_2 = :custo_2,
                            custo_3 = :custo_3,
                            custo_4 = :custo_4,
                            margem = :margem,
                            lucro_liquido = :lucro_liquido,
                            lucro_bruto = :lucro_bruto,
                            valor_tabela = :valor_tabela,
                            perc_desconto = :perc_desconto,
                            valor_desconto = :valor_desconto



            WHERE id = :id_venda";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':tipo_venda',       $tipo_venda);
    $stmt->bindValue(':id_cliente',       $id_cliente);
    $stmt->bindValue(':cpf',              $cpf_cliente);
    $stmt->bindValue(':cliente',          $nome_cliente);
    $stmt->bindValue(':celular',          $celular_cliente);
    $stmt->bindValue(':informacoes',      $informacoes);
    $stmt->bindValue(':email',            $email_cliente);
    $stmt->bindValue(':data_venda',            $data_venda);
    $stmt->bindValue(':data_proposta',            $data_proposta);
    $stmt->bindValue(':valor_final',      $valor_final);
    $stmt->bindValue(':custo_total',      $custo_total);
    $stmt->bindValue(':vendedor_nome',   $nome_vendedor);
    $stmt->bindValue(':vendedor_user_id',$id_vendedor);
    $stmt->bindValue(':validade',        $data_validade);
    $stmt->bindValue(':data_alteracao',   $agora);
    $stmt->bindValue(':id_user_alteracao',$id_usuario); // conforme pedido
    $stmt->bindValue(':user_alteracao',   $nome_usuario);   // conforme pedido
    $stmt->bindValue(':id_venda',         $id_venda);
    $stmt->bindValue(':saldo',         $saldo_venda);
    $stmt->bindValue(':total_pagamentos',         $total_pagamentos);
    $stmt->bindValue(':quitado_total', $quitado_total);
    
    $stmt->bindValue(':custo_1', $custo_1);
    $stmt->bindValue(':custo_2', $custo_2);
    $stmt->bindValue(':custo_3', $custo_3);
    $stmt->bindValue(':custo_4', $custo_4);
    $stmt->bindValue(':margem', $margem);
    $stmt->bindValue(':lucro_liquido', $lucro_liquido);
    $stmt->bindValue(':lucro_bruto', $lucro_bruto);
    $stmt->bindValue(':valor_tabela', $preco_tabela);
    $stmt->bindValue(':perc_desconto', $perc_desconto);
    $stmt->bindValue(':valor_desconto', $valor_desconto);

    try {
        $stmt->execute();
        $errorInfo = $stmt->errorInfo();
            if ($errorInfo[0] !== '00000') {
                echo "Erro (UPDATE venda dados): ";
                print_r($errorInfo);
            } else {
               // echo "update venda dados OK. Ultimo ID: " . $pdo->lastInsertId() . "<br>";
            }


       // echo "Salvo com Sucesso!";
    } catch (Exception $e) {
        echo "Erro ao atualizar Vendas: " . $e->getMessage();
    }
}


if($venda==1){
//GRAVANDO O NOVO SALDO DO CLIENTE
$stmtUpdateCli = $pdo->prepare("
    UPDATE clientes
       SET saldo = :novo_saldo
     WHERE id    = :id_cliente
");
$stmtUpdateCli->execute([
    ':novo_saldo' => $saldo_cliente,
    ':id_cliente' => $id_cliente,
]);
}

















/************************************************************
* 2) SEGUNDA ETAPA: TABELA 'venda_itens'
************************************************************/

// 2.1) Buscar itens existentes no banco para este $id_venda
$sqlItens = "SELECT id FROM venda_itens WHERE id_venda = :id_venda";
$stmtItens = $pdo->prepare($sqlItens);
$stmtItens->bindValue(':id_venda', $id_venda);
$stmtItens->execute();
$itensNoBanco = $stmtItens->fetchAll(PDO::FETCH_COLUMN); 
// $itensNoBanco será um array com todos os IDs (coluna 'id') que
// já existem em venda_itens para este id_venda.








// 2.3) Percorrer cada índice do array e decidir se é INSERT ou UPDATE



foreach ($array_item_id as $index => $id_item_bd) {
    
    // Capturar valores de cada campo (no mesmo índice)
    $tipo_item    = isset($array_tipo_item[$index])   ? $array_tipo_item[$index]   : '';
    
    $nome_item    = isset($array_item[$index])        ? $array_item[$index]        : '';
    $id_item      = isset($array_id_item[$index])     ? $array_id_item[$index]     : '';
    $preco_tabela = isset($array_preco_tabela[$index])? $array_preco_tabela[$index]: '';
    $preco_cob    = isset($array_preco_cobrado[$index])? $array_preco_cobrado[$index]: '';

    
    
    $qtde         = isset($array_quantidade[$index])  ? $array_quantidade[$index]  : '';
    $preco_tot    = isset($array_preco_total[$index]) ? $array_preco_total[$index] : '';
    $perc_desc    = isset($array_perc_desc[$index])   ? $array_perc_desc[$index]   : '';
    $valor_desc   = isset($array_valor_desconto[$index]) ? $array_valor_desconto[$index] : '';
    $custo_op     = isset($array_custo_operacional[$index])? $array_custo_operacional[$index] : '';
    $custo_adm    = isset($array_custo_adm[$index])   ? $array_custo_adm[$index]   : '';
    $imposto      = isset($array_imposto[$index])     ? $array_imposto[$index]     : '';
    $taxa_cartao  = isset($array_taxa_cartao[$index]) ? $array_taxa_cartao[$index] : '';
    $margem       = isset($array_margem[$index])      ? $array_margem[$index]      : '';
    $lucro_bruto  = isset($array_lucro_bruto[$index]) ? $array_lucro_bruto[$index] : '';
    $lucro_liq    = isset($array_lucro_liquido[$index]) ? $array_lucro_liquido[$index] : '';

    // Converter todos os campos decimais
    $preco_tabela = converterParaDecimal($preco_tabela);
    $preco_cob    = converterParaDecimal($preco_cob);
    $preco_tot    = converterParaDecimal($preco_tot);
    $perc_desc    = converterParaDecimal($perc_desc);
    $valor_desc   = converterParaDecimal($valor_desc);
    $custo_op     = converterParaDecimal($custo_op);
    $custo_adm    = converterParaDecimal($custo_adm);
    $imposto      = converterParaDecimal($imposto);
    $taxa_cartao  = converterParaDecimal($taxa_cartao);
    $margem       = converterParaDecimal($margem);
    $lucro_bruto  = converterParaDecimal($lucro_bruto);
    $lucro_liq    = converterParaDecimal($lucro_liq);
    
    // Somar os 4 custos
    $custo_total = $custo_op + $custo_adm + $imposto + $taxa_cartao;
    
   
    // Se não existe item_id (ou é 0), significa que é NOVO -> INSERT
    if (empty($id_item_bd) || $id_item_bd == 0) {
        
        
        
        $sqlInsert = "INSERT INTO venda_itens 
            (id_venda, data_venda, tipo_item, item, id_item, 
             precoUn, precoUn_efetivo, quantidade, preco_total, 
             perc_desconto, valor_desconto,
             custo_1, custo_2, custo_3, custo_4, custo_total, 
             margem, lucro_bruto, lucro_liquido, venda, id_cliente)
        VALUES
            (:id_venda, :data_venda, :tipo_item, :nome_item, :id_item,
             :preco_tabela, :preco_cob, :qtde, :preco_tot,
             :perc_desc, :valor_desc,
             :custo_op, :custo_adm, :imposto, :taxa_cartao, :custo_total, 
             :margem, :lucro_bruto, :lucro_liq, :venda, :id_cliente)";

             

        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->bindValue(':id_venda',       $id_venda);
        $stmtInsert->bindValue(':data_venda',     $data_venda);
        $stmtInsert->bindValue(':tipo_item',      $tipo_item);
        $stmtInsert->bindValue(':nome_item',      $nome_item);
        $stmtInsert->bindValue(':id_item',        $id_item);
        $stmtInsert->bindValue(':preco_tabela',   $preco_tabela);
        $stmtInsert->bindValue(':preco_cob',      $preco_cob);
        $stmtInsert->bindValue(':qtde',           $qtde);
        $stmtInsert->bindValue(':preco_tot',      $preco_tot);
        $stmtInsert->bindValue(':perc_desc',      $perc_desc);
        $stmtInsert->bindValue(':valor_desc',     $valor_desc);
        $stmtInsert->bindValue(':custo_op',       $custo_op);
        $stmtInsert->bindValue(':custo_adm',      $custo_adm);
        $stmtInsert->bindValue(':imposto',        $imposto);
        $stmtInsert->bindValue(':taxa_cartao',    $taxa_cartao);
        $stmtInsert->bindValue(':custo_total',    $custo_total);
        $stmtInsert->bindValue(':margem',         $margem);
        $stmtInsert->bindValue(':lucro_bruto',    $lucro_bruto);
        $stmtInsert->bindValue(':lucro_liq',      $lucro_liq);
        $stmtInsert->bindValue(':venda',      $venda);
        $stmtInsert->bindValue(':id_cliente',      $id_cliente);

      //  $stmtInsert->execute();
    
        try {
            $stmtInsert->execute();
            $errorInfo = $stmtInsert->errorInfo();
                if ($errorInfo[0] !== '00000') {
                    echo "Erro (INSERT venda_itens): ";
                    print_r($errorInfo);
                } else {
                    //echo "Insert em venda_itens OK. Ultimo ID: " . $pdo->lastInsertId() . "<br>";
                }


           // echo "Salvo com Sucesso!";
        } catch (Exception $e) {
            echo "Erro ao Inserir em Itens: " . $e->getMessage();
        }

        

    } else {
        // Caso contrário, é um item já existente -> UPDATE
        // Remover este ID do array $itensNoBanco, pois já vamos tratá-lo
        if (($key = array_search($id_item_bd, $itensNoBanco)) !== false) {
            unset($itensNoBanco[$key]);
        }
        
        $sqlUpdate = "UPDATE venda_itens
                        SET data_venda        = :data_venda,
                            tipo_item         = :tipo_item,
                            item              = :nome_item,
                            id_item           = :id_item,
                            precoUn           = :preco_tabela,
                            precoUn_efetivo   = :preco_cob,
                            quantidade        = :qtde,
                            preco_total       = :preco_tot,
                            perc_desconto     = :perc_desc,
                            valor_desconto    = :valor_desc,
                            custo_1           = :custo_op,
                            custo_2           = :custo_adm,
                            custo_3           = :imposto,
                            custo_4           = :taxa_cartao,
                            custo_total       = :custo_total,
                            margem            = :margem,
                            lucro_bruto       = :lucro_bruto,
                            lucro_liquido     = :lucro_liq,
                            venda             = :venda,
                            id_cliente        = :id_cliente

                       WHERE id = :id_item_bd";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindValue(':data_venda',       $data_venda);
        $stmtUpdate->bindValue(':tipo_item',        $tipo_item);
        $stmtUpdate->bindValue(':nome_item',        $nome_item);
        $stmtUpdate->bindValue(':id_item',          $id_item);
        $stmtUpdate->bindValue(':preco_tabela',     $preco_tabela);
        $stmtUpdate->bindValue(':preco_cob',        $preco_cob);
        $stmtUpdate->bindValue(':qtde',             $qtde);
        $stmtUpdate->bindValue(':preco_tot',        $preco_tot);
        $stmtUpdate->bindValue(':perc_desc',        $perc_desc);
        $stmtUpdate->bindValue(':valor_desc',       $valor_desc);
        $stmtUpdate->bindValue(':custo_op',         $custo_op);
        $stmtUpdate->bindValue(':custo_adm',        $custo_adm);
        $stmtUpdate->bindValue(':imposto',          $imposto);
        $stmtUpdate->bindValue(':taxa_cartao',      $taxa_cartao);
        $stmtUpdate->bindValue(':custo_total',      $custo_total);
        $stmtUpdate->bindValue(':margem',           $margem);
        $stmtUpdate->bindValue(':lucro_bruto',      $lucro_bruto);
        $stmtUpdate->bindValue(':lucro_liq',        $lucro_liq);
        $stmtUpdate->bindValue(':id_item_bd',       $id_item_bd);
        $stmtUpdate->bindValue(':venda',            $venda);
         $stmtUpdate->bindValue(':id_cliente',      $id_cliente);
        //$stmtUpdate->execute();
        try {
            $stmtUpdate->execute();
            $errorInfo = $stmtUpdate->errorInfo();
                if ($errorInfo[0] !== '00000') {
                    echo "Erro (UPDATE venda_itens): ";
                    print_r($errorInfo);
                } else {
                   // echo "update em venda_itens OK. Ultimo ID: " . $pdo->lastInsertId() . "<br>";
                }


            //echo "Salvo com Sucesso!";
        } catch (Exception $e) {
            echo "Erro ao atualizar Itens: " . $e->getMessage();
        }

    }
}

// 2.4) Agora, $itensNoBanco contém IDs que não foram enviados no formulário,
//      portanto precisamos deletá-los (foram removidos na interface).
if (!empty($itensNoBanco)) {
    // Excluir todos os IDs restantes
   
    $idsParaExcluir = implode(',', $itensNoBanco);
    $sqlDelete = "DELETE FROM venda_itens WHERE id IN ($idsParaExcluir)";
    $pdo->exec($sqlDelete);
}


/************************************************************
 * 3) TERCEIRA ETAPA: TABELA 'venda_pagamentos'
 ************************************************************/

// 3.1) Buscar registros existentes no banco para este $id_venda
$sqlPag = "SELECT id FROM venda_pagamentos WHERE id_venda = :id_venda";
$stmtPag = $pdo->prepare($sqlPag);
$stmtPag->bindValue(':id_venda', $id_venda);
$stmtPag->execute();
$pagamentosNoBanco = $stmtPag->fetchAll(PDO::FETCH_COLUMN);
// $pagamentosNoBanco agora é um array com todos os 'id' existentes em venda_pagamentos
// para esse id_venda.



// Se você já tem uma função global converterParaDecimal($valorBR), pode usar aqui

// 3.3) Percorrer cada índice e decidir INSERT ou UPDATE
foreach ($array_pagamento_id as $index => $id_pag_bd) {
    // Capturar valores correspondentes no mesmo índice
    $forma             = isset($array_tipo_pagamento[$index])    ? $array_tipo_pagamento[$index]    : '';
    $condicao          = isset($array_pagamento[$index])         ? $array_pagamento[$index]         : '';
    $id_condicao       = isset($array_id_pagamento[$index])      ? $array_id_pagamento[$index]      : '';
    $qtd_parcelas      = isset($array_qt_parcelas[$index])       ? $array_qt_parcelas[$index]       : '';
    $valor             = isset($array_valor_pagamento[$index])   ? $array_valor_pagamento[$index]   : '';
    $valor_taxa        = isset($array_valor_taxa[$index])        ? $array_valor_taxa[$index]        : '';
    $perc_taxa         = isset($array_perc_taxa[$index])         ? $array_perc_taxa[$index]         : '';
    $dias_pag          = isset($array_dias_pagamento[$index])    ? $array_dias_pagamento[$index]    : '';
    $id_conta_corrente = isset($array_id_conta_corrente[$index]) ? $array_id_conta_corrente[$index] : '';

    // Converter os campos decimais
    $valor      = converterParaDecimal($valor);
    $valor_taxa = converterParaDecimal($valor_taxa);
    $perc_taxa  = converterParaDecimal($perc_taxa);

    // Se pagamento_id estiver vazio => novo pagamento => INSERT
    if (empty($id_pag_bd) || $id_pag_bd == 0) {
        $sqlInsertPag = "INSERT INTO venda_pagamentos 
            (id_venda, venda, data_venda, forma, condicao, id_condicao,
             qtd_parcelas, valor, valor_taxa, perc_taxa,
             dias_pagamento, id_cliente, id_conta_corrente)
         VALUES
            (:id_venda, :venda, :data_venda, :forma, :condicao, :id_condicao,
             :qtd_parcelas, :valor, :valor_taxa, :perc_taxa,
             :dias_pagamento, :id_cliente, :id_conta_corrente)";
        
        $stmtInsertPag = $pdo->prepare($sqlInsertPag);
        $stmtInsertPag->bindValue(':id_venda',         $id_venda);
        $stmtInsertPag->bindValue(':venda',            $venda);
        $stmtInsertPag->bindValue(':data_venda',       $data_venda);
        $stmtInsertPag->bindValue(':forma',            $forma);
        $stmtInsertPag->bindValue(':condicao',         $condicao);
        $stmtInsertPag->bindValue(':id_condicao',      $id_condicao);
        $stmtInsertPag->bindValue(':qtd_parcelas',     $qtd_parcelas);
        $stmtInsertPag->bindValue(':valor',            $valor);
        $stmtInsertPag->bindValue(':valor_taxa',       $valor_taxa);
        $stmtInsertPag->bindValue(':perc_taxa',        $perc_taxa);
        $stmtInsertPag->bindValue(':dias_pagamento',   $dias_pag);
        $stmtInsertPag->bindValue(':id_cliente',        $id_cliente);
        $stmtInsertPag->bindValue(':id_conta_corrente',$id_conta_corrente);
     
        //$stmtInsertPag->execute();
        try {
            $stmtInsertPag->execute();
            $errorInfo =  $stmtInsertPag->errorInfo();
                if ($errorInfo[0] !== '00000') {
                    echo "Erro (INSERT venda_pagamentos): ";
                    print_r($errorInfo);
                } else {
                    //echo "insert em venda_pagamentos OK. Ultimo ID: " . $pdo->lastInsertId() . "<br>";
                }


            //echo "Salvo com Sucesso!";
        } catch (Exception $e) {
            echo "Erro ao inserir pagamentos: " . $e->getMessage();
        }
        
    } else {
        // Já existe => UPDATE
        // Retirar este ID do array $pagamentosNoBanco (pois será atualizado)
        if (($key = array_search($id_pag_bd, $pagamentosNoBanco)) !== false) {
            unset($pagamentosNoBanco[$key]);
        }

        $sqlUpdatePag = "UPDATE venda_pagamentos
                            SET venda           = :venda,
                                data_venda       = :data_venda,
                                forma            = :forma,
                                condicao         = :condicao,
                                id_condicao      = :id_condicao,
                                qtd_parcelas     = :qtd_parcelas,
                                valor            = :valor,
                                valor_taxa       = :valor_taxa,
                                perc_taxa        = :perc_taxa,
                                dias_pagamento   = :dias_pagamento,
                                id_cliente       = :id_cliente,
                                id_conta_corrente= :id_conta_corrente
                          WHERE id = :id_pag_bd";


        $stmtUpdatePag = $pdo->prepare($sqlUpdatePag);
        $stmtUpdatePag->bindValue(':venda',            $venda);
        $stmtUpdatePag->bindValue(':data_venda',       $data_venda);
        $stmtUpdatePag->bindValue(':forma',            $forma);
        $stmtUpdatePag->bindValue(':condicao',         $condicao);
        $stmtUpdatePag->bindValue(':id_condicao',      $id_condicao);
        $stmtUpdatePag->bindValue(':qtd_parcelas',     $qtd_parcelas);
        $stmtUpdatePag->bindValue(':valor',            $valor);
        $stmtUpdatePag->bindValue(':valor_taxa',       $valor_taxa);
        $stmtUpdatePag->bindValue(':perc_taxa',        $perc_taxa);
        $stmtUpdatePag->bindValue(':dias_pagamento',   $dias_pag);
        $stmtUpdatePag->bindValue(':id_cliente',        $id_cliente);
        $stmtUpdatePag->bindValue(':id_conta_corrente',$id_conta_corrente);
        $stmtUpdatePag->bindValue(':id_pag_bd',        $id_pag_bd);
        

        //$stmtUpdatePag->execute();
        try {
            $stmtUpdatePag->execute();
            $errorInfo = $stmtUpdatePag->errorInfo();
                if ($errorInfo[0] !== '00000') {
                    echo "Erro (UPDATE venda_pagamentos): ";
                    print_r($errorInfo);
                } else {
                  //  echo "update em venda_pagamentos OK. Ultimo ID: " . $pdo->lastInsertId() . "<br>";
                }


            //echo "Salvo com Sucesso!";
        } catch (Exception $e) {
            echo "Erro ao atualizar pagamentos: " . $e->getMessage();
        }


    }
}




// 3.4) Excluir pagamentos que sobraram no array $pagamentosNoBanco (pois não vieram no form)
if (!empty($pagamentosNoBanco)) {
    $idsParaExcluir = implode(',', $pagamentosNoBanco);
    $sqlDeletePag = "DELETE FROM venda_pagamentos WHERE id IN ($idsParaExcluir)";
    $pdo->exec($sqlDeletePag);
}













$response = [
    'status' => 'success',
    'mensagem' => 'Salvo com Sucesso!',
    'id_venda' => $id_venda, // Importante para você manipular no front
    'outro_dado' => 'qualquer_coisa',
    'data_validade' => $data_validade,
   // 'total_itens' => count($array_item_id),
    // Adicione quantos precisar
];

// Devolve JSON pro front
header('Content-Type: application/json');
echo json_encode($response);







?>
