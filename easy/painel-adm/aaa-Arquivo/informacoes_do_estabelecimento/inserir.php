<?php 
require_once("../../conexao.php");
@session_start();

@$id_usuario = $_SESSION['id_usuario'];
@$categoria = $_POST['categoria']; 
@$id = $_POST['id'];
@$created = date('d-m-Y H:i:s');

@$nome = $_POST['nome']; 
@$email = $_POST['email']; 

@$inscricao_estadual = $_POST['inscricao_estadual']; 
@$inscricao_municipal = $_POST['inscricao_municipal']; 
@$razao_social = $_POST['razao_social']; 
@$foto = $_POST['foto']; 
@$cpf_cnpj = $_POST['cpf_cnpj']; 

@$categoria_por_genero = $_POST['categoria_por_genero']; 
@$categoria_por_servico = $_POST['categoria_por_servico']; 

@$estado = $_POST['estado']; 
@$cidade = $_POST['cidade']; 
@$numero = $_POST['numero'];
@$cep = $_POST['cep']; 
@$complemento = $_POST['complemento']; 
@$bairro = $_POST['bairro']; 

@$nome_responsavel = $_POST['nome_responsavel']; 
@$celular_responsavel = $_POST['celular_responsavel']; 
@$telefone = $_POST['telefone']; 
@$endereco = $_POST['endereco']; 
@$facebook = $_POST['facebook']; 

@$complemento = $_POST['complemento']; 
@$celular_agendamento = $_POST['celular_agendamento']; 
@$site = $_POST['site']; 
@$instagram = $_POST['instagram']; 


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/informacoes_do_estabelecimento/' .$nome_img;
if (@$_FILES['imagem']['name'] == ""){
  $imagem = "sem-foto.jpg";
}else{
    $imagem = $nome_img;
}

$imagem_temp = @$_FILES['imagem']['tmp_name']; 
$ext = pathinfo($imagem, PATHINFO_EXTENSION);   
if($ext == 'JPG' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'JPEG' or $ext == 'png' or $ext == 'PNG' or $ext == 'gif' or $ext == 'GIF' or $ext == 'pdf' or $ext == 'PDF' or $ext == 'mp4' or $ext == 'MP4' or $ext == 'mp3' or $ext == 'MP3' or $ext == 'txt' or $ext == 'TXT' or $ext == 'docx' or $ext == 'DOCX' or $ext == 'doc' or $ext == 'DOC' or $ext == 'xlsx' or $ext == 'XLSX' or $ext == 'pptx' or $ext == 'PPTX'){ 
move_uploaded_file($imagem_temp, $caminho);
}else{
	echo 'Extensão de Imagem não permitida!';
	exit();
}


if($id == ""){
	$res = $pdo->prepare("INSERT INTO informacoes_do_estabelecimento SET  email = '$email', instagram = '$instagram', site = '$site', celular_agendamento = '$celular_agendamento', nome_responsavel = '$nome_responsavel', bairro = '$bairro', complemento = '$complemento', facebook = '$facebook', endereco = '$endereco', telefone = '$telefone', celular_responsavel = '$celular_responsavel',  cep = '$cep', numero = '$numero', cidade = '$cidade', estado = '$estado', categoria_por_servico = '$categoria_por_servico', categoria_por_genero = '$categoria_por_genero', cpf_cnpj = '$cpf_cnpj', razao_social = '$razao_social', inscricao_municipal = '$inscricao_municipal', inscricao_estadual = '$inscricao_estadual', nome = '$nome', usuario = '$id_usuario', categoria = '$categoria', foto = :foto, created = '$created'");

	$res->bindValue(":foto", $imagem);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		
		$res = $pdo->prepare("UPDATE informacoes_do_estabelecimento SET   email = '$email', instagram = '$instagram', site = '$site', celular_agendamento = '$celular_agendamento', nome_responsavel = '$nome_responsavel', bairro = '$bairro', complemento = '$complemento', facebook = '$facebook', endereco = '$endereco', telefone = '$telefone', celular_responsavel = '$celular_responsavel',  cep = '$cep', numero = '$numero', cidade = '$cidade', estado = '$estado', categoria_por_servico = '$categoria_por_servico', categoria_por_genero = '$categoria_por_genero', cpf_cnpj = '$cpf_cnpj', razao_social = '$razao_social', inscricao_municipal = '$inscricao_municipal', inscricao_estadual = '$inscricao_estadual', nome = '$nome', usuario = '$id_usuario', categoria = '$categoria', foto = :foto, modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE informacoes_do_estabelecimento SET  email = '$email', instagram = '$instagram', site = '$site', celular_agendamento = '$celular_agendamento', nome_responsavel = '$nome_responsavel', bairro = '$bairro', complemento = '$complemento', facebook = '$facebook', endereco = '$endereco', telefone = '$telefone', celular_responsavel = '$celular_responsavel',  cep = '$cep', numero = '$numero', cidade = '$cidade', estado = '$estado', categoria_por_servico = '$categoria_por_servico', categoria_por_genero = '$categoria_por_genero', cpf_cnpj = '$cpf_cnpj', razao_social = '$razao_social', inscricao_municipal = '$inscricao_municipal', inscricao_estadual = '$inscricao_estadual', nome = '$nome', usuario = '$id_usuario', categoria = '$categoria', modificado = '$created' WHERE id = :id");
	}
	$res->bindValue(":id", $id);
	$res->execute();
}



echo 'Salvo com Sucesso!';
?>