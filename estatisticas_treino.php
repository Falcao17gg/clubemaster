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

// Processar filtros
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d', strtotime('-30 days'));
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d');
$filtro_escalao = isset($_GET['escalao']) ? $_GET['escalao'] : '';
$filtro_atleta = isset($_GET['atleta']) ? $_GET['atleta'] : '';

// Obter lista de atletas para o filtro
$sql_atletas = "SELECT id_atleta, nome FROM atletas WHERE codigo_clube = '$codigo_clube'";
if (!empty($filtro_escalao) && $filtro_escalao != 'Todos') {
    $sql_atletas .= " AND escalao = '$filtro_escalao'";
}
$sql_atletas .= " ORDER BY nome ASC";
$result_atletas = mysqli_query($conn, $sql_atletas);

// Obter dados de presenças em treinos por escalão
$sql_presencas = "SELECT t.escalao, COUNT(pt.id_presenca) as total_presencas, 
                 SUM(CASE WHEN pt.presente = 1 THEN 1 ELSE 0 END) as presentes,
                 SUM(CASE WHEN pt.presente = 0 THEN 1 ELSE 0 END) as faltas
                 FROM treinos t
                 LEFT JOIN presencas_treino pt ON t.id_treino = pt.id_treino
                 WHERE t.codigo_clube = '$codigo_clube'
                 AND t.data BETWEEN '$data_inicio' AND '$data_fim'";

if (!empty($filtro_escalao) && $filtro_escalao != 'Todos') {
    $sql_presencas .= " AND t.escalao = '$filtro_escalao'";
}

if (!empty($filtro_atleta) && $filtro_atleta != 'Todos') {
    $sql_presencas .= " AND pt.id_atleta = '$filtro_atleta'";
}

$sql_presencas .= " GROUP BY t.escalao";
$result_presencas = mysqli_query($conn, $sql_presencas);

// Obter dados de avaliação média por escalão
$sql_avaliacao = "SELECT t.escalao, AVG(pt.avaliacao) as media_avaliacao
                 FROM treinos t
                 LEFT JOIN presencas_treino pt ON t.id_treino = pt.id_treino
                 WHERE t.codigo_clube = '$codigo_clube'
                 AND t.data BETWEEN '$data_inicio' AND '$data_fim'
                 AND pt.presente = 1
                 AND pt.avaliacao IS NOT NULL";

if (!empty($filtro_escalao) && $filtro_escalao != 'Todos') {
    $sql_avaliacao .= " AND t.escalao = '$filtro_escalao'";
}

if (!empty($filtro_atleta) && $filtro_atleta != 'Todos') {
    $sql_avaliacao .= " AND pt.id_atleta = '$filtro_atleta'";
}

$sql_avaliacao .= " GROUP BY t.escalao";
$result_avaliacao = mysqli_query($conn, $sql_avaliacao);

// Preparar dados para os gráficos
$escaloes = [];
$presencas = [];
$faltas = [];
$avaliacoes = [];

while ($row = mysqli_fetch_assoc($result_presencas)) {
    $escaloes[] = $row['escalao'];
    $presencas[] = $row['presentes'];
    $faltas[] = $row['faltas'];
}

$escaloes_avaliacao = [];
$media_avaliacao = [];

while ($row = mysqli_fetch_assoc($result_avaliacao)) {
    $escaloes_avaliacao[] = $row['escalao'];
    $media_avaliacao[] = round($row['media_avaliacao'], 1);
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
    <title>ClubeMaster - Estatísticas de Treinos</title>

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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <li><a href="./presencas_treino.php">Folhas de Presenças</a></li>
                    <li class="active"><a href="./estatisticas_treino.php">Estatísticas</a></li>
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
                                        <li><a href="./presencas_treino.php">Folhas de Presenças</a></li>
                                        <li class="active"><a href="./estatisticas_treino.php">Estatísticas</a></li>
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
                        <h2>ESTATÍSTICAS DE TREINOS</h2>
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
                        
                        <!-- Filtros -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fa fa-filter"></i> Filtros
                            </div>
                            <div class="card-body">
                                <form action="estatisticas_treino.php" method="get">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="data_inicio">Data Início</label>
                                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?php echo $data_inicio; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="data_fim">Data Fim</label>
                                                <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?php echo $data_fim; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="escalao">Escalão</label>
                                                <select class="form-control" id="escalao" name="escalao">
                                                    <option value="Todos">Todos</option>
                                                    <?php foreach($todos_escaloes as $escalao => $sub_escaloes): ?>
                                                    <option value="<?php echo $escalao; ?>" <?php echo ($filtro_escalao == $escalao) ? 'selected' : ''; ?>><?php echo $escalao; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="atleta">Atleta</label>
                                                <select class="form-control" id="atleta" name="atleta">
                                                    <option value="Todos">Todos</option>
                                                    <?php while($atleta = mysqli_fetch_assoc($result_atletas)): ?>
                                                    <option value="<?php echo $atleta['id_atleta']; ?>" <?php echo ($filtro_atleta == $atleta['id_atleta']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($atleta['nome']); ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-filter"></i> Filtrar
                                            </button>
                                            <a href="estatisticas_treino.php" class="btn btn-secondary ml-2">
                                                <i class="fa fa-eraser"></i> Limpar Filtros
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Estatísticas -->
                        <div class="row">
                            <!-- Presenças em Treinos -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5>Presenças em Treinos</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="presencasChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Avaliação Média -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5>Avaliação Média por Escalão</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="avaliacaoChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabela de Estatísticas Detalhadas -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Estatísticas Detalhadas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Escalão</th>
                                                <th>Total Presenças</th>
                                                <th>Total Faltas</th>
                                                <th>Taxa de Presença</th>
                                                <th>Avaliação Média</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Reset pointer
                                            mysqli_data_seek($result_presencas, 0);
                                            
                                            while ($row = mysqli_fetch_assoc($result_presencas)) {
                                                $total = $row['presentes'] + $row['faltas'];
                                                $taxa_presenca = $total > 0 ? round(($row['presentes'] / $total) * 100, 1) : 0;
                                                
                                                // Buscar avaliação média para este escalão
                                                $avaliacao_media = 0;
                                                mysqli_data_seek($result_avaliacao, 0);
                                                while ($row_avaliacao = mysqli_fetch_assoc($result_avaliacao)) {
                                                    if ($row_avaliacao['escalao'] == $row['escalao']) {
                                                        $avaliacao_media = round($row_avaliacao['media_avaliacao'], 1);
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['escalao']); ?></td>
                                                <td><?php echo $row['presentes']; ?></td>
                                                <td><?php echo $row['faltas']; ?></td>
                                                <td><?php echo $taxa_presenca; ?>%</td>
                                                <td><?php echo $avaliacao_media; ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
    
    <script>
        // Gráfico de Presenças
        var ctxPresencas = document.getElementById('presencasChart').getContext('2d');
        var presencasChart = new Chart(ctxPresencas, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($escaloes); ?>,
                datasets: [
                    {
                        label: 'Presenças',
                        data: <?php echo json_encode($presencas); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Faltas',
                        data: <?php echo json_encode($faltas); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantidade'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Escalão'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Presenças por Escalão'
                    },
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
        
        // Gráfico de Avaliação Média
        var ctxAvaliacao = document.getElementById('avaliacaoChart').getContext('2d');
        var avaliacaoChart = new Chart(ctxAvaliacao, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($escaloes_avaliacao); ?>,
                datasets: [{
                    label: 'Avaliação Média',
                    data: <?php echo json_encode($media_avaliacao); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        title: {
                            display: true,
                            text: 'Avaliação (0-5)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Escalão'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Avaliação Média por Escalão'
                    },
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
        
        // Atualizar filtro de atletas quando o escalão mudar
        document.getElementById('escalao').addEventListener('change', function() {
            var escalao = this.value;
            var atletaSelect = document.getElementById('atleta');
            
            // Limpar opções atuais
            atletaSelect.innerHTML = '<option value="Todos">Todos</option>';
            
            // Se "Todos" selecionado, não fazer nada mais
            if (escalao === 'Todos') {
                return;
            }
            
            // Fazer requisição AJAX para obter atletas do escalão selecionado
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_atletas_por_escalao.php?escalao=' + encodeURIComponent(escalao), true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var atletas = JSON.parse(xhr.responseText);
                    atletas.forEach(function(atleta) {
                        var option = document.createElement('option');
                        option.value = atleta.id_atleta;
                        option.textContent = atleta.nome;
                        atletaSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        });
    </script>
</body>

</html>
