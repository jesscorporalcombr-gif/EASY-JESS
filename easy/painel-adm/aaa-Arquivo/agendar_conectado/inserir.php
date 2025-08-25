<?php 

/*
essa classe recebe os dados da classe agenda_grupo_por_profissional.php tambem.

*/
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


if ( $tot_tamanho == 156){ 
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

if ($tot_tamanho == 988) { 
			$ajuste_tamanho = 16;
		}

if ($tot_tamanho == 1040) { 
			$ajuste_tamanho = 17;
		}

if ($tot_tamanho == 1092) { 
			$ajuste_tamanho = 18;
		}

if ($tot_tamanho == 1144) { 
			$ajuste_tamanho = 19;
		}

if ($tot_tamanho == 1196) { 
			$ajuste_tamanho = 20;
		}

if ($tot_tamanho == 1248) { 
			$ajuste_tamanho = 21;
		}

if ($tot_tamanho == 1300) {  ///1200 
			$ajuste_tamanho = 22;
		}

if ($tot_tamanho == 1352) { 
			$ajuste_tamanho = 23;
		}

if ($tot_tamanho == 1404) { 
			$ajuste_tamanho = 24;
		}

if ($tot_tamanho == 1456) { 
			$ajuste_tamanho = 25;
		}

if ($tot_tamanho == 1508) { 
			$ajuste_tamanho = 26;
		}

if ($tot_tamanho == 1560) { 
			$ajuste_tamanho = 27;
		}

if ($tot_tamanho == 1612) { 
			$ajuste_tamanho = 28;
		}

if ($tot_tamanho == 1664) { //1400
			$ajuste_tamanho = 29;
		}


if ($tot_tamanho == 1716) { 
			$ajuste_tamanho = 32;
		}

if ($tot_tamanho == 1768) { 
			$ajuste_tamanho = 33;
		}

if ($tot_tamanho == 1820) { 
			$ajuste_tamanho = 31;
		}

if ($tot_tamanho == 1872) { 
			$ajuste_tamanho = 35;
		}

if ($tot_tamanho == 1924) { 
			$ajuste_tamanho = 36;
		}

if ($tot_tamanho == 1976) { 
			$ajuste_tamanho = 37;
		}

if ($tot_tamanho == 2028) { //1600
			$ajuste_tamanho = 39;
		}

if ($tot_tamanho == 2080) { 
			$ajuste_tamanho = 40;
		}

if ($tot_tamanho == 2132) { 
			$ajuste_tamanho = 41;
		}

if ($tot_tamanho == 2184) { 
			$ajuste_tamanho = 42;
		}

if ($tot_tamanho == 2236) { 
			$ajuste_tamanho = 45;
		}

if ($tot_tamanho == 2288) { 
			$ajuste_tamanho = 46;
		}


if ($tot_tamanho == 2340) { 
			$ajuste_tamanho = 47;
		}

if ($tot_tamanho == 2392) { //18000
			$ajuste_tamanho = 47;
		}

if ($tot_tamanho == 2444) { 
			$ajuste_tamanho = 47;
		}

if ($tot_tamanho == 2496) { 
			$ajuste_tamanho = 47;
		}

if ($tot_tamanho == 2548) { 
			$ajuste_tamanho = 48;
		}

if ($tot_tamanho == 2600) { 
			$ajuste_tamanho = 49;
		}

if ($tot_tamanho == 2652) { 
			$ajuste_tamanho = 50;
		}

if ($tot_tamanho == 2704) { 
			$ajuste_tamanho = 51;
		}

if ($tot_tamanho == 2756) { //2000
			$ajuste_tamanho = 52;
		}

if ($tot_tamanho == 2808) { 
			$ajuste_tamanho = 53;
		}

if ($tot_tamanho == 2860) { 
			$ajuste_tamanho = 54;
		}


if ($tot_tamanho == 2912) { 
			$ajuste_tamanho = 55;
		}

if ($tot_tamanho == 2964) { 
			$ajuste_tamanho = 56;
		}

if ($tot_tamanho == 3016) { 
			$ajuste_tamanho = 57;
		}

if ($tot_tamanho == 3068) { 
			$ajuste_tamanho = 58;
		}

if ($tot_tamanho == 3120) { //22
			$ajuste_tamanho = 59;
		}

if ($tot_tamanho == 3172) { 
			$ajuste_tamanho = 60;
		}

if ($tot_tamanho == 3224) { 
			$ajuste_tamanho = 61;
		}

if ($tot_tamanho == 3276) { 
			$ajuste_tamanho = 62;
		}

if ($tot_tamanho == 3328) { 
			$ajuste_tamanho = 63;
		}

if ($tot_tamanho == 3380) { 
			$ajuste_tamanho = 64;
		}

if ($tot_tamanho == 3432) { 
			$ajuste_tamanho = 65;
		}

if ($tot_tamanho == 3484) { 
			$ajuste_tamanho = 66;
		}


if ($tot_tamanho == 3536) { 
			$ajuste_tamanho = 67;
		}

if ($tot_tamanho == 3588) { 
			$ajuste_tamanho = 62;
		}

if ($tot_tamanho == 3640) { 
			$ajuste_tamanho = 68;
		}

if ($tot_tamanho == 3692) { 
			$ajuste_tamanho = 69;
		}

if ($tot_tamanho == 3744) { 
			$ajuste_tamanho = 65;
		}

if ($tot_tamanho == 3796) { 
			$ajuste_tamanho = 70;
		}

if ($tot_tamanho == 3848) { 
			$ajuste_tamanho = 71;
		}

if ($tot_tamanho == 3900) { 
			$ajuste_tamanho = 72;
		}

if ($tot_tamanho == 3952) { 
			$ajuste_tamanho = 73;
		}

if ($tot_tamanho == 4004) { 
			$ajuste_tamanho = 74;
		}

if ($tot_tamanho == 4056) { 
			$ajuste_tamanho = 75;
		}

if ($tot_tamanho == 4108) { 
			$ajuste_tamanho = 76;
		}


if ($tot_tamanho == 4160) { 
			$ajuste_tamanho = 77;
		}

if ($tot_tamanho == 4212) { 
			$ajuste_tamanho = 78;
		}

if ($tot_tamanho == 4264) { 
			$ajuste_tamanho = 79;
		}

if ($tot_tamanho == 4316) { 
			$ajuste_tamanho = 80;
		}

if ($tot_tamanho == 4368) { 
			$ajuste_tamanho = 81;
		}

if ($tot_tamanho == 4420) { 
			$ajuste_tamanho = 82;
		}

if ($tot_tamanho == 4472) { 
			$ajuste_tamanho = 83;
		}

if ($tot_tamanho == 4524) { 
			$ajuste_tamanho = 82;
		}

if ($tot_tamanho == 4576) { 
			$ajuste_tamanho = 85;
		}

if ($tot_tamanho == 4628) { 
			$ajuste_tamanho = 86;
		}

if ($tot_tamanho == 4680) { 
			$ajuste_tamanho = 87;
		}

if ($tot_tamanho == 4732) { 
			$ajuste_tamanho = 88;
		}


if ($tot_tamanho == 4784) { 
			$ajuste_tamanho = 89;
		}


if ($tot_tamanho == 4836) { 
			$ajuste_tamanho = 90;
		}

if ($tot_tamanho == 4888) { 
			$ajuste_tamanho = 91;
		}

if ($tot_tamanho == 4940) { 
			$ajuste_tamanho = 91;
		}

if ($tot_tamanho == 4992) { 
			$ajuste_tamanho = 92;
		}

if ($tot_tamanho == 5044) { 
			$ajuste_tamanho = 93;
		}

if ($tot_tamanho == 5096) { 
			$ajuste_tamanho = 94;
		}

if ($tot_tamanho == 5148) { 
			$ajuste_tamanho = 95;
		}

if ($tot_tamanho == 5200) { 
			$ajuste_tamanho = 96;
		}

if ($tot_tamanho == 5252) { 
			$ajuste_tamanho = 97;
		}

if ($tot_tamanho == 5304) { 
			$ajuste_tamanho = 98;
		}


if ($tot_tamanho == 5356) { 
			$ajuste_tamanho = 99;
		}

if ($tot_tamanho == 5408) { 
			$ajuste_tamanho = 100;
		}

if ($tot_tamanho == 5460) { 
			$ajuste_tamanho = 102;
		}

if ($tot_tamanho == 5512) { 
			$ajuste_tamanho = 103;
		}

if ($tot_tamanho == 5564) { 
			$ajuste_tamanho = 104;
		}

if ($tot_tamanho == 5616) { 
			$ajuste_tamanho = 105;
		}

if ($tot_tamanho == 5668) { 
			$ajuste_tamanho = 106;
		}

if ($tot_tamanho == 5720) { 
			$ajuste_tamanho = 107;
		}

if ($tot_tamanho == 5772) { 
			$ajuste_tamanho = 108;
		}

if ($tot_tamanho == 5824) { 
			$ajuste_tamanho = 109;
		}

if ($tot_tamanho == 5876) { 
			$ajuste_tamanho = 110;
		}

if ($tot_tamanho == 5928) { 
			$ajuste_tamanho = 111;
		}


if ($tot_tamanho == 5980) { 
			$ajuste_tamanho = 112;
		}

if ($tot_tamanho == 6032) { 
			$ajuste_tamanho = 113;
		}

if ($tot_tamanho == 6084) { 
			$ajuste_tamanho = 114;
		}

if ($tot_tamanho == 6136) { 
			$ajuste_tamanho = 115;
		}

if ($tot_tamanho == 6188) { 
			$ajuste_tamanho = 116;
		}

if ($tot_tamanho == 6240) { 
			$ajuste_tamanho = 117;
		}






$altura_soma = $tot_tamanho + $ajuste_tamanho; /* soma a diferença do tamanho.  50 + var */

$int_inicio = (int)$inicio;
$int_fim = (int)$fim;
$tot_horario = $int_fim - $int_inicio; // subitrai o horario final para saber se é menor que  inicial 




if ($cliente == '' ) { //confere se o nome está vazio
			
			@$cliente = 'Sem Paciente';
		}

if ($situacao == 'INTERVALO' ) { //confere se é intervalo
			
			@$cliente = 'INTERVALO';
		}


if ($status == 'BLOQUEADO' ) { //confere se é BLOQUEADO
			
			@$cliente = 'BLOQUEADO';
		}



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
	$res = $pdo->prepare("INSERT INTO agendar_conectado SET   data = '$data_selected', usuario = '$id_usuario', descricao = :descricao, valor = :valor, arquivo = :foto, inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, altura = '$altura_soma', created = '$created'");

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
		$res = $pdo->prepare("UPDATE agendar_conectado SET data = '$data_selected', usuario = '$id_usuario', descricao = :descricao, valor = :valor, arquivo = :foto, inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, altura = '$altura_soma', modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE agendar_conectado SET   data = '$data_selected', usuario = '$id_usuario', descricao = :descricao, valor = :valor,  inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, altura = '$altura_soma', modificado = '$created' WHERE id = :id");
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
