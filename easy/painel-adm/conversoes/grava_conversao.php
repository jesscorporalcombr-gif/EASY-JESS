<?php
session_start();
require_once '../../conexao.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // 1) Inicia transação
    $pdo->beginTransaction();

    // 2) Captura POST básicos
    $id_comum         = intval($_POST['id_comum']           ?? 0);
    $id_cliente       = intval($_POST['id-cliente']             ?? 0);
    $nome_cliente     = trim($_POST['nome-cliente']             ?? '');
    $cpf_cliente      = trim($_POST['cpf-cliente']              ?? '');
    $celular_cliente  = trim($_POST['celular-cliente']          ?? '');
    $email_cliente    = trim($_POST['email-cliente']            ?? '');
    $informacoes      = trim($_POST['informacoes']              ?? '');
    $data_hoje        = date('Y-m-d');
    $valor_conversao  = floatval($_POST['valor-total-conversao-calc'] ?? 0);

    // 3) Usuário e timestamps
    $idUser   = $_SESSION['id_usuario']   ?? null;
    $userName = $_SESSION['nome_usuario'] ?? null;
    $now      = date('Y-m-d H:i:s');

            $stmtDisp = $pdo->prepare("
            SELECT id_item, 
                (quantidade 
                    - COALESCE(convertidos,0) 
                    - COALESCE(realizados,0) 
                    - COALESCE(transferidos,0) 
                    - COALESCE(descontados,0)
                ) AS disponiveis
            FROM venda_itens
            WHERE id_cliente = :id_cliente
            AND tipo_item  = 'servico'
            AND (data_validade >= CURDATE() OR data_validade IS NULL OR data_validade = '')
            HAVING disponiveis > 0
        ");
        $stmtDisp->execute([':id_cliente' => $id_cliente]);
        $listaDisp = $stmtDisp->fetchAll(PDO::FETCH_KEY_PAIR); 
        // agora $listaDisp é um mapa [ id_item => disponiveis ]

        // valida antes de abrir transação
        foreach ($_POST['id_servico'] as $i => $srv) {
            $qtd = intval($_POST['qtd-transf'][$i] ?? 0);
            if ($qtd < 1) continue;
            if (!isset($listaDisp[$srv])) {
                echo json_encode([
                'success' => false,
                'message' => "Serviço ID {$srv} não disponível."
                ]);
                exit;
            }
            if ($listaDisp[$srv] < $qtd) {
                echo json_encode([
                'success' => false,
                'message' => "Só há {$listaDisp[$srv]} unidades do serviço ID {$srv}, mas pediu {$qtd}."
                ]);
                exit;
            }
        }

    // 4) Calcular delta para clientes.saldo
    if ($id_comum > 0) {
        $stmtOld = $pdo->prepare("SELECT saldo FROM venda WHERE id = :id");
        $stmtOld->execute([':id'=>$id_comum]);
        $oldSaldo = floatval($stmtOld->fetchColumn());
        $delta = $valor_conversao - $oldSaldo;
    } else {
        $delta = $valor_conversao;
    }

    // 5) INSERT/UPDATE em venda
    if ($id_comum > 0) {
        $sql = "UPDATE venda SET
            tipo_venda = 'conversao',
            id_cliente = :id_cliente,
            cliente    = :cliente,
            cpf        = :cpf,
            celular    = :celular,
            email      = :email,
            informacoes= :infos,
            valor_final= -:vf,
            saldo      = :vs,
            user_alteracao    = :usr_alt,
            id_user_alteracao = :uid_alt,
            dataHora_alteracao= :dh_alt
          WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_cliente'=> $id_cliente,
            ':cliente'   => $nome_cliente,
            ':cpf'       => $cpf_cliente,
            ':celular'   => $celular_cliente,
            ':email'     => $email_cliente,
            ':infos'     => $informacoes,
            ':vf'        => $valor_conversao,
            ':vs'        => $valor_conversao,
            ':usr_alt'   => $userName,
            ':uid_alt'   => $idUser,
            ':dh_alt'    => $now,
            ':id'        => $id_comum
        ]);
    } else {
        $sql = "INSERT INTO venda
          (tipo_venda, data_venda, id_cliente, cliente,
           cpf, celular, email, informacoes,
           valor_final, saldo,
           user_criacao, id_user_criacao, dataHora_criacao)
          VALUES
          ('conversao', :data, :id_cliente, :cliente,
           :cpf, :celular, :email, :infos,
           -:vf, :vs,
           :usr_cr, :uid_cr, :dh_cr)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':data'      => $data_hoje,
            ':id_cliente'=> $id_cliente,
            ':cliente'   => $nome_cliente,
            ':cpf'       => $cpf_cliente,
            ':celular'   => $celular_cliente,
            ':email'     => $email_cliente,
            ':infos'     => $informacoes,
            ':vf'        => $valor_conversao,
            ':vs'        => $valor_conversao,
            ':usr_cr'    => $userName,
            ':uid_cr'    => $idUser,
            ':dh_cr'     => $now
        ]);
        $id_comum = $pdo->lastInsertId();
    }

    // 6) Atualiza saldo do cliente
    $stmtUpdCli = $pdo->prepare("UPDATE clientes SET saldo = saldo + :delta WHERE id = :id");
    $stmtUpdCli->execute([':delta'=>$delta,':id'=>$id_cliente]);

    // 7) Montagem de itens de conversão
    $dataArr    = $_POST['data-venda']             ?? [];
    $qtdArr     = $_POST['qtd-convert']            ?? [];
    $origArr    = $_POST['id_venda_item']          ?? [];
    $convArr    = $_POST['id-conversao']           ?? [];
    $unitArr    = $_POST['valor-unitario-calc']    ?? [];
    $totArr     = $_POST['valor-total-item-calc']  ?? [];
    $svcIdArr   = $_POST['id_servico']             ?? [];
    $svcArr     = $_POST['servico']                ?? [];
    $idVendaArr  = $_POST['id_venda']                ?? [];




    $items = [];
    foreach ($qtdArr as $i => $v) {
        $q = intval($v);
        if ($q < 1) continue;
        $items[] = [
            'id_conv'    => intval($convArr[$i]   ?? 0),
            'id_venda'  => intval($idVendaArr[$i] ?? 0),
            'data'       => $dataArr[$i]         ?? $data_hoje,
            'qtd'        => $q,
            'orig'       => intval($origArr[$i]   ?? 0),
            'unit'       => floatval($unitArr[$i] ?? 0),
            'tot'        => floatval($totArr[$i]  ?? 0),
            'svc_id'     => intval($svcIdArr[$i]  ?? 0),
            'svc'        => $svcArr[$i]          ?? ''
        ];
    }



            if (count($items)<1) {
                echo json_encode([
                'success' => false,
                'message' => "Nenhum Serviço para converter!"
                ]);
                exit;
            }




    // 8) Fetch conversões antigas
    $stmtOld = $pdo->prepare("SELECT id, quantidade, id_item_servico_origem FROM venda_conversoes WHERE id_comum = :com");
    $stmtOld->execute([':com'=>$id_comum]);
    $olds = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

    $oldIds = array_column($olds,'id');
    $newIds = array_filter(array_column($items,'id_conv'));
    $toDel  = array_diff($oldIds,$newIds);

    // 9) Preparação de statements auxiliares
    $sFetchQty = $pdo->prepare("SELECT quantidade FROM venda_conversoes WHERE id=:id");
    $sFetchItm = $pdo->prepare("SELECT convertidos FROM venda_itens WHERE id=:id");
    $sUpdItm   = $pdo->prepare("UPDATE venda_itens SET convertidos=:c WHERE id=:id");
    $sDelConv  = $pdo->prepare("DELETE FROM venda_conversoes WHERE id=:id");

    $sInsConv = $pdo->prepare(
        "INSERT INTO venda_conversoes
         (tipo, data, id_comum,
          id_venda_origem, id_venda_destino,
          id_cliente_origem, id_item_servico_origem,
          id_cliente_destino,
          id_servico, servico, valor_un, quantidade,
          user_criacao, id_user_criacao, dataHora_criacao)
         VALUES
         ('conversao',:data,:com,
          :orig_venda,:dest_venda,
          :cli_ori,:svc_ori,
          :cli_dst,
          :svc_id,:svc,:vu,:qtd,
          :usr,:uid,:dh)"
    );
    $sUpdConv = $pdo->prepare(
        "UPDATE venda_conversoes SET
           quantidade=:qtd,valor_un=:vu,
           user_alteracao=:usr,id_user_alteracao=:uid,dataHora_alteracao=:dh
         WHERE id=:id"
    );

    // 10) Excluir conversões removidas
    foreach ($toDel as $did) {
        $sFetchQty->execute([':id'=>$did]); $oq = intval($sFetchQty->fetchColumn());
        $origRec = array_values(array_filter($olds,function($o)use($did){return $o['id']==$did;}))[0];
        $sFetchItm->execute([':id'=>$origRec['id_item_servico_origem']]); $cv = intval($sFetchItm->fetchColumn());
        $sUpdItm->execute([':c'=>$cv-$oq,':id'=>$origRec['id_item_servico_origem']]);
        $sDelConv->execute([':id'=>$did]);
    }

    // 11) Inserir/atualizar atuais
    foreach ($items as $it) {
        $sFetchItm->execute([':id'=>$it['orig']]); $cv=intval($sFetchItm->fetchColumn());
        if ($it['id_conv']>0) {
            $sFetchQty->execute([':id'=>$it['id_conv']]); $oq=intval($sFetchQty->fetchColumn());
            $newCv = $cv - $oq + $it['qtd'];
            $sUpdItm->execute([':c'=>$newCv,':id'=>$it['orig']]);
            $sUpdConv->execute([
                ':qtd'=>$it['qtd'],':vu'=>$it['unit'],
                ':usr'=>$userName,':uid'=>$idUser,':dh'=>$now,
                ':id'=>$it['id_conv']
            ]);
        } else {
            $sUpdItm->execute([':c'=>$cv+$it['qtd'],':id'=>$it['orig']]);
            $sInsConv->execute([
                ':data'=> $it['data'],
                ':com' => $id_comum,
                ':orig_venda'=> $it['id_venda'],
                ':dest_venda'=> $id_comum,
                ':cli_ori'=> $id_cliente,
                ':svc_ori'=> $it['orig'],
                ':cli_dst'=> $id_cliente,
                ':svc_id'=> $it['svc_id'],
                ':svc'   => $it['svc'],
                ':vu'    => $it['unit'],
                ':qtd'   => $it['qtd'],
                ':usr'   => $userName,
                ':uid'   => $idUser,
                ':dh'    => $now
            ]);
        }
    }

    // 12) Commit e retorno
    $pdo->commit();
    echo json_encode([
        'success'=>true,
        'id_comum'=>$id_comum,
        'message'=>'Serviços convertidos com sucesso'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
