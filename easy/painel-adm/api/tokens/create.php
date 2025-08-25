<?php
@session_start();
require_once('../../../conexao.php');
header('Content-Type: application/json; charset=utf-8');

function out($ok,$data=[],$http=200){ http_response_code($http); echo json_encode(array_merge(['ok'=>$ok],$data)); exit; }
function make_token($len=48){ return rtrim(strtr(base64_encode(random_bytes($len)), '+/', '-_'), '='); }

// CONFIG: ajuste a URL pública do respondedor completo
$BASE_URL_PUBLIC = 'https://www.easyclinicas.com.br/easy/painel-adm/forms/fill_full.php';

$form_id   = (int)($_POST['form_id'] ?? 0);
$patient_id= $_POST['patient_id'] ?? null; // opcional
$expires_h = (int)($_POST['expires_hours'] ?? 72);
$clinic_id = $_POST['clinic_id'] ?? null;

if ($form_id <= 0) out(false, ['error'=>'form_id obrigatório'], 400);

// pega versão atual do form
$st = $pdo->prepare("SELECT version FROM forms WHERE id=:id AND is_active=1");
$st->execute([':id'=>$form_id]);
$row = $st->fetch(PDO::FETCH_ASSOC);
if (!$row) out(false, ['error'=>'Form não encontrado/ativo'], 404);

$token = make_token(32);
$expires_at = (new DateTime("+{$expires_h} hours"))->format('Y-m-d H:i:s');

$ins = $pdo->prepare("INSERT INTO form_tokens (clinic_id, form_id, token, patient_id, expires_at, max_uses, created_by)
                      VALUES (:c, :f, :t, :p, :e, 1, :u)");
$ins->execute([
  ':c'=>($clinic_id===''?null:$clinic_id),
  ':f'=>$form_id,
  ':t'=>$token,
  ':p'=>($patient_id===''?null:$patient_id),
  ':e'=>$expires_at,
  ':u'=>($_SESSION['user_id'] ?? null)
]);

$link = $BASE_URL_PUBLIC . '?token=' . urlencode($token);
out(true, [
  'token'=>$token,
  'expires_at'=>$expires_at,
  'link'=>$link
]);
