<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'id inválido']); exit; }

try {
  // busca pra apagar o arquivo físico, se houver
  $st = $pdo->prepare("SELECT arquivo FROM servicos_conteudos WHERE id=:id");
  $st->bindValue(':id',$id, PDO::PARAM_INT);
  $st->execute();
  $row = $st->fetch(PDO::FETCH_ASSOC);

  $del = $pdo->prepare("DELETE FROM servicos_conteudos WHERE id=:id");
  $del->bindValue(':id',$id, PDO::PARAM_INT);
  $del->execute();

  if ($row && !empty($row['arquivo'])) {
    $pasta = $_SESSION['x_url'] ?? '';
    $destDir = $pasta ? "../../{$pasta}/documentos/servicos/" : "../../documentos/servicos/";
    @unlink($destDir.$row['arquivo']);
  }

  echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Erro ao excluir','detail'=>$e->getMessage()]);
}
