<?php 
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];
@$descricao = $_POST['descricao'];  //Observação
@$valor = $_POST['valor'];
//@$eh_avaliacao = $_POST['eh_avaliacao'];


@$cliente = $_POST['cliente'];
@$tel_cliente = $_POST['tel_cliente'];
@$profissional = $_POST['profissional'];
@$sala = $_POST['sala'];
@$inicio = $_POST['inicio'];
@$fim = $_POST['fim'];

@$equipamento = $_POST['equipamento'];
@$produtos = $_POST['produtos'];
@$procedimento = $_POST['procedimento']; //serviços

@$status = $_POST['status'];
@$situacao = $_POST['situacao'];

$id = $_POST['id'];
$horarios_id = $id;
$data = $_POST['data'];

$created = date('d-m-Y H:i:s');

$int_inicio = (int)$inicio;
$int_fim = (int)$fim;
$tot_horario = $int_fim - $int_inicio;

/* echo $int_inicio; echo '---'; echo $int_fim; echo '---//'; echo $tot_horario; */

if ($int_inicio == $fim ) { //confere se a data inicial é igual que a final
			echo 'Horário inicial igual ao final';
			exit();
		}

if ($tot_horario < -1) { //confere se a data inicial é menor que a final
			echo 'Horário inicial menor que o final';
			exit();
		}

// confere se o profissional já está agendado no horario e data inserir

  $query = $pdo->query("SELECT * from agendar_conectado order by id asc"); //conta os loopings
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);

	if($total_reg > 0){  // enquanto existir registro 

		for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}   //enconta as variaveis de cada registro

				@$horarios_profissional = $res[$i]['profissional'];
				@$horarios_inicio = $res[$i]['inicio'];
				@$horarios_fim = $res[$i]['fim'];
				@$horarios_data = $res[$i]['data'];
				
				if ($id =='') {  // o horarios_id é para poder editar
				
						if ( $horarios_profissional == $profissional and 
								$horarios_inicio == $inicio and 
								$horarios_data == $horarios_data) {

							echo 'Profissional já agendado para este horario inicial';
							exit();
						}
				}
		}
	}


// confere se o profissional já está agendado no horario e data INSERIR

  $query = $pdo->query("SELECT * from agendar_conectado order by id asc"); //conta os loopings
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);

	if($total_reg > 0){ 

		for($i=0; $i < $total_reg; $i++){
						foreach ($res[$i] as $key => $value){	}   //enconta as variaveis

				@$horarios_profissional = $res[$i]['profissional'];
				@$horarios_inicio = $res[$i]['inicio'];
				@$horarios_fim = $res[$i]['fim'];
				@$horarios_data = $res[$i]['data'];
				@$horarios_id = $res[$i]['data'];
				
						
					if($res[$i]['data'] == ''){
						
						if ( $horarios_profissional == $profissional and 
								$horarios_inicio == $inicio and 
								$horarios_data == $data) {

							echo 'Profissional já agendado neste periodo';
							exit();
								
						}
					}

		}
	}
	

/*
 echo $id_usuario.'//ide usua/////'. $descricao.'//descrição/////'. $data  .'//data////'. $valor.'//valor/////'. $cliente.'//cliente/////'. $tel_cliente.'//tel cliente/////'. $profissional.'//profissi/////'. $sala.'//sala/////'. $inicio.'//inicio/////'. $fim.'//fim/////'. $equipamento.'//equipamen/////'. $produtos.'//produto/////'. $procedimento.'//procedim/////'. $status.'status/////';
 */


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/agendar_conectado/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO agendar_conectado SET   data = '$data', usuario = '$id_usuario', descricao = :descricao, valor = :valor, arquivo = :foto, inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, created = '$created'");

	//$res->bindValue(":eh_avaliacao", $eh_avaliacao);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":foto", $imagem);
	$res->bindValue(":inicio", $inicio);
	$res->bindValue(":fim", $fim);
	$res->bindValue(":sala", $sala);
	$res->bindValue(":equipamento", $equipamento);
	$res->bindValue(":produtos", $produtos);
	$res->bindValue(":procedimento", $procedimento);
	$res->bindValue(":status", $status);
	$res->bindValue(":cliente", $cliente);
	$res->bindValue(":tel_cliente", $tel_cliente);
	$res->bindValue(":profissional", $profissional);
	$res->bindValue(":situacao", $situacao);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE agendar_conectado SET data = '$data', usuario = '$id_usuario', descricao = :descricao, valor = :valor, arquivo = :foto, inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE agendar_conectado SET   data = '$data', usuario = '$id_usuario', descricao = :descricao, valor = :valor,  inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, modificado = '$created' WHERE id = :id");
	}


	//$res->bindValue(":eh_avaliacao", $eh_avaliacao);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":id", $id);
	$res->bindValue(":inicio", $inicio);
	$res->bindValue(":fim", $fim);
	$res->bindValue(":sala", $sala);
	$res->bindValue(":equipamento", $equipamento);
	$res->bindValue(":produtos", $produtos);
	$res->bindValue(":procedimento", $procedimento);
	$res->bindValue(":status", $status);
	$res->bindValue(":cliente", $cliente);
	$res->bindValue(":tel_cliente", $tel_cliente);
	$res->bindValue(":profissional", $profissional);
	$res->bindValue(":situacao", $situacao);
	$res->execute();
}

echo 'Salvo com Sucesso!';
?>
