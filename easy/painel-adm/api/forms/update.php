<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');
function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }

$id          = (int)($_POST['id'] ?? 0);
$name        = isset($_POST['name']) ? trim($_POST['name']) : null;
$description = isset($_POST['description']) ? trim($_POST['description']) : null;
$schema_json = $_POST['schema_json'] ?? null;

if ($id<=0) out(false, ['error'=>'id obrigatório'], 400);
if ($schema_json !== null) {
  $decoded = json_decode($schema_json, true);
  if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) out(false, ['error'=>'schema_json inválido'], 400);
}

$pdo->beginTransaction();
try {
  // pega versão atual
  $cur = $pdo->prepare("SELECT version, schema_json FROM forms WHERE id=:id FOR UPDATE");
  $cur->execute([':id'=>$id]);
  $row = $cur->fetch(PDO::FETCH_ASSOC);
  if (!$row) { $pdo->rollBack(); out(false, ['error'=>'form não encontrado'], 404); }
  $newVersion = (int)$row['version'] + 1;

  // atualiza base
  $sets = ["version=:v"];
  $params = [':v'=>$newVersion, ':id'=>$id];
  if ($name !== null) { $sets[]="name=:n"; $params[':n']=$name; }
  if ($description !== null) { $sets[]="description=:d"; $params[':d']=$description; }
  if ($schema_json !== null) { $sets[]="schema_json=CAST(:s AS JSON)"; $params[':s']=$schema_json; }
  $sql = "UPDATE forms SET ".implode(", ", $sets).", updated_at=NOW() WHERE id=:id";
  $pdo->prepare($sql)->execute($params);

  // grava versão nova (usa o último schema salvo — se veio null, repete o atual)
  $schemaForHistory = $schema_json ?? $row['schema_json'];
  $pdo->prepare("INSERT INTO form_versions (form_id, version, schema_json, created_by)
                 VALUES (:f,:v,CAST(:s AS JSON),:u)")
      ->execute([
        ':f'=>$id, ':v'=>$newVersion, ':s'=>$schemaForHistory, ':u'=>($_SESSION['user_id'] ?? null)
      ]);

  $pdo->commit();
  out(true, ['id'=>$id, 'version'=>$newVersion]);
} catch (Exception $e) {
  $pdo->rollBack();
  out(false, ['error'=>$e->getMessage()], 500);
}
