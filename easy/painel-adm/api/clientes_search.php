<?php
// api/clientes_search.php
header('Content-Type: application/json; charset=utf-8');

// Ajuste este require para o caminho da sua conexão
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

try {
    // Recebe e sanitiza o termo de busca
    $termo = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Prepara e executa a query
    $stmt = $pdo->prepare(
        "SELECT id, nome 
         FROM clientes 
         WHERE nome LIKE :search 
         ORDER BY nome ASC 
         LIMIT 10"
    );
    $stmt->execute([':search' => "%{$termo}%"]);

    // Fetch e retorno em JSON
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clientes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro ao buscar clientes',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
