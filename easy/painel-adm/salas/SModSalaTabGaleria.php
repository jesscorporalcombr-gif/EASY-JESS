<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg='', $rows=null){
  echo json_encode(['success'=>$ok, 'msg'=>$msg, 'rows'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

try {
  $idSala = isset($_GET['id_sala']) ? (int)$_GET['id_sala'] : 0;
  $q         = trim($_GET['q'] ?? '');

  if ($idSala <= 0) jexit(false, 'id_sala inválido');

  $sql = "SELECT id, id_sala, titulo, data_foto, tipo_foto, descricao, arquivo_ori, arquivo_mini
          FROM salas_fotos
          WHERE id_sala = :s";

  $params = [':s' => $idSala];

  if ($q !== '') {
    $sql .= " AND (titulo LIKE :q OR tipo_foto LIKE :q OR descricao LIKE :q)";
    $params[':q'] = '%'.$q.'%';
  }

  // Ordena por data (nulos por último) e depois id desc
  $sql .= " ORDER BY (data_foto IS NULL), data_foto DESC, id DESC";

  $st = $pdo->prepare($sql);
  $st->execute($params);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  jexit(true, '', $rows);

} catch (Throwable $e) {
  jexit(false, 'Erro: '.$e->getMessage());
}
