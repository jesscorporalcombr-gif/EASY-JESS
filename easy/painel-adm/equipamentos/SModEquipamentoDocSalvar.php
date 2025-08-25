<?php
// equipamentos/SModEquipamentosDocSalvar.php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg = '', $data = []) {
  echo json_encode(array_merge(['ok'=>$ok, 'msg'=>$msg], $data), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

function cleanStr($v, $max = 500) {
  $v = trim((string)$v);
  if ($max > 0 && mb_strlen($v) > $max) $v = mb_substr($v, 0, $max);
  return $v;
}
function isValidDateYmd($s) {
  if (!$s) return false;
  $p = explode('-', $s);
  return count($p) === 3 && checkdate((int)$p[1], (int)$p[2], (int)$p[0]);
}
function sanitizeFileName($name) {
  // mantém letras, números, espaço, ponto, hífen e underscore; troca o restante por _
  $name = preg_replace('/[^\pL\pN\s\.\-\_]/u', '_', $name);
  $name = preg_replace('/\s+/', '_', $name);
  // limita tamanho
  return mb_substr($name, 0, 180);
}

try {
  // -------- inputs obrigatórios/ opcionais --------
  $id              = (int)($_POST['id'] ?? 0);
  $id_equipamento  = (int)($_POST['id_equipamento'] ?? 0);

  $titulo          = cleanStr($_POST['titulo'] ?? '', 200);
  $descricao       = cleanStr($_POST['descricao'] ?? '', 2000);
  $tipo            = cleanStr($_POST['tipo'] ?? '', 40);          // ex.: Laudo, Nota Fiscal, etc.
  $data_arquivo_in = cleanStr($_POST['data_arquivo'] ?? '', 10);  // esperado YYYY-MM-DD
  $data_arquivo    = $data_arquivo_in && isValidDateYmd($data_arquivo_in) ? $data_arquivo_in : null;

  if ($id_equipamento <= 0 || $titulo === '') {
    jexit(false, 'Parâmetros inválidos.');
  }

  // -------- diretório de upload --------
  $pasta = $_SESSION['x_url'] ?? '';
  $baseDir   = realpath(__DIR__ . '/..'); // .../painel/equipamentos
  $uploadDir = $baseDir . '/../' . ($pasta ? "$pasta/" : '') . 'docs/equipamentos/';

  if (!is_dir($uploadDir)) {
    if (!@mkdir($uploadDir, 0775, true)) {
      jexit(false, 'Não foi possível criar o diretório de upload.');
    }
  }

  // -------- arquivo (opcional no update, obrigatório no insert) --------
  $hasFile = isset($_FILES['arquivo']) && is_uploaded_file($_FILES['arquivo']['tmp_name']);
  $fileErr = $_FILES['arquivo']['error'] ?? UPLOAD_ERR_NO_FILE;

  if ($id <= 0 && !$hasFile) {
    jexit(false, 'Selecione um arquivo.');
  }
  if ($hasFile && $fileErr !== UPLOAD_ERR_OK) {
    // mensagens amigáveis
    $errs = [
      UPLOAD_ERR_INI_SIZE   => 'Arquivo excede o limite do servidor.',
      UPLOAD_ERR_FORM_SIZE  => 'Arquivo excede o limite do formulário.',
      UPLOAD_ERR_PARTIAL    => 'Upload parcial. Tente novamente.',
      UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo enviado.',
      UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausente no servidor.',
      UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar o arquivo.',
      UPLOAD_ERR_EXTENSION  => 'Upload bloqueado por extensão do PHP.'
    ];
    jexit(false, $errs[$fileErr] ?? 'Falha no upload do arquivo.');
  }

  // whitelist de extensões
  $allowed = ['pdf','doc','docx','jpg','jpeg','png','ppt','pptx','xls','xlsx','txt'];
  $newFileName = null; $mime = null; $size = null;
  $newFilePath = null;

  if ($hasFile) {
    $orig = $_FILES['arquivo']['name'];
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
      jexit(false, 'Extensão de arquivo não permitida.');
    }
    $size = (int)$_FILES['arquivo']['size'];            // sem limitar a 2MB (depende do php.ini)
    $mime = $_FILES['arquivo']['type'] ?: 'application/octet-stream';

    $base = sanitizeFileName(pathinfo($orig, PATHINFO_FILENAME));
    $newFileName = sprintf(
      'equipdoc_%d_%s_%s.%s',
      $id_equipamento,
      date('YmdHis'),
      substr(md5($orig . microtime(true)), 0, 6),
      $ext
    );
    $newFilePath = $uploadDir . $newFileName;

    if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $newFilePath)) {
      jexit(false, 'Falha ao mover o arquivo para o destino.');
    }
  }

  // -------- INSERT ou UPDATE --------
  $pdo->beginTransaction();

  if ($id > 0) {
    // --- UPDATE ---
    // pega arquivo antigo para remoção, se for substituir
    $st = $pdo->prepare("SELECT arquivo FROM equipamentos_documentos WHERE id = :id AND id_equipamento = :id_equipamento");
    $st->execute([':id'=>$id, ':id_equipamento'=>$id_equipamento]);
    $found = $st->fetch(PDO::FETCH_ASSOC);
    if (!$found) {
      // se fez upload, limpa o arquivo novo, pois não há registro
      if ($newFilePath && is_file($newFilePath)) @unlink($newFilePath);
      jexit(false, 'Documento não encontrado.');
    }

    if ($hasFile) {
      $sql = "UPDATE equipamentos_documentos
                 SET titulo = :titulo,
                     tipo = :tipo,
                     descricao = :descricao,
                     data_arquivo = :data_arquivo,
                     arquivo = :arquivo,
                     mime = :mime,
                     tamanho_bytes = :tamanho,
                     data_upload = NOW()
               WHERE id = :id AND id_equipamento = :id_equipamento";
      $params = [
        ':titulo'=>$titulo, ':tipo'=>$tipo, ':descricao'=>$descricao,
        ':data_arquivo'=>$data_arquivo,
        ':arquivo'=>$newFileName, ':mime'=>$mime, ':tamanho'=>$size,
        ':id'=>$id, ':id_equipamento'=>$id_equipamento
      ];
    } else {
      $sql = "UPDATE equipamentos_documentos
                 SET titulo = :titulo,
                     tipo = :tipo,
                     descricao = :descricao,
                     data_arquivo = :data_arquivo
               WHERE id = :id AND id_equipamento = :id_equipamento";
      $params = [
        ':titulo'=>$titulo, ':tipo'=>$tipo, ':descricao'=>$descricao,
        ':data_arquivo'=>$data_arquivo,
        ':id'=>$id, ':id_equipamento'=>$id_equipamento
      ];
    }

    $up = $pdo->prepare($sql);
    $up->execute($params);

    $pdo->commit();

    // remove arquivo antigo após commit (se substituiu)
    if ($hasFile && !empty($found['arquivo'])) {
      $oldPath = $uploadDir . $found['arquivo'];
      if (is_file($oldPath)) @unlink($oldPath);
    }

    jexit(true, 'Documento atualizado.', ['id'=>$id]);

  } else {
    // --- INSERT ---
    // arquivo é obrigatório no insert; já garantido acima
    $sql = "INSERT INTO equipamentos_documentos
              (id_equipamento, titulo, tipo, descricao, data_arquivo,
               arquivo, mime, tamanho_bytes, data_upload)
            VALUES
              (:id_equipamento, :titulo, :tipo, :descricao, :data_arquivo,
               :arquivo, :mime, :tamanho, NOW())";
    $ins = $pdo->prepare($sql);
    $ins->execute([
      ':id_equipamento'=>$id_equipamento,
      ':titulo'=>$titulo, ':tipo'=>$tipo, ':descricao'=>$descricao, ':data_arquivo'=>$data_arquivo,
      ':arquivo'=>$newFileName, ':mime'=>$mime, ':tamanho'=>$size
    ]);
    $newId = (int)$pdo->lastInsertId();

    $pdo->commit();

    jexit(true, 'Documento criado.', ['id'=>$newId]);
  }

} catch (Throwable $e) {
  if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  // se movemos um novo arquivo e deu erro, apaga-o para não orphanar
  if (!empty($newFilePath) && is_file($newFilePath)) @unlink($newFilePath);
  jexit(false, 'Erro ao salvar documento.', ['detail'=>$e->getMessage()]);
}
