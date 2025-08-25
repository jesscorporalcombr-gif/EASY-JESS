<?php
require_once(__DIR__ . "/../../conexao.php");
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$id_user_criacao    = $_SESSION['id_usuario'];
$user_criacao       = $_SESSION['nome_usuario'];
$id_user_alteracao  = intval($_SESSION['id_usuario']);
$user_alteracao     = $_SESSION['nome_usuario'];
$data_hora          = date('Y-m-d H:i:s');

// lê os IDs via JSON
$input = json_decode(file_get_contents('php://input'), true);
$ids   = $input['ids'] ?? [];

if (!is_array($ids) || empty($ids)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nenhum ID fornecido']);
    exit;
}

// ID da categoria “Receitas de Vendas”
define('RECEITAS_VENDAS_CATEGORY_ID', 5);

$pdo->beginTransaction();
try {
    // 1) busca os registros iniciais
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("
        SELECT id, transferencia, id_categoria, id_transferencia
        FROM financeiro_extrato
        WHERE id IN ($in)
    ");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $toDelete = [];
    $isBatch  = count($ids) > 1;

    foreach ($rows as $row) {
        $id       = (int)$row['id'];
        $catId    = (int)$row['id_categoria'];
        $isTrans  = (int)$row['transferencia'] === 1;

        // nunca receitas de vendas
        if ($catId === RECEITAS_VENDAS_CATEGORY_ID) {
            continue;
        }

        if (!$isTrans) {
            // registro comum
            $toDelete[] = $id;
        } else {
            // transferência
            if ($isBatch) {
                // em lote, não excluir transferências
                continue;
            }
            // exclusão individual: inclui sempre o próprio
            $toDelete[] = $id;

            // e mais todos que compartilham a mesma chave de transferência
            $groupKey = $row['id_transferencia'];
            if ($groupKey) {
                $stmt2 = $pdo->prepare("
                    SELECT id
                    FROM financeiro_extrato
                    WHERE transferencia = 1
                      AND id_transferencia = :grp
                ");
                $stmt2->execute([':grp' => $groupKey]);
                $paired = $stmt2->fetchAll(PDO::FETCH_COLUMN, 0);
                foreach ($paired as $pid) {
                    $toDelete[] = (int)$pid;
                }
            }
        }
    }

    // garante IDs únicos
    $toDelete = array_values(array_unique($toDelete));

    // 2) monta DELETE com placeholders nomeados
    if (!empty($toDelete)) {
        $placeholders = [];
        $params       = [];
        foreach ($toDelete as $i => $delId) {
            $ph          = ":id{$i}";
            $placeholders[] = $ph;
            $params[$ph]    = $delId;
        }

        $sql = "DELETE FROM financeiro_extrato WHERE id IN (" . implode(',', $placeholders) . ")";
        $stmtDel = $pdo->prepare($sql);
        $stmtDel->execute($params);
    }

    $pdo->commit();
    echo json_encode([
        'success'     => true,
        'deleted_ids' => $toDelete
    ]);
} catch (\Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Falha ao excluir: ' . $e->getMessage()
    ]);
}
