<?php
// definir_senha.php
include "ligarbd.php";
session_start();

$codigo = isset($_GET['codigo']) ? mysqli_real_escape_string($conn, $_GET['codigo']) : null;
$mensagem = "";
$formulario_visivel = false;

if ($codigo) {
    // Verifica se o clube com o código existe
    $sql = "SELECT * FROM clube WHERE codigo = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $codigo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $formulario_visivel = true;
    } else {
        $mensagem = "Link inválido. Por favor, volte e tente novamente.";
    }
} else {
    $mensagem = "Dados não fornecidos.";
}

// Processar o formulário de redefinição de senha
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nova_senha'])) {
    $codigo = mysqli_real_escape_string($conn, $_POST['codigo']);
    $nova_senha = mysqli_real_escape_string($conn, $_POST['nova_senha']);
    
    if (empty($codigo) || empty($nova_senha)) {
        $mensagem = "Todos os campos são obrigatórios.";
    } else {
        // Gerar o hash da nova senha
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // Atualizar a senha na base de dados
        $sql_update = "UPDATE clube SET password = ? WHERE codigo = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "ss", $hash, $codigo);
        
        if (mysqli_stmt_execute($stmt_update)) {
            // Redirecionar para o login com mensagem de sucesso
            header("Location: index.php?senha_atualizada=1");
            exit();
        } else {
            $mensagem = "Erro ao atualizar senha. Por favor, tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - ClubeMaster</title>
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
            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Redefinir Senha</h2>
            
            <?php if (!$formulario_visivel): ?>
                <div class="alert alert-danger text-center">
                    <?php echo $mensagem; ?>
                </div>
                <div class="text-center mt-3">
                    <a href="index.php" class="btn btn-outline-secondary">Voltar ao Login</a>
                </div>
            <?php else: ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <!-- Campo oculto com o código -->
                    <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($codigo); ?>">
                    <div class="mb-3">
                        <label for="nova_senha" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" name="nova_senha" id="nova_senha" required>
                    </div>
                    <div class="d-grid my-3">
                        <button class="btn btn-primary" type="submit" style="background-color:#C41E3A;">Redefinir Senha</button>
                    </div>
                    
                    <?php if (!empty($mensagem)): ?>
                        <div class="alert alert-danger text-center">
                            <?php echo $mensagem; ?>
                        </div>
                    <?php endif; ?>
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