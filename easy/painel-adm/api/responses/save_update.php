<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }

$response_id = (int)($_POST['response_id'] ?? 0);
$status      = $_POST['status'] ?? 'validated'; // interno: pode já validar
$answers     = $_POST['answers_json'] ?? '';

if ($response_id<=0 || $answers==='') out(false, ['error'=>'response_id e answers_json são obrigatórios'], 400);
$decoded = json_decode($answers, true);
if ($decoded===null && json_last_error()!==JSON_ERROR_NONE) out(false, ['error'=>'answers_json inválido'], 400);

try {
  $st = $pdo->prepare("UPDATE form_responses
                       SET answers_json = CAST(:a AS JSON),
                           status = :s,
                           submitted_at = NOW(),
                           updated_at = NOW()
                       WHERE id = :id");
  $st->execute([
    ':a'=>json_encode($decoded, JSON_UNESCAPED_UNICODE),
    ':s'=>in_array($status, ['in_progress','submitted_pending_validation','validated','archived']) ? $status : 'validated',
    ':id'=>$response_id
  ]);
  out(true, ['response_id'=>$response_id, 'status'=>$status]);
} catch (Exception $e) {
  out(false, ['error'=>$e->getMessage()], 500);
}
