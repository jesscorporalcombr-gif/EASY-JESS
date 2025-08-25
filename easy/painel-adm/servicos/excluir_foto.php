<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg = '', $data = null){
  echo json_encode(['success'=>$ok, 'msg'=>$msg, 'data'=>$data], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

try {
  // aceita POST (preferido); se vier por GET, também funciona
  $id = isset($_POST['id']) ? (int)$_POST['id'] : (int)($_GET['id'] ?? 0);
  if ($id <= 0) jexit(false, 'ID inválido.');

  // busca os nomes dos arquivos antes de deletar
  $st = $pdo->prepare("SELECT id, id_servico, arquivo_ori, arquivo_mini FROM servicos_fotos WHERE id = :id");
  $st->execute([':id' => $id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if (!$row) jexit(false, 'Foto não encontrada.');

  // remove do banco
  $del = $pdo->prepare("DELETE FROM servicos_fotos WHERE id = :id");
  $del->execute([':id' => $id]);

  // monta paths para remover arquivos
  $pasta  = $_SESSION['x_url'] ?? '';
  $base   = $pasta ? "../../{$pasta}/img/servicos/galeria/" : "../../img/servicos/galeria/";
  $mini   = $base . "mini/";

  // tenta apagar arquivos (se existirem)
  if (!empty($row['arquivo_ori'])) {
    @unlink($base . $row['arquivo_ori']);
  }
  if (!empty($row['arquivo_mini'])) {
    @unlink($mini . $row['arquivo_mini']);
  }

  jexit(true, 'Excluído com sucesso.', ['id' => $id]);

} catch (Throwable $e) {
  jexit(false, 'Erro ao excluir: ' . $e->getMessage());
}
