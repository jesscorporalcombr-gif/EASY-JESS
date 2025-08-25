
 <?php 
require_once("../../conexao.php");

$id = $_POST['id'];

//BUSCAR A IMAGEM PARA EXCLUIR DA PASTA
$query_con = $pdo->query("SELECT * FROM contratos WHERE id = '$id'");
$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
$imagem = $res_con[0]['arquivo'];
if($imagem != 'sem-foto.jpg'){
    unlink('../../img/contratos/'.$imagem);
}

$query_con = $pdo->query("DELETE from contratos WHERE id = '$id'");

echo 'Excluído com Sucesso!';

 ?>