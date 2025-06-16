<?php
session_start();
include 'ligarbd.php';


$mensagem = "";
$classe_mensagem = "alert-danger"; // Classe padrão para mensagens de erro


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    //se clicou em registar
    if(isset($_POST['btn_registar'])){

        //verificar se o código e o email já estão criados pelo administrador na BD

        $email=$_POST["email"];
        $codigo=$_POST["codigo"];

        $sql="select * from clube where email= '$email' and codigo='$codigo'";
        $result=mysqli_query($conn,$sql);
        $row=mysqli_fetch_array($result);

        if(!$row){
            $mensagem="Email e/ou Código inválidos. Se o erro persistir envie um email para apoioaocliente@clubemaster.com.";
        }else{
              $_SESSION['clube']=$row['codigo'];
              $_SESSION['mail']=$row['email'];
              echo "<script>window.location.href='definicoes.php'</script>";
              exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registar Clube - ClubeMaster</title>
    <link rel="stylesheet" href="css/estiloslogin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<section class="bg-light py-3 py-md-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
        <div class="card border border-light-subtle rounded-3 shadow-sm">
          <div class="card-body p-3 p-md-4 p-xl-5">
            <div class="text-center mb-3">
              <a href="index.php">
                <img src="imagens/ClubeMaster_pequeno.png" alt="ClubeMaster" width="175" height="57">
              </a>
            </div>
            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Registar Clube</h2>

            <div class="alert <?php echo $classe_mensagem; ?> text-center">
                <p>Para efetuar o registo, é necessário um código de acesso. O código deve ser pedido por email: apoioaocliente@clubemaster.com.</p>
              </div>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" required>
              </div>
              <div class="mb-3">
                <input type="text" class="form-control w-50" name="codigo" id="codigo" maxlength="6"  required>
                <div class="form-text">Introduza o código de acesso que recebeu via email.</div>
              </div>
              <script>
                  document.getElementById('codigo').addEventListener('input', function () {
                    this.value = this.value.replace(/\D/g, ''); // remove tudo o que não for dígito
                  });
             </script>
              <div class="d-grid my-3">
                <button class="btn btn-primary btn-lg" type="submit" style="background-color:#C41E3A;" name="btn_registar">Registar</button>
              </div>
              
              <?php if (!empty($mensagem)): ?>
              <div class="alert <?php echo $classe_mensagem; ?> text-center">
                <?php echo $mensagem; ?>
              </div>
              <?php endif; ?>
              
              <div class="text-center mt-3">
                <a href="index.php" class="btn btn-outline-secondary">Voltar ao Login</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>