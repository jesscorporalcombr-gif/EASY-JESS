<?php 
require_once("../../conexao.php");
//require_once('verificar-permissao.php');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Verifique e sanitize o ID do profissional
    $idProfissional = isset($_GET['id_profissional']) ? (int)$_GET['id_profissional'] : 0;

    // Preparar consulta SQL para buscar os serviços e suas informações de preço e tempo
    $sql = "SELECT sp.id_servico, s.servico, sp.tempo, sp.preco
            FROM servicos_profissional sp
            JOIN servicos s ON sp.id_servico = s.id AND s.excluido <> 1
            WHERE sp.executa = 1 AND sp.id_profissional = :idProfissional";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idProfissional', $idProfissional, PDO::PARAM_INT);
    $stmt->execute();

    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar os serviços e informações como JSON
    echo json_encode($servicos);

} catch (PDOException $e) {
    // Em um cenário de produção, você deve lidar com o erro de maneira adequada
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>