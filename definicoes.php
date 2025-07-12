<?php
//iniciar sessão para verificar se o cliente fez login
session_start();

//ligar à base de dados
include 'ligarbd.php';

// validar sessão 
if (isset($_SESSION['clube'])) {
    $login = true;
    $codigo_clube = $_SESSION['clube'];
} else {
    $login = false;
    echo "<script>window.location.href='index.php'</script>";
    exit();
}

// Inicializar variáveis
$msg = "";
$nome_clube = "";
$nome_utilizador = "";
$email = "";
$morada = "";
$imagem = "no_image.png";

// Buscar informações do clube
$sql = "SELECT * FROM clube WHERE codigo = '$codigo_clube'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
    $clube = mysqli_fetch_assoc($result);
    $nome_clube = $clube['nome_clube'];
    $nome_utilizador = $clube['nome_utilizador'];
    $email = $clube['email'];
    $morada = $clube['morada'];
    $imagem = $clube['imagem'];
}

// Processar formulário de atualização
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_atualizar'])) {
        $nome_clube = mysqli_real_escape_string($conn, $_POST['nome_clube']);
        $nome_utilizador = mysqli_real_escape_string($conn, $_POST['nome_utilizador']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $morada = mysqli_real_escape_string($conn, $_POST['morada']);
        $imagem_atual = $_POST['imagem_atual'];
        
        // Verificar se foi enviada uma nova imagem
        if(isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
            $nomefoto = time() . basename($_FILES["imagem"]["name"]);
            $imagem = $nomefoto;
            
            // Upload da nova imagem
            $target_dir = "imagens/";
            if(move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_dir . $nomefoto)) {
                // Imagem carregada com sucesso
            } else {
                $msg = "Erro ao carregar a nova imagem.";
                $imagem = $imagem_atual; // Manter a imagem atual em caso de erro
            }
        } else {
            $imagem = $imagem_atual; // Manter a imagem atual se não foi enviada uma nova
        }
        
        // Atualizar dados do clube
        $sql_update = "UPDATE clube SET 
                        nome_clube = '$nome_clube',
                        nome_utilizador = '$nome_utilizador',
                        email = '$email',
                        morada = '$morada',
                        imagem = '$imagem'
                      WHERE codigo = '$codigo_clube'";
        
        if(mysqli_query($conn, $sql_update)) {
            $msg = "Dados do clube atualizados com sucesso!";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Dados do clube atualizados com sucesso!');
                window.location.href='definicoes.php';
            </script>";
            exit();
        } else {
            $msg = "Erro ao atualizar dados: " . mysqli_error($conn);
        }
    }
    
    // Processar alteração de senha
    if(isset($_POST['btn_alterar_senha'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];
        
        // Verificar se a nova senha e a confirmação coincidem
        if($nova_senha != $confirmar_senha) {
            $msg = "A nova senha e a confirmação não coincidem.";
        } else {
            // Buscar senha atual do clube
            $sql_senha = "SELECT password FROM clube WHERE codigo = '$codigo_clube'";
            $result_senha = mysqli_query($conn, $sql_senha);
            
            if (!$result_senha) {
                $msg = "Erro ao verificar senha: " . mysqli_error($conn);
            } else {
                $row_senha = mysqli_fetch_assoc($result_senha);
                $senha_bd = $row_senha['password'];
                
                // Verificar se a senha atual está correta
                // Se a senha_bd não for um hash (ex: texto simples), password_verify retornará false.
                // Para permitir a transição de senhas em texto simples para hasheadas, 
                // podemos verificar se a senha_bd é igual à senha_atual (texto simples) OU se password_verify é true.
                if (password_verify($senha_atual, $senha_bd) || ($senha_bd === $senha_atual && !password_needs_rehash($senha_bd, PASSWORD_DEFAULT))) {
                    // Criptografar a nova senha
                    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    
                    // Atualizar senha
                    $sql_update_senha = "UPDATE clube SET password = '$senha_hash' WHERE codigo = '$codigo_clube'";
                    
                    if(mysqli_query($conn, $sql_update_senha)) {
                        $msg = "Senha alterada com sucesso!";
                        
                        // Redirecionar após sucesso
                        echo "<script>
                            alert('Senha alterada com sucesso!');
                            window.location.href='definicoes.php';
                        </script>";
                        exit();
                    } else {
                        $msg = "Erro ao alterar senha: " . mysqli_error($conn);
                    }
                } else {
                    $msg = "Senha atual incorreta.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Definições</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/estilos.css" type="text/css">
    
    <style>
        /* Estilo personalizado para o tema vermelho */
        .card-header {
            background-color: #e60000;
            color: white;
        }
        
        .btn-primary {
            background-color: #e60000;
            border-color: #cc0000;
        }
        
        .btn-primary:hover {
            background-color: #cc0000;
            border-color: #b30000;
        }
        
        .nav-pills .nav-link.active {
            background-color: #e60000;
        }
        
        .form-control:focus {
            border-color: #e60000;
            box-shadow: 0 0 0 0.2rem rgba(230, 0, 0, 0.25);
        }
    </style>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Offcanvas Menu Section Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="canvas-close">
            <i class="fa fa-close"></i>
        </div>
        <div class="search-btn search-switch">
            <i class="fa fa-search"></i>
        </div>
        <div class="header__top--canvas">
            <div class="ht-info">
                <ul>
                    <li><?php echo date("d/m/Y"); ?></li>
                </ul>
            </div>
            <div class="ht-links">
                <a href="definicoes.php" class="primary-btn">Definições</a>
            </div>
            <div class="ht-links">
                <a href="logout.php" class="primary-btn">Logout</a>
            </div>
        </div>
        <ul class="main-menu mobile-menu">
            <li><a href="./home.php">Home</a></li>
            <li><a href="./atletas.php">Atletas</a>
               
            </li>
            <li><a href="#">Treinos</a>
                <ul class="dropdown">
                    <li><a href="./calendario_treinos.php">Calendário</a></li>
                    <li><a href="./convocatorias_treino.php">Convocatórias</a></li>
                    <li><a href="./presencas_treino.php">Folhas de Presenças</a></li>
                    <li><a href="./estatisticas_treino.php">Estatísticas</a></li>
                </ul>
            </li>
            <li><a href="#">Jogos</a>
                <ul class="dropdown">
                    <li><a href="./calendario_jogos.php">Calendário</a></li>
                    <li><a href="./convocatorias_jogo.php">Convocatórias</a></li>
                    <li><a href="./presencas_jogo.php">Folhas de Presenças</a></li>
                    <li><a href="./estatisticas_jogo.php">Estatísticas</a></li>
                </ul>
            </li>
            <li><a href="./contacto.php">Contacto</a></li>
        </ul>
        <div id="mobile-menu-wrap"></div>
    </div>
    <!-- Offcanvas Menu Section End -->

    <!-- Header Section Begin -->
    <header class="header-section">
        <div class="header__top" style="padding: 20px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="ht-info">
                            <ul>
                                <a href="home.php"><img src="imagens/ClubeMaster_pequeno.png"></a>
                            </ul>
                        </div>
                    </div>
                   
                    <div class="col-lg-6" style="text-align: right;">
                        <div class="">
                            <p>
                            <a href="definicoes.php" class="primary-btn" style="padding: 5px 10px; font-size: 12px;">Definições da conta</a> 
                            <a href="logout.php" class="primary-btn" style="padding: 5px 10px; font-size: 12px;">Logout</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header__nav">
            <div class="container">
                <div class="row justify-content-center">
                  
                    <div class="col-lg-12 text-center">
                        <div class="nav-menu">
                            <ul class="main-menu d-inline-block">
                                <li><a href="./home.php">Home</a></li>
                                <li><a href="./atletas.php">Atletas</a>
                                   
                                </li>
                                <li><a href="#">Treinos</a>
                                    <ul class="dropdown">
                                        <li><a href="./calendario_treinos.php">Calendário</a></li>
                                        <li><a href="./convocatorias_treino.php">Convocatórias</a></li>
                                        <li><a href="./presencas_treino.php">Folhas de Presenças</a></li>
                                        <li><a href="./estatisticas_treino.php">Estatísticas</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Jogos</a>
                                    <ul class="dropdown">
                                        <li><a href="./calendario_jogos.php">Calendário</a></li>
                                        <li><a href="./convocatorias_jogo.php">Convocatórias</a></li>
                                        <li><a href="./presencas_jogo.php">Folhas de Presenças</a></li>
                                        <li><a href="./estatisticas_jogo.php">Estatísticas</a></li>
                                    </ul>
                                </li>
                                <li><a href="./contacto.php">Contacto</a></li>
                            </ul>

                            <div class="nm-right search-switch">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
             
            </div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="bs-text">
                        <h2>Definições</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Settings Section Begin -->
    <section class="settings-section spad">
        <div class="container">
            <?php if(!empty($msg)): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert <?php echo (strpos($msg, 'sucesso') !== false) ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $msg; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-3">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Menu de Definições</h5>
                        </div>
                        <div class="card-body">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link active" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">Perfil do Clube</a>
                                <a class="nav-link" id="v-pills-password-tab" data-toggle="pill" href="#v-pills-password" role="tab" aria-controls="v-pills-password" aria-selected="false">Alterar Senha</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <!-- Perfil do Clube -->
                        <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Perfil do Clube</h5>
                                </div>
                                <div class="card-body">
                                    <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                        <input type="hidden" name="imagem_atual" value="<?php echo $imagem; ?>">
                                        
                                        <div class="form-group row">
                                            <div class="col-md-12 text-center mb-4">
                                                <?php if(!empty($imagem) && $imagem != 'no_image.png'): ?>
                                                    <img src="imagens/<?php echo $imagem; ?>" alt="Logo do Clube" style="max-height: 200px;" class="img-thumbnail">
                                                <?php else: ?>
                                                    <img src="imagens/no_image.png" alt="Logo do Clube" style="max-height: 200px;" class="img-thumbnail">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="nome_clube" class="col-sm-3 col-form-label">Nome do Clube</label>
                                            <div class="col-sm-9 mb-3">
                                                <input type="text" class="form-control" id="nome_clube" name="nome_clube" value="<?php echo htmlspecialchars($nome_clube); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="nome_utilizador" class="col-sm-3 col-form-label">Nome de Utilizador</label>
                                            <div class="col-sm-9 mb-3">
                                                <input type="text" class="form-control" id="nome_utilizador" name="nome_utilizador" value="<?php echo htmlspecialchars($nome_utilizador); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                                            <div class="col-sm-9 mb-3">
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="morada" class="col-sm-3 col-form-label">Morada</label>
                                            <div class="col-sm-9 mb-3">
                                                <input type="text" class="form-control" id="morada" name="morada" value="<?php echo htmlspecialchars($morada); ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="imagem" class="col-sm-3 col-form-label">Logo do Clube</label>
                                            <div class="col-sm-9 mb-3">
                                                <input type="file" class="form-control-file" id="imagem" name="imagem" accept="image/*">
                                                <small class="form-text text-muted">Carregue uma nova imagem para o logo do clube.</small>
                                            </div>
                                        </div>
                                        <div class="form-group row mt-4">
                                            <div class="col-sm-12 text-center">
                                                <button type="submit" name="btn_atualizar" class="btn btn-primary">Atualizar Perfil</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alterar Senha -->
                        <div class="tab-pane fade" id="v-pills-password" role="tabpanel" aria-labelledby="v-pills-password-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Alterar Senha</h5>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                        <div class="form-group row">
                                            <label for="senha_atual" class="col-sm-4 col-form-label">Senha Atual</label>
                                            <div class="col-sm-8 mb-3">
                                                <input type="password" class="form-control" id="senha_atual" name="senha_atual" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="nova_senha" class="col-sm-4 col-form-label">Nova Senha</label>
                                            <div class="col-sm-8 mb-3">
                                                <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="confirmar_senha" class="col-sm-4 col-form-label">Confirmar Nova Senha</label>
                                            <div class="col-sm-8 mb-3">
                                                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                                            </div>
                                        </div>
                                        <div class="form-group row mt-4">
                                            <div class="col-sm-12 text-center">
                                                <button type="submit" name="btn_alterar_senha" class="btn btn-primary">Alterar Senha</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Settings Section End -->

   <!-- Footer Section Begin -->
   <footer class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="copyright">
                        <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> ClubeMaster | Sistema de Gestão de Clubes Desportivos</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->


    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>

