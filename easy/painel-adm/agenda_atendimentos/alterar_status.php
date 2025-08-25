<?php 
require_once("../../conexao.php");
//require_once('verificar-permissao.php');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Captura e sanitiza os dados recebidos
$id_agendamento = intval($_POST['id_agendamento']);
$status = trim($_POST['status']);

$descricao = $_POST['descricao'];

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



if (!isset($_POST['id_agendamento'], $_POST['status'])) {
    echo 'Erro: Dados incompletos.';
    exit;
}



// Validação básica
if (empty($id_agendamento) || empty($status)) {
    echo 'Erro: ID ou status inválido.';
    exit;
}



















if ($status=='Cancelado' || $status=='Faltou' || $status=='NRealizado'){
    try {
        $stmt = $pdo->prepare("SELECT id, id_item_servico FROM atendimentos WHERE id_agendamento = ?");
        $stmt->execute([$id_agendamento]);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ids_atendimentos = array_column($atendimentos, 'id');


        if ($ids_atendimentos) {
            $placeholders = implode(',', array_fill(0, count($ids_atendimentos), '?'));

            // Busca os arquivos vinculados
            $stmt = $pdo->prepare("SELECT nome_completo_arquivo FROM atendimentos_arquivos WHERE id_atendimento IN ($placeholders)");
            $stmt->execute($ids_atendimentos);
            $arquivos = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Exclui cada arquivo físico do disco (ajuste o caminho base se necessário)
            if ($arquivos){
                foreach ($arquivos as $arquivo) {
                    if ($arquivo && file_exists($arquivo)) {
                        unlink($arquivo); // Remove o arquivo físico
                    }
                }
            }

            // Agora sim, exclui do banco
            $stmt = $pdo->prepare("DELETE FROM atendimentos_arquivos WHERE id_atendimento IN ($placeholders)");
            $stmt->execute($ids_atendimentos);

            $stmt = $pdo->prepare("DELETE FROM atendimentos WHERE id IN ($placeholders)");
            $stmt->execute($ids_atendimentos);
            foreach ($atendimentos as $at) {
                if ($at['id_item_servico'] && $at['id_item_servico'] != '0') {
                    $stmt = $pdo->prepare("UPDATE venda_itens SET realizados = realizados - 1 WHERE id = ?");
                    $stmt->execute([$at['id_item_servico']]);
                }
            }



        }

    
        // Prepara o update
        $query = $pdo->prepare("UPDATE agendamentos SET 
            status = :status,
            observacoes = :descricao,
            id_user_alteracao = :id_user,
            user_alteracao = :user_nome,
            data_alteracao = :data_alteracao
            WHERE id = :id_agendamento
        ");

        // Executa
        $query->execute([
            ':status' => $status,
            ':descricao' => $descricao,
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


}



?>