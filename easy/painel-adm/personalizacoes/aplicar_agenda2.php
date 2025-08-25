<?php 
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




$created = date('Y-m-d H:i:s');
$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['nome_usuario'];;

$configuracao_nome = 'Negocio';

$intervalo_tempo_agenda= $_POST['intervalo_tempo_agenda'];
$altura_linha_agenda= $_POST['altura_linha_agenda'];

$abertura_agenda= $_POST['abertura_agenda'];
$fechamento_agenda= $_POST['fechamento_agenda'];

		
	


    // Configurar o PDO para lançar exceções
    

    $query = "SELECT COUNT(*) FROM personalizacao_agenda WHERE configuracao_nome = :configuracao_nome";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':configuracao_nome', $configuracao_nome);

    $stmt->execute();

    // Depois de executar a consulta, você pode verificar o resultado
    $count = $stmt->fetchColumn();

    // Log no console do navegador
  

   

  

if ($count > 0) {
    // UPDATE se a configuração já existe
    
    $query = "UPDATE personalizacao_agenda SET 
                
                intervalo_tempo_agenda = :intervalo_tempo_agenda, 
                altura_linha_agenda = :altura_linha_agenda, abertura_agenda = :abertura_agenda, fechamento_agenda = :fechamento_agenda
              WHERE configuracao_nome = :configuracao_nome";
              $stmt = $pdo->prepare($query);
} else {
    // INSERT se a configu ração não existe
    
   
    $query = "INSERT INTO personalizacao (configuracao_nome, intervalo_tempo_agenda, altura_linha_agenda, abertura_agenda, fechamento_agenda) 
              VALUES (:configuracao_nome, :intervalo_tempo_agenda, :altura_linha_agenda, :abertura_agenda, :fechamento_agenda)";
              

$stmt = $pdo->prepare($query);
}
// Bind dos valores comuns para INSERT e UPDATE
$stmt->bindParam(':configuracao_nome', $configuracao_nome);
$stmt->bindParam(':intervalo_tempo_agenda', $intervalo_tempo_agenda);
$stmt->bindParam(':altura_linha_agenda', $altura_linha_agenda);
$stmt->bindParam(':abertura_agenda', $abertura_agenda);
$stmt->bindParam(':fechamento_agenda', $fechamento_agenda);

if ($stmt->execute()) {
    // Supondo sucesso na operação
    $response = ['success' => true, 'message' => 'Configuração salva com sucesso!'];
} else {
    // Em caso de falha
    $errorInfo = $stmt->errorInfo();
    $response = ['success' => false, 'message' => 'Erro ao salvar a configuração: ' . $errorInfo[2]]; // $errorInfo[2] contém a mensagem de erro específica do driver
}

// Configura o header para indicar que o tipo de resposta é JSON
header('Content-Type: application/json');
// Envia a resposta JSON
echo json_encode($response);


?>