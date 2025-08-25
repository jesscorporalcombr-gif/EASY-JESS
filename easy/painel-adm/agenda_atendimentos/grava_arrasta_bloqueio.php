<?php
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$id_usuario = $_SESSION['id_usuario'];
$usuario    = $_SESSION['nome_usuario'];
$data_alteracao = date('Y-m-d H:i:s');

// Recebe via POST (FormData)
$id_agendamento  = $_POST['id_agendamento']  ?? '';
$id_profissional = $_POST['id_profissional'] ?? '';
$profissional    = $_POST['profissional']    ?? '';
$hora_agenda     = $_POST['hora_agenda']     ?? '';

// Validação simples
if (!$id_agendamento || !$id_profissional || !$profissional || !$hora_agenda) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados obrigatórios ausentes!']);
    exit;
}

try {
    $sql = "UPDATE agendamentos
               SET id_profissional_1 = :id_profissional,
                   profissional_1 = :profissional,
                   hora = :hora,
                   user_alteracao = :usuario,
                   data_alteracao = :data_alteracao,
                   id_user_alteracao = :id_usuario
             WHERE id = :id_agendamento";

    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        ':id_profissional'   => $id_profissional,
        ':profissional'      => $profissional,
        ':hora'              => $hora_agenda . ":00",
        ':usuario'           => $usuario,
        ':data_alteracao'    => $data_alteracao,
        ':id_usuario'        => $id_usuario,
        ':id_agendamento'    => $id_agendamento
    ]);

    if ($ok) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Bloqueio alterado com sucesso!']);
    } else {
        $error = $stmt->errorInfo();
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar bloqueio.', 'erroPDO' => $error]);
    }
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}