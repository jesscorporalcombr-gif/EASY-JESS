<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }

$response_id = (int)($_POST['response_id'] ?? 0);
$patient_id  = $_POST['patient_id'] ?? null;

if ($response_id<=0) out(false, ['error'=>'response_id obrigatório'], 400);

$st = $pdo->prepare("UPDATE form_responses
                     SET status='validated',
                         patient_id = COALESCE(:p, patient_id),
                         validated_at = NOW(),
                         validated_by = :u
                     WHERE id=:id");
$st->execute([
  ':p'=>($patient_id===''?null:$patient_id),
  ':u'=>($_SESSION['user_id'] ?? null),
  ':id'=>$response_id
]);

echo json_encode(['ok'=>true]);
