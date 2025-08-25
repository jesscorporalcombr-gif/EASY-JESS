<?php
require_once('../../conexao.php');

$data = json_decode(file_get_contents('php://input'), true);

$id_cliente = $data['id_cliente'] ?? null;
$data_ = $data['data'] ?? null;
$enviado = $data['enviado'] ?? 0;

if ($id_cliente && $data_) {
    // Verifica se já existe
    $sql = "SELECT COUNT(*) FROM agenda_lembretes WHERE id_cliente = ? AND data = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_cliente, $data_]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        // Atualiza
        $sql = "UPDATE agenda_lembretes SET enviado = ? WHERE id_cliente = ? AND data = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$enviado, $id_cliente, $data_]);
    } else {
        // Insere
        $sql = "INSERT INTO agenda_lembretes (id_cliente, data, enviado) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cliente, $data_, $enviado]);
    }
    echo json_encode(['success' => true]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'msg' => 'Dados insuficientes']);
}
