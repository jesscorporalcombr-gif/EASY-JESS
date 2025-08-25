<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg='', $row=null){
  echo json_encode(['success'=>$ok, 'msg'=>$msg, 'row'=>$row], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

function ensure_dir($path){
  if (!is_dir($path)) @mkdir($path, 0775, true);
  if (!is_dir($path)) jexit(false, 'Falha ao criar diretório: '.$path);
}

function image_create_from_ext($file, $ext){
  switch ($ext) {
    case 'jpg':
    case 'jpeg': return imagecreatefromjpeg($file);
    case 'png':  return imagecreatefrompng($file);
    case 'gif':  return imagecreatefromgif($file);
    case 'webp': return function_exists('imagecreatefromwebp') ? imagecreatefromwebp($file) : null;
    default:     return null;
  }
}

function save_image_as_ext($img, $path, $ext){
  switch ($ext) {
    case 'jpg':
    case 'jpeg': return imagejpeg($img, $path, 85);
    case 'png':  return imagepng($img, $path, 6);
    case 'gif':  return imagegif($img, $path);
    case 'webp': return function_exists('imagewebp') ? imagewebp($img, $path, 85) : false;
    default:     return false;
  }
}

/**
 * Cria miniatura 400x400 com crop central (cover)
 */
function make_square_thumb($srcPath, $dstPath, $extOut) {
  $ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));
  $src = image_create_from_ext($srcPath, $ext);
  if (!$src) return false;

  $w = imagesx($src);
  $h = imagesy($src);
  $size = 400;

  // calcula crop central (cover)
  $srcRatio = $w / $h;
  if ($srcRatio >= 1) { // mais largo
    $newH = $h;
    $newW = $h; // pega um quadrado central pela altura
    $sx = (int)(($w - $newW) / 2);
    $sy = 0;
  } else {
    $newW = $w;
    $newH = $w;
    $sx = 0;
    $sy = (int)(($h - $newH) / 2);
  }

  $dst = imagecreatetruecolor($size, $size);
  // fundo transparente p/ png/webp
  if (in_array($extOut, ['png','webp'])) {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);
  }

  imagecopyresampled($dst, $src, 0, 0, $sx, $sy, $size, $size, $newW, $newH);
  $ok = save_image_as_ext($dst, $dstPath, $extOut);

  imagedestroy($src);
  imagedestroy($dst);
  return $ok;
}

try {
  // -------- inputs --------
  $idFoto     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $idSala  = isset($_POST['id_sala']) ? (int)$_POST['id_sala'] : '';
  $titulo     = trim($_POST['titulo'] ?? '');
  $dataFoto   = $_POST['data_foto'] ?? null;  // YYYY-MM-DD
  $tipoFoto   = trim($_POST['tipo_foto'] ?? ''); // "Clínico", "Antes e Depois", etc.
  $descricao  = trim($_POST['descricao'] ?? '');

  if ($idSala <= 0) jexit(false, 'Serviço não informado.');
  if ($titulo === '') jexit(false, 'Informe o título.');

  $pasta = $_SESSION['x_url'] ?? '';
  $baseDir = $pasta ? "../../{$pasta}/img/salas/galeria/" : "../../img/salas/galeria/";
  $miniDir = $baseDir . "mini/";

  ensure_dir($baseDir);
  ensure_dir($miniDir);

  $allowed = ['jpg','jpeg','png','webp','gif'];
  $novoOri = null;
  $novoMin = null;
  $extUp   = null;

  // Em edição, recuperar arquivos antigos (se houver)
  $oldOri = $oldMin = null;
  if ($idFoto > 0) {
    $ck = $pdo->prepare("SELECT arquivo_ori, arquivo_mini FROM salas_fotos WHERE id=:id AND id_sala=:s");
    $ck->execute([':id'=>$idFoto, ':s'=>$idSala]);
    if ($r = $ck->fetch(PDO::FETCH_ASSOC)) {
      $oldOri = $r['arquivo_ori'] ?? null;
      $oldMin = $r['arquivo_mini'] ?? null;
    } else {
      jexit(false, 'Foto não encontrada para editar.');
    }
  }

  // Upload (obrigatório no insert; opcional no update)
  if (!empty($_FILES['foto']['name'])) {
    if (!empty($_FILES['foto']['error']) && $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
      jexit(false, 'Erro no upload (código '.$_FILES['foto']['error'].')');
    }
    $fname = $_FILES['foto']['name'];
    $tmp   = $_FILES['foto']['tmp_name'];
    $extUp = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
    if (!in_array($extUp, $allowed, true)) jexit(false, 'Extensão não permitida: '.$extUp);

    $novoOri = uniqid('sf_', true) . '.' . $extUp;
    $novoMin = uniqid('sfm_', true) . '.' . $extUp; // mini com mesma extensão

    if (!move_uploaded_file($tmp, $baseDir.$novoOri)) {
      jexit(false, 'Falha ao salvar arquivo original.');
    }
    // cria mini
    if (!make_square_thumb($baseDir.$novoOri, $miniDir.$novoMin, $extUp)) {
      // se falhar mini, apaga original salvo
      @unlink($baseDir.$novoOri);
      jexit(false, 'Falha ao gerar miniatura.');
    }
  } else {
    if ($idFoto === 0) {
      jexit(false, 'Selecione uma foto.');
    }
  }

  if ($idFoto > 0) {
    // UPDATE
    $sql = "UPDATE salas_fotos SET
              titulo=:titulo,
              data_foto=:data_foto,
              tipo_foto=:tipo_foto,
              descricao=:descricao";

    $params = [
      ':titulo'    => $titulo,
      ':data_foto' => $dataFoto,
      ':tipo_foto' => $tipoFoto,
      ':descricao' => $descricao,
      ':id'        => $idFoto,
      ':s'         => $idSala
    ];

    if ($novoOri && $novoMin) {
      $sql .= ", arquivo_ori=:ori, arquivo_mini=:mini";
      $params[':ori']  = $novoOri;
      $params[':mini'] = $novoMin;
    }

    $sql .= " WHERE id=:id AND id_sala=:s";
    $up = $pdo->prepare($sql);
    $up->execute($params);

    // Se substituiu arquivo, remove antigos
    if ($novoOri && $oldOri && $oldOri !== $novoOri) @unlink($baseDir.$oldOri);
    if ($novoMin && $oldMin && $oldMin !== $novoMin) @unlink($miniDir.$oldMin);

    $idFinal = $idFoto;

  } else {
    // INSERT
    $ins = $pdo->prepare(
      "INSERT INTO salas_fotos
        (id_sala, titulo, data_foto, tipo_foto, descricao, arquivo_ori, arquivo_mini, cadastrado)
       VALUES
        (:s, :titulo, :data_foto, :tipo_foto, :descricao, :ori, :mini, NOW())"
    );
    $ins->execute([
      ':s'         => $idSala,
      ':titulo'    => $titulo,
      ':data_foto' => $dataFoto,
      ':tipo_foto' => $tipoFoto,
      ':descricao' => $descricao,
      ':ori'       => $novoOri,
      ':mini'      => $novoMin
    ]);
    $idFinal = (int)$pdo->lastInsertId();
  }

  // retorna linha atualizada (útil se quiser reaproveitar no front)
  $st = $pdo->prepare("SELECT * FROM salas_fotos WHERE id=:id");
  $st->execute([':id'=>$idFinal]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  jexit(true, '', $row);

} catch (Throwable $e) {
  jexit(false, 'Erro no servidor: '.$e->getMessage());
}
