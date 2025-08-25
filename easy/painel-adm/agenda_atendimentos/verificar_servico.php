<?php
require_once("../../conexao.php");

// Certifique-se de que a sessão foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obter dados do POST
$idNovoProf = $_POST['idNovoProf'];
$idServAg = $_POST['idServAg'];

// Sua lógica de consulta ao banco de dados
$sql = "SELECT COUNT(*) FROM servicos_profissional 
        WHERE id_profissional = ? AND id_servico = ? AND executa = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idNovoProf, $idServAg]);
$executa = $stmt->fetchColumn() > 0;

// Retornar resultado
echo json_encode(['executa' => $executa]);
?>