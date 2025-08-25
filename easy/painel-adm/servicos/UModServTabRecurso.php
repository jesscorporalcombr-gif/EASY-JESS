<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok,$http=200,$extra=[]){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$extra)); exit; }
function brToFloat($v){ if ($v===null||$v==='') return null; $s=str_replace(['.',' '],'',(string)$v); $s=str_replace(',','.',$s); return is_numeric($s)?(float)$s:null; }

$raw=file_get_contents('php://input');
$p=json_decode($raw,true) ?: $_POST;

$tipo = isset($p['tipo']) ? trim($p['tipo']) : '';
$id_servico = isset($p['id_servico']) ? (int)$p['id_servico'] : 0;
$item = isset($p['item']) ? $p['item'] : [];
if (!in_array($tipo,['salas','equipamentos']) || !$id_servico || !$item) out(false,400,['error'=>'Parâmetros inválidos.']);

$id_link = !empty($item['id_link']) ? (int)$item['id_link'] : null;
$id_recurso = !empty($item['id_recurso']) ? (int)$item['id_recurso'] : 0;
if (!$id_recurso) out(false,400,['error'=>'id_recurso requerido.']);

try{
  if ($tipo==='salas'){
    if ($id_link){
      $up=$pdo->prepare("UPDATE servicos_salas SET id_sala=:r WHERE id=:id AND id_servico=:s");
      $up->execute([':r'=>$id_recurso, ':id'=>$id_link, ':s'=>$id_servico]);
    } else {
      // evita duplicar
      $ck=$pdo->prepare("SELECT id FROM servicos_salas WHERE id_servico=:s AND id_sala=:r");
      $ck->execute([':s'=>$id_servico, ':r'=>$id_recurso]);
      $exist=$ck->fetchColumn();
      if ($exist){ $id_link=(int)$exist; } else {
        $ins=$pdo->prepare("INSERT INTO servicos_salas (id_servico,id_sala) VALUES (:s,:r)");
        $ins->execute([':s'=>$id_servico, ':r'=>$id_recurso]);
        $id_link=(int)$pdo->lastInsertId();
      }
    }
    $st=$pdo->prepare("
      SELECT ss.id AS id_link, ss.id_servico, ss.id_sala AS id_recurso,
             s.nome AS recurso, s.foto AS foto_recurso, NULL AS quantidade, 'salas' AS tipo
      FROM servicos_salas ss
      JOIN salas s ON s.id = ss.id_sala
      WHERE ss.id = :id
    ");
    $st->execute([':id'=>$id_link]);
  } else {
    if ($id_link){
        $up=$pdo->prepare("UPDATE servicos_equipamentos SET id_equipamento=:r WHERE id=:id AND id_servico=:s");
        $up->execute([':r'=>$id_recurso, ':id'=>$id_link, ':s'=>$id_servico]);
    } else {
        $ck=$pdo->prepare("SELECT id FROM servicos_equipamentos WHERE id_servico=:s AND id_equipamento=:r");
        $ck->execute([':s'=>$id_servico, ':r'=>$id_recurso]);
        $exist=$ck->fetchColumn();
        if ($exist){
        $id_link=(int)$exist; // já existe; nada a atualizar
        } else {
        $ins=$pdo->prepare("INSERT INTO servicos_equipamentos (id_servico,id_equipamento) VALUES (:s,:r)");
        $ins->execute([':s'=>$id_servico, ':r'=>$id_recurso]);
        $id_link=(int)$pdo->lastInsertId();
        }
    }
    $st=$pdo->prepare("
        SELECT se.id AS id_link, se.id_servico, se.id_equipamento AS id_recurso,
            e.nome AS recurso, e.foto AS foto_recurso, 'equipamentos' AS tipo
        FROM servicos_equipamentos se
        JOIN equipamentos e ON e.id = se.id_equipamento
        WHERE se.id = :id
    ");
    $st->execute([':id'=>$id_link]);
  }

  $row=$st->fetch(PDO::FETCH_ASSOC);
  if (!$row) out(false,500,['error'=>'Falha ao recarregar item salvo.']);
  $row['id_link']=(int)$row['id_link'];
  $row['id_servico']=(int)$row['id_servico'];
  $row['id_recurso']=(int)$row['id_recurso'];
  if ($row['quantidade']!==null) $row['quantidade']=(float)$row['quantidade'];

  out(true,200,['row'=>$row]);

}catch(Throwable $e){
  out(false,500,['error'=>'Erro ao salvar recurso','detail'=>$e->getMessage()]);
}
