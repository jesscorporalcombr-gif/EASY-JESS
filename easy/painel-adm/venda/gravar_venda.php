<?php
require_once("../../conexao.php");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Iniciar sessão, se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




// Definir o content type para JSON (p/ retornar ao front)
header('Content-Type: application/json');
 

$idUserCriacao  = $_SESSION['id_usuario']   ?? null; // user_criacao
$userCriacao    = $_SESSION['nome_usuario'] ?? null; // id_user_criacao
    $dataHoraAtual  = date('Y-m-d H:i:s');
    $dataSomenteHoje= date('Y-m-d');


// Recuperar dados enviados via POST
$id_venda    = $_POST['id_venda']    ?? null;
$id_cliente  = $_POST['id_cliente']  ?? null;
$data_venda  = $_POST['data_venda']  ?? null; // data que você definiu no front
$valor_final = $_POST['valor_final'] ?? null;
$pagamentosRecebidos = $_POST['pagamentos']   ?? []; // array de pagamentos

// Verificações mínimas
if (!$id_venda || !$id_cliente) {
    echo json_encode(['status'=>'error','mensagem'=>'Dados incompletos (id_venda / id_cliente ausentes)']);
    exit;
}





 $stmt = $pdo->prepare("
                SELECT id, valor_final, data_venda
                FROM venda
                WHERE quitado_total = 0
                AND id_cliente     = :id_cliente
                AND tipo_venda = 'venda'
                ORDER BY data_venda ASC, id ASC
            ");
            $stmt->execute([':id_cliente' => $id_cliente]);
            $vendasAbertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2) Monta o array de vendas abertas com saldo_restante = valor_final
            $arr_vendas_abertas = [];
            foreach ($vendasAbertas as $v) {
                $id = $v['id'];
                $arr_vendas_abertas[$id] = [
                    'id'              => $id,
                    'data_venda'      => $v['data_venda'],
                    'saldo_restante'  => (float) $v['valor_final'],
                    'pagamentos'      => [],
                ];
            }


            //echo '<pre>VENDAS ABERTAS:';       
            ///rint_r($arr_vendas_abertas);   
            //echo '</pre>';

            // 3) Reúne os pagamentos antigos (saldos não consumidos) + os novos da venda atual
            $pagamentos_post = [];

            // 3.1) Pagamentos antigos do cliente com saldo > 0
            $sqlOld = "
                SELECT 
                vp.id                   AS id_pagamento_origem,
                vp.id_venda             AS id_venda_origem,
                vp.perc_taxa            AS perc_taxa,
                vp.valor 
                    - COALESCE(SUM(vpd.valor_pagamento),0) AS valor_pagamento
                FROM venda_pagamentos vp
                JOIN venda v ON v.id = vp.id_venda
                LEFT JOIN venda_pagamentos_detalhados vpd 
                ON vpd.id_pagamento_origem = vp.id
                WHERE v.id_cliente = :id_cliente
                AND vp.id_venda     <> :id_venda
                AND vp.venda = 1
                AND (vp.pago IS NULL OR vp.pago <> 1)
                GROUP BY vp.id, vp.id_venda, vp.perc_taxa, vp.valor
                HAVING valor_pagamento > 0
                ORDER BY vp.id
            ";
            $stmtOld = $pdo->prepare($sqlOld);
            $stmtOld->execute([
                ':id_cliente' => $id_cliente, 
                ':id_venda'   => $id_venda,
            ]);


            while ($r = $stmtOld->fetch(PDO::FETCH_ASSOC)) {
                $pagamentos_post[] = [
                    'id_pagamento_origem' => (int)$r['id_pagamento_origem'],
                    'id_venda_origem'     => (int)$r['id_venda_origem'],
                    'valor_pagamento'     => (float)$r['valor_pagamento'],
                    'perc_taxa'           => (float)$r['perc_taxa'],
                ];
            }





            //echo '<pre>pagamentos post  1 :';       
            //print_r($pagamentos_post);   
            //echo '</pre>';


            // 3.2) Pagamentos novos da venda atual
            $sqlNew = "
                SELECT 
                id                   AS id_pagamento_origem,
                id_venda             AS id_venda_origem,
                perc_taxa            AS perc_taxa,
                valor                AS valor_pagamento
                FROM venda_pagamentos
                WHERE id_venda = :id_venda
                AND venda = 1
                ORDER BY id
            ";
            $stmtNew = $pdo->prepare($sqlNew);
            $stmtNew->execute([':id_venda' => $id_venda]);
            while ($r = $stmtNew->fetch(PDO::FETCH_ASSOC)) {
                $pagamentos_post[] = [
                    'id_pagamento_origem' => (int)$r['id_pagamento_origem'],
                    'id_venda_origem'     => (int)$r['id_venda_origem'],
                    'valor_pagamento'     => (float)$r['valor_pagamento'],
                    'perc_taxa'           => (float)$r['perc_taxa'],
                ];
            }


            //echo '<pre>pagamentos post 2 :';       
            //print_r($pagamentos_post);   
            //echo '</pre>';

            // 4) “Fatia” todos os pagamentos sobre as vendas em aberto (FIFO)
            $arr_detalhamentos = [];
            foreach ($pagamentos_post as $pag) {
                $restante = $pag['valor_pagamento'];

                foreach ($arr_vendas_abertas as $vid => &$v) {
                    if ($restante <= 0) break;
                    if ($v['saldo_restante'] <= 0) continue;

                    $alocar = min($v['saldo_restante'], $restante);

                    $arr_detalhamentos[] = [
                        'id_venda_origem'     => $pag['id_venda_origem'],
                        'id_pagamento_origem' => $pag['id_pagamento_origem'],
                        'id_venda_destino'    => $vid,
                        'valor_pagamento'     => $alocar,
                        'perc_taxa'           => $pag['perc_taxa'],
                    ];

                    $v['saldo_restante'] -= $alocar;
                    $restante           -= $alocar;
                }

                unset($v);
            }




            //echo '<pre>DEBUG detalhamentos:' . PHP_EOL;
            //print_r($arr_detalhamentos);
            //echo '</pre>';



            // 5) Calcula indicadores de vendas quitadas e pagamentos consumidos
            $idsVendasQuitadas       = [];
            $idsPagamentosConsumidos = [];

            // Vendas que zeraram saldo
            foreach ($arr_vendas_abertas as $vid => $v) {
                if ($v['saldo_restante'] <= 0) {
                    $idsVendasQuitadas[] = $vid;
                }
            }

            // Pagamentos cujo soma de fatias >= valor original
            foreach ($pagamentos_post as $pag) {
                $consumido = 0;
                foreach ($arr_detalhamentos as $d) {
                    if ($d['id_pagamento_origem'] === $pag['id_pagamento_origem']) {
                        $consumido += $d['valor_pagamento'];
                    }
                }
                if ($consumido >= $pag['valor_pagamento']) {
                    $idsPagamentosConsumidos[] = $pag['id_pagamento_origem'];
                }
            }

            // 6) Grava tudo em transação (reset + insert + updates)
            try {
                $pdo->beginTransaction();

                // 6.1) Deleta todos os detalhes antigos para estas vendas
                $idsV = array_keys($arr_vendas_abertas);
                if ($idsV) {
                    $ph = implode(',', array_fill(0, count($idsV), '?'));
                    $pdo->prepare("DELETE FROM venda_pagamentos_detalhados WHERE id_venda_destino IN ($ph)")
                        ->execute($idsV);
                }
//echo 'chegou aqui';
                // 6.2) Insere os novos detalhamentos (com cálculo de taxas)
                $sqlIns = "
                INSERT INTO venda_pagamentos_detalhados
                    (id_venda_origem, id_pagamento_origem, id_venda_destino,
                    valor_pagamento, perc_taxa, valor_taxa, valor_liquido,
                    id_cliente, id_user_criacao, user_criacao, data_hora_criacao)
                VALUES
                    (:idorig, :idpag, :iddest,
                    :val,    :perc,  :vtax,      :vliq,
                    :idcli,  :usrid, :usrnm,     :dh)
                ";
                $ins = $pdo->prepare($sqlIns);
                foreach ($arr_detalhamentos as $d) {
                    $vtax = round($d['valor_pagamento'] * ($d['perc_taxa']/100), 2);
                    $vliq = $d['valor_pagamento'] - $vtax;
                    $ins->execute([
                        ':idorig' => $d['id_venda_origem'],
                        ':idpag'  => $d['id_pagamento_origem'],
                        ':iddest' => $d['id_venda_destino'],
                        ':val'    => $d['valor_pagamento'],
                        ':perc'   => $d['perc_taxa'],
                        ':vtax'   => $vtax,
                        ':vliq'   => $vliq,
                        ':idcli'  => $id_cliente,
                        ':usrid'  => $idUserCriacao,
                        ':usrnm'  => $userCriacao,
                        ':dh'     => date('Y-m-d H:i:s'),
                    ]);
                }

                // 6.3) Marca vendas quitadas
                if ($idsVendasQuitadas) {
                    $ph = implode(',', array_fill(0, count($idsVendasQuitadas), '?'));
                    $pdo->prepare("UPDATE venda SET quitado_total = 1 WHERE id IN ($ph)")
                        ->execute($idsVendasQuitadas);
                }

                // 6.4) Marca pagamentos consumidos
                if ($idsPagamentosConsumidos) {
                    $ph = implode(',', array_fill(0, count($idsPagamentosConsumidos), '?'));
                    $pdo->prepare("UPDATE venda_pagamentos SET pago = 1 WHERE id IN ($ph)")
                        ->execute($idsPagamentosConsumidos);
                }

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }

















try {
    // 1) Verificar se a venda existe e atualizar de "proposta" para "venda"
    //    Carregar também nome e cpf do cliente da venda p/ montar "descricao" depois
    $sql = "SELECT id, id_cliente, cliente, cpf
              FROM venda
             WHERE id = :id_venda";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_venda' => $id_venda]);
    $venda = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venda) {
        echo json_encode(['status'=>'error','mensagem'=>'Venda não encontrada.']);
        exit;
    }

    // Verifica se o id_cliente bate
    if ($venda['id_cliente'] != $id_cliente) {
        echo json_encode(['status'=>'error','mensagem'=>'O ID do cliente não confere com o registrado na venda.']);
        exit;
    }

    // Pega cliente e cpf para compor 'descricao'
    $nome_cliente = $venda['cliente'] ?? '';
    $cpf_cliente  = $venda['cpf']     ?? '';

    // Atualiza a venda => muda tipo_venda para "venda" e define a data_venda
    $sqlUpdate = "UPDATE venda
                     SET tipo_venda = :tipo_venda,
                         data_venda = :data_venda,
                         dataHora_alteracao = :dataHora_alteracao,
                         id_user_alteracao  = :id_user_alteracao,
                         user_alteracao     = :user_alteracao
                   WHERE id = :id_venda";
    $stmtUp = $pdo->prepare($sqlUpdate);


    $sqlUpdateIt = "UPDATE venda_itens
                     SET venda = 1,
                        data_venda = :data_venda,
                        tipo_venda = :tipo_venda
                     WHERE id_venda = :id_venda";
    $stmtUpIt = $pdo->prepare($sqlUpdateIt);
    $stmtUpIt->bindValue(':id_venda',         $id_venda);
    $stmtUpIt->bindValue(':data_venda',       $data_venda);
    $stmtUpIt->bindValue(':tipo_venda',       'venda');
    $stmtUpIt->execute();


    $sqlUpdatePg = "UPDATE venda_pagamentos
                    SET venda = 1,
                        data_venda = :data_venda
                    WHERE id_venda = :id_venda";
    $stmtUpPg = $pdo->prepare($sqlUpdatePg);
    $stmtUpPg->bindValue(':id_venda',          $id_venda);
    $stmtUpPg->bindValue(':data_venda',          $data_venda);
    $stmtUpPg->execute();







    // Coletar informações de auditoria
    $agora       = date('Y-m-d H:i:s');
    $tipo_venda  = 'venda';
    $idUserAlt   = $_SESSION['id_usuario']   ?? null;  // ou outro campo
    $userAlt     = $_SESSION['nome_usuario'] ?? null;

    $stmtUp->bindValue(':tipo_venda',        $tipo_venda);
    $stmtUp->bindValue(':data_venda',        $data_venda);
    $stmtUp->bindValue(':dataHora_alteracao',$agora);
    $stmtUp->bindValue(':id_user_alteracao', $idUserAlt);
    $stmtUp->bindValue(':user_alteracao',    $userAlt);
    $stmtUp->bindValue(':id_venda',          $id_venda);
    $stmtUp->execute();























    // 2) Agora sincronizar financeiro_extrato
    // ----------------------------------------------------
    // A- vuscar registros existentes em financeiro_extrato p/ este id_venda
    $sqlExtrato = "SELECT id, id_pagamento_venda
                     FROM financeiro_extrato
                    WHERE id_venda = :id_venda";
    $stmtExtrato = $pdo->prepare($sqlExtrato);
    $stmtExtrato->execute([':id_venda' => $id_venda]);
    $extratosDB = $stmtExtrato->fetchAll(PDO::FETCH_ASSOC);

    // Mapa: [id_pagamento_venda -> id_financeiro_extrato]
    $mapaFinanceiroDB = [];
    foreach ($extratosDB as $row) {
        $mapaFinanceiroDB[$row['id_pagamento_venda']] = $row['id'];
    }

    // Preparar dados de auditoria
   

    // Função p/ converter decimal br p/ float
    function converterParaDecimal($valorBR) {
        $valorSemPonto = str_replace('.', '', $valorBR);
        return (float) str_replace(',', '.', $valorSemPonto);
    }

    // B) Percorre cada pagamento do array para Insert/Update
    $pagamentosEncontrados = []; // Para identificar quais do DB não excluiremos

    foreach ($pagamentosRecebidos as $pg) {
        $idPagamentoVenda  = $pg['pagamento_id']      ?? 0;
        $diasPagamento     = (int) ($pg['dias_pagamento'] ?? 0);
        $valorPagamento    = converterParaDecimal($pg['valor_pagamento'] ?? '0');
        $valorTaxa         = converterParaDecimal($pg['valor_taxa']      ?? '0');
        $valorLiquido = $valorPagamento - $valorTaxa;
        $pago=1;


        $tipo_pagamento = $pg['tipo_pagamento'];
        $id_tipo_pagamento = $pg['id_tipo_pagamento'];

        $forma_pagamento = $pg['pagamento']          ?? null;
        $id_forma_pagamento = $pg['id_pagamento']       ?? null;
        $conta_corrente = $pg['conta_corrente'] ?? null;
        $id_conta_corrente = $pg['id_conta_corrente'] ?? null;
        
        $categoria = 'RECEITAS DE VENDAS';
        $id_categoria = 2;
        if ($id_tipo_pagamento ==11){
            $categoria = 'Receitas a Receber (venda Cliente)';
            $id_categoria = 128;

        } 






        //  supor que data_vencimento = data_venda + dias_pagamento
        $dataVencimento = date('Y-m-d', strtotime("{$data_venda} +{$diasPagamento} days"));
        // Neste exemplo, data_pagamento igual a data_vencimento (ou ajuste conforme regra)
        $dataPagamento  = $dataVencimento;

        // Aqui adiciona as colunas extras (descricao, forma_pagamento, etc.)
        // Observação: 'descricao' contendo id_venda, nome_cliente e cpf_cliente
        $dadosExtrato = [
            'data_competencia'   => $data_venda,
            'data_vencimento'    => $dataVencimento,
            'data_pagamento'     => $dataPagamento,
            'descricao'          => 'Venda ' . $id_venda . ' - ' . $nome_cliente . ' CPF: ' . $cpf_cliente,
            'categoria'          => $categoria, //'RECEITAS DE VENDAS',
            'id_categoria'       => $id_categoria, //2,
            'conta'              => $conta_corrente,
            'id_conta'           => $id_conta_corrente,
            'valor_principal'    => $valorPagamento,
            'desconto_taxa'      => ($valorTaxa != 0) ? -1 * $valorTaxa : 0,
            'valor_liquido'      => $valorLiquido,
            'tipo_pagamento'     => $tipo_pagamento,
            'id_tipo_pagamento'  => $id_tipo_pagamento,
            'forma_pagamento'    => $forma_pagamento,
            'id_forma_pagamento' => $id_forma_pagamento,
            'id_venda'           => $id_venda,
            'id_pagamento_venda' => $idPagamentoVenda,
            'id_cliente'         => $id_cliente,
            'pago'         => $pago
        ];

        // Ver se existe no DB
        if (!empty($idPagamentoVenda) && isset($mapaFinanceiroDB[$idPagamentoVenda])) {
            // UPDATE
            $idExtrato = $mapaFinanceiroDB[$idPagamentoVenda];
            $pagamentosEncontrados[] = $idPagamentoVenda;

            $sqlUpdateFin = "UPDATE financeiro_extrato SET
                data_competencia   = :data_competencia,
                data_vencimento    = :data_vencimento,
                data_pagamento     = :data_pagamento,
                descricao          = :descricao,
                categoria          = :categoria,
                id_categoria       = :id_categoria,
                conta              = :conta,
                id_conta           = :id_conta,
                valor_principal    = :valor_principal,
                desconto_taxa      = :desconto_taxa,
                valor_liquido      = :valor_liquido,
                tipo_pagamento    = :tipo_pagamento,
                id_tipo_pagamento = :id_tipo_pagamento,
                forma_pagamento           = :forma_pagamento,
                id_forma_pagamento        = :id_forma_pagamento,
                id_venda           = :id_venda,
                id_pagamento_venda = :id_pagamento_venda,
                id_cliente         = :id_cliente,
                id_user_alteracao  = :id_user_alteracao,
                user_alteracao     = :user_alteracao,
                data_alteracao     = :data_alteracao,
                pago = :pago
            WHERE id = :id_extrato";

            $stmtUpFin = $pdo->prepare($sqlUpdateFin);
            $stmtUpFin->bindValue(':id_extrato',        $idExtrato);
            $stmtUpFin->bindValue(':data_alteracao',    $dataHoraAtual);
            $stmtUpFin->bindValue(':id_user_alteracao', $idUserCriacao);
            $stmtUpFin->bindValue(':user_alteracao',    $userCriacao);

            // Faz o bind dos campos do array
            foreach ($dadosExtrato as $col => $val) {
                $stmtUpFin->bindValue(":{$col}", $val);
            }

            $stmtUpFin->execute();

        } else {

            $stmtLookup = $pdo->prepare("
                SELECT id
                FROM venda_pagamentos
                WHERE id_venda    = :id_venda
                AND id_condicao = :id_condicao
                AND valor       = :valor
                AND valor_taxa  = :valor_taxa
                LIMIT 1
            ");
            $stmtLookup->execute([
                ':id_venda'    => $id_venda,
                ':id_condicao' => (int)$pg['id_pagamento'],
                ':valor'       => converterParaDecimal($pg['valor_pagamento'] ?? '0'),
                ':valor_taxa'  => converterParaDecimal($pg['valor_taxa']      ?? '0'),
            ]);
            $novoPagamentoId = $stmtLookup->fetchColumn(); // ou false

            // INSERT
            $sqlInsertFin = "INSERT INTO financeiro_extrato (
                data_competencia, data_vencimento, data_pagamento,
                descricao,
                categoria, id_categoria, conta, id_conta,
                valor_principal, desconto_taxa, valor_liquido,
                tipo_pagamento, id_tipo_pagamento,
                forma_pagamento, id_forma_pagamento,
                id_venda, id_pagamento_venda, id_cliente,
                id_user_criacao, user_criacao, data_criacao, pago
            ) VALUES (
                :data_competencia, :data_vencimento, :data_pagamento,
                :descricao,
                :categoria, :id_categoria, :conta, :id_conta,
                :valor_principal, :desconto_taxa, :valor_liquido,
                :tipo_pagamento, :id_tipo_pagamento,
                :forma_pagamento, :id_forma_pagamento,
                :id_venda, :id_pagamento_venda, :id_cliente,
                :id_user_criacao, :user_criacao, :data_criacao, :pago
            )";

            $stmtInFin = $pdo->prepare($sqlInsertFin);
            
            // Preencher dados de criação
            $stmtInFin->bindValue(':id_user_criacao', $idUserCriacao);
            $stmtInFin->bindValue(':user_criacao',    $userCriacao);
            $stmtInFin->bindValue(':data_criacao',    $dataSomenteHoje);

            // Faz o bind dos campos do array
            foreach ($dadosExtrato as $col => $val) {
                $stmtInFin->bindValue(":{$col}", $val);
            }

            if ($novoPagamentoId) {
                $stmtInFin->bindValue(':id_pagamento_venda', $novoPagamentoId, PDO::PARAM_INT);
            } else {
                $stmtInFin->bindValue(':id_pagamento_venda', null, PDO::PARAM_NULL);
            }


            try {
                $stmtInFin->execute();

            } catch (PDOException $e) {
                // Se houver erro de sintaxe ou constraint, cai aqui
                echo 'Exception: ', $e->getMessage();
                exit;
            }

        }
    }

    // C) Excluir registros do DB que não apareceram no array de pagamentos
    $idsParaExcluir = [];
    foreach ($mapaFinanceiroDB as $idPagVenda => $idExtrato) {
        if (!in_array($idPagVenda, $pagamentosEncontrados)) {
            $idsParaExcluir[] = $idPagVenda;
        }
    }
    if (!empty($idsParaExcluir)) {
        // Montar placeholders
        $placeholders = rtrim(str_repeat('?,', count($idsParaExcluir)), ',');
        $sqlDel = "DELETE FROM financeiro_extrato
                    WHERE id_venda = ?
                      AND id_pagamento_venda IN ($placeholders)";
        $stmtDel = $pdo->prepare($sqlDel);

        $i = 1;
        $stmtDel->bindValue($i++, $id_venda);
        foreach ($idsParaExcluir as $payId) {
            $stmtDel->bindValue($i++, $payId);
        }
        $stmtDel->execute();
    }

    // Se chegou até aqui, deu tudo certo
    echo json_encode([
        'status' => 'success',
        'mensagem' => 'Venda e financeiro_extrato atualizados com sucesso!',
        'id_venda' => $id_venda
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(['status'=>'error','mensagem'=>'Erro geral: '.$e->getMessage()]);
    exit;
}






























?>