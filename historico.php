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
$msg_type = ""; // Para controlar a cor da mensagem (success/danger)
$id_atleta = "";

// Verificar se foi passado um ID de atleta
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id_atleta = $_GET['id'];
    
    // Verificar se o atleta pertence ao clube
    $sql_check = "SELECT * FROM atletas WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if(mysqli_num_rows($result_check) == 0) {
        echo "<script>
            alert('Atleta não encontrado ou não pertence ao seu clube.');
            window.location.href='atletas.php';
        </script>";
        exit();
    }
    
    $atleta = mysqli_fetch_assoc($result_check);
} else {
    echo "<script>
        alert('Nenhum atleta selecionado.');
        window.location.href='atletas.php';
    </script>";
    exit();
}

// Processar formulário de histórico
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_adicionar'])) {
        $tipo_evento = mysqli_real_escape_string($conn, $_POST['tipo_evento']);
        $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
        $data_evento = mysqli_real_escape_string($conn, $_POST['data_evento']);
        $registrado_por = mysqli_real_escape_string($conn, $_POST['registrado_por']);
        
        // Inserir evento no histórico
        $sql = "INSERT INTO historico (id_atleta, codigo_clube, tipo_evento, descricao, data_evento, registrado_por) 
                VALUES ('$id_atleta', '$codigo_clube', '$tipo_evento', '$descricao', '$data_evento', '$registrado_por')";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Evento adicionado ao histórico com sucesso!";
            $msg_type = "success";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Evento adicionado ao histórico com sucesso!');
                window.location.href='historico.php?id=$id_atleta';
            </script>";
            exit();
        } else {
            $msg = "Erro ao adicionar evento ao histórico: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
    
    // Processar exclusão de evento
    if(isset($_POST['btn_excluir'])) {
        $id_historico = $_POST['id_historico'];
        
        // Excluir evento do histórico
        $sql = "DELETE FROM historico WHERE id_historico = '$id_historico' AND codigo_clube = '$codigo_clube'";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Evento excluído do histórico com sucesso!";
            $msg_type = "success";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Evento excluído do histórico com sucesso!');
                window.location.href='historico.php?id=$id_atleta';
            </script>";
            exit();
        } else {
            $msg = "Erro ao excluir evento do histórico: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}

// Buscar histórico do atleta
$sql_historico = "SELECT * FROM historico WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube' ORDER BY data_evento DESC, data_registro DESC";
$result_historico = mysqli_query($conn, $sql_historico);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Histórico do Atleta</title>

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
        
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #e60000;
            left: 50%;
            margin-left: -2px;
        }
        
        .timeline-item {
            margin-bottom: 30px;
            position: relative;
        }
        
        .timeline-item:after {
            content: "";
            display: table;
            clear: both;
        }
        
        .timeline-item .timeline-badge {
            color: #fff;
            width: 50px;
            height: 50px;
            line-height: 50px;
            font-size: 1.4em;
            text-align: center;
            position: absolute;
            top: 16px;
            left: 50%;
            margin-left: -25px;
            background-color: #e60000;
            border-radius: 50%;
            z-index: 100;
        }
        
        .timeline-item .timeline-panel {
            width: 45%;
            float: left;
            border: 1px solid #d4d4d4;
            border-radius: 2px;
            padding: 20px;
            position: relative;
            background: #ffffff;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        }
        
        .timeline-item .timeline-panel:before {
            position: absolute;
            top: 26px;
            right: -15px;
            display: inline-block;
            border-top: 15px solid transparent;
            border-left: 15px solid #ccc;
            border-right: 0 solid #ccc;
            border-bottom: 15px solid transparent;
            content: " ";
        }
        
        .timeline-item .timeline-panel:after {
            position: absolute;
            top: 27px;
            right: -14px;
            display: inline-block;
            border-top: 14px solid transparent;
            border-left: 14px solid #fff;
            border-right: 0 solid #fff;
            border-bottom: 14px solid transparent;
            content: " ";
        }
        
        .timeline-item.timeline-inverted .timeline-panel {
            float: right;
        }
        
        .timeline-item.timeline-inverted .timeline-panel:before {
            border-left-width: 0;
            border-right-width: 15px;
            left: -15px;
            right: auto;
        }
        
        .timeline-item.timeline-inverted .timeline-panel:after {
            border-left-width: 0;
            border-right-width: 14px;
            left: -14px;
            right: auto;
        }
        
        .timeline-title {
            margin-top: 0;
            color: inherit;
        }
        
        .timeline-body > p,
        .timeline-body > ul {
            margin-bottom: 0;
        }
        
        .timeline-body > p + p {
            margin-top: 5px;
        }
        
        @media (max-width: 767px) {
            .timeline:before {
                left: 40px;
            }
            
            .timeline-item .timeline-badge {
                left: 40px;
                margin-left: 0;
            }
            
            .timeline-item .timeline-panel {
                width: calc(100% - 90px);
                float: right;
            }
            
            .timeline-item .timeline-panel:before {
                border-left-width: 0;
                border-right-width: 15px;
                left: -15px;
                right: auto;
            }
            
            .timeline-item .timeline-panel:after {
                border-left-width: 0;
                border-right-width: 14px;
                left: -14px;
                right: auto;
            }
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
            <li class="active"><a href="./atletas.php">Atletas</a>
                <ul class="dropdown">
                    <li><a href="./perfil.php?id=<?php echo $id_atleta; ?>">Perfil</a></li>
                    <li><a href="./ficha_clinica.php?id=<?php echo $id_atleta; ?>">Ficha Clínica</a></li>
                    <li><a href="./documentos.php?id=<?php echo $id_atleta; ?>">Upload de Documentos</a></li>
                    <li class="active"><a href="./historico.php?id=<?php echo $id_atleta; ?>">Histórico</a></li>
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
                                <li class="active"><a href="./atletas.php">Atletas</a>
                                    <ul class="dropdown">
                                        <li><a href="./perfil.php?id=<?php echo $id_atleta; ?>">Perfil</a></li>
                                        <li><a href="./ficha_clinica.php?id=<?php echo $id_atleta; ?>">Ficha Clínica</a></li>
                                        <li><a href="./documentos.php?id=<?php echo $id_atleta; ?>">Upload de Documentos</a></li>
                                        <li class="active"><a href="./historico.php?id=<?php echo $id_atleta; ?>">Histórico</a></li>
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
                        <h2>Histórico - <?php echo $atleta['nome']; ?></h2>
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
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4>Histórico do Atleta</h4>
                                        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#modalNovoEvento">
                                            <i class="fa fa-plus"></i> Adicionar Evento
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="card mb-4">
                                                    <div class="card-body text-center">
                                                        <img src="fotos_jogadores/<?php echo $atleta['fotografia']; ?>" alt="<?php echo $atleta['nome']; ?>" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                                                        <h5><?php echo $atleta['nome']; ?></h5>
                                                        <p><?php echo $atleta['escalao']; ?> <?php echo $atleta['sub_escalao'] ? '(' . $atleta['sub_escalao'] . ')' : ''; ?></p>
                                                        <p><?php echo $atleta['posicao']; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="timeline">
                                                    <?php 
                                                    $count = 0;
                                                    if(mysqli_num_rows($result_historico) > 0): 
                                                        while($evento = mysqli_fetch_assoc($result_historico)): 
                                                            $count++;
                                                            $inverted = $count % 2 == 0 ? 'timeline-inverted' : '';
                                                            
                                                            // Definir ícone com base no tipo de evento
                                                            $icon = 'fa-calendar';
                                                            switch($evento['tipo_evento']) {
                                                                case 'Inscrição':
                                                                    $icon = 'fa-user-plus';
                                                                    break;
                                                                case 'Avaliação':
                                                                    $icon = 'fa-line-chart';
                                                                    break;
                                                                case 'Lesão':
                                                                    $icon = 'fa-medkit';
                                                                    break;
                                                                case 'Prémio':
                                                                    $icon = 'fa-trophy';
                                                                    break;
                                                                case 'Transferência':
                                                                    $icon = 'fa-exchange';
                                                                    break;
                                                                case 'Renovação':
                                                                    $icon = 'fa-refresh';
                                                                    break;
                                                            }
                                                    ?>
                                                    <div class="timeline-item <?php echo $inverted; ?>">
                                                        <div class="timeline-badge">
                                                            <i class="fa <?php echo $icon; ?>"></i>
                                                        </div>
                                                        <div class="timeline-panel">
                                                            <div class="timeline-heading">
                                                                <h4 class="timeline-title"><?php echo $evento['tipo_evento']; ?></h4>
                                                                <p><small class="text-muted"><i class="fa fa-clock-o"></i> <?php echo date('d/m/Y', strtotime($evento['data_evento'])); ?></small></p>
                                                            </div>
                                                            <div class="timeline-body">
                                                                <p><?php echo $evento['descricao']; ?></p>
                                                                <p class="text-muted"><small>Registrado por: <?php echo $evento['registrado_por']; ?></small></p>
                                                                <div class="btn-group mt-2">
                                                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_atleta);?>">
                                                                        <input type="hidden" name="id_historico" value="<?php echo $evento['id_historico']; ?>">
                                                                        <button type="submit" name="btn_excluir" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este evento?');">
                                                                            <i class="fa fa-trash"></i> Excluir
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                        endwhile; 
                                                    else: 
                                                    ?>
                                                    <div class="text-center py-5">
                                                        <h5>Nenhum evento registrado no histórico.</h5>
                                                        <p>Clique em "Adicionar Evento" para começar a registrar o histórico deste atleta.</p>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

    <!-- Modal Novo Evento -->
    <div class="modal fade" id="modalNovoEvento" tabindex="-1" role="dialog" aria-labelledby="modalNovoEventoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoEventoLabel">Adicionar Novo Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_atleta);?>">
                        <div class="form-group">
                            <label for="tipo_evento">Tipo de Evento</label>
                            <select class="form-control" id="tipo_evento" name="tipo_evento" required>
                                <option value="">Selecione...</option>
                                <option value="Inscrição">Inscrição</option>
                                <option value="Avaliação">Avaliação</option>
                                <option value="Lesão">Lesão</option>
                                <option value="Prémio">Prémio</option>
                                <option value="Transferência">Transferência</option>
                                <option value="Renovação">Renovação</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="data_evento">Data do Evento</label>
                            <input type="date" class="form-control" id="data_evento" name="data_evento" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="registrado_por">Registrado Por</label>
                            <input type="text" class="form-control" id="registrado_por" name="registrado_por" required>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" name="btn_adicionar" class="btn btn-primary">Adicionar Evento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
