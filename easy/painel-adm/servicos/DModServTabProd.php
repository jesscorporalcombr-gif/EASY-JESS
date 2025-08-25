<?php
// servicos/DModServTabProd.php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

$id = isset($_POST['id_serv_prod']) ? (int)$_POST['id_serv_prod'] : 0;
if (!$id) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'id_serv_prod requerido']);
  exit;
}
try {
  $st = $pdo->prepare("DELETE FROM servicos_produtos WHERE id = :id");
  $st->execute([':id' => $id]);
  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Erro ao excluir', 'detail' => $e->getMessage()]);
}
