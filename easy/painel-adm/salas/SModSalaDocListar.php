<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok, $rows=[], $http=200, $extra=[]){
  http_response_code($http);
  echo json_encode(array_merge(['ok'=>$ok,'count'=>count($rows),'rows'=>$rows], $extra), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function getP($n,$d=null){ return $_GET[$n] ?? $_POST[$n] ?? $d; }

try {
  $id_sala = (int)getP('id_sala', 0);
  if ($id_sala <= 0) out(false, [], 400, ['msg'=>'id_sala inválido']);

  $pasta = $_SESSION['x_url'] ?? ''; // mesma lógica do resto do painel
  $baseUrl = '../' . ($pasta ? "$pasta/" : '') . 'docs/salas/';

  $sql = "SELECT id, id_sala, titulo, descricao, arquivo, mime, tamanho_bytes, data_upload
            FROM salas_documentos
           WHERE id_sala = :id_sala
           ORDER BY data_upload DESC, id DESC";
  $st = $pdo->prepare($sql);
  $st->execute([':id_sala'=>$id_sala]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  foreach($rows as &$r){
    $r['id'] = (int)$r['id'];
    $r['id_sala'] = (int)$r['id_sala'];
    $r['tamanho_bytes'] = isset($r['tamanho_bytes']) ? (int)$r['tamanho_bytes'] : null;
    $r['url'] = $baseUrl . ($r['arquivo'] ?? '');
  }
  unset($r);

  out(true, $rows);
} catch(Throwable $e){
  out(false, [], 500, ['msg'=>'Erro ao listar documentos','detail'=>$e->getMessage()]);
}
