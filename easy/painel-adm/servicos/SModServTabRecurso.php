<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok, $rows=[], $http=200, $extra=[]){
  http_response_code($http);
  echo json_encode(array_merge(['ok'=>$ok,'count'=>count($rows),'rows'=>$rows], $extra));
  exit;
}
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$id_servico = isset($_GET['id_servico']) ? (int)$_GET['id_servico'] : 0;
if (!$id_servico || !in_array($tipo,['salas','equipamentos'])) out(false, [], 400, ['error'=>'Parâmetros inválidos.']);

try{
  if ($tipo==='salas'){
    $sql = "
      SELECT
        ss.id AS id_link,
        ss.id_servico,
        ss.id_sala AS id_recurso,
        s.nome AS recurso,
        s.foto AS foto_recurso,
        NULL AS quantidade,
        'salas' AS tipo
      FROM servicos_salas ss
      JOIN salas s ON s.id = ss.id_sala
      WHERE ss.id_servico = :id
      ORDER BY s.nome ASC
    ";
  } else {
    $sql = "
      SELECT
        se.id AS id_link,
        se.id_servico,
        se.id_equipamento AS id_recurso,
        e.nome AS recurso,
        e.foto AS foto_recurso,
        'equipamentos' AS tipo
      FROM servicos_equipamentos se
      JOIN equipamentos e ON e.id = se.id_equipamento
      WHERE se.id_servico = :id
      ORDER BY e.nome ASC
    ";
  }
  $st=$pdo->prepare($sql);
  $st->bindValue(':id', $id_servico, PDO::PARAM_INT);
  $st->execute();
  $rows=$st->fetchAll(PDO::FETCH_ASSOC);

  foreach($rows as &$r){
    $r['id_link'] = (int)$r['id_link'];
    $r['id_servico'] = (int)$r['id_servico'];
    $r['id_recurso'] = (int)$r['id_recurso'];
    
  }
  unset($r);
  out(true, $rows);
}catch(Throwable $e){
  out(false, [], 500, ['error'=>'Erro ao listar recursos','detail'=>$e->getMessage()]);
}
