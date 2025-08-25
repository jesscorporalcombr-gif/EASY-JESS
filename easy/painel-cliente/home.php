<?php 

@session_start();
require_once('../conexao.php');
require_once('verificar-permissao.php');

?>

<!DOCTYPE html>

<html lang="pt-BR">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Meu Painel</title>

  <!-- Custom Google fonts -->
  <link href='http://fonts.googleapis.com/css?family=Raleway:400,500,300,700' rel='stylesheet' type='text/css'>
  <link href="http://fonts.googleapis.com/css?family=Crimson+Text:400,600" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css">

  <!-- Bootstrap CSS Style -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">

  <!-- Template CSS Style -->
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Animate CSS  -->
  <link rel="stylesheet" href="assets/css/animate.css">

  <!-- FontAwesome 4.3.0 Icons  -->
  <link rel="stylesheet" href="assets/css/font-awesome.min.css">

  <!-- et line font  -->
  <link rel="stylesheet" href="assets/css/et-line-font/style.css">

  <!-- BXslider CSS  -->
  <link rel="stylesheet" href="assets/css/bxslider/jquery.bxslider.css">

  <!-- Owl Carousel CSS Style -->
  <link rel="stylesheet" href="assets/css/owl-carousel/owl.carousel.css">
  <link rel="stylesheet" href="assets/css/owl-carousel/owl.theme.css">
  <link rel="stylesheet" href="assets/css/owl-carousel/owl.transitions.css">

  <!-- Magnific-Popup CSS Style -->
  <link rel="stylesheet" href="assets/css/magnific-popup/magnific-popup.css">

  <link rel="shortcut icon" href="assets/images/logo.png" />



  

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
	
</head>
<body>


  <!-- Preload the Whole Page
  <div id="preloader">      
    <div id="loading-animation">&nbsp;</div>
  </div>  -->


  <div id="wrapper">

  <!-- Hero
  ================================================== -->
    <section>
      <div id="hero-section" class="landing-hero" data-stellar-background-ratio="0">
        <div class="hero-content">
          <div class="container">
            <div class="row">
              <div class="col-sm-8 col-sm-offset-2">

                <div class="hero-text">
                  <div class="herolider">
                    <ul class="caption-slides">

                      <li class="caption">
                        <h1>REFERÊNCIA EM MASSAGENS</h1>
                        <div class="div-line"></div>
                        <p class="hero">Competência  &amp; Eficiência</p>
                      </li>

                      <li class="caption">
                        <h1>ALTA TECNOLOGIA</h1>
                        <div class="div-line"></div>
                        <p class="hero">Referencia &amp; Alto Nível</p>
                      </li>

                      <li class="caption">
                        <h1>MASSAGENS DE ALTA PERFORMANCE</h1>
                        <div class="div-line"></div>
                        <p class="hero">Resultados  &amp; Competência</p>
                      </li>

                    </ul>
                  </div> <!-- end herolider -->
                </div> <!-- end hero-text -->

                <div class="hero-btn">
                  <a href="#landing-offer" class="btn btn-clean">Ler Mais</a>
                </div> <!-- end hero-btn -->

              </div> <!-- end col-md-6 -->
            </div> <!-- end row -->
          </div> <!-- End container -->
        </div> <!-- End hero-content -->
      </div> <!-- End hero-section -->
    </section>
    <!-- End hero section -->

    <!-- Offer
    ================================================== -->
    <section>
      <div id="landing-offer" class="pad-sec">
        <div class="container">

          <div class="title-section big-title-sec animated out" data-animation="fadeInUp" data-delay="0">
            <div class="row">
              <div class="col-sm-8 col-sm-offset-2">
                <h2 class="big-title">
                Sobre nós
              </h2>

                <h1 class="big-subtitle">
                  Somos uma equipe de excelência 
                </h1>
                <hr>

                <p class="about-text">
                    Através dessa aplicação buscamos lhe conectar com suas principais informações e tratamentos de forma fácil e eficaz. Este é o seu painel pessoal.
                </p></br>

                <p class="about-text">
                  Com uma localização em um ponto de fácil acesso da Zona Norte de Porto Alegre, estamos prontos para receber você em nossa Clínica!

                  Estamos te esperando com o que há de mais novo no mercado da estética e como compromisso de entregar resultados além das expectativas.
                   </p>

                  
              </div>
            </div> <!-- End row -->
          </div> <!-- end title-section -->




          

          <div class="offer-boxes">
            
                 <center><h4>
                  Sua Aplicação
                </h4></center></br>

            <div class="row">
            <div class="col-sm-4">
              <div class="offer-post text-center animated out" data-animation="fadeInLeft" data-delay="0">

                <div class="offer-icon">
                  <span class="icon-desktop"></span>
                </div>

                <h4>INTERFACE SIMPLIFICADA</h4>

                <p>
                  Interface pensada para simplifica seu acesso e maximizar seu tempo em consultas e agendamentos.
                </p>

              </div> <!-- End offer-post -->
            </div> <!-- End col-sm-4 -->

            <div class="col-sm-4">
              <div class="offer-post text-center animated out" data-animation="fadeInUp" data-delay="0">
                <div class="offer-icon">
                  <span class="icon-piechart"></span>
                </div>

                <h4>SEUS RESULTADOS</h4>

                <p>
                Seus resultados reunidos para que possa ter uma melhor analise sobre seus avanços nos tratamentos.
              </p>

              </div> <!-- End offer-post -->
            </div> <!-- End col-sm-4 -->

            <div class="col-sm-4">
              <div class="offer-post text-center animated out" data-animation="fadeInRight" data-delay="0">
                <div class="offer-icon">
                  <span class="icon-lifesaver"></span>
                </div>

                <h4>INTEGRAÇÃO COM A EQUIPE</h4>

                <p>
                 Um canal para que possa tirar suas duvidas direto com a equipe e profissionais que ministram os tratamentos.
               </p>

              </div> <!-- End offer-post -->
            </div> <!-- End col-sm-4 -->

            </div> <!-- End row -->

          </div> <!-- End offer-boxes -->
        </div> <!-- End container -->
      </div> <!-- End landing-offer-section -->
    </section>
    <!-- End offer section -->

    <section>
      <div class="sep-section"></div>
    </section>

    <!-- Features services
    ================================================== -->
    <section>
      <div id="features-section" class="pad-sec">
        <div class="container">
          <div class="title-section text-center animated out" data-animation="fadeInUp" data-delay="0">
            <div class="row">
              <div class="col-sm-8 col-sm-offset-2">
                <h2>E que seu painel pode fazer? </h2>
                <hr>
                <p>
                  Essa é uma área que você pode ter acesso a todas suas informações e resultados, assim como um contato direto com nossa equipe.
                </p>
              </div> <!-- edn col-sm-8 -->
            </div> <!-- End row -->
          </div> <!-- end title-section -->
          <div class="row">
            <div class="col-md-3 without-padding">
              <div class="left-features-services">
                <ul class="features-list">
                  <!-- feature -->
                  <li>
                    <div class="left-features-box animated out" data-animation="fadeInLeft" data-delay="0">
                      <span class="iconbox"><i class="icon-mobile"></i></span>
                      <div class="features-box-content">
                        <h6>Uso no Mobile</h6>
                        <p>Pode acessar pelo smartphone</p>
                      </div> <!-- end features-box-content -->
                    </div> <!-- end left-features-box -->
                  </li>
                  <!-- feature -->
                  <li>
                    <div class="left-features-box animated out" data-animation="fadeInLeft" data-delay="0">
                      <span class="iconbox"><i class="icon-browser"></i></span>
                      <div class="features-box-content">
                        <h6>Acessível no PC</h6>
                        <p>Também por qualquer outro dispositivo</p>
                      </div> <!-- end features-box-content -->
                    </div> <!-- end left-features-box -->
                  </li>
                  <!-- feature -->
                  <li>
                    <div class="left-features-box animated out" data-animation="fadeInLeft" data-delay="0">
                      <span class="iconbox"><i class="icon-strategy"></i></span>
                      <div class="features-box-content">
                        <h6>Vantagem nas Promoções </h6>
                        <p>Recebera em primeira mão as promoções </p>
                      </div> <!-- end features-box-content -->
                    </div> <!-- end left-features-box -->
                  </li>
                  <!-- feature -->
                  <li>
                    <div class="left-features-box animated out" data-animation="fadeInLeft" data-delay="0">
                      <span class="iconbox"><i class="icon-hotairballoon "></i></span>
                      <div class="features-box-content">
                        <h6>Prioridade na Agenda</h6>
                        <p>Terá prioridade nos horários e agendamentos</p>
                      </div> <!-- end features-box-content -->
                    </div> <!-- end left-features-box -->
                  </li>
                  <!-- feature -->
                  <li>
                    <div class="left-features-box animated out" data-animation="fadeInLeft" data-delay="0">
                      <span class="iconbox"><i class="icon-lightbulb"></i></span>
                      <div class="features-box-content">
                        <h6>Dicas e Conteúdo</h6>
                        <p>Receba conteúdos exclusivos para te auxiliar</p>
                      </div> <!-- end features-box-content -->
                    </div> <!-- end left-features-box -->
                  </li>
                </ul> <!-- end features-list -->
              </div> <!-- end left-features-service -->
            </div><!--  end col-md-3 -->

            <div class="col-md-6">
              <div class="features-image animated out" data-animation="fadeInUp" data-delay="0">
                <img src="assets/images/temp/woman.jpg" alt="">
              </div> <!-- end features-image -->
            </div> <!-- end col-md-6 -->

            <div class="col-md-3 without-padding">
              <div class="right-features-services">
                <ul class="features-list">
                  <!-- feature -->
                  <li>
                    <div class="right-features-box animated out" data-animation="fadeInRight" data-delay="0">
                      <span class="iconbox"><i class="icon-tools-2"></i></span>
                      <div class="features-box-content">
                        <h6>Uso Simplificado </h6>
                        <p>Encontre tudo com poucos cliques</p>
                      </div>
                    </div>
                  </li>
                  <!-- feature -->
                  <li>
                    <div class="right-features-box animated out" data-animation="fadeInRight" data-delay="0">
                      <span class="iconbox"><i class="icon-video"></i></span>
                      <div class="features-box-content">
                        <h6>Aviso de Novidades</h6>
                        <p>Receba notificações sobre seus agendamentos </p>
                      </div>
                    </div>
                  </li>
                  <!-- feature -->
                  <li>
                    <div class="right-features-box animated out" data-animation="fadeInRight" data-delay="0">
                      <span class="iconbox"><i class="icon-camera"></i></span>
                      <div class="features-box-content">
                        <h6>Acesso a sua Galeria </h6>
                        <p>Confira sua galeria pessoal de resultados</p>
                      </div>
                    </div>
                  </li>
                  <!-- feature -->
                  <li>
                    <div class="right-features-box animated out" data-animation="fadeInRight" data-delay="0">
                      <span class="iconbox"><i class="icon-presentation"></i></span>
                      <div class="features-box-content">
                        <h6>Seus Índices e Conquistas</h6>
                        <p>Estatísticas sobre seus progressos</p>
                      </div>
                    </div>
                  </li>
                  <!-- feature -->
                  <li>
                    <div class="right-features-box animated out" data-animation="fadeInRight" data-delay="0">
                      <span class="iconbox"><i class="fa fa-life-ring"></i></span>
                      <div class="features-box-content">
                        <h6>Suporte</h6>
                        <p>Um canal direto com a equipe de desenvolvedores</p>
                      </div>
                    </div>
                  </li>
                </ul> <!-- end features-list -->
              </div>
            </div> <!-- end col-md-3 -->

          </div> <!-- end row -->
        </div> <!-- end container -->
      </div>
    </section>
    <!-- End features-section -->


     <!-- Video section
    ================================================== -->
   

   <! <section>
      <div id="video-section" data-stellar-background-ratio="0">
        <div class="container">
          <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
              <div class="video-section-content text-center">
                <a href="https://meueasy.com/show/02.mp4" class="video-pop-up"><i class="fa fa-play"></i></a>
                <div class="video-head">Veja Mais</div>
                <div class="video-sub-heading">Esse time é totalmente comprometido em lhe trazer os melhores resultados </div>

              </div>
            </div> 
          </div> 
        </div> 
      </div>
    </section>


    <!-- End Video section -->

   
    <!-- Team
    ================================================== -->
    <section>
      <div id="team-section" class="pad-sec">
        <div class="container">
          <div class="title-section animated out" data-animation="fadeInUp" data-delay="0">
            <div class="row">
              <div class="col-sm-8 col-sm-offset-2">
                <h2>NOSSA EQUIPE INCRÍVEL</h2>
                <hr>
                <p>Nossa equipe é composta por profissionais altamente capacitados referencias em suas áreas de atuação. Todos os colaborados engajados conosco seguem nossa visão de entregar o melhor para os pacientes.</p>
              </div>
            </div> <!-- End row -->
          </div> <!-- end title-section -->

          <div class="team-members">
            <div class="row">

              <!-- member-team -->
              <div class="col-sm-4">
                <div class="member-team animated out" data-animation="fadeInLeft" data-delay="0">
                  <img src="assets/images/team/m1.jpg" alt="">
                  <div class="magnifier">
                    <div class="magnifier-inner">
                      <h3>Cris</h3>
                      <p>Fisioterapeuta</p>
                      
                      <!--<p>
                      Descrição da profissional
                    </p> -->
                      <ul class="social-icons">  

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Facebook" href="" data-original-title="" title=""><i class="fa fa-facebook"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Twitter" href="#" data-original-title="" title=""><i class="fa fa-twitter"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Google Plus" href="#" data-original-title="" title=""><i class="fa fa-google-plus"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Skype" href="#" data-original-title="" title=""><i class="fa fa-skype"></i></a></li>

                      </ul>
                    </div> <!-- End magnifier-inner -->
                  </div> <!-- End magnifier -->
                </div> <!-- End member-team -->
              </div> <!-- End col-sm-4 -->

              <!-- member-team -->
              <div class="col-sm-4">
                <div class="member-team animated out" data-animation="fadeInUp" data-delay="0">
                  <img src="assets/images/team/m2.jpg" alt="">
                  <div class="magnifier">
                    <div class="magnifier-inner">
                      <h3>Jessica Rodrigues</h3>
                      <p>Diretora</p>
                      
                      <!--<p>
                      Descrição da profissional
                    </p> -->
                      <ul class="social-icons">

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Facebook" href="#" data-original-title="" title=""><i class="fa fa-facebook"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Twitter" href="#" data-original-title="" title=""><i class="fa fa-twitter"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Google Plus" href="#" data-original-title="" title=""><i class="fa fa-google-plus"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Skype" href="#" data-original-title="" title=""><i class="fa fa-skype"></i></a></li>

                      </ul>
                    </div> <!-- End magnifier-inner -->
                  </div> <!-- End magnifier -->
                </div> <!-- End member-team -->
              </div> <!-- End col-sm-4 -->

              <!-- member-team -->
              <div class="col-sm-4">
                <div class="member-team animated out" data-animation="fadeInRight" data-delay="0">
                  <img src="assets/images/team/m3.jpg" alt="">
                  <div class="magnifier">
                    <div class="magnifier-inner">
                      <h3>Tai</h3>
                      <p>Masso Terapeuta</p>
                      
                      <!--<p>
                      Descrição da profissional
                    </p>-->
                      <ul class="social-icons">

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Facebook" href="#" data-original-title="" title=""><i class="fa fa-facebook"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Twitter" href="#" data-original-title="" title=""><i class="fa fa-twitter"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Google Plus" href="#" data-original-title="" title=""><i class="fa fa-google-plus"></i></a></li>

                        <li><a data-rel="tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" data-title="Skype" href="#" data-original-title="" title=""><i class="fa fa-skype"></i></a></li>

                      </ul>
                    </div> <!-- End magnifier-inner -->
                  </div> <!-- End magnifier -->
                </div> <!-- End member-team -->
              </div> <!-- End col-sm-4 -->

            </div>
          </div> <!-- End team-members -->
        </div> <!-- End container -->
      </div> <!-- End team-section -->
    </section>
    <!-- End team section -->

    <!-- About Team
    ================================================== -->
    <section>
      <div id="about-team">
        <div class="container">
            <div class="row">

              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="title-section">
                  <h3>Competências da nossa equipe</h3>
                </div>
                <p>
                A Jess Corporal é sinônimo de responsabilidade e saúde.
                Nossos tratamentos exclusivos são realizados por mãos experientes que colocam seu bem-estar em primeiro lugar.

              </p>
              </div> <!-- end col-md-6 -->

              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="progress-bars">
                  
                  <!-- skillbar -->
                  <div class="p-bar">
                    <!-- meta -->
                    <div class="progress-meta">
                      <h6 class="progress-title">Ética</h6>
                      <h6 class="progress-value">100%</h6>
                    </div>

                    <div class="progress">
                      <div class="progress-bar" aria-valuenow="100" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                  </div> <!-- end p-bar -->

                  <!-- skillbar -->
                  <div class="p-bar">
                    <!-- meta -->
                    <div class="progress-meta">
                      <h6 class="progress-title">Empatia</h6>
                      <h6 class="progress-value">100%</h6>
                    </div>

                    <div class="progress">
                      <div class="progress-bar" aria-valuenow="100" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                  </div> <!-- end p-bar -->

                  <!-- skillbar -->
                  <div class="p-bar">
                    <!-- meta -->
                    <div class="progress-meta">
                      <h6 class="progress-title">Confiança</h6>
                      <h6 class="progress-value">100%</h6>
                    </div>

                    <div class="progress">
                      <div class="progress-bar" aria-valuenow="100" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                  </div> <!-- end p-bar -->

                  <!-- skillbar -->
                  <div class="p-bar">
                    <!-- meta -->
                    <div class="progress-meta">
                      <h6 class="progress-title">Comprometimento</h6>
                      <h6 class="progress-value">100%</h6>
                    </div>

                    <div class="progress">
                      <div class="progress-bar" aria-valuenow="100" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                  </div> <!-- end p-bar -->

                </div> <!-- end skills-bars -->
              </div> <!-- end col-md-6 -->
            </div> <!-- end row -->
        </div>
      </div>
    </section>
    <!-- End About Team -->

    <!-- Banner-Services
    ================================================== -->
    <section>
      <div id="banner-services" data-stellar-background-ratio="0">
        <div class="container">
          <div class="row">

            <div class="col-sm-6">
              <div class="banner-content animated out" data-animation="fadeInUp" data-delay="0">
                <h3 class="banner-heading">Acompanhe nossas redes sociais e fique por dentro do nosso dia a dia </h3>
                <div class="banner-decription">
                  Quer conhecer melhor a nossa rotina e ficar por dentro das novidades? Nos acompanhe nas Redes Sociais!
                  Todos os dias nós atualizamos o nosso Facebook, Instagram e Blog , com várias Dicas, noticias, e muito mais.

                </div> <!-- end banner-decription -->
                <div>
                  <a href="https://www.instagram.com/jesscorporal.com.br/" target="_blank" class="btn btn-sm btn-clean">Vamos lá!</a>
                </div>
              </div> <!-- end banner-content -->
            </div> <!-- end col-sm-6 -->

            <div class="col-sm-6">
              <div class="banner-image animated out" data-animation="fadeInUp" data-delay="0">
                <img src="assets/images/temp/banner-img.png" alt="">
              </div> <!-- end banner-image -->
            </div> <!-- end col-sm-6 -->
            
          </div> <!-- end row -->
        </div> <!-- end container -->
      </div>
    </section>
    <!-- End Banner services section -->

   

    <section>
      <div class="sep-section"></div>
    </section>

   

    <!-- Screenshots
    ================================================== -->
      <section>
        <div id="screenshots-section" class="pad-sec">
          <div class="container">
           <div class="title-section text-center animated out" data-animation="fadeInUp" data-delay="0">
              <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                  <h2>Alguns Resulatados</h2>
                  <hr>
                  <p>
                    Acompanhe alguns de nossos resultados incríveis. Nossas pacientes estão cada vez mais felizes com suas conquistas.
                  </p>
              </div>
            </div> <!-- End row -->
          </div> <!-- end title-section -->




            <div class="row">
              <div class="col-md-12">
                <div class="screenshots-carousel animated out" data-animation="fadeInUp" data-delay="0">

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/1.jpg"><img src="assets/images/screenshots/1.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/2.jpg"><img src="assets/images/screenshots/2.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/3.jpg"><img src="assets/images/screenshots/3.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/4.jpg"><img src="assets/images/screenshots/4.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/5.jpg"><img src="assets/images/screenshots/5.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/3.jpg"><img src="assets/images/screenshots/3.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->


                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/1.jpg"><img src="assets/images/screenshots/1.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/2.jpg"><img src="assets/images/screenshots/2.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/3.jpg"><img src="assets/images/screenshots/1.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/1.jpg"><img src="assets/images/screenshots/1.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/2.jpg"><img src="assets/images/screenshots/2.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->

                  <div class="shot">
                    <div class="screen">
                      <a class="zoom" href="assets/images/screenshots/3.jpg"><img src="assets/images/screenshots/3.jpg" alt="screenshot"></a>
                    </div> <!-- end screen -->
                  </div> <!-- end shot -->



                </div> <!-- end screenshots-carousel -->
              </div> <!-- end col-md-12 -->
            </div> <!-- end row -->



          </div><!--  end container -->
        </div>
      </section>
      <!-- End screenshots-section -->

      <!-- Clients-section
    ================================================== -->
    <section>
      <div id="clients-section" class="clients-bg" data-stellar-background-ratio="0">
        <div class="container">
          <div class="row">

                  <center>
                      <img style=" width: 220px;" src="assets/images/clients/client_3.png" alt="">
                  </center>
         

          </div> <!-- End row -->
        </div> <!-- End container -->
      </div> 
    </section>
    <!-- End clients-section -->

   


    <section>
      <div class="sep-section"></div>
    </section>

      <!-- Testimonial
      ================================================== -->      
      <section>
        <div id="testimonials-section">
          <div class="container">
            <div class="row">
              <div class="col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">

                <div class="testimonials-carousel">

                  <ul class="testimonials-slider">
                    
                    <!-- Testimonial -->
                    <li>
                      <img src="assets/images/temp/client-photo1.jpg" alt="">
                      <p>
                      Estou fazendo as massagens FHL e os resultados são visíveis! Desde a primeira sessão a pele está visivelmente mais lisa. Os edemas junto ao joelho também diminuíram bastante.
                    </p>
                      <div class="testimonials-author"> MARIA ISABEL MARCARELL</div>
                    </li>
                    
                    <!-- Testimonial -->
                    <li>
                      <img src="assets/images/temp/client-photo2.jpg" alt="">
                      <p>
                        Estou muito feliz por ter conhecido a Jess corporal. Além de ser sempre muito bem atendida, o tratamento escolhido foi feito com muito cuidado e sob medida para minhas necessidades.
                      </p>
                      <div class="testimonials-author">DÉBORA MENGER </div>
                    </li>
                    
                    <!-- Testimonial -->
                    <li>
                      <img src="assets/images/temp/client-photo3.jpg" alt="">
                      <p>Descobri o trabalho da Jess por uma postagem, o que me levou a conhecer e me apaixonar pelo trabalho, cuidado e competência.</p>
                      <div class="testimonials-author">ANA PAULA TELÓ BELLISSI</div>
                    </li>

                  </ul>

                  <div class="tc-arrows">
                     <div class="tc-arrow-left"></div>
                     <div class="tc-arrow-right"></div>
                  </div> <!-- end tc-arrows -->
                </div> <!-- end testimonials-carousel -->

              </div> <!-- end col-md-8 -->
            </div> <!-- end row -->
          </div> <!-- end container -->
        </div>
      </section>
      <!-- end testimonial section -->

    <section>
      <div class="sep-section"></div>
    </section>

    <!-- Google map
    ================================================== -->
    <section>
      
        <center>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3455.02654447729!2d-51.14405158488578!3d-30.007394181895805!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95197708f81adbe9%3A0xa9a052257d53f816!2sJess%20Corporal%20-%20Cl%C3%ADnica%20de%20Est%C3%A9tica!5e0!3m2!1spt-BR!2sbr!4v1683672102385!5m2!1spt-BR!2sbr" width="1350" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </center>

 </section>
    <!-- End Google map -->


  

   

  </div> <!-- End wrapper -->

  <!-- Back-to-top
  ================================================== -->
  <div class="back-to-top">
    <i class="fa fa-angle-up fa-3x"></i>
  </div> <!-- end back-to-top -->

  <!-- JS libraries and scripts -->
  <script src="assets/js/jquery-1.11.3.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
  <script src="assets/js/jquery.appear.min.js"></script>
  <script src="assets/js/jquery.bxslider.min.js"></script>
  <script src="assets/js/jquery.owl.carousel.min.js"></script>
  <script src="assets/js/jquery.countTo.js"></script>
  <script src="assets/js/jquery.easing.1.3.js"></script>
  <script src="assets/js/jquery.imagesloaded.min.js"></script>
  <script src="assets/js/jquery.isotope.js"></script>
  <script src="assets/js/jquery.placeholder.js"></script>
  <script src="assets/js/jquery.smoothscroll.js"></script>
  <script src="assets/js/jquery.stellar.min.js"></script>
  <script src="assets/js/jquery.waypoints.js"></script>
  <script src="assets/js/jquery.fitvids.js"></script>
  <script src="assets/js/jquery.magnific-popup.min.js"></script>
  <script src="assets/js/jquery.ajaxchimp.min.js"></script>
  <script src="assets/js/jquery.countdown.js"></script>
  <script src="assets/js/jquery.navbar-scroll.js"></script>
  <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <script src="assets/js/jquery.gmaps.js"></script>
  <script src="assets/js/main.js"></script>





</body>
</html>