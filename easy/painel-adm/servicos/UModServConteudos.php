<?php
@session_start();
require_once('../../conexao.php');
require_once('../verificar-permissao.php');

header('Content-Type: application/json; charset=utf-8');

// -------- helpers --------
function jexit($http, $arr) {
  http_response_code($http);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function is_multipart() {
  $ct = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
  return stripos($ct, 'multipart/form-data') !== false;
}

try {
  $isMultipart = is_multipart();
  $data = $isMultipart ? $_POST : (json_decode(file_get_contents('php://input'), true) ?: []);

  // -------- inputs --------
  $id          = !empty($data['id']) ? (int)$data['id'] : null;
  $id_servico  = !empty($data['id_servico']) ? (int)$data['id_servico'] : 0;
  $tipo        = isset($data['tipo']) ? strtoupper(trim($data['tipo'])) : '';
  $titulo      = isset($data['titulo']) ? trim($data['titulo']) : '';
  $descricao   = isset($data['descricao']) ? trim($data['descricao']) : '';
  $data_ref    = isset($data['data_referencia']) && $data['data_referencia'] !== '' ? $data['data_referencia'] : null;
  $url         = isset($data['url']) ? trim($data['url']) : '';
  $obrigatorio = isset($data['obrigatorio']) ? (int)$data['obrigatorio'] : 0;
  $carga       = isset($data['carga_horaria']) && $data['carga_horaria'] !== '' ? (float)$data['carga_horaria'] : null;
  $validade    = isset($data['validade_dias']) && $data['validade_dias'] !== '' ? (int)$data['validade_dias'] : null;
  $tags        = isset($data['tags']) ? trim($data['tags']) : '';

  $tiposValidos = ['TERMO','POP','TREINAMENTO','LINK','ARQUIVO'];
  if (!$id_servico || !$tipo || !in_array($tipo, $tiposValidos, true) || $titulo === '') {
    jexit(400, ['ok'=>false, 'error'=>'Parâmetros inválidos']);
  }
  if ($tipo === 'LINK' && $url === '') {
      jexit(400, ['ok'=>false, 'error'=>'URL é obrigatória para tipo LINK']);
  }



  $precisaArquivo = in_array($tipo, ['TERMO','POP','ARQUIVO'], true);

  // Carrega registro atual (se edição) para preservar arquivo quando não vier novo
  $arquivoAtual = null; $extAtual = null;
  if ($id) {
    $chk = $pdo->prepare("SELECT arquivo, extensao FROM servicos_conteudos WHERE id=:id AND id_servico=:s");
    $chk->execute([':id'=>$id, ':s'=>$id_servico]);
    if ($row = $chk->fetch(PDO::FETCH_ASSOC)) {
      $arquivoAtual = $row['arquivo'];
      $extAtual     = $row['extensao'];
    } else {
      jexit(404, ['ok'=>false, 'error'=>'Conteúdo não encontrado para editar']);
    }
  }

  // -------- upload (se aplicável) --------
  $arquivo = null; $ext = null;

  // Pastas
  $pastaSess = $_SESSION['x_url'] ?? '';
  $destDir = $pastaSess ? "../../{$pastaSess}/documentos/servicos/" : "../../documentos/servicos/";
  if (!is_dir($destDir)) @mkdir($destDir, 0775, true);

  // Extensões permitidas
  $allowed = [
    'pdf','doc','docx','ppt','pptx','xls','xlsx',
    'jpg','jpeg','png','gif','txt','csv','mp4','mov','avi'
  ];

  if ($isMultipart && $precisaArquivo) {
    // Pode vir arquivo novo tanto no insert quanto no update
    if (!empty($_FILES['file']['name'])) {
      if (!empty($_FILES['file']['error']) && $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        jexit(400, ['ok'=>false, 'error'=>'Erro no upload (código '.$_FILES['file']['error'].')']);
      }
      $fname = $_FILES['file']['name'];
      $tmp   = $_FILES['file']['tmp_name'];
      $ext   = strtolower(pathinfo($fname, PATHINFO_EXTENSION));

      if (!in_array($ext, $allowed, true)) {
        jexit(400, ['ok'=>false, 'error'=>'Extensão não permitida: '.$ext]);
      }

      $novo  = uniqid('svc_', true).'.'.$ext;
      if (!move_uploaded_file($tmp, $destDir.$novo)) {
        jexit(500, ['ok'=>false, 'error'=>'Falha ao salvar o arquivo']);
      }
      $arquivo = $novo;
    }
  }

  // Obrigatoriedade de arquivo na criação para tipos ≠ LINK
  if ($precisaArquivo && !$id && !$arquivo) {
    jexit(400, ['ok'=>false, 'error'=>'Arquivo é obrigatório para este tipo de conteúdo']);
  }

  // Em edição, se não veio novo arquivo e o tipo exige, preserva o atual
  if ($id && $precisaArquivo && !$arquivo) {
    $arquivo = $arquivoAtual;
    $ext     = $extAtual;
  }

  // -------- INSERT / UPDATE --------
  if ($id) {
    // UPDATE
    $sql = "UPDATE servicos_conteudos SET
              tipo=:tipo, titulo=:titulo, descricao=:descricao, data_referencia=:data_ref,
              url=:url, obrigatorio=:obrig, carga_horaria=:carga, validade_dias=:validade, tags=:tags";

    $params = [
      ':tipo'    => $tipo,
      ':titulo'  => $titulo,
      ':descricao'=> $descricao,
      ':data_ref'=> $data_ref,
      ':url'     => $url,
      ':obrig'   => $obrigatorio,
      ':carga'   => $carga,
      ':validade'=> $validade,
      ':tags'    => $tags,
      ':id'      => $id,
      ':id_serv' => $id_servico
    ];

    // Se veio novo arquivo (ou foi obrigatório na criação), atualiza campos de arquivo
    if ($arquivo !== null) {
      $sql .= ", arquivo=:arquivo, extensao=:extensao";
      $params[':arquivo']  = $arquivo;
      $params[':extensao'] = $ext;
    }

    $sql .= " WHERE id=:id AND id_servico=:id_serv";
    $up = $pdo->prepare($sql);
    $up->execute($params);

    // Se substituiu arquivo, apaga o antigo físico
    if ($arquivo !== null && $arquivoAtual && $arquivoAtual !== $arquivo) {
      @unlink($destDir.$arquivoAtual);
    }

  } else {
    // INSERT
    $ins = $pdo->prepare(
      "INSERT INTO servicos_conteudos
        (id_servico, tipo, titulo, descricao, data_referencia, url, arquivo, extensao, obrigatorio, carga_horaria, validade_dias, tags)
       VALUES
        (:id_serv, :tipo, :titulo, :descricao, :data_ref, :url, :arquivo, :extensao, :obrig, :carga, :validade, :tags)"
    );
    $ins->execute([
      ':id_serv'  => $id_servico,
      ':tipo'     => $tipo,
      ':titulo'   => $titulo,
      ':descricao'=> $descricao,
      ':data_ref' => $data_ref,
      ':url'      => $url,
      ':arquivo'  => $arquivo,   // pode ser null se tipo=LINK
      ':extensao' => $ext,
      ':obrig'    => $obrigatorio,
      ':carga'    => $carga,
      ':validade' => $validade,
      ':tags'     => $tags
    ]);
    $id = (int)$pdo->lastInsertId();
  }

  // -------- retorna o registro atualizado --------
  $st = $pdo->prepare("SELECT * FROM servicos_conteudos WHERE id=:id");
  $st->execute([':id'=>$id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    jexit(500, ['ok'=>false, 'error'=>'Falha ao recuperar registro salvo']);
  }

  // Monta href_arquivo para facilitar o front
  $pasta = $_SESSION['x_url'] ?? '';
  $baseHref = ($pasta ? "../{$pasta}" : "..") . "/documentos/servicos/";
  $row['href_arquivo'] = !empty($row['arquivo']) ? $baseHref.$row['arquivo'] : null;

  jexit(200, ['ok'=>true, 'row'=>$row]);

} catch (Throwable $e) {
  jexit(500, ['ok'=>false, 'error'=>'Erro no servidor', 'detail'=>$e->getMessage()]);
}
