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
$msg_type = "";

// Lista completa de escalões
$todos_escaloes = [
    "Pré-Petizes" => ["Sub-5"],
    "Petizes" => ["Sub-6", "Sub-7"],
    "Traquinas" => ["Sub-8", "Sub-9"],
    "Benjamins" => ["Sub-10", "Sub-11"],
    "Infantis" => ["Sub-12", "Sub-13"],
    "Iniciados" => ["Sub-14", "Sub-15"],
    "Juvenis" => ["Sub-16", "Sub-17"],
    "Juniores" => ["Sub-18", "Sub-19"],
    "Sub-23" => ["B"],
    "Seniores" => [""],
    "Veteranos" => [""]
];

// Processar formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se é uma ação de convocatória
    if (isset($_POST['id_jogo']) && isset($_POST['atletas'])) {
        $id_jogo = mysqli_real_escape_string($conn, $_POST['id_jogo']);
        $atletas = $_POST['atletas'];
        
        // Primeiro, excluir convocatórias existentes para este jogo
        $sql_delete = "DELETE FROM convocatorias_jogo WHERE id_jogo = '$id_jogo'";
        if (mysqli_query($conn, $sql_delete)) {
            // Inserir novas convocatórias
            $success = true;
            foreach ($atletas as $id_atleta) {
                $id_atleta = mysqli_real_escape_string($conn, $id_atleta);
                $sql_insert = "INSERT INTO convocatorias_jogo (id_jogo, id_atleta) VALUES ('$id_jogo', '$id_atleta')";
                if (!mysqli_query($conn, $sql_insert)) {
                    $success = false;
                    break;
                }
            }
            
            if ($success) {
                $msg = "Convocatória salva com sucesso!";
                $msg_type = "success";
            } else {
                $msg = "Erro ao salvar convocatória: " . mysqli_error($conn);
                $msg_type = "danger";
            }
        } else {
            $msg = "Erro ao limpar convocatórias anteriores: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}

// Obter jogos futuros
$data_atual = date('Y-m-d');
$sql_jogos = "SELECT * FROM jogos WHERE codigo_clube = '$codigo_clube' AND data >= '$data_atual' ORDER BY data ASC, hora ASC";
$result_jogos = mysqli_query($conn, $sql_jogos);

// Obter jogo selecionado
$jogo_selecionado = null;
$id_jogo_selecionado = isset($_GET['id_jogo']) ? $_GET['id_jogo'] : '';

if (!empty($id_jogo_selecionado)) {
    $id_jogo_selecionado = mysqli_real_escape_string($conn, $id_jogo_selecionado);
    $sql_jogo = "SELECT * FROM jogos WHERE id_jogo = '$id_jogo_selecionado' AND codigo_clube = '$codigo_clube'";
    $result_jogo = mysqli_query($conn, $sql_jogo);
    
    if (mysqli_num_rows($result_jogo) > 0) {
        $jogo_selecionado = mysqli_fetch_assoc($result_jogo);
        
        // Obter atletas do mesmo escalão
        $escalao = $jogo_selecionado['escalao'];
        $sql_atletas = "SELECT * FROM atletas WHERE codigo_clube = '$codigo_clube' AND escalao = '$escalao' ORDER BY nome ASC";
        $result_atletas = mysqli_query($conn, $sql_atletas);
        
        // Obter atletas já convocados
        $sql_convocados = "SELECT id_atleta FROM convocatorias_jogo WHERE id_jogo = '$id_jogo_selecionado'";
        $result_convocados = mysqli_query($conn, $sql_convocados);
        $atletas_convocados = [];
        
        while ($row = mysqli_fetch_assoc($result_convocados)) {
            $atletas_convocados[] = $row['id_atleta'];
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
    <title>ClubeMaster - Convocatórias de Jogo</title>

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
    
    <!-- Select2 para melhorar a seleção múltipla -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            <li><a href="./atletas.php">Atletas</a></li>
            <li><a href="#">Treinos</a>
                <ul class="dropdown">
                    <li><a href="./calendario_treinos.php">Calendário</a></li>
                    <li><a href="./convocatorias_treino.php">Convocatórias</a></li>
                    <li><a href="./presencas_treino.php">Folhas de Presenças</a></li>
                    <li><a href="./estatisticas_treino.php">Estatísticas</a></li>
                </ul>
            </li>
            <li class="active"><a href="#">Jogos</a>
                <ul class="dropdown">
                    <li><a href="./calendario_jogos.php">Calendário</a></li>
                    <li class="active"><a href="./convocatorias_jogo.php">Convocatórias</a></li>
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
                                <li><a href="./atletas.php">Atletas</a></li>
                                <li><a href="#">Treinos</a>
                                    <ul class="dropdown">
                                        <li><a href="./calendario_treinos.php">Calendário</a></li>
                                        <li><a href="./convocatorias_treino.php">Convocatórias</a></li>
                                        <li><a href="./presencas_treino.php">Folhas de Presenças</a></li>
                                        <li><a href="./estatisticas_treino.php">Estatísticas</a></li>
                                    </ul>
                                </li>
                                <li class="active"><a href="#">Jogos</a>
                                    <ul class="dropdown">
                                        <li><a href="./calendario_jogos.php">Calendário</a></li>
                                        <li class="active"><a href="./convocatorias_jogo.php">Convocatórias</a></li>
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
                        <h2>Convocatórias de Jogo</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Contact Section Begin -->
    <section class="contact-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="contact-form">
                        <?php if(!empty($msg)): ?>
                        <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo $msg; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4>Convocatórias de Jogo</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="calendario_jogos.php" class="btn btn-secondary">
                                    <i class="fa fa-calendar"></i> Calendário de Jogos
                                </a>
                            </div>
                        </div>
                        
                        <!-- Seleção de Jogo -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Selecionar Jogo</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Hora</th>
                                                <th>Local</th>
                                                <th>Adversário</th>
                                                <th>Escalão</th>
                                                <th>Tipo</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(mysqli_num_rows($result_jogos) > 0): ?>
                                                <?php while($jogo = mysqli_fetch_assoc($result_jogos)): ?>
                                                <tr <?php echo ($id_jogo_selecionado == $jogo['id_jogo']) ? 'class="table-primary"' : ''; ?>>
                                                    <td><?php echo date('d/m/Y', strtotime($jogo['data'])); ?></td>
                                                    <td><?php echo date('H:i', strtotime($jogo['hora'])); ?></td>
                                                    <td><?php echo htmlspecialchars($jogo['local']); ?></td>
                                                    <td><?php echo htmlspecialchars($jogo['adversario']); ?></td>
                                                    <td><?php echo htmlspecialchars($jogo['escalao']); ?></td>
                                                    <td><?php echo htmlspecialchars($jogo['tipo_jogo']); ?></td>
                                                    <td>
                                                        <a href="convocatorias_jogo.php?id_jogo=<?php echo $jogo['id_jogo']; ?>" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-users"></i> Convocar
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">Não há jogos futuros agendados.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulário de Convocatória -->
                        <?php if($jogo_selecionado): ?>
                        <div class="card">
                            <div class="card-header">
                                <h5>Convocatória para o Jogo</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5><?php echo htmlspecialchars($jogo_selecionado['adversario']); ?></h5>
                                        <p>
                                            <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($jogo_selecionado['data'])); ?><br>
                                            <strong>Hora:</strong> <?php echo date('H:i', strtotime($jogo_selecionado['hora'])); ?><br>
                                            <strong>Local:</strong> <?php echo htmlspecialchars($jogo_selecionado['local']); ?><br>
                                            <strong>Escalão:</strong> <?php echo htmlspecialchars($jogo_selecionado['escalao']); ?><br>
                                            <strong>Tipo:</strong> <?php echo htmlspecialchars($jogo_selecionado['tipo_jogo']); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <form action="convocatorias_jogo.php" method="post">
                                    <input type="hidden" name="id_jogo" value="<?php echo $jogo_selecionado['id_jogo']; ?>">
                                    
                                    <div class="form-group">
                                        <label for="atletas">Selecionar Atletas</label>
                                        <select class="form-control select2" id="atletas" name="atletas[]" multiple required>
                                            <?php while($atleta = mysqli_fetch_assoc($result_atletas)): ?>
                                            <option value="<?php echo $atleta['id_atleta']; ?>" <?php echo in_array($atleta['id_atleta'], $atletas_convocados) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($atleta['nome']); ?> 
                                                <?php if(!empty($atleta['sub_escalao'])): ?>
                                                (<?php echo htmlspecialchars($atleta['sub_escalao']); ?>)
                                                <?php endif; ?>
                                                - <?php echo htmlspecialchars($atleta['posicao']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <small class="form-text text-muted">Selecione os atletas para a convocatória.</small>
                                    </div>
                                    
                                    <div class="form-group text-center mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Salvar Convocatória
                                        </button>
                                        <a href="convocatorias_jogo.php" class="btn btn-secondary ml-2">
                                            <i class="fa fa-times"></i> Cancelar
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

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
            <form class="search-model-form" action="pesquisa.php" method="get">
                <input type="text" id="search-input" name="q" placeholder="Pesquisar...">
                <button type="submit" class="d-none">Pesquisar</button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                placeholder: "Selecione os atletas",
                allowClear: true
            });
        });
    </script>
</body>

</html>
