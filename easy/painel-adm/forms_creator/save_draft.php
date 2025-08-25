<?php
// /easy/painel-adm/api/forms_creator/save_draft.php
@session_start();
header('Content-Type: application/json');
require_once('../../conexao.php');
// opcional: require_once(__DIR__.'/../../lib/auth.php');

$raw = file_get_contents('php://input');
$in  = json_decode($raw,true);

$version_id = isset($in['version_id']) ? (int)$in['version_id'] : 0;
$schema_json = isset($in['schema_json']) ? json_encode($in['schema_json'], JSON_UNESCAPED_UNICODE) : null;

if(!$version_id || !$schema_json){ echo json_encode(['ok'=>false,'error'=>'Parâmetros inválidos']); exit; }

// validação mínima do schema (deve ter sections array)
$schema = json_decode($schema_json,true);
if(!is_array($schema) || !isset($schema['sections']) || !is_array($schema['sections'])){
  echo json_encode(['ok'=>false,'error'=>'Schema inválido']); exit;
}

try{
  $pdo->beginTransaction();
  // checa status da versão
  $st = $pdo->prepare('SELECT status FROM form_versions WHERE id = ? FOR UPDATE');
  $st->execute([$version_id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if(!$row){ throw new Exception('Versão não encontrada'); }
  if($row['status']!=='draft'){ throw new Exception('Apenas versões draft podem ser salvas'); }

  $up = $pdo->prepare('UPDATE form_versions SET schema_json = ?, updated_at = NOW() WHERE id = ?');
  $up->execute([$schema_json, $version_id]);

  $pdo->commit();
  echo json_encode(['ok'=>true,'status'=>'draft']);
}catch(Exception $e){
  if($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}