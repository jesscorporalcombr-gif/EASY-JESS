<?php 
require_once("../../conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$cpf = $_POST['cpf'];
$senha = $_POST['senha'];
$nivel = $_POST['nivel'];
$id = $_POST['id'];

$aniversario = $_POST['aniversario'];
$telefone = $_POST['telefone'];
$celular = $_POST['celular'];
$sexo = $_POST['sexo'];
$como_conheceu = $_POST['como_conheceu'];
$cep = $_POST['cep'];
$endereco = $_POST['endereco'];
$numero = $_POST['numero'];
$estado = $_POST['estado'];
$cidade = $_POST['cidade'];
$bairro = $_POST['bairro'];
$profissao = $_POST['profissao'];
$cadastrado = $_POST['cadastrado'];
$obs = $_POST['obs'];
$rg = $_POST['rg'];
$complemento = $_POST['complemento'];
$created = date('d-m-Y H:i:s');

$antigo = $_POST['antigo'];
$antigo2 = $_POST['antigo2'];

// EVITAR DUPLICIDADE NO EMAIL

if($antigo != $cpf){
// EVITAR DUPLICIDADE NO CPF
	$query_con = $pdo->prepare("SELECT * from aniversario_cliente WHERE cpf = :cpf");
	$query_con->bindValue(":cpf", $cpf);
	$query_con->execute();
	$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res_con) > 0){
		echo 'O CPF já está cadastrado!';
		exit();
	}
}


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/aniversario_cliente/' .$nome_img;
if (@$_FILES['imagem']['name'] == ""){
  $imagem = "sem-foto.jpg";
}else{
    $imagem = $nome_img;
}

$imagem_temp = @$_FILES['imagem']['tmp_name']; 
$ext = pathinfo($imagem, PATHINFO_EXTENSION);   
if($ext == 'JPG' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'JPEG' or $ext == 'png' or $ext == 'PNG' or $ext == 'gif' or $ext == 'GIF'){ 
move_uploaded_file($imagem_temp, $caminho);
}else{
	echo 'Extensão de Imagem não permitida!';
	exit();
}



if($id == ""){
	$res = $pdo->prepare("INSERT INTO aniversario_cliente SET nome = :nome, email = :email, cpf = :cpf, senha = :senha, nivel = :nivel, aniversario = :aniversario, telefone = :telefone, celular = :celular, sexo = :sexo, como_conheceu = :como_conheceu, cep = :cep, endereco = :endereco, numero = :numero, estado = :estado, cidade = :cidade, bairro = :bairro, profissao = :profissao, cadastrado = :cadastrado, obs = :obs, rg = :rg, foto = :foto, complemento = :complemento, created = '$created'");

	$res->bindValue(":nome", $nome);
	$res->bindValue(":email", $email);
	$res->bindValue(":cpf", $cpf);
	$res->bindValue(":senha", $senha);
	$res->bindValue(":nivel", $nivel);

	$res->bindValue(":aniversario", $aniversario);
	$res->bindValue(":telefone", $telefone);
	$res->bindValue(":celular", $celular);
	$res->bindValue(":sexo", $sexo);
	$res->bindValue(":como_conheceu", $como_conheceu);
	$res->bindValue(":cep", $cep);
	$res->bindValue(":endereco", $endereco);
	$res->bindValue(":numero", $numero);
	$res->bindValue(":estado", $estado);
	$res->bindValue(":cidade", $cidade);
	$res->bindValue(":bairro", $bairro);
	$res->bindValue(":profissao", $profissao);
	$res->bindValue(":cadastrado", $cadastrado);
	$res->bindValue(":obs", $obs);
	$res->bindValue(":rg", $rg);
	$res->bindValue(":complemento", $complemento);

	$res->bindValue(":foto", $imagem);
	$res->execute();
}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE aniversario_cliente SET nome = :nome, email = :email, cpf = :cpf, foto = :foto, senha = :senha, nivel = :nivel, aniversario = :aniversario, telefone = :telefone, celular = :celular, sexo = :sexo, como_conheceu = :como_conheceu, cep = :cep, endereco = :endereco, numero = :numero, estado = :estado, cidade = :cidade, bairro = :bairro, profissao = :profissao, cadastrado = :cadastrado, obs = :obs, rg = :rg, complemento = :complemento, modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
		
		}else{
			$res = $pdo->prepare("UPDATE aniversario_cliente SET nome = :nome, email = :email, cpf = :cpf, senha = :senha, nivel = :nivel, aniversario = :aniversario, telefone = :telefone, celular = :celular, sexo = :sexo, como_conheceu = :como_conheceu, cep = :cep, endereco = :endereco, numero = :numero, estado = :estado, cidade = :cidade, bairro = :bairro, profissao = :profissao, cadastrado = :cadastrado, obs = :obs, rg = :rg, complemento = :complemento, modificado = '$created' WHERE id = :id");

		}

	$res->bindValue(":nome", $nome);
	$res->bindValue(":email", $email);
	$res->bindValue(":cpf", $cpf);
	$res->bindValue(":senha", $senha);
	$res->bindValue(":nivel", $nivel);

	$res->bindValue(":aniversario", $aniversario);
	$res->bindValue(":telefone", $telefone);
	$res->bindValue(":celular", $celular);
	$res->bindValue(":sexo", $sexo);
	$res->bindValue(":como_conheceu", $como_conheceu);
	$res->bindValue(":cep", $cep);
	$res->bindValue(":endereco", $endereco);
	$res->bindValue(":numero", $numero);
	$res->bindValue(":estado", $estado);
	$res->bindValue(":cidade", $cidade);
	$res->bindValue(":bairro", $bairro);
	$res->bindValue(":profissao", $profissao);
	$res->bindValue(":cadastrado", $cadastrado);
	$res->bindValue(":obs", $obs);
	$res->bindValue(":rg", $rg);
	$res->bindValue(":complemento", $complemento);

	$res->bindValue(":id", $id);
	$res->execute();
	}



echo 'Salvo com Sucesso!';
?>