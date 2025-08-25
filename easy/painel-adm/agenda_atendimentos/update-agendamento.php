<?php 

require_once("../../conexao.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificase o usuário está autenticado e autorizado a fazer a alteração aqui

$created = date('Y-m-d H:i:s');
$id_usuario = $_SESSION['id_usuario']; // Certifica se de que o usuário está definido e é válido
$usuario = $_SESSION['nom_usuario']; //  deve puxar isso de $_SESSION['usuario']

$id_agendamento = $_POST['id_agendamento'];
$id_profissional = $_POST['id_profissional'];
$profissional = $_POST['profissional'];
$hora_agenda = $_POST['hora_agenda'];
$hora_fimAgenda = '00:00:00'; //$_POST['hora_fim']; coloquir zero para pedir para finalizar novamente

 //Validação dos valores recebidos
if (!is_numeric($id_agendamento) || !is_numeric($id_profissional) || !preg_match("/^\d{2}:\d{2}:\d{2}$/", $hora_agenda)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}


// Verificar se existe atendimento FINALIZADO ou vinculado a venda
$stmt = $pdo->prepare("
    SELECT id 
    FROM atendimentos 
    WHERE id_agendamento = ?
      AND (
            status = 'Finalizado'
         OR (
                id_item_servico IS NOT NULL
            AND id_item_servico != 0
            AND id_item_servico != ''
            )
        )
    LIMIT 1
");
$stmt->execute([$id_agendamento]);
$bloqueado = $stmt->fetchColumn();

if ($bloqueado) {
    echo json_encode(['success' => false, 'error' => 'Já existe atendimento finalizado ou vinculado a venda para esse agendamento.']);
    exit;
}

// Verificar se existe atendimento NÃO finalizado para esse agendamento
$stmt = $pdo->prepare("
    SELECT id 
    FROM atendimentos 
    WHERE id_agendamento = ? 
      AND status != 'Finalizado'
    LIMIT 1
");
$stmt->execute([$id_agendamento]);
$idAtendimento = $stmt->fetchColumn();

if ($idAtendimento) {
    // Atualiza os campos que vieram via POST no atendimento
    $stmtUp = $pdo->prepare("
        UPDATE atendimentos
        SET 
            hora_inicio = :hora_inicio,
            hora_fim    = :hora_fim,
            profissional_1 = :profissional_1,
            id_profissional_1 = :id_profissional_1,
            id_user_alteracao = :id_user_alteracao,
            user_alteracao = :user_alteracao,
            data_alteracao = :data_alteracao
        WHERE id = :id_atendimento
    ");
    $stmtUp->execute([
        ':hora_inicio' => $hora_agenda,
        ':hora_fim'    => $hora_fimAgenda,
        ':profissional_1' => $profissional,
        ':id_profissional_1' => $id_profissional,
        ':id_user_alteracao' => $id_usuario,
        ':user_alteracao'    => $usuario,
        ':data_alteracao'    => $created,
        ':id_atendimento'    => $idAtendimento
    ]);
}




try {
    $res = $pdo->prepare("UPDATE agendamentos SET 
        id_profissional_1 = :id_profissional_1, 
        profissional_1 = :profissional_1, 
        hora = :hora, 
        user_alteracao = :user_alteracao, 
        id_user_alteracao = :id_user_alteracao, 
        data_alteracao = :data_alteracao 
        WHERE id = :id");

    $res->bindValue(":id_profissional_1", $id_profissional);
    $res->bindValue(":profissional_1", $profissional);
    $res->bindValue(":hora", $hora_agenda);
    $res->bindValue(":user_alteracao", $usuario);
    $res->bindValue(":id_user_alteracao", $id_usuario);
    $res->bindValue(":data_alteracao", $created);
    $res->bindValue(":id", $id_agendamento);

    $success = $res->execute();

    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
