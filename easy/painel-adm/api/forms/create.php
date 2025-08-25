<?php
@session_start();
require_once('../../../conexao.php');
// require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok, $data=[], $http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok], $data)); exit; }

$clinic_id   = $_POST['clinic_id']   ?? null;
$name        = trim($_POST['name']   ?? '');
$description = trim($_POST['description'] ?? '');
$schema_json = $_POST['schema_json'] ?? '';

if ($name === '' || $schema_json === '') out(false, ['error'=>'name e schema_json são obrigatórios'], 400);

// valida JSON
$decoded = json_decode($schema_json, true);
if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
  out(false, ['error'=>'schema_json inválido (JSON)'], 400);
}

$pdo->beginTransaction();
try {
  $stmt = $pdo->prepare("INSERT INTO forms (clinic_id, name, description, schema_json, version, is_active, created_by)
                         VALUES (:clinic_id, :name, :description, CAST(:schema_json AS JSON), 1, 1, :created_by)");
  $stmt->execute([
    ':clinic_id'   => ($clinic_id === '' ? null : $clinic_id),
    ':name'        => $name,
    ':description' => $description,
    ':schema_json' => $schema_json,
    ':created_by'  => $_SESSION['user_id'] ?? null
  ]);
  $form_id = (int)$pdo->lastInsertId();

  $stmt2 = $pdo->prepare("INSERT INTO form_versions (form_id, version, schema_json, created_by)
                          VALUES (:form_id, 1, CAST(:schema_json AS JSON), :created_by)");
  $stmt2->execute([
    ':form_id'     => $form_id,
    ':schema_json' => $schema_json,
    ':created_by'  => $_SESSION['user_id'] ?? null
  ]);

  $pdo->commit();
  out(true, ['id'=>$form_id, 'version'=>1]);
} catch (Exception $e) {
  $pdo->rollBack();
  out(false, ['error'=>$e->getMessage()], 500);
}
