<?php
//iniciar sessão para verificar se o cliente fez login
session_start();

// ocultar mensagens de erro
//error_reporting(0);

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

// Buscar informações do clube
$sql_clube = "SELECT * FROM clube WHERE codigo = '$codigo_clube'";
$result_clube = mysqli_query($conn, $sql_clube);
$clube_info = mysqli_fetch_assoc($result_clube);

// Contar atletas
$sql_atletas = "SELECT COUNT(*) as total_atletas FROM atletas WHERE codigo_clube = '$codigo_clube'";
$result_atletas = mysqli_query($conn, $sql_atletas);
$row_atletas = mysqli_fetch_assoc($result_atletas);
$total_atletas = $row_atletas['total_atletas'];

// Contar treinos
$sql_treinos = "SELECT COUNT(*) as total_treinos FROM treinos WHERE codigo_clube = '$codigo_clube'";
$result_treinos = mysqli_query($conn, $sql_treinos);
$row_treinos = mysqli_fetch_assoc($result_treinos);
$total_treinos = $row_treinos['total_treinos'];

// Contar jogos
$sql_jogos = "SELECT COUNT(*) as total_jogos FROM jogos WHERE codigo_clube = '$codigo_clube'";
$result_jogos = mysqli_query($conn, $sql_jogos);
$row_jogos = mysqli_fetch_assoc($result_jogos);
$total_jogos = $row_jogos['total_jogos'];

// Buscar próximos eventos (treinos e jogos)
$hoje = date('Y-m-d');
$sql_eventos = "
    (SELECT 'treino' as tipo, data, hora_inicio as hora, local, escalao, 'Treino' as titulo, id_treino as id, NULL as adversario 
    FROM treinos 
    WHERE codigo_clube = '$codigo_clube' AND data >= '$hoje' 
    ORDER BY data, hora_inicio 
    LIMIT 3)
    UNION
    (SELECT 'jogo' as tipo, data, hora, local, escalao, CONCAT('Jogo vs. ', adversario) as titulo, id_jogo as id, adversario 
    FROM jogos 
    WHERE codigo_clube = '$codigo_clube' AND data >= '$hoje' 
    ORDER BY data, hora 
    LIMIT 3)
    ORDER BY data, hora
    LIMIT 3
";
$result_eventos = mysqli_query($conn, $sql_eventos);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Painel de Controlo</title>

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
            <li class="active"><a href="./home.php">Home</a></li>
            <li><a href="./atletas.php">Atletas</a></li>
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
                                <a href="logout.php" class="primary-btn" style="padding: 5px 10px; font-size: 12px;">Logout</a>
                            </p>
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
                                <li class="active"><a href="./home.php">Home</a></li>
                                <li><a href="./atletas.php">Atletas</a></li>
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

    <!-- Dashboard Overview Section Begin -->
    <section class="dashboard-overview">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h3>Painel de <span>Controlo</span></h3>
                    </div>
                </div>
            </div>
            
            <!-- Bem-vindo -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="alert alert-info">
                        <h4>Bem-vindo, <?php echo isset($clube_info['nome_utilizador']) ? $clube_info['nome_utilizador'] : 'Utilizador'; ?>!</h4>
                        <p>Este é o painel de controlo do <?php echo isset($clube_info['nome_clube']) ? $clube_info['nome_clube'] : 'seu clube'; ?>. Aqui você pode gerir atletas, treinos, jogos e muito mais.</p>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards Row -->
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 text-center" style="border-top: 4px solid #c2151c;">
                        <div class="card-body">
                            <i class="fa fa-users fa-3x mb-3" style="color: #c2151c;"></i>
                            <h4 class="card-title">Atletas</h4>
                            <h2 class="display-4"><?php echo $total_atletas; ?></h2>
                        </div>
                        <div class="card-footer">
                            <a href="atletas.php" class="text-muted">Ver detalhes</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 text-center" style="border-top: 4px solid #1c87c2;">
                        <div class="card-body">
                            <i class="fa fa-calendar-check-o fa-3x mb-3" style="color: #1c87c2;"></i>
                            <h4 class="card-title">Treinos</h4>
                            <h2 class="display-4"><?php echo $total_treinos; ?></h2>
                        </div>
                        <div class="card-footer">
                            <a href="calendario_treinos.php" class="text-muted">Ver calendário</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 text-center" style="border-top: 4px solid #28a745;">
                        <div class="card-body">
                            <i class="fa fa-trophy fa-3x mb-3" style="color: #28a745;"></i>
                            <h4 class="card-title">Jogos</h4>
                            <h2 class="display-4"><?php echo $total_jogos; ?></h2>
                        </div>
                        <div class="card-footer">
                            <a href="calendario_jogos.php" class="text-muted">Ver calendário</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Row -->
            <div class="row">
                <!-- Upcoming Events Column -->
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fa fa-calendar"></i> Próximos Eventos</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            if(mysqli_num_rows($result_eventos) > 0) {
                                while($evento = mysqli_fetch_assoc($result_eventos)) {
                                    $data_formatada = date('d', strtotime($evento['data']));
                                    $mes_formatado = strtoupper(substr(date('M', strtotime($evento['data'])), 0, 3));
                                    
                                    $bg_color = ($evento['tipo'] == 'treino') ? '#1c87c2' : '#28a745';
                                    $link = ($evento['tipo'] == 'treino') ? 'calendario_treinos.php' : 'calendario_jogos.php';
                            ?>
                            <div class="upcoming-event d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid #eee;">
                                <div class="date-box text-center mr-3" style="width: 60px; height: 60px; background-color: <?php echo $bg_color; ?>; color: white; border-radius: 5px;">
                                    <div class="day" style="font-size: 22px; font-weight: bold;"><?php echo $data_formatada; ?></div>
                                    <div class="month" style="font-size: 13px;"><?php echo $mes_formatado; ?></div>
                                </div>
                                <div class="event-details">
                                    <h6><?php echo $evento['titulo']; ?></h6>
                                    <p class="mb-0">
                                        <i class="fa fa-clock-o"></i> <?php echo substr($evento['hora'], 0, 5); ?> | 
                                        <i class="fa fa-map-marker"></i> <?php echo $evento['local']; ?> | 
                                        <i class="fa fa-users"></i> <?php echo $evento['escalao']; ?>
                                    </p>
                                </div>
                            </div>
                            <?php 
                                }
                            } else {
                            ?>
                            <div class="alert alert-info">
                                Não há eventos agendados. <a href="calendario_treinos.php">Agende um treino</a> ou <a href="calendario_jogos.php">agende um jogo</a>.
                            </div>
                            <?php } ?>
                            
                            <div class="text-center mt-3">
                                <a href="calendario_treinos.php" class="btn btn-outline-primary btn-sm mr-2">Ver treinos</a>
                                <a href="calendario_jogos.php" class="btn btn-outline-success btn-sm">Ver jogos</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Performance Chart Column -->
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fa fa-line-chart"></i> Ações Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="novoatleta.php" class="btn btn-danger btn-block">
                                        <i class="fa fa-user-plus fa-2x mb-2"></i><br>
                                        Adicionar Atleta
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="calendario_treinos.php" class="btn btn-primary btn-block">
                                        <i class="fa fa-calendar-plus-o fa-2x mb-2"></i><br>
                                        Agendar Treino
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="calendario_jogos.php" class="btn btn-success btn-block">
                                        <i class="fa fa-futbol-o fa-2x mb-2"></i><br>
                                        Agendar Jogo
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="convocatorias_treino.php" class="btn btn-info btn-block">
                                        <i class="fa fa-list-alt fa-2x mb-2"></i><br>
                                        Convocatória
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Dashboard Overview Section End -->

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
</body>

</html>
