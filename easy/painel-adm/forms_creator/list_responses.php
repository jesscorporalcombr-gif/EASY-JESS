<?php
@session_start();
header('Content-Type: application/json');
try{
  require_once('../../conexao.php');
  $in = json_decode(file_get_contents('php://input'), true) ?: [];
  $form_id = (int)($in['form_id'] ?? 0);
  $page = max(1, (int)($in['page'] ?? 1));
  $ps = min(100, max(1, (int)($in['page_size'] ?? 20)));
  $off = ($page-1)*$ps;
  $status = trim($in['status'] ?? '');
  $search = trim($in['search'] ?? '');

  if(!$form_id) { echo json_encode(['ok'=>false,'error'=>'form_id obrigatório']); exit; }

  $where = "WHERE r.form_id = :fid AND r.excluido=0";
  $args = [':fid'=>$form_id];

  if($status!==''){ $where .= " AND r.status = :st"; $args[':st']=$status; }
  if($search!==''){ 
    $where .= " AND (p.nome LIKE :q OR r.id = :rid)"; 
    $args[':q'] = "%$search%";
    $args[':rid'] = ctype_digit($search)? (int)$search : 0;
  }

  $sql = "SELECT SQL_CALC_FOUND_ROWS
            r.id, r.paciente_id, r.created_at, r.status, r.source, r.has_attachment,
            p.nome as paciente_nome
          FROM form_responses r
          LEFT JOIN pacientes p ON p.id = r.paciente_id
          $where
          ORDER BY r.id DESC
          LIMIT $off, $ps";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($args);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $total = (int)$pdo->query("SELECT FOUND_ROWS()")->fetchColumn();

  echo json_encode(['ok'=>true,'rows'=>$rows,'total'=>$total]);
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
