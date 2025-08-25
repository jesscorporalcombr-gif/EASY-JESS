<?php
header('Content-Type: application/json');
try {
    require_once("../../conexao.php");
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Recebe IDs pela query string, separados por vírgula
    $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
    $ids = array_filter(array_map('intval', $ids));

    if (empty($ids)) {
        echo json_encode(['profissionais' => [], 'defaults' => []]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Traz só o que o profissional executa E cujo serviço NÃO está excluído
    $sql = "
        SELECT 
            sp.id_profissional,
            s.id               AS id_servico,
            s.servico,
            sp.tempo,
            sp.preco,
            s.tempo            AS tempo_padrao,
            s.valor_venda
        FROM servicos_profissional sp
        INNER JOIN servicos s 
                ON s.id = sp.id_servico
               AND s.excluido = 0
        WHERE sp.id_profissional IN ($placeholders)
          AND sp.executa = 1
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($ids as $i => $pid) {
        $stmt->bindValue($i + 1, $pid, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monta saída
    $profissionais = [];
    $defaults = [];
    foreach ($rows as $r) {
        $pid = (int)$r['id_profissional'];
        $sid = (int)$r['id_servico'];
        // lista por profissional
        $profissionais[$pid][] = [
            'id_servico' => $sid,
            'tempo'      => isset($r['tempo']) ? (int)$r['tempo'] : null,
            'preco'      => $r['preco'],
            'servico'    => $r['servico']
        ];
        // defaults do serviço
        if (!isset($defaults[$sid])) {
            $defaults[$sid] = [
                'tempo' => (int)$r['tempo_padrao'],
                'preco' => $r['valor_venda']
            ];
        }
    }

    echo json_encode([
        'profissionais' => $profissionais,
        'defaults'      => $defaults
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
