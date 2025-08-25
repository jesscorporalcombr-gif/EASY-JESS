<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');
header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg='', $data=[]){
  echo json_encode(array_merge(['ok'=>$ok,'msg'=>$msg], $data), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function sanitizeFileName($name){
  $name = preg_replace('/[^\w\s\.-]/u','_', $name);
  $name = preg_replace('/\s+/','_', $name);
  return substr($name, 0, 180);
}

try {
  $id        = (int)($_POST['id'] ?? 0);
  $id_sala   = (int)($_POST['id_sala'] ?? 0);
  $titulo    = trim($_POST['titulo'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');

  if ($id_sala <= 0 || $titulo === '') jexit(false, 'Parâmetros inválidos.');

  $pasta = $_SESSION['x_url'] ?? '';
  $dir = realpath(__DIR__ . '/..'); // .../painel/salas
  $uploadDir = $dir . '/../' . ($pasta ? "$pasta/" : '') . 'docs/salas/';

  if (!is_dir($uploadDir)){
    if (!@mkdir($uploadDir, 0775, true)) jexit(false, 'Não foi possível criar o diretório de upload.');
  }

  $hasFile = isset($_FILES['arquivo']) && is_uploaded_file($_FILES['arquivo']['tmp_name']);

  // validação de arquivo (se existir)
  $allowed = ['pdf','doc','docx','jpg','jpeg','png','ppt','pptx','xls','xlsx','txt'];
  $newFileName = null; $mime = null; $size = null;

  if ($hasFile){
    $size = (int)$_FILES['arquivo']['size'];
    if ($size > 2*1024*1024) jexit(false,'Arquivo excede 2 MB.');

    $orig = $_FILES['arquivo']['name'];
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) jexit(false, 'Extensão de arquivo não permitida.');

    $mime = $_FILES['arquivo']['type'] ?: 'application/octet-stream';

    // nome seguro/único
    $base = sanitizeFileName(pathinfo($orig, PATHINFO_FILENAME));
    $newFileName = sprintf('sala_%d_%s_%s.%s', $id_sala, date('YmdHis'), substr(md5($orig.microtime(true)),0,6), $ext);
    $dest = $uploadDir . $newFileName;

    if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $dest)) {
      jexit(false, 'Falha ao mover o arquivo.');
    }
  }

  if ($id > 0){
    // edição
    // busca antigo para remover arquivo se substituído
    $st = $pdo->prepare("SELECT arquivo FROM salas_documentos WHERE id = :id AND id_sala = :id_sala");
    $st->execute([':id'=>$id, ':id_sala'=>$id_sala]);
    $old = $st->fetch(PDO::FETCH_ASSOC);
    if (!$old) jexit(false, 'Documento não encontrado.');

    if ($hasFile){
      // apaga antigo se existir
      if (!empty($old['arquivo'])){
        $oldPath = $uploadDir . $old['arquivo'];
        if (is_file($oldPath)) @unlink($oldPath);
      }
      $sql = "UPDATE salas_documentos
                 SET titulo=:titulo, descricao=:descricao, arquivo=:arquivo, mime=:mime, tamanho_bytes=:tamanho, data_upload=NOW()
               WHERE id=:id AND id_sala=:id_sala";
      $params = [
        ':titulo'=>$titulo, ':descricao'=>$descricao, ':arquivo'=>$newFileName,
        ':mime'=>$mime, ':tamanho'=>$size, ':id'=>$id, ':id_sala'=>$id_sala
      ];
    } else {
      $sql = "UPDATE salas_documentos
                 SET titulo=:titulo, descricao=:descricao
               WHERE id=:id AND id_sala=:id_sala";
      $params = [
        ':titulo'=>$titulo, ':descricao'=>$descricao, ':id'=>$id, ':id_sala'=>$id_sala
      ];
    }
    $up = $pdo->prepare($sql);
    $up->execute($params);
    jexit(true, 'Documento atualizado.', ['id'=>$id]);
  } else {
    // novo (arquivo obrigatório)
    if (!$hasFile) jexit(false, 'Selecione um arquivo.');

    $sql = "INSERT INTO salas_documentos
              (id_sala, titulo, descricao, arquivo, mime, tamanho_bytes, data_upload)
            VALUES
              (:id_sala, :titulo, :descricao, :arquivo, :mime, :tamanho, NOW())";
    $ins = $pdo->prepare($sql);
    $ins->execute([
      ':id_sala'=>$id_sala, ':titulo'=>$titulo, ':descricao'=>$descricao,
      ':arquivo'=>$newFileName, ':mime'=>$mime, ':tamanho'=>$size
    ]);
    $newId = (int)$pdo->lastInsertId();
    jexit(true, 'Documento criado.', ['id'=>$newId]);
  }

} catch(Throwable $e){
  jexit(false, 'Erro ao salvar documento.', ['detail'=>$e->getMessage()]);
}
