<?php
// servicos/UModServTabProd.php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function out($ok, $http = 200, $extra = []) {
  http_response_code($http);
  echo json_encode(array_merge(['ok' => $ok], $extra));
  exit;
}

function brToFloat($v) {
  if ($v === null || $v === '') return null;
  if (is_numeric($v)) return (float)$v;
  $s = str_replace(['.', ' '], '', (string)$v);
  $s = str_replace(',', '.', $s);
  return is_numeric($s) ? (float)$s : null;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!$payload) $payload = $_POST;

$id_servico = isset($payload['id_servico']) ? (int)$payload['id_servico'] : 0;
$item = $payload['item'] ?? [];
if ($id_servico <= 0 || !is_array($item)) {
  out(false, 400, ['error' => 'Dados insuficientes.']);
}

$id_serv_prod   = isset($item['id_serv_prod']) && $item['id_serv_prod'] !== '' ? (int)$item['id_serv_prod'] : null;
$id_produto     = isset($item['id_produto']) ? (int)$item['id_produto'] : 0;
if ($id_produto <= 0) {
  out(false, 400, ['error' => 'id_produto requerido.']);
}

$quantidade     = brToFloat($item['quantidade'] ?? null);
if ($quantidade === null) $quantidade = 0.0; // coluna é NOT NULL com default 0.000

$custo_unitario = brToFloat($item['custo_unitario'] ?? null); // pode ser NULL (coluna permite)

// Se custo_unitario não vier, usa produtos.preco_custo (se vier NULL no banco, fica NULL mesmo)
if ($custo_unitario === null) {
  $st = $pdo->prepare("SELECT preco_custo FROM produtos WHERE id = :id");
  $st->execute([':id' => $id_produto]);
  $tmp = $st->fetchColumn();
  $custo_unitario = ($tmp === false ? null : (float)$tmp);
}

try {
  if ($id_serv_prod) {
    // UPDATE
    $sql = $pdo->prepare("
      UPDATE servicos_produtos
         SET id_produto = :id_produto,
             quantidade = :quantidade,
             custo_unitario = :custo_unitario
       WHERE id = :id_serv_prod
         AND id_servico = :id_servico
    ");
    $sql->execute([
      ':id_produto'     => $id_produto,
      ':quantidade'     => $quantidade,
      ':custo_unitario' => $custo_unitario, // aceita null
      ':id_serv_prod'   => $id_serv_prod,
      ':id_servico'     => $id_servico,
    ]);
  } else {
    // Evita duplicar o mesmo produto para o serviço
    $ck = $pdo->prepare("SELECT id FROM servicos_produtos WHERE id_servico = :s AND id_produto = :p");
    $ck->execute([':s' => $id_servico, ':p' => $id_produto]);
    $existId = $ck->fetchColumn();

    if ($existId) {
      $id_serv_prod = (int)$existId;
      $sql = $pdo->prepare("
        UPDATE servicos_produtos
           SET quantidade = :quantidade,
               custo_unitario = :custo_unitario
         WHERE id = :id
      ");
      $sql->execute([
        ':quantidade'     => $quantidade,
        ':custo_unitario' => $custo_unitario,
        ':id'             => $id_serv_prod,
      ]);
    } else {
      $sql = $pdo->prepare("
        INSERT INTO servicos_produtos (id_servico, id_produto, quantidade, custo_unitario)
        VALUES (:id_servico, :id_produto, :quantidade, :custo_unitario)
      ");
      $sql->execute([
        ':id_servico'     => $id_servico,
        ':id_produto'     => $id_produto,
        ':quantidade'     => $quantidade,
        ':custo_unitario' => $custo_unitario,
      ]);
      $id_serv_prod = (int)$pdo->lastInsertId();
    }
  }

  // Retorna a linha consolidada para render na tabela
  $st = $pdo->prepare("
    SELECT
      sp.id           AS id_serv_prod,
      sp.id_servico,
      sp.id_produto,
      p.nome          AS produto,
      COALESCE(p.foto, p.imagem) AS foto_produto,
      p.unidade       AS unidade,
      sp.quantidade,
      COALESCE(sp.custo_unitario, p.preco_custo) AS custo_unitario
    FROM servicos_produtos sp
    JOIN produtos p ON p.id = sp.id_produto
    WHERE sp.id = :id
  ");
  $st->execute([':id' => $id_serv_prod]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    out(false, 500, ['error' => 'Falha ao carregar o item salvo.']);
  }

  // Normalização numérica para o front
  $row['id_serv_prod']   = (int)$row['id_serv_prod'];
  $row['id_servico']     = (int)$row['id_servico'];
  $row['id_produto']     = (int)$row['id_produto'];
  $row['quantidade']     = (float)$row['quantidade'];
  $row['custo_unitario'] = ($row['custo_unitario'] === null) ? 0.0 : (float)$row['custo_unitario'];

  out(true, 200, ['row' => $row]);

} catch (Throwable $e) {
  out(false, 500, ['error' => 'Erro ao salvar item', 'detail' => $e->getMessage()]);
}
