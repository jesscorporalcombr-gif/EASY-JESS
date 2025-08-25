<?php

//equipamentos/SModEquipamentosDocListar.php
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
  $id_equipamento = (int)getP('id_equipamento', 0);
  if ($id_equipamento <= 0) out(false, [], 400, ['msg'=>'id_equipamento inválido']);

  $pasta = $_SESSION['x_url'] ?? ''; // mesma lógica do resto do painel
  $baseUrl = '../' . ($pasta ? "$pasta/" : '') . 'docs/equipamentos/';

  $sql = "SELECT id, id_equipamento, titulo, tipo, descricao, data_arquivo,
               arquivo, mime, tamanho_bytes, data_upload
        FROM equipamentos_documentos
        WHERE id_equipamento = :id_equipamento
        ORDER BY COALESCE(data_arquivo, data_upload) DESC, id DESC";
        
  $st = $pdo->prepare($sql);
  $st->execute([':id_equipamento'=>$id_equipamento]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  foreach($rows as &$r){
    $r['id'] = (int)$r['id'];
    $r['id_equipamento'] = (int)$r['id_equipamento'];
    $r['tamanho_bytes'] = isset($r['tamanho_bytes']) ? (int)$r['tamanho_bytes'] : null;
    $r['url'] = $baseUrl . ($r['arquivo'] ?? '');
  }
  unset($r);

  out(true, $rows);
} catch(Throwable $e){
  out(false, [], 500, ['msg'=>'Erro ao listar documentos','detail'=>$e->getMessage()]);
}
