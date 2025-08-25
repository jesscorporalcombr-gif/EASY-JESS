
 <?php 
require_once("../../conexao.php");

$id = $_POST['id'];

//BUSCAR A IMAGEM PARA EXCLUIR DA PASTA
$query_con = $pdo->query("SELECT * FROM agenda_grupo_por_profissional WHERE id = '$id'");
$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
$imagem = $res_con[0]['foto'];
if($imagem != 'sem-foto.jpg'){
    unlink('../../img/agenda_grupo_por_profissional/'.$imagem);
}

$query_con = $pdo->query("DELETE from agenda_grupo_por_profissional WHERE id = '$id'");

echo 'Excluído com Sucesso!';

 ?>