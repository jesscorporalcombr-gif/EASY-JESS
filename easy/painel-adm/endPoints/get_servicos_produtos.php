<?php
require_once('../../conexao.php');

$query_servicos = $pdo->query("SELECT id, servico, valor_venda FROM servicos");
$servicos = $query_servicos->fetchAll(PDO::FETCH_ASSOC);

$query_produtos = $pdo->query("SELECT id, nome, valor FROM produtos");
$produtos = $query_produtos->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['servicos' => $servicos, 'produtos' => $produtos]);
?>
