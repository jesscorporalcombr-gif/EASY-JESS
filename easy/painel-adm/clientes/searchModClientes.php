<?php
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
// 1. Parâmetros básicos
$table  = $_GET['table']  ?? '';
$fields = $_GET['fields'] ?? 'id';

// 2. Verifica existência da tabela
$stmt = $pdo->prepare("SHOW TABLES LIKE :table");
$stmt->execute([':table' => $table]);
if ($stmt->rowCount() !== 1) {
    http_response_code(404);
    echo json_encode(['error' => 'Table not found']);
    exit;
}

// 3. Obtém colunas válidas da tabela
$stmt = $pdo->prepare("DESCRIBE `$table`");
$stmt->execute();
$tableFields = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

// 4. Valida campos solicitados
$requestedFields  = array_map('trim', explode(',', $fields));
$validatedFields  = array_intersect($requestedFields, $tableFields);
if (empty($validatedFields)) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid fields specified']);
    exit;
}


$selectedFieldsSql = implode(', ', array_map(function($f){ return "`$f`"; }, $validatedFields));


if ($table === 'venda_itens') {
    $selectedFieldsSql .= ",
        ( quantidade
          - COALESCE(realizados,0)
          - COALESCE(transferidos,0)
          - COALESCE(convertidos,0)
          - COALESCE(descontados,0)
        ) AS saldo,
        (
          COALESCE(realizados,0)
        + COALESCE(transferidos,0)
        + COALESCE(convertidos,0)
        + COALESCE(descontados,0)
        ) AS consumidos
    ";
}

// 5. Monta SELECT base
$query = "SELECT $selectedFieldsSql FROM `$table`";



// 5. Monta SELECT base
$query    = "SELECT $selectedFieldsSql FROM `$table`";
$clauses  = [];
$params   = [];

// 5.1 Filtro padrão: registros não deletados
if (in_array('deletado_data_hora', $tableFields, true)) {
    $clauses[] = "`deletado_data_hora` IS NULL";
}

// 5.2 Filtros estruturados (via JSON em GET ?filters=...)
if (!empty($_GET['filters'])) {
    $filterObj = json_decode($_GET['filters'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid filters JSON']);
        exit;
    }
    $logic = strtoupper($filterObj['logic'] ?? 'AND');
    if (!in_array($logic, ['AND','OR'], true)) {
        $logic = 'AND';
    }

    $userClauses = [];
    foreach ($filterObj['conditions'] as $cond) {
        $field = $cond['field'] ?? null;
        $op    = strtoupper($cond['op']    ?? '=');
        $value = $cond['value'] ?? null;

        // só prossegue se o campo for válido
        if (!in_array($field, $tableFields, true)) {
            continue;
        }

        switch ($op) {
            // operadores simples
            case '=': case '<>': case '>': case '<': case '>=': case '<=':
                $ph = ":{$field}";
                $userClauses[] = "`$field` $op $ph";
                $params[$field] = $value;
                break;

            // IN (lista de valores)
            case 'IN':
                if (is_array($value) && count($value)) {
                    $placeholders = [];
                    foreach ($value as $i => $v) {
                        $key = "{$field}_{$i}";
                        $placeholders[] = ":{$key}";
                        $params[$key] = $v;
                    }
                    $userClauses[] = "`$field` IN (" . implode(', ', $placeholders) . ")";
                }
                break;

            // BETWEEN (intervalo 2 elementos)
            case 'BETWEEN':
                if (is_array($value) && count($value) === 2) {
                    $start = "{$field}_start";
                    $end   = "{$field}_end";
                    $userClauses[] = "`$field` BETWEEN :{$start} AND :{$end}";
                    $params[$start] = $value[0];
                    $params[$end]   = $value[1];
                }
                break;

            // você pode estender aqui com LIKE, IS NULL etc.
        }
    }

    if (!empty($userClauses)) {
        // agrupa as cláusulas do usuário entre parênteses
        $clauses[] = '(' . implode(" {$logic} ", $userClauses) . ')';
    }
}

// 6. Monta WHERE
if (!empty($clauses)) {
    $query .= ' WHERE ' . implode(' AND ', $clauses);
}

// 7. Executa a query
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}
$stmt->execute();

// 8. Retorna JSON
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['rows' => $rows]);
