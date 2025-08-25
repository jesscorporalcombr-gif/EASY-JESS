<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

$response_id = (int)($_POST['response_id'] ?? 0);
$do = $_POST['action'] ?? 'archive'; // 'archive' | 'restore'
if ($response_id<=0) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'response_id obrigatório']); exit; }

$status = ($do==='restore') ? 'validated' : 'archived';

$st = $pdo->prepare("UPDATE form_responses SET status=:s WHERE id=:id");
$st->execute([':s'=>$status, ':id'=>$response_id]);

echo json_encode(['ok'=>true, 'status'=>$status]);