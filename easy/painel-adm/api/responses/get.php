<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_GET['response_id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'response_id obrigatório']); exit; }

try {
  $st = $pdo->prepare("SELECT r.*, JSON_EXTRACT(r.answers_json, '$') AS answers, JSON_EXTRACT(r.schema_json_snapshot, '$') AS schema_snap
                       FROM form_responses r
                       WHERE r.id = :id");
  $st->execute([':id'=>$id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if (!$row) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'Resposta não encontrada']); exit; }

  echo json_encode(['ok'=>true, 'response'=>[
    'id'          => (int)$row['id'],
    'form_id'     => (int)$row['form_id'],
    'form_version'=> (int)$row['form_version'],
    'patient_id'  => $row['patient_id'],
    'status'      => $row['status'],
    'answers'     => $row['answers'],
    'schema_json' => $row['schema_snap'] ?: null // se nulo, podemos cair no schema atual do form
  ]]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
