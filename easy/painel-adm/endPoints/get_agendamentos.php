<?php 
require_once("../../conexao.php");

// Certifique-se de que a sessão foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Aqui você recupera a data enviada pelo método GET
$dataAgenda = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d'); // Se não for fornecida, usa a data atual

// Preparando a consulta SQL para buscar apenas os agendamentos da data específica
$query = $pdo->prepare("SELECT * FROM agendamentos WHERE data = :dataAgenda");
$query->bindParam(':dataAgenda', $dataAgenda);
$query->execute();
$agendamentosDoDia = $query->fetchAll(PDO::FETCH_ASSOC);

// Devolvendo os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($agendamentosDoDia);
?>
