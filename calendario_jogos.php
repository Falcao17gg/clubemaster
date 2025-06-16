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
$adversario = "";
$tipo_jogo = "Amigável"; // Valor padrão
$gols_favor = 0;
$gols_contra = 0;
$observacoes = "";

// Processar formulário de adição de jogo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_adicionar'])) {
        $data = $_POST['data'];
        $hora = $_POST['hora'];
        $local = $_POST['local'];
        $escalao = $_POST['escalao'];
        $adversario = $_POST['adversario'];
        $tipo_jogo = $_POST['tipo_jogo'];
        $observacoes = $_POST['observacoes'];
        
        // Inserir jogo na base de dados
        $sql = "INSERT INTO jogos (codigo_clube, data, hora, local, escalao, adversario, tipo_jogo, observacoes) 
                VALUES ('$codigo_clube', '$data', '$hora', '$local', '$escalao', '$adversario', '$tipo_jogo', '$observacoes')";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Jogo agendado com sucesso!";
            // Limpar campos após sucesso
            $data = "";
            $hora = "";
            $local = "";
            $escalao = "";
            $adversario = "";
            $tipo_jogo = "Amigável";
            $observacoes = "";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Jogo agendado com sucesso!');
                window.location.href='calendario_jogos.php';
            </script>";
            exit();
        } else {
            $msg = "Erro ao agendar jogo: " . mysqli_error($conn);
        }
    }
    
    // Processar formulário de edição de jogo
    if(isset($_POST['btn_editar'])) {
        $id_jogo = $_POST['id_jogo'];
        $data = $_POST['data'];
        $hora = $_POST['hora'];
        $local = $_POST['local'];
        $escalao = $_POST['escalao'];
        $adversario = $_POST['adversario'];
        $tipo_jogo = $_POST['tipo_jogo'];
        $gols_favor = isset($_POST['gols_favor']) ? $_POST['gols_favor'] : 0;
        $gols_contra = isset($_POST['gols_contra']) ? $_POST['gols_contra'] : 0;
        $observacoes = $_POST['observacoes'];
        
        // Atualizar jogo na base de dados
        $sql = "UPDATE jogos SET 
                data = '$data', 
                hora = '$hora', 
                local = '$local', 
                escalao = '$escalao', 
                adversario = '$adversario',
                tipo_jogo = '$tipo_jogo',
                gols_favor = '$gols_favor',
                gols_contra = '$gols_contra',
                observacoes = '$observacoes' 
                WHERE id_jogo = '$id_jogo' AND codigo_clube = '$codigo_clube'";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Jogo atualizado com sucesso!";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Jogo atualizado com sucesso!');
                window.location.href='calendario_jogos.php';
            </script>";
            exit();
        } else {
            $msg = "Erro ao atualizar jogo: " . mysqli_error($conn);
        }
    }
    
    // Processar exclusão de jogo
    if(isset($_POST['btn_excluir'])) {
        $id_jogo = $_POST['id_jogo'];
        
        // Excluir jogo da base de dados
        $sql = "DELETE FROM jogos WHERE id_jogo = '$id_jogo' AND codigo_clube = '$codigo_clube'";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Jogo excluído com sucesso!";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Jogo excluído com sucesso!');
                window.location.href='calendario_jogos.php';
            </script>";
            exit();
        } else {
            $msg = "Erro ao excluir jogo: " . mysqli_error($conn);
        }
    }
}

// Buscar jogo específico para edição
if(isset($_GET['editar']) && !empty($_GET['editar'])) {
    $id_jogo = $_GET['editar'];
    
    $sql = "SELECT * FROM jogos WHERE id_jogo = '$id_jogo' AND codigo_clube = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $jogo = mysqli_fetch_assoc($result);
        $id_jogo = $jogo['id_jogo'];
        $data = $jogo['data'];
        $hora = $jogo['hora'];
        $local = $jogo['local'];
        $escalao = $jogo['escalao'];
        $adversario = $jogo['adversario'];
        $tipo_jogo = $jogo['tipo_jogo'];
        $gols_favor = $jogo['gols_favor'];
        $gols_contra = $jogo['gols_contra'];
        $observacoes = $jogo['observacoes'];
    }
}

// Buscar todos os jogos do clube
$sql_jogos = "SELECT * FROM jogos WHERE codigo_clube = '$codigo_clube' ORDER BY data DESC";
$result_jogos = mysqli_query($conn, $sql_jogos);

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

// Lista de tipos de jogo
$tipos_jogo = array(
    "Amigável",
    "Campeonato",
    "Taça",
    "Torneio"
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
    <title>ClubeMaster - Calendário de Jogos</title>

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
                        <h2>Calendário de Jogos</h2>
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
                            <h5>Calendário de Jogos</h5>
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
                            <h5><?php echo isset($_GET['editar']) ? 'Editar Jogo' : 'Agendar Novo Jogo'; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <?php if(isset($_GET['editar'])): ?>
                                <input type="hidden" name="id_jogo" value="<?php echo $id_jogo; ?>">
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
                                            <?php foreach($escaloes as $opcao): ?>
                                            <option value="<?php echo $opcao; ?>" <?php echo ($escalao == $opcao) ? 'selected' : ''; ?>><?php echo $opcao; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="adversario">Adversário</label>
                                        <input type="text" class="form-control" id="adversario" name="adversario" value="<?php echo $adversario; ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tipo_jogo">Tipo de Jogo</label>
                                        <select class="form-control" id="tipo_jogo" name="tipo_jogo" required>
                                            <?php foreach($tipos_jogo as $tipo): ?>
                                            <option value="<?php echo $tipo; ?>" <?php echo ($tipo_jogo == $tipo) ? 'selected' : ''; ?>><?php echo $tipo; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <?php if(isset($_GET['editar'])): ?>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="gols_favor">Gols a Favor</label>
                                        <input type="number" class="form-control" id="gols_favor" name="gols_favor" value="<?php echo $gols_favor; ?>" min="0">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="gols_contra">Gols Contra</label>
                                        <input type="number" class="form-control" id="gols_contra" name="gols_contra" value="<?php echo $gols_contra; ?>" min="0">
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="observacoes">Observações</label>
                                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo $observacoes; ?></textarea>
                                </div>
                                
                                <div class="form-group text-right">
                                    <?php if(isset($_GET['editar'])): ?>
                                    <button type="submit" name="btn_editar" class="btn btn-primary">Atualizar Jogo</button>
                                    <?php else: ?>
                                    <button type="submit" name="btn_adicionar" class="btn btn-primary">Agendar Jogo</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Lista de Jogos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Hora</th>
                                            <th>Local</th>
                                            <th>Escalão</th>
                                            <th>Adversário</th>
                                            <th>Tipo</th>
                                            <th>Resultado</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(mysqli_num_rows($result_jogos) > 0): ?>
                                            <?php while($jogo = mysqli_fetch_assoc($result_jogos)): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($jogo['data'])); ?></td>
                                                <td><?php echo date('H:i', strtotime($jogo['hora'])); ?></td>
                                                <td><?php echo $jogo['local']; ?></td>
                                                <td><?php echo $jogo['escalao']; ?></td>
                                                <td><?php echo $jogo['adversario']; ?></td>
                                                <td><?php echo $jogo['tipo_jogo']; ?></td>
                                                <td>
                                                    <?php 
                                                    if($jogo['gols_favor'] > 0 || $jogo['gols_contra'] > 0) {
                                                        echo $jogo['gols_favor'] . ' - ' . $jogo['gols_contra'];
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="calendario_jogos.php?editar=<?php echo $jogo['id_jogo']; ?>" class="btn btn-sm btn-info">Editar</a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#excluirModal<?php echo $jogo['id_jogo']; ?>">Excluir</button>
                                                    
                                                    <!-- Modal de Exclusão -->
                                                    <div class="modal fade" id="excluirModal<?php echo $jogo['id_jogo']; ?>" tabindex="-1" role="dialog" aria-labelledby="excluirModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="excluirModalLabel">Confirmar Exclusão</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Tem certeza que deseja excluir este jogo?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                                                        <input type="hidden" name="id_jogo" value="<?php echo $jogo['id_jogo']; ?>">
                                                                        <button type="submit" name="btn_excluir" class="btn btn-danger">Excluir</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">Nenhum jogo agendado.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
                        <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> ClubeMaster | Todos os direitos reservados</p>
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
    
    <!-- Fullcalendar -->
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
                locale: 'pt',
                navLinks: true,
                editable: false,
                eventLimit: true,
                events: [
                    <?php 
                    mysqli_data_seek($result_jogos, 0);
                    while($jogo = mysqli_fetch_assoc($result_jogos)): 
                        $titulo = $jogo['escalao'] . ' vs ' . $jogo['adversario'];
                        $resultado = '';
                        if($jogo['gols_favor'] > 0 || $jogo['gols_contra'] > 0) {
                            $resultado = ' (' . $jogo['gols_favor'] . '-' . $jogo['gols_contra'] . ')';
                        }
                    ?>
                    {
                        title: '<?php echo $titulo . $resultado; ?>',
                        start: '<?php echo $jogo['data'] . 'T' . $jogo['hora']; ?>',
                        url: 'calendario_jogos.php?editar=<?php echo $jogo['id_jogo']; ?>',
                        color: '<?php echo ($jogo['gols_favor'] > $jogo['gols_contra']) ? '#28a745' : (($jogo['gols_favor'] < $jogo['gols_contra']) ? '#dc3545' : '#007bff'); ?>'
                    },
                    <?php endwhile; ?>
                ],
                dayClick: function(date, jsEvent, view) {
                    // Ao clicar em um dia, preenche o campo de data no formulário
                    $('#data').val(date.format('YYYY-MM-DD'));
                    
                    // Rola a página até o formulário
                    $('html, body').animate({
                        scrollTop: $("form").offset().top - 100
                    }, 500);
                }
            });
        });
    </script>
</body>

</html>
