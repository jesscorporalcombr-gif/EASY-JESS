<?php
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Normaliza datas (aceita DD/MM/YYYY, DD-MM-YYYY, YYYY-MM-DD)
 * Retorna YYYY-MM-DD ou null se vazio/ inválido
 */
function normalize_date(?string $s) : ?string {
    $s = trim((string)$s);
    if ($s === '') return null;

    // troca - por /
    $s = str_replace('-', '/', $s);
    $parts = explode('/', $s);

    if (count($parts) === 3) {
        // decide se veio D/M/Y ou Y/M/D
        if (strlen($parts[0]) === 4) { // YYYY/MM/DD
            $y = (int)$parts[0]; $m = (int)$parts[1]; $d = (int)$parts[2];
        } else { // DD/MM/YYYY
            $d = (int)$parts[0]; $m = (int)$parts[1]; $y = (int)$parts[2];
        }
        if (checkdate($m, $d, $y)) {
            return sprintf("%04d-%02d-%02d", $y, $m, $d);
        }
        return null;
    }

    // fallback: tenta DateTime
    try {
        $dt = new DateTime($s);
        return $dt->format('Y-m-d');
    } catch (Exception $e) {
        return null;
    }
}
function respondJSON(bool $ok, string $msg, array $extra = []): void {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        // opcional: status HTTP
        http_response_code($ok ? 200 : 400);
    }
    echo json_encode(array_merge(['success' => $ok, 'message' => $msg], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}
// --- Sessão / metadados
$pasta       = $_SESSION['x_url'] ?? ''; // defina um default se necessário (ex: 'site')
$created     = date('Y-m-d H:i:s');
$id_usuario  = $_SESSION['id_usuario'] ?? null;
$usuario     = $_SESSION['nome_usuario'] ?? '';

// --- POST (com defaults para evitar notices)
$id                 = $_POST['frm-id']              ?? '';
$nome               = $_POST['frm-nome']            ?? '';
$data_nascimento    = normalize_date($_POST['frm-data_nascimento'] ?? null);
$sexo               = $_POST['frm-sexo']            ?? '';
$cpf                = $_POST['frm-cpf']             ?? '';
$cnh                = $_POST['frm-cnh']             ?? '';
$cnh_categoria      = $_POST['frm-cnh_categoria']   ?? '';
$cnh_dt_validade    = normalize_date($_POST['frm-cnh_dt_validade'] ?? null);
$rg                 = $_POST['frm-rg']              ?? '';
$orgao              = $_POST['frm-orgao']           ?? '';
$data_exp           = normalize_date($_POST['frm-data_exp'] ?? null);
$e_social           = $_POST['frm-e_social']        ?? '';
$data_chegada_brasil= normalize_date($_POST['frm-data_chegada_brasil'] ?? null);
$etinia             = $_POST['frm-etinia']          ?? ''; // manter conforme seu banco
$pis_dt_cadastro    = normalize_date($_POST['frm-pis_dt_cadastro'] ?? null);
$conta_fgts         = $_POST['frm-conta_fgts']      ?? '';
$fgts_dt_opcao      = normalize_date($_POST['frm-fgts_dt_opcao'] ?? null);
$cert_reservista    = $_POST['frm-cert_reservista'] ?? '';
$est_civil          = $_POST['frm-est_civil']       ?? '';
$nome_conj          = $_POST['frm-nome_conj']       ?? '';
$dados_conj         = $_POST['frm-dados_conj']      ?? '';
$ctps               = $_POST['frm-ctps']            ?? '';
$serie              = $_POST['frm-serie']           ?? '';
$pis                = $_POST['frm-pis']             ?? '';
$titulo             = $_POST['frm-titulo']          ?? '';
$zona               = $_POST['frm-zona']            ?? '';
$sessao             = $_POST['frm-sessao']          ?? ''; // corrigido (antes estava frmsessao)
$cep                = $_POST['frm-cep']             ?? '';
$endereco           = $_POST['frm-endereco']        ?? '';
$numero             = $_POST['frm-numero']          ?? '';
$complemento        = $_POST['frm-complemento']     ?? '';
$bairro             = $_POST['frm-bairro']          ?? '';
$cidade             = $_POST['frm-cidade']          ?? '';
$uf_endereco        = $_POST['frm-uf_endereco']     ?? '';
$nome_mae           = $_POST['frm-nome_mae']        ?? '';
$nome_pai           = $_POST['frm-nome_pai']        ?? '';
$telefone           = $_POST['frm-telefone']        ?? '';
$telefone2          = $_POST['frm-telefone2']       ?? '';
$email_pessoal      = $_POST['frm-email_pessoal']   ?? '';
$banco_if           = $_POST['frm-banco_if']        ?? '';
$agencia            = $_POST['frm-agencia']         ?? '';
$conta              = $_POST['frm-conta']           ?? '';
$pix                = $_POST['frm-pix']             ?? '';
$tipo_pix           = $_POST['frm-tipo_pix']        ?? '';
$tp_sanguineo       = $_POST['frm-tp_sanguineo']    ?? '';
$naturalidade       = $_POST['frm-naturalidade']    ?? '';
$uf_naturalidade    = $_POST['frm-uf_naturalidade'] ?? '';
$deficiente_sim_nao = $_POST['frm-deficiente_sim_nao'] ?? '';
$deficiencia        = $_POST['frm-deficiencia']     ?? '';
$tp_deficiencia     = $_POST['frm-tp_deficiencia']  ?? '';
$nacionalidade      = $_POST['frm-nacionalidade']   ?? '';
$escolaridade       = $_POST['frm-escolaridade']    ?? '';
$ativo_agenda       = (isset($_POST['frm-ativo_agenda']) && $_POST['frm-ativo_agenda'] === "Ativo") ? 1 : 0;
$situacao           = $_POST['frm-situacao']        ?? '';
$senha_sistema      = $_POST['frm-senha_sistema']   ?? '';
if (!$senha_sistema) { $senha_sistema = $_POST['frm-passalt'] ?? ''; }
$email              = $_POST['frm-email']           ?? '';
$instagram = $_POST['frm-instagram']??'';
$facebook= $_POST['frm-facebook']??'';
$linkedin = $_POST['frm-linkedin']??'';
$tiktok= $_POST['frm-tiktok']??'';
$outras_redes = $_POST['frm-outras_redes']??'';

// --- Validação de duplicidade (contra os dados de entrada)



//var_dump($id); exit;
try {
    $sql = "SELECT cpf, nome, telefone, email_pessoal, pis, rg, email, COUNT(*) as num_duplicados
            FROM colaboradores_cadastros
            WHERE cpf IS NOT NULL AND cpf != '' 
              AND nome IS NOT NULL AND nome != ''
              AND telefone IS NOT NULL AND telefone != ''
              AND email_pessoal IS NOT NULL AND email_pessoal != ''
              AND pis IS NOT NULL AND pis != ''
              AND rg IS NOT NULL AND rg != ''
              AND email IS NOT NULL AND email != ''"
              . ($id !== '' ? " AND id <> :id" : '') . "
            GROUP BY cpf, nome, telefone, email_pessoal, pis, rg, email
            HAVING COUNT(*) > 1";

    $stmt = $pdo->prepare($sql);
    if ($id !== '') {
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    }
    $stmt->execute();
    $duplicados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($duplicados) {
        // Aqui você decide se quer interromper ou apenas avisar
        respondJSON(false, "Já existe outro cadastro com os mesmos dados principais. ". $duplicados);
    }
} catch (PDOException $e) {
    respondJSON(false, "Erro na consulta de duplicidade: " . $e->getMessage());
}

// --- Upload de imagem (robusto: sem SVG, cria diretório, checa error)
$tempoMarca   = date('ymdHis');
$nomeSan      = preg_replace('/[^a-zA-Z0-9_-]/', '-', $nome);
$nomeImgBase  = "cadastro{$id}-{$nomeSan}{$tempoMarca}";
$baseDir      = '../../' . ($pasta !== '' ? $pasta . '/' : '') . 'img/cadastro_colaboradores/';
$imagem_cadastro = "sem-foto.jpg";

if (isset($_FILES['input-foto_cadColaborador']) && is_array($_FILES['input-foto_cadColaborador']) && $_FILES['input-foto_cadColaborador']['name'] !== '') {
    if ($_FILES['input-foto_cadColaborador']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['input-foto_cadColaborador']['name'], PATHINFO_EXTENSION));
        $extPermitidas = ['jpg','jpeg','png','gif']; // sem svg por segurança

        if (!in_array($extensao, $extPermitidas, true)) {
            respondJSON(false, "Extensão de imagem não permitida!");
        }

        if (!is_dir($baseDir)) {
            if (!mkdir($baseDir, 0775, true) && !is_dir($baseDir)) {
                respondJSON(false, "Não foi possível criar o diretório de upload.");
            }
        }

        $nomeArquivoCompleto = $nomeImgBase . '.' . $extensao;
        $caminhoCompleto     = $baseDir . $nomeArquivoCompleto;

        if (move_uploaded_file($_FILES['input-foto_cadColaborador']['tmp_name'], $caminhoCompleto)) {
            $imagem_cadastro = $nomeArquivoCompleto;
        } else {
            respondJSON(false, "Erro ao enviar o arquivo de imagem.");
        }
    } else {
        respondJSON(false, "Falha no upload: código " . (int)$_FILES['input-foto_cadColaborador']['error']);
    }
} else {
    // Sem novo upload: se for update, manter a imagem anterior (se existir)
    if ($id !== '') {
        try {
            $stmt = $pdo->prepare("SELECT foto_cadastro FROM colaboradores_cadastros WHERE id = ?");
            $stmt->execute([$id]);
            $existente = $stmt->fetchColumn();
            if (!empty($existente)) {
                $imagem_cadastro = $existente;
            }
        } catch (PDOException $e) {
            respondJSON(false, "Erro ao obter a foto atual: " . $e->getMessage());
        }
    }
}

// --- Senha (hash no insert; no update mantém a atual se não enviada)
$senha_hash = $senha_sistema;
if ($senha_sistema !== '') {
    // sempre que vier uma senha nova, re-hash
    $senha_hash = password_hash($senha_sistema, PASSWORD_DEFAULT);
}

if ($id === '') {
    // INSERT
    try {
        $sql = "INSERT INTO colaboradores_cadastros
            (nome, data_nascimento, sexo, cpf, cnh, cnh_categoria, cnh_dt_validade, rg, orgao, data_exp, e_social,
             data_chegada_brasil, etinia, pis_dt_cadastro, conta_fgts, fgts_dt_opcao, cert_reservista, est_civil, nome_conj,
             dados_conj, ctps, serie, pis, titulo, zona, sessao, cep, endereco, numero, complemento, bairro, cidade,
             uf_endereco, nome_mae, nome_pai, telefone, telefone2, email_pessoal, banco_if, agencia, conta, pix, tipo_pix,
             tp_sanguineo, naturalidade, uf_naturalidade, deficiente_sim_nao, deficiencia, tp_deficiencia, nacionalidade,
             escolaridade, ativo_agenda, foto_cadastro, situacao, senha_sistema, email, id_user_criacao, user_criacao, data_criacao, instagram, facebook, linkedin, tiktok, outras_redes)
            VALUES
            (:nome, :data_nascimento, :sexo, :cpf, :cnh, :cnh_categoria, :cnh_dt_validade, :rg, :orgao, :data_exp, :e_social,
             :data_chegada_brasil, :etinia, :pis_dt_cadastro, :conta_fgts, :fgts_dt_opcao, :cert_reservista, :est_civil, :nome_conj,
             :dados_conj, :ctps, :serie, :pis, :titulo, :zona, :sessao, :cep, :endereco, :numero, :complemento, :bairro, :cidade,
             :uf_endereco, :nome_mae, :nome_pai, :telefone, :telefone2, :email_pessoal, :banco_if, :agencia, :conta, :pix, :tipo_pix,
             :tp_sanguineo, :naturalidade, :uf_naturalidade, :deficiente_sim_nao, :deficiencia, :tp_deficiencia, :nacionalidade,
             :escolaridade, :ativo_agenda, :foto_cadastro, :situacao, :senha_sistema, :email, :id_user_criacao, :user_criacao, :data_criacao, :instagram, :facebook, :linkedin, :tiktok, :outras_redes)";
        $res = $pdo->prepare($sql);

        $res->bindValue(":nome", $nome);
        $res->bindValue(":data_nascimento", $data_nascimento);
        $res->bindValue(":sexo", $sexo);
        $res->bindValue(":cpf", $cpf);
        $res->bindValue(":cnh", $cnh);
        $res->bindValue(":cnh_categoria", $cnh_categoria);
        $res->bindValue(":cnh_dt_validade", $cnh_dt_validade);
        $res->bindValue(":rg", $rg);
        $res->bindValue(":orgao", $orgao);
        $res->bindValue(":data_exp", $data_exp);
        $res->bindValue(":e_social", $e_social);
        $res->bindValue(":data_chegada_brasil", $data_chegada_brasil);
        $res->bindValue(":etinia", $etinia);
        $res->bindValue(":pis_dt_cadastro", $pis_dt_cadastro);
        $res->bindValue(":conta_fgts", $conta_fgts);
        $res->bindValue(":fgts_dt_opcao", $fgts_dt_opcao);
        $res->bindValue(":cert_reservista", $cert_reservista);
        $res->bindValue(":est_civil", $est_civil);
        $res->bindValue(":nome_conj", $nome_conj);
        $res->bindValue(":dados_conj", $dados_conj);
        $res->bindValue(":ctps", $ctps);
        $res->bindValue(":serie", $serie);
        $res->bindValue(":pis", $pis);
        $res->bindValue(":titulo", $titulo);
        $res->bindValue(":zona", $zona);
        $res->bindValue(":sessao", $sessao);
        $res->bindValue(":cep", $cep);
        $res->bindValue(":endereco", $endereco);
        $res->bindValue(":numero", $numero);
        $res->bindValue(":complemento", $complemento);
        $res->bindValue(":bairro", $bairro);
        $res->bindValue(":cidade", $cidade);
        $res->bindValue(":uf_endereco", $uf_endereco);
        $res->bindValue(":nome_mae", $nome_mae);
        $res->bindValue(":nome_pai", $nome_pai);
        $res->bindValue(":telefone", $telefone);
        $res->bindValue(":telefone2", $telefone2);
        $res->bindValue(":email_pessoal", $email_pessoal);
        $res->bindValue(":banco_if", $banco_if);
        $res->bindValue(":agencia", $agencia);
        $res->bindValue(":conta", $conta);
        $res->bindValue(":pix", $pix);
        $res->bindValue(":tipo_pix", $tipo_pix);
        $res->bindValue(":tp_sanguineo", $tp_sanguineo);
        $res->bindValue(":naturalidade", $naturalidade);
        $res->bindValue(":uf_naturalidade", $uf_naturalidade);
        $res->bindValue(":deficiente_sim_nao", $deficiente_sim_nao);
        $res->bindValue(":deficiencia", $deficiencia);
        $res->bindValue(":tp_deficiencia", $tp_deficiencia);
        $res->bindValue(":nacionalidade", $nacionalidade);
        $res->bindValue(":escolaridade", $escolaridade);
        $res->bindValue(":ativo_agenda", $ativo_agenda, PDO::PARAM_INT);
        $res->bindValue(":foto_cadastro", $imagem_cadastro);
        $res->bindValue(":situacao", $situacao);
        $res->bindValue(":senha_sistema", $senha_hash);
        $res->bindValue(":email", $email);
        $res->bindValue(":id_user_criacao", $id_usuario);
        $res->bindValue(":user_criacao", $usuario);
        $res->bindValue(":data_criacao", $created);
		$res->bindValue(":instagram", $instagram);
		$res->bindValue(":facebook", $facebook);
		$res->bindValue(":linkedin", $linkedin);
		$res->bindValue(":tiktok", $tiktok);
		$res->bindValue(":outras_redes", $outras_redes);

        $res->execute();
    } catch (PDOException $e) {
        respondJSON(false, "Erro ao inserir os dados no banco: " . $e->getMessage());
    }
} else {
    // UPDATE
    try {
        // Se nenhuma senha nova foi enviada, mantém a existente
        if ($senha_sistema === '' || $senha_sistema === null) {
            $q = $pdo->prepare("SELECT senha_sistema FROM colaboradores_cadastros WHERE id = :id");
            $q->execute([':id' => $id]);
            $senha_hash = $q->fetchColumn() ?: null;
        }

        $sql = "UPDATE colaboradores_cadastros SET
            nome = :nome, data_nascimento = :data_nascimento, sexo = :sexo, cpf = :cpf,
            cnh = :cnh, cnh_categoria = :cnh_categoria, cnh_dt_validade = :cnh_dt_validade,
            rg = :rg, orgao = :orgao, data_exp = :data_exp, e_social = :e_social,
            data_chegada_brasil = :data_chegada_brasil, etinia = :etinia, pis_dt_cadastro = :pis_dt_cadastro,
            conta_fgts = :conta_fgts, fgts_dt_opcao = :fgts_dt_opcao, cert_reservista = :cert_reservista,
            est_civil = :est_civil, nome_conj = :nome_conj, dados_conj = :dados_conj, ctps = :ctps,
            serie = :serie, pis = :pis, titulo = :titulo, zona = :zona, sessao = :sessao,
            cep = :cep, endereco = :endereco, numero = :numero, complemento = :complemento,
            bairro = :bairro, cidade = :cidade, uf_endereco = :uf_endereco, nome_mae = :nome_mae,
            nome_pai = :nome_pai, telefone = :telefone, telefone2 = :telefone2, email_pessoal = :email_pessoal,
            banco_if = :banco_if, agencia = :agencia, conta = :conta, pix = :pix, tipo_pix = :tipo_pix,
            tp_sanguineo = :tp_sanguineo, naturalidade = :naturalidade, uf_naturalidade = :uf_naturalidade,
            deficiente_sim_nao = :deficiente_sim_nao, deficiencia = :deficiencia, tp_deficiencia = :tp_deficiencia,
            nacionalidade = :nacionalidade, escolaridade = :escolaridade, ativo_agenda = :ativo_agenda,
            foto_cadastro = :foto_cadastro, situacao = :situacao, senha_sistema = :senha_sistema,
            email = :email, id_user_alteracao = :id_user_alteracao, user_alteracao = :user_alteracao,
            data_alteracao = :data_alteracao, instagram = :instagram, facebook = :facebook, linkedin = :linkedin, tiktok = :tiktok, outras_redes = :outras_redes
            WHERE id = :id";

        $res = $pdo->prepare($sql);

        $res->bindValue(":id", $id);
        $res->bindValue(":nome", $nome);
        $res->bindValue(":data_nascimento", $data_nascimento);
        $res->bindValue(":sexo", $sexo);
        $res->bindValue(":cpf", $cpf);
        $res->bindValue(":cnh", $cnh);
        $res->bindValue(":cnh_categoria", $cnh_categoria);
        $res->bindValue(":cnh_dt_validade", $cnh_dt_validade);
        $res->bindValue(":rg", $rg);
        $res->bindValue(":orgao", $orgao);
        $res->bindValue(":data_exp", $data_exp);
        $res->bindValue(":e_social", $e_social);
        $res->bindValue(":data_chegada_brasil", $data_chegada_brasil);
        $res->bindValue(":etinia", $etinia);
        $res->bindValue(":pis_dt_cadastro", $pis_dt_cadastro);
        $res->bindValue(":conta_fgts", $conta_fgts);
        $res->bindValue(":fgts_dt_opcao", $fgts_dt_opcao);
        $res->bindValue(":cert_reservista", $cert_reservista);
        $res->bindValue(":est_civil", $est_civil);
        $res->bindValue(":nome_conj", $nome_conj);
        $res->bindValue(":dados_conj", $dados_conj);
        $res->bindValue(":ctps", $ctps);
        $res->bindValue(":serie", $serie);
        $res->bindValue(":pis", $pis);
        $res->bindValue(":titulo", $titulo);
        $res->bindValue(":zona", $zona);
        $res->bindValue(":sessao", $sessao);
        $res->bindValue(":cep", $cep);
        $res->bindValue(":endereco", $endereco);
        $res->bindValue(":numero", $numero);
        $res->bindValue(":complemento", $complemento);
        $res->bindValue(":bairro", $bairro);
        $res->bindValue(":cidade", $cidade);
        $res->bindValue(":uf_endereco", $uf_endereco);
        $res->bindValue(":nome_mae", $nome_mae);
        $res->bindValue(":nome_pai", $nome_pai);
        $res->bindValue(":telefone", $telefone);
        $res->bindValue(":telefone2", $telefone2);
        $res->bindValue(":email_pessoal", $email_pessoal);
        $res->bindValue(":banco_if", $banco_if);
        $res->bindValue(":agencia", $agencia);
        $res->bindValue(":conta", $conta);
        $res->bindValue(":pix", $pix);
        $res->bindValue(":tipo_pix", $tipo_pix);
        $res->bindValue(":tp_sanguineo", $tp_sanguineo);
        $res->bindValue(":naturalidade", $naturalidade);
        $res->bindValue(":uf_naturalidade", $uf_naturalidade);
        $res->bindValue(":deficiente_sim_nao", $deficiente_sim_nao);
        $res->bindValue(":deficiencia", $deficiencia);
        $res->bindValue(":tp_deficiencia", $tp_deficiencia);
        $res->bindValue(":nacionalidade", $nacionalidade);
        $res->bindValue(":escolaridade", $escolaridade);
        $res->bindValue(":ativo_agenda", $ativo_agenda, PDO::PARAM_INT);
        $res->bindValue(":foto_cadastro", $imagem_cadastro);
        $res->bindValue(":situacao", $situacao);
        $res->bindValue(":senha_sistema", $senha_hash);
        $res->bindValue(":email", $email);
        $res->bindValue(":id_user_alteracao", $id_usuario);
        $res->bindValue(":user_alteracao", $usuario);
        $res->bindValue(":data_alteracao", $created);
		$res->bindValue(":instagram", $instagram);
		$res->bindValue(":facebook", $facebook);
		$res->bindValue(":linkedin", $linkedin);
		$res->bindValue(":tiktok", $tiktok);
		$res->bindValue(":outras_redes", $outras_redes);

        $res->execute();
    } catch (PDOException $e) {
        respondJSON(false, "Erro ao atualizar os dados no banco: " . $e->getMessage());
    }
}

respondJSON(true, 'Salvo com Sucesso!');
