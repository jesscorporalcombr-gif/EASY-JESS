<?php
require_once('../../conexao.php');
$data = $_GET['data'] ?? '';
$retorno = [];

if ($data) {
    $sql = "SELECT id_cliente, enviado FROM agenda_lembretes WHERE data = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data]);
    $retorno = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
header('Content-Type: application/json');
echo json_encode($retorno);