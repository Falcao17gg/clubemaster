<?php
// recuperar_senha.php
include 'ligarbd.php';
session_start();
$mensagem = "";
$classe_mensagem = "alert-danger";
$successo = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = mysqli_real_escape_string($conn, $_POST['codigo']);

    // Verificar se o código existe
    $sql = "SELECT * FROM clube WHERE codigo = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $codigo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $email = $row['email'];
        
        // Gerar link para redefinir senha - Usar caminho absoluto para localhost
        $link_redefinir = "http://localhost/ClubeMaster/definir_senha.php?codigo=" . urlencode($codigo);
        
        // Como estamos em localhost, não tentamos enviar e-mail
        // Em vez disso, mostramos o link diretamente
        $mensagem = "Em um ambiente de produção, um email seria enviado para <strong>" . htmlspecialchars($email) . "</strong> com instruções para redefinir a senha.";
        $mensagem .= "<div class='alert alert-info mt-3'>Para fins de teste, use este link para redefinir sua senha: <br>";
        $mensagem .= "<a href='" . $link_redefinir . "'>" . $link_redefinir . "</a></div>";
        $classe_mensagem = "alert-success";
        $successo = true;
    } else {
        $mensagem = "Código não encontrado no sistema!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Senha - ClubeMaster</title>
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
            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Recuperar Senha</h2>
            
            <?php if ($successo): ?>
                <div class="alert <?php echo $classe_mensagem; ?> text-center" role="alert">
                    <?php echo $mensagem; ?>
                </div>
                <div class="text-center mt-3">
                    <a href="index.php" class="btn btn-outline-secondary">Voltar ao Login</a>
                </div>
            <?php else: ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código do Clube</label>
                        <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Insira o código do clube" required>
                        <div class="form-text">Introduza o código do clube para recuperar sua senha.</div>
                    </div>
                    
                    <div class="d-grid my-3">
                        <button type="submit" class="btn btn-primary btn-lg" style="background-color:#C41E3A;">Recuperar Senha</button>
                    </div>
                    
                    <?php if (!empty($mensagem)): ?>
                        <div class="alert <?php echo $classe_mensagem; ?> text-center" role="alert">
                            <?php echo $mensagem; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="btn btn-outline-secondary">Voltar ao Login</a>
                    </div>
                </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>