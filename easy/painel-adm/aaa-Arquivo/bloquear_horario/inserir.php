<?php 

/*
essa classe recebe os dados da classe agenda_grupo_por_profissional.php tambem.

*/
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];
@$descricao = $_POST['descricao'];  //Observação
//@$eh_avaliacao = $_POST['eh_avaliacao'];


@$profissional = $_POST['profissional'];
@$inicio = $_POST['inicio'];
@$fim = $_POST['fim'];
@$data_final = $_POST['data_final'];

@$equipamento = $_POST['equipamento'];
@$produtos = $_POST['produtos'];
@$procedimento = $_POST['procedimento']; //serviços

@$status = $_POST['status'];

$id = $_POST['id'];
$horarios_id = $id;
@$data_selected = $_POST['data']; //data vinda do formoluario

$created = date('d-m-Y H:i:s');
$hoje = date('Y-m-d');

$inicio_ = (int)$inicio;
//$parte = substr($inicio_, 2);
$ajuste_tamanho = 0;
$diferenca_entre_horarios = 0;

// tranforma hora em minutos para calcula a distancia em pixels
$hora_ini = 60*(substr($inicio, -4, 2));  // hora inicial em minutos
$hora_mim_ini = substr($inicio, -2, 2);  // 2 digitos minutos do horario inicial
$hora_fim = 60*(substr($fim, -4, 2));    // hora final em minutos
$hora_mim_fim = substr($fim, -2, 2);    // 2 digitos minutos do horario final
$tot_ini = (int) ($hora_ini + $hora_mim_ini);
$tot_fim = (int) ($hora_fim + $hora_mim_fim);
$tot_em_minutos = abs($tot_ini - $tot_fim); //retorna a diferença em num positivo

//$tot_tamanho = (int)($tot_em_minutos * 50)/10;  // contabiliza o tamanho em pixels

$tot_tamanho = (int)($tot_em_minutos * 5.2);  // contabiliza o tamanho em pixels


/// calcula a dirença de pixel pelos minutos e multiplica pela hora. (hora * 13) + dif_minutos.

$dois_dig_horas = (int)(substr($inicio, -4, 2));
$diferenca_entre_horas = (int)abs( ( (substr($inicio, -4, 2))) - ((substr($fim, -4, 2))) );


if ( $tot_tamanho == 156){ // compar os minutos 2 digitos finais
			$ajuste_tamanho = 3;
		}
if ($tot_tamanho == 208) { 
			$ajuste_tamanho = 3;
		}
if ($tot_tamanho == 312) { 
			$ajuste_tamanho = 5;
		}
if ($tot_tamanho == 364) { 
			$ajuste_tamanho = 5;
		}
if ($tot_tamanho == 416) { 
			$ajuste_tamanho = 6;
		}

if ($tot_tamanho == 468) { 
			$ajuste_tamanho = 7;
		}
if ($tot_tamanho == 520) { 
			$ajuste_tamanho = 8;
		}
if ($tot_tamanho == 572) { 
			$ajuste_tamanho = 9;
		}

if ($tot_tamanho == 624) { //800 as 1000
			$ajuste_tamanho = 10;
		}

if ($tot_tamanho == 676) { 
			$ajuste_tamanho = 11;
		}

if ($tot_tamanho == 729) { 
			$ajuste_tamanho = 12;
		}
if ($tot_tamanho == 780) { 
			$ajuste_tamanho = 13;
		}
if ($tot_tamanho == 832) { 
			$ajuste_tamanho = 13;
		}
if ($tot_tamanho == 845) { 
			$ajuste_tamanho = 14;
		}
if ($tot_tamanho == 936) { 
			$ajuste_tamanho = 15; //0800 as 1000  continuar a calcular até as 23 horas 
		}


$altura_soma = $tot_tamanho + $ajuste_tamanho; /* soma a diferença do tamanho.  50 + var */

$int_inicio = (int)$inicio;
$int_fim = (int)$fim;
$tot_horario = $int_fim - $int_inicio; // subitrai o horario final para saber se é menor que  inicial 





if ($int_inicio == $fim ) { //confere se a data inicial é igual que a final
			echo 'Horário inicial igual ao final';
			exit();
		}

if ($tot_horario < -1) { //confere se a data inicial é menor que a final
			echo 'Horário inicial menor que o final';
			exit();
		}


if ($data_selected == '' ) { //confere se a data está vazia		
			@$data_selected = $hoje;
		}


			
@$cliente = 'BLOQUEADO';




// confere se o profissional já está agendado no horario e data ao inserir mais 2 vezes

$query_tot_de_registros = $pdo->query("
SELECT * from agendar_conectado WHERE profissional = '$profissional' and inicio = '$inicio' and data = '$data_selected'
 "); //conta os loopings

$res_tot_de_registros = $query_tot_de_registros->fetchAll(PDO::FETCH_ASSOC); 
$total_reg_tot_de_registros = @count($res_tot_de_registros);

//echo $total_reg_tot_de_registros;

if ($total_reg_tot_de_registros >1) {	 // conta quanto agendamentos estao para esse horario (1) seguinica dois agendamentos
		echo 'Exedou o maximo de agendamento para esse profissional neste mesmo horario ';
		exit();
}




// confere se o profissional já está agendado no horario e data inserir

/*
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
				
						if ( $horarios_profissional == $profissional and $horarios_inicio == $inicio and $horarios_data == $data_selected) {

							echo 'Profissional já agendado para este horario inicial';
							exit();
						}
				}
		}
	} */


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
	$res = $pdo->prepare("INSERT INTO agendar_conectado SET  cliente = '$cliente', data_final = '$data_final', data = '$data_selected', usuario = '$id_usuario', descricao = :descricao, arquivo = :foto, inicio = :inicio, fim = :fim, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, profissional = :profissional, altura = '$altura_soma', created = '$created'");

	//$res->bindValue(":eh_avaliacao", $eh_avaliacao);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":foto", $imagem);
	$res->bindValue(":inicio", $inicio);
	$res->bindValue(":fim", $fim);
	$res->bindValue(":equipamento", $equipamento);
	$res->bindValue(":produtos", $produtos);
	$res->bindValue(":procedimento", $procedimento);
	$res->bindValue(":status", $status);
	$res->bindValue(":profissional", $profissional);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE agendar_conectado SET cliente = '$cliente', data_final = '$data_final', data = '$data_selected', usuario = '$id_usuario', descricao = :descricao, arquivo = :foto, inicio = :inicio, fim = :fim, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, profissional = :profissional, altura = '$altura_soma', modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE agendar_conectado SET  cliente = '$cliente',  data_final = '$data_final', data = '$data_selected', usuario = '$id_usuario', descricao = :descricao,  inicio = :inicio, fim = :fim,  equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status,  profissional = :profissional, altura = '$altura_soma', modificado = '$created' WHERE id = :id");
	}


	//$res->bindValue(":eh_avaliacao", $eh_avaliacao);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":id", $id);
	$res->bindValue(":inicio", $inicio);
	$res->bindValue(":fim", $fim);
	$res->bindValue(":equipamento", $equipamento);
	$res->bindValue(":produtos", $produtos);
	$res->bindValue(":procedimento", $procedimento);
	$res->bindValue(":status", $status);
	$res->bindValue(":profissional", $profissional);
	$res->execute();
}

echo 'Salvo com Sucesso!';
?>
