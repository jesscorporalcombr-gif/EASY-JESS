<?php
// /easy/painel-adm/api/forms_creator/create.php
@session_start();
header('Content-Type: application/json');
require_once('../../conexao.php');

$raw = file_get_contents('php://input');
$in  = json_decode($raw,true);
$nome = trim($in['nome'] ?? 'Novo formulário');
$descricao = trim($in['descricao'] ?? '');
$tipo = $in['tipo'] ?? 'anamnese';

try{
  $pdo->beginTransaction();
  $ins = $pdo->prepare('INSERT INTO forms (nome,descricao,tipo,ativo,excluido,created_at) VALUES (?,?,?,?,0,NOW())');
  $ins->execute([$nome,$descricao,$tipo,1]);
  $form_id = (int)$pdo->lastInsertId();

  $schema = [ 'meta'=>['title'=>$nome,'description'=>$descricao,'type'=>$tipo,'version'=>1], 'sections'=>[] ];
  $schema_json = json_encode($schema, JSON_UNESCAPED_UNICODE);

  $iv = $pdo->prepare('INSERT INTO form_versions (form_id,versao,status,schema_json,created_at) VALUES (?,?,?,?,NOW())');
  $iv->execute([$form_id,1,'draft',$schema_json]);
  $version_id = (int)$pdo->lastInsertId();

  $pdo->commit();
  echo json_encode(['ok'=>true,'form_id'=>$form_id,'version_id'=>$version_id,'versao'=>1]);
}catch(Exception $e){
  if($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}