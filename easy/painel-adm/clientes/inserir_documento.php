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
    $dataDocumento  = $_POST['data_documento'] ?? null;
    $tipoDocumento  = trim($_POST['tipo_documento'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    // diretórios físicos
    $origDir = __DIR__ . '/../../'.$pasta.'/documentos/clientes/';
    $miniDir = __DIR__ . '/../../img/extensoes/';
    if (!is_dir($origDir)) mkdir($origDir, 0755, true);
    if (!is_dir($miniDir)) mkdir($miniDir, 0755, true);

    // processa upload
    $arquivoOri  = null;
    $arquivoMini = null;
    if (!empty($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $info = pathinfo($_FILES['documento']['name']);
        $ext  = strtolower($info['extension']);
       
        // nomes de arquivo
        $baseName  = uniqid("cli{$idCliente}_") . '.' . $ext;
       // $miniName  = 'mini_' . $baseName;
        $origPath  = $origDir . $baseName;
        //$miniPath  = $miniDir . $miniName;

        // salva original
        if (!move_uploaded_file($_FILES['documento']['tmp_name'], $origPath)) {
            throw new Exception('Falha ao gravar imagem original');
        }
        // gera miniatura 250x250
        $arquivoOri  = $baseName;
       
    }

    // INSERT ou UPDATE
    if (!empty($_POST['id'])) {
        $idDocumento = (int) $_POST['id'];
        $sql = "UPDATE clientes_arquivos
                   SET titulo      = :titulo,
                       extensao    = :ext,
                       data_arquivo   = :data_documento,
                       tipo_arquivo   = :tipo_documento,
                       descricao   = :descricao,
                       arquivo = :orig
                    WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titulo',    $titulo);
        $stmt->bindValue(':ext',    $ext);
        $stmt->bindValue(':data_documento', $dataDocumento);
        $stmt->bindValue(':tipo_documento', $tipoDocumento);
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':orig', $arquivoOri);
        $stmt->bindValue(':id', $idDocumento, PDO::PARAM_INT);
        $stmt->execute();

    } else {
        $sql = "INSERT INTO clientes_arquivos
                (id_cliente, arquivo, extensao, titulo, data_arquivo, tipo_arquivo, descricao)
                VALUES
                (:id_cliente, :orig, :ext, :titulo, :data_documento, :tipo_documento, :descricao)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_cliente'=> $idCliente,
            ':orig'      => $arquivoOri,
            ':ext'      => $ext,
            ':titulo'    => $titulo,
            ':data_documento' => $dataDocumento,
            ':tipo_documento' => $tipoDocumento,
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
