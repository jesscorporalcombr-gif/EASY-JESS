<?php
// servicos/SModServTabProd.php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok, $rows = [], $http = 200, $extra = []) {
  http_response_code($http);
  echo json_encode(array_merge(['ok' => $ok, 'count' => count($rows), 'rows' => $rows], $extra));
  exit;
}
$id_servico = isset($_GET['id_servico']) ? (int)$_GET['id_servico'] : 0;
if (!$id_servico) out(false, [], 400, ['error' => 'id_servico requerido']);

$sql = "
  SELECT
    sp.id AS id_serv_prod,
    sp.id_servico,
    sp.id_produto,
    p.nome AS produto,
    p.foto AS foto_produto,
    p.unidade AS unidade,
    sp.quantidade,
    COALESCE(sp.custo_unitario, p.preco_custo) AS custo_unitario
  FROM servicos_produtos sp
  JOIN produtos p ON p.id = sp.id_produto
  WHERE sp.id_servico = :id_servico
  ORDER BY p.nome ASC
";
try {
  $st = $pdo->prepare($sql);
  $st->bindValue(':id_servico', $id_servico, PDO::PARAM_INT);
  $st->execute();
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  foreach ($rows as &$r) {
    $r['id_serv_prod']   = (int)$r['id_serv_prod'];
    $r['id_servico']     = (int)$r['id_servico'];
    $r['id_produto']     = (int)$r['id_produto'];
    $r['quantidade']     = $r['quantidade'] === null ? 0.0 : (float)$r['quantidade'];
    $r['custo_unitario'] = $r['custo_unitario'] === null ? 0.0 : (float)$r['custo_unitario'];
  }
  unset($r);

  out(true, $rows);
} catch (Throwable $e) {
  out(false, [], 500, ['error' => 'Erro ao listar produtos do serviço', 'detail' => $e->getMessage()]);
}
