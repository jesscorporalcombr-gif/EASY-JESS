<?php 
$pag = 'kanban';
@session_start();

require_once('../conexao.php');
require_once('verificar-permissao.php');

 header("location: kanban/index.php");  // Direciona para outra pasta
 gerarMenu($pag, $grupos);
?>