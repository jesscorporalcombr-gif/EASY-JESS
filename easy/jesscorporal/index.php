
<?php 
require_once("conexao.php");
 ?>
<!-------------------------------------------------------------------------
 ----------------------------------------------------------------------->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/css_login/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../img/logo.png" />

    <title>Login</title>

</head>

<body style="background-image: url('../img/images/1.jpg');"> 
<!--<body>-->

  <div class="container">
    <div class="header">
         <center><img src="../img/logo_login.png" width="60"></center> </br></br>
        <!--<h1>Login</h1>-->
    </div>

    <div class="main">
        <form class="" method="POST" action="autenticar.php">
            
            <span>          
                <input class="input" type="text" placeholder="Email" id="usuario" name="usuario" required="">
            </span>

            <span>              
                <input class="input" type="password" placeholder="Senha" id="senha" name="senha" required="">
            </span>
              
            <button  class="col">Entrar</button>

            <center>
                <label>
                    <a class="input"href="inscricao.php">Inscrever-se Gratuitamente</a>
                </label> 
            </center>

            </br></br></br>

            <center>
                <label>
                    <a style="color: #fb6d6d" class="input" href="#" data-toggle="modal" data-target="#modalRecuperar">Recuperar Senha?</a>
                </label> 
            </center>

<!--
             </br></br>

             <center>
                    <p style="color: white; font-size: 12px;">Login de Teste: admin@meuemail.com    Senha: 123</p>
                    <p style="color: white; font-size: 12px;">Login de Teste: cliente@meuemail.com    Senha: 123</p>

            </center>
-->
        </form>
    </div>
 </div>




  <!--<video autoplay muted loop>
    <source src="videos/video.mp4" >
 </video> -->

 </body>
</html>

    <script>

        function abrirPopUp(){
         janela = window.open('http://www.google.com.br', 'nova_janela', 'width=200, height=100' )
        }
        function fecharPopUp(){
          janela.close()
        }
        
    </script>






