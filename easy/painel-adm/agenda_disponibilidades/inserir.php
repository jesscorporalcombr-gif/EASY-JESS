<?php 
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




$created = date('Y-m-d H:i:s');
$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['nome_usuario'];
//$pasta = $_SESSION['x_url'];
$pasta = $_SESSION['x_url'] ?? '';

$id = $_POST['frm-id'];

$nome_agenda = $_POST['frm-nome_agenda'];
$cor_fundo_agenda = $_POST['cor_fundo_agenda'];
$cor_fonte_agenda = $_POST['cor_fonte_agenda'];

$foto_agenda = $_POST['foto-agenda'];
$especialidade_agenda = $_POST['frm-especialidade_agenda'];
$descricao_agenda = $_POST['frm-descricao_agenda'];
$ordem_agenda = $_POST['frm-ordem_agenda'];
$nova_foto_agenda = $_POST['nova-foto-agenda'];


$ativo_agenda = (isset($_POST['frm-ativo_agenda']) && $_POST['frm-ativo_agenda'] == "Ativo") ? true : false;





//SCRIPT PARA SUBIR FOTO NO BANCO
// Definindo o nome da imagem com base no ID e nome, sanitizando o nome para evitar caracteres especiais no nome do arquivo
$tempoMarca = date('ymdHis');
$nomeImgBase = preg_replace('/[^a-zA-Z0-9_-]/', '-', "agenda" . $id . "-" . $nome_agenda) . $tempoMarca;
$nomeImgBase = preg_replace('/[ :]+/', '-', $nomeImgBase); // Substitui espaços e dois-pontos por hífens

// Definindo o caminho onde a imagem será salva
$caminhoPasta = '../../'.$pasta.'/img/cadastro_colaboradores/';
$caminho = $caminhoPasta . $nomeImgBase;
 
// Verifica se um arquivo foi enviado
if (isset($_FILES['input-foto_agenda']['name']) && $_FILES['input-foto_agenda']['name'] != "") {
 $extensao = strtolower(pathinfo($_FILES['input-foto_agenda']['name'], PATHINFO_EXTENSION));
    $nomeArquivoCompleto = $nomeImgBase . '.' . $extensao;
    $caminhoCompleto = $caminho . '.' . $extensao;
    
    // Lista de extensões permitidas
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
      
    // Verifica se a extensão está na lista de permitidas
    if (in_array($extensao, $extensoesPermitidas)) {
        // Tenta mover o arquivo para o diretório de destino
        if (move_uploaded_file($_FILES['input-foto_agenda']['tmp_name'], $caminhoCompleto)) {
            $stmt = $pdo->prepare("SELECT foto_agenda FROM colaboradores_contratos WHERE id = ?");
            $stmt->execute([$id]);
            $nomeImagemAntiga = $stmt->fetchColumn();

            if (!empty($nomeImagemAntiga) && $nomeImagemAntiga !== 'sem-foto.jpg' && file_exists($caminhoPasta . $nomeImagemAntiga)) {
                unlink($caminhoPasta . $nomeImagemAntiga);
            }
                        
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
    $stmt = $pdo->prepare("SELECT id_colaborador, foto_agenda FROM colaboradores_contratos WHERE id = ?");
            $stmt->execute([$id]);
            $dados_cad = $stmt->fetch();
            $foto_agendaCont = $dados_cad['foto_agenda']; //foto da agenda no contrato
            $id_colaborador = $dados_cad['id_colaborador'];
    
    
    
    if($foto_agenda==$foto_agendaCont){
     
        $imagem_agenda = $foto_agendaCont;

    }elseif ($nova_foto_agenda){
     
       $info = pathinfo($foto_agenda);
        $extent = $info['extension'];
       
        if ($nova_foto_agenda=='cadastro_foto_sistema'){
            $caminhoOrigem ='../../'.$pasta.'/img/users/';
        }else{
            $caminhoOrigem='../../'.$pasta.'/img/cadastro_colaboradores/';
        }

       $origem = $caminhoOrigem. $foto_agenda;
        $destino = $caminho . '.' . $extent;
       

        if (!copy($origem, $destino)) {
            echo "Falha ao copiar o arquivo.";
        }

        $imagem_agenda = $nomeImgBase . '.' . $extent;

    }else{

         $imagem_agenda = '';
    }

}







	try {
		
			$sql = "UPDATE colaboradores_contratos SET nome_agenda = :nome_agenda, cor_fundo_agenda = :cor_fundo_agenda, cor_fonte_agenda = :cor_fonte_agenda, ordem_agenda = :ordem_agenda, especialidade_agenda = :especialidade_agenda, foto_agenda = :foto_agenda, descricao_agenda = :descricao_agenda, ativo_agenda = :ativo_agenda WHERE id = :id";
			
		$res = $pdo->prepare($sql);


		$res->bindValue(":id", $id);

		$res->bindValue(":nome_agenda", $nome_agenda);
		$res->bindValue(":foto_agenda", $imagem_agenda);
		$res->bindValue(":cor_fundo_agenda", $cor_fundo_agenda);
        $res->bindValue(":cor_fonte_agenda", $cor_fonte_agenda);
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