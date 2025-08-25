<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
$id_link = isset($_POST['id_link']) ? (int)$_POST['id_link'] : 0;
if (!$id_link || !in_array($tipo,['salas','equipamentos'])) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Parâmetros inválidos.']); exit;
}
try{
  if ($tipo==='salas'){
    $st=$pdo->prepare("DELETE FROM servicos_salas WHERE id=:id");
  } else {
    $st=$pdo->prepare("DELETE FROM servicos_equipamentos WHERE id=:id");
  }
  $st->execute([':id'=>$id_link]);
  echo json_encode(['ok'=>true]);
}catch(Throwable $e){
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Erro ao excluir','detail'=>$e->getMessage()]);
}
