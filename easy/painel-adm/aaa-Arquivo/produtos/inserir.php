<?php 


require_once("../../conexao.php");

$nome = $_POST['nome'];
$codigo = $_POST['codigo'];
$valor_venda = $_POST['valor_venda'];
$valor_compra = $_POST['valor_compra'];
$valor_venda = str_replace(',', '.', $valor_venda);
$valor_compra = str_replace(',', '.', $valor_compra);
$descricao = $_POST['descricao'];
$categoria = $_POST['categoria'];
$sub_categoria = $_POST['sub_categoria'];
$local = $_POST['local'];
$valor = $valor_venda;    /*salvo o valor para a loja online*/
$fornecedor = $_POST['fornecedor'];
$ativo = $_POST['ativo'];

$estoque_min = $_POST['estoque_min'];
$estoque = $_POST['estoque'];
$id = $_POST['id'];

$antigo = $_POST['antigo'];
$antigo2 = $_POST['antigo2'];


/*
// EVITAR DUPLICIDADE NO NOME
if($antigo != $nome){
	$query_con = $pdo->prepare("SELECT * from produtos WHERE nome = :nome");
	$query_con->bindValue(":nome", $nome);
	$query_con->execute();
	$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res_con) > 0){
		echo 'Produto já Cadastrado!';
		exit();
	}
}
*/

/*
// EVITAR DUPLICIDADE NO CÓDIGO
if($antigo2 != $codigo){
	$query_con = $pdo->prepare("SELECT * from produtos WHERE codigo = :codigo");
	$query_con->bindValue(":codigo", $codigo);
	$query_con->execute();
	$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res_con) > 0){
		echo 'Código do Produto já Cadastrado!';
		exit();
	}
}
*/


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/produtos/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO produtos SET ativo = :ativo, fornecedor = :fornecedor, codigo = :codigo, nome = :nome, descricao = :descricao, valor_venda = :valor_venda, valor_compra = :valor_compra, categoria = :categoria, sub_categoria = :sub_categoria, local = :local, estoque = :estoque, foto = :foto, imagem = :foto, estoque_min = '$estoque_min', valor = $valor ");

	$res->bindValue(":ativo", $ativo);
	$res->bindValue(":fornecedor", $fornecedor);
	$res->bindValue(":codigo", $codigo);
	$res->bindValue(":nome", $nome);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":valor_venda", $valor_venda);
	$res->bindValue(":valor_compra", $valor_compra);
	$res->bindValue(":categoria", $categoria);
	$res->bindValue(":sub_categoria", $sub_categoria);
	
	$res->bindValue(":local", $local);
	$res->bindValue(":estoque", $estoque);
	$res->bindValue(":foto", $imagem);
	$res->bindValue(":imagem", $imagem);
	$res->execute();
}else{

	if($imagem != 'sem-foto.jpg'){
			$res = $pdo->prepare("UPDATE produtos SET ativo = :ativo, fornecedor = :fornecedor, codigo = :codigo, nome = :nome, descricao = :descricao, valor_venda = :valor_venda, valor_compra = :valor_compra, categoria = :categoria, sub_categoria = :sub_categoria,  local = :local, estoque = :estoque, foto = :foto, imagem = :foto, estoque_min = '$estoque_min', valor = $valor WHERE id = :id");
			$res->bindValue(":foto", $imagem);
			$res->bindValue(":imagem", $imagem);
			
		}else{
			$res = $pdo->prepare("UPDATE produtos SET ativo = :ativo, fornecedor = :fornecedor, codigo = :codigo, nome = :nome, descricao = :descricao, valor_venda = :valor_venda, valor_compra = :valor_compra, categoria = :categoria, sub_categoria = :sub_categoria, local = :local, estoque = :estoque, estoque_min = '$estoque_min', valor = $valor WHERE id = :id");
		}

	
	$res->bindValue(":ativo", $ativo);
	$res->bindValue(":fornecedor", $fornecedor);
	$res->bindValue(":codigo", $codigo);
	$res->bindValue(":nome", $nome);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":valor_venda", $valor_venda);
	$res->bindValue(":valor_compra", $valor_compra);
	$res->bindValue(":categoria", $categoria);
	$res->bindValue(":sub_categoria", $sub_categoria);

	$res->bindValue(":local", $local);
	$res->bindValue(":estoque", $estoque);
	$res->bindValue(":id", $id);
	$res->execute();
}



echo 'Salvo com Sucesso!';
?>