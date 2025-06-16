<?php
//iniciar sessão para verificar se o cliente fez login
session_start();

//ligar à base de dados
include 'ligarbd.php';
include 'permissoes.php';

// validar sessão 
if (isset($_SESSION['clube'])) {
    $login = true;
    $codigo_clube = $_SESSION['clube'];
    
    // Verificar permissão para acessar a página
    if (!verificar_permissao('utilizadores', 'visualizar')) {
        header("Location: acesso_negado.php");
        exit();
    }
} else {
    $login = false;
    echo "<script>window.location.href='index.php'</script>";
    exit();
}

// Inicializar variáveis
$msg = "";
$id_utilizador = "";
$nome = "";
$email = "";
$id_funcao = "";
$ativo = 1;

// Processar formulário de adição de utilizador
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_adicionar'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $id_funcao = $_POST['id_funcao'];
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        
        // Verificar se o email já existe
        $sql_check = "SELECT * FROM utilizadores WHERE email = '$email' AND codigo_clube = '$codigo_clube'";
        $result_check = mysqli_query($conn, $sql_check);
        
        if(mysqli_num_rows($result_check) > 0) {
            $msg = "Erro: Este email já está em uso.";
        } else {
            // Inserir utilizador na base de dados
            $data_registo = date('Y-m-d');
            $sql = "INSERT INTO utilizadores (codigo_clube, nome, email, password, id_funcao, data_registo, ativo) 
                    VALUES ('$codigo_clube', '$nome', '$email', '$password', '$id_funcao', '$data_registo', '$ativo')";
            
            if(mysqli_query($conn, $sql)) {
                $msg = "Utilizador adicionado com sucesso!";
                
                // Registrar ação no log
                registrar_log('adicionar_utilizador', "Adicionado utilizador: $nome ($email)");
                
                // Limpar campos após sucesso
                $nome = "";
                $email = "";
                $id_funcao = "";
                $ativo = 1;
                
                // Redirecionar após sucesso
                echo "<script>
                    alert('Utilizador adicionado com sucesso!');
                    window.location.href='utilizadores.php';
                </script>";
                exit();
            } else {
                $msg = "Erro ao adicionar utilizador: " . mysqli_error($conn);
            }
        }
    }
    
    // Processar formulário de edição de utilizador
    if(isset($_POST['btn_editar'])) {
        $id_utilizador = $_POST['id_utilizador'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $id_funcao = $_POST['id_funcao'];
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        
        // Verificar se o email já existe (exceto para o próprio utilizador)
        $sql_check = "SELECT * FROM utilizadores WHERE email = '$email' AND codigo_clube = '$codigo_clube' AND id_utilizador != '$id_utilizador'";
        $result_check = mysqli_query($conn, $sql_check);
        
        if(mysqli_num_rows($result_check) > 0) {
            $msg = "Erro: Este email já está em uso por outro utilizador.";
        } else {
            // Verificar se a senha foi alterada
            if(!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sql_password = ", password = '$password'";
            } else {
                $sql_password = "";
            }
            
            // Atualizar utilizador na base de dados
            $sql = "UPDATE utilizadores SET 
                    nome = '$nome', 
                    email = '$email', 
                    id_funcao = '$id_funcao',
                    ativo = '$ativo'
                    $sql_password
                    WHERE id_utilizador = '$id_utilizador' AND codigo_clube = '$codigo_clube'";
            
            if(mysqli_query($conn, $sql)) {
                $msg = "Utilizador atualizado com sucesso!";
                
                // Registrar ação no log
                registrar_log('editar_utilizador', "Editado utilizador ID: $id_utilizador, Nome: $nome");
                
                // Redirecionar após sucesso
                echo "<script>
                    alert('Utilizador atualizado com sucesso!');
                    window.location.href='utilizadores.php';
                </script>";
                exit();
            } else {
                $msg = "Erro ao atualizar utilizador: " . mysqli_error($conn);
            }
        }
    }
    
    // Processar exclusão de utilizador
    if(isset($_POST['btn_excluir'])) {
        $id_utilizador = $_POST['id_utilizador'];
        
        // Verificar se não é o próprio utilizador logado
        if(isset($_SESSION['id_utilizador']) && $_SESSION['id_utilizador'] == $id_utilizador) {
            $msg = "Não é possível excluir o seu próprio utilizador.";
        } else {
            // Obter informações do utilizador antes de excluir (para o log)
            $sql_info = "SELECT nome, email FROM utilizadores WHERE id_utilizador = '$id_utilizador'";
            $result_info = mysqli_query($conn, $sql_info);
            $user_info = mysqli_fetch_assoc($result_info);
            
            // Excluir utilizador da base de dados
            $sql = "DELETE FROM utilizadores WHERE id_utilizador = '$id_utilizador' AND codigo_clube = '$codigo_clube'";
            
            if(mysqli_query($conn, $sql)) {
                $msg = "Utilizador excluído com sucesso!";
                
                // Registrar ação no log
                registrar_log('excluir_utilizador', "Excluído utilizador: {$user_info['nome']} ({$user_info['email']})");
                
                // Redirecionar após sucesso
                echo "<script>
                    alert('Utilizador excluído com sucesso!');
                    window.location.href='utilizadores.php';
                </script>";
                exit();
            } else {
                $msg = "Erro ao excluir utilizador: " . mysqli_error($conn);
            }
        }
    }
}

// Buscar utilizador específico para edição
if(isset($_GET['editar']) && !empty($_GET['editar'])) {
    $id_utilizador = $_GET['editar'];
    
    $sql = "SELECT * FROM utilizadores WHERE id_utilizador = '$id_utilizador' AND codigo_clube = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $utilizador = mysqli_fetch_assoc($result);
        $id_utilizador = $utilizador['id_utilizador'];
        $nome = $utilizador['nome'];
        $email = $utilizador['email'];
        $id_funcao = $utilizador['id_funcao'];
        $ativo = $utilizador['ativo'];
    }
}

// Buscar todos os utilizadores do clube
$sql_utilizadores = "SELECT u.*, f.nome_funcao FROM utilizadores u 
                    JOIN funcoes f ON u.id_funcao = f.id_funcao 
                    WHERE u.codigo_clube = '$codigo_clube' 
                    ORDER BY u.nome";
$result_utilizadores = mysqli_query($conn, $sql_utilizadores);

// Buscar todas as funções disponíveis
$sql_funcoes = "SELECT * FROM funcoes ORDER BY id_funcao";
$result_funcoes = mysqli_query($conn, $sql_funcoes);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Gestão de Utilizadores</title>

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
                <ul class="dropdown">
                    <li><a href="./perfil.php">Perfil</a></li>
                    <li><a href="./ficha_clinica.php">Ficha Clínica</a></li>
                    <li><a href="./documentos.php">Upload de Documentos</a></li>
                    <li><a href="./historico.php">Histórico</a></li>
                </ul>
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
            <?php if (verificar_permissao('utilizadores', 'visualizar')): ?>
            <li class="active"><a href="./utilizadores.php">Utilizadores</a></li>
            <?php endif; ?>
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
                                    <ul class="dropdown">
                                        <li><a href="./perfil.php">Perfil</a></li>
                                        <li><a href="./ficha_clinica.php">Ficha Clínica</a></li>
                                        <li><a href="./documentos.php">Upload de Documentos</a></li>
                                        <li><a href="./historico.php">Histórico</a></li>
                                    </ul>
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
                                <?php if (verificar_permissao('utilizadores', 'visualizar')): ?>
                                <li class="active"><a href="./utilizadores.php">Utilizadores</a></li>
                                <?php endif; ?>
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
                        <h2>Gestão de Utilizadores</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Utilizadores Section Begin -->
    <section class="utilizadores-section spad">
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
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5><i class="fa fa-users"></i> Lista de Utilizadores</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Função</th>
                                            <th>Status</th>
                                            <th>Último Acesso</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if(mysqli_num_rows($result_utilizadores) > 0) {
                                            while($row = mysqli_fetch_assoc($result_utilizadores)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['nome']; ?></td>
                                            <td><?php echo $row['email']; ?></td>
                                            <td><?php echo $row['nome_funcao']; ?></td>
                                            <td>
                                                <?php if($row['ativo'] == 1): ?>
                                                <span class="badge badge-success">Ativo</span>
                                                <?php else: ?>
                                                <span class="badge badge-danger">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if($row['ultimo_acesso']) {
                                                    echo date('d/m/Y H:i', strtotime($row['ultimo_acesso']));
                                                } else {
                                                    echo 'Nunca acessou';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (verificar_permissao('utilizadores', 'editar')): ?>
                                                <a href="utilizadores.php?editar=<?php echo $row['id_utilizador']; ?>" class="btn btn-sm btn-primary">Editar</a>
                                                <?php endif; ?>
                                                
                                                <?php if (verificar_permissao('utilizadores', 'excluir') && (isset($_SESSION['id_utilizador']) && $_SESSION['id_utilizador'] != $row['id_utilizador'])): ?>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalExcluir<?php echo $row['id_utilizador']; ?>">Excluir</button>
                                                
                                                <!-- Modal de Confirmação de Exclusão -->
                                                <div class="modal fade" id="modalExcluir<?php echo $row['id_utilizador']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalExcluirLabel<?php echo $row['id_utilizador']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title" id="modalExcluirLabel<?php echo $row['id_utilizador']; ?>">Confirmar Exclusão</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Tem certeza que deseja excluir o utilizador <strong><?php echo $row['nome']; ?></strong>?</p>
                                                                <p class="text-danger">Esta ação não pode ser desfeita.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                                                    <input type="hidden" name="id_utilizador" value="<?php echo $row['id_utilizador']; ?>">
                                                                    <button type="submit" name="btn_excluir" class="btn btn-danger">Excluir</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php 
                                            }
                                        } else {
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Nenhum utilizador cadastrado.</td>
                                        </tr>
                                        <?php 
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5><?php echo isset($id_utilizador) ? 'Editar Utilizador' : 'Adicionar Novo Utilizador'; ?></h5>
                        </div>
                        <div class="card-body">
                            <?php if (verificar_permissao('utilizadores', isset($id_utilizador) ? 'editar' : 'adicionar')): ?>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <?php if(isset($id_utilizador) && !empty($id_utilizador)): ?>
                                <input type="hidden" name="id_utilizador" value="<?php echo $id_utilizador; ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $nome; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Senha <?php echo isset($id_utilizador) ? '(deixe em branco para manter a atual)' : ''; ?></label>
                                    <input type="password" class="form-control" id="password" name="password" <?php echo isset($id_utilizador) ? '' : 'required'; ?>>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_funcao">Função</label>
                                    <select class="form-control" id="id_funcao" name="id_funcao" required>
                                        <option value="">Selecione...</option>
                                        <?php 
                                        mysqli_data_seek($result_funcoes, 0);
                                        while($row = mysqli_fetch_assoc($result_funcoes)) {
                                            $selected = ($id_funcao == $row['id_funcao']) ? 'selected' : '';
                                            echo "<option value='{$row['id_funcao']}' $selected>{$row['nome_funcao']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" <?php echo ($ativo == 1) ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="ativo">Utilizador Ativo</label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <?php if(isset($id_utilizador) && !empty($id_utilizador)): ?>
                                    <button type="submit" name="btn_editar" class="btn btn-primary">Atualizar Utilizador</button>
                                    <a href="utilizadores.php" class="btn btn-secondary">Cancelar</a>
                                    <?php else: ?>
                                    <button type="submit" name="btn_adicionar" class="btn btn-primary">Adicionar Utilizador</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                Você não tem permissão para <?php echo isset($id_utilizador) ? 'editar' : 'adicionar'; ?> utilizadores.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5>Informações</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Presidente:</strong> Acesso total ao sistema.</p>
                            <p><strong>Diretor Desportivo:</strong> Gestão de atletas, treinos e jogos.</p>
                            <p><strong>Treinador:</strong> Gestão de treinos e jogos da sua equipa.</p>
                            <p><strong>Médico/Fisioterapeuta:</strong> Acesso à ficha clínica dos atletas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Utilizadores Section End -->

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

    <!-- Search model Begin -->
    <div class="search-model">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-switch"><i class="fa fa-close"></i></div>
            <form class="search-model-form">
                <input type="text" id="search-input" placeholder="Pesquisar...">
            </form>
        </div>
    </div>
    <!-- Search model end -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
