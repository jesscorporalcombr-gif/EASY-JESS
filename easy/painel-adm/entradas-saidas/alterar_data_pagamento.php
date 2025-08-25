<?php
require_once(__DIR__ . "/../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');

// usuário e timestamp para auditoria
$id_user_alteracao = intval($_SESSION['id_usuario']);
$user_alteracao    = $_SESSION['nome_usuario'];
$data_hora         = date('Y-m-d H:i:s');

// lê dados do corpo JSON
$input    = json_decode(file_get_contents('php://input'), true);
$ids      = $input['ids'] ?? [];
$novaData = $input['novaData'] ?? '';

// validações básicas
if (!is_array($ids) || empty($ids)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nenhum ID fornecido']);
    exit;
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $novaData)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Data inválida']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1) verifica se algum dos IDs é transferência
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmtCheck = $pdo->prepare("
        SELECT COUNT(*) 
        FROM financeiro_extrato
        WHERE id IN ($placeholders)
          AND transferencia = 1
    ");
    $stmtCheck->execute($ids);
    $cntTrans = (int)$stmtCheck->fetchColumn();
    if ($cntTrans > 0) {
        $pdo->rollBack();
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error'   => 'Não é permitido alterar data de pagamentos de transferências.'
        ]);
        exit;
    }

    // 2) monta UPDATE com placeholders nomeados para os IDs
    $named = [];
    $params = [
        ':novaData'           => $novaData,
        ':id_user_alteracao'  => $id_user_alteracao,
        ':user_alteracao'     => $user_alteracao,
        ':data_hora'          => $data_hora
    ];
    foreach ($ids as $i => $id) {
        $ph = ":id{$i}";
        $named[]         = $ph;
        $params[$ph]     = (int)$id;
    }

    $sql = "
        UPDATE financeiro_extrato
        SET data_pagamento    = :novaData,
            id_user_alteracao = :id_user_alteracao,
            user_alteracao    = :user_alteracao,
            data_alteracao     = :data_hora
        WHERE id IN (" . implode(',', $named) . ")
    ";


    


    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $pdo->commit();

    echo json_encode([
        'success'     => true,
        'updated_ids' => $ids,
        'new_date'    => $novaData
    ]);
} catch (\Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Erro ao alterar data: ' . $e->getMessage()
    ]);
}
