<?php
session_start();
require_once '../../conexao.php';

// Configura PDO para lançar exceções
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id_comum = $_POST['id-comum']??'';

if(!$id_comum){
    try {
        // 1) Inicia transação
       

        // 2) Captura dos dados do POST
        $id_cliente_origem    = intval($_POST['id-cliente'] ?? 0);
        $id_cliente_destino   = intval($_POST['id-cliente-destino'] ?? 0);
        $usuario              = $_SESSION['nome_usuario']  ?? null;
        $id_usuario           = $_SESSION['id_usuario']    ?? null;
        $dataHora             = date('Y-m-d H:i:s');
        $data_venda = date('Y-m-d');

        // Arrays de itens
        $idsVendaItem         = $_POST['id_venda_item']          ?? [];
        $qtdTransfArr         = $_POST['qtd-transf']            ?? [];
        $valorUnitArr         = $_POST['valor-unitario-calc']   ?? [];
        $servicoArr           = $_POST['servico']               ?? [];
        $idServicoArr         = $_POST['id_servico']            ?? [];

       //verificar se o cliente realmente tem os serviços solicitados.
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
        $stmtDisp->execute([':id_cliente' => $id_cliente_origem]);
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
        // Fim da consulta 



        // Preparação de statements
        $pdo->beginTransaction();

        $stmtFetchOrig = $pdo->prepare("SELECT * FROM venda_itens WHERE id = :id");
        $stmtUpdateOrig = $pdo->prepare("UPDATE venda_itens SET transferidos = transferidos + :qtd WHERE id = :id");
        $stmtInsertItem = $pdo->prepare(
            "INSERT INTO venda_itens
            (id_venda, tipo_item, venda, tipo_venda, data_venda, item, id_item,
            precoUn, precoUn_efetivo, perc_desconto, margem,
            data_validade, nota_fiscal,
            id_cliente, id_cliente_origem, quantidade,
            preco_total, valor_desconto,
            custo_1, custo_2, custo_3, custo_4,
            custo_5, custo_6, custo_7, custo_8,
            custo_total, lucro_bruto, lucro_liquido)
            VALUES
            (:id_venda, :tipo_item, '1', 'transferencia', :data_venda, :item, :id_item,
            :precoUn, :precoUn_efetivo, :perc_desconto, :margem,
            :data_validade, :nota_fiscal,
            :id_cliente_destino, :id_cliente_origem, :qtd,
            :preco_total, :valor_desconto,
            :custo_1, :custo_2, :custo_3, :custo_4,
            :custo_5, :custo_6, :custo_7, :custo_8,
            :custo_total, :lucro_bruto, :lucro_liquido)"
        );
        $stmtInsertConv = $pdo->prepare(
            "INSERT INTO venda_conversoes
            (tipo, id_comum, id_venda_origem, id_cliente_origem, id_item_servico_origem,
            id_cliente_destino, id_item_servico_destino,
            id_servico, servico, valor_un, quantidade,
            user_criacao, id_user_criacao, dataHora_criacao, data)
            VALUES
            ('transferencia servicos', :id_comum, :id_venda_origem, :id_cliente_origem, :id_item_servico_origem,
            :id_cliente_destino, :id_item_servico_destino,
            :id_servico, :servico, :valor_un, :quantidade,
            :usuario_criacao, :id_usuario_criacao, :data_hora_criacao, :data_venda)"
        );

        // 3) Processa cada item com quantidade de transferência > 0

        $firstConvId = null;
            $updConvId = $pdo->prepare(
                "UPDATE venda_conversoes 
                SET id_comum = :idc 
                WHERE id = :id"
        );
        
        foreach ($idsVendaItem as $i => $origId) {
            $qtd = intval($qtdTransfArr[$i] ?? 0);
            if ($qtd < 1) continue; // pula itens sem transferência

            // a) obtém registro original
            $stmtFetchOrig->execute([':id' => $origId]);
            $orig = $stmtFetchOrig->fetch(PDO::FETCH_ASSOC);
            if (!$orig) continue;

            // b) atualiza campo transferidos do registro pai
            $stmtUpdateOrig->execute([
                ':qtd' => $qtd,
                ':id'  => $origId
            ]);

            // c) calcula valores proporcionais para o filho
            $prop = $qtd / max($orig['quantidade'], 1);
            // custos proporcionais
            $custos = [];
            $somaCustos = 0;
            for ($j = 1; $j <= 8; $j++) {
                $custos[$j] = $orig["custo_{$j}"] * $prop;
                $somaCustos += $custos[$j];
            }
            $preco_total    = $orig['precoUn_efetivo'] * $qtd;
            $valor_desconto = $preco_total * $orig['perc_desconto'];
            $lucro_bruto    = isset($orig['lucro_bruto']) ? $orig['lucro_bruto'] * $prop : 0;
            $lucro_liquido  = isset($orig['lucro_liquido']) ? $orig['lucro_liquido'] * $prop : 0;

            // d) insere o registro "filho"
            $stmtInsertItem->execute([
                ':id_venda'            => $orig['id_venda'],
                ':data_venda'          => $data_venda,
                ':tipo_item'           => $orig['tipo_item'],
                ':item'                => $orig['item'],
                ':id_item'             => $orig['id_item'],
                ':precoUn'             => $orig['precoUn'],
                ':precoUn_efetivo'     => $orig['precoUn_efetivo'],
                ':perc_desconto'       => $orig['perc_desconto'],
                ':margem'              => $orig['margem'],
                ':data_validade'       => $orig['data_validade'],
                ':nota_fiscal'         => $orig['nota_fiscal'],
                ':id_cliente_destino'  => $id_cliente_destino,
                ':id_cliente_origem'   => $id_cliente_origem,
                ':qtd'                 => $qtd,
                ':preco_total'         => $preco_total,
                ':valor_desconto'      => $valor_desconto,
                ':custo_1'             => $custos[1],
                ':custo_2'             => $custos[2],
                ':custo_3'             => $custos[3],
                ':custo_4'             => $custos[4],
                ':custo_5'             => $custos[5],
                ':custo_6'             => $custos[6],
                ':custo_7'             => $custos[7],
                ':custo_8'             => $custos[8],
                ':custo_total'         => $somaCustos,
                ':lucro_bruto'         => $lucro_bruto,
                ':lucro_liquido'       => $lucro_liquido
            ]);
            $novoItemId = $pdo->lastInsertId();
           


            // e) registra na venda_conversoes
            $stmtInsertConv->execute([
                ':id_comum'                => $firstConvId ?? 0,
                ':id_venda_origem'         => $orig['id_venda'],
                ':id_cliente_origem'       => $id_cliente_origem,
                ':id_item_servico_origem'  => $idsVendaItem[$i],
                ':id_cliente_destino'      => $id_cliente_destino,
                ':id_item_servico_destino' => $novoItemId,
                ':id_servico'              => $idServicoArr[$i],
                ':servico'                 => $servicoArr[$i],
                ':valor_un'                => floatval($valorUnitArr[$i]),
                ':quantidade'              => $qtd,
                ':usuario_criacao'         => $usuario,
                ':id_usuario_criacao'      => $id_usuario,
                ':data_hora_criacao'       => $dataHora,
                ':data_venda'              =>$data_venda
            ]);


            //preciso de ajuda aqui, preciso pegar o primeiro id inserido em venda_conversoes e usar como id_comum de todos inserts de id venda, talvez tenha que preparar um update, mas preciso localizar cada item inserido pedir ajuda para o gpt


            // captura o ID desse insert
                $convId = $pdo->lastInsertId();

                if ($firstConvId === null) {
                    // 1) esse é o primeiro insert: define $firstConvId
                    $firstConvId = $convId;

                    // 2) ajusta o próprio registro para setar id_comum = $firstConvId
                    $updConvId->execute([
                        ':idc' => $firstConvId,
                        ':id'  => $firstConvId
                    ]);
                }


        }

        // 4) Commit e resposta
        $pdo->commit();
        echo json_encode([
            'success'         => true,
            'id_comum'        => $firstConvId,
            'message' => 'Gravado com sucesso'
        ]);
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}else{
    echo json_encode(['success' => false, 'message' => 'Não é possível alterar transferências de serviços!']);
    exit;

}
