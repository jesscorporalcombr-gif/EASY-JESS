<?php
require_once("../../conexao.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$ids = $input['ids'] ?? [];

if (empty($ids)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum ID recebido.']);
    exit;
}

$tabelaHtml = '';
$temUso = false;
foreach ($ids as $id) {
    
    $query = $pdo->prepare("SELECT item, realizados, descontados, transferidos, convertidos FROM venda_itens WHERE id = ?");
    $query->execute([$id]);
    $res = $query->fetch(PDO::FETCH_ASSOC);

    if (!$res) continue;

    $campos = ['realizados', 'descontados', 'transferidos', 'convertidos'];
    $usoDetectado = false;

    foreach ($campos as $campo) {
        if (!empty($res[$campo]) && $res[$campo] != '0') {
            $usoDetectado = true;
            break;
        }
    }

    if ($usoDetectado) {
        $temUso = true;
        $tabelaHtml .= "<tr>
            <td>" . htmlspecialchars($res['item'] ?? 'N/A') . "</td>
            <td>" . ($res['realizados'] ?? '-') . "</td>
            <td>" . ($res['descontados'] ?? '-') . "</td>
            <td>" . ($res['transferidos'] ?? '-') . "</td>
            <td>" . ($res['convertidos'] ?? '-') . "</td>
        </tr>";
    }
}

if ($temUso) {
    $tabelaFinal = "<table border='1' cellpadding='6' style='width:100%; text-align:left; border-collapse:collapse'>
        <thead>
            <tr style='background:#f0f0f0;'>
                <th>Serviço</th>
                <th>Realizados</th>
                <th>Descontados</th>
                <th>Transferidos</th>
                <th>Convertidos</th>
            </tr>
        </thead>
        <tbody>{$tabelaHtml}</tbody>
    </table>";

    echo json_encode(['sucesso' => true, 'tabelaHtml' => $tabelaFinal]);
} else {
    echo json_encode(['sucesso' => false, 'tabelaHtml' => '']);
}
