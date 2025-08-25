<?php 
require_once("../../conexao.php");
//require_once('verificar-permissao.php');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}





// Captura e sanitiza os dados recebidos
$id_agendamento = intval($_POST['id_agendamento']);
$equipamento = $_POST['equipamento'];
$id_equipamento = $_POST['id_equipamento'];

// Dados da sessão do usuário
$id_usuario = $_SESSION['id_usuario'] ?? null;
$nome_usuario = $_SESSION['nome_usuario'] ?? null;
$data_alteracao = date('Y-m-d H:i:s');


// Cria um horário-limite de 3 segundos atrás
$limite = date('Y-m-d H:i:s', time() - 3);

// 1) Verifica se já existe registro do mesmo usuário nos últimos 3 segundos
$sqlVerifica = "
    SELECT COUNT(*) 
    FROM agendamentos
    WHERE id_user_criacao = :id_usuario
      AND data_criacao >= :limite
";
$stmt = $pdo->prepare($sqlVerifica);
$stmt->bindValue(':id_usuario', $id_usuario);
$stmt->bindValue(':limite', $limite);
$stmt->execute();
$total = $stmt->fetchColumn();

if ($total > 0) {
    // Já tem registro nos últimos 3s
    echo json_encode([
        'success' => false,
        'message' => 'Você acabou de inserir um agendamento. Aguarde alguns segundos.'
    ]);
    exit;
}



if (!isset($_POST['id_agendamento'], $_POST['id_equipamento'])) {
    echo 'Erro: Dados incompletos.';
    exit;
}

// Validação básica
if (empty($id_agendamento) || empty($id_equipamento)) {
    echo 'Erro: ID ou status inválido.';
    exit;
}

try {
    // Prepara o update
    $query = $pdo->prepare("UPDATE agendamentos SET 
        equipamento = :equipamento,
        id_equipamento = :id_equipamento,
        id_user_alteracao = :id_user,
        user_alteracao = :user_nome,
        data_alteracao = :data_alteracao
        WHERE id = :id_agendamento
    ");

    // Executa
    $query->execute([
        ':equipamento' => $equipamento,
        ':id_equipamento' => $id_equipamento,

        ':id_user' => $id_usuario,
        ':user_nome' => $nome_usuario,
        ':data_alteracao' => $data_alteracao,
        ':id_agendamento' => $id_agendamento
    ]);

    // Verifica se alguma linha foi afetada
    if ($query->rowCount() > 0) {
        echo 'Sucesso: Status atualizado!';
    } else {
        echo 'Aviso: Nenhuma alteração feita. Verifique o ID do agendamento.';
    }

} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
    exit;
}









?>