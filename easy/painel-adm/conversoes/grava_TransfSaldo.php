<?php
session_start();
require_once '../../conexao.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // 1) Inicia transação
    $pdo->beginTransaction();

    // 2) Captura dados do POST
    $id_comum_transf    = intval($_POST['id-comum-transferencia']   ?? 0);
    // origem
    $id_cli_orig        = intval($_POST['id-cliente']              ?? 0);
    $nome_cli_orig      = trim($_POST['nome-cliente']             ?? '');
    $cpf_cli_orig       = trim($_POST['cpf-cliente']              ?? '');
    $cel_cli_orig       = trim($_POST['celular-cliente']          ?? '');
    $email_cli_orig     = trim($_POST['email-cliente']            ?? '');
    // destino
    $id_cli_dest        = intval($_POST['id-cliente-recebe']       ?? 0);
    $nome_cli_dest      = trim($_POST['nome-cliente-recebe']       ?? '');
    $cpf_cli_dest       = trim($_POST['cpf-cliente-recebe']        ?? '');
    $cel_cli_dest       = trim($_POST['celular-cliente-recebe']    ?? '');
    $email_cli_dest     = trim($_POST['email-cliente-recebe']      ?? '');

    $valor_transf       = floatval(str_replace(',', '.', $_POST['valor-transferencia'] ?? 0));
    $infos              = trim($_POST['informacoes']               ?? '');
    $arquivoAtual       = trim($_POST['name-file']                 ?? '');
    $anexo              = $_FILES['anexo']                         ?? null;



    // Dados de usuário
    $idUser  = $_SESSION['id_usuario']   ?? null;
    $user    = $_SESSION['nome_usuario'] ?? null;
    
    $now     = date('Y-m-d H:i:s');
    $data_hoje = date('Y-m-d');
    $local = $_SESSION['x_url'] ??'';
    $pastaBruta = __DIR__ . '/../../' . $local . '/arquivos/conversoes/';

        // 2) Resolve o caminho “de verdade” e adiciona a barra final
        $pastaArquivo = realpath($pastaBruta);
        if ($pastaArquivo === false) {
            die("Diretório não existe ou permissão negada: $pastaBruta");
        }
    $pasta_arquivo = rtrim($pastaArquivo, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    

    if ($id_comum_transf > 0) {
        // UPDATE EXISTENTE
        $stmt = $pdo->prepare("SELECT * FROM venda WHERE id_comum = :com ORDER BY id");
        $stmt->execute([':com'=>$id_comum_transf]);
        $regs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // identifica
        foreach ($regs as $r) {
            if ($r['tipo_venda']=='enviado')  $enviado  = $r;
            if ($r['tipo_venda']=='recebido') $recebido = $r;
        }
        if (!$enviado||!$recebido) throw new Exception('Registros não encontrados.');
        if ($enviado['id_cliente']!=$id_cli_orig||$recebido['id_cliente']!=$id_cli_dest) {
            throw new Exception('Clientes não podem ser alterados.');
        }
        // saldos clientes
        $c = $pdo->prepare("SELECT saldo FROM clientes WHERE id = :id");
        $c->execute([':id'=>$id_cli_orig]);    $sal_orig = floatval($c->fetchColumn());
        $c->execute([':id'=>$id_cli_dest]);    $sal_dest = floatval($c->fetchColumn());
        // recalcula
        $novo_orig = $sal_orig - $valor_transf;
        $novo_dest = $sal_dest + $valor_transf;
        if ($novo_orig<0) throw new Exception('Saldo insuficiente.');
        // atualiza clientes
        $u = $pdo->prepare("UPDATE clientes SET saldo = :s WHERE id = :id");
        $u->execute([':s'=>$novo_orig,':id'=>$id_cli_orig]);
        $u->execute([':s'=>$novo_dest,':id'=>$id_cli_dest]);
        // anexo
        if (!empty($anexo['name'])) {
            // remover antigo e mover novo
            unlink($pasta_arquivo.$arquivoAtual);
            $novoArquivo = time().'_'.basename($anexo['name']);
            
            
            if (move_uploaded_file($anexo['tmp_name'], $pasta_arquivo . $novoArquivo)) {
                
            } else {
                echo "Falha ao gravar o arquivo.";
                exit;
            }
        
        } else {
            $novoArquivo = $arquivoAtual;
        }
        // update vendas
        $upd = $pdo->prepare("UPDATE venda SET
            tipo_venda       = :tipo,
            id_cliente       = :idc,
            cliente          = :nome,
            cpf              = :cpf,
            celular          = :cel,
            email            = :email,
            informacoes      = :inf,
            valor_final      = :vf,
            saldo_final      = :sf,
            arquivo          = :arq,
            user_alteracao   = :usr,
            id_user_alteracao= :uid,
            dataHora_alteracao = :dh
          WHERE id = :id");
        // enviado
        $upd->execute([
            ':tipo'=>'enviado',':idc'=>$id_cli_orig,':nome'=>$nome_cli_orig,
            ':cpf'=>$cpf_cli_orig,':cel'=>$cel_cli_orig,':email'=>$email_cli_orig,
            ':inf'=>$infos,':vf'=>-$valor_transf,':sf'=>$valor_transf,
            ':arq'=>$novoArquivo,':usr'=>$user,':uid'=>$idUser,':dh'=>$now,
            ':id'=>$enviado['id']
        ]);
        // recebido
        $upd->execute([
            ':tipo'=>'recebido',':idc'=>$id_cli_dest,':nome'=>$nome_cli_dest,
            ':cpf'=>$cpf_cli_dest,':cel'=>$cel_cli_dest,':email'=>$email_cli_dest,
            ':inf'=>$infos,':vf'=>$valor_transf,':sf'=>$valor_transf,
            ':arq'=>$novoArquivo,':usr'=>$user,':uid'=>$idUser,':dh'=>$now,
            ':id'=>$recebido['id']
        ]);
        //atualiza conversoes
        
        $upt = $pdo->prepare("UPDATE venda_conversoes SET
            valor_un           = :vt,
            informacoes        =:inf,
            arquivo            = :arq,                
            user_alteracao     = :usr,
            id_user_alteracao  = :uid,
            dataHora_alteracao = :dh
        ");

        $upt->execute([
            ':vt'=>$valor_transf,
            'inf:'=>$infos,
            ':arq' => $novoArquivo,
            ':usr'=>$user,
            ':uid'=>$idUser,
            ':dh'=>$now
        ]);









    } else {
        // NOVA TRANSFERÊNCIA
        // valida clientes
        $c = $pdo->prepare("SELECT id,saldo FROM clientes WHERE id IN(:o,:d)");
        $c->execute([':o'=>$id_cli_orig,':d'=>$id_cli_dest]);
        $cls = $c->fetchAll(PDO::FETCH_KEY_PAIR);
        if (count($cls)<2) throw new Exception('Cliente não cadastrado.');
        if ($cls[$id_cli_orig]<$valor_transf) throw new Exception('Saldo insuficiente.');
        // novos saldos
        $novo_orig = $cls[$id_cli_orig]-$valor_transf;
        $novo_dest = $cls[$id_cli_dest]+$valor_transf;
        $u = $pdo->prepare("UPDATE clientes SET saldo=:s WHERE id=:id");
        $u->execute([':s'=>$novo_orig,':id'=>$id_cli_orig]);
        $u->execute([':s'=>$novo_dest,':id'=>$id_cli_dest]);
        // anexo
        if (!empty($anexo['name'])) {
            $novoArquivo = time().'_'.basename($anexo['name']);
            move_uploaded_file($anexo['tmp_name'],$pasta_arquivo.$novoArquivo);
        } else {
            $novoArquivo = '';
        }
        // insere vendas
        $ins = $pdo->prepare("INSERT INTO venda
          (tipo_venda,data_venda,id_cliente,cliente,cpf,celular,email,
           valor_final,saldo,informacoes,arquivo,
           user_criacao,id_user_criacao,dataHora_criacao,
           id_cliente_origem,id_cliente_destino)
         VALUES
          (:tipo,:data,:idc,:nome,:cpf,:cel,:email,
           :vf,:sf,:inf,:arq,
           :usr,:uid,:dh,:o,:d)");
        // enviado
        $ins->execute([
            ':tipo'=>'enviado',':data'=>$data_hoje,':idc'=>$id_cli_orig,':nome'=>$nome_cli_orig,
            ':cpf'=>$cpf_cli_orig,':cel'=>$cel_cli_orig,':email'=>$email_cli_orig,
            ':vf'=>-$valor_transf,':sf'=>-$valor_transf,':inf'=>$infos,':arq'=>$novoArquivo,
            ':usr'=>$user,':uid'=>$idUser,':dh'=>$now,':o'=>$id_cli_orig,':d'=>$id_cli_dest
        ]);
        $id1 = $pdo->lastInsertId();
        // recebido
        $ins->execute([
            ':tipo'=>'recebido',':data'=>$data_hoje,':idc'=>$id_cli_dest,':nome'=>$nome_cli_dest,
            ':cpf'=>$cpf_cli_dest,':cel'=>$cel_cli_dest,':email'=>$email_cli_dest,
            ':vf'=>$valor_transf,':sf'=>$valor_transf,':inf'=>$infos,':arq'=>$novoArquivo,
            ':usr'=>$user,':uid'=>$idUser,':dh'=>$now,':o'=>$id_cli_orig,':d'=>$id_cli_dest
        ]);
        $id2 = $pdo->lastInsertId();
        // define id_comum
        $com = min($id1,$id2);
        $pdo->prepare("UPDATE venda SET id_comum=:c WHERE id IN(:a,:b)")
            ->execute([':c'=>$com,':a'=>$id1,':b'=>$id2]);


        // insere conversoes
        $insc = $pdo->prepare("INSERT INTO venda_conversoes
          (tipo,data,id_comum,id_venda_origem,id_cliente_origem,id_venda_destino, id_cliente_destino, valor_un, informacoes, arquivo, user_criacao, id_user_criacao, dataHora_criacao)
                VALUES
                (:tipo,:data,:idc,:idvo,:idco,:idvd,:idcd,:vu,:inf,:arq,:uc,:iduc,:dhc)");
        $insc->execute([
            ':tipo'=>'transferencia saldo',
            ':data'=>$data_hoje,
            ':idc'=>$com,
            ':idvo'=>$id1,
            ':idco'=>$id_cli_orig,
            ':idvd'=>$id2,
            ':idcd'=>$id_cli_dest,
            ':vu'=>$valor_transf,
            ':inf'=>$infos,
            ':arq'=>$novoArquivo,
            ':uc'=>$user,
            ':iduc'=>$idUser,
            ':dhc'=>$now

        ]);

    }

    // commit
    if ($id_comum_transf > 0) {
        $idEnvio        = $enviado['id'];
        $idRecebimento  = $recebido['id'];
        $idComum        = $id_comum_transf;
    } else {
        $idEnvio        = $id1;   // último insert enviado
        $idRecebimento  = $id2;   // último insert recebido
        $idComum        = $com;   // menor dos dois, definido como id_comum
    }

    // commit da transação
    $pdo->commit();

    // retorno JSON incluindo os 3 IDs
    echo json_encode([
        'success'         => true,
        'id_comum'        => $idComum,
        'id_envio'        => $idEnvio,
        'id_recebimento'  => $idRecebimento
    ]);
} catch(Exception $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
