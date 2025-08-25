<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once('../verificar-permissao.php');

require_once('../../conexao.php');



try {
    // 1) ID da foto a deletar
    if (empty($_POST['id'])) {
        throw new Exception('Foto não informada');
    }
    $idFoto = (int) $_POST['id'];

    // 2) Busca caminhos existentes
    $stmt = $pdo->prepare("SELECT arquivo FROM clientes_arquivos WHERE id = :id");
    $stmt->execute([':id' => $idFoto]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        throw new Exception('Registro não encontrado');
    }

    // caminhos físicos
    $baseDir  = __DIR__ . '/../../documentos/clientes/';
    $origPath = $baseDir . $row['arquivo'];
    
    // 3) Remove o arquivo original
    if (file_exists($origPath)) {
        @unlink($origPath);
    }

    // 4) Re-compacta a miniatura para auditoria (qualidade 50)
    

    // 5) Auditoria: quem e quando
    $deletorId   =isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
    $deletorName =  isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
    $deletedAt   = date('Y-m-d H:i:s');

    // 6) Soft-delete com auditoria
    $sql = "
      UPDATE clientes_arquivos
         SET deletado_data_hora = :deleted_at,
             deletado_id_user   = :id_user,
             deletado_user      = :user
       WHERE id = :id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':deleted_at', $deletedAt);
    $stmt->bindValue(':id_user',     $deletorId,   PDO::PARAM_INT);
    $stmt->bindValue(':user',        $deletorName);
    $stmt->bindValue(':id',          $idFoto,       PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'msg'     => $e->getMessage()
    ]);
    exit;
}
