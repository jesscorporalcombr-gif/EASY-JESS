<?php
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

$id_usuario = $_SESSION['id_usuario'];
$usuario    = $_SESSION['nome_usuario'];
$now        = date('Y-m-d H:i:s');

$items = $_POST['agendamento-jan'] ?? [];
if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Nenhum agendamento enviado']);
    exit;
}





// 1. Monta array com todos os ids de agendamento dos items
$idAgendamentos = [];
foreach ($items as $item) {
    if (!empty($item['idAgendamento'])) {
        $idAgendamentos[] = (int)$item['idAgendamento'];
    }
}

if ($idAgendamentos) {
    $placeholders = implode(',', array_fill(0, count($idAgendamentos), '?'));

    // 2. Consulta para achar qualquer atendimento proibitivo
    $sql = "
        SELECT id_agendamento 
        FROM atendimentos 
        WHERE id_agendamento IN ($placeholders)
          AND (
                status = 'Finalizado'
             OR (
                    id_item_servico IS NOT NULL
                AND id_item_servico != 0
                AND id_item_servico != ''
                )
          )
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($idAgendamentos);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Já tem algum agendamento proibido, retorna erro e para tudo!
        echo json_encode(['success' => false, 'error' => 'Já existe atendimento finalizado ou vinculado a venda para um dos agendamentos selecionados.']);
        exit;
    }
}
// Agora pode seguir normalmente pro foreach($items as $i => $d) { ... }





try {
    $pdo->beginTransaction();
    $results = [];

    foreach ($items as $i => $d) {
        $idAg        = intval($d['idAgendamento'] ?? 0);
        $data        = $d['data'] ?? null;
        $horaIni     = $d['horaInicio'] ?? null;
        $tempo       = intval($d['tempo'] ?? 0);
        $horaFim     = $d['horaFim'] ?? null;
        $preco       = $d['preco'] ?? null;
        $idServ      = intval($d['idServico'] ?? 0);
        $servNome    = $d['servicoTexto'] ?? null;
        $idProf      = intval($d['idProfissional'] ?? 0);
        $profNome    = $d['profissionalTexto'] ?? null;
        $idCliente   = intval($d['idCliente'] ?? 0);
        $obs         = $d['observacoes'] ?? null;

        // Consulta dados do cliente
        $sqlC = "SELECT nome, cpf, celular, email FROM clientes WHERE id = ?";
        $stmtC = $pdo->prepare($sqlC);
        $stmtC->execute([$idCliente]);
        $cli = $stmtC->fetch(PDO::FETCH_ASSOC) ?: [];
        $cliNome = $cli['nome'] ?? null;
        $cliCpf  = $cli['cpf'] ?? null;
        $cliTel  = $cli['celular'] ?? null;
        $cliEmail= $cli['email'] ?? null;

        // Consulta dados do profissional
        $sqlP = "SELECT nome FROM colaboradores_cadastros WHERE id = ?";
        $stmtP = $pdo->prepare($sqlP);
        $stmtP->execute([$idProf]);
        $prof = $stmtP->fetchColumn();
        $profNomeDB = $prof ?: $profNome;

        // Processa imagem se existir
        $imagemPath = null;
        if (!empty($_FILES['agendamento-jan']['tmp_name'][$i]['imagem'])) {
            $tmp = $_FILES['agendamento-jan']['tmp_name'][$i]['imagem'];
            $ext = pathinfo($_FILES['agendamento-jan']['name'][$i]['imagem'], PATHINFO_EXTENSION);
            $fileName = uniqid('ag_') . ".{$ext}";
            move_uploaded_file($tmp, __DIR__ . "/uploads/{$fileName}");
            $imagemPath = $fileName;
        }

        // Define SQL
        if ($idAg > 0) {

            // --- Sincronizar dados no atendimento, se existir ---
            $stmtAt = $pdo->prepare("SELECT id FROM atendimentos WHERE id_agendamento = ?");
            $stmtAt->execute([$idAg]);
            $idAtendimento = $stmtAt->fetchColumn();

            if ($idAtendimento) {
                // Prepare os campos para atualização
                $stmtUp = $pdo->prepare("
                    UPDATE atendimentos
                    SET 
                        data = :data,
                        hora_inicio = :hora_inicio,
                        hora_fim = :hora_fim,
                        profissional_1 = :profissional_1,
                        id_profissional_1 = :id_profissional_1,
                        id_servico = :id_servico,
                        servico = :servico,
                        tempo_min = :tempo_min,
                        id_item_servico = 0,
                        id_user_alteracao = :id_user_alteracao,
                        user_alteracao = :user_alteracao,
                        data_alteracao = :data_alteracao
                    WHERE id = :id_atendimento
                ");
                $stmtUp->execute([
                    ':data'                => $data,                  // data do agendamento
                    ':hora_inicio'         => $horaIni,           // hora de início do agendamento
                    ':hora_fim'            => $horaFim,              // hora de fim do agendamento
                    ':profissional_1'      => $profNome,        // nome do profissional
                    ':id_profissional_1'   => $idProf,     // id do profissional
                    ':id_servico'          => $idServ,            // id do serviço
                    ':servico'             => $servNome,               // nome do serviço
                    ':tempo_min'           => $tempo,             // tempo do serviço
                    ':id_user_alteracao'   => $id_usuario ?? null,
                    ':user_alteracao'      => $usuario ?? null,
                    ':data_alteracao'      => date('Y-m-d H:i:s'),
                    ':id_atendimento'      => $idAtendimento
                ]);
            }

            $sql = "UPDATE agendamentos SET
                      data = :data,
                      hora = :hora,
                      tempo_min = :tempo,
                      preco = :preco,
                      id_servico = :idServ,
                      servico = :servNome,
                      id_profissional_1 = :idProf,
                      profissional_1 = :profNome,
                      id_cliente = :idCli,
                      nome_cliente = :cliNome,
                      cpf_cliente = :cliCpf,
                      telefone_cliente = :cliTel,
                      email_cliente = :cliEmail,
                      observacoes = :obs,
                      user_alteracao = :userAlt,
                      id_user_alteracao = :userAltId,
                      data_alteracao = :alteracao
                    WHERE id = :idAg";
        } else {
            $sql = "INSERT INTO agendamentos (
                      data, hora, tempo_min, preco,
                      id_servico, servico,
                      id_profissional_1, profissional_1,
                      id_cliente, nome_cliente,
                      cpf_cliente, telefone_cliente, email_cliente,
                      observacoes,
                      origem_agendamento, status,
                      user_criacao, id_user_criacao, data_criacao
                    ) VALUES (
                      :data, :hora, :tempo, :preco,
                      :idServ, :servNome,
                      :idProf, :profNome,
                      :idCli, :cliNome,
                      :cliCpf, :cliTel, :cliEmail,
                      :obs, 
                      'interno', 'Agendado',
                      :userCri, :userCriId, :criacao
                    )";
        }

        // Prepara e bind
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':data', $data);
        $stmt->bindValue(':hora', $horaIni);
        $stmt->bindValue(':tempo', $tempo, PDO::PARAM_INT);
        $stmt->bindValue(':preco', $preco);
        $stmt->bindValue(':idServ', $idServ, PDO::PARAM_INT);
        $stmt->bindValue(':servNome', $servNome);
        $stmt->bindValue(':idProf', $idProf, PDO::PARAM_INT);
        $stmt->bindValue(':profNome', $profNomeDB);
        $stmt->bindValue(':idCli', $idCliente, PDO::PARAM_INT);
        $stmt->bindValue(':cliNome', $cliNome);
        $stmt->bindValue(':cliCpf', $cliCpf);
        $stmt->bindValue(':cliTel', $cliTel);
        $stmt->bindValue(':cliEmail', $cliEmail);
        $stmt->bindValue(':obs', $obs);
        //$stmt->bindValue(':img', $imagemPath);

        if ($idAg > 0) {
            $stmt->bindValue(':idAg', $idAg, PDO::PARAM_INT);
            $stmt->bindValue(':userAlt', $usuario);
            $stmt->bindValue(':userAltId', $id_usuario, PDO::PARAM_INT);
            $stmt->bindValue(':alteracao', $now);
        } else {
            $stmt->bindValue(':userCri', $usuario);
            $stmt->bindValue(':userCriId', $id_usuario, PDO::PARAM_INT);
            $stmt->bindValue(':criacao', $now);
        }

        $ok = $stmt->execute();
        $results[] = ['index' => $i, 'action' => $idAg>0 ? 'update' : 'insert', 'success' => $ok];
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'results' => $results]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}