<?php 
require_once("../../conexao.php");

// Certifique-se de que a sessão foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Consulta para buscar os dados específicos de todos os clientes
$query = $pdo->query("SELECT id, nome, aniversario, celular, email, sexo, cpf, foto, saldo FROM clientes");

// Fetch all rows at once
$clientes = $query->fetchAll(PDO::FETCH_ASSOC);

// Devolvendo os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($clientes);
?>