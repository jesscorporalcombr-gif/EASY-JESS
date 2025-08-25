<?php
@session_start();
header('Content-Type: application/json');
try {
  require_once('../../../conexao.php');

  $forms = $pdo->query("SELECT id, nome FROM forms WHERE excluido=0 AND tipo='anamnese' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

  $getPub = $pdo->prepare("SELECT id, schema_json FROM form_versions WHERE form_id=? AND status='published' ORDER BY versao DESC LIMIT 1");
  $getDrf = $pdo->prepare("SELECT id, schema_json FROM form_versions WHERE form_id=? AND status='draft' ORDER BY versao DESC LIMIT 1");

  foreach($forms as &$f){
    $title = null; $vid = null;
    $getPub->execute([$f['id']]);
    $v = $getPub->fetch(PDO::FETCH_ASSOC);
    if(!$v){ $getDrf->execute([$f['id']]); $v = $getDrf->fetch(PDO::FETCH_ASSOC); }
    if($v){ $vid = (int)$v['id']; $sj = json_decode($v['schema_json'], true); if(is_array($sj) && isset($sj['meta']['title'])) $title = $sj['meta']['title']; }
    $f['title'] = $title;
    $f['version_id'] = $vid;
  }

  echo json_encode(['ok'=>true,'rows'=>$forms]);
} catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
