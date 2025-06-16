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
        $id_jogo = $_POST['id_jogo'];
        $presencas = isset($_POST['presenca']) ? $_POST['presenca'] : [];
        $justificacoes = isset($_POST['observacao']) ? $_POST['observacao'] : [];
        
        // Verificar se o jogo pertence ao clube atual
        $sql_check_jogo = "SELECT * FROM jogos WHERE id_jogo = '$id_jogo' AND codigo_clube = '$codigo_clube'";
        $result_check_jogo = mysqli_query($conn, $sql_check_jogo);
        if (mysqli_num_rows($result_check_jogo) == 0) {
            $msg = "Jogo não encontrado ou não pertence ao seu clube";
        } else {
            // Primeiro, excluir presenças anteriores para este jogo
            $sql_delete = "DELETE FROM presencas_jogo WHERE id_jogo = '$id_jogo'";
            mysqli_query($conn, $sql_delete);
            
            // Inserir novas presenças
            $success = true;
            foreach($presencas as $id_atleta => $presente) {
                $justificacao = isset($justificacoes[$id_atleta]) ? $justificacoes[$id_atleta] : '';
                
                $sql = "INSERT INTO presencas_jogo (id_jogo, id_atleta, presente, justificacao, data_registro) 
                        VALUES ('$id_jogo', '$id_atleta', '$presente', '$justificacao', NOW())";
                
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
                    window.location.href='presencas_jogo.php';
                </script>";
                exit();
            }
        }
    }
}

// Buscar jogos agendados
$sql_jogos = "SELECT * FROM jogos WHERE codigo_clube = '$codigo_clube' ORDER BY data DESC LIMIT 30";
$result_jogos = mysqli_query($conn, $sql_jogos);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Presenças em Jogos</title>

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
                    <li><a href="./convocatorias_jogo.php">Convocatórias</a></li>
                    <li class="active"><a href="./presencas_jogo.php">Folhas de Presenças</a></li>
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
                                        <li><a href="./convocatorias_jogo.php">Convocatórias</a></li>
                                        <li class="active"><a href="./presencas_jogo.php">Folhas de Presenças</a></li>
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
                        <h2>Presenças em Jogos</h2>
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
                        <h3>Folha de Presenças em Jogos</h3>
                        <p>Selecione um jogo e registre as presenças dos atletas</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Jogos Realizados</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php 
                                if($result_jogos && mysqli_num_rows($result_jogos) > 0) {
                                    while($jogo = mysqli_fetch_assoc($result_jogos)) {
                                        $data_formatada = date('d/m/Y', strtotime($jogo['data']));
                                        $hora_formatada = date('H:i', strtotime($jogo['hora']));
                                        
                                        // Verificar se já existe registro de presenças para este jogo
                                        $sql_check = "SELECT COUNT(*) as total FROM presencas_jogo WHERE id_jogo = '" . $jogo['id_jogo'] . "'";
                                        $result_check = mysqli_query($conn, $sql_check);
                                        $row_check = mysqli_fetch_assoc($result_check);
                                        
                                        $status_class = ($row_check['total'] > 0) ? 'list-group-item-success' : '';
                                ?>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action jogo-item <?php echo $status_class; ?>" data-id="<?php echo $jogo['id_jogo']; ?>" data-data="<?php echo $data_formatada; ?>" data-hora="<?php echo $hora_formatada; ?>" data-local="<?php echo $jogo['local']; ?>" data-adversario="<?php echo $jogo['adversario']; ?>" data-gols-favor="<?php echo $jogo['gols_favor']; ?>" data-gols-contra="<?php echo $jogo['gols_contra']; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">vs <?php echo $jogo['adversario']; ?></h5>
                                        <small><?php echo $data_formatada; ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo $jogo['local']; ?> - <?php echo $hora_formatada; ?></p>
                                    <?php if(isset($jogo['gols_favor']) && isset($jogo['gols_contra'])): ?>
                                    <p class="mb-1"><strong>Resultado:</strong> <?php echo $jogo['gols_favor'] . ' - ' . $jogo['gols_contra']; ?></p>
                                    <?php endif; ?>
                                    
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
                                    <p class="mb-1">Nenhum jogo encontrado.</p>
                                    <a href="calendario_jogos.php" class="btn btn-sm btn-primary">Agendar Jogo</a>
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
                            <div id="sem-jogo-selecionado">
                                <div class="text-center">
                                    <p>Selecione um jogo para registrar as presenças.</p>
                                </div>
                            </div>
                            
                            <div id="presencas-form" style="display: none;">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    <input type="hidden" id="id_jogo" name="id_jogo">
                                    
                                    <div class="jogo-info mb-4">
                                        <h4>Detalhes do Jogo</h4>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p><strong>Data:</strong> <span id="data_jogo"></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Hora:</strong> <span id="hora_jogo"></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Local:</strong> <span id="local_jogo"></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Adversário:</strong> <span id="adversario"></span></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><strong>Resultado:</strong> <span id="resultado"></span></p>
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
            // Ao clicar em um jogo, exibir formulário de presenças
            $('.jogo-item').click(function() {
                var id_jogo = $(this).data('id');
                var data = $(this).data('data');
                var hora = $(this).data('hora');
                var local = $(this).data('local');
                var adversario = $(this).data('adversario');
                var gols_favor = $(this).data('gols-favor');
                var gols_contra = $(this).data('gols-contra');
                
                // Preencher campos do formulário
                $('#id_jogo').val(id_jogo);
                $('#data_jogo').text(data);
                $('#hora_jogo').text(hora);
                $('#local_jogo').text(local);
                $('#adversario').text(adversario);
                
                // Mostrar resultado se disponível
                if (gols_favor !== undefined && gols_contra !== undefined) {
                    $('#resultado').text(gols_favor + ' - ' + gols_contra);
                } else {
                    $('#resultado').text('Não disponível');
                }
                
                // Buscar atletas convocados para este jogo
                $.ajax({
                    url: 'get_atletas_jogo.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {id_jogo: id_jogo},
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
                            html = '<tr><td colspan="3" class="text-center">Nenhum atleta encontrado. Verifique se há atletas cadastrados e convocados para este jogo.</td></tr>';
                        }
                        
                        $('#atletas-list').html(html);
                    },
                    error: function() {
                        alert('Erro ao carregar atletas. Por favor, tente novamente.');
                    }
                });
                
                // Exibir formulário
                $('#sem-jogo-selecionado').hide();
                $('#presencas-form').show();
            });
        });
    </script>
</body>

</html>
