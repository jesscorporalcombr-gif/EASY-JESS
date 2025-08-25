<?php
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

$table  = $_GET['table']  ?? '';
$fields = $_GET['fields'] ?? 'id, nome';          // fallback

/* -----------------------------------------------------------
   2. Valida a tabela
----------------------------------------------------------- */
$stmt = $pdo->prepare("SHOW TABLES LIKE :table");
$stmt->execute([':table' => $table]);
if ($stmt->rowCount() !== 1) {
    http_response_code(404);
    echo json_encode(['error' => 'tabela não encontrada']);
    exit;
}

/* -----------------------------------------------------------
   3. Obtém metadados da tabela
----------------------------------------------------------- */
$stmt = $pdo->prepare("DESCRIBE `$table`");
$stmt->execute();
$tableFields = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

$requestedFields  = array_map('trim', explode(',', $fields));
$validatedFields  = array_intersect($requestedFields, $tableFields);

if (empty($validatedFields)) {
    echo json_encode(['error' => 'nenhum campo válido informado']); exit;
}

$selectedFieldsSql = implode(', ', array_map(fn($f) => "`$f`", $validatedFields));

/* -----------------------------------------------------------
   4. Constrói filtros dinâmicos
----------------------------------------------------------- */
$ignoredKeys = ['table','fields','all','PHPSESSID'];
$clauses = [];
$params  = [];

foreach ($_GET as $key => $value) {
    if (!in_array($key, $ignoredKeys, true) && in_array($key, $tableFields, true)) {

        // intervalo: campo=2024-01-01<->2024-12-31
        if (strpos($value, '<->') !== false) {
            [$start, $end] = array_map('trim', explode('<->', $value, 2));
            $clauses[] = "`$key` BETWEEN :{$key}_start AND :{$key}_end";
            $params["{$key}_start"] = $start;
            $params["{$key}_end"]   = $end;
        }
        // lista: campo=1,2,3
        elseif (strpos($value, ',') !== false) {
            $items = array_map('trim', explode(',', $value));
            $ph    = [];
            foreach ($items as $i => $item) {
                $name = "{$key}_{$i}";
                $ph[] = ":$name";
                $params[$name] = $item;
            }
            $clauses[] = "`$key` IN (" . implode(', ', $ph) . ")";
        }
        // igualdade simples
        else {
            $clauses[] = "`$key` = :$key";
            $params[$key] = $value;
        }
    }
}

/* -----------------------------------------------------------
   5. SELECT das linhas
----------------------------------------------------------- */
$sqlRows = "SELECT $selectedFieldsSql FROM `$table`";
if ($clauses) $sqlRows .= ' WHERE ' . implode(' AND ', $clauses);

$stmtRows = $pdo->prepare($sqlRows);
foreach ($params as $k => $v) $stmtRows->bindValue(":$k", $v);
$stmtRows->execute();
$rows = $stmtRows->fetchAll(PDO::FETCH_ASSOC);

/* ===========================================================
   6. Agregados financeiros
   =========================================================== */

// reaproveita os mesmos filtros, mas elimina quaisquer que citem `pago`
$aggClauses = $clauses;
$aggParams  = $params;

foreach ($aggClauses as $i => $c) if (strpos($c, '`pago`') !== false) unset($aggClauses[$i]);
foreach ($aggParams as $k => $v)  if (strpos($k, 'pago') === 0)       unset($aggParams[$k]);

$sqlAgg = "
    SELECT
        /* entradas   (pago = 1, valores positivos) */
        SUM(CASE WHEN pago = 1 AND valor_liquido   > 0 THEN valor_liquido   END) AS entradas_liquidas,
        SUM(CASE WHEN pago = 1 AND valor_principal > 0 THEN valor_principal END) AS entradas_brutas,

        /* saídas     (pago = 1, valores negativos) */
        SUM(CASE WHEN pago = 1 AND valor_liquido   < 0 THEN valor_liquido   END) AS saídas_liquidas,
        SUM(CASE WHEN pago = 1 AND valor_principal < 0 THEN valor_principal END) AS saídas_brutas,

        /* pendências (pago IS NULL ou 0)           */
        SUM(CASE WHEN (pago IS NULL OR pago = 0) THEN valor_liquido   END) AS pendências_liquidas,
        SUM(CASE WHEN (pago IS NULL OR pago = 0) THEN valor_principal END) AS pendências_brutas
    FROM `$table`
";
if ($aggClauses) $sqlAgg .= ' WHERE ' . implode(' AND ', $aggClauses);

$stmtAgg = $pdo->prepare($sqlAgg);
foreach ($aggParams as $k => $v) $stmtAgg->bindValue(":$k", $v);
$stmtAgg->execute();

$t = $stmtAgg->fetch(PDO::FETCH_ASSOC) ?: [];




$mostrarTotais = $_GET['saldos']  ?? '';


// ====================================================
//  CÁLCULO DE SALDOS APÓS O SELECT PRINCIPAL
// ====================================================

/* -----------------------------------------------------------
   1. Detecta a coluna de data usada no filtro (é a que veio no GET)
----------------------------------------------------------- */
if ($mostrarTotais==="true"){
    //echo 'Mostrando totais no echo:  ' . $mostrarTotais . '  ';
    $possiveisDatas = ['data_vencimento','data_competencia','data_pagamento'];
    $dataCol = null;
    foreach ($possiveisDatas as $c) {
        if (isset($_GET[$c]) && strpos($_GET[$c], '<->') !== false) {
            $dataCol = $c; break;
        }
    }
    if (!$dataCol) {
        echo json_encode(['error'=>'Filtro de data ausente.']); exit;
    }
    list($dataIni, $dataFim) = array_map('trim', explode('<->', $_GET[$dataCol], 2));
    $diaAnterior = date('Y-m-d', strtotime("$dataIni -1 day"));


    $clauses[] = '`pago` = :pago';
    $params['pago'] = 1; 
    /* -----------------------------------------------------------
    2. Monta as cláusulas-base sem o BETWEEN original
    ----------------------------------------------------------- */
    $baseClauses = $clauses;          // já têm todos os filtros
    $baseParams  = $params;


    foreach ($baseClauses as $k=>$cl) {
        if (strpos($cl, "`$dataCol` BETWEEN") !== false) unset($baseClauses[$k]);
    }
    foreach ($baseParams as $k=>$v) {
        if (strpos($k, $dataCol) === 0) unset($baseParams[$k]);
    }

    /* -----------------------------------------------------------
    3. Query única com CASE para quatro somas
    ----------------------------------------------------------- */
    $caseSQL = "
        SUM(CASE WHEN `$dataCol` <= :diaAnterior THEN valor_principal END) AS saldoBrutoDiaAnterior,
        SUM(CASE WHEN `$dataCol` <= :diaAnterior THEN valor_liquido  END) AS saldoLiquidoDiaAnterior,
        SUM(CASE WHEN `$dataCol` <= :dataFim     THEN valor_principal END) AS saldoBrutoAtual,
        SUM(CASE WHEN `$dataCol` <= :dataFim     THEN valor_liquido  END) AS saldoLiquidoAtual
    ";

    $saldoSQL = "SELECT $caseSQL FROM `$table`";
    if ($baseClauses) $saldoSQL .= ' WHERE '.implode(' AND ', $baseClauses);

    $stmtSaldo = $pdo->prepare($saldoSQL);

    /* → bind de filtros não-data */
    foreach ($baseParams as $k=>$v)  $stmtSaldo->bindValue(":$k", $v);
    /* → bind das datas-limite */
    $stmtSaldo->bindValue(':diaAnterior', $diaAnterior);
    $stmtSaldo->bindValue(':dataFim',     $dataFim);

    $stmtSaldo->execute();
    $saldos = $stmtSaldo->fetch(PDO::FETCH_ASSOC);

    /* -----------------------------------------------------------
    4. Resposta final
    ----------------------------------------------------------- */
    echo json_encode([
        'rows'                    => $rows,               // da 1ª consulta
        'saldoBrutoDiaAnterior'   => (float)$saldos['saldoBrutoDiaAnterior'],
        'saldoLiquidoDiaAnterior' => (float)$saldos['saldoLiquidoDiaAnterior'],
        'saldoBrutoAtual'         => (float)$saldos['saldoBrutoAtual'],
        'saldoLiquidoAtual'       => (float)$saldos['saldoLiquidoAtual'],
        'entradas_liquidas'    => (float)($t['entradas_liquidas']    ?? 0),
        'entradas_brutas'      => (float)($t['entradas_brutas']      ?? 0),
        'saidas_liquidas'      => (float)($t['saídas_liquidas']      ?? 0),
        'saidas_brutas'        => (float)($t['saídas_brutas']        ?? 0),
        'pendencias_liquidas'  => (float)($t['pendências_liquidas']  ?? 0),
        'pendencias_brutas'    => (float)($t['pendências_brutas']    ?? 0),
    ]);
}else{

    echo json_encode([
        'rows'                    => $rows,               // da 1ª consulta
        'saldoBrutoDiaAnterior'   => 0,
        'saldoLiquidoDiaAnterior' => 0,
        'saldoBrutoAtual'         => 0,
        'saldoLiquidoAtual'       => 0,
        'entradas_liquidas'    => (float)($t['entradas_liquidas']    ?? 0),
        'entradas_brutas'      => (float)($t['entradas_brutas']      ?? 0),
        'saidas_liquidas'      => (float)($t['saídas_liquidas']      ?? 0),
        'saidas_brutas'        => (float)($t['saídas_brutas']        ?? 0),
        'pendencias_liquidas'  => (float)($t['pendências_liquidas']  ?? 0),
        'pendencias_brutas'    => (float)($t['pendências_brutas']    ?? 0),
    ]);



}


?>
