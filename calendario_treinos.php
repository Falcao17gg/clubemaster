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
$id_atleta = "";
$data = "";
$hora = "";
$local = "";
$escalao = "";
$tipo_treino = "Normal"; // Valor padrão
$observacoes = "";

// Processar formulário de adição de treino
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_adicionar'])) {
        $data = mysqli_real_escape_string($conn, $_POST['data']);
        $hora = mysqli_real_escape_string($conn, $_POST['hora']);
        $local = mysqli_real_escape_string($conn, $_POST['local']);
        $escalao = mysqli_real_escape_string($conn, $_POST['escalao']);
        $tipo_treino = mysqli_real_escape_string($conn, $_POST['tipo_treino']);
        $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes']);
        
        // Inserir treino na base de dados
        $sql = "INSERT INTO treinos (codigo_clube, data, hora, local, escalao, tipo_treino, observacoes) 
                VALUES ('$codigo_clube', '$data', '$hora', '$local', '$escalao', '$tipo_treino', '$observacoes')";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Treino agendado com sucesso!";
            // Limpar campos após sucesso
            $data = "";
            $hora = "";
            $local = "";
            $escalao = "";
            $tipo_treino = "Normal";
            $observacoes = "";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Treino agendado com sucesso!');
                window.location.href='calendario_treinos.php';
            </script>";
            exit();
        } else {
            $msg = "Erro ao agendar treino: " . mysqli_error($conn);
        }
    }
    
    // Processar formulário de edição de treino
    if(isset($_POST['btn_editar'])) {
        $id_treino = mysqli_real_escape_string($conn, $_POST['id_treino']);
        $data = mysqli_real_escape_string($conn, $_POST['data']);
        $hora = mysqli_real_escape_string($conn, $_POST['hora']);
        $local = mysqli_real_escape_string($conn, $_POST['local']);
        $escalao = mysqli_real_escape_string($conn, $_POST['escalao']);
        $tipo_treino = mysqli_real_escape_string($conn, $_POST['tipo_treino']);
        $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes']);
        
        // Atualizar treino na base de dados
        $sql = "UPDATE treinos SET 
                data = '$data', 
                hora = '$hora', 
                local = '$local', 
                escalao = '$escalao',
                tipo_treino = '$tipo_treino',
                observacoes = '$observacoes' 
                WHERE id_treino = '$id_treino' AND codigo_clube = '$codigo_clube'";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Treino atualizado com sucesso!";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Treino atualizado com sucesso!');
                window.location.href='calendario_treinos.php';
            </script>";
            exit();
        } else {
            $msg = "Erro ao atualizar treino: " . mysqli_error($conn);
        }
    }
    
    // Processar exclusão de treino
    if(isset($_POST['btn_excluir'])) {
        $id_treino = mysqli_real_escape_string($conn, $_POST['id_treino']);
        
        // Verificar se existem convocatórias ou presenças associadas
        $sql_check = "SELECT COUNT(*) as total FROM convocatorias_treino WHERE id_treino = '$id_treino'";
        $result_check = mysqli_query($conn, $sql_check);
        $row_check = mysqli_fetch_assoc($result_check);
        
        $sql_check2 = "SELECT COUNT(*) as total FROM presencas_treino WHERE id_treino = '$id_treino'";
        $result_check2 = mysqli_query($conn, $sql_check2);
        $row_check2 = mysqli_fetch_assoc($result_check2);
        
        if($row_check['total'] > 0 || $row_check2['total'] > 0) {
            $msg = "Não é possível excluir este treino pois existem convocatórias ou presenças associadas.";
        } else {
            // Excluir treino da base de dados
            $sql = "DELETE FROM treinos WHERE id_treino = '$id_treino' AND codigo_clube = '$codigo_clube'";
            
            if(mysqli_query($conn, $sql)) {
                $msg = "Treino excluído com sucesso!";
                
                // Redirecionar após sucesso
                echo "<script>
                    alert('Treino excluído com sucesso!');
                    window.location.href='calendario_treinos.php';
                </script>";
                exit();
            } else {
                $msg = "Erro ao excluir treino: " . mysqli_error($conn);
            }
        }
    }
}

// Buscar treino específico para edição
if(isset($_GET['editar']) && !empty($_GET['editar'])) {
    $id_treino = mysqli_real_escape_string($conn, $_GET['editar']);
    
    $sql = "SELECT * FROM treinos WHERE id_treino = '$id_treino' AND codigo_clube = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $treino = mysqli_fetch_assoc($result);
        $id_treino = $treino['id_treino'];
        $data = $treino['data'];
        $hora = $treino['hora'];
        $local = $treino['local'];
        $escalao = $treino['escalao'];
        $tipo_treino = $treino['tipo_treino'];
        $observacoes = $treino['observacoes'];
    }
}

// Lista de escalões disponíveis
$escaloes = array(
    "Pré-Petizes (Sub-5)",
    "Petizes (Sub-6)",
    "Petizes (Sub-7)",
    "Traquinas (Sub-8)",
    "Traquinas (Sub-9)",
    "Benjamins (Sub-10)",
    "Benjamins (Sub-11)",
    "Infantis (Sub-12)",
    "Infantis (Sub-13)",
    "Iniciados (Sub-14)",
    "Iniciados (Sub-15)",
    "Juvenis (Sub-16)",
    "Juvenis (Sub-17)",
    "Juniores (Sub-18)",
    "Juniores (Sub-19)",
    "Sub-23 (B)",
    "Seniores",
    "Veteranos"
);

// Lista de tipos de treino
$tipos_treino = array(
    "Normal",
    "Tático",
    "Físico",
    "Técnico",
    "Recuperação",
    "Jogo-Treino"
);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Calendário de Treinos</title>

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
    
    <!-- Fullcalendar -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css' rel='stylesheet' media='print' />
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
            <li class="active"><a href="#">Treinos</a>
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
                                    <ul class="dropdown">
                                        <li><a href="./perfil.php">Perfil</a></li>
                                        <li><a href="./ficha_clinica.php">Ficha Clínica</a></li>
                                        <li><a href="./documentos.php">Upload de Documentos</a></li>
                                        <li><a href="./historico.php">Histórico</a></li>
                                    </ul>
                                </li>
                                <li class="active"><a href="#">Treinos</a>
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
                        <h2>Calendário de Treinos</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Calendar Section Begin -->
    <section class="calendar-section spad">
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
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Calendário de Treinos</h5>
                        </div>
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><?php echo isset($_GET['editar']) ? 'Editar Treino' : 'Agendar Novo Treino'; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <?php if(isset($_GET['editar'])): ?>
                                <input type="hidden" name="id_treino" value="<?php echo $id_treino; ?>">
                                <?php endif; ?>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="data">Data</label>
                                        <input type="date" class="form-control" id="data" name="data" value="<?php echo $data; ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="hora">Hora</label>
                                        <input type="time" class="form-control" id="hora" name="hora" value="<?php echo $hora; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="local">Local</label>
                                        <input type="text" class="form-control" id="local" name="local" value="<?php echo $local; ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="escalao">Escalão</label>
                                        <select class="form-control" id="escalao" name="escalao" required>
                                            <option value="">Selecione...</option>
                                            <?php foreach($escaloes as $esc): ?>
                                            <option value="<?php echo $esc; ?>" <?php echo ($escalao == $esc) ? 'selected' : ''; ?>><?php echo $esc; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="tipo_treino">Tipo de Treino</label>
                                        <select class="form-control" id="tipo_treino" name="tipo_treino" required>
                                            <?php foreach($tipos_treino as $tipo): ?>
                                            <option value="<?php echo $tipo; ?>" <?php echo ($tipo_treino == $tipo) ? 'selected' : ''; ?>><?php echo $tipo; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="observacoes">Observações</label>
                                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo $observacoes; ?></textarea>
                                </div>
                                
                                <div class="form-group text-center">
                                    <?php if(isset($_GET['editar'])): ?>
                                    <button type="submit" name="btn_editar" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Atualizar Treino
                                    </button>
                                    <button type="submit" name="btn_excluir" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este treino?');">
                                        <i class="fa fa-trash"></i> Excluir Treino
                                    </button>
                                    <?php else: ?>
                                    <button type="submit" name="btn_adicionar" class="btn btn-success">
                                        <i class="fa fa-plus"></i> Agendar Treino
                                    </button>
                                    <?php endif; ?>
                                    <a href="calendario_treinos.php" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Calendar Section End -->

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
    
    <!-- FullCalendar -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/pt.js'></script>
    
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultDate: '<?php echo date("Y-m-d"); ?>',
                locale: 'pt',
                navLinks: true,
                editable: false,
                eventLimit: true,
                events: 'calendario_interativo.php?tipo=treinos',
                eventClick: function(calEvent, jsEvent, view) {
                    // Já está configurado para redirecionar para a página de edição
                },
                dayClick: function(date, jsEvent, view) {
                    // Ao clicar em um dia vazio, redireciona para adicionar um novo treino nessa data
                    window.location.href = 'calendario_treinos.php?data=' + date.format();
                }
            });
            
            // Se houver um parâmetro de data na URL, preenche o campo de data
            <?php if(isset($_GET['data']) && !isset($_GET['editar'])): ?>
            $('#data').val('<?php echo $_GET['data']; ?>');
            <?php endif; ?>
        });
    </script>
</body>

</html>
