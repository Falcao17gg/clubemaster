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

// Processar formulário de presença
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_registrar'])) {
        $id_treino = $_POST['id_treino'];
        $presencas = isset($_POST['presenca']) ? $_POST['presenca'] : [];
        $justificacoes = isset($_POST['observacao']) ? $_POST['observacao'] : [];
        
        // Verificar se o treino pertence ao clube atual
        $sql_check_treino = "SELECT * FROM treinos WHERE id_treino = '$id_treino' AND codigo_clube = '$codigo_clube'";
        $result_check_treino = mysqli_query($conn, $sql_check_treino);
        if (mysqli_num_rows($result_check_treino) == 0) {
            $msg = "Treino não encontrado ou não pertence ao seu clube";
        } else {
            // Primeiro, excluir presenças anteriores para este treino
            $sql_delete = "DELETE FROM presencas_treino WHERE id_treino = '$id_treino'";
            mysqli_query($conn, $sql_delete);
            
            // Inserir novas presenças
            $success = true;
            foreach($presencas as $id_atleta => $presente) {
                $justificacao = isset($justificacoes[$id_atleta]) ? $justificacoes[$id_atleta] : '';
                
                $sql = "INSERT INTO presencas_treino (id_treino, id_atleta, presente, justificacao, data_registro) 
                        VALUES ('$id_treino', '$id_atleta', '$presente', '$justificacao', NOW())";
                
                if(!mysqli_query($conn, $sql)) {
                    $success = false;
                    $msg = "Erro ao registrar presença do atleta ID $id_atleta: " . mysqli_error($conn);
                    break;
                }
            }
            
            if($success) {
                $msg = "Presenças registradas com sucesso!";
                
                // Redirecionar após sucesso
                echo "<script>
                    alert('Presenças registradas com sucesso!');
                    window.location.href='presencas_treino.php';
                </script>";
                exit();
            }
        }
    }
}

// Buscar treinos agendados
$sql_treinos = "SELECT * FROM treinos WHERE codigo_clube = '$codigo_clube' ORDER BY data DESC LIMIT 30";
$result_treinos = mysqli_query($conn, $sql_treinos);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Presenças em Treinos</title>

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
                    <li><a href="./convocatorias_treino.php">Convocatórias</a></li>
                    <li class="active"><a href="./presencas_treino.php">Folhas de Presenças</a></li>
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
                                        <li><a href="./convocatorias_treino.php">Convocatórias</a></li>
                                        <li class="active"><a href="./presencas_treino.php">Folhas de Presenças</a></li>
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
                        <h2>Presenças em Treinos</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Attendance Section Begin -->
    <section class="attendance-section spad">
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
                <div class="col-lg-12">
                    <div class="section-title">
                        <h3>Folha de Presenças em Treinos</h3>
                        <p>Selecione um treino e registre as presenças dos atletas</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Treinos Realizados</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php 
                                if($result_treinos && mysqli_num_rows($result_treinos) > 0) {
                                    while($treino = mysqli_fetch_assoc($result_treinos)) {
                                        $data_formatada = date('d/m/Y', strtotime($treino['data']));
                                        $hora_inicio = date('H:i', strtotime($treino['hora_inicio']));
                                        $hora_fim = date('H:i', strtotime($treino['hora_fim']));
                                        
                                        // Verificar se já existe registro de presenças para este treino
                                        $sql_check = "SELECT COUNT(*) as total FROM presencas_treino WHERE id_treino = '" . $treino['id_treino'] . "'";
                                        $result_check = mysqli_query($conn, $sql_check);
                                        $row_check = mysqli_fetch_assoc($result_check);
                                        
                                        $status_class = ($row_check['total'] > 0) ? 'list-group-item-success' : '';
                                ?>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action treino-item <?php echo $status_class; ?>" data-id="<?php echo $treino['id_treino']; ?>" data-data="<?php echo $data_formatada; ?>" data-hora-inicio="<?php echo $hora_inicio; ?>" data-hora-fim="<?php echo $hora_fim; ?>" data-local="<?php echo $treino['local']; ?>" data-escalao="<?php echo $treino['escalao']; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Treino - <?php echo $treino['escalao']; ?></h5>
                                        <small><?php echo $data_formatada; ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo $treino['local']; ?> - <?php echo $hora_inicio; ?> às <?php echo $hora_fim; ?></p>
                                    
                                    <?php
                                    if($row_check['total'] > 0) {
                                        echo '<span class="badge badge-success">Presenças registradas</span>';
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
                                    <p class="mb-1">Nenhum treino encontrado.</p>
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
                            <h5>Folha de Presenças</h5>
                        </div>
                        <div class="card-body">
                            <div id="sem-treino-selecionado">
                                <div class="text-center">
                                    <p>Selecione um treino para registrar as presenças.</p>
                                </div>
                            </div>
                            
                            <div id="presencas-form" style="display: none;">
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
                                    
                                    <h4>Atletas Convocados</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Atleta</th>
                                                    <th>Presença</th>
                                                    <th>Justificação (se ausente)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="atletas-list">
                                                <!-- Lista de atletas será carregada via AJAX -->
                                                <tr>
                                                    <td colspan="3" class="text-center">Carregando atletas...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="form-group mt-4 text-right">
                                        <button type="submit" name="btn_registrar" class="btn btn-primary">Registrar Presenças</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Attendance Section End -->

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
            // Ao clicar em um treino, exibir formulário de presenças
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
                
                // Buscar atletas convocados para este treino
                $.ajax({
                    url: 'get_atletas_treino.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {id_treino: id_treino},
                    success: function(response) {
                        if(response.error) {
                            alert(response.error);
                            return;
                        }
                        
                        var html = '';
                        if(response.atletas.length > 0) {
                            $.each(response.atletas, function(index, atleta) {
                                var presente = 0;
                                var observacao = '';
                                
                                // Verificar se já existe registro de presença para este atleta
                                if(response.presencas[atleta.id_atleta]) {
                                    presente = response.presencas[atleta.id_atleta].presente;
                                    observacao = response.presencas[atleta.id_atleta].observacao;
                                }
                                
                                html += '<tr>';
                                html += '<td>' + atleta.nome + ' <small>(' + atleta.escalao + ')</small></td>';
                                html += '<td>';
                                html += '<div class="form-check form-check-inline">';
                                html += '<input class="form-check-input" type="radio" name="presenca[' + atleta.id_atleta + ']" id="presente-' + atleta.id_atleta + '" value="1" ' + (presente == 1 ? 'checked' : '') + '>';
                                html += '<label class="form-check-label" for="presente-' + atleta.id_atleta + '">Presente</label>';
                                html += '</div>';
                                html += '<div class="form-check form-check-inline">';
                                html += '<input class="form-check-input" type="radio" name="presenca[' + atleta.id_atleta + ']" id="ausente-' + atleta.id_atleta + '" value="0" ' + (presente == 0 ? 'checked' : '') + '>';
                                html += '<label class="form-check-label" for="ausente-' + atleta.id_atleta + '">Ausente</label>';
                                html += '</div>';
                                html += '</td>';
                                html += '<td>';
                                html += '<input type="text" class="form-control" name="observacao[' + atleta.id_atleta + ']" value="' + observacao + '" placeholder="Motivo da ausência">';
                                html += '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="3" class="text-center">Nenhum atleta encontrado. Verifique se há atletas cadastrados e convocados para este treino.</td></tr>';
                        }
                        
                        $('#atletas-list').html(html);
                    },
                    error: function() {
                        alert('Erro ao carregar atletas. Por favor, tente novamente.');
                    }
                });
                
                // Exibir formulário
                $('#sem-treino-selecionado').hide();
                $('#presencas-form').show();
            });
        });
    </script>
</body>

</html>
