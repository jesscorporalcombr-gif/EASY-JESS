<?php
session_start();
require_once('../conexao.php');
require_once('verificar-permissao.php');

// 1. Ler parâmetros básicos da URL
$table  = $_GET['table']  ?? '';
$fields = $_GET['fields'] ?? 'id, nome'; // Fallback caso não enviem campos

// 2. Verifica se a tabela existe no banco
$stmt = $pdo->prepare("SHOW TABLES LIKE :table");
$stmt->execute([':table' => $table]);
if ($stmt->rowCount() !== 1) {
    http_response_code(404);
    echo json_encode(['error' => 'Table not found']);
    exit;
}

// 3. Obter campos válidos da tabela (para não selecionar colunas inexistentes)
$stmt = $pdo->prepare("DESCRIBE `$table`");
$stmt->execute();
$tableFields = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

$requestedFields = explode(',', $fields);
// Intersect garante que só usaremos campos que existem na tabela
$validatedFields = array_intersect($requestedFields, $tableFields);

if (empty($validatedFields)) {
    echo json_encode(['error' => 'No valid fields']);
    exit;
}

// Monta lista de campos para o SELECT, com aspas invertidas para evitar problemas
$selectedFieldsSql = implode(', ', array_map(function($field) {
    return "`" . trim($field) . "`";
}, $validatedFields));

// 4. Montar a query básica
$query = "SELECT $selectedFieldsSql FROM `$table`";

// 5. Construir filtros dinâmicos a partir de $_GET (exceto alguns parâmetros)
$ignoredKeys = ['table','fields','all','PHPSESSID']; 
$clauses     = [];
$params      = [];

if (in_array('deletado_data_hora', $tableFields, true)) {
    $clauses[] = "`deletado_data_hora` IS NULL";
}

// Percorre os parâmetros GET e interpreta o filtro conforme o valor
foreach ($_GET as $key => $value) {
    if (!in_array($key, $ignoredKeys, true) && in_array($key, $tableFields)) {
        // Se o valor contém o delimitador de intervalo "<->"
        if (strpos($value, '<->') !== false) {
            list($start, $end) = array_map('trim', explode('<->', $value, 2));
            $clauses[] = "`$key` BETWEEN :{$key}_start AND :{$key}_end";
            $params["{$key}_start"] = $start;
            $params["{$key}_end"] = $end;
        }
        // Se o valor contém vírgulas, trata como lista (IN)
        else if (strpos($value, ',') !== false) {
            $items = array_map('trim', explode(',', $value));
            $placeholders = [];
            foreach ($items as $index => $item) {
                $ph = ":{$key}_{$index}";
                $placeholders[] = $ph;
                $params["{$key}_{$index}"] = $item;
            }
            $clauses[] = "`$key` IN (" . implode(', ', $placeholders) . ")";
        }
        // Caso simples: igualdade
        else {
            $clauses[] = "`$key` = :$key";
            $params[$key] = $value;
        }
    }
}

// Se existirem cláusulas de filtro, adiciona WHERE
if (!empty($clauses)) {
    $query .= ' WHERE ' . implode(' AND ', $clauses);
}

// 6. Preparar e executar a query
$stmt = $pdo->prepare($query);

// Fazer bind de cada parâmetro para evitar SQL injection
foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}

$stmt->execute();

// 7. Retornar resultado em JSON
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['rows' => $rows]);

?>
