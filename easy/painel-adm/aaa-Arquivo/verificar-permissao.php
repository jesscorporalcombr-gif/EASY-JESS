<?php 

//VERIFICAR PERMISSÃO DO USUÁRIO
if(@$_SESSION['nome_usuario'] == ''){
	echo "<script language='javascript'>window.location='../index.php'</script>";
}

 ?>