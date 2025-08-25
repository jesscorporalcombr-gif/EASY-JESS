<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg = '', $data = null){
  echo json_encode(['success'=>$ok, 'msg'=>$msg, 'data'=>$data], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function br2dec($s){
  $s = trim((string)$s);
  if ($s==='') return null;
  $s = str_replace('.', '', $s);
  $s = str_replace(',', '.', $s);
  return is_numeric($s) ? (float)$s : null;
}

try {
  $id_servico      = (int)($_POST['id_servico'] ?? 0);
  $id_profissional = (int)($_POST['id_profissional'] ?? 0);
  $id_contrato = (int)($_POST['id_contrato'] ?? 0);
  $id_serv_prof    = (int)($_POST['id_serv_prof'] ?? 0);

  // campos editáveis
  $tempo            = ($_POST['tempo'] ?? '') === '' ? null : (int)$_POST['tempo'];
  $comissao         = br2dec($_POST['comissao'] ?? '');
  $preco            = br2dec($_POST['preco'] ?? '');
  $ag_online        = isset($_POST['agendamento_online']) ? (int)$_POST['agendamento_online'] : 0;
  $executa          = isset($_POST['executa']) ? (int)$_POST['executa'] : 0;

  if ($id_servico<=0 || $id_profissional<=0) jexit(false, 'Parâmetros obrigatórios ausentes.');

  // Se existir pelo par (serviço, profissional), força usar esse id
  if ($id_serv_prof<=0) {
    $st = $pdo->prepare("SELECT id FROM servicos_profissional WHERE id_servico=:s AND id_profissional=:p LIMIT 1");
    $st->execute([':s'=>$id_servico, ':p'=>$id_profissional]);
    $id_serv_prof = (int)$st->fetchColumn();
  }

  if ($id_serv_prof > 0) {
    // UPDATE
    $sql = "UPDATE servicos_profissional SET
              id_contrato = :id_contrato,
              tempo = :tempo,
              comissao = :comissao,
              preco = :preco,
              agendamento_online = :ag_online,
              executa = :executa,
              atualizado_em = NOW()
            WHERE id = :id";
    $up = $pdo->prepare($sql);
    $up->bindValue(':id_contrato',    $id_contrato, $id_contrato===null?PDO::PARAM_NULL:PDO::PARAM_INT);
    $up->bindValue(':tempo',    $tempo, $tempo===null?PDO::PARAM_NULL:PDO::PARAM_INT);
    $up->bindValue(':comissao', $comissao, $comissao===null?PDO::PARAM_NULL:PDO::PARAM_STR);
    $up->bindValue(':preco',    $preco, $preco===null?PDO::PARAM_NULL:PDO::PARAM_STR);
    $up->bindValue(':ag_online',$ag_online, PDO::PARAM_INT);
    $up->bindValue(':executa',  $executa, PDO::PARAM_INT);
    $up->bindValue(':id',       $id_serv_prof, PDO::PARAM_INT);
    $up->execute();

    jexit(true, '', ['id'=>$id_serv_prof]);
  } else {
    // Não existe ainda — INSERE (criamos o vínculo mesmo que executa=0,
    // para manter suas preferências; se preferir não criar quando executa=0, teste aqui)
    $sql = "INSERT INTO servicos_profissional
              (id_servico, id_profissional, id_contrato, tempo, comissao, preco, agendamento_online, executa, criado_em, atualizado_em)
            VALUES
              (:s, :p, :id_contrato, :tempo, :comissao, :preco, :ag_online, :executa, NOW(), NOW())";
    $in = $pdo->prepare($sql);
    $in->bindValue(':s',         $id_servico, PDO::PARAM_INT);
    $in->bindValue(':p',         $id_profissional, PDO::PARAM_INT);
    $in->bindValue(':id_contrato',         $id_contrato, PDO::PARAM_INT);
    $in->bindValue(':tempo',     $tempo, $tempo===null?PDO::PARAM_NULL:PDO::PARAM_INT);
    $in->bindValue(':comissao',  $comissao, $comissao===null?PDO::PARAM_NULL:PDO::PARAM_STR);
    $in->bindValue(':preco',     $preco, $preco===null?PDO::PARAM_NULL:PDO::PARAM_STR);
    $in->bindValue(':ag_online', $ag_online, PDO::PARAM_INT);
    $in->bindValue(':executa',   $executa, PDO::PARAM_INT);
    $in->execute();
    $newId = (int)$pdo->lastInsertId();
    jexit(true, '', ['id'=>$newId]);
  }

} catch (Throwable $e) {
  jexit(false, 'Erro ao salvar: '.$e->getMessage());
}
