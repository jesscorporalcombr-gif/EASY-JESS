<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');
function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }

$form_id     = (int)($_POST['form_id'] ?? 0);
$clinic_id   = $_POST['clinic_id'] ?? null;
$patient_id  = $_POST['patient_id'] ?? null;
$patient_ref = $_POST['patient_ref'] ?? null;
$status      = $_POST['status'] ?? 'completed';
$answers     = $_POST['answers_json'] ?? '';

if ($form_id<=0 || $answers==='') out(false, ['error'=>'form_id e answers_json são obrigatórios'], 400);
$decoded = json_decode($answers, true);
if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) out(false, ['error'=>'answers_json inválido'], 400);

$ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? null;

$stmt = $pdo->prepare("INSERT INTO form_responses (form_id, clinic_id, patient_id, patient_ref, status, answers_json, submitted_at, ip, user_agent)
                       VALUES (:f, :c, :p, :pr, :s, CAST(:a AS JSON), NOW(), :ip, :ua)");
$stmt->execute([
  ':f'=>$form_id,
  ':c'=>($clinic_id===''?null:$clinic_id),
  ':p'=>($patient_id===''?null:$patient_id),
  ':pr'=>($patient_ref===''?null:$patient_ref),
  ':s'=>in_array($status,['in_progress','completed'])?$status:'completed',
  ':a'=>$answers,
  ':ip'=>$ip, ':ua'=>$ua
]);

echo json_encode(['ok'=>true, 'response_id'=>(int)$pdo->lastInsertId()]);
