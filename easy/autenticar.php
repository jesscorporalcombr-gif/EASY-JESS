<?php
require_once("conexao.php");
session_start();
echo 'chegou aqui';
// Validação simples dos campos
if (empty($_POST['usuario']) || empty($_POST['senha'])) {
    header("Location: index.php?erro=1");
    exit;
}

$usuario = trim($_POST['usuario']);
$senha = $_POST['senha'];

// Consulta usuário
$stmt = $pdo->prepare("SELECT * FROM colaboradores_cadastros WHERE (email = :usuario OR cpf = :usuario) LIMIT 1");
$stmt->bindValue(":usuario", $usuario);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $senha == $user['senha_sistema']) { // Use hash na senha!
    // Dados do estabelecimento
    $est_stmt = $pdo->query("SELECT diretorio_interno FROM informacoes_do_estabelecimento LIMIT 1");
    $est = $est_stmt->fetch(PDO::FETCH_ASSOC);

    // Seta sessão segura
    $_SESSION['nome_usuario'] = $user['nome'];
    $_SESSION['cpf_usuario'] = $user['cpf'];
    $_SESSION['id_usuario'] = $user['id'];
    $_SESSION['x_url'] = $est['diretorio_interno'] ?? '';

    // Exemplo de redirecionamento por nome ou nível
    if ($user['nome'] === 'Ronaldo Oliveira da Silva') {
        header("Location: painel-adm/index2.php");
        exit;
    }

    // Exemplo para futuro: niveis de acesso
    // switch($user['nivel']) {
    //     case 'Administrador':
    //         header("Location: painel-adm");
    //         break;
    //     case 'Operador':
    //         header("Location: painel-operador");
    //         break;
    //     default:
    //         header("Location: painel-adm");
    // }
    header("Location: painel-adm");
    exit;
} else {
    // Nunca detalhe o erro ("senha ou usuário inválidos", sempre genérico)
    $_SESSION['login_error'] = "Usuário ou senha incorretos!";
    header("Location: index.php");
    exit;
}
?>
