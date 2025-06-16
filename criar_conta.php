<?php

//error_reporting(0);

//ligar à base de dados
include "ligar.php";

//mensagem de erro
$msg_erro="Introduza os dados!";

//inicializar as variáveis
$nome="";
$datan="";
$email="";

//verificar se clicou no botão 
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    //se clicou em criar conta
    if(isset($_POST['btn_criarconta'])){

     
        $nome=$_POST["nome"];
        $datan=$_POST["datan"];
        $email=$_POST["email"];
        $pass=$_POST["pass"];
        $pass2=$_POST["pass2"];

        //verificar se as password's são iguais
        if(strcmp($pass,$pass2)==0){

              //verificar se o email já está registado
              $sql="select * from utilizadores where email= '$email'";
              $result=mysqli_query($conn,$sql);
              $row=mysqli_fetch_array($result);

              if($row==0){
                
                //criar a conta
                 //encriptar a password
                $passwordencript=trim($pass);
                $password = password_hash($passwordencript, PASSWORD_BCRYPT);


                //criar a conta
                $sql1 = "INSERT INTO utilizadores (nome, email,pass,datan) VALUES ('$nome', '$email', '$password', '$datan')";

                $result=mysqli_query($conn,$sql1);
                                           
                //$msg_erro="Conta criada com sucesso!";
                echo "<script>window.location.href='login.php'</script>";
                exit();


              }else{
                  $msg_erro="Já existe uma conta com esse E-mail!";
              }


        }
        else{
                  $msg_erro="Confirme a palavra-passe!";
              }



    }


}


?>
<!DOCTYPE html>
<html lang="pt-PT">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>RitaCris</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Arsha
  * Template URL: https://bootstrapmade.com/arsha-free-bootstrap-html-template-corporate/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1 class="sitename">RitaCris</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="index.php#about">About</a></li>
          <li><a href="index.php#services">Services</a></li>
          <li><a href="index.php#portfolio">Portfolio</a></li>
          <li><a href="index.php#team">Team</a></li>
          <li><a href="index.php#pricing">Pricing</a></li>
          <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Dropdown 1</a></li>
              <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="#">Dropdown 2</a></li>
              <li><a href="#">Dropdown 3</a></li>
              <li><a href="#">Dropdown 4</a></li>
            </ul>
          </li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="login.php">Login</a>

    </div>
  </header>

  <main class="main">

    

   <!-- Clients Section -->
    <section id="clients" class="clients section dark-background">
    

      <div class="container">

          <p>&nbsp;</p>
          <p>&nbsp;</p>

      </div>

    </section><!-- /Clients Section -->

  
    <!-- Formulário  criar conta-->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>CRIAR CONTA</h2>
        <p>...</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-2">
          </div>

          <div class="col-lg-8">
            <!-- formulário  -->

       

              <form method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="php-email-form">
          

              <div class="row gy-4">

                <div class="col-md-8">
                  <label for="nome" class="pb-2">Nome</label>
                  <input type="text" name="nome" id="nome" maxlength="150" class="form-control"  value="<?php echo $nome;?>" required>
                </div>

                <div class="col-md-6">
                  <label for="datan" class="pb-2">Data Nascimento</label>
                  <input type="date" name="datan" id="datan" min="1900-01-01" max="<?php echo date("Y-m-d"); ?>" class="form-control" value="<?php echo $datan;?>"  required>
                </div>

                <div class="col-md-6">
                </div>

                <div class="col-md-6">
                  <label for="email" class="pb-2">E-mail</label>
                  <input type="email" class="form-control" name="email" id="email" value="<?php echo $email;?>" required>
                </div>

                 <div class="col-md-6">
                </div>

                <div class="col-md-6">
                  <label for="pass" class="pb-2">Palavra-passe</label>
                  <input type="password" class="form-control" name="pass" id="pass" minlength="12" required>
                </div>

                <div class="col-md-6">
                  <label for="pass2" class="pb-2">Confirmar a Palavra-passe</label>
                  <input type="password" class="form-control" name="pass2" id="pass2" minlength="12" required>
                </div>

        
                <div class="col-md-12 text-center">
                 
                 <p><?php echo $msg_erro;?></p>
                 
                      <button type="submit" name="btn_criarconta">Criar Conta</button>
                </div>

              </div>
            </form>
          </div><!-- fim formulário -->

          <div class="col-lg-2">
          </div>

        </div>

      </div>

    </section><!-- /Formulário de login -->

  </main>

  <footer id="footer" class="footer">

    <div class="footer-newsletter">
      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-lg-6">
            <h4>Join Our Newsletter</h4>
            <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
           <!-- <form action="forms/newsletter.php" method="post" class="php-email-form">
              <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
              <div class="loading">Loading</div>
              <div class="error-message"></div>
              <div class="sent-message">Your subscription request has been sent. Thank you!</div>
            </form>-->
          </div>
        </div>
      </div>
    </div>

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.php" class="d-flex align-items-center">
            <span class="sitename">Arsha</span>
          </a>
          <div class="footer-contact pt-3">
            <p>A108 Adam Street</p>
            <p>New York, NY 535022</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
            <p><strong>Email:</strong> <span>info@example.com</span></p>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Home</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">About us</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Services</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Terms of service</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Web Design</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Web Development</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Product Management</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Marketing</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12">
          <h4>Follow Us</h4>
          <p>Cras fermentum odio eu feugiat lide par naso tierra videa magna derita valies</p>
          <div class="social-links d-flex">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Arsha</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!--<script src="assets/vendor/php-email-form/validate.js"></script>-->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>