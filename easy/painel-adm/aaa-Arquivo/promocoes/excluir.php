
 <?php 
require_once("../../conexao.php");

$id = $_POST['id'];

//BUSCAR A IMAGEM PARA EXCLUIR DA PASTA
$query_con = $pdo->query("SELECT * FROM promocoes WHERE id = '$id'");
$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
$imagem = $res_con[0]['foto'];
if($imagem != 'sem-foto.jpg'){
    unlink('../../img/promocoes/'.$imagem);
}

$query_con = $pdo->query("DELETE from promocoes WHERE id = '$id'");

echo 'Excluído com Sucesso!';

 ?>