<?php 
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];
$obs = $_POST['obs']; 
$cliente = $_POST['usuario_referente_id']; //ID do cliente
$tecnico = $_POST['tecnico'];
$id_form_pag = $_POST['id_form_pag'];
$item = $_POST['item'];
$qtd = $_POST['qtd'];
$desconto = $_POST['desconto'];
$status = $_POST['status'];
$qtd_usados = $_POST['qtd_usados'];
$situacao = $_POST['situacao'];

$id = $_POST['id'];
$data = date('d-m-Y H:i:s');
$created = date('d-m-Y H:i:s');


// Verifica se a quantidade gasta é compativel com a comprada

$query_contar_compra = $pdo->query("SELECT * from vender_servico where id = '$id' limit 1");
$res_contar_compra = $query_contar_compra->fetchAll(PDO::FETCH_ASSOC);
@$res_contar_compra_tot = $res_contar_compra[0]['qtd'];

if((int)@$res_contar_compra_tot < (int)@$qtd_usados){ 

	echo 'Quatidade Gasta Maior que a Comprada!';
	exit();
}


// busca o nome do clientes referente ID para salvar em vendas
@$query_clientes = $pdo->query("SELECT * from clientes where id = '$cliente'");
@$res_clientes = $query_clientes->fetchAll(PDO::FETCH_ASSOC);
@$cliente_nome = $res_clientes[0]['nome'];


// Calculos do pagamento, descontos e total.

// Busca o preço do item atraves do id. valor unidade
	$id_item_ = $item;
	$query_item_ = $pdo->query("SELECT * from servicos where id = '$id_item_' limit 1");
	$res_item_ = $query_item_->fetchAll(PDO::FETCH_ASSOC);

	@$valor_pecas  = (double)$res_item_[0]['valor_venda']; 


	@$valor_servico_sem_desconto  = (double)@$valor_pecas * (double)@$qtd; //valor sem desconto

  @$valor_total = (double)@$valor_servico_sem_desconto - ((double)$valor_servico_sem_desconto / 100 * (double)$desconto); // valor com desconto

  @$valor_em_desconto = (double)@$valor_servico_sem_desconto - (double)$valor_total; //  quanto foi dado de desconto



//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/vender_servico/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO vender_servico SET  situacao = '$situacao', cliente_nome = '$cliente_nome', valor_em_desconto = '$valor_em_desconto', valor_total = '$valor_total', valor_servico_sem_desconto = '$valor_servico_sem_desconto', valor_pecas = '$valor_pecas', qtd_usados = :qtd_usados, status = :status, desconto = :desconto, qtd = :qtd, item = :item, id_form_pag = :id_form_pag, tecnico = :tecnico, cliente = :cliente, obs = :obs, foto = :foto, created = '$created', data_abertura = '$created', usuario = '$id_usuario' ");

	$res->bindValue(":qtd_usados", $qtd_usados);
	$res->bindValue(":status", $status);
	$res->bindValue(":desconto", $desconto);
	$res->bindValue(":qtd", $qtd);
	$res->bindValue(":item", $item);
	$res->bindValue(":id_form_pag", $id_form_pag);
	$res->bindValue(":tecnico", $tecnico);
	$res->bindValue(":cliente", $cliente);
	$res->bindValue(":obs", $obs);
	$res->bindValue(":foto", $imagem);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE vender_servico SET situacao = '$situacao', cliente_nome = '$cliente_nome', valor_em_desconto = '$valor_em_desconto', valor_total = '$valor_total', valor_servico_sem_desconto = '$valor_servico_sem_desconto', valor_pecas = '$valor_pecas', qtd_usados = :qtd_usados, status = :status, desconto = :desconto, qtd = :qtd, item = :item, id_form_pag = :id_form_pag, tecnico = :tecnico, cliente = :cliente, obs = :obs, foto = :foto, data_abertura = '$created', modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE vender_servico SET  situacao = '$situacao', cliente_nome = '$cliente_nome', valor_em_desconto = '$valor_em_desconto', valor_total = '$valor_total', valor_servico_sem_desconto = '$valor_servico_sem_desconto', valor_pecas = '$valor_pecas', qtd_usados = :qtd_usados, status = :status, desconto = :desconto, qtd = :qtd, item = :item, id_form_pag = :id_form_pag, tecnico = :tecnico, cliente = :cliente, obs = :obs, data_abertura = '$created', modificado = '$created' WHERE id = :id");
	}

	$res->bindValue(":qtd_usados", $qtd_usados);
	$res->bindValue(":status", $status);
	$res->bindValue(":desconto", $desconto);
	$res->bindValue(":qtd", $qtd);
	$res->bindValue(":item", $item);
	$res->bindValue(":id_form_pag", $id_form_pag);
	$res->bindValue(":tecnico", $tecnico);
	$res->bindValue(":cliente", $cliente);
	$res->bindValue(":obs", $obs);
	$res->bindValue(":id", $id);
	$res->execute();
}



echo 'Salvo com Sucesso!';
?>