<?php
// servicos/LModServProdutosCatalog.php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = 20;

$sql = "
  SELECT
    p.id,
    p.nome AS produto,
    p.foto,
    p.unidade,
    p.preco_custo AS custo_unitario
  FROM produtos p
  WHERE p.excluido = 0
    AND (:q = '' OR p.nome LIKE :like)
  ORDER BY p.nome ASC
  LIMIT $limit
";
try {
  $st = $pdo->prepare($sql);
  $st->bindValue(':q', $q, PDO::PARAM_STR);
  $st->bindValue(':like', '%'.$q.'%', PDO::PARAM_STR);
  $st->execute();
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  foreach ($rows as &$r) {
    $r['id'] = (int)$r['id'];
    $r['custo_unitario'] = $r['custo_unitario'] === null ? 0.0 : (float)$r['custo_unitario'];
  }
  unset($r);

  echo json_encode(['ok' => true, 'count' => count($rows), 'rows' => $rows]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro no catálogo de produtos', 'detail' => $e->getMessage()]);
}
