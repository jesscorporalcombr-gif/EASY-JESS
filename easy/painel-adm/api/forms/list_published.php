<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

try {
  $stmt = $pdo->query("SELECT id, name, version AS published_version
                       FROM forms
                       WHERE is_active = 1
                       ORDER BY updated_at DESC");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['ok'=>true, 'rows'=>$rows]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}
