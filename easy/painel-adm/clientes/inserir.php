<?php
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');



// Utilitários
function sanitizeText($txt) {
    return trim(filter_var($txt, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
}
function sanitizeNumber($num) {
    return preg_replace('/\D/', '', $num);
}
function getPost($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// Autenticação do usuário da sessão
$id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
$usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
if (!$id_usuario || !$usuario) {
    echo json_encode(['success' => false, 'msg' => 'Sessão inválida. Faça login novamente.']);
    exit;
}

// Coleta e sanitização dos dados do POST


$id = getPost('id');


$nome = sanitizeText(getPost('nome'));
$email = filter_var(getPost('email'), FILTER_VALIDATE_EMAIL) ? getPost('email') : '';
$cpf = sanitizeNumber(getPost('cpf'));
$nivel = sanitizeText(getPost('nivel'));
$aniversario = getPost('aniversario');
$telefone = sanitizeNumber(getPost('telefone'));
$celular = sanitizeNumber(getPost('celular'));
$sexo = getPost('sexo');
$sexo = ($sexo == "Masculino") ? "m" : (($sexo == "Feminino") ? "f" : "p");
$como_conheceu = sanitizeText(getPost('como_conheceu'));
$cep = sanitizeNumber(getPost('cep'));
$endereco = sanitizeText(getPost('endereco'));
$numero = sanitizeText(getPost('numero'));
$estado = sanitizeText(getPost('estado'));
$cidade = sanitizeText(getPost('cidade'));
$bairro = sanitizeText(getPost('bairro'));
$profissao = sanitizeText(getPost('profissao'));
$observacoes = sanitizeText(getPost('observacoes'));
$cadastrado = getPost('cadastrado'); // não utilizado?
$rg = sanitizeText(getPost('rg'));
$complemento = sanitizeText(getPost('complemento'));
$nome_usuario = sanitizeText(getPost('nome_usuario'));
$senha = getPost('senha');

$created = date('Y-m-d H:i:s');

// Se for edição e senha está vazia, mantém a senha existente
if (empty($senha) && !empty($id)) {
    $sql = "SELECT senha FROM clientes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $senhaReg = $stmt->fetch(PDO::FETCH_ASSOC);
    $senha = $senhaReg ? $senhaReg['senha'] : '';
}

// Checagem de duplicidade (CPF, nome, celular, nome_usuario)






// Checagem de duplicidade (CPF, nome, celular, nome_usuario)

 $isUpdate = !empty($id);

try {
   
    $sqlDup = "SELECT cpf, nome, celular, nome_usuario 
           FROM clientes
           WHERE " . ($isUpdate ? "id != :id" : "1=1") . "
           AND (
               (:cpf <> '' AND cpf IS NOT NULL AND cpf <> '' AND REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = :cpf)
               OR (:nome <> '' AND nome IS NOT NULL AND nome <> '' AND REPLACE(nome, ' ', '') = :nome)
               OR (:celular <> '' AND celular IS NOT NULL AND celular <> '' AND REPLACE(REPLACE(REPLACE(celular, '(', ''), ')', ''), '-', '') = :celular)
               OR (:nome_usuario <> '' AND nome_usuario IS NOT NULL AND nome_usuario <> '' AND nome_usuario = :nome_usuario)
           )";

    $stmt = $pdo->prepare($sqlDup);
    if ($isUpdate) $stmt->bindValue(':id', $id);
    $stmt->bindValue(':cpf', $cpf);
    $stmt->bindValue(':nome', preg_replace('/\s/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nome)));
    $stmt->bindValue(':celular', $celular);
    $stmt->bindValue(':nome_usuario', $nome_usuario);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $dups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $mensagens = [];
        foreach ($dups as $dup) {
            if (!empty($dup['cpf']) && $cpf === sanitizeNumber($dup['cpf'])) $mensagens[] = "CPF já cadastrado!";
            if (!empty($dup['nome']) && preg_replace('/\s/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nome)) === preg_replace('/\s/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $dup['nome']))) $mensagens[] = "Nome já cadastrado!";
            if (!empty($dup['celular']) && $celular === sanitizeNumber($dup['celular'])) $mensagens[] = "Celular já cadastrado!";
            if (!empty($dup['nome_usuario']) && $nome_usuario === $dup['nome_usuario']) $mensagens[] = "Nome de usuário já cadastrado!";
        }
        if (count($mensagens)) {
            echo json_encode(['success' => false, 'msg' => implode(' ', $mensagens)], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'msg' => 'Erro na checagem de duplicidade: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}






// Upload da imagem
function handleImageUpload($id, $nome, $pdo) {
    $pasta = $_SESSION['x_url'];
    $imgDir = '../../'. $pasta . '/img/clientes/';
    $img = '';

    if (isset($_FILES['input-foto_cadCliente']['name']) && $_FILES['input-foto_cadCliente']['name'] != "") {
        // Busca e remove a foto antiga se existir
        if ($id) {
            $stmt = $pdo->prepare("SELECT foto FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $fotoAntiga = $stmt->fetchColumn();
            if ($fotoAntiga && file_exists($imgDir . $fotoAntiga)) {
                @unlink($imgDir . $fotoAntiga);
            }
        }

        $tempoMarca = date('ymdHis');
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '-', "cliente" . $id . "-" . $nome) . $tempoMarca;
        $baseName = preg_replace('/[ :]+/', '-', $baseName);
        $ext = strtolower(pathinfo($_FILES['input-foto_cadCliente']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) return ['ok' => false, 'msg' => 'Extensão de imagem não permitida!'];
        $fullName = $baseName . '.' . $ext;
        $dest = $imgDir . $fullName;
        if (move_uploaded_file($_FILES['input-foto_cadCliente']['tmp_name'], $dest)) {
            $img = $fullName;
        } else {
            return ['ok' => false, 'msg' => 'Erro ao enviar o arquivo.'];
        }
    }
    return ['ok' => true, 'file' => $img];
}

// Define $imagem (foto do cliente)
$imagem = '';
if (!empty($_FILES['input-foto_cadCliente']['name'])) {
    $up = handleImageUpload($id, $nome, $pdo);
    if (!$up['ok']) {
        echo json_encode(['success' => false, 'msg' => $up['msg']]);
        exit;
    }
    $imagem = $up['file'];
} else if ($id) {
    $stmt = $pdo->prepare("SELECT foto FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $imagem = $stmt->fetchColumn();
}

// INSERÇÃO OU ATUALIZAÇÃO
try {
    if (empty($id)) {
        // INSERT
        $sql = "INSERT INTO clientes 
            (nome, email, cpf, senha, nivel, aniversario, telefone, celular, sexo, como_conheceu, cep, endereco, numero, estado, cidade, bairro, profissao, data_cadastro, observacoes, rg, complemento, id_user_criacao, user_criacao, data_criacao, foto, nome_usuario)
            VALUES
            (:nome, :email, :cpf, :senha, :nivel, :aniversario, :telefone, :celular, :sexo, :como_conheceu, :cep, :endereco, :numero, :estado, :cidade, :bairro, :profissao, :data_cadastro, :observacoes, :rg, :complemento, :id_user_criacao, :user_criacao, :data_criacao, :foto, :nome_usuario)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':senha', $senha); // ou password_hash($senha, PASSWORD_DEFAULT) se quiser criptografar
        $stmt->bindValue(':nivel', $nivel);
        $stmt->bindValue(':aniversario', $aniversario);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':celular', $celular);
        $stmt->bindValue(':sexo', $sexo);
        $stmt->bindValue(':como_conheceu', $como_conheceu);
        $stmt->bindValue(':cep', $cep);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->bindValue(':numero', $numero);
        $stmt->bindValue(':estado', $estado);
        $stmt->bindValue(':cidade', $cidade);
        $stmt->bindValue(':bairro', $bairro);
        $stmt->bindValue(':profissao', $profissao);
        $stmt->bindValue(':data_cadastro', $created);
        $stmt->bindValue(':observacoes', $observacoes);
        $stmt->bindValue(':rg', $rg);
        $stmt->bindValue(':complemento', $complemento);
        $stmt->bindValue(':id_user_criacao', $id_usuario);
        $stmt->bindValue(':user_criacao', $usuario);
        $stmt->bindValue(':data_criacao', $created);
        $stmt->bindValue(':foto', $imagem);
        $stmt->bindValue(':nome_usuario', $nome_usuario);
        $stmt->execute();
    } else {
        // UPDATE
        $sql = "UPDATE clientes SET
            nome = :nome, email = :email, cpf = :cpf, senha = :senha, nivel = :nivel, aniversario = :aniversario, telefone = :telefone, celular = :celular, sexo = :sexo, como_conheceu = :como_conheceu, cep = :cep, endereco = :endereco, numero = :numero, estado = :estado, cidade = :cidade, bairro = :bairro, profissao = :profissao, observacoes = :observacoes, rg = :rg, complemento = :complemento, id_user_alteracao = :id_user_alteracao, user_alteracao = :user_alteracao, data_alteracao = :data_alteracao, foto = :foto, nome_usuario = :nome_usuario
            WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':senha', $senha); // ou password_hash($senha, PASSWORD_DEFAULT)
        $stmt->bindValue(':nivel', $nivel);
        $stmt->bindValue(':aniversario', $aniversario);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':celular', $celular);
        $stmt->bindValue(':sexo', $sexo);
        $stmt->bindValue(':como_conheceu', $como_conheceu);
        $stmt->bindValue(':cep', $cep);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->bindValue(':numero', $numero);
        $stmt->bindValue(':estado', $estado);
        $stmt->bindValue(':cidade', $cidade);
        $stmt->bindValue(':bairro', $bairro);
        $stmt->bindValue(':profissao', $profissao);
        $stmt->bindValue(':observacoes', $observacoes);
        $stmt->bindValue(':rg', $rg);
        $stmt->bindValue(':complemento', $complemento);
        $stmt->bindValue(':id_user_alteracao', $id_usuario);
        $stmt->bindValue(':user_alteracao', $usuario);
        $stmt->bindValue(':data_alteracao', $created);
        $stmt->bindValue(':foto', $imagem);
        $stmt->bindValue(':nome_usuario', $nome_usuario);
        $stmt->execute();
    }
    echo json_encode(['success' => true, 'msg' => 'Salvo com sucesso!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'msg' => 'Erro ao salvar: ' . $e->getMessage()]);
}
?>
