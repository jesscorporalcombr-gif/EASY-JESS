<?php
// consultar_saldo_cliente.php

header('Content-Type: application/json; charset=utf-8');

require_once '../../conexao.php';  // ajusta o caminho conforme seu projeto

$id_cliente   = isset($_REQUEST['id_cliente'])   ? (int) $_REQUEST['id_cliente']   : null;
$id_venda     = isset($_REQUEST['id_venda'])     ? (int) $_REQUEST['id_venda']     : null;
$data_posicao = isset($_REQUEST['data_posicao']) ? $_REQUEST['data_posicao']       : null;

if (!$id_cliente) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetro id_cliente é obrigatório']);
    exit;
}




try {

    $stmt = $pdo->prepare("SELECT saldo FROM clientes WHERE id = :id_cliente");
    $stmt->execute([':id_cliente'=>$id_cliente]);
    $saldo_cliente = (float)$stmt->fetchColumn();
    // Caso não seja fornecido id_venda ou data_posicao, retorna saldo atual
    if (!empty($id_venda) && empty($data_posicao)) {
        // caso 2: subtrai o valor da venda atual
        $stmtV = $pdo->prepare("
            SELECT saldo
            FROM venda 
            WHERE id = :id_venda
        ");
        $stmtV->execute([':id_venda'=>$id_venda]);
        $saldo_venda = (float)$stmtV->fetchColumn();
        $saldo = $saldo_cliente - $saldo_venda;
    } elseif (!empty($id_venda) && !empty($data_posicao)){
        // Interpreta data_posicao e considera o fim do dia
        $dt = DateTime::createFromFormat('Y-m-d', $data_posicao);
        if (!$dt) {
            throw new Exception("Formato de data_posicao inválido, use YYYY-MM-DD");
        }
        $data_fim = $dt->format('Y-m-d') . ' 23:59:59';

        // 1) Total de créditos (pagamentos) até data_posicao
        $stmt1 = $pdo->prepare("
            SELECT COALESCE(SUM(valor), 0) AS total_credito
            FROM venda_pagamentos
            WHERE id_cliente = :id_cliente
            AND venda = 1
            AND data_venda <= :data_fim
            AND id_venda   <  :id_venda
        ");
        $stmt1->execute([
            ':id_cliente' => $id_cliente,
            ':data_fim'   => $data_fim,
            ':id_venda'   => $id_venda,
        ]);
        $total_credito = (float) $stmt1->fetchColumn();

        // 2) Total de débitos (vendas) até data_posicao, excluindo a venda atual
        $stmt2 = $pdo->prepare("
            SELECT COALESCE(SUM(valor_final), 0) AS total_debito
              FROM venda
             WHERE id_cliente = :id_cliente
               AND tipo_venda = 'venda'
               AND data_venda <= :data_posicao
               AND id          < :id_venda
        ");
        $stmt2->execute([
            ':id_cliente'   => $id_cliente,
            ':data_posicao' => $data_posicao,
            ':id_venda'     => $id_venda,
            
        ]);
        $total_debito = (float) $stmt2->fetchColumn();

        // Saldo histórico naquela data
        $saldo = $total_credito- $total_debito;
        

    } else {
        // caso 1: só id_cliente
        $saldo = $saldo_cliente;
    }







   echo json_encode(['saldo'=>$saldo]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
}








