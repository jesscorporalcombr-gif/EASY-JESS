<?php

require_once("../../conexao.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_agendamento = intval($_POST['id_agendamento'] ?? 0);

// ============================
// Função idêntica ao seu modelo
// ============================
function servicos_sem_credito_do_cliente($pdo, $id_agendamento) {
    // 1. Descobrir cliente e data do agendamento-base
    $stmt = $pdo->prepare("SELECT id_cliente, data FROM agendamentos WHERE id = ?");
    $stmt->execute([$id_agendamento]);
    $ag = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$ag) return [];

    $id_cliente = $ag['id_cliente'];
    $dataAgenda = $ag['data'];

    // 2. Buscar todos os agendamentos do cliente neste dia
    $stmt = $pdo->prepare("SELECT id, id_servico, preco, status FROM agendamentos WHERE id_cliente = ? AND data = ?");
    $stmt->execute([$id_cliente, $dataAgenda]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Coletar serviços únicos
    $ids_clientes_servicos = [];
    foreach ($agendamentos as $ag) {
        if(
            $ag['status'] != 'Finalizado' &&
            $ag['status'] != 'Cancelado' &&
            $ag['status'] != 'Faltou' &&
            $ag['status'] != 'NRealizado'
        ){
            $key = "{$id_cliente}-{$ag['id_servico']}";
            $ids_clientes_servicos[$key] = [
                'id_cliente' => $id_cliente,
                'id_servico' => $ag['id_servico']
            ];
        }
    }

    // 4. Consulta venda_itens (primeira consulta)
    $saldo_vendas_map = [];
    if (!empty($ids_clientes_servicos)) {
        $placeholders_cliente_servico = [];
        $params_cliente_servico = [];
        foreach ($ids_clientes_servicos as $comb) {
            $placeholders_cliente_servico[] = '(id_cliente = ? AND id_item = ?)';
            $params_cliente_servico[] = $comb['id_cliente'];
            $params_cliente_servico[] = $comb['id_servico'];
        }
        $sql_vendas = "
            SELECT id_cliente, id_item, 
                SUM(quantidade - realizados - transferidos - convertidos - descontados) AS saldo
            FROM venda_itens
            WHERE venda = 1
            AND tipo_item = 'servico'
            AND (quantidade - realizados - transferidos - convertidos - descontados) > 0
            AND (data_validade IS NULL OR data_validade = '' OR data_validade >= ?)
            AND (" . implode(' OR ', $placeholders_cliente_servico) . ")
            GROUP BY id_cliente, id_item
        ";
        $stmt_vendas = $pdo->prepare($sql_vendas);
        $stmt_vendas->execute(array_merge([$dataAgenda], $params_cliente_servico));
        while ($row = $stmt_vendas->fetch(PDO::FETCH_ASSOC)) {
            $key = "{$row['id_cliente']}-{$row['id_item']}";
            $saldo_vendas_map[$key] = (int)$row['saldo'];
        }
    }

    // 5. Consulta agendamentos anteriores (segunda consulta)
    $sql_agendamentos_anteriores = "
        SELECT id_cliente, id_servico, data, id
        FROM agendamentos
        WHERE id_cliente = ?
          AND status IN ('Agendado', 'Confirmado', 'Em Atendimento', 'Atendimento Concluido', 'Aguardando')
    ";
    $stmt_agenda_ant = $pdo->prepare($sql_agendamentos_anteriores);
    $stmt_agenda_ant->execute([$id_cliente]);
    $agendamentos_anteriores = $stmt_agenda_ant->fetchAll(PDO::FETCH_ASSOC);
    $agendamentos_map = [];

    foreach ($agendamentos_anteriores as $ant) {
        $key = "{$ant['id_cliente']}-{$ant['id_servico']}";
        if (!isset($agendamentos_map[$key])) $agendamentos_map[$key] = [];
        $agendamentos_map[$key][] = ['data' => $ant['data'], 'id' => $ant['id']];
    }

    // 6. Avaliar saldo para cada agendamento do dia
    $servicos_sem_credito = [];

    foreach ($agendamentos as $agendamento) {
        if(
            $agendamento['status'] != 'Finalizado' &&
            $agendamento['status'] != 'Cancelado' &&
            $agendamento['status'] != 'Faltou' &&
            $agendamento['status'] != 'NRealizado'
        ){
            $key = "{$id_cliente}-{$agendamento['id_servico']}";
            $contagem = 0;
            if (!empty($agendamentos_map[$key])) {
                foreach ($agendamentos_map[$key] as $ant) {
                    if ($ant['data'] < $dataAgenda ||
                        ($ant['data'] == $dataAgenda && $ant['id'] < $agendamento['id'])) {
                        $contagem++;
                    }
                }
            }
            $saldo_venda = $saldo_vendas_map[$key] ?? 0;
            $quantidade_disponivel = $saldo_venda - $contagem;
            if ($saldo_venda < 1){
                $servicos_sem_credito[] = [
                    'id_servico' => $agendamento['id_servico'],
                    'preco'      => $agendamento['preco'],
                    'saldo'      => $saldo_venda,
                    'status'     => $agendamento['status']
                ];
            }
        }
    }

    return $servicos_sem_credito;
}

// ================
// Execução
// ================

// Buscar se existe agendamento
$stmt = $pdo->prepare("SELECT id FROM agendamentos WHERE id = ?");
$stmt->execute([$id_agendamento]);
$ag = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não existe, retorna lista vazia
if (!$ag) {
    echo json_encode(['itensVendaAg' => []]);
    exit;
}

// Faz a consulta e retorna como o modelo
$lista = servicos_sem_credito_do_cliente($pdo, $id_agendamento);
echo json_encode(['itensVendaAg' => $lista]);
exit;

?>
