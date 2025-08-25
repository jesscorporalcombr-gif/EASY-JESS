<?php
@session_start();
header('Content-Type: application/json');

/**
 * /easy/painel-adm/forms_creator/version_get.php
 * Retorna o schema_json de UMA versão específica.
 * Preferência: GET version_id.
 * Fallback opcional: se vier form_id SEM version_id, pega a mais recente (draft > published).
 */

try {
  require_once(__DIR__ . '/../../conexao.php'); // caminho correto a partir de /forms

  $version_id = isset($_GET['version_id']) ? (int)$_GET['version_id'] : 0;
  $form_id    = isset($_GET['form_id'])    ? (int)$_GET['form_id']    : 0;

  if ($version_id > 0) {
    $stmt = $pdo->prepare('SELECT schema_json FROM form_versions WHERE id = ?');
    $stmt->execute([$version_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
      echo json_encode(['ok' => false, 'error' => 'Versão não encontrada']);
      exit;
    }
    $schema = json_decode($row['schema_json'], true);
    echo json_encode(['ok' => true, 'schema' => $schema]);
    exit;
  }

  // Fallback: permitir chamar com form_id (pega a mais recente)
  if ($form_id > 0) {
    // 1º tenta a draft mais recente
    $draft = $pdo->prepare("SELECT id, schema_json FROM form_versions WHERE form_id = ? AND status = 'draft' ORDER BY versao DESC LIMIT 1");
    $draft->execute([$form_id]);
    $row = $draft->fetch(PDO::FETCH_ASSOC);

    // senão, tenta a publicada mais recente
    if (!$row) {
      $pub = $pdo->prepare("SELECT id, schema_json FROM form_versions WHERE form_id = ? AND status = 'published' ORDER BY versao DESC LIMIT 1");
      $pub->execute([$form_id]);
      $row = $pub->fetch(PDO::FETCH_ASSOC);
    }

    if (!$row) {
      echo json_encode(['ok' => false, 'error' => 'Nenhuma versão encontrada para este formulário']);
      exit;
    }

    $schema = json_decode($row['schema_json'], true);
    echo json_encode(['ok' => true, 'schema' => $schema, 'version_id' => (int)$row['id']]);
    exit;
  }

  echo json_encode(['ok' => false, 'error' => 'Parâmetros inválidos (envie version_id ou form_id)']);
} catch (Exception $e) {
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
