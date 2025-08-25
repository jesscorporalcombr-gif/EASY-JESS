<?php 
require_once("../../conexao.php");

// Inicia sessão, se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pega o parâmetro idVenda da URL
$idVenda = isset($_GET['idVenda']) ? intval($_GET['idVenda']) : 0;

// Prepara e executa a query
$stmt = $pdo->prepare("
    SELECT 
        id, 
        forma, 
        condicao, 
        valor 
    FROM venda_pagamentos 
    WHERE id_venda = :idVenda
");
$stmt->execute([':idVenda' => $idVenda]);

// Busca todos os resultados como array associativo
$pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define o header e imprime o JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($pagamentos, JSON_UNESCAPED_UNICODE);
?>