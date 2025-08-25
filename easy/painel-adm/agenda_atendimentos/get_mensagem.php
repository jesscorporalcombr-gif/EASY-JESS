<?php
require_once('../../conexao.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg = '';
if ($id) {
    $stmt = $pdo->prepare("SELECT mensagem FROM agenda_mensagens WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) $msg = $row['mensagem'];
}
echo json_encode(['mensagem' => $msg]);
