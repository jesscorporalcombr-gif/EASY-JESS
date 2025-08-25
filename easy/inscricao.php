
<?php 
require_once("conexao/conexao2.php");
 ?>
<!-------------------------------------------------------------------------
 ----------------------------------------------------------------------->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/css_login/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="img/logo.png" />

    <title>Inscrição</title>

</head>

<!--<body style="background-image: url('img/images/1.jpg');"> -->
<body>

  <div class="container">
    <div class="header">
         <center><img src="img/logo_login.png" width="25"></center> </br>
        <!--<h1>Login</h1>-->
    </div>

    <div class="main">
        <form class="" method="POST" action="processa.php">
            
            <span>          
                <center> <input type="text" class="input" placeholder="Digite seu nome completo" id="nome" name="nome" required="" >  </center>
            </span>

            <span>              
                 <center><input type="email" class="input" placeholder="Digite o seu email" id="email" name="email" required="">  </center>
            </span>

             <span>              
                 <center><input type="Password" class="input" placeholder="Escolha uma Senha" id="senha" name="senha" required="">  </center>
            </span>

            <button class="col">Cadastrar</button>
            </br></br>

            <label><a href="index.php">Voltar ao Login</a></label>

        </form>
    </div>
 </div>

  <video autoplay muted loop>
    <source src="videos/video_2.mp4" >
 </video>

 </body>
</html>

