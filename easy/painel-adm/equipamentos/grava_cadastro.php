<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg = '', $data = []) {
  echo json_encode(['success' => $ok, 'msg' => $msg, 'data' => $data],
    JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

function cleanIntOrNull($v){
  if ($v === '' || $v === null) return null;
  return (int)$v;
}
function cleanStrOrNull($v){
  $v = trim((string)$v);
  return $v === '' ? null : $v;
}
function isValidDateYmd($s){
  if (!$s) return false;
  $p = explode('-', $s);
  if (count($p) !== 3) return false;
  return checkdate((int)$p[1], (int)$p[2], (int)$p[0]);
}
function safeDateOrNull($s){
  $s = trim((string)$s);
  return isValidDateYmd($s) ? $s : null;
}

// caminho de upload
$pasta = $_SESSION['x_url'] ?? '';
$baseDir = realpath(__DIR__ . '/..');                 // .../painel/equipamentos
$uploadDir = $baseDir . '/../' . ($pasta ? "$pasta/" : '') . 'img/equipamentos/';

if (!is_dir($uploadDir)) {
  @mkdir($uploadDir, 0775, true);
  if (!is_dir($uploadDir)) jexit(false, 'Não foi possível criar a pasta de imagens.');
}

try {
  $id                 = isset($_POST['id']) ? (int)$_POST['id'] : 0;

  $nome               = cleanStrOrNull($_POST['frm-nome'] ?? '');
  $marca              = cleanStrOrNull($_POST['frm-marca'] ?? '');
  $modelo             = cleanStrOrNull($_POST['frm-modelo'] ?? '');
  $anvisa             = cleanStrOrNull($_POST['frm-anvisa'] ?? '');

  $ag_paralelo        = isset($_POST['frm-paralelo']) ? (int)$_POST['frm-paralelo'] : 0;
  $patrimonio         = isset($_POST['frm-patrimonio']) ? (int)$_POST['frm-patrimonio'] : 1;

  $data_compra        = safeDateOrNull($_POST['frm-data_compra'] ?? null);
  $nota_fiscal_compra = cleanIntOrNull($_POST['frm-nota_fiscal_compra'] ?? null);
  // se você adicionar a coluna depois, descomente:
  $numero_serie       = cleanStrOrNull($_POST['frm-numero_serie'] ?? null);

  $data_ultima_rev    = safeDateOrNull($_POST['frm-data_ultima_revisao'] ?? null);
  $data_proxima_rev   = safeDateOrNull($_POST['frm-data_proxima_revisao'] ?? null);

  $pag_fabricante     = cleanStrOrNull($_POST['frm-pag_fabricante'] ?? '');
  $site_referencia    = cleanStrOrNull($_POST['frm-site_referencia'] ?? '');

  $descricao          = cleanStrOrNull($_POST['frm-descricao'] ?? '');
  $excluido           = isset($_POST['frm-excluido']) ? (int)$_POST['frm-excluido'] : 0;

  if (!$nome) jexit(false, 'Informe o nome do equipamento.');

  // se não é patrimônio, zera campos
  if ($patrimonio !== 1) {
    $data_compra        = null;
    $nota_fiscal_compra = null;
    $numero_serie       = null;
  }

  // trata upload de foto (opcional)
  $newFoto = null; $newExt = null;
  if (isset($_FILES['input-foto_cadEquipamento']) && is_uploaded_file($_FILES['input-foto_cadEquipamento']['tmp_name'])) {
    $f  = $_FILES['input-foto_cadEquipamento'];
    $sz = (int)$f['size'];
    if ($sz > 5*1024*1024) jexit(false, 'Imagem muito grande (limite 5MB).');

    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
      jexit(false, 'Formato de imagem não suportado. Use jpg, png ou webp.');
    }
    $newExt  = $ext === 'jpeg' ? 'jpg' : $ext;
    $newFoto = 'equip_' . date('Ymd_His') . '_' . substr(md5($f['name'].microtime(true)),0,6) . '.' . $newExt;

    if (!move_uploaded_file($f['tmp_name'], $uploadDir . $newFoto)) {
      jexit(false, 'Falha ao gravar a imagem.');
    }
  }

  $pdo->beginTransaction();

  if ($id > 0) {
    // pega foto antiga se for trocar
    $oldFoto = null;
    if ($newFoto) {
      $st = $pdo->prepare("SELECT foto FROM equipamentos WHERE id = :id");
      $st->execute([':id'=>$id]);
      $oldFoto = $st->fetchColumn();
    }

   
    $sql = "UPDATE equipamentos
               SET nome               = :nome,
                   marca              = :marca,
                   modelo             = :modelo,
                   anvisa             = :anvisa,
                   ag_paralelo        = :ag_paralelo,
                   patrimonio         = :patrimonio,
                   numero_serie = :numero_serie,
                   data_compra        = :data_compra,
                   nota_fiscal_compra = :nota_fiscal_compra,
                   data_ultima_revisao= :data_ultima_revisao,
                   data_proxima_revisao= :data_proxima_revisao,
                   pag_fabricante     = :pag_fabricante,
                   site_referencia    = :site_referencia,
                   descricao          = :descricao,
                   excluido           = :excluido,
                   modificado         = :modificado"
             . ($newFoto ? ", foto = :foto" : "")
             . " WHERE id = :id";

    $st = $pdo->prepare($sql);
    $st->bindValue(':id', $id, PDO::PARAM_INT);
    $st->bindValue(':nome', $nome);
    $st->bindValue(':marca', $marca);
    $st->bindValue(':modelo', $modelo);
    $st->bindValue(':anvisa', $anvisa);
    $st->bindValue(':ag_paralelo', $ag_paralelo, PDO::PARAM_INT);
    $st->bindValue(':patrimonio', $patrimonio, PDO::PARAM_INT);
    $st->bindValue(':numero_serie', $numero_serie);
    $st->bindValue(':data_compra', $data_compra);
    $st->bindValue(':nota_fiscal_compra', $nota_fiscal_compra);
    $st->bindValue(':data_ultima_revisao', $data_ultima_rev);
    $st->bindValue(':data_proxima_revisao', $data_proxima_rev);
    $st->bindValue(':pag_fabricante', $pag_fabricante);
    $st->bindValue(':site_referencia', $site_referencia);
    $st->bindValue(':descricao', $descricao);
    $st->bindValue(':excluido', $excluido, PDO::PARAM_INT);
    $st->bindValue(':modificado', date('Y-m-d H:i:s'));
    if ($newFoto) $st->bindValue(':foto', $newFoto);
    $st->execute();

    // apaga foto antiga se trocou
    if ($newFoto && $oldFoto) {
      $oldPath = $uploadDir . $oldFoto;
      if (is_file($oldPath)) @unlink($oldPath);
    }

    $pdo->commit();

    $titulo_modal = $nome . '<span class="status-serv ' . ($excluido==0?'status-ativo':'status-deletado') . '">' . ($excluido==0?'Ativo':'Deletado') . '</span>';
    jexit(true, 'Atualizado.', [
      'id' => $id,
      'foto_head' => $newFoto ? ('../' . ($pasta ? "$pasta/" : '') . 'img/equipamentos/' . $newFoto) : null,
      'titulo' => $titulo_modal
    ]);

  } else {
    // INSERT
    $sql = "INSERT INTO equipamentos
              (nome, marca, modelo, anvisa, ag_paralelo, patrimonio, numero_serie,
               data_compra, nota_fiscal_compra, data_ultima_revisao, data_proxima_revisao,
               pag_fabricante, site_referencia, descricao, excluido, foto, created, modificado)
            VALUES
              (:nome, :marca, :modelo, :anvisa, :ag_paralelo, :patrimonio, :numero_serie,
               :data_compra, :nota_fiscal_compra, :data_ultima_revisao, :data_proxima_revisao,
               :pag_fabricante, :site_referencia, :descricao, :excluido, :foto, :created, :modificado)";
    $st = $pdo->prepare($sql);
    $st->bindValue(':nome', $nome);
    $st->bindValue(':marca', $marca);
    $st->bindValue(':modelo', $modelo);
    $st->bindValue(':anvisa', $anvisa);
    $st->bindValue(':ag_paralelo', $ag_paralelo, PDO::PARAM_INT);
    $st->bindValue(':patrimonio', $patrimonio, PDO::PARAM_INT);
    $st->bindValue(':numero_serie', $numero_serie);
    $st->bindValue(':data_compra', $data_compra);
    $st->bindValue(':nota_fiscal_compra', $nota_fiscal_compra);
    $st->bindValue(':data_ultima_revisao', $data_ultima_rev);
    $st->bindValue(':data_proxima_revisao', $data_proxima_rev);
    $st->bindValue(':pag_fabricante', $pag_fabricante);
    $st->bindValue(':site_referencia', $site_referencia);
    $st->bindValue(':descricao', $descricao);
    $st->bindValue(':excluido', $excluido, PDO::PARAM_INT);
    $st->bindValue(':foto', $newFoto);
    $now = date('Y-m-d H:i:s');
    $st->bindValue(':created', $now);
    $st->bindValue(':modificado', $now);
    $st->execute();

    $newId = (int)$pdo->lastInsertId();
    $pdo->commit();

    $titulo_modal = $nome . '<span class="status-serv ' . ($excluido==0?'status-ativo':'status-deletado') . '">' . ($excluido==0?'Ativo':'Deletado') . '</span>';
    jexit(true, 'Criado.', [
      'id' => $newId,
      'foto_head' => $newFoto ? ('../' . ($pasta ? "$pasta/" : '') . 'img/equipamentos/' . $newFoto) : null,
      'titulo' => $titulo_modal
    ]);
  }

} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  jexit(false, 'Erro ao gravar cadastro.', ['detail' => $e->getMessage()]);
}
