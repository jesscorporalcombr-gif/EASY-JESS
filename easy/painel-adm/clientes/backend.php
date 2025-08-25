<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once('../verificar-permissao.php');
require_once('../../conexao.php');
// Parâmetros de paginação e busca
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = max(1, (int)($_GET['perPage'] ?? $_GET['perPage']));
$search  = $_GET['search'] ?? '';
$offset  = ($page - 1) * $perPage;

// 1) Contagem total para paginação
$countSql = "SELECT COUNT(*) FROM clientes c WHERE c.nome LIKE :search";
$countStmt = $pdo->prepare($countSql);
$countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$countStmt->execute();
$total = $countStmt->fetchColumn();

// 2) Query otimizada com índices e agregações em subqueries
$sql = <<<SQL
SELECT
  c.id,
  c.foto,
  c.nome,
  DATE_FORMAT(c.data_cadastro, '%d/%m/%Y') AS cliente_desde,
  c.celular AS telefone,
  -- último comparecimento
  (
    SELECT DATE_FORMAT(MAX(a.data), '%d/%m/%Y')
    FROM agendamentos a
    WHERE a.id_cliente = c.id
      AND a.status <> 'cancelado'
      AND a.data < CURDATE()
  ) AS compareceu_em,
  -- próximo agendamento
  (
    SELECT DATE_FORMAT(MIN(a2.data), '%d/%m/%Y')
    FROM agendamentos a2
    WHERE a2.id_cliente = c.id
      AND a2.status <> 'cancelado'
      AND a2.data >= CURDATE()
  ) AS proximo_agendamento,
  -- saldo de serviços pendentes
  COALESCE(saldo_sub.pendente,0) AS saldo,
  -- situação
  CASE
    WHEN saldo_sub.count_vi = 0 THEN 'Não Ativado'
    WHEN saldo_sub.pendente > 0 AND saldo_sub.min_validade < CURDATE() THEN 'Vencido'
    WHEN saldo_sub.pendente > 0 THEN 'Ativo'
    ELSE 'Inativo'
  END AS situacao
FROM clientes c
-- subquery agregada de venda_itens, indexada por id_cliente
LEFT JOIN (
  SELECT
    vi.id_cliente,
    COUNT(*) AS count_vi,
    SUM(
      vi.quantidade - COALESCE(vi.realizados,0)
      - COALESCE(vi.convertidos,0) - COALESCE(vi.descontados,0)
      - COALESCE(vi.transferidos,0)
    ) AS pendente,
    MIN(vi.data_validade) AS min_validade
  FROM venda_itens vi
  WHERE vi.tipo_item = 'servico' AND vi.venda = 1
  GROUP BY vi.id_cliente
) saldo_sub ON saldo_sub.id_cliente = c.id
WHERE c.nome LIKE :search
ORDER BY c.nome
LIMIT :offset, :perPage
SQL;

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['total'=>$total, 'rows'=>$rows]);
?>