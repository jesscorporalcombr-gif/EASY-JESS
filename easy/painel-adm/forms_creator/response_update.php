<?php
@session_start();
header('Content-Type: application/json');
try{
  require_once(__DIR__ . '/../../conexao.php');
  $in = json_decode(file_get_contents('php://input'), true) ?: [];

  $form_id = (int)($in['form_id'] ?? 0);
  $response_id = (int)($in['response_id'] ?? 0);
  $version_id = (int)($in['schema_version_id'] ?? 0);
  $answers = $in['answers_json'] ?? [];

  if(!$form_id || !$version_id){ echo json_encode(['ok'=>false,'error'=>'Parâmetros inválidos']); exit; }

  if($response_id){
    // update
    $stmt = $pdo->prepare("UPDATE form_responses SET answers_json=:a, status='pending', updated_at=NOW() WHERE id=:id AND excluido=0");
    $stmt->execute([':a'=>json_encode($answers, JSON_UNESCAPED_UNICODE), ':id'=>$response_id]);
    echo json_encode(['ok'=>true,'response_id'=>$response_id]);
  } else {
    // insert novo draft -> pending
    $stmt = $pdo->prepare("INSERT INTO form_responses (form_id, version_id, paciente_id, answers_json, status, source, created_at, excluido)
                           VALUES (:f,:v,NULL,:a,'pending','internal',NOW(),0)");
    $stmt->execute([':f'=>$form_id, ':v'=>$version_id, ':a'=>json_encode($answers, JSON_UNESCAPED_UNICODE)]);
    echo json_encode(['ok'=>true,'response_id'=>(int)$pdo->lastInsertId()]);
  }
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
