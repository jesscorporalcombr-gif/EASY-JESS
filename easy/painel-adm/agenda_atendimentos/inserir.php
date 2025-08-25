<?php 
require_once("../../conexao.php");
//require_once('verificar-permissao.php');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




$id_usuario = $_SESSION['id_usuario'];


$created = date('Y-m-d H:i:s');

// Cria um horário-limite de 3 segundos atrás
$limite = date('Y-m-d H:i:s', time() - 3);

// 1) Verifica se já existe registro do mesmo usuário nos últimos 3 segundos
$sqlVerifica = "
    SELECT COUNT(*) 
    FROM agendamentos
    WHERE id_user_criacao = :id_usuario
      AND data_criacao >= :limite
";
$stmt = $pdo->prepare($sqlVerifica);
$stmt->bindValue(':id_usuario', $id_usuario);
$stmt->bindValue(':limite', $limite);
$stmt->execute();
$total = $stmt->fetchColumn();

if ($total > 0) {
    // Já tem registro nos últimos 3s
    echo json_encode([
        'success' => false,
        'message' => 'Você acabou de inserir um agendamento. Aguarde alguns segundos.'
    ]);
    exit;
}





$usuario = $_SESSION['nome_usuario'];

$tipo_agendamento = $_POST['frm-tipo_agendamento'];
$origem_agendamento = $_POST['frm-origem_agendamento'];
$id_agendamento = $_POST['frm-id_agendamento'];



$id_cliente = $_POST['frm-id_cliente'];
$nome_cliente = $_POST['frm-nome_cliente'];
$cpf_cliente = $_POST['frm-cpf_cliente'];
$telefone_cliente = $_POST['frm-telefone_cliente'];
$email_cliente = $_POST['frm-email_cliente'];



$data_agenda = $_POST['frm-data_agenda'];
$hora_ini = $_POST['frm-hora_ini'];
$tempo_min = $_POST['frm-tempo_min'];


$id_servico = $_POST['frm-id_servico'];
$servico = $_POST['frm-servico'];
$valor = $_POST['frm-valor'];

$id_profissional = $_POST['frm-id_profissional']; 
$profissional = $_POST['frm-nome_profissional'];

$observacoes = $_POST['frm-observacao'];
$status = $_POST['frm-status'];


// Certifique-se de que esta entrada está sendo enviada pelo formulário



if (!is_numeric($id_agendamento) || !is_numeric($id_profissional) || !preg_match("/^\d{2}:\d{2}$/", $hora_ini)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

try {
    if($tipo_agendamento == "edicao"){
    
                $res = $pdo->prepare("UPDATE agendamentos SET id_profissional_1 = :id_profissional_1, profissional_1 = :profissional_1, hora = :hora, data = :data, id_cliente = :id_cliente, nome_cliente = :nome_cliente, cpf_cliente = :cpf_cliente, telefone_cliente = :telefone_cliente, email_cliente = :email_cliente, servico = :servico, id_servico = :id_servico,  tempo_min = :tempo_min, preco = :preco, origem_agendamento = :origem_agendamento, observacoes = :observacoes, status = :status, user_alteracao = :user_alteracao, id_user_alteracao = :id_user_alteracao, data_alteracao = :data_alteracao WHERE id = :id" );
                
                //colocar tmepo min
                
                
                $res->bindValue(":id", $id_agendamento);
                $res->bindValue(":data", $data_agenda);
                $res->bindValue(":hora", $hora_ini);
                $res->bindValue(":profissional_1", $profissional);
                $res->bindValue(":id_profissional_1", $id_profissional);

                $res->bindValue(":id_cliente", $id_cliente);
                $res->bindValue(":nome_cliente", $nome_cliente);
                $res->bindValue(":cpf_cliente", $cpf_cliente);
                $res->bindValue(":telefone_cliente", $telefone_cliente);
                $res->bindValue(":email_cliente", $email_cliente);


                $res->bindValue(":servico", $servico);
                $res->bindValue(":tempo_min", $tempo_min);
                $res->bindValue(":id_servico", $id_servico);
                $res->bindValue(":preco", $valor);
                $res->bindValue(":origem_agendamento", $origem_agendamento);
                $res->bindValue(":observacoes", $observacoes);
                $res->bindValue(":status", $status);
                $res->bindValue(":user_alteracao", $usuario);
                $res->bindValue(":id_user_alteracao", $id_usuario);
                $res->bindValue(":data_alteracao", $created);
                $success = $res->execute();

                echo json_encode(['success' => $success]);
    } else {

        $res = $pdo->prepare("INSERT INTO agendamentos SET id_profissional_1 = :id_profissional_1, profissional_1 = :profissional_1, hora = :hora, data = :data, id_cliente = :id_cliente, nome_cliente = :nome_cliente, cpf_cliente = :cpf_cliente, telefone_cliente = :telefone_cliente, email_cliente = :email_cliente, servico = :servico, id_servico = :id_servico, tempo_min = :tempo_min, preco = :preco, origem_agendamento = :origem_agendamento, observacoes = :observacoes, status = :status, user_criacao = :user_criacao , id_user_criacao = :id_user_criacao, data_criacao = :data_criacao");
        

                $res->bindValue(":data", $data_agenda);
                $res->bindValue(":hora", $hora_ini);
                $res->bindValue(":profissional_1", $profissional);
                $res->bindValue(":id_profissional_1", $id_profissional);

                $res->bindValue(":id_cliente", $id_cliente);
                $res->bindValue(":nome_cliente", $nome_cliente);
                $res->bindValue(":cpf_cliente", $cpf_cliente);
                $res->bindValue(":telefone_cliente", $telefone_cliente);
                $res->bindValue(":email_cliente", $email_cliente);
                
                $res->bindValue(":servico", $servico);
                $res->bindValue(":id_servico", $id_servico);
                $res->bindValue(":tempo_min", $tempo_min);
                $res->bindValue(":preco", $valor);
                $res->bindValue(":origem_agendamento", $origem_agendamento);
                $res->bindValue(":observacoes", $observacoes);
                $res->bindValue(":status", $status);
                $res->bindValue(":user_criacao", $usuario);
                $res->bindValue(":id_user_criacao", $id_usuario);
                $res->bindValue(":data_criacao", $created);
                
                
                $success = $res->execute();

                echo json_encode(['success' => $success]);


    }
    
    
    
    
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


?>