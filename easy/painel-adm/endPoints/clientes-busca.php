<?php
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) session_start();

$q_raw = $_GET['q'] ?? '';
$term = trim($q_raw);
$termLower = mb_strtolower($term, 'UTF-8');

// Detecta se é só dígitos (CPF ou telefone)
$isNumeric = preg_match('/^[0-9]+$/', $term);
$params = [];
$whereClauses = [];

if ($isNumeric) {
    // CPF ou telefone (retirando caracteres especiais)
    $numTerm = $term . '%';
    $whereClauses[] = "REPLACE(REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), '(', ''), ')', '') LIKE :numTerm";
    $whereClauses[] = "REPLACE(REPLACE(REPLACE(REPLACE(celular, '(', ''), ')', ''), '-', ''), ' ', '') LIKE :numTerm";
    $params[':numTerm'] = $numTerm;
}

// Busca por nome usando FULLTEXT (in Boolean mode com wildcard *)
$whereClauses[] = "MATCH(nome) AGAINST(:nomeTerm IN BOOLEAN MODE)";
$params[':nomeTerm'] = $termLower . '*';

$whereSql = implode(' OR ', $whereClauses);
$stmt = $pdo->prepare("SELECT id, nome FROM clientes WHERE $whereSql LIMIT 50");
$stmt->execute($params);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($matches);
