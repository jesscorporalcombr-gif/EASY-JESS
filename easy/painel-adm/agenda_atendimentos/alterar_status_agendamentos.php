<?php
// consultar a tabela agendamentos onde $id_agendamento == 'id'
//obter data, profissional_1, id_profissional_1, id_cliente, id_servico, servico, status, 

// consultar a tabela atendimentos onde id_agendamento == $id_agendamento, se houver registro,obter  id, status, hora_inicio, hora_fim, id_item_servico, id_venda e será um update em atendimentos.
    
    // se houver registro (Updates em agendamentos e possiveis updates em venda_itens)
        //se status == 'Finalizado' e ($status == 'Em Atendimento' ou $status=='Atendimento Concluido) "quer dizer que o status já estava finalizado e agora, o usuario, está reabrindo", então: 
            //localizar na tabela venda_itens onde id = atendimentos['id_item_servico'], e atualizar realizados para realizados -1.
            //$id_item_servico ='', $id_venda ='';
            //se $status ='Em atendimento', fazer update em atendimentos onde id_agendamento ==$id_agendamento então, id_item_servico ='', id_venda='', hora_fim='', hora_inicio=$hora_inicio, status = 'Em Atendimento
            //se $status ='Atendimento Concluido', fazer update em atendimentos onde id_agendamento ==$id_agendamento então, id_item_servico ='', id_venda='', hora_fim= $hora_fim, hora_inicio=$hora_inicio, status = 'Atendimento Concluido'.
            // demais updates em atendimentos conforme obtidos de agendamentos (profissional_1, id_profissional_1, id_cliente, id_servico, servico), mesmos nomes de campos.
        //se $status=='Finalizado' e (status == 'Em Atendimento' ou status=='Atendimento Concluido'), então : 
            //consultar a tabela venda_itens, onde id_cliente = id_cliente e id_servico== id_servico e (quantidade - realizados - convertidos - transferidos - descontados > 0) e (data_validade > (data) ou data_validade vazio ou nulo)
            // se não encontrar registros que atendam, retornar, sem vendas para o cliente.
            // se encontrar, obter o id do primeiro registro($id_item_servico) e respectivo id_venda, com o menor numero. e fazer update em realizados, +1.
                ////$id_item_servico =id , $id_venda =id_venda;

        // se $status=="Em Atendimento" e status!='Finalizado', update em atendimentos onde $id_agendamento == id_agendamento, atualizar todos os campos obtidos de agendamentos(profissional_1, id_profissional_1, id_cliente, id_servico, servico), hora_inicio = $hora_inicio, status="Em Atendimento"
        // se $status=="Atendimento Concluido" e status!='Finalizado', update em atendimentos onde $id_agendamento == id_agendamento, atualizar todos os campos obtidos de agendamentos(profissional_1, id_profissional_1, id_cliente, id_servico, servico), hora_inicio = $hora_inicio, hoda_fim = $hora_fim, status="Atendimento Concluido"
    // se não houver registros (Insert em atendimentos e possivel update em venda_itens)
        //se status=='Finalizado':
           //consultar a tabela venda_itens, onde id_cliente == id_cliente e id_servico== id_servico e (quantidade - realizados - convertidos - transferidos - descontados > 0) e (data_validade > (data) ou data_validade vazio ou nulo)
            // se não encontrar registros que atendam: retornar, sem vendas para o cliente.
            // se encontrar: obter o id do primeiro registro(gravar variavel) e respectivo id_venda, com o id de menor numero. e fazer update em realizados, +1.
            //$id_item_servico =id , $id_venda =id_venda;
            // hora_inidio = $hora inicio, hora_fim=$hora_fim, status = 'Finalizado', id_item_servico = $id_item_servico, id_venda = $id_venda;
        //se status=='Em Atendimento': hora_inidio = $hora inicio, hora_fim='', 
        //se status=='Atendimento Concluido' ou status =='Finalizado': hora_inidio = $hora inicio, hora_fim=$hora_fim,
        //demais campos (data, profissional_1, id_profissional_1, id_cliente, id_servico, servico) conforme agendamentos. 

        //consultar o id  gerado no insert e  $id_atendimento ='id gerado no insert'


//atualizar  a tabela agendamentos onde id==$id_agendamento: status=$status, id_atendimento=$id_atendimento, id_item_servico=$id_item_servico, id_venda=$id_venda,

echo 'chegou aqui';
require_once("../../conexao.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$id_agendamento = intval($_POST['id_agendamento']);
$status = trim($_POST['status']); // 'Em Atendimento', 'Atendimento Concluido' ou 'Finalizado'

$texto_prontuario = $_POST['texto_prontuario'];




function servicos_sem_credito_do_cliente($pdo, $id_agendamento) {
    // 1. Descobrir cliente e data do agendamento-base
    $stmt = $pdo->prepare("SELECT id_cliente, data FROM agendamentos WHERE id = ?");
    $stmt->execute([$id_agendamento]);
    $ag = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$ag) return [];

    $id_cliente = $ag['id_cliente'];
    $dataAgenda = $ag['data'];

    // 2. Buscar todos os agendamentos do cliente neste dia
    $stmt = $pdo->prepare("SELECT id, id_servico, preco, status FROM agendamentos WHERE id_cliente = ? AND data = ?");
    $stmt->execute([$id_cliente, $dataAgenda]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Coletar serviços únicos
    $ids_clientes_servicos = [];
    foreach ($agendamentos as $ag) {
        if($ag['status'] != 'Finalizado' || $ag['status'] != 'Cancelado' || $ag['status'] != 'Faltou' || $ag['status'] != 'NRealizado'){
            $key = "{$id_cliente}-{$ag['id_servico']}";
            $ids_clientes_servicos[$key] = [
                'id_cliente' => $id_cliente,
                'id_servico' => $ag['id_servico']
            ];
        }
    }

    // 4. Consulta venda_itens (primeira consulta)
    $saldo_vendas_map = [];
    if (!empty($ids_clientes_servicos)) {
        $placeholders_cliente_servico = [];
        $params_cliente_servico = [];
        foreach ($ids_clientes_servicos as $comb) {
            $placeholders_cliente_servico[] = '(id_cliente = ? AND id_item = ?)';
            $params_cliente_servico[] = $comb['id_cliente'];
            $params_cliente_servico[] = $comb['id_servico'];
        }
        $sql_vendas = "
            SELECT id_cliente, id_item, 
                SUM(quantidade - realizados - transferidos - convertidos - descontados) AS saldo
            FROM venda_itens
            WHERE venda = 1
            AND tipo_item = 'servico'
            AND (quantidade - realizados - transferidos - convertidos - descontados) > 0
            AND (data_validade IS NULL OR data_validade = '' OR data_validade >= ?)
            AND (" . implode(' OR ', $placeholders_cliente_servico) . ")
            GROUP BY id_cliente, id_item
        ";
        $stmt_vendas = $pdo->prepare($sql_vendas);
        $stmt_vendas->execute(array_merge([$dataAgenda], $params_cliente_servico));
       
        while ($row = $stmt_vendas->fetch(PDO::FETCH_ASSOC)) {
            $key = "{$row['id_cliente']}-{$row['id_item']}";
            $saldo_vendas_map[$key] = (int)$row['saldo'];
        }
    }

    // 5. Consulta agendamentos anteriores (segunda consulta)
    $sql_agendamentos_anteriores = "
        SELECT id_cliente, id_servico, data, id
        FROM agendamentos
        WHERE id_cliente = ?
          AND status IN ('Agendado', 'Confirmado', 'Em Atendimento', 'Atendimento Concluido', 'Aguardando')
    ";
    $stmt_agenda_ant = $pdo->prepare($sql_agendamentos_anteriores);
    $stmt_agenda_ant->execute([$id_cliente]);
    $agendamentos_anteriores = $stmt_agenda_ant->fetchAll(PDO::FETCH_ASSOC);
    $agendamentos_map = [];
    
    
    foreach ($agendamentos_anteriores as $ant) {
        $key = "{$ant['id_cliente']}-{$ant['id_servico']}";
        if (!isset($agendamentos_map[$key])) $agendamentos_map[$key] = [];
        $agendamentos_map[$key][] = ['data' => $ant['data'], 'id' => $ant['id']];
    }

    // 6. Avaliar saldo para cada agendamento do dia
    $servicos_sem_credito = [];

    foreach ($agendamentos as $agendamento) {
        if(
        $agendamento['status'] !='Finalizado' &&
        $agendamento['status'] != 'Cancelado' &&
        $agendamento['status'] != 'Faltou' &&
        $agendamento['status'] != 'NRealizado'
        ){
            $key = "{$id_cliente}-{$agendamento['id_servico']}";
            $contagem = 0;
            if (!empty($agendamentos_map[$key])) {
                foreach ($agendamentos_map[$key] as $ant) {
                    if ($ant['data'] < $dataAgenda || 
                        ($ant['data'] == $dataAgenda && $ant['id'] < $agendamento['id'])) {
                        $contagem++;
                    }
                }
            }
            $saldo_venda = $saldo_vendas_map[$key] ?? 0;
            $quantidade_disponivel = $saldo_venda - $contagem;
            if ($saldo_venda<1){
                // Coloca saldo no retorno, independente se está sem crédito ou não
                $servicos_sem_credito[] = [
                    'id_servico' => $agendamento['id_servico'],
                    'preco'      => $agendamento['preco'],
                    'saldo'      => $saldo_venda, // <-- aqui!
                    'status'=> $agendamento['status']
                ];
            }
        }
    }

    return $servicos_sem_credito;
}













if ($status=="Atendimento"){
    $status='Em Atendimento';
}

if ($status=="Concluido"){
    $status='Atendimento Concluido';
}


$hora_inicio = $_POST['hora_inicio'] ?? null;
$hora_fim = $_POST['hora_fim'] ?? null;

$id_usuario = $_SESSION['id_usuario'] ?? null;
$nome_usuario = $_SESSION['nome_usuario'] ?? null;
$data_alteracao = date('Y-m-d H:i:s');

// Buscar dados do agendamento
$stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id = ?");
$stmt->execute([$id_agendamento]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);








if (!$agendamento) {
    die(json_encode(['erro' => 'Agendamento não encontrado']));
}

// Buscar registro prévio de atendimento
$stmt = $pdo->prepare("SELECT * FROM atendimentos WHERE id_agendamento = ?");
$stmt->execute([$id_agendamento]);
$atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

$id_item_servico = $atendimento['id_item_servico'] ?? '';
$id_venda        = $atendimento['id_venda']        ?? '';
$id_atendimento  = $atendimento['id']              ?? '';

// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
// Função para buscar item de venda disponível
function buscarItemVendaDisponivel($pdo, $id_cliente, $id_servico, $data) {
    $sql = "SELECT * FROM venda_itens 
            WHERE id_cliente = ? 
              AND id_item = ? 
              AND (quantidade - realizados - transferidos - convertidos - descontados > 0)
              AND (data_validade IS NULL OR data_validade = '' OR data_validade >= ?)
            ORDER BY id ASC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_cliente, $id_servico, $data]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

// ================================
//   LÓGICA PRINCIPAL
// ================================





try {
    $pdo->beginTransaction();

    // Atual status original para tomada de decisão
    $status_anterior = $agendamento['status'];

    // Atualizar registros existentes em atendimentos
    if ($atendimento) {

        // Caso o usuário esteja "reabrindo" um finalizado:
        if ($status_anterior == 'Finalizado' && in_array($status, ['Em Atendimento', 'Atendimento Concluido', 'Agendado', 'confirmado', 'Aguardando'])) {
            // Desfaz consumo em venda_itens
            if ($id_item_servico) {
                $pdo->prepare("UPDATE venda_itens SET realizados = realizados - 1 WHERE id = ?")
                    ->execute([$id_item_servico]);
            }
            // Zera vinculação na tabela atendimentos
            $id_item_servico = '0';
            $id_venda = '0';
        }

        // Se agora está FINALIZANDO, consome novo saldo
        if ($status == 'Finalizado' && $status_anterior != 'Finalizado') {
            
            $itemVenda = buscarItemVendaDisponivel($pdo, $agendamento['id_cliente'], $agendamento['id_servico'], $agendamento['data']);
            if (!$itemVenda) {
                
                $stmt = $pdo->prepare("SELECT valor_venda FROM servicos WHERE id = ?");
                $stmt->execute([$agendamento['id_servico']]);
                $servicoVal = $stmt->fetch(PDO::FETCH_ASSOC);
                $descontaServico=false;
                
                if ($servicoVal>0){
                    $descontaServico=true;
                }else{
                    $descontServico=false;
                }

                if ($descontaServico){
                    $pdo->rollBack();
                    $lista = servicos_sem_credito_do_cliente($pdo, $id_agendamento);
                    echo json_encode(['NVenda' => $lista]);
                    exit;
                }
            }

            if ($descontaServico){
                $id_item_servico = $itemVenda['id'];
                $id_venda = $itemVenda['id_venda'];

                // Atualiza venda_itens (realizados +1)
                $pdo->prepare("UPDATE venda_itens SET realizados = realizados + 1 WHERE id = ?")
                    ->execute([$id_item_servico]);
            }
        }

        // Atualizar tabela atendimentos
        $campos_atendimento = [
            'data'             => $agendamento['data'],
            'profissional_1'   => $agendamento['profissional_1'],
            'id_profissional_1'=> $agendamento['id_profissional_1'],
            'id_cliente'       => $agendamento['id_cliente'],
            'id_servico'       => $agendamento['id_servico'],
            'servico'          => $agendamento['servico'],
            'prontuario'       => $texto_prontuario,
            'hora_inicio'      => $hora_inicio,
            'hora_fim'         => $hora_fim,
            'status'           => $status,
            'id_item_servico'  => $id_item_servico,
            'id_venda'         => $id_venda,
            'id_user_alteracao'=> $id_usuario,
            'user_alteracao'   => $nome_usuario,
            'data_alteracao'   => $data_alteracao
        ];

        $set = implode(", ", array_map(fn($k)=>"$k = :$k", array_keys($campos_atendimento)));
        $campos_atendimento['id'] = $id_atendimento;

        $sql_update_atendimento = "UPDATE atendimentos SET $set WHERE id = :id";
        $stmt = $pdo->prepare($sql_update_atendimento);
        $stmt->execute($campos_atendimento);

    } else {
        // INSERT NOVO ATENDIMENTO
        if ($status =='Finalizado' || $status == 'Em Atendimento' || $status =="Atendimento Concluido"){
                if ($status == 'Finalizado') {
                        $itemVenda = buscarItemVendaDisponivel($pdo, $agendamento['id_cliente'], $agendamento['id_servico'], $agendamento['data']);
                    
                        if (!$itemVenda) {
                            $pdo->rollBack();
                            $lista = servicos_sem_credito_do_cliente($pdo, $id_agendamento);
                            echo json_encode(['NVenda' => $lista]);
                            exit;
                        }
                        $id_item_servico = $itemVenda['id'];
                        $id_venda = $itemVenda['id_venda'];
                        // Atualiza venda_itens (realizados +1)
                        $pdo->prepare("UPDATE venda_itens SET realizados = realizados + 1 WHERE id = ?")
                            ->execute([$id_item_servico]);
                    }
                }

                // Montar campos para insert
                $campos_insert = [
                    'id_agendamento'     => $id_agendamento,
                    'data'               => $agendamento['data'],
                    'profissional_1'     => $agendamento['profissional_1'],
                    'id_profissional_1'  => $agendamento['id_profissional_1'],
                    'id_cliente'         => $agendamento['id_cliente'],
                    'id_servico'         => $agendamento['id_servico'],
                    'servico'            => $agendamento['servico'],
                    'prontuario'         => $texto_prontuario,
                    'hora_inicio'        => $hora_inicio,
                    'hora_fim'           => $hora_fim,
                    'status'             => $status,
                    'id_item_servico'    => $id_item_servico,
                    'id_venda'           => $id_venda,
                    'id_user_alteracao'  => $id_usuario,
                    'user_alteracao'     => $nome_usuario,
                    'data_alteracao'     => $data_alteracao
                ];

                $cols = implode(',', array_keys($campos_insert));
                $vals = implode(',', array_map(fn($k)=>":$k", array_keys($campos_insert)));
                $sql_insert = "INSERT INTO atendimentos ($cols) VALUES ($vals)";
                $stmt = $pdo->prepare($sql_insert);
                $stmt->execute($campos_insert);
                $id_atendimento = $pdo->lastInsertId();

            } 
    }

    // Atualiza sempre o agendamento!
if (in_array($status, ['Faltou','Cancelado','NRealizado'])) {
    $id_item_servico = '0';
    $id_venda = '0';
    $id_atendimento = '0';
}
    
    $stmt = $pdo->prepare("UPDATE agendamentos 
        SET status = ?, id_atendimento = ?, id_item_servico = ?, id_venda = ?
        WHERE id = ?");
    $stmt->execute([
        $status, $id_atendimento, $id_item_servico, $id_venda, $id_agendamento
    ]);

    $pdo->commit();
    echo json_encode(['sucesso'=>true, 'id_atendimento'=>$id_atendimento]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['erro' => 'Erro ao salvar: '.$e->getMessage()]);
}







?>