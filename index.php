<?php
session_start();
include 'ligarbd.php';
$mensagemdeerro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['btnlogin'])) {
        $codigo = mysqli_real_escape_string($conn, $_POST['codigo']);
        $password = $_POST['password'];

        // Verificar se o código está registado
        $sql = "SELECT * FROM clube WHERE codigo = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $codigo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_array($result);

        if ($row) {
            // Verificar a password
            $hashed_password = $row['password'];
            if (password_verify($password, $hashed_password)) {
                $_SESSION['clube'] = $row['codigo'];
                $_SESSION['mail'] = $row['email'];
                echo "<script>window.location.href='home.php'</script>";
                exit();
            } else {
                $mensagemdeerro = "Dados Inválidos. Consulte o administrador.";
            }
        } else {
            $mensagemdeerro = "Utilizador não registado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
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
              <a href="#!">
                <img src="imagens/ClubeMaster_pequeno.png" alt="ClubeMaster" width="175" height="57">
              </a>
            </div>
            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Sign in to your account</h2>

            <form id="form1" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <div class="row gy-2 overflow-hidden">
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" required>
                    <label for="Código" class="form-label">Código</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <label for="password" class="form-label">Password</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="d-flex gap-2 justify-content-between">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="" name="rememberMe" id="rememberMe">
                      <label class="form-check-label text-secondary" for="rememberMe">
                        Keep me logged in
                      </label>
                    </div>
                    <a href="recuperar_senha.php" class="link-primary text-decoration-none">Forgot password?</a>
                  </div>
                </div>
                <div class="col-12">
                  <div class="d-grid my-3">
                    <button class="btn btn-primary btn-lg" type="submit" name="btnlogin" id="btnlogin" style="background-color:#C41E3A;">Log in</button>
                  </div>
                </div>
                <div class="col-12 text-center">
                  <p class="m-0 text-secondary"><?php echo $mensagemdeerro; ?></p>
                </div>
                <div class="col-12 text-center mt-3">
                  <p class="m-0 text-secondary">Ainda não tem conta?</p>
                  <a href="registar_clube.php" class="btn mt-2" style="border: 1px solid #C41E3A; color: #C41E3A; padding: 6px 12px; border-radius: 5px; text-decoration: none;"
   onmouseover="this.style.backgroundColor='#C41E3A'; this.style.color='white';"
   onmouseout="this.style.backgroundColor='white'; this.style.color='#C41E3A';">
   Registar Clube
                  </a>

                </div>
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