<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }

$response_id = (int)($_POST['response_id'] ?? 0);
if ($response_id<=0) out(false, ['error'=>'response_id obrigatório'], 400);

if (!isset($_FILES['file']) || $_FILES['file']['error']!==UPLOAD_ERR_OK) {
  out(false, ['error'=>'arquivo ausente ou inválido'], 400);
}

$dir = __DIR__ . '/../../../storage/form_attachments';
if (!is_dir($dir)) @mkdir($dir, 0775, true);

$origName = $_FILES['file']['name'];
$ext = pathinfo($origName, PATHINFO_EXTENSION);
$fname = 'att_' . $response_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.'.$ext : '');
$dest = $dir . '/' . $fname;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
  out(false, ['error'=>'falha ao mover arquivo'], 500);
}

$relPath = 'storage/form_attachments/' . $fname;

$st = $pdo->prepare("INSERT INTO form_attachments (response_id, file_path, file_type, uploaded_by)
                     VALUES (:r, :p, :t, :u)");
$st->execute([
  ':r'=>$response_id,
  ':p'=>$relPath,
  ':t'=>($_FILES['file']['type'] ?? null),
  ':u'=>($_SESSION['user_id'] ?? null)
]);

out(true, ['path'=>$relPath]);
