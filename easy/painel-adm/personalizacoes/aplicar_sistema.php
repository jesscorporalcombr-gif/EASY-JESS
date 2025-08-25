<?php 
require_once("../../conexao.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$created = date('Y-m-d H:i:s');
$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['nome_usuario'];

$configuracao_nome = 'Negocio';

// Preparar os dados para inserir ou atualizar
$dados = [
    'nome' => $configuracao_nome,
    'cor_background' => $_POST['cor_background'],
    'cor_fonte_background' => $_POST['cor_fonte_background'],
    'cor_principal' => $_POST['cor_principal'],
    'cor_secundaria' => $_POST['cor_secundaria'],
    'cor_terciaria' => $_POST['cor_terciaria'],
    'cor_fonte_secundaria' => $_POST['cor_fonte_secundaria'],
    'cor_head_tabelas' => $_POST['cor_head_tabelas'],
    'cor_fonte_head_tabelas' => $_POST['cor_fonte_head_tabelas'],
    'cor_linha_impar' => $_POST['cor_linha_impar'],
    'cor_linha_par' => $_POST['cor_linha_par'],
    'cor_fonte_tabela' => $_POST['cor_fonte_tabela'],
    'cor_head_form' => $_POST['cor_head_form'],
    'cor_fonte_head_form' => $_POST['cor_fonte_head_form'],
    'cor_fundo_form' => $_POST['cor_fundo_form'],
    'cor_fonte_fundo_form' => $_POST['cor_fonte_fundo_form'],
    'cor_rodape_form' => $_POST['cor_rodape_form'],
    'cor_fonte_rodape_form' => $_POST['cor_fonte_rodape_form'],
    'cor_icons' => $_POST['cor_icons'],
    'cor_fonte_icons' => $_POST['cor_fonte_icons'],
    'cor_barra2' => $_POST['cor_barra2'],
    'cor_fonte_barra2' => $_POST['cor_fonte_barra2'],
    'size_icons' => $_POST['size_icons'],
    'espaco_entre_icons' => $_POST['espaco_entre_icons'],
    'align_icons' => $_POST['align_icons'],
    'cor_barra_topo' => $_POST['cor_barra_topo'],
    'cor_fonte_barra_topo' => $_POST['cor_fonte_barra_topo'],
    'cor_fonte_barra_topo2' => $_POST['cor_fonte_barra_topo2'],
    'cor_linha_barra' => $_POST['cor_linha_barra'],
    'size_icons_barra_topo' => $_POST['size_icons_barra_topo'],
    'cor_barra3' => $_POST['cor_barra3'],
    'cor_fonte_barra3' => $_POST['cor_fonte_barra3'],
    'size_icons_barra3' => $_POST['size_icons_barra3'],
    'cor_btn_add' => $_POST['cor_btn_add'],
    'cor_btn_enviar' => $_POST['cor_btn_enviar'],
    'cor_btn_fechar' => $_POST['cor_btn_fechar'],
    'cor_btn_padrao' => $_POST['cor_btn_padrao'],
    'cor_fonte_btn_add' => $_POST['cor_fonte_btn_add'],
    'cor_fonte_btn_enviar' => $_POST['cor_fonte_btn_enviar'],
    'cor_fonte_btn_fechar' => $_POST['cor_fonte_btn_fechar'],
    'cor_fonte_btn_padrao' => $_POST['cor_fonte_btn_padrao'],
];

$query = "SELECT COUNT(*) FROM personalizacao_sistema WHERE nome = :nome";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':nome', $configuracao_nome);
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count > 0) {
    // UPDATE se a configuração já existe
    $setPart = [];
    foreach ($dados as $key => $value) {
        $setPart[] = "$key = :$key";
    }
    $setPartString = implode(', ', $setPart);
    $query = "UPDATE personalizacao_sistema SET $setPartString WHERE nome = :nome";
} else {
    // INSERT se a configuração não existe
    $columns = implode(', ', array_keys($dados));
    $placeholders = ':' . implode(', :', array_keys($dados));
    $query = "INSERT INTO personalizacao_sistema ($columns) VALUES ($placeholders)";
}

$stmt = $pdo->prepare($query);

foreach ($dados as $key => &$val) {
    $stmt->bindParam(':'.$key, $val);
}

try {
    $stmt->execute();
    $response = ['success' => true, 'message' => 'Configuração salva com sucesso!'];
} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Erro ao salvar a configuração: ' . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
