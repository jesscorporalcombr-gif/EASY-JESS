<?php
// servicos/SModServTabProf.php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

// -------------------------------
// Helpers
// -------------------------------
function out($ok, $rows = [], $http = 200, $extra = []) {
  http_response_code($http);
  echo json_encode(array_merge(['ok' => $ok, 'count' => count($rows), 'rows' => $rows], $extra));
  exit;
}
function getParam($name, $default = null) {
  if (isset($_GET[$name])) return $_GET[$name];
  if (isset($_POST[$name])) return $_POST[$name];
  return $default;
}

// -------------------------------
// Entrada
// -------------------------------
$id_servico = getParam('id_servico', getParam('servico_id'));
if ($id_servico === null || $id_servico === '' || !is_numeric($id_servico)) {
  out(false, [], 400, ['error' => 'Parâmetro id_servico ausente ou inválido.']);
}
$id_servico = (int)$id_servico;

// Campos requisitados pelo front (opcional)
$fieldsParam = trim((string)getParam('fields', ''));

// Whitelist de campos disponíveis
$map = [
  // Identificadores
  'id_servico'        => 'CAST(:id_servico AS SIGNED) AS id_servico',
  'id_profissional'   => 'cca.id_colaborador AS id_profissional',
  'id_contrato'       => 'cca.id AS id_contrato',

  // Dados do colaborador
  'profissional'      => 'c.nome AS profissional',
  //'foto_profissional' => 'c.foto_cadastro AS foto_profissional', //altleara se quiser a foto do cadastro
  'foto_profissional' => 'cca.foto_agenda AS foto_profissional', //peguei a foto da agenda
  
  
  // Linha específica em servicos_profissional (se existir)
  'id_serv_prof'      => 'sp.id AS id_serv_prof',
  'id'                => 'sp.id AS id', // compat com thead atual

  // Campos editáveis (com fallback)
  'tempo'              => 'COALESCE(sp.tempo, s.tempo) AS tempo',
  'comissao'           => 'COALESCE(sp.comissao, s.comissao) AS comissao',
  'preco'              => 'COALESCE(sp.preco, s.valor_venda) AS preco',
  'agendamento_online' => 'COALESCE(sp.agendamento_online, s.agendamento_online) AS agendamento_online',
  'executa'            => 'COALESCE(sp.executa, 0) AS executa',
];

// Monta lista de campos solicitados ou padrão
$requested = [];
if ($fieldsParam !== '') {
  $reqList = array_filter(array_map('trim', explode(',', $fieldsParam)));
  foreach ($reqList as $f) {
    if (isset($map[$f])) $requested[$f] = $map[$f];
  }
}
if (empty($requested)) {
  $default = [
    'foto_profissional',
    'profissional',
    'id_profissional',
    'id_contrato',
    'id_serv_prof',
    'id',                 // compat
    'tempo',
    'comissao',
    'preco',
    'agendamento_online',
    'executa',
    'id_servico',
  ];
  foreach ($default as $f) {
    if (isset($map[$f])) $requested[$f] = $map[$f];
  }
}
$selectList = implode(",\n       ", array_values($requested));

// ------------------------------------
// SQL
//   - cc_active: último contrato ativo + ativo_agenda por colaborador
//   - join com cadastro do colaborador
//   - left join com servicos_profissional para este serviço
//   - join com servicos (para pegar os padrões do próprio serviço alvo)
// ------------------------------------
$sql = "
  WITH cc_active AS (
    SELECT cc.*
    FROM colaboradores_contratos cc
    JOIN (
      SELECT id_colaborador, MAX(id) AS max_id
      FROM colaboradores_contratos
      WHERE ativo = 1 AND ativo_agenda = 1
      GROUP BY id_colaborador
    ) last_cc
      ON last_cc.id_colaborador = cc.id_colaborador
     AND last_cc.max_id = cc.id
    WHERE cc.ativo = 1
      AND cc.ativo_agenda = 1
  )
  SELECT
       $selectList
  FROM cc_active AS cca
  JOIN colaboradores_cadastros c
    ON c.id = cca.id_colaborador
  JOIN servicos s
    ON s.id = :id_servico
  LEFT JOIN servicos_profissional sp
    ON sp.id_profissional = cca.id_colaborador
   AND sp.id_servico     = :id_servico
  ORDER BY c.nome ASC
";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':id_servico', $id_servico, PDO::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Tipagem consistente pro front
  foreach ($rows as &$r) {
    if (isset($r['id']))                 $r['id'] = is_null($r['id']) ? null : (int)$r['id'];
    if (isset($r['id_serv_prof']))       $r['id_serv_prof'] = is_null($r['id_serv_prof']) ? null : (int)$r['id_serv_prof'];
    if (isset($r['id_servico']))         $r['id_servico'] = (int)$r['id_servico'];
    if (isset($r['id_profissional']))    $r['id_profissional'] = (int)$r['id_profissional'];
    if (isset($r['id_contrato']))        $r['id_contrato'] = is_null($r['id_contrato']) ? null : (int)$r['id_contrato'];

    if (isset($r['tempo']))              $r['tempo'] = is_null($r['tempo']) ? null : (int)$r['tempo'];
    if (isset($r['comissao']))           $r['comissao'] = is_null($r['comissao']) ? null : (float)$r['comissao'];
    if (isset($r['preco']))              $r['preco'] = is_null($r['preco']) ? null : (float)$r['preco'];
    if (isset($r['agendamento_online'])) $r['agendamento_online'] = (int)$r['agendamento_online']; // 0/1
    if (isset($r['executa']))            $r['executa'] = (int)$r['executa'];                       // 0/1

    if (isset($r['profissional']) && $r['profissional'] === null)         $r['profissional'] = '';
    if (isset($r['foto_profissional']) && $r['foto_profissional'] === null) $r['foto_profissional'] = '';
  }
  unset($r);

  out(true, $rows);
} catch (Throwable $e) {
  out(false, [], 500, ['error' => 'Erro ao montar lista de profissionais do serviço.', 'detail' => $e->getMessage()]);
}
