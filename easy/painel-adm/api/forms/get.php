<?php
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');
function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id<=0) out(false, ['error'=>'id obrigatório'], 400);

$stmt = $pdo->prepare("SELECT id, clinic_id, name, description, version, is_active, JSON_EXTRACT(schema_json, '$') AS schema_json
                       FROM forms WHERE id=:id");
$stmt->execute([':id'=>$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) out(false, ['error'=>'não encontrado'], 404);

out(true, ['form'=>$row]);
