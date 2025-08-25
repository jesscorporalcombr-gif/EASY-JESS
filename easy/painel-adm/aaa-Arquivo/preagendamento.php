<?php 
$pag = 'preagendamento';
@session_start();

//RECUPERAR DADOS DO USUÁRIO
$query = $pdo->query("SELECT * from usuarios WHERE id = '$_SESSION[id_usuario]'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_usu = $res[0]['nome'];
$email_usu = $res[0]['email'];
$senha_usu = $res[0]['senha'];
$nivel_usu = $res[0]['nivel'];
$cpf_usu = $res[0]['cpf'];
$id_usu = $res[0]['id'];

require_once('../conexao.php');
#require_once('verificar-permissao.php')

?>

<?php  gerarMenu($pag, $grupos); ?>
<h2>PRÉ AGENDAMENTO</h2>

       </br></br></br>

        <div class="container">
        	<div class="row">
          		<div class="col-md-10"> 
          			<div class="ol-md-12 text-center"> 

		            <h2 class="site-section-heading text-center font-secondary">Você sabia que na sua avaliação corporal, você ganha a Bioempedância? </h2></br>
		              <p>Com a Bioempedância você tem um parâmetro completo da sua estrutura corporal, com a leitura do TGC (taxa de gordura corporal) e o IMC (Índice metabólico corporal). 
		              Onde temos um parâmetro correto para indicação do seu tratamento.🥰🥰</p>
		              </br>
	          		  
	          		  <hr/>
	                               
                    </div>
                </div>
            </div>
        </div>


        



				



		