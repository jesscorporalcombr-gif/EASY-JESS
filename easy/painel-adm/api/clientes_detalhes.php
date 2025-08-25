<?php
// api/clientes_detalhe.php
header('Content-Type: application/json; charset=utf-8');

// Ajuste este require para o caminho da sua conexão
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de cliente inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, nome, sexo, cpf, celular, email, foto, saldo 
          FROM clientes 
         WHERE id = :id
    ");
    $stmt->execute([':id' => $id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        http_response_code(404);
        echo json_encode(['error' => 'Cliente não encontrado']);
        exit;
    }

    echo json_encode($cliente, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => 'Erro ao buscar detalhes do cliente',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
