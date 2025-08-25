<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once('../verificar-permissao.php');
require_once('../../conexao.php');
$pasta = $_SESSION['x_url'] ?? '';
try {
    if (empty($_POST['id_cliente'])) {
        throw new Exception('Cliente não informado');
    }
    $idCliente = (int) $_POST['id_cliente'];
    $titulo    = trim($_POST['titulo'] ?? '');
    $dataFoto  = $_POST['data_foto'] ?? null;
    $tipoFoto  = trim($_POST['tipo_foto'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    // diretórios físicos
    $origDir = __DIR__ . '/../../'.$pasta.'/img/clientes/galeria/';
    $miniDir = $origDir . 'mini/';
    if (!is_dir($origDir)) mkdir($origDir, 0755, true);
    if (!is_dir($miniDir)) mkdir($miniDir, 0755, true);

    // processa upload
    $arquivoOri  = null;
    $arquivoMini = null;
    if (!empty($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $info = pathinfo($_FILES['foto']['name']);
        $ext  = strtolower($info['extension']);
        if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
            throw new Exception('Tipo de arquivo não permitido');
        }
        // nomes de arquivo
        $baseName  = uniqid("cli{$idCliente}_") . '.' . $ext;
        $miniName  = 'mini_' . $baseName;
        $origPath  = $origDir . $baseName;
        $miniPath  = $miniDir . $miniName;

        // salva original
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $origPath)) {
            throw new Exception('Falha ao gravar imagem original');
        }
        // gera miniatura 250x250
        switch ($ext) {
            case 'jpg': case 'jpeg':
                $img = imagecreatefromjpeg($origPath); break;
            case 'png':
                $img = imagecreatefrompng($origPath);  break;
            case 'gif':
                $img = imagecreatefromgif($origPath);  break;
        }
        if (!$img) throw new Exception('Erro ao processar imagem');
        
        
            $origWidth  = imagesx($img);
            $origHeight = imagesy($img);
            $maxDim     = 250;
            $scale      = min($maxDim / $origWidth, $maxDim / $origHeight);
            $newWidth   = (int)($origWidth * $scale);
            $newHeight  = (int)($origHeight * $scale);

            $thumb = imagescale($img, $newWidth, $newHeight, IMG_BILINEAR_FIXED);
            imagedestroy($img);
            imagejpeg($thumb, $miniPath, 80);
            imagedestroy($thumb);
        // só o nome no banco
        $arquivoOri  = $baseName;
        $arquivoMini = $miniName;
    }

    // INSERT ou UPDATE
    if (!empty($_POST['id'])) {
        $idFoto = (int) $_POST['id'];
        $sql = "UPDATE clientes_fotos
                   SET titulo      = :titulo,
                       data_foto   = :data_foto,
                       tipo_foto   = :tipo_foto,
                       descricao   = :descricao"
             . ($arquivoOri ? ", arquivo_ori = :orig, arquivo_mini = :mini" : "")
             . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titulo',    $titulo);
        $stmt->bindValue(':data_foto', $dataFoto);
        $stmt->bindValue(':tipo_foto', $tipoFoto);
        $stmt->bindValue(':descricao', $descricao);
        if ($arquivoOri) {
            $stmt->bindValue(':orig', $arquivoOri);
            $stmt->bindValue(':mini', $arquivoMini);
        }
        $stmt->bindValue(':id', $idFoto, PDO::PARAM_INT);
        $stmt->execute();

    } else {
        $sql = "INSERT INTO clientes_fotos
                (id_cliente, arquivo_ori, arquivo_mini, titulo, data_foto, tipo_foto, descricao)
                VALUES
                (:id_cliente, :orig, :mini, :titulo, :data_foto, :tipo_foto, :descricao)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_cliente'=> $idCliente,
            ':orig'      => $arquivoOri,
            ':mini'      => $arquivoMini,
            ':titulo'    => $titulo,
            ':data_foto' => $dataFoto,
            ':tipo_foto' => $tipoFoto,
            ':descricao' => $descricao
        ]);
    }

    echo json_encode(['success' => true]);
    exit;
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
    exit;
}
