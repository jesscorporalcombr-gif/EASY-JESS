<?php
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }

$token = $_GET['token'] ?? '';
if ($token === '') out(false, ['error'=>'token obrigatório'], 400);

$st = $pdo->prepare("SELECT t.*, f.name, f.version, JSON_EXTRACT(f.schema_json, '$') AS schema_json
                     FROM form_tokens t
                     JOIN forms f ON f.id = t.form_id
                     WHERE t.token = :token AND t.revoked = 0");
$st->execute([':token'=>$token]);
$row = $st->fetch(PDO::FETCH_ASSOC);
if (!$row) out(false, ['error'=>'Token inválido'], 404);

// validade
if ($row['expires_at'] !== null && strtotime($row['expires_at']) < time()) {
  out(false, ['error'=>'Token expirado'], 410);
}
if ((int)$row['max_uses'] > 0 && (int)$row['used_count'] >= (int)$row['max_uses']) {
  out(false, ['error'=>'Token já utilizado'], 409);
}

out(true, [
  'form'=>[
    'id'=>(int)$row['form_id'],
    'name'=>$row['name'],
    'version'=>(int)$row['version'],
    'schema_json'=>$row['schema_json']
  ],
  'patient_id'=>$row['patient_id']
]);
