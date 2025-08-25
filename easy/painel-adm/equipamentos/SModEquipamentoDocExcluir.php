<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

function jexit($ok,$msg='',$data=[]){
  echo json_encode(array_merge(['ok'=>$ok,'msg'=>$msg],$data), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
try{
  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) jexit(false,'ID inválido.');

  // pega info para remover arquivo
  $st = $pdo->prepare("SELECT d.id_equipamento, d.arquivo FROM equipamentos_documentos d WHERE d.id = :id");
  $st->execute([':id'=>$id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if (!$row) jexit(false,'Documento não encontrado.');

  $pasta = $_SESSION['x_url'] ?? '';
  $dir = realpath(__DIR__ . '/..');
  $uploadDir = $dir . '/../' . ($pasta ? "$pasta/" : '') . 'docs/equipamentos/';
  if (!empty($row['arquivo'])){
    $path = $uploadDir . $row['arquivo'];
    if (is_file($path)) @unlink($path);
  }

  $del = $pdo->prepare("DELETE FROM equipamentos_documentos WHERE id = :id");
  $del->execute([':id'=>$id]);

  jexit(true,'Excluído.');
}catch(Throwable $e){
  jexit(false,'Erro ao excluir documento.',['detail'=>$e->getMessage()]);
}
