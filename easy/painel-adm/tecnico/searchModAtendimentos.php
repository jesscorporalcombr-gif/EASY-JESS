<?php
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

// Campos retornados
$select = [
    'cc.data',
    'cc.id AS atendimento_id',
    'cc.id_cliente',
    'cc.servico',
    'cc.profissional_1',
    'cc.status',
    'cad.nome AS nome_cliente',
    'cad.foto AS foto_cliente'
];

$query = "
    SELECT " . implode(", ", $select) . "
    FROM atendimentos cc
    LEFT JOIN clientes cad ON cad.id = cc.id_cliente
";

// Datas recebidas (YYYY-MM-DD); default = últimos 10 dias até hoje
$ini = $_GET['data_inicial'] ?? null;
$fim = $_GET['data_final']  ?? null;

$defFim = (new DateTime('today'))->format('Y-m-d');
$defIni = (new DateTime('today'))->modify('-10 days')->format('Y-m-d');

$rx = '/^\d{4}-\d{2}-\d{2}$/';
$ini = ($ini && preg_match($rx, $ini)) ? $ini : $defIni;
$fim = ($fim && preg_match($rx, $fim)) ? $fim : $defFim;

// garante ini <= fim
if (strtotime($ini) > strtotime($fim)) {
    $tmp = $ini; $ini = $fim; $fim = $tmp;
}

$clauses = [];
$params  = [];

// filtro somente por datas (inclusive)
$clauses[] = "cc.data BETWEEN :ini AND :fim";
$params[':ini'] = $ini;
$params[':fim'] = $fim;

// monta WHERE
if (!empty($clauses)) {
    $query .= " WHERE " . implode(" AND ", $clauses);
}

// ordenação padrão
$query .= " ORDER BY cc.data DESC, cad.nome ASC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['rows' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro na consulta',
        'message' => $e->getMessage()
    ]);
    exit;
}
