<?php
// /easy/painel-adm/api/forms_creator/publish.php
@session_start();
header('Content-Type: application/json');
require_once('../../conexao.php');

$raw = file_get_contents('php://input');
$in  = json_decode($raw,true);

$version_id = isset($in['version_id']) ? (int)$in['version_id'] : 0;
$schema_json = isset($in['schema_json']) ? json_encode($in['schema_json'], JSON_UNESCAPED_UNICODE) : null;

if(!$version_id || !$schema_json){ echo json_encode(['ok'=>false,'error'=>'Parâmetros inválidos']); exit; }

// validação mínima
$schema = json_decode($schema_json,true);
if(!is_array($schema) || empty($schema['meta']['title']) || !isset($schema['sections']) || !is_array($schema['sections']) || count($schema['sections'])===0){
  echo json_encode(['ok'=>false,'error'=>'Preencha título e ao menos uma seção']); exit;
}

try{
  $pdo->beginTransaction();

  // garante que é draft e pega form_id + próxima versão se necessário
  $st = $pdo->prepare('SELECT fv.id, fv.form_id, fv.versao, fv.status FROM form_versions fv WHERE fv.id = ? FOR UPDATE');
  $st->execute([$version_id]);
  $v = $st->fetch(PDO::FETCH_ASSOC);
  if(!$v){ throw new Exception('Versão não encontrada'); }
  if($v['status']!=='draft'){ throw new Exception('Apenas versões draft podem ser publicadas'); }

  // congela schema e publica
  $up = $pdo->prepare('UPDATE form_versions SET schema_json = ?, status = "published", published_at = NOW() WHERE id = ?');
  $up->execute([$schema_json, $version_id]);

  $pdo->commit();
  echo json_encode(['ok'=>true,'status'=>'published','published_at'=>date('Y-m-d H:i:s')]);
}catch(Exception $e){
  if($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}