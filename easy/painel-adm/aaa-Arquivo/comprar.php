<?php 
////cartao presente cartao presente
gerarMenu($pag, $grupos);

?>






<!DOCTYPE html>
<html lang="en">
<head>

     <title>Compras</title>

     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=Edge">
     <meta name="description" content="">
     <meta name="keywords" content="">
     <meta name="author" content="">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

     <link rel="stylesheet" href="css/bootstrap.min.css">
     <link rel="stylesheet" href="css/font-awesome.min.css">
     <link rel="stylesheet" href="css/aos.css">
     <link rel="stylesheet" href="css/owl.carousel.min.css">
     <link rel="stylesheet" href="css/owl.theme.default.min.css">

     <!-- MAIN CSS -->
     <link rel="stylesheet" href="css/templatemo-digital-trend.css">


      <style> 

        input {
            color: var(--link-color);
            font-weight: normal;
            text-decoration: none;
          }

          input:hover, 
          input:active, 
          input:focus {
            color: var(--secondary-color);
            outline: none;
            text-decoration: none;
          }

          ::selection {
            background: var(--secondary-color);
            color: var(--white-color);
          }

          .section-padding {
            padding: 8em 0;
          }
          .section-padding-half {
            padding: 4em 0;
          }

          .google-map iframe {
            display: block;
            width: 100%;
          }
      </style>

</head>
<body>

    
     <!-- MENU BAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="">
              <!-- <i class="fa fa-line-chart"></i> -->
              Cartão Presente
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="https://www.jesscorporal.com/" target="_blank" class="nav-link smoothScroll">Sobre</a>
                    </li>
                    <li class="nav-item">
                        <a href="#protocolos" target="_blank" class="nav-link smoothScroll">Nosso Tratamentos</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://www.jesscorporal.com/blog" target="_blank" class="nav-link">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://web.whatsapp.com/send?phone=555185706133" target="_blank" class="nav-link contact">Contato</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php 

    require_once('../conexao/conexao.php');

    /*@$servico = $_POST['data1'];
    @$valor = $_POST['data2'];*/

    @$servico = $_GET['data1'];
    @$valor = $_GET['data2'];

    @$hoje = date('d/m/Y');

    /*gera números de 100000 a 9999999999.*/
    //@$numero = rand(100000,9999999999);

    ?>



     <!-- BLOG DETAIL -->
     <section class="project-detail section-padding-half">
          <div class="container">
               <div class="row">

                    <div class="col-lg-9 mx-auto col-md-10 col-12 mt-lg-5 text-center" data-aos="fade-up">
                      <h4 class="blog-category text-info">Seu Cartão Presente</h4>
                      
                      <h1>Jess Corporal</h1>

                      <div class="client-info">
                          <div class="d-flex justify-content-center align-items-center mt-3">
                            <img src="images/project/project-detail/male-avatar.png" class="img-fluid" alt="male avatar">

                            <h5><?php echo @$servico ?></br>  
                            Valor R$:  <?php echo @$valor ?></br> 
                            <!-- <?php echo @$hoje ?></h5> -->

                          </div>





                        <form id="contact-form" role="form" method="POST" action="registra_fat.php">

                            <div class="col-lg-9 mx-auto col-md-10 col-12 mt-lg-5 text-center" data-aos="fade-up">
                              
                              </br>
                              

                              <select data-width="100%" class="form-control mr-2" id="select2cli" name="nome1">
                                    <?php

                                    $query = "SELECT * FROM clientes ORDER BY nome asc";
                                    $result = mysqli_query($conexao, $query);

                                    if(mysqli_num_rows($result)){
                                      while($res_1 = mysqli_fetch_array($result)){
                                       ?>                                             
                                       <option value="<?php echo $res_1['id']; ?>"><?php echo $res_1['nome']; ?> </option> 
                                       <?php      
                                     }
                                   }
                                   ?>
                               </select>




s

                              <input type="hidden" name="servico" value="<?=$servico?>" />
                              <!--<input type="hidden" name="numero" value="<?=$numero?>" />-->
                              <input type="hidden" name="valor" value="<?=$valor?>" />
                              <input type="hidden" name="hoje" value="<?=$hoje?>" />

                              <!-- aqui pode já salvar e na proxima pagina faz um update usando
                              o numero da compra e o nome da pessoa como chave   -->
                                                                                     

                              <div class="col-lg-9 mx-auto col-md-10 col-12 mt-lg-5 text-center" data-aos="fade-up">
                                    <!--Botao para confirmar -->
                                    <input type="submit" class="form-control" name="send_message" value="Continuar">

                              </div>
                        </form>



                          </div>                          
                      </div>


                     
                    
                       <!--<div class="col-lg-9 mx-auto col-md-10 col-12 mt-lg-5 text-center" data-aos="fade-up">
                             
                             <a href="comprarClass.php?ser=<?php echo $servico ?>&val=<?php echo $valor ?>&nun=<?php echo $numero ?>&nome1=<?php echo $nome1 ?>&nome2=<?php echo $nome2 ?>"

                              target="_blank" class="custom-btn btn-bg btn mt-3" data-aos="fade-up" data-aos-delay="100">Confirmar Compra</a> </br></br>
                        </div> -->
                              
                        </div>

                    </div>
               </div>
          </div>
     </section>

    

     <!--  -->
     <section class="project-detail">
          <div class="container">
               <div class="row">
                  <div class="col-lg-9 mx-auto col-md-11 col-12 my-5 pt-3" data-aos="fade-up">

                    
                  </div>
               </div>                      
          </div>   
     </section>



     <footer class="site-footer">
      <div class="container">
        <div class="row">

          <div class="col-lg-5 mx-lg-auto col-md-8 col-10">
            <h1 class="text-white" data-aos="fade-up" data-aos-delay="100">Presenteie Quem Você Ama com a <strong>Jess Corporal</strong></h1>
          </div>

          <div class="col-lg-3 col-md-6 col-12" data-aos="fade-up" data-aos-delay="200">
            <h4 class="my-4">Contato</h4>

            <p class="mb-1">
              <i class="fa fa-phone mr-2 footer-icon"></i> 
              51 8570-6133
            </p>

            <p>
              <a href="#">
                <i class="fa fa-envelope mr-2 footer-icon"></i>
                contato@jesscorporal.com.br
              </a>
            </p>
          </div>

          <div class="col-lg-3 col-md-6 col-12" data-aos="fade-up" data-aos-delay="300">
            <h4 class="my-4">Nossa Clinica</h4>

            <p class="mb-1">
              <i class="fa fa-home mr-2 footer-icon"></i> 
              Av. Assis Brasil, 4550, Torre B - Centro Comercial ICON - Sarandi -
               Porto Alegre - RS. Sala 1402. CEP: 91010-004
            </p>
          </div>

          <div class="col-lg-4 mx-lg-auto text-center col-md-8 col-12" data-aos="fade-up" data-aos-delay="400">
            <p class="copyright-text">Copyright &copy; 2022 OnlySoft
            <br>
            <a rel="nofollow noopener" href="http://onlysoftinstituto.com/" target="_blank">OnlySoft Dev</a></p>
          </div>

          <div class="col-lg-4 mx-lg-auto col-md-6 col-12" data-aos="fade-up" data-aos-delay="500">
            
            <ul class="footer-link">
              <li><a href="#">home</a></li>
              <li><a href="https://www.jesscorporal.com/vagas" target="_blank">Trabalhe conosco</a></li>
              <li><a href="https://www.jesscorporal.com/promo" target="_blank">Promoções</a></li>
            </ul>
          </div>

          <div class="col-lg-3 mx-lg-auto col-md-6 col-12" data-aos="fade-up" data-aos-delay="600">
            <ul class="social-icon">

              <li><a href="https://www.instagram.com/jesscorporal.com.br/" target="_blank" class="fa fa-instagram"></a></li>

              <li><a href="https://www.facebook.com/jesscorporal.com.br" target="_blank" class="fa fa-facebook"></a></li>
              <li><a href="https://www.youtube.com/channel/UCEGPGtiv3t-AdKzb3H7DcFQ" target="_blank" class="fa fa-youtube"></a></li>

            </ul>
          </div>

        </div>
      </div>
    </footer>


     <!-- SCRIPTS -->
     <script src="js/jquery.min.js"></script>
     <script src="js/bootstrap.min.js"></script>
     <script src="js/aos.js"></script>
     <script src="js/owl.carousel.min.js"></script>
     <script src="js/smoothscroll.js"></script>
     <script src="js/custom.js"></script>

</body>
</html>