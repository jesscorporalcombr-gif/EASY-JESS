<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg = '', $data = []) {
  echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $data), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

try {
  $raw = file_get_contents('php://input');
  $payload = json_decode($raw, true);
  $src = is_array($payload) ? $payload : $_POST;

  $id_sala    = isset($src['id_sala'])    ? (int)$src['id_sala']    : 0;
  $id_servico = isset($src['id_servico']) ? (int)$src['id_servico'] : 0;
  $executa    = isset($src['executa'])    ? (int)$src['executa']    : 0;

  if ($id_sala <= 0 || $id_servico <= 0) jexit(false, 'Parâmetros inválidos.');

  $pdo->beginTransaction();

  if ($executa === 1) {
    // Tenta achar vínculo
    $sqlSel = "SELECT id FROM servicos_salas WHERE id_sala = :id_sala AND id_servico = :id_servico LIMIT 1";
    $st = $pdo->prepare($sqlSel);
    $st->execute([':id_sala'=>$id_sala, ':id_servico'=>$id_servico]);
    $link = $st->fetch(PDO::FETCH_ASSOC);

    if ($link) {
      $id_link = (int)$link['id']; // já existe
    } else {
      // INSERT; se tiver UNIQUE(uq_servico_sala), pode usar INSERT IGNORE/ON DUPLICATE
      $ins = $pdo->prepare("
        INSERT INTO servicos_salas (id_sala, id_servico) 
        VALUES (:id_sala, :id_servico)
      ");
      $ins->execute([':id_sala'=>$id_sala, ':id_servico'=>$id_servico]);
      $id_link = (int)$pdo->lastInsertId();
    }

    $pdo->commit();
    jexit(true, 'Vínculo ativo', ['id_link'=>$id_link, 'executa'=>1]);
  } else {
    // executa = 0 -> DELETE
    $del = $pdo->prepare("DELETE FROM servicos_salas WHERE id_sala = :id_sala AND id_servico = :id_servico");
    $del->execute([':id_sala'=>$id_sala, ':id_servico'=>$id_servico]);

    $pdo->commit();
    jexit(true, 'Vínculo removido', ['id_link'=>null, 'executa'=>0]);
  }

} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  jexit(false, 'Erro ao salvar vínculo serviço/sala.', ['detail'=>$e->getMessage()]);
}
