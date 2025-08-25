<?php
@session_start();
header('Content-Type: application/json');
require_once('../../conexao.php');

$raw = file_get_contents('php://input');
$in  = json_decode($raw,true);
$form_id = (int)($in['form_id'] ?? 0);
$from_version_id = (int)($in['from_version_id'] ?? 0);
if(!$form_id || !$from_version_id){ echo json_encode(['ok'=>false,'error'=>'Parâmetros inválidos']); exit; }

try{
  $pdo->beginTransaction();
  $src = $pdo->prepare('SELECT versao, schema_json FROM form_versions WHERE id=? AND form_id=? FOR UPDATE');
  $src->execute([$from_version_id,$form_id]);
  $row = $src->fetch(PDO::FETCH_ASSOC);
  if(!$row) throw new Exception('Versão origem não encontrada');

  $nextV = (int)$row['versao'] + 1;
  $ins = $pdo->prepare('INSERT INTO form_versions (form_id,versao,status,schema_json,created_at) VALUES (?,?,?,?,NOW())');
  $ins->execute([$form_id,$nextV,'draft',$row['schema_json']]);
  $version_id = (int)$pdo->lastInsertId();

  $pdo->commit();
  echo json_encode(['ok'=>true,'version_id'=>$version_id,'versao'=>$nextV,'status'=>'draft']);
}catch(Exception $e){ if($pdo->inTransaction()) $pdo->rollBack(); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); }