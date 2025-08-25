<?php
@session_start();
header('Content-Type: application/json');
try{
  require_once('../../conexao.php');
  $in = json_decode(file_get_contents('php://input'), true) ?: [];
  $form_id = (int)($in['form_id'] ?? 0);
  $paciente_id = (int)($in['paciente_id'] ?? 0);
  if(!$form_id){ echo json_encode(['ok'=>false,'error'=>'form_id inválido']); exit; }

  // pegar versão publicada mais recente
  $stmt = $pdo->prepare("SELECT id FROM form_versions WHERE form_id=? AND status='published' ORDER BY versao DESC LIMIT 1");
  $stmt->execute([$form_id]);
  $vid = (int)$stmt->fetchColumn();
  if(!$vid){ echo json_encode(['ok'=>false,'error'=>'Formulário sem versão publicada']); exit; }

  $pdo->prepare("INSERT INTO form_responses (form_id, version_id, paciente_id, status, source, created_at, excluido)
                 VALUES (?,?,?,?,?,NOW(),0)")
      ->execute([$form_id, $vid, $paciente_id ?: null, 'draft', 'internal']);

  $rid = (int)$pdo->lastInsertId();

  echo json_encode(['ok'=>true,'response_id'=>$rid,'version_id'=>$vid]);
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
//teste comit
}
