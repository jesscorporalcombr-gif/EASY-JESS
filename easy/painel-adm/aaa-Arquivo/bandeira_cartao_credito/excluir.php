
 <?php 
require_once("../../conexao.php");

$id = $_POST['id'];

//BUSCAR A IMAGEM PARA EXCLUIR DA PASTA
$query_con = $pdo->query("SELECT * FROM bandeira_cartao_credito WHERE id = '$id'");
$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
$imagem = $res_con[0]['foto'];
if($imagem != 'sem-foto.jpg'){
    unlink('../../img/bandeira_cartao_credito/'.$imagem);
}

$query_con = $pdo->query("DELETE from bandeira_cartao_credito WHERE id = '$id'");

echo 'Excluído com Sucesso!';

 ?>