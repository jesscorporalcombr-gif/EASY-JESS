<?php
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) session_start();

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT id, nome, aniversario, celular, email, sexo, cpf, foto, data_cadastro
     FROM clientes
     WHERE id = :id LIMIT 1"
);
$stmt->execute([':id' => $id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($cliente);
