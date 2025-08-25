<?php 

require_once("../../conexao.php");
require_once('verificar-permissao.php');


if (session_status() === PHP_SESSION_NONE) { session_start(); }
//@session_start()



$created = date('d-m-Y H:i:s');
$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['usuario'];

$id = $_POST['id'];
$antigo = $_POST['antigo'];







        //$data_competencia = isset($_POST['data_competencia']) ? DateTime::createFromFormat('d/m/Y', $_POST['data_competencia'])->format('Y-m-d') : null;

        $data_competencia = $_POST['data_competencia'];
         $data_vencimento = $_POST['data_vencimento'];
        $data_pagamento = $_POST['data_pagamento'];
       
       // if (isset($_POST['data_competencia'])) {
        //        $data_competencia_array = explode('/', $_POST['data_competencia']);
        //        if (count($data_competencia_array) == 3) {
        //            $data_competencia = $data_competencia_array[2] . '-' . $data_competencia_array[1] . '-' . $data_competencia_array[0];
        //        } else {
        //            // Trate o erro conforme apropriado
        //            $data_competencia = null;
        //        }
        //    } else {
         //       $data_competencia = null;
    //    }
 
 
 
 
       

		$descricao = $_POST['descricao'];
		$categoria = $_POST['categoria'];
	    $id_categoria = $_POST['id_categoria'];
		$conta = $_POST['conta'];
		$id_conta = $_POST['id_conta'];
	

		
        $valor_principal = str_replace(",", ".", str_replace(".", "", $_POST['valor']));
        $multa_juros = str_replace(",", ".", str_replace(".", "", $_POST['multa_juros']));
        $desconto_taxa = str_replace(",", ".", str_replace(".", "", $_POST['desconto_taxa']));
        
        // Converter para float
        $valor_principal = (float)$valor_principal;
        $multa_juros = (float)$multa_juros;
        $desconto_taxa = (float)$desconto_taxa;
        
        // Checar se $recdesp indica uma despesa e ajustar os valores conforme necessário
        if ($_POST['recdesp'] == "Despesa") {
            $valor_principal *= -1;
            $multa_juros *= -1;
         
        } else {
          $desconto_taxa *= -1;
        }
     
        
        
        
        $centro_custo = $_POST['centro_custo'];
		$id_centro_custo =$_POST['id_centro_custo'];
		$forma_pagamento =$_POST['forma_pagamento'];
		$id_forma_pagamento =$_POST['id_forma_pagamento'];
		$bandeira =$_POST['bandeira'];
		$id_bandeira = $_POST['id_bandeira'];
    	$fornecedor= $_POST['fornecedor'];
		$id_fornecedor= $_POST['id_fornecedor'];
		$observacoes = $_POST['observacoes'];
	    $id_venda =$_POST['id_venda'];
	    //$user_criacao = $_POST['user_criacao'];
		//$data_criacao = $_POST['data_criacao'];
		//$user_alteracao = $_POST['user_alteracao'];
		//$data_criacao = $_POST['data_criacao'];
		$nota_fiscal = $_POST['nota_fiscal'];
		$id_comum = $_POST['id_comum'];
		$id_cliente = $_POST['id_cliente'];
		$id_filial = $_POST['id_filial'];
		
		$nome_img = $_POST['imagem'];
		
		$user_criacao = $_POST['user_alteracao'];
		$data_criacao = $_POST['data_alteracao'];
		
		
		
        $excluido = "";	

	


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/entradas-saidas/' .$nome_img;
if (@$_FILES['imagem']['name'] == ""){
  $imagem = "sem-foto.jpg";
}else{
    $imagem = $nome_img;
}

$imagem_temp = @$_FILES['imagem']['tmp_name']; 
$ext = pathinfo($imagem, PATHINFO_EXTENSION);   
if($ext == 'JPG' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'JPEG' or $ext == 'png' or $ext == 'PNG' or $ext == 'gif' or $ext == 'GIF' or $ext == 'pdf' or $ext == 'PDF' or $ext == 'mp4' or $ext == 'MP4' or $ext == 'mp3' or $ext == 'MP3' or $ext == 'txt' or $ext == 'TXT' or $ext == 'docx' or $ext == 'DOCX' or $ext == 'doc' or $ext == 'DOC' or $ext == 'xlsx' or $ext == 'XLSX' or $ext == 'pptx' or $ext == 'PPTX' or $ext == 'HEIC'){ 
move_uploaded_file($imagem_temp, $caminho);
}else{
	echo 'Extensão de Imagem não permitida!';
	exit();
}







if($id == ""){
	$res = $pdo->prepare("INSERT INTO financeiro_extrato SET data_competencia = :data_competencia, data_vencimento = :data_vencimento, data_pagamento = :data_pagamento, descricao = :descricao, categoria = :categoria, id_categoria = :id_categoria, conta = :conta, id_conta = :id_conta, valor_principal = :valor_principal, multa_juros = :multa_juros, desconto_taxa = :desconto_taxa, centro_custo = :centro_custo, id_centro_custo = :id_centro_custo, forma_pagamento = :forma_pagamento, id_forma_pagamento = :id_forma_pagamento, bandeira = :bandeira, id_bandeira = :id_bandeira, fornecedor = :fornecedor, id_fornecedor = :id_fornecedor, observacoes = :observacoes, id_venda = :id_venda, user_criacao = :user_criacao, data_criacao = :data_criacao, user_alteracao = :user_alteracao, data_alteracao = :data_alteracao, nota_fiscal = :nota_fiscal, id_comum = :id_comum, id_cliente = :id_cliente, id_filial = :id_filial, excluido = :excluido, imagem = :imagem ");

$user_alteracao = "";
$data_alteracao = "";
$excluido = "";



	$res->bindValue(":data_competencia", $data_competencia);
	$res->bindValue(":data_vencimento", $data_vencimento);
	$res->bindValue(":data_pagamento", $data_pagamento);
	$res->bindValue(":descricao", $descricao);
    $res->bindValue(":categoria", $categoria);
    $res->bindValue(":id_categoria", $id_categoria);
    $res->bindValue(":conta", $conta);
    $res->bindValue(":id_conta", $id_conta);
    $res->bindValue(":valor_principal", $valor_principal);
    $res->bindValue(":multa_juros", $multa_juros);
    $res->bindValue(":desconto_taxa", $desconto_taxa);
    $res->bindValue(":centro_custo", $centro_custo);
    $res->bindValue(":id_centro_custo", $id_centro_custo);
    $res->bindValue(":forma_pagamento", $forma_pagamento);
    $res->bindValue(":id_forma_pagamento", $id_forma_pagamento);
    $res->bindValue(":bandeira", $bandeira);
    $res->bindValue(":id_bandeira", $id_bandeira);
    $res->bindValue(":fornecedor", $fornecedor);
    $res->bindValue(":id_fornecedor", $id_fornecedor);
    $res->bindValue(":observacoes", $observacoes);
    $res->bindValue(":id_venda", $id_venda);
    $res->bindValue(":nota_fiscal", $nota_fiscal);
    $res->bindValue(":id_comum", $id_comum);
    $res->bindValue(":id_cliente", $id_cliente);
    $res->bindValue(":id_filial", $id_filial);
   
   
    $res->bindValue(":user_criacao", $id_usuario);
    $res->bindValue(":data_criacao", $created);
    
    $res->bindValue(":user_alteracao", $user_alteracao);
    $res->bindValue(":data_alteracao", $data_alteracao);
    
    $res->bindValue(":excluido", $excluido);
    
	$res->bindValue(":imagem", $imagem);
	$res->execute();
	

}else{


			$res = $pdo->prepare("UPDATE financeiro_extrato SET data_competencia = :data_competencia, data_vencimento = :data_vencimento, data_pagamento = :data_pagamento, descricao = :descricao, categoria = :categoria, id_categoria = :id_categoria, conta = :conta, id_conta = :id_conta, valor_principal = :valor_principal, multa_juros = :multa_juros, desconto_taxa = :desconto_taxa, centro_custo = :centro_custo, id_centro_custo = :id_centro_custo, forma_pagamento = :forma_pagamento, id_forma_pagamento = :id_forma_pagamento, bandeira = :bandeira, id_bandeira = :id_bandeira, fornecedor = :fornecedor, id_fornecedor = :id_fornecedor, observacoes = :observacoes, id_venda = :id_venda, user_criacao = :user_criacao, data_criacao = :data_criacao, user_alteracao = :user_alteracao, data_alteracao = :data_alteracao, nota_fiscal = :nota_fiscal, id_comum = :id_comum, id_cliente = :id_cliente, id_filial = :id_filial, excluido = :excluido, imagem = :imagem WHERE id = :id");

		
		


	$res->bindValue(":data_competencia", $data_competencia);
	$res->bindValue(":data_vencimento", $data_vencimento);
	$res->bindValue(":data_pagamento", $data_pagamento);
	$res->bindValue(":descricao", $descricao);
    $res->bindValue(":categoria", $categoria);
    $res->bindValue(":id_categoria", $id_categoria);
    $res->bindValue(":conta", $conta);
    $res->bindValue(":id_conta", $id_conta);
    $res->bindValue(":valor_principal", $valor_principal);
    $res->bindValue(":multa_juros", $multa_juros);
    $res->bindValue(":desconto_taxa", $desconto_taxa);
    $res->bindValue(":centro_custo", $centro_custo);
    $res->bindValue(":id_centro_custo", $id_centro_custo);
    $res->bindValue(":forma_pagamento", $forma_pagamento);
    $res->bindValue(":id_forma_pagamento", $id_forma_pagamento);
    $res->bindValue(":bandeira", $bandeira);
    $res->bindValue(":id_bandeira", $id_bandeira);
    $res->bindValue(":fornecedor", $fornecedor);
    $res->bindValue(":id_fornecedor", $id_fornecedor);
    $res->bindValue(":observacoes", $observacoes);
    $res->bindValue(":id_venda", $id_venda);
    $res->bindValue(":nota_fiscal", $nota_fiscal);
    $res->bindValue(":id_comum", $id_comum);
    $res->bindValue(":id_cliente", $id_cliente);
    $res->bindValue(":id_filial", $id_filial);
    
    $res->bindValue(":user_criacao", $user_criacao);
    $res->bindValue(":data_criacao", $data_criacao);
    $res->bindValue(":user_alteracao", $id_user);
    $res->bindValue(":data_alteracao", $created);
    
     $res->bindValue(":excluido", $excluido);
    $res->bindValue(":imagem", $imagem);
   
    
    
    
	$res->bindValue(":id", $id);
	$res->execute();
	}



echo 'Salvo com Sucesso!';
?>