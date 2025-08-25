<?php
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$id_usuario = $_SESSION['id_usuario'];
$usuario    = $_SESSION['nome_usuario'];
$created = date('Y-m-d H:i:s');

// 1. Receber e decodificar JSON
$dados = json_decode(file_get_contents('php://input'), true);
if (!$dados) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}






$bloqueios = $dados['bloqueios'] ?? [];
$intervalo = $dados['intervalo'] ?? [];
$diasSemana = $dados['dias'] ?? [];
$idBloqueioCar = $dados['idBloqueioCar'] ?? '';
$titBloqueio = $dados['titBloqueio']?? '';
$descBloqueio = $dados['descBloqueio']?? '';

// 2. Bloqueio contra múltiplas inserções do mesmo user em 3s
$limite = date('Y-m-d H:i:s', time() - 3);
$sqlVerifica = "SELECT COUNT(*) FROM agendamentos WHERE id_user_criacao = :id_usuario AND data_criacao >= :limite";
$stmt = $pdo->prepare($sqlVerifica);
$stmt->bindValue(':id_usuario', $id_usuario);
$stmt->bindValue(':limite', $limite);
$stmt->execute();
$total = $stmt->fetchColumn();
if ($total > 0) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Você acabou de inserir um agendamento. Aguarde alguns segundos.'
    ]);
    exit;
}

// ======= ALTERAÇÃO (UPDATE) =======
if ($idBloqueioCar && count($bloqueios) === 1) {
    $b = $bloqueios[0];
    $sql = "UPDATE agendamentos 
               SET 
                   hora = :hora,
                   tempo_min = :tempo,
                   observacoes = :descricao,
                   user_alteracao = :usuario,
                   data_alteracao = :data_alteracao,
                   id_user_alteracao = :id_usuario,
                   titulo_bloqueio = :titulo_bloqueio
             WHERE id = :id_bloqueio 
               AND id_profissional_1 = :id_profissional";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        
        ':hora' => $b['horaInicio'].":00",
        ':tempo' => $b['tempo'],
        ':descricao' => $descBloqueio,
        ':usuario' => $usuario,
        ':data_alteracao' => $created,
        ':id_usuario' => $id_usuario,
        ':titulo_bloqueio' => $titBloqueio,
        ':id_bloqueio' => $idBloqueioCar,
        ':id_profissional' => $b['idProfissional']
    ]);
    echo json_encode(['sucesso' => $ok]);
    exit;
}





// ======= INSERÇÃO EM LOTE =======
if (empty($bloqueios) || empty($intervalo)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
    exit;
}



$dataInicio = $intervalo['inicio'];
$dataFim = $intervalo['fim'];


$datas = [];
$start = new DateTime($dataInicio);
$end = new DateTime($dataFim);
$end->modify('+1 day'); // inclui a data final no loop





if (!empty($diasSemana)) {
    // Intervalo com dias da semana marcados
    for ($date = clone $start; $date < $end; $date->modify('+1 day')) {
        $dayOfWeek = (int)$date->format('w');
        if (in_array($dayOfWeek, $diasSemana)) {
            $datas[] = $date->format('Y-m-d');
        }
    }
} else {
    // Se não há dias da semana (ex: bloqueio pontual), grava todas as datas do intervalo
    for ($date = clone $start; $date < $end; $date->modify('+1 day')) {
        $datas[] = $date->format('Y-m-d');
    }
}



if (count($bloqueios) === 1 && count($datas) === 1) {
    $idLote = ''; // ou NULL, conforme seu banco
} else {
    $idLote = uniqid('bloq_', true);
}




$inserts = 0;
foreach ($bloqueios as $b) {
    // Buscar nome do profissional
    $stmtProf = $pdo->prepare("SELECT nome FROM cadastro_colaboradores WHERE id = ?");
    $stmtProf->execute([$b['idProfissional']]);
    $nomeProf = $stmtProf->fetchColumn();

    foreach ($datas as $data) {
        $sql = "INSERT INTO agendamentos (
                    data, hora, profissional_1, id_profissional_1, tempo_min, observacoes, bloqueio, id_bloqueio_lote,
                    user_criacao, data_criacao, id_user_criacao, titulo_bloqueio
                ) VALUES (
                    :data, :hora, :profissional_1, :id_profissional_1, :tempo_min, :observacoes, 1, :id_bloqueio_lote,
                    :user_criacao, :data_criacao, :id_user_criacao, :titulo_bloqueio
                )";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':data' => $data,
            ':hora' => $b['horaInicio'].":00",
            ':profissional_1' => $nomeProf,
            ':id_profissional_1' => $b['idProfissional'],
            ':tempo_min' => $b['tempo'],
            ':observacoes' => $descBloqueio,
            ':id_bloqueio_lote' => $idLote,
            ':user_criacao' => $usuario,
            ':data_criacao' => $created,
            ':id_user_criacao' => $id_usuario,
            ':titulo_bloqueio' => $titBloqueio
        ]);
        if ($ok) $inserts++;
    }
}
echo json_encode(['sucesso' => true, 'total' => $inserts, 'idLote' => $idLote]);
