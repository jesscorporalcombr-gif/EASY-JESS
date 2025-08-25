<?php
/**
 * Endpoint para gravação de lançamentos financeiros e recorrências
 * Aceita tanto JSON (fetch com application/json) quanto multipart/form-data
 */
require_once(__DIR__ . "/../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');


$contasAReceber = 'CONTAS A RECEBER - Lançamentos';
$id_contasAReceber = '22';

$contasAPagar = 'CONTAS A PAGAR - Lançanebtos';
$id_contasAPagar = '15';




try {
    $pdo->beginTransaction();

    // 1) Recebe dados via JSON ou form-data
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        $form = $data['form'] ?? [];
        $recurrences = $data['recurrences'] ?? [];
    } else {
        $form = $_POST;
        $recurrences = [];
        if (!empty($_POST['recurrences'])) {
            $recurrences = json_decode($_POST['recurrences'], true);
        }
    }

    // 2) Upload de arquivo (multipart/form-data)
    $arquivoPath = null;
    if (!empty($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/financeiro/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $tmp  = $_FILES['arquivo']['tmp_name'];
        $name = time() . '_' . basename($_FILES['arquivo']['name']);
        $dest = $uploadDir . $name;
        if (move_uploaded_file($tmp, $dest)) {
            $arquivoPath = 'uploads/financeiro/' . $name;
        }
    }

    // 3) Mapeia campos do form para variáveis

    $idChecagem =!empty($form['id']) ? intval($form['id']): 0;

    if($idChecagem > 0){
        $query_checagem = $pdo->prepare("SELECT id_venda FROM financeiro_extrato WHERE id = :id AND id_venda > 0");
        $query_checagem->execute(['id' => $idChecagem]);
        $checagem = $query_checagem->fetch(PDO::FETCH_ASSOC);
        $chk = $checagem['id_venda'];
    
        if($chk>0){
            http_response_code(200);
          echo json_encode(['success' => false, 'message' => 'Proibido a Alteração de uma Venda por este caminho'. $chk]);
            exit;  
            exit; // <- encerra a execução aqui
        }
    }
    


    $tipo_lancamento =  $form['tipo-lancamento'];


    //-------------------------------TRANSFERÊNCIAS---------------------------------//
    if($tipo_lancamento=='transferencia'){

        $id = !empty($form['id']) ? intval($form['id']): 0;
        $data_transferencia   = $form['data-transferencia'] ?? null;
        $pago               = 1;
        $valor_transferencia    = isset($form['tra-valor-transferencia'])  ? str_replace(',', '.', $form['tra-valor-transferencia']) : 0;

        $observacoes        = $form['observacoes']      ?? '';
        $descricao = $form['descricao']?? '';
        // Usuário e datas
        $id_user_criacao    = $_SESSION['id_usuario'];
        $user_criacao       = $_SESSION['nome_usuario'];
        $id_user_alteracao  = intval($_SESSION['id_usuario']);
        $user_alteracao     = $_SESSION['nome_usuario'];
        $data_hora          = date('Y-m-d H:i:s');
        
        
        $id_transferencia = $form['id-transferencia'];
        

        //----------------SAÍDA -----------------

        $id_saida = $form['id-tra-saida']      ?? '';
        $valor_saida = $valor_transferencia *-1;
        $tipo_pg_saida = $form['tra-tipo-pagamento-saida-text']      ?? '';
        $tipo_pg_saida_id = $form['tra-tipo-pagamento-saida']      ?? '';
        $forma_pg_saida = $form['tra-forma-pagamento-saida-text']      ?? '';
        $forma_pg_saida_id = $form['tra-forma-pagamento-saida']      ?? '';
        $conta_saida = $form['tra-conta-corrente-saida-text']      ?? '';
        $conta_saida_id = $form['tra-conta-corrente-saida']      ?? '';
        $categoria_saida = "Transf Interna Saída";
        $id_categoria_saida = '';


        //----------------------------------------
  
        //-----------------ENTRADA------------------
        $id_entrada = $form['id-tra-entrada']      ?? '';
        $valor_entrada = $valor_transferencia;
        $tipo_pg_entrada = $form['tra-tipo-pagamento-entrada-text']      ?? '';
        $tipo_pg_entrada_id = $form['tra-tipo-pagamento-entrada']      ?? '';
        $forma_pg_entrada = $form['tra-forma-pagamento-entrada-text']      ?? '';
        $forma_pg_entrada_id=$form['tra-forma-pagamento-entrada']      ?? '';
        $conta_entrada = $form['tra-conta-corrente-entrada-text']      ?? '';
        $conta_entrada_id = $form['tra-conta-corrente-entrada']      ?? '';
        $categoria_entrada = "Transf Interna Entrada";
        $id_categoria_entrada = '';

        //----------------------------------------
        $transferencia = 1;

       

        if ($id > 0) {
            // UPDATE
           
           //---UPDATE SAIDA-------
           $sql = "UPDATE financeiro_extrato SET
                data_competencia = :data_competencia,
                data_vencimento  = :data_vencimento,
                data_pagamento   = :data_pagamento,
                descricao        = :descricao,
                id_categoria     = :id_categoria,
                categoria        = :categoria,
                id_conta         = :id_conta,
                conta            = :conta,
                valor_principal  = :valor_principal,
                valor_liquido    = :valor_liquido,
                id_tipo_pagamento= :id_tipo_pagamento,
                tipo_pagamento   = :tipo_pagamento,
                id_forma_pagamento       = :id_forma_pagamento,
                forma_pagamento        = :forma_pagamento,
                observacoes      = :observacoes,
                id_user_alteracao = :id_user_alteracao,
                user_alteracao    = :user_alteracao,
                data_alteracao    = :data_hora,
                excluido          = 0,
                arquivo           = :arquivo,
                pago              = 1,
                transferencia     =1
                WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':data_competencia'    => $data_transferencia,
                ':data_vencimento'     => $data_transferencia,
                ':data_pagamento'      => $data_transferencia,
                ':descricao'           => $descricao,
                ':id_categoria'        => $id_categoria_saida,
                ':categoria'           => $categoria_saida,
                ':id_conta'            => $conta_saida_id,
                ':conta'               => $conta_saida,
                ':valor_principal'     => $valor_saida,
                ':valor_liquido'       => $valor_saida,
                ':id_tipo_pagamento'  => $tipo_pg_saida_id,
                ':tipo_pagamento'     => $tipo_pg_saida,
                ':id_forma_pagamento'         => $forma_pg_saida_id,
                ':forma_pagamento'            => $forma_pg_saida,
                ':observacoes'         => $observacoes,
                ':id_user_alteracao'   => $id_user_alteracao,
                ':user_alteracao'      => $user_alteracao,
                ':data_hora'           => $data_hora,
                ':arquivo'             => $arquivoPath,
                ':id'                  => $id_saida
            ]);


                //---UPDATE ENTRADA-------
                $sql = "UPDATE financeiro_extrato SET
                data_competencia = :data_competencia,
                data_vencimento  = :data_vencimento,
                data_pagamento   = :data_pagamento,
                descricao        = :descricao,
                id_categoria     = :id_categoria,
                categoria        = :categoria,
                id_conta         = :id_conta,
                conta            = :conta,
                valor_principal  = :valor_principal,
                valor_liquido    = :valor_liquido,
                id_tipo_pagamento= :id_tipo_pagamento,
                tipo_pagamento   = :tipo_pagamento,
                id_forma_pagamento       = :id_forma_pagamento,
                forma_pagamento        = :forma_pagamento,
                observacoes      = :observacoes,
                id_user_alteracao = :id_user_alteracao,
                user_alteracao    = :user_alteracao,
                data_alteracao    = :data_hora,
                excluido          = 0,
                arquivo           = :arquivo,
                pago              = 1,
                transferencia     =1
                WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':data_competencia'    => $data_transferencia,
                ':data_vencimento'     => $data_transferencia,
                ':data_pagamento'      => $data_transferencia,
                ':descricao'           => $descricao,
                ':id_categoria'        => $id_categoria_entrada,
                ':categoria'           => $categoria_entrada,
                ':id_conta'            => $conta_entrada_id,
                ':conta'               => $conta_entrada,
                ':valor_principal'     => $valor_entrada,
                ':valor_liquido'       => $valor_entrada,
                ':id_tipo_pagamento'  => $tipo_pg_entrada_id,
                ':tipo_pagamento'     => $tipo_pg_entrada,
                ':id_forma_pagamento'         => $forma_pg_entrada_id,
                ':forma_pagamento'            => $forma_pg_entrada,
                ':observacoes'         => $observacoes,
                ':id_user_alteracao'   => $id_user_alteracao,
                ':user_alteracao'      => $user_alteracao,
                ':data_hora'           => $data_hora,
                ':arquivo'             => $arquivoPath,
                ':id'                  => $id_entrada


            ]);

        } 
        
        

        if  ($id==0 || !$id_transferencia){
           
               

            $id_transferencia = date('YmdHis') . rand(1000,9999);
            error_log(">> NOVO ID GERADO: $id_transferencia"); // <--- e aqui
            $transferencia_flag = 1;

            $id_transferencia = uniqid('rec_', true); 
            $form['id-transferencia'] = $id_transferencia; // garante que será usado no insert
        




            // INSERT (Saída)
            $sqlIns = "INSERT INTO financeiro_extrato (
                data_competencia, 
                data_vencimento, 
                data_pagamento,
                descricao, 
                id_categoria, 
                categoria, 
                id_conta, 
                conta,
                valor_principal,
                valor_liquido,
                id_tipo_pagamento, 
                tipo_pagamento, 
                id_forma_pagamento, 
                forma_pagamento, 
                observacoes, 
                id_user_criacao, 
                user_criacao, 
                data_criacao,
                arquivo, 
                pago, 
                transferencia, 
                id_transferencia
            ) VALUES (
                :data_competencia, 
                :data_vencimento, 
                :data_pagamento,
                :descricao, 
                :id_categoria, 
                :categoria, 
                :id_conta, 
                :conta,
                :valor_principal, 
                :valor_liquido,
                :id_tipo_pagamento, 
                :tipo_pagamento, 
                :id_forma_pagamento, 
                :forma_pagamento, 
                :observacoes, 
                :id_user_criacao, 
                :user_criacao, 
                :data_hora,
                :arquivo, 
                :pago, 
                :transferencia, 
                :id_transferencia
            )";
            $stmtIns = $pdo->prepare($sqlIns);

            $stmtIns->execute([
                ':data_competencia'    => $data_transferencia,
                ':data_vencimento'     => $data_transferencia,
                ':data_pagamento'      => $data_transferencia,
                ':descricao'           => $descricao,
                ':id_categoria'        => $id_categoria_saida,
                ':categoria'           => $categoria_saida,
                ':id_conta'            => $conta_saida_id,
                ':conta'               => $conta_saida,
                ':valor_principal'     => $valor_saida,
                ':valor_liquido'       => $valor_saida,
                ':id_tipo_pagamento'  => $tipo_pg_saida_id,
                ':tipo_pagamento'     => $tipo_pg_saida,
                ':id_forma_pagamento'         => $forma_pg_saida_id,
                ':forma_pagamento'            => $forma_pg_saida,
                ':observacoes'         => $observacoes,
                ':id_user_criacao'     => $id_user_criacao,
                ':user_criacao'        => $user_criacao,
                ':data_hora'           => $data_hora,
                ':arquivo'             => $arquivoPath,
                ':pago'                => $pago,
                ':transferencia'         => 1,
                ':id_transferencia'      => $id_transferencia
            ]);

            // INSERT (Entrada)
            $sqlIns = "INSERT INTO financeiro_extrato (
                data_competencia, 
                data_vencimento, 
                data_pagamento,
                descricao, 
                id_categoria, 
                categoria, 
                id_conta, 
                conta,
                valor_principal,
                valor_liquido,
                id_tipo_pagamento, 
                tipo_pagamento, 
                id_forma_pagamento, 
                forma_pagamento, 
                observacoes, 
                id_user_criacao, 
                user_criacao, 
                data_criacao,
                arquivo, 
                pago, 
                transferencia, 
                id_transferencia
            ) VALUES (
                :data_competencia, 
                :data_vencimento, 
                :data_pagamento,
                :descricao, 
                :id_categoria, 
                :categoria, 
                :id_conta, 
                :conta,
                :valor_principal, 
                :valor_liquido,
                :id_tipo_pagamento, 
                :tipo_pagamento, 
                :id_forma_pagamento, 
                :forma_pagamento, 
                :observacoes, 
                :id_user_criacao, 
                :user_criacao, 
                :data_hora,
                :arquivo, 
                :pago, 
                :transferencia, 
                :id_transferencia
            )";
            $stmtIns = $pdo->prepare($sqlIns);

            $stmtIns->execute([
                ':data_competencia'    => $data_transferencia,
                ':data_vencimento'     => $data_transferencia,
                ':data_pagamento'      => $data_transferencia,
                ':descricao'           => $descricao,
                ':id_categoria'        => $id_categoria_entrada,
                ':categoria'           => $categoria_entrada,
                ':id_conta'            => $conta_entrada_id,
                ':conta'               => $conta_entrada,
                ':valor_principal'     => $valor_entrada,
                ':valor_liquido'       => $valor_entrada,
                ':id_tipo_pagamento'  => $tipo_pg_entrada_id,
                ':tipo_pagamento'     => $tipo_pg_entrada,
                ':id_forma_pagamento'         => $forma_pg_entrada_id,
                ':forma_pagamento'            => $forma_pg_entrada,
                ':observacoes'         => $observacoes,
                ':id_user_criacao'     => $id_user_criacao,
                ':user_criacao'        => $user_criacao,
                ':data_hora'           => $data_hora,
                ':arquivo'             => $arquivoPath,
                ':pago'                => $pago,
                ':transferencia'         => 1,
                ':id_transferencia'      => $id_transferencia
            ]);

        }
    }


    //----------------------------ENTRADAS E SAÍDAS --------------------------------------//
    if($tipo_lancamento!='transferencia'){
        $id                 = !empty($form['id'])              ? intval($form['id'])              : 0;
        $data_competencia   = $form['data-competencia'] ?? null;
        $data_vencimento    = $form['data-vencimento']   ?? null;
        $pago               = (!empty($form['pago']) && $form['pago'] === '1') ? 1 : 0;
        
        if ($pago ==''){
            $pago =0;
        }
        
        $data_pagamento     = $pago ? ($form['data-pagamento'] ?? null) : null;
        $descricao          = $form['descricao']        ?? '';
        
        $id_categoria       = !empty($form['categoria'])       ? intval($form['categoria'])       : null;
        $categoria          = !empty($form['categoria-text'])       ?$form['categoria-text']      : null;//atr

        $id_conta           = (!empty($form['conta-corrente'])) ? intval($form['conta-corrente']) : null;
        $conta     = (!empty($form['conta-corrente-text'])) ? $form['conta-corrente-text'] : null;
        
        $valor_principal    = isset($form['valor-bruto'])  ? str_replace(',', '.', $form['valor-bruto']) : 0;

        $multa_juros        = ($pago && isset($form['multa-encargo'])) ? str_replace(',', '.', $form['multa-encargo']) : 0;
        $desconto_taxa      = ($pago && isset($form['juros-tarifas'])) ? str_replace(',', '.', $form['juros-tarifas']) : 0;

        if ($multa_juros==''){
            $multa_juros = 0;
        }
        if ($desconto_taxa ==''){
            $desconto_taxa = 0;
        }

        

        $valor_liquido      = ($pago && isset($form['valor-liquido'])) ? str_replace(',', '.', $form['valor-liquido']) : null;
        
        


        if ($tipo_lancamento=='despesa'){
            
            $valor_principal = $valor_principal *-1;
        }

        if ($pago =='1'){
            
            if ($tipo_lancamento =='receita'){
                $desconto_taxa = $desconto_taxa *-1;
            }else if($tipo_lancamento == 'despesa'){
                $multa_juros = $multa_juros*-1;
                $valor_liquido = $valor_liquido*-1;
            }
        } else{
            $desconto_taxa = '0';
            $multa_juros = '0';
            $valor_liquido = $valor_principal;

            //if ($tipo_lancamento =='despesa'){
            //    $id_conta = $id_contasAPagar;
            //    $conta = $constasAPagar;
            //   $id_tipo_pagamento = '';
            //} elseif ($tipo_lancamento =='receita'){
        //     $id_conta = $id_contasAReceber;
        //     $conta = $contasAReceber;
        // }

            //$data_pagamento=$data_vencimento; não mais
        }
        

        $id_centro_custo    = !empty($form['centro-custo'])    ? intval($form['centro-custo'])    : null;
        $centro_custo    = !empty($form['centro-custo-text'])    ? $form['centro-custo-text']    : null;
        if ($id_centro_custo==0){
            $centro_custo ='';
        }
        
        $id_tipo_pagamento  = !empty($form['tipo-pagamento'])  ? intval($form['tipo-pagamento'])  : null;
        $tipo_pagamento     = !empty($form['tipo-pagamento-text'])  ?$form['tipo-pagamento-text'] : null; //atr
        if ($id_tipo_pagamento==0){
            $tipo_pagamento ='';
        }
        $id_forma_pagamento        = !empty($form['forma-pagamento']) ? intval($form['forma-pagamento']) : null;
        $forma_pagamento        = !empty($form['forma-pagamento-text']) ? $form['forma-pagamento-text'] : null; //atr
        if ($id_forma_pagamento==0){
            $forma_pagamento ='';
        }

        $id_fornecedor      = !empty($form['fornecedor'])      ? intval($form['fornecedor'])      : null;
        $fornecedor         = !empty($form['fornecedor-text'])      ? $form['fornecedor-text']      : null; //atr
        if ($id_fornecedor==0){
            $fornecedor ='';
        }






        $observacoes        = $form['observacoes']      ?? '';
        $id_cliente         = !empty($form['id-cliente'])     ? intval($form['id-cliente'])     : null;
        $id_filial          = !empty($form['id-filial'])       ? intval($form['id-filial'])       : null;
        $id_comum           = !empty($form['id-comum'])        ? intval($form['id-comum'])        : null;
        $nota_fiscal        = $form['nota-fiscal']       ?? null;

        // Usuário e datas
        $id_user_criacao    = $_SESSION['id_usuario'];
        $user_criacao       = $_SESSION['nome_usuario'];
        $id_user_alteracao  = intval($_SESSION['id_usuario']);
        $user_alteracao     = $_SESSION['nome_usuario'];
        $data_hora          = date('Y-m-d H:i:s');


        if (!$pago){
            $data_pagamento = $data_vencimento;
        
        }

        // Recorrência
        $hasRec = !empty($recurrences) && is_array($recurrences);
        error_log("recurrences count: " . count($recurrences)); 
        if ($hasRec && $id === 0) {
            $id_recorrencia = date('YmdHis') . rand(1000,9999);
            error_log(">> NOVO ID GERADO: $id_recorrencia"); // <--- e aqui
            $recorrencia_flag = 1;

            $id_recorrencia = uniqid('rec_', true); 
            $form['id_recorrencia'] = $id_recorrencia; // garante que será usado no insert
            $recorrencia_flag = 1;
        } elseif ($id > 0) {
            // ao editar, preserva o id_recorrencia existente
            $stmtF = $pdo->prepare("SELECT id_recorrencia FROM financeiro_extrato WHERE id = :id");
            $stmtF->execute([':id'=>$id]);
            $id_recorrencia = $stmtF->fetchColumn();
            $recorrencia_flag = !empty($id_recorrencia) ? 1 : 0;
        } else {
            $id_recorrencia = null;
            $recorrencia_flag = 0;
        }







        // 4) Inserção ou atualização
        if ($id > 0) {
            // UPDATE somente do registro atual
            $sql = "UPDATE financeiro_extrato SET
                data_competencia = :data_competencia,
                data_vencimento  = :data_vencimento,
                data_pagamento   = :data_pagamento,
                descricao        = :descricao,
                id_categoria     = :id_categoria,
                categoria        = :categoria,
                id_conta         = :id_conta,
                conta            = :conta,
                valor_principal  = :valor_principal,
                multa_juros      = :multa_juros,
                desconto_taxa    = :desconto_taxa,
                valor_liquido    = :valor_liquido,
                id_centro_custo  = :id_centro_custo,
                centro_custo     = :centro_custo,
                id_tipo_pagamento= :id_tipo_pagamento,
                tipo_pagamento   = :tipo_pagamento,
                id_forma_pagamento       = :id_forma_pagamento,
                forma_pagamento        = :forma_pagamento,
                id_fornecedor    = :id_fornecedor,
                fornecedor       = :fornecedor,
                observacoes      = :observacoes,
                id_user_alteracao = :id_user_alteracao,
                user_alteracao    = :user_alteracao,
                data_alteracao    = :data_hora,
                nota_fiscal       = :nota_fiscal,
                id_comum          = :id_comum,
                id_cliente        = :id_cliente,
                id_filial         = :id_filial,
                excluido          = 0,
                arquivo           = :arquivo,
                pago              = :pago,
                recorrencia       = :recorrencia,
                id_recorrencia    = :id_recorrencia
                WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':data_competencia'    => $data_competencia,
                ':data_vencimento'     => $data_vencimento,
                ':data_pagamento'      => $data_pagamento,
                ':descricao'           => $descricao,
                ':id_categoria'        => $id_categoria,
                ':categoria'           => $categoria,
                ':id_conta'            => $id_conta,
                ':conta'               => $conta,
                ':valor_principal'     => $valor_principal,
                ':multa_juros'         => $multa_juros,
                ':desconto_taxa'       => $desconto_taxa,
                ':valor_liquido'       => $valor_liquido,
                ':id_centro_custo'     => $id_centro_custo,
                ':centro_custo'        => $centro_custo,
                ':id_tipo_pagamento'  => $id_tipo_pagamento,
                ':tipo_pagamento'     => $tipo_pagamento,
                ':id_forma_pagamento'         => $id_forma_pagamento,
                ':forma_pagamento'            => $forma_pagamento,
                ':id_fornecedor'       => $id_fornecedor,
                ':fornecedor'          => $fornecedor,
                ':observacoes'         => $observacoes,
                ':id_user_alteracao'   => $id_user_alteracao,
                ':user_alteracao'      => $user_alteracao,
                ':data_hora'           => $data_hora,
                ':nota_fiscal'         => $nota_fiscal,
                ':id_comum'            => $id_comum,
                ':id_cliente'          => $id_cliente,
                ':id_filial'           => $id_filial,
                ':arquivo'             => $arquivoPath,
                ':pago'                => $pago,
                ':recorrencia'         => $recorrencia_flag,
                ':id_recorrencia'      => $id_recorrencia,
                ':id'                  => $id
            ]);

        } else {
            // INSERT (simples ou em lote se recorrência)
            $sqlIns = "INSERT INTO financeiro_extrato (
                data_competencia, data_vencimento, data_pagamento,
                descricao, id_categoria, categoria, id_conta, conta,
                valor_principal, multa_juros, desconto_taxa, valor_liquido,
                id_centro_custo, centro_custo, id_tipo_pagamento, tipo_pagamento, 
                id_forma_pagamento, forma_pagamento, id_fornecedor, fornecedor,
                observacoes, id_user_criacao, user_criacao, data_criacao,
                nota_fiscal, id_comum, id_cliente, id_filial,
                excluido, arquivo, pago, recorrencia, id_recorrencia
            ) VALUES (
                :data_competencia, :data_vencimento, :data_pagamento,
                :descricao, :id_categoria, :categoria, :id_conta, :conta,
                :valor_principal, :multa_juros, :desconto_taxa, :valor_liquido,
                :id_centro_custo, :centro_custo, :id_tipo_pagamento, :tipo_pagamento, 
                :id_forma_pagamento, :forma_pagamento, :id_fornecedor, :fornecedor,
                :observacoes, :id_user_criacao, :user_criacao, :data_hora,
                :nota_fiscal, :id_comum, :id_cliente, :id_filial,
                0, :arquivo, :pago, :recorrencia, :id_recorrencia
            )";
            $stmtIns = $pdo->prepare($sqlIns);

            if ($hasRec) {
                // insere cada recorrência como registro independente
                foreach ($recurrences as $r) {
                    $stmtIns->execute([
                        ':data_competencia'    => substr($r['competencia'],0,10),
                        ':data_vencimento'     => substr($r['vencimento'],0,10),
                        ':data_pagamento'      => null,
                        ':descricao'           => $r['descricao'],  
                        ':id_categoria'        => $id_categoria,
                        ':categoria'           => $categoria,
                        ':id_conta'            => $id_conta,
                        ':conta'               => $conta,
                        ':valor_principal'     => $valor_principal,
                        ':multa_juros'         => 0,
                        ':desconto_taxa'       => 0,
                        ':valor_liquido'       => $valor_liquido,
                        ':id_centro_custo'     => $id_centro_custo,
                        ':centro_custo'        => $centro_custo,
                        ':id_tipo_pagamento'  => $id_tipo_pagamento,
                        ':tipo_pagamento'     => $tipo_pagamento,
                        ':id_forma_pagamento'         => $id_forma_pagamento,
                        ':forma_pagamento'            => $forma_pagamento,
                        ':id_fornecedor'       => $id_fornecedor,
                        ':fornecedor'          => $fornecedor,
                        ':observacoes'         => $observacoes,
                        ':id_user_criacao'     => $id_user_criacao,
                        ':user_criacao'        => $user_criacao,
                        ':data_hora'           => $data_hora,
                        ':nota_fiscal'         => $nota_fiscal,
                        ':id_comum'            => $id_comum,
                        ':id_cliente'          => $id_cliente,
                        ':id_filial'           => $id_filial,
                        ':arquivo'             => $arquivoPath,
                        ':pago'                => 0,
                        ':recorrencia'         => 1,
                        ':id_recorrencia'      => $id_recorrencia
                    ]);
                }
            } else {
                // lançamento único sem recorrência
                $stmtIns->execute([
                    ':data_competencia'    => $data_competencia,
                    ':data_vencimento'     => $data_vencimento,
                    ':data_pagamento'      => $data_pagamento,
                    ':descricao'           => $descricao,
                    ':id_categoria'        => $id_categoria,
                    ':categoria'           => $categoria,
                    ':id_conta'            => $id_conta,
                    ':conta'               => $conta,
                    ':valor_principal'     => $valor_principal,
                    ':multa_juros'         => $multa_juros,
                    ':desconto_taxa'       => $desconto_taxa,
                    ':valor_liquido'       => $valor_liquido,
                    ':id_centro_custo'     => $id_centro_custo,
                    ':centro_custo'        => $centro_custo,
                    ':id_tipo_pagamento'  => $id_tipo_pagamento,
                    ':tipo_pagamento'     => $tipo_pagamento,
                    ':id_forma_pagamento'         => $id_forma_pagamento,
                    ':forma_pagamento'            => $forma_pagamento,
                    ':id_fornecedor'       => $id_fornecedor,
                    ':fornecedor'          => $fornecedor,
                    ':observacoes'         => $observacoes,
                    ':id_user_criacao'     => $id_user_criacao,
                    ':user_criacao'        => $user_criacao,
                    ':data_hora'           => $data_hora,
                    ':nota_fiscal'         => $nota_fiscal,
                    ':id_comum'            => $id_comum,
                    ':id_cliente'          => $id_cliente,
                    ':id_filial'           => $id_filial,
                    ':arquivo'             => $arquivoPath,
                    ':pago'                => $pago,
                    ':recorrencia'         => 0,
                    ':id_recorrencia'      => null
                ]);
            }
        }

        
    }//fim do if de !transferencia (entradas e saidas)
$pdo->commit();
        echo json_encode(['success' => true]);
    


} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
