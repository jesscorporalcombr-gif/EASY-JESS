<?php
@session_start();
require_once('../../conexao.php');

// Recebe dados via POST
$id       = isset($_POST['id']) ? trim($_POST['id']) : '';
$nome     = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';
$mostrar_menu = isset($_POST['mostrar_menu']) ? intval($_POST['mostrar_menu']) : 0;
//echo 'mostrar_menu: ' . $mostrar_menu;
// Validação simples
if (!$nome || !$mensagem) {
    echo json_encode(['success' => false, 'msg' => 'Preencha todos os campos.']);
    exit;
}

try {
    if ($id) {
        // Atualizar existente
        $sql = "UPDATE agenda_mensagens SET nome = :nome, mensagem = :mensagem, mostrar_menu = :mostrar_menu WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':mensagem', $mensagem);
        $stmt->bindValue(':mostrar_menu', $mostrar_menu, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Inserir novo
            $sql = "INSERT INTO agenda_mensagens (nome, mensagem, mostrar_menu) VALUES (:nome, :mensagem, :mostrar_menu)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':mensagem', $mensagem);
            $stmt->bindValue(':mostrar_menu', $mostrar_menu, PDO::PARAM_INT);
           $stmt->execute();
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => 'Erro ao salvar: ' . $e->getMessage()]);
}
?>
