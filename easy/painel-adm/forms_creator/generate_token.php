<?php
@session_start();
header('Content-Type: application/json');
try{
  require_once('../../conexao.php');
  $in = json_decode(file_get_contents('php://input'), true) ?: [];
  $form_id = (int)($in['form_id'] ?? 0);
  $hours = max(1, (int)($in['expires_in_hours'] ?? 72));
  if(!$form_id) { echo json_encode(['ok'=>false,'error'=>'form_id inválido']); exit; }

  // garante que existe versão publicada
  $stmt = $pdo->prepare("SELECT id FROM form_versions WHERE form_id=? AND status='published' ORDER BY versao DESC LIMIT 1");
  $stmt->execute([$form_id]);
  $vid = (int)$stmt->fetchColumn();
  if(!$vid){ echo json_encode(['ok'=>false,'error'=>'Formulário sem versão publicada']); exit; }

  $token = bin2hex(random_bytes(16));
  $pdo->prepare("INSERT INTO form_tokens (form_id, version_id, token, expires_at, created_at)
                 VALUES (?,?,?,?,NOW())")
      ->execute([$form_id, $vid, $token, date('Y-m-d H:i:s', time()+$hours*3600)]);

  // ajuste a URL base se necessário
  $url = sprintf('%s://%s%s/forms/fill_external.php?t=%s',
    (!empty($_SERVER['HTTPS'])?'https':'http'),
    $_SERVER['HTTP_HOST'],
    rtrim(dirname($_SERVER['PHP_SELF']), '/'),
    $token
  );

  echo json_encode(['ok'=>true,'token'=>$token,'expires_at'=>date('Y-m-d H:i:s', time()+$hours*3600),'url'=>$url]);
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
