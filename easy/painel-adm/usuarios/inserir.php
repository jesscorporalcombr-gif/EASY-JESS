<?php 
require_once("../../conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$cpf = $_POST['cpf'];
$senha = $_POST['senha'];
$nivel = $_POST['nivel'];
$id = $_POST['id'];
$dt_nascimento = $_POST['dt_nascimento'];
$orgao = $_POST['orgao'];
$rg = $_POST['rg'];
$est_civil = $_POST['est_civil'];
$cnh = $_POST['cnh'];
$ctps = $_POST['ctps'];
$serie = $_POST['serie'];
$pis = $_POST['pis'];
$titulo = $_POST['titulo'];
$zona = $_POST['zona'];
$sesao = $_POST['sesao'];
$cep = $_POST['cep'];
$endereco = $_POST['endereco'];
$num = $_POST['num'];
$complemento = $_POST['complemento'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$uf = $_POST['uf'];
$nome_mae = $_POST['nome_mae'];
$nome_pai = $_POST['nome_pai'];
$telefone = $_POST['telefone'];
$banco_conta = $_POST['banco_conta'];
$agencia = $_POST['agencia'];
$conta = $_POST['conta'];
$pix = $_POST['pix'];
$tipo_pix = $_POST['tipo_pix'];
$nome_conj = $_POST['nome_conj'];
$dados_conj = $_POST['dados_conj'];
$sexo = $_POST['sexo'];
$cargo = $_POST['cargo'];
$situacao = $_POST['situacao'];
$created = date('d-m-Y H:i:s');
$celular = $_POST['celular'];
$etinia = $_POST['etinia'];
$deficiente = $_POST['deficiente'];
$tipo_de_deficiencia = $_POST['tipo_de_deficiencia'];
$tipo_sanguineo = $_POST['tipo_sanguineo'];
$naturalidade = $_POST['naturalidade'];
$estado = $_POST['estado'];
$nacionalidade = $_POST['nacionalidade'];
$chegada_ao_brasil = $_POST['chegada_ao_brasil'];
$rg_data_de_emissso = $_POST['rg_data_de_emissso'];
$cnh_categoria = $_POST['cnh_categoria'];
$validade_cnh = $_POST['validade_cnh'];
$digito = $_POST['digito'];
$carteira_reservista = $_POST['carteira_reservista'];
$digito_conta_corrente = $_POST['digito_conta_corrente'];
$grau_de_instrucao = $_POST['grau_de_instrucao'];
$cursos = $_POST['cursos'];
$pis_data_de_cadastramento = $_POST['pis_data_de_cadastramento'];
$num_da_conta_fgts = $_POST['num_da_conta_fgts'];
$fgts_data_de_opcao = $_POST['fgts_data_de_opcao'];
$banco_depositario_fgts = $_POST['banco_depositario_fgts'];
$num_dependentes = $_POST['num_dependentes'];
$dados_dependentes = $_POST['dados_dependentes'];
$ativo_na_agenda = $_POST['ativo_na_agenda'];
$cor_usuario = $_POST['cor_usuario'];
$antigo = $_POST['antigo'];
$antigo2 = $_POST['antigo2'];






/*Restringe a quantidade de usuario*/
/*Restringe a quantidade de usuario*/

// CONFERE A QUANTIDADE DE USUARIOS DO SISTEMA
if($id == ""){


	$query_cont_user = $pdo->query("SELECT * from usuarios order by id desc");
	$res_cont_user = $query_cont_user->fetchAll(PDO::FETCH_ASSOC);
	$total_reg_cont_user = @count($res_cont_user);

	//echo $total_reg_cont_user;
	//exit();


	if($total_reg_cont_user >= 7){ // o cont soma o zero enão tem que por um a menos do que se quer.
		echo $total_reg_cont_user . '. ';
		echo 'Excedeu o numero máximo de Colaboradores!';
		exit();
	}
}


// CONFERE A QUANTIDADE DE USUARIOS na agenda
if($id != ""){

	$query_empresa2 = $pdo->query("SELECT * from `usuarios` where ativo_na_agenda = 'Ativo'");
	$res_empresa2 = $query_empresa2->fetchAll(PDO::FETCH_ASSOC);
	$total_reg_empresa2 = @count($res_empresa2);

	if($total_reg_empresa2 >= 5 && $ativo_na_agenda == 'Ativo'){
		echo 'Excedeu o numero máximo de Colaboradores na agenda!';
		exit();
	}
}

if($id == ""){

	$query_empresa2 = $pdo->query("SELECT * from `usuarios` where ativo_na_agenda = 'Ativo'");
	$res_empresa2 = $query_empresa2->fetchAll(PDO::FETCH_ASSOC);
	$total_reg_empresa2 = @count($res_empresa2);

	if($total_reg_empresa2 >= 5 && $ativo_na_agenda == 'Ativo'){
		echo 'Excedeu o numero máximo de Colaboradores na agenda!';
		exit();
	}
}

/*Restringe a quantidade de usuario*/
/*Restringe a quantidade de usuario*/






/*
// CONFERE A QUANTIDADE DE USUARIOS ATIVOS NA AGENDA
if($id == ""){

	$query_cont_agenda = $pdo->prepare("SELECT count(ativo_na_agenda) from `usuarios` where ativo_na_agenda = 'Ativo'
"); 
	$query_cont_agenda->execute();
	$res_cont_agenda = $query_cont_agenda->fetchAll(PDO::FETCH_ASSOC);


	if($res_cont_agenda >= 5){
		echo 'Excedeu o numero máximo de Colaboradores na agenda!';
		exit();
	}
}

// CONFERE A QUANTIDADE DE USUARIOS ATIVOS NA AGENDA
if($id != ""){

	$query_cont_agenda = $pdo->prepare("SELECT count(ativo_na_agenda) from `usuarios` where ativo_na_agenda = 'Ativo'
"); 
	$query_cont_agenda->execute();
	$res_cont_agenda = $query_cont_agenda->fetchAll(PDO::FETCH_ASSOC);


	if($res_cont_agenda >= 5){
		echo 'Excedeu o numero máximo de Colaboradores na agenda!';
		exit();
	}
}

*/


// EVITAR DUPLICIDADE NO EMAIL
if($antigo2 != $email){
	$query_con = $pdo->prepare("SELECT * from usuarios WHERE email = :email");
	$query_con->bindValue(":email", $email);
	$query_con->execute();
	$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res_con) > 0){
		echo 'O email do usuário já está cadastrado!';
		exit();
	}
}

if($antigo != $cpf){
// EVITAR DUPLICIDADE NO CPF
	$query_con = $pdo->prepare("SELECT * from usuarios WHERE cpf = :cpf");
	$query_con->bindValue(":cpf", $cpf);
	$query_con->execute();
	$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res_con) > 0){
		echo 'O CPF do usuário já está cadastrado!';
		exit();
	}
}


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/usuarios/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO usuarios SET cor_usuario = :cor_usuario, ativo_na_agenda = :ativo_na_agenda, nome = :nome, email = :email, cpf = :cpf, senha = :senha, nivel = :nivel, foto = :foto, imagem = :foto, dt_nascimento = :dt_nascimento, rg = :rg, orgao = :orgao, est_civil = :est_civil, cnh = :cnh, ctps = :ctps, serie = :serie, pis = :pis, titulo = :titulo, zona = :zona, sesao = :sesao, cep = :cep, endereco = :endereco, num = :num, complemento = :complemento, bairro = :bairro, cidade = :cidade, uf = :uf, nome_mae = :nome_mae, nome_pai = :nome_pai, telefone = :telefone, banco_conta = :banco_conta, agencia = :agencia, conta = :conta, pix = :pix, tipo_pix = :tipo_pix, nome_conj = :nome_conj, dados_conj = :dados_conj, sexo = :sexo, cargo = :cargo, situacao = :situacao, created = '$created', celular = :celular, etinia = :etinia, deficiente = :deficiente, tipo_de_deficiencia = :tipo_de_deficiencia, tipo_sanguineo = :tipo_sanguineo, naturalidade = :naturalidade, estado = :estado, nacionalidade = :nacionalidade, chegada_ao_brasil = :chegada_ao_brasil, rg_data_de_emissso = :rg_data_de_emissso, cnh_categoria = :cnh_categoria, validade_cnh = :validade_cnh, digito = :digito, carteira_reservista = :carteira_reservista, digito_conta_corrente = :digito_conta_corrente, grau_de_instrucao = :grau_de_instrucao, cursos = :cursos, pis_data_de_cadastramento = :pis_data_de_cadastramento, num_da_conta_fgts = :num_da_conta_fgts, fgts_data_de_opcao = :fgts_data_de_opcao, banco_depositario_fgts = :banco_depositario_fgts, num_dependentes = :num_dependentes, dados_dependentes = :dados_dependentes ");

	$res->bindValue(":cor_usuario", $cor_usuario);
	$res->bindValue(":ativo_na_agenda", $ativo_na_agenda);
	$res->bindValue(":nome", $nome);
	$res->bindValue(":email", $email);
	$res->bindValue(":cpf", $cpf);
	$res->bindValue(":senha", $senha);
	$res->bindValue(":nivel", $nivel);
	$res->bindValue(":foto", $imagem);
	$res->bindValue(":imagem", $imagem);

	$res->bindValue(":dt_nascimento", $dt_nascimento);
	$res->bindValue(":rg", $rg);
	$res->bindValue(":orgao", $orgao);
	$res->bindValue(":est_civil", $est_civil);
	$res->bindValue(":cnh", $cnh);
	$res->bindValue(":ctps", $ctps);
	$res->bindValue(":serie", $serie);
	$res->bindValue(":pis", $pis);
	$res->bindValue(":titulo", $titulo);
	$res->bindValue(":zona", $zona);
	$res->bindValue(":sesao", $sesao);
	$res->bindValue(":cep", $cep);
	$res->bindValue(":endereco", $endereco);
	$res->bindValue(":num", $num);
	$res->bindValue(":complemento", $complemento);
	$res->bindValue(":bairro", $bairro);
	$res->bindValue(":cidade", $cidade);
	$res->bindValue(":uf", $uf);
	$res->bindValue(":nome_mae", $nome_mae);
	$res->bindValue(":nome_pai", $nome_pai);
	$res->bindValue(":telefone", $telefone);
	$res->bindValue(":banco_conta", $banco_conta);
	$res->bindValue(":agencia", $agencia);
	$res->bindValue(":conta", $conta);
	$res->bindValue(":pix", $pix);
	$res->bindValue(":tipo_pix", $tipo_pix);
	$res->bindValue(":nome_conj", $nome_conj);
	$res->bindValue(":dados_conj", $dados_conj);
	$res->bindValue(":sexo", $sexo);
	$res->bindValue(":cargo", $cargo);
	$res->bindValue(":situacao", $situacao);

	$res->bindValue(":celular", $celular);
  $res->bindValue(":etinia", $etinia);
  $res->bindValue(":deficiente", $deficiente);

  $res->bindValue(":tipo_de_deficiencia", $tipo_de_deficiencia);
	$res->bindValue(":tipo_sanguineo", $tipo_sanguineo);
	$res->bindValue(":naturalidade", $naturalidade);

	$res->bindValue(":estado", $estado);
	$res->bindValue(":nacionalidade", $nacionalidade);
	$res->bindValue(":chegada_ao_brasil", $chegada_ao_brasil);

	$res->bindValue(":rg_data_de_emissso", $rg_data_de_emissso);
	$res->bindValue(":cnh_categoria", $cnh_categoria);
	$res->bindValue(":validade_cnh", $validade_cnh);

	$res->bindValue(":digito", $digito);
	$res->bindValue(":carteira_reservista", $carteira_reservista);
	$res->bindValue(":digito_conta_corrente", $digito_conta_corrente);

	$res->bindValue(":grau_de_instrucao", $grau_de_instrucao);
	$res->bindValue(":cursos", $cursos);
	$res->bindValue(":pis_data_de_cadastramento", $pis_data_de_cadastramento);

	$res->bindValue(":num_da_conta_fgts", $num_da_conta_fgts);
	$res->bindValue(":fgts_data_de_opcao", $fgts_data_de_opcao);
	$res->bindValue(":banco_depositario_fgts", $banco_depositario_fgts);

	$res->bindValue(":num_dependentes", $num_dependentes);
  $res->bindValue(":dados_dependentes", $dados_dependentes);

	

	$res->execute();
}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE usuarios SET cor_usuario = :cor_usuario, ativo_na_agenda = :ativo_na_agenda, nome = :nome, email = :email, cpf = :cpf, foto = :foto, imagem = :foto, senha = :senha, nivel = :nivel, dt_nascimento = :dt_nascimento, rg = :rg, orgao = :orgao, est_civil = :est_civil, cnh = :cnh, ctps = :ctps, serie = :serie, pis = :pis, titulo = :titulo, zona = :zona, sesao = :sesao, cep = :cep, endereco = :endereco, num = :num, complemento = :complemento, bairro = :bairro, cidade = :cidade, uf = :uf, nome_mae = :nome_mae, nome_pai = :nome_pai, telefone = :telefone, banco_conta = :banco_conta, agencia = :agencia, conta = :conta, pix = :pix, tipo_pix = :tipo_pix, nome_conj = :nome_conj, dados_conj = :dados_conj, sexo = :sexo, cargo = :cargo, situacao = :situacao, modificado = '$created', celular = :celular, etinia = :etinia , deficiente = :deficiente, tipo_de_deficiencia = :tipo_de_deficiencia, tipo_sanguineo = :tipo_sanguineo, naturalidade = :naturalidade, estado = :estado, nacionalidade = :nacionalidade, chegada_ao_brasil = :chegada_ao_brasil, rg_data_de_emissso = :rg_data_de_emissso, cnh_categoria = :cnh_categoria, validade_cnh = :validade_cnh, digito = :digito, carteira_reservista = :carteira_reservista, digito_conta_corrente = :digito_conta_corrente, grau_de_instrucao = :grau_de_instrucao, cursos = :cursos, pis_data_de_cadastramento = :pis_data_de_cadastramento, num_da_conta_fgts = :num_da_conta_fgts, fgts_data_de_opcao = :fgts_data_de_opcao, banco_depositario_fgts = :banco_depositario_fgts , num_dependentes = :num_dependentes, dados_dependentes = :dados_dependentes WHERE id = :id");

		$res->bindValue(":foto", $imagem);
		$res->bindValue(":imagem", $imagem);
		
		}else{
			$res = $pdo->prepare("UPDATE usuarios SET cor_usuario = :cor_usuario, ativo_na_agenda = :ativo_na_agenda, nome = :nome, email = :email, cpf = :cpf, senha = :senha, nivel = :nivel, dt_nascimento = :dt_nascimento, rg = :rg, orgao = :orgao, est_civil = :est_civil, cnh = :cnh, ctps = :ctps, serie = :serie, pis = :pis, titulo = :titulo, zona = :zona, sesao = :sesao, cep = :cep, endereco = :endereco, num = :num, complemento = :complemento, bairro = :bairro, cidade = :cidade, uf = :uf, nome_mae = :nome_mae, nome_pai = :nome_pai, telefone = :telefone, banco_conta = :banco_conta, agencia = :agencia, conta = :conta, pix = :pix, tipo_pix = :tipo_pix, nome_conj = :nome_conj, dados_conj = :dados_conj, sexo = :sexo, cargo = :cargo, situacao = :situacao, modificado = '$created', celular = :celular, etinia = :etinia , deficiente = :deficiente, tipo_de_deficiencia = :tipo_de_deficiencia, tipo_sanguineo = :tipo_sanguineo, naturalidade = :naturalidade, estado = :estado, nacionalidade = :nacionalidade, chegada_ao_brasil = :chegada_ao_brasil, rg_data_de_emissso = :rg_data_de_emissso, cnh_categoria = :cnh_categoria, validade_cnh = :validade_cnh, digito = :digito, carteira_reservista = :carteira_reservista, digito_conta_corrente = :digito_conta_corrente, grau_de_instrucao = :grau_de_instrucao, cursos = :cursos, pis_data_de_cadastramento = :pis_data_de_cadastramento, num_da_conta_fgts = :num_da_conta_fgts, fgts_data_de_opcao = :fgts_data_de_opcao, banco_depositario_fgts = :banco_depositario_fgts, num_dependentes = :num_dependentes, dados_dependentes = :dados_dependentes WHERE id = :id");

		}

	$res->bindValue(":cor_usuario", $cor_usuario);
	$res->bindValue(":ativo_na_agenda", $ativo_na_agenda);
	$res->bindValue(":nome", $nome);
	$res->bindValue(":email", $email);
	$res->bindValue(":cpf", $cpf);
	$res->bindValue(":senha", $senha);
	$res->bindValue(":nivel", $nivel);
	$res->bindValue(":dt_nascimento", $dt_nascimento);
	$res->bindValue(":id", $id);
	$res->bindValue(":rg", $rg);
	$res->bindValue(":orgao", $orgao);
	$res->bindValue(":est_civil", $est_civil);
	$res->bindValue(":cnh", $cnh);
	$res->bindValue(":ctps", $ctps);
	$res->bindValue(":serie", $serie);
	$res->bindValue(":pis", $pis);
	$res->bindValue(":titulo", $titulo);
	$res->bindValue(":zona", $zona);
	$res->bindValue(":sesao", $sesao);
	$res->bindValue(":cep", $cep);
	$res->bindValue(":endereco", $endereco);
	$res->bindValue(":num", $num);
	$res->bindValue(":complemento", $complemento);
	$res->bindValue(":bairro", $bairro);
	$res->bindValue(":cidade", $cidade);
	$res->bindValue(":uf", $uf);
	$res->bindValue(":nome_mae", $nome_mae);
	$res->bindValue(":nome_pai", $nome_pai);
	$res->bindValue(":telefone", $telefone);
	$res->bindValue(":banco_conta", $banco_conta);
	$res->bindValue(":agencia", $agencia);
	$res->bindValue(":conta", $conta);
	$res->bindValue(":pix", $pix);
	$res->bindValue(":tipo_pix", $tipo_pix);
	$res->bindValue(":nome_conj", $nome_conj);
	$res->bindValue(":dados_conj", $dados_conj);

	$res->bindValue(":sexo", $sexo);
	$res->bindValue(":cargo", $cargo);
	$res->bindValue(":situacao", $situacao);

	$res->bindValue(":celular", $celular);
  $res->bindValue(":etinia", $etinia);
  $res->bindValue(":deficiente", $deficiente);

  $res->bindValue(":tipo_de_deficiencia", $tipo_de_deficiencia);
	$res->bindValue(":tipo_sanguineo", $tipo_sanguineo);
	$res->bindValue(":naturalidade", $naturalidade);

	$res->bindValue(":estado", $estado);
	$res->bindValue(":nacionalidade", $nacionalidade);
	$res->bindValue(":chegada_ao_brasil", $chegada_ao_brasil);

	$res->bindValue(":rg_data_de_emissso", $rg_data_de_emissso);
	$res->bindValue(":cnh_categoria", $cnh_categoria);
	$res->bindValue(":validade_cnh", $validade_cnh);

	$res->bindValue(":digito", $digito);
	$res->bindValue(":carteira_reservista", $carteira_reservista);
	$res->bindValue(":digito_conta_corrente", $digito_conta_corrente);

	$res->bindValue(":grau_de_instrucao", $grau_de_instrucao);
	$res->bindValue(":cursos", $cursos);
	$res->bindValue(":pis_data_de_cadastramento", $pis_data_de_cadastramento);

	$res->bindValue(":num_da_conta_fgts", $num_da_conta_fgts);
	$res->bindValue(":fgts_data_de_opcao", $fgts_data_de_opcao);
	$res->bindValue(":banco_depositario_fgts", $banco_depositario_fgts);

	$res->bindValue(":num_dependentes", $num_dependentes);
  $res->bindValue(":dados_dependentes", $dados_dependentes);

	
	

	$res->execute();
	}


echo 'Salvo com Sucesso!';
?>