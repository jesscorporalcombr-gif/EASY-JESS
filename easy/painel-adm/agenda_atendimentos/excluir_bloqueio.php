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

$id = $dados['id'];
$tpExc = $dados['tpExc'];

// ====== Buscando informações do bloqueio original ======
$sqlBusca = "SELECT id_bloqueio_lote, id_profissional_1, data FROM agendamentos WHERE id = :id LIMIT 1";
$stmtBusca = $pdo->prepare($sqlBusca);
$stmtBusca->bindValue(':id', $id);
$stmtBusca->execute();
$info = $stmtBusca->fetch(PDO::FETCH_ASSOC);

if (!$info) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Registro não encontrado.']);
    exit;
}

$idLote = $info['id_bloqueio_lote'];
$idProf = $info['id_profissional_1'];
$dataBase = $info['data'];

// ====== EXCLUSÃO ======
try {
    if ($tpExc === '1') {
        // Exclui só o registro pelo ID
        $sql = "DELETE FROM agendamentos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([':id' => $id]);
    } elseif ($tpExc === '1+post') {
        // Exclui o registro e todos do mesmo lote e prof, com data > dataBase
        $sql = "DELETE FROM agendamentos 
                WHERE (id = :id) 
                OR (id_bloqueio_lote = :idLote AND id_profissional_1 = :idProf AND data > :dataBase)";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':id' => $id,
            ':idLote' => $idLote,
            ':idProf' => $idProf,
            ':dataBase' => $dataBase
        ]);
    } elseif ($tpExc === 'allOth') {
        // Exclui todos do mesmo lote e prof
        $sql = "DELETE FROM agendamentos 
                WHERE id_bloqueio_lote = :idLote AND id_profissional_1 = :idProf";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':idLote' => $idLote,
            ':idProf' => $idProf
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tipo de exclusão inválido.']);
        exit;
    }

    if ($ok) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Bloqueio(s) excluído(s) com sucesso!']);
    } else {
        $error = $stmt->errorInfo();
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir bloqueio.', 'erroPDO' => $error]);
    }
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}
