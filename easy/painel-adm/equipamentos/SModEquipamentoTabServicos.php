<?php
// equipamentos/SModEquipamentoTabServicos.php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function out($ok, $rows = [], $http = 200, $extra = []) {
  http_response_code($http);
  echo json_encode(array_merge(['ok' => $ok, 'count' => count($rows), 'rows' => $rows], $extra), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function getParam($name, $default = null) {
  if (isset($_GET[$name])) return $_GET[$name];
  if (isset($_POST[$name])) return $_POST[$name];
  return $default;
}

try {
  $id_equipamento = getParam('id_equipamento');
  if ($id_equipamento === null || $id_equipamento === '' || !is_numeric($id_equipamento)) {
    out(false, [], 400, ['error' => 'Parâmetro id_equipamento ausente ou inválido.']);
  }
  $id_equipamento = (int)$id_equipamento;

  // Opcional: campos solicitados (whitelist simples)
  $fieldsParam = trim((string)getParam('fields', ''));
  $map = [
    'id_equipamento'        => 'CAST(:id_equipamento AS SIGNED) AS id_equipamento',
    'id_servico'     => 's.id AS id_servico',
    'servico'        => 's.servico AS servico',
    'categoria'      => 's.categoria',
    'foto_servico'   => 's.foto AS foto_servico',
    // se tua tabela tiver "categoria" (ou ajuste o nome)
    'categoria'      => 's.categoria AS categoria',
    // vínculo
    'id_link'        => 'ss.id AS id_link',
    'executa'        => 'CASE WHEN ss.id IS NULL THEN 0 ELSE 1 END AS executa',
  ];

  $requested = [];
  if ($fieldsParam !== '') {
    foreach (array_filter(array_map('trim', explode(',', $fieldsParam))) as $f) {
      if (isset($map[$f])) $requested[$f] = $map[$f];
    }
  }
  if (empty($requested)) {
    foreach (['id_equipamento','id_servico','servico', 'categoria', 'foto_servico','categoria','id_link','executa'] as $f) {
      if (isset($map[$f])) $requested[$f] = $map[$f];
    }
  }
  $selectList = implode(",\n       ", array_values($requested));

  $sql = "
    SELECT
       $selectList
    FROM servicos s
    LEFT JOIN servicos_equipamentos ss
           ON ss.id_servico = s.id
          AND ss.id_equipamento    = :id_equipamento
          AND (s.excluido IS NULL OR s.excluido <> 1)
    WHERE (s.excluido IS NULL OR s.excluido <> 1)
    ORDER BY s.servico ASC
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':id_equipamento', $id_equipamento, PDO::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // tipagem consistente p/ front
  foreach ($rows as &$r) {
    if (isset($r['id_equipamento']))     $r['id_equipamento'] = (int)$r['id_equipamento'];
    if (isset($r['id_servico']))  $r['id_servico'] = (int)$r['id_servico'];
    if (isset($r['id_link']))     $r['id_link'] = is_null($r['id_link']) ? null : (int)$r['id_link'];
    if (isset($r['executa']))     $r['executa'] = (int)$r['executa']; // 0/1
    if (isset($r['servico']) && $r['servico'] === null) $r['servico'] = '';
    if (isset($r['categoria']) && $r['categoria'] === null) $r['categoria'] = '';
    if (isset($r['foto_servico']) && $r['foto_servico'] === null) $r['foto_servico'] = '';
    if (isset($r['categoria']) && $r['categoria'] === null) $r['categoria'] = '';
  }
  unset($r);

  out(true, $rows);
} catch (Throwable $e) {
  out(false, [], 500, ['error' => 'Erro ao listar serviços para a equipamento.', 'detail' => $e->getMessage()]);
}
