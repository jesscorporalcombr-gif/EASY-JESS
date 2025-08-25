<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

function jexit($ok, $msg = '', $data = null) {
  echo json_encode(['success'=>$ok, 'msg'=>$msg, 'data'=>$data], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

try {
  // -------- usuário da sessão (VARCHAR 20) --------
  $usuarioSess = $_SESSION['usuario']
              ?? $_SESSION['user']
              ?? $_SESSION['nome_usuario']
              ?? $_SESSION['login']
              ?? $_SESSION['nome']
              ?? 'sistema';
  $usuarioSess = mb_substr((string)$usuarioSess, 0, 20);

  // -------- inputs do form --------
  $id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $nome = trim($_POST['frm-nome'] ?? '');

  $nivel_paralelo = isset($_POST['frm-paralelo']) ? (int)$_POST['frm-paralelo'] : 0;
  $excluido       = isset($_POST['frm-excluido']) ? (int)$_POST['frm-excluido'] : 0;
  $descricao      = trim((string)($_POST['frm-descricao'] ?? ''));

  if ($nome === '') jexit(false, 'Informe o nome da sala.');

  // -------- upload da foto (opcional) --------
  $pastaSess = $_SESSION['x_url'] ?? '';
  $destDir   = $pastaSess ? "../../{$pastaSess}/img/salas/" : "../../img/salas/";
  if (!is_dir($destDir)) @mkdir($destDir, 0775, true);

  $fotoNovoNome = null;
  if (!empty($_FILES['input-foto_cadSala']['name'])) {
    if (isset($_FILES['input-foto_cadSala']['error']) && $_FILES['input-foto_cadSala']['error'] !== UPLOAD_ERR_OK) {
      jexit(false, 'Erro ao enviar a foto (código '.$_FILES['input-foto_cadSala']['error'].')');
    }
    $fname = $_FILES['input-foto_cadSala']['name'];
    $tmp   = $_FILES['input-foto_cadSala']['tmp_name'];
    $ext   = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
    $permitidas = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $permitidas, true)) jexit(false, 'Extensão de foto não permitida.');
    $fotoNovoNome = uniqid('svc_', true).'.'.$ext;
    if (!@move_uploaded_file($tmp, $destDir.$fotoNovoNome)) jexit(false, 'Falha ao salvar a foto.');
  }

  // Foto antiga (se update)
  $fotoAntiga = null;
  if ($id > 0) {
    $st = $pdo->prepare("SELECT foto FROM salas WHERE id=:id");
    $st->execute([':id'=>$id]);
    if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
      $fotoAntiga = $row['foto'] ?? null;
    }
  }

  // -------- UPDATE ou INSERT --------
  if ($id > 0) {
    // UPDATE
    $sql = "UPDATE salas SET
              nome = :nome,
              ag_paralelo = :nivel_paralelo,
              descricao = :descricao,
              excluido = :excluido,
              modificado = :modificado";
    if ($fotoNovoNome) {
      $sql .= ", foto = :foto";
    }
    $sql .= " WHERE id = :id";

    $up = $pdo->prepare($sql);
    $up->bindValue(':nome',            $nome);
    $up->bindValue(':nivel_paralelo',  $nivel_paralelo, PDO::PARAM_INT);
    $up->bindValue(':descricao',       $descricao);
    $up->bindValue(':excluido',        $excluido, PDO::PARAM_INT);
    $up->bindValue(':modificado',      $usuarioSess);
    if ($fotoNovoNome) $up->bindValue(':foto', $fotoNovoNome);
    $up->bindValue(':id',              $id, PDO::PARAM_INT);
    $up->execute();

    if ($fotoNovoNome && $fotoAntiga && $fotoAntiga !== $fotoNovoNome) {
      @unlink($destDir.$fotoAntiga);
    }

    $idFinal = $id;

  } else {
    // INSERT (adiciona created e modificado)
    $cols = "nome, ag_paralelo, descricao, excluido, created, modificado";
    $vals = ":nome, :nivel_paralelo, :descricao, :excluido, :created, :modificado";
    if ($fotoNovoNome) {
      $cols .= ", foto";
      $vals .= ", :foto";
    }

    $sql = "INSERT INTO salas ($cols) VALUES ($vals)";
    $ins = $pdo->prepare($sql);
    $ins->bindValue(':nome',           $nome);
    $ins->bindValue(':nivel_paralelo', $nivel_paralelo, PDO::PARAM_INT);
    $ins->bindValue(':descricao',      $descricao);
    $ins->bindValue(':excluido',       $excluido, PDO::PARAM_INT);
    $ins->bindValue(':created',        $usuarioSess);
    $ins->bindValue(':modificado',     $usuarioSess);
    if ($fotoNovoNome) $ins->bindValue(':foto', $fotoNovoNome);
    $ins->execute();

    $idFinal = (int)$pdo->lastInsertId();
  }

  // -------- retorno p/ atualizar header do modal --------
  $pasta   = $_SESSION['x_url'] ?? '';
  $baseImg = ($pasta ? "../{$pasta}" : "..") . "/img/salas/";
  $fotoHead = null;
  if ($fotoNovoNome)      $fotoHead = $baseImg.$fotoNovoNome;
  else if ($fotoAntiga)   $fotoHead = $baseImg.$fotoAntiga;

  jexit(true, '', [
    'id'        => $idFinal,
    'foto_head' => $fotoHead,
    'titulo'    => $nome
  ]);

} catch (Throwable $e) {
  jexit(false, 'Erro ao salvar: '.$e->getMessage());
}
