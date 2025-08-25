<?php
require_once('../../conexao.php');
$data = json_decode(file_get_contents("php://input"), true);

$mensagem = trim($data['mensagem'] ?? '');
$mostrar_profissional = $data['mostrar_profissional'] ?? 0;
$mostrar_etiqueta_pagamento = $data['mostrar_etiqueta_pagamento'] ?? 0;
$mostrar_tempo_total = $data['mostrar_tempo_total'] ?? 0;
$mostrar_horario_procedimento = $data['mostrar_horario_procedimento'] ?? 0;
$mostrar_preco = $data['mostrar_preco'] ?? 0;
$mostrar_status = $data['mostrar_status'] ?? 0;

// Aqui só estou sugerindo update para id=1, mas pode adaptar para multiusuario
$stmt = $pdo->prepare("REPLACE INTO agenda_lembrete_padrao 
  (id, mensagem, mostrar_profissional, mostrar_etiqueta_pagamento, mostrar_tempo_total, mostrar_horario_procedimento, mostrar_preco, mostrar_status)
  VALUES (1, :mensagem, :mostrar_profissional, :mostrar_etiqueta_pagamento, :mostrar_tempo_total, :mostrar_horario_procedimento, :mostrar_preco, :mostrar_status)");
$stmt->bindValue(':mensagem', $mensagem);
$stmt->bindValue(':mostrar_profissional', $mostrar_profissional, PDO::PARAM_INT);
$stmt->bindValue(':mostrar_etiqueta_pagamento', $mostrar_etiqueta_pagamento, PDO::PARAM_INT);
$stmt->bindValue(':mostrar_tempo_total', $mostrar_tempo_total, PDO::PARAM_INT);
$stmt->bindValue(':mostrar_horario_procedimento', $mostrar_horario_procedimento, PDO::PARAM_INT);
$stmt->bindValue(':mostrar_preco', $mostrar_preco, PDO::PARAM_INT);
$stmt->bindValue(':mostrar_status', $mostrar_status, PDO::PARAM_INT);

echo json_encode(['success' => $stmt->execute()]);
