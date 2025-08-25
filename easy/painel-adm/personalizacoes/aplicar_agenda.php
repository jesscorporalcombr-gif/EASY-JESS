<?php 
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




$created = date('Y-m-d H:i:s');
$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['nome_usuario'];;

$configuracao_nome = 'Negocio';




$cor_fundo_agenda = $_POST['cor_fundo_agenda'];
$cor_fonte_horario = $_POST['cor_fonte_horario'];
$cor_fonte_celula = $_POST['cor_fonte_celula'];
$cor_celula_selecionada = $_POST['cor_celula_selecionada'];
$cor_linha_horizontal = $_POST['cor_linha_horizontal'];
$cor_linha_vertical = $_POST['cor_linha_vertical'];
$cor_fonte_profissional = $_POST['cor_fonte_profissional'];
$cor_fundo_profissional = $_POST['cor_fundo_profissional'];
$cor_fundo_caixa_pesquisa = $_POST['cor_fundo_caixa_pesquisa'];


$cor_sombra = $_POST['cor_sombra'];
$opacidade = $_POST['opacidade'];
$efeito = $_POST['efeito'];
$desloc_horizontal = $_POST['desloc_horizontal'];
$desloc_vertical = $_POST['desloc_vertical'];

$cor_agendado = $_POST['cor_agendado'];
$cor_confirmado = $_POST['cor_confirmado'];
$cor_aguardando = $_POST['cor_aguardando'];
$cor_nao_realizado = $_POST['cor_nao_realizado'];
$cor_atendimento = $_POST['cor_atendimento'];
$cor_concluido = $_POST['cor_concluido'];
$cor_finalizado= $_POST['cor_finalizado'];

$cor_faltou = $_POST['cor_faltou'];
$cor_cancelado = $_POST['cor_cancelado']; // Corrigido de 'cor_calcelado' para 'cor_cancelado'



$cor_n_atende = $_POST['cor_n_atende'];



$cor_bloqueio = $_POST['cor_bloqueio'];


$cor_borda_bloqueio = $_POST['cor_borda_bloqueio'];
$size_borda_bloqueio= $_POST['size_borda_bloqueio']; // Ajustado para coletar o tamanho da borda
$opacicidade_bloqueio = $_POST['opacicidade_bloqueio'];
$cor_fonte_bloqueio = $_POST['cor_fonte_bloqueio'];

	


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
                cor_sombra = :cor_sombra, 
                opacidade = :opacidade, 
                efeito = :efeito, 
                desloc_horizontal = :desloc_horizontal, 
                desloc_vertical = :desloc_vertical, 
                cor_agendado = :cor_agendado, 
                cor_confirmado = :cor_confirmado, 
                cor_aguardando = :cor_aguardando, 
                cor_nao_realizado = :cor_nao_realizado,
                cor_atendimento = :cor_atendimento, 
                cor_finalizado = :cor_finalizado, 
                cor_concluido = :cor_concluido, 
                cor_faltou = :cor_faltou, 
                cor_cancelado = :cor_cancelado, 
                cor_bloqueio = :cor_bloqueio, 
                cor_n_atende = :cor_n_atende, 
                cor_borda_bloqueio = :cor_borda_bloqueio, 
                size_borda_bloqueio = :size_borda_bloqueio,
                cor_fundo_agenda = :cor_fundo_agenda,
                cor_fonte_horario = :cor_fonte_horario,
                cor_fonte_celula = :cor_fonte_celula,
                cor_celula_selecionada = :cor_celula_selecionada,
                cor_linha_horizontal = :cor_linha_horizontal,
                cor_linha_vertical = :cor_linha_vertical,
                cor_fonte_profissional = :cor_fonte_profissional,
                cor_fundo_profissional = :cor_fundo_profissional, 
                cor_fundo_caixa_pesquisa = :cor_fundo_caixa_pesquisa,
                opacicidade_bloqueio = :opacicidade_bloqueio,
                cor_fonte_bloqueio = :cor_fonte_bloqueio
              WHERE configuracao_nome = :configuracao_nome";
              $stmt = $pdo->prepare($query);
} else {
    // INSERT se a configu ração não existe
    
   
    $query = "INSERT INTO personalizacao_agenda (configuracao_nome, cor_sombra, opacidade, efeito, desloc_horizontal, desloc_vertical, cor_agendado, cor_confirmado, cor_aguardando, cor_nao_realizado, cor_atendimento, cor_finalizado, cor_concluido, cor_faltou, cor_cancelado, cor_bloqueio, cor_n_atende, cor_borda_bloqueio, size_borda_bloqueio, cor_fundo_agenda, cor_fonte_horario, cor_fonte_celula, cor_celula_selecionada, cor_linha_horizontal, cor_linha_vertical, cor_fonte_profissional, cor_fundo_profissional, cor_fundo_caixa_pesquisa, opacicidade_bloqueio, cor_fonte_bloqueio) 
              VALUES (:configuracao_nome, :cor_sombra, :opacidade, :efeito, :desloc_horizontal, :desloc_vertical, :cor_agendado, :cor_confirmado, :cor_aguardando, :cor_nao_realizado, :cor_atendimento, :cor_finalizado, :cor_concluido, :cor_faltou, :cor_cancelado, :cor_bloqueio, :cor_n_atende, :cor_borda_bloqueio, :size_borda_bloqueio, :cor_fundo_agenda, :cor_fonte_horario, :cor_fonte_celula, :cor_celula_selecionada, :cor_linha_horizontal, :cor_linha_vertical, :cor_fonte_profissional, :cor_fundo_profissional, :cor_fundo_caixa_pesquisa, :opacicidade_bloqueio, :cor_fonte_bloqueio)";
              

$stmt = $pdo->prepare($query);
}
// Bind dos valores comuns para INSERT e UPDATE
$stmt->bindParam(':configuracao_nome', $configuracao_nome);
$stmt->bindParam(':cor_sombra', $cor_sombra);
$stmt->bindParam(':opacidade', $_POST['opacidade']);
$stmt->bindParam(':efeito', $_POST['efeito']);
$stmt->bindParam(':desloc_horizontal', $_POST['desloc_horizontal']);
$stmt->bindParam(':desloc_vertical', $_POST['desloc_vertical']);
$stmt->bindParam(':cor_agendado', $cor_agendado);
$stmt->bindParam(':cor_confirmado', $_POST['cor_confirmado']);
$stmt->bindParam(':cor_aguardando', $_POST['cor_aguardando']);
$stmt->bindParam(':cor_nao_realizado', $_POST['cor_nao_realizado']);
$stmt->bindParam(':cor_atendimento', $_POST['cor_atendimento']);
$stmt->bindParam(':cor_finalizado', $cor_finalizado);
$stmt->bindParam(':cor_concluido', $cor_concluido);
$stmt->bindParam(':cor_faltou', $_POST['cor_faltou']);
$stmt->bindParam(':cor_cancelado', $_POST['cor_cancelado']);
$stmt->bindParam(':cor_bloqueio', $_POST['cor_bloqueio']);
$stmt->bindParam(':cor_n_atende', $_POST['cor_n_atende']);
$stmt->bindParam(':cor_borda_bloqueio', $cor_borda_bloqueio);
$stmt->bindParam(':size_borda_bloqueio', $size_borda_bloqueio);
$stmt->bindParam(':cor_fundo_agenda', $cor_fundo_agenda);
$stmt->bindParam(':cor_fonte_horario', $cor_fonte_horario);
$stmt->bindParam(':cor_fonte_celula', $cor_fonte_celula);
$stmt->bindParam(':cor_celula_selecionada', $cor_celula_selecionada);
$stmt->bindParam(':cor_linha_horizontal', $cor_linha_horizontal);
$stmt->bindParam(':cor_linha_vertical', $cor_linha_vertical);
$stmt->bindParam(':cor_fonte_profissional', $cor_fonte_profissional);
$stmt->bindParam(':cor_fundo_profissional', $cor_fundo_profissional);
$stmt->bindParam(':cor_fundo_caixa_pesquisa', $cor_fundo_caixa_pesquisa);
$stmt->bindParam(':opacicidade_bloqueio', $opacicidade_bloqueio);
$stmt->bindParam(':cor_fonte_bloqueio', $cor_fonte_bloqueio);

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