<?php
// api/servicos_disponiveis.php

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ERROR);


















// Ajuste este require para o caminho da sua conexão
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id_cliente = isset($_GET['id_cliente']) ? (int) $_GET['id_cliente'] : 0;
if (!$id_cliente) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de cliente inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
          id,
          id_venda,
          data_venda,
          tipo_venda,
          item,
          id_item,
          precoUn_efetivo,
          (quantidade 
            - COALESCE(convertidos,0) 
            - COALESCE(realizados,0) 
            - COALESCE(transferidos,0) 
            - COALESCE(descontados,0)
          ) AS disponiveis
        FROM venda_itens
        WHERE id_cliente    = :id_cliente
          AND tipo_item     = 'servico'
          AND (data_validade >= CURDATE() OR data_validade IS NULL OR data_validade = '')
        HAVING disponiveis > 0
        ORDER BY item ASC
    ");
    $stmt->execute([':id_cliente' => $id_cliente]);
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($servicos, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
      'error'   => 'Erro ao buscar serviços',
      'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
