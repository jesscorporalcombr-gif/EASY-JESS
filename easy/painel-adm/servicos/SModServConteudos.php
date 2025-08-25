<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

$id_servico = isset($_GET['id_servico']) ? (int)$_GET['id_servico'] : 0;
$filtro_tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : ''; // opcional

if (!$id_servico) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'id_servico inválido']); exit; }

$tiposValidos = ['TERMO','POP','TREINAMENTO','LINK','ARQUIVO'];
$whereTipo = '';
$params = [':id'=>$id_servico];
if ($filtro_tipo && in_array($filtro_tipo,$tiposValidos)) {
  $whereTipo = ' AND c.tipo = :tipo ';
  $params[':tipo'] = $filtro_tipo;
}

$sql = "SELECT c.*
        FROM servicos_conteudos c
        WHERE c.id_servico = :id {$whereTipo}
        ORDER BY c.tipo, COALESCE(c.data_referencia, c.created_at) DESC, c.titulo ASC";

try {
  $st = $pdo->prepare($sql);
  foreach ($params as $k=>$v) $st->bindValue($k,$v);
  $st->execute();
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  // Anexa URL navegável do arquivo (quando houver)
  $pasta = $_SESSION['x_url'] ?? '';
  $baseHref = ($pasta ? "../{$pasta}" : "..") . "/documentos/servicos/";

  foreach ($rows as &$r) {
    if (!empty($r['arquivo'])) {
      $r['href_arquivo'] = $baseHref . $r['arquivo'];
    } else {
      $r['href_arquivo'] = null;
    }
  }
  unset($r);

  echo json_encode(['ok'=>true, 'rows'=>$rows, 'count'=>count($rows)]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Erro ao listar','detail'=>$e->getMessage()]);
}
