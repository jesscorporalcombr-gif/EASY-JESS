<?php
require_once('../../conexao.php');
//$data = $_GET['data'] ?? '';
$retorno = [];


    $sql = "SELECT * FROM agenda_lembrete_padrao";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $retorno = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($retorno);