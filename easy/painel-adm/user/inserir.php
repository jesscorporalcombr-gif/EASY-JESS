<?php 
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




$created = date('Y-m-d H:i:s');
$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['nome_usuario'];;
$pasta = $_SESSIO['x_url'];

$nickname = $_POST['nickname'];
$senhaAntiga = $_POST['senha-perfil-atual'];
$novaSenha = $_POST['senha-perfil-nova'];

if (empty($novaSenha)){
    $novaSenha=$senhaAntiga;
}


//SCRIPT PARA SUBIR FOTO NO BANCO
// Definindo o nome da imagem com base no ID e nome, sanitizando o nome para evitar caracteres especiais no nome do arquivo
$tempoMarca = date('ymdHis');
$nomeImgBase = preg_replace('/[^a-zA-Z0-9_-]/', '-', "cadastro" . $id_usuario . "-" . $nickname) . $tempoMarca;
$nomeImgBase = preg_replace('/[ :]+/', '-', $nomeImgBase); // Substitui espaços e dois-pontos por hífens

// Definindo o caminho onde a imagem será salva
$caminho = '../../'.$pasta.'/img/users/' . $nomeImgBase;

// Verifica se um arquivo foi enviado
if (isset($_FILES['input-foto_sistema']['name']) && $_FILES['input-foto_sistema']['name'] != "") {
    $extensao = strtolower(pathinfo($_FILES['input-foto_sistema']['name'], PATHINFO_EXTENSION));
    $nomeArquivoCompleto = $nomeImgBase . '.' . $extensao;
    $caminhoCompleto = $caminho . '.' . $extensao;
    
    // Lista de extensões permitidas
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Verifica se a extensão está na lista de permitidas
    if (in_array($extensao, $extensoesPermitidas)) {
        // Tenta mover o arquivo para o diretório de destino
        if (move_uploaded_file($_FILES['input-foto_sistema']['tmp_name'], $caminhoCompleto)) {
            //echo "Arquivo enviado com sucesso.";
            $imagem_sistema = $nomeArquivoCompleto;
        } else {
            echo "Erro ao enviar o arquivo.";
            exit;
        }
    } else {
        echo "Extensão de Imagem não permitida!";
        exit;
    }
} else {
	if($id_usuario != ""){
        try {
            $stmt = $pdo->prepare("SELECT foto_sistema FROM colaboradores_cadastros WHERE id = ?");
            $stmt->execute([$id_usuario]);
            $foto_cadastro = $stmt->fetchColumn();
            $imagem_sistema=$foto_cadastro;
        } catch (PDOException $e) {
            die("Erro ao obter a foto: " . $e->getMessage());
        }
    }else{
        $imagem_cadastro = "sem-foto.jpg";
    }
}



$sql = "SELECT * FROM colaboradores_cadastros WHERE id = :id_usuario AND senha_sistema = :senhaAntiga";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->bindParam(':senhaAntiga', $senhaAntiga);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // A senha atual está correta, prosseguir com a atualização
    $sqlUpdate = "UPDATE colaboradores_cadastros SET senha_sistema = :nova_senha, foto_sistema = :imagem, nickname = :nickname WHERE id = :id_usuario";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':nova_senha', $novaSenha);
    $stmtUpdate->bindParam(':imagem', $imagem_sistema);
    $stmtUpdate->bindParam(':nickname', $nickname);
    $stmtUpdate->bindParam(':id_usuario', $id_usuario);
    $stmtUpdate->execute();

    echo "Salvo com Sucesso!";
} else {
    echo "Senha atual incorreta!";
}





?>