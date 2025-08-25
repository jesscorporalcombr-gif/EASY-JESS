

 <?php 
require_once("../../conexao.php");

$id = $_POST['id'];

//BUSCAR A IMAGEM PARA EXCLUIR DA PASTA
$query_con = $pdo->query("SELECT * FROM contas_contabeis WHERE id = '$id'");
$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
$imagem = $res_con[0]['arquivo'];
if($imagem != 'sem-foto.jpg'){
    unlink('../../img/contas_contabeis/'.$imagem);
}

$query_con = $pdo->query("DELETE from contas_contabeis WHERE id = '$id'");

echo 'Excluído com Sucesso!';

 ?>