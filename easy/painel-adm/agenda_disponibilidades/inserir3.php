<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../conexao.php");

// Garante que a sessão está ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Validação simples dos parâmetros esperados
    if (!isset($_POST['id_prof3'], $_POST['id-contrato_aba3'], $_POST['servico']) || !is_array($_POST['servico'])) {
        throw new Exception('Dados incompletos enviados.');
    }

    $id_prof3  = $_POST['id_prof3'];
    $id_contr3 = $_POST['id-contrato_aba3'];
    $servicos  = $_POST['servico'];

    foreach ($servicos as $id_servico => $dados) {

        // Busca se já existe essa combinação
        $stmtVerifica = $pdo->prepare("
            SELECT COUNT(*) 
            FROM servicos_profissional 
            WHERE id_profissional = :id_profissional 
              AND id_servico = :id_servico
              AND id_contrato = :id_contrato
        ");
        $stmtVerifica->execute([
            ':id_profissional' => $id_prof3,
            ':id_servico'      => $id_servico,
            ':id_contrato'     => $id_contr3,
        ]);
        $existe = $stmtVerifica->fetchColumn() > 0;

        // Prepara os valores
        $tempo             = $dados['tempo'] ?? null;
        $preco             = $dados['venda'] ?? null;
        $comissao          = $dados['comissao'] ?? null;
        $agendamento_online= isset($dados['ag_online']) ? 1 : 0;
        $executa           = isset($dados['executa'])   ? 1 : 0;

        if ($existe) {
            // Atualiza o registro existente
            $stmt = $pdo->prepare("
                UPDATE servicos_profissional 
                SET tempo = :tempo,
                    preco = :preco,
                    comissao = :comissao,
                    agendamento_online = :agendamento_online,
                    executa = :executa
                WHERE id_contrato = :id_contrato
                  AND id_profissional = :id_profissional
                  AND id_servico = :id_servico
            ");
        } else {
            // Insere novo registro
            $stmt = $pdo->prepare("
                INSERT INTO servicos_profissional 
                (id_contrato, id_profissional, id_servico, tempo, preco, comissao, agendamento_online, executa)
                VALUES
                (:id_contrato, :id_profissional, :id_servico, :tempo, :preco, :comissao, :agendamento_online, :executa)
            ");
        }

        // Executa a query (INSERT ou UPDATE)
        $stmt->execute([
            ':id_contrato'        => $id_contr3,
            ':id_profissional'    => $id_prof3,
            ':id_servico'         => $id_servico,
            ':tempo'              => $tempo,
            ':preco'              => $preco,
            ':comissao'           => $comissao,
            ':agendamento_online' => $agendamento_online,
            ':executa'            => $executa,
        ]);
    }

    echo 'Salvo com Sucesso!';

} catch (Exception $e) {
    // Captura erro genérico (além de PDO)
    http_response_code(500);
    echo "Erro ao atualizar os dados no banco: " . $e->getMessage();
}
?>
