<?php 
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




$created = date('Y-m-d H:i:s');
$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['nome_usuario'];;
$pasta = $_SESSION['x_url'];


$id = $_POST['frm-id'];

$nome_agenda = $_POST['frm-nome_agenda'];
$cor_agenda = $_POST['frm-cor_agenda'];
$foto_agenda = $_POST['frm-foto_agenda'];
$especialidade_agenda = $_POST['frm-especialidade_agenda'];
$descricao_agenda = $_POST['frm-descricao_agenda'];
$ordem_agenda = $_POST['frm-ordem_agenda'];

$ativo_agenda = (isset($_POST['frm-ativo_agenda']) && $_POST['frm-ativo_agenda'] == "Ativo") ? true : false;





//SCRIPT PARA SUBIR FOTO NO BANCO
// Definindo o nome da imagem com base no ID e nome, sanitizando o nome para evitar caracteres especiais no nome do arquivo
$tempoMarca = date('ymdHis');
$nomeImgBase = preg_replace('/[^a-zA-Z0-9_-]/', '-', "agenda" . $id . "-" . $nome_agenda) . $tempoMarca;
$nomeImgBase = preg_replace('/[ :]+/', '-', $nomeImgBase); // Substitui espaços e dois-pontos por hífens

// Definindo o caminho onde a imagem será salva
$caminho = '../../'.$pasta.'/img/cadastro_colaboradores/' . $nomeImgBase;

// Verifica se um arquivo foi enviado
if (isset($_FILES['img-foto_agenda']['name']) && $_FILES['img-foto_agenda']['name'] != "") {
    $extensao = strtolower(pathinfo($_FILES['img-foto_agenda']['name'], PATHINFO_EXTENSION));
    $nomeArquivoCompleto = $nomeImgBase . '.' . $extensao;
    $caminhoCompleto = $caminho . '.' . $extensao;
    
    // Lista de extensões permitidas
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Verifica se a extensão está na lista de permitidas
    if (in_array($extensao, $extensoesPermitidas)) {
        // Tenta mover o arquivo para o diretório de destino
        if (move_uploaded_file($_FILES['img-foto_agenda']['tmp_name'], $caminhoCompleto)) {
            //echo "Arquivo enviado com sucesso.";
            $imagem_agenda = $nomeArquivoCompleto;
        } else {
            echo "Erro ao enviar o arquivo.";
            exit;
        }
    } else {
        echo "Extensão de Imagem não permitida!";
        exit;
    }
} else {
    // Define um nome padrão caso nenhum arquivo tenha sido enviado
    if($id != ""){
        try {
            $stmt = $pdo->prepare("SELECT foto_agenda FROM cadastro_colaboradores WHERE id = ?");
            $stmt->execute([$id]);
            $foto_agenda = $stmt->fetchColumn();
            $imagem_agenda=$foto_agenda;
        } catch (PDOException $e) {
            die("Erro ao obter a foto: " . $e->getMessage());
        }
    }else{
        $imagem_agenda = "sem-foto.jpg";
    }

}







	try {
		
			$sql = "UPDATE cadastro_colaboradores SET nome_agenda = :nome_agenda, cor_agenda = :cor_agenda, ordem_agenda = :ordem_agenda, especialidade_agenda = :especialidade_agenda, foto_agenda = :foto_agenda, descricao_agenda = :descricao_agenda, ativo_agenda = :ativo_agenda WHERE id = :id";
			
		$res = $pdo->prepare($sql);


		$res->bindValue(":id", $id);

		$res->bindValue(":nome_agenda", $nome_agenda);
		$res->bindValue(":foto_agenda", $imagem_agenda);
		$res->bindValue(":cor_agenda", $cor_agenda);
		$res->bindValue(":ordem_agenda", $ordem_agenda);
		$res->bindValue(":descricao_agenda", $descricao_agenda);
		$res->bindValue(":especialidade_agenda", $especialidade_agenda);
		$res->bindValue(":ativo_agenda", $ativo_agenda);
		

		//$res->bindValue(":id_user_alteracao", $id_usuario);
		//$res->bindValue(":user_alteracao", $usuario);
		//$res->bindValue(":data_alteracao", $created);


		$res->execute();
	} catch (PDOException $e) {
		die("Erro ao atualizar os dados no banco: " . $e->getMessage());
	}




echo 'Salvo com Sucesso!';
?>