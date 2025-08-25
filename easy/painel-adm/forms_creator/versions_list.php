<?php
@session_start();
header('Content-Type: application/json');
require_once('../../conexao.php');
$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
if(!$form_id){ echo json_encode(['ok'=>false,'error'=>'form_id inválido']); exit; }
$stmt = $pdo->prepare('SELECT id as version_id, versao, status, published_at FROM form_versions WHERE form_id=? ORDER BY versao ASC');
$stmt->execute([$form_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'rows'=>$rows]);