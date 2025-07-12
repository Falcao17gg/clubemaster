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

// Processar formulário de convocatória
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_convocar'])) {
        $id_treino = $_POST['id_treino'];
        $atletas_convocados = isset($_POST['atletas']) ? $_POST['atletas'] : [];
        
        // Verificar se o treino pertence ao clube atual
        $sql_check_treino = "SELECT * FROM treinos WHERE id_treino = '$id_treino' AND codigo_clube = '$codigo_clube'";
        $result_check_treino = mysqli_query($conn, $sql_check_treino);
        if (mysqli_num_rows($result_check_treino) == 0) {
            $msg = "Treino não encontrado ou não pertence ao seu clube";
        } else {
            // Primeiro, excluir convocatórias anteriores para este treino
            $sql_delete = "DELETE FROM convocatorias_treino WHERE id_treino = '$id_treino'";
            mysqli_query($conn, $sql_delete);
            
            // Inserir novas convocatórias
            $success = true;
            foreach($atletas_convocados as $id_atleta) {
                $sql = "INSERT INTO convocatorias_treino (id_treino, id_atleta, data_convocatoria) 
                        VALUES ('$id_treino', '$id_atleta', NOW())";
                
                if(!mysqli_query($conn, $sql)) {
                    $success = false;
                    $msg = "Erro ao convocar atleta ID $id_atleta: " . mysqli_error($conn);
                    break;
                }
            }
            
            if($success) {
                $msg = "Convocatória realizada com sucesso!";
                
                // Redirecionar após sucesso
                echo "<script>
                    alert('Convocatória realizada com sucesso!');
                    window.location.href='convocatorias_treino.php';
                </script>";
                exit();
            }
        }
    }
}

// Buscar treinos agendados
$sql_treinos = "SELECT * FROM treinos WHERE codigo_clube = '$codigo_clube' AND data >= CURDATE() ORDER BY data ASC";
$result_treinos = mysqli_query($conn, $sql_treinos);

// Buscar atletas do clube
$sql_atletas = "SELECT * FROM atletas WHERE codigo_clube = '$codigo_clube' ORDER BY nome ASC";
$result_atletas = mysqli_query($conn, $sql_atletas);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Convocatórias para Treinos</title>

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
            <li><a href="./atletas.php">Atletas</a></li>
            <li class="active"><a href="#">Treinos</a>
                <ul class="dropdown">
                    <li><a href="./calendario_treinos.php">Calendário</a></li>
                    <li class="active"><a href="./convocatorias_treino.php">Convocatórias</a></li>
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
                                <li><a href="./atletas.php">Atletas</a></li>
                                <li class="active"><a href="#">Treinos</a>
                                    <ul class="dropdown">
                                        <li><a href="./calendario_treinos.php">Calendário</a></li>
                                        <li class="active"><a href="./convocatorias_treino.php">Convocatórias</a></li>
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
                        <h2>Convocatórias de Treinos</h2>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Convocations Section Begin -->
    <section class="convocations-section spad">
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
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h3>Convocatórias de Treinos</h3>
                    <p>Selecione um treino e convoque os atletas</p>
                </div>
                <div class="col-md-6 text-right">
                    <a href="calendario_treinos.php" class="btn btn-secondary">
                        <i class="fa fa-calendar"></i> Calendário de Treinos
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Treinos Agendados</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php 
                                if($result_treinos && mysqli_num_rows($result_treinos) > 0) {
                                    while($treino = mysqli_fetch_assoc($result_treinos)) {
                                        $data_formatada = date('d/m/Y', strtotime($treino['data']));
                                        $hora_inicio = date('H:i', strtotime($treino['hora_inicio']));
                                        $hora_fim = date('H:i', strtotime($treino['hora_fim']));
                                ?>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action treino-item" data-id="<?php echo $treino['id_treino']; ?>" data-data="<?php echo $data_formatada; ?>" data-hora-inicio="<?php echo $hora_inicio; ?>" data-hora-fim="<?php echo $hora_fim; ?>" data-local="<?php echo $treino['local']; ?>" data-escalao="<?php echo $treino['escalao']; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Treino - <?php echo $treino['escalao']; ?></h5>
                                        <small><?php echo $data_formatada; ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo $treino['local']; ?> - <?php echo $hora_inicio; ?> às <?php echo $hora_fim; ?></p>
                                    
                                    <?php
                                    // Verificar se já existe convocatória para este treino
                                    $sql_check = "SELECT COUNT(*) as total FROM convocatorias_treino WHERE id_treino = '" . $treino['id_treino'] . "'";
                                    $result_check = mysqli_query($conn, $sql_check);
                                    $row_check = mysqli_fetch_assoc($result_check);
                                    
                                    if($row_check['total'] > 0) {
                                        echo '<span class="badge badge-success">Convocatória realizada</span>';
                                    } else {
                                        echo '<span class="badge badge-warning">Pendente</span>';
                                    }
                                    ?>
                                </a>
                                <?php 
                                    }
                                } else {
                                ?>
                                <div class="list-group-item">
                                    <p class="mb-1">Nenhum treino agendado.</p>
                                    <a href="calendario_treinos.php" class="btn btn-sm btn-primary">Agendar Treino</a>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Convocatória</h5>
                        </div>
                        <div class="card-body">
                            <div id="sem-treino-selecionado">
                                <div class="text-center">
                                    <p>Selecione um treino para realizar a convocatória.</p>
                                </div>
                            </div>
                            
                            <div id="convocatoria-form" style="display: none;">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    <input type="hidden" id="id_treino" name="id_treino">
                                    
                                    <div class="treino-info mb-4">
                                        <h4>Detalhes do Treino</h4>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p><strong>Data:</strong> <span id="data_treino"></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Horário:</strong> <span id="horario_treino"></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Local:</strong> <span id="local_treino"></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Escalão:</strong> <span id="escalao_treino"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h4>Selecione os Atletas</h4>
                                    
                                    <div class="form-group mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="selecionar-todos">
                                            <label class="custom-control-label" for="selecionar-todos">Selecionar Todos</label>
                                        </div>
                                    </div>
                                    
                                    <div class="atletas-list">
                                        <?php 
                                        if($result_atletas && mysqli_num_rows($result_atletas) > 0) {
                                            while($atleta = mysqli_fetch_assoc($result_atletas)) {
                                        ?>
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input atleta-checkbox" id="atleta-<?php echo $atleta['id_atleta']; ?>" name="atletas[]" value="<?php echo $atleta['id_atleta']; ?>">
                                            <label class="custom-control-label" for="atleta-<?php echo $atleta['id_atleta']; ?>">
                                                <?php echo $atleta['nome']; ?> 
                                                <small>(<?php echo $atleta['posicao']; ?> - <?php echo $atleta['escalao']; ?>)</small>
                                            </label>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<p>Nenhum atleta cadastrado. <a href="novoatleta.php">Adicione um atleta</a>.</p>';
                                        }
                                        ?>
                                    </div>
                                    
                                    <div class="form-group mt-4 text-right">
                                        <button type="submit" name="btn_convocar" class="btn btn-primary">Realizar Convocatória</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Convocations Section End -->

    <!-- Footer Section Begin -->
    <footer class="footer-section">
        <div class="copyright-option">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="co-text">
                            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | ClubeMaster
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
                        </div>
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
                <input type="text" id="search-input" placeholder="Search here.....">
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
    
    <script>
        $(document).ready(function() {
            // Selecionar/Deselecionar todos os atletas
            $('#selecionar-todos').change(function() {
                $('.atleta-checkbox').prop('checked', $(this).prop('checked'));
            });
            
            // Ao clicar em um treino, exibir formulário de convocatória
            $('.treino-item').click(function() {
                var id_treino = $(this).data('id');
                var data = $(this).data('data');
                var hora_inicio = $(this).data('hora-inicio');
                var hora_fim = $(this).data('hora-fim');
                var local = $(this).data('local');
                var escalao = $(this).data('escalao');
                
                // Preencher campos do formulário
                $('#id_treino').val(id_treino);
                $('#data_treino').text(data);
                $('#horario_treino').text(hora_inicio + ' às ' + hora_fim);
                $('#local_treino').text(local);
                $('#escalao_treino').text(escalao);
                
                // Desmarcar todos os atletas
                $('.atleta-checkbox').prop('checked', false);
                $('#selecionar-todos').prop('checked', false);
                
                // Buscar atletas já convocados para este treino
                $.ajax({
                    url: 'get_atletas_convocados_treino.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {id_treino: id_treino},
                    success: function(response) {
                        if(response.atletas) {
                            // Marcar atletas já convocados
                            $.each(response.atletas, function(index, id_atleta) {
                                $('#atleta-' + id_atleta).prop('checked', true);
                            });
                        }
                    }
                });
                
                // Exibir formulário
                $('#sem-treino-selecionado').hide();
                $('#convocatoria-form').show();
            });
        });
    </script>
</body>

</html>
