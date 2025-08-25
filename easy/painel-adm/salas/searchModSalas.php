<?php
session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

// Define se deve incluir inativos: ?mostrarInativos=1
$mostrarInativos = isset($_GET['mostrarInativos']) && ($_GET['mostrarInativos'] === '1' || strtolower($_GET['mostrarInativos']) === 'true');

// Campos fixos que serão retornados
$select = [
    // da tabela de contratos
    'id AS sala_id',
    'nome',
    'descricao',
    'foto',
    'excluido AS status'
];

// Monta FROM + JOIN
$query = "
    SELECT " . implode(", ", $select) . "
    FROM salas
";

// Filtro padrão: se não quiser mostrar inativos, traz só ativos (`ativo` = 1)
$clauses = [];
$params = [];

if (!$mostrarInativos) {
    $clauses[] = "excluido = 0";
}

// Se houver cláusulas, adiciona WHERE
if (!empty($clauses)) {
    $query .= " WHERE " . implode(" AND ", $clauses);
}

// Opcional: ordenação padrão (por exemplo, nome asc)
$query .= " ORDER BY id ASC";

try {
    $stmt = $pdo->prepare($query);
    // nenhum parâmetro dinâmico além do ativo que já foi tratado direto na cláusula
    $stmt->execute();
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
