<?php
require_once("../../conexao.php");

header('Content-Type: application/json');
http_response_code(200);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agendamento = intval($_POST['id_agendamento'] ?? 0);


    $stmt = $pdo->prepare("SELECT hora_inicio, hora_fim, prontuario, status FROM atendimentos WHERE id_agendamento = ?");
    $stmt->execute([$id_agendamento]);
    $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($atendimento) {
        // Retorna dados do atendimento encontrado
        echo json_encode([
            'existe'      => true,
            'hora_inicio' => $atendimento['hora_inicio'],
            'hora_fim'    => $atendimento['hora_fim'],
            'texto_prontuario' => $atendimento['prontuario'],
            'status'      => $atendimento['status']
        ]);
    } else {
        // Não existe registro ainda
        echo json_encode([
            'existe'      => false,
            'hora_inicio' => null,
            'hora_fim'    => null,
            'texto_prontuario' => null,
            'status'      => null
        ]);
    }
    exit;
}

// Se não for POST
http_response_code(400);
echo json_encode(['erro' => 'Requisição inválida.']);