<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }

$token   = $_POST['token'] ?? '';
$answers = $_POST['answers_json'] ?? '';
$ip      = $_SERVER['REMOTE_ADDR'] ?? null;
$ua      = $_SERVER['HTTP_USER_AGENT'] ?? null;

if ($token==='' || $answers==='') out(false, ['error'=>'token e answers_json são obrigatórios'], 400);
$decoded = json_decode($answers, true);
if ($decoded===null && json_last_error()!==JSON_ERROR_NONE) out(false, ['error'=>'answers_json inválido'], 400);

$pdo->beginTransaction();
try {
  // Confere token + form
  $q = $pdo->prepare("SELECT t.*, f.version, JSON_EXTRACT(f.schema_json, '$') AS schema_json
                      FROM form_tokens t
                      JOIN forms f ON f.id = t.form_id
                      WHERE t.token=:tok AND t.revoked=0
                      FOR UPDATE");
  $q->execute([':tok'=>$token]);
  $tk = $q->fetch(PDO::FETCH_ASSOC);
  if(!$tk){ $pdo->rollBack(); out(false, ['error'=>'Token inválido'], 404); }

  if ($tk['expires_at'] !== null && strtotime($tk['expires_at']) < time()){
    $pdo->rollBack(); out(false, ['error'=>'Token expirado'], 410);
  }
  if ((int)$tk['max_uses'] > 0 && (int)$tk['used_count'] >= (int)$tk['max_uses']) {
    $pdo->rollBack(); out(false, ['error'=>'Token já utilizado'], 409);
  }

  // Insere response aguardando validação
  $ins = $pdo->prepare("INSERT INTO form_responses
      (form_id, form_version, clinic_id, patient_id, origin, status, answers_json, schema_json_snapshot,
       ip, user_agent, started_at, submitted_at)
      VALUES
      (:f, :v, :c, :p, 'link', 'submitted_pending_validation', CAST(:a AS JSON), CAST(:s AS JSON),
       :ip, :ua, NOW(), NOW())");
  $ins->execute([
    ':f'=>(int)$tk['form_id'],
    ':v'=>(int)$tk['version'],
    ':c'=>($tk['clinic_id'] === null ? null : (int)$tk['clinic_id']),
    ':p'=>($tk['patient_id'] === null ? null : (int)$tk['patient_id']),
    ':a'=>json_encode($decoded, JSON_UNESCAPED_UNICODE),
    ':s'=>$tk['schema_json'],
    ':ip'=>$ip, ':ua'=>$ua
  ]);
  $response_id = (int)$pdo->lastInsertId();

  // Consome token
  $upd = $pdo->prepare("UPDATE form_tokens SET used_count = used_count + 1 WHERE id=:id");
  $upd->execute([':id'=>(int)$tk['id']]);

  $pdo->commit();
  out(true, ['response_id'=>$response_id, 'status'=>'submitted_pending_validation']);
} catch(Exception $e){
  $pdo->rollBack();
  out(false, ['error'=>$e->getMessage()], 500);
}
