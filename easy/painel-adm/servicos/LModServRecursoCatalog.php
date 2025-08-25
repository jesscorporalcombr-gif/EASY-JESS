<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (!in_array($tipo,['salas','equipamentos'])) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'tipo inválido']); exit;
}
$table = $tipo==='salas' ? 'salas' : 'equipamentos';

$sql = "
  SELECT id, nome AS recurso, foto
  FROM {$table}
  WHERE (:q = '' OR nome LIKE :like)
  ORDER BY nome ASC
  LIMIT 20
";
try{
  $st=$pdo->prepare($sql);
  $st->bindValue(':q',$q,PDO::PARAM_STR);
  $st->bindValue(':like','%'.$q.'%',PDO::PARAM_STR);
  $st->execute();
  $rows=$st->fetchAll(PDO::FETCH_ASSOC);
  foreach($rows as &$r){ $r['id']=(int)$r['id']; }
  unset($r);
  echo json_encode(['ok'=>true,'count'=>count($rows),'rows'=>$rows]);
}catch(Throwable $e){
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Erro catálogo','detail'=>$e->getMessage()]);
}
