<?php
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

$clinic_id = $_GET['clinic_id'] ?? null;
$q = "SELECT id, name, version, is_active, updated_at FROM forms";
$params = [];
if ($clinic_id !== null && $clinic_id !== '') { $q .= " WHERE clinic_id=:c"; $params[':c']=$clinic_id; }
$q .= " ORDER BY updated_at DESC";

$stmt = $pdo->prepare($q);
$stmt->execute($params);
echo json_encode(['ok'=>true, 'rows'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
