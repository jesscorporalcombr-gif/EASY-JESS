<?php
@session_start();
header('Content-Type: application/json');

try {
  require_once(__DIR__ . '/../../../conexao.php');

  // GET
  $form_id     = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
  $paciente_id = isset($_GET['paciente_id']) ? trim($_GET['paciente_id']) : '';
  $status      = isset($_GET['status']) ? trim($_GET['status']) : '';
  $source      = isset($_GET['source']) ? trim($_GET['source']) : '';
  $data_inicial= isset($_GET['data_inicial']) ? $_GET['data_inicial'] : '';
  $data_final  = isset($_GET['data_final'])   ? $_GET['data_final']   : '';

  if(!$form_id){ echo json_encode(['ok'=>false,'error'=>'form_id obrigatório']); exit; }

  if ($data_inicial === '') $data_inicial = date('Y-m-d', strtotime('-90 days'));
  if ($data_final   === '') $data_final   = date('Y-m-d');
  $dt_ini = date('Y-m-d', strtotime($data_inicial));
  $dt_fim = date('Y-m-d', strtotime($data_final));

  $where = " WHERE r.excluido = 0 AND r.form_id = :fid
             AND r.created_at >= :dtini AND r.created_at < DATE_ADD(:dtfim, INTERVAL 1 DAY) ";
  $args  = [ ':fid'=>$form_id, ':dtini'=>$dt_ini, ':dtfim'=>$dt_fim ];

  if($paciente_id !== ''){
    $where .= " AND r.paciente_id = :pid ";
    $args[':pid'] = (int)$paciente_id;
  }
  if($status !== ''){
    $where .= " AND r.status = :st ";
    $args[':st'] = $status;
  }
  if($source !== ''){
    $where .= " AND r.source = :src ";
    $args[':src'] = $source;
  }

  // total
  $stmtCnt = $pdo->prepare("SELECT COUNT(*) FROM form_responses r $where");
  $stmtCnt->execute($args);
  $total = (int)$stmtCnt->fetchColumn();

  // dados
  $sql = "
    SELECT
      r.id          AS anamnese_id,
      r.form_id     AS form_id,
      r.paciente_id AS paciente_id,
      c.nome        AS paciente_nome,
      r.status      AS status,
      r.source      AS source,
      r.created_at  AS created_at,
     
      c.foto        AS foto
    FROM form_responses r
    LEFT JOIN clientes c ON c.id = r.paciente_id
    $where
    ORDER BY r.id DESC
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($args);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // monta url da foto se possível
  // se você já tem $pastaFiles disponível via conexao.php ou config, ele será usado
  $baseFiles = null;
  if (isset($pastaFiles) && $pastaFiles) {
    // caminho relativo a partir do painel-adm
    $baseFiles = "../../$pastaFiles/clientes/";
  }

  foreach($rows as &$r){
    $r['paciente_foto_url'] = null;
    if(!empty($r['foto']) && $baseFiles){
      $r['paciente_foto_url'] = $baseFiles . $r['foto'];
    }
    unset($r['foto']);
  }

  echo json_encode(['ok'=>true,'rows'=>$rows,'total'=>$total], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
