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
$termo_pesquisa = isset($_GET['q']) ? trim($_GET['q']) : '';
$resultados_encontrados = false;

// Verificar se há um termo de pesquisa
if (!empty($termo_pesquisa)) {
    // Sanitizar o termo de pesquisa
    $termo = mysqli_real_escape_string($conn, $termo_pesquisa);
    
    // Usar LIKE com % antes e depois para pesquisa parcial
    $termo_busca = "%$termo%";
    
    // Pesquisar atletas
    $sql_atletas = "SELECT * FROM atletas 
                   WHERE codigo_clube = ? 
                   AND (nome LIKE ? 
                   OR cc LIKE ? 
                   OR nif LIKE ?
                   OR escalao LIKE ?
                   OR sub_escalao LIKE ?
                   OR posicao LIKE ?)
                   ORDER BY nome ASC";
                   
    $stmt_atletas = mysqli_prepare($conn, $sql_atletas);
    mysqli_stmt_bind_param($stmt_atletas, "sssssss", $codigo_clube, $termo_busca, $termo_busca, $termo_busca, $termo_busca, $termo_busca, $termo_busca);
    mysqli_stmt_execute($stmt_atletas);
    $result_atletas = mysqli_stmt_get_result($stmt_atletas);
    
    // Pesquisar treinos
    $sql_treinos = "SELECT * FROM treinos 
                   WHERE codigo_clube = ? 
                   AND (escalao LIKE ? 
                   OR local LIKE ?
                   OR tipo_treino LIKE ?)
                   ORDER BY data DESC, hora DESC";
                   
    $stmt_treinos = mysqli_prepare($conn, $sql_treinos);
    mysqli_stmt_bind_param($stmt_treinos, "ssss", $codigo_clube, $termo_busca, $termo_busca, $termo_busca);
    mysqli_stmt_execute($stmt_treinos);
    $result_treinos = mysqli_stmt_get_result($stmt_treinos);
    
    // Pesquisar jogos
    $sql_jogos = "SELECT * FROM jogos 
                 WHERE codigo_clube = ? 
                 AND (escalao LIKE ? 
                 OR local LIKE ?
                 OR adversario LIKE ?
                 OR tipo_jogo LIKE ?)
                 ORDER BY data DESC, hora DESC";
                 
    $stmt_jogos = mysqli_prepare($conn, $sql_jogos);
    mysqli_stmt_bind_param($stmt_jogos, "sssss", $codigo_clube, $termo_busca, $termo_busca, $termo_busca, $termo_busca);
    mysqli_stmt_execute($stmt_jogos);
    $result_jogos = mysqli_stmt_get_result($stmt_jogos);
    
    // Pesquisar documentos
    $sql_documentos = "SELECT d.*, a.nome as nome_atleta 
                      FROM documentos d
                      LEFT JOIN atletas a ON d.id_atleta = a.id_atleta
                      WHERE d.codigo_clube = ? 
                      AND (d.nome_documento LIKE ? 
                      OR d.tipo_documento LIKE ?
                      OR a.nome LIKE ?)
                      ORDER BY d.data_upload DESC";
                      
    $stmt_documentos = mysqli_prepare($conn, $sql_documentos);
    mysqli_stmt_bind_param($stmt_documentos, "ssss", $codigo_clube, $termo_busca, $termo_busca, $termo_busca);
    mysqli_stmt_execute($stmt_documentos);
    $result_documentos = mysqli_stmt_get_result($stmt_documentos);
    
    // Verificar se foram encontrados resultados
    $resultados_encontrados = (
        mysqli_num_rows($result_atletas) > 0 || 
        mysqli_num_rows($result_treinos) > 0 || 
        mysqli_num_rows($result_jogos) > 0 || 
        mysqli_num_rows($result_documentos) > 0
    );
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
    <title>ClubeMaster - Resultados da Pesquisa</title>

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
                        <h2>Resultados da Pesquisa</h2>
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
                        
                        <!-- Formulário de Pesquisa -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="pesquisa.php" method="get" class="mb-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="q" placeholder="Pesquisar por nome, CC, NIF, escalão, posição..." value="<?php echo htmlspecialchars($termo_pesquisa); ?>" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-search"></i> Pesquisar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <?php if(!empty($termo_pesquisa)): ?>
                            <h4>Resultados para: "<?php echo htmlspecialchars($termo_pesquisa); ?>"</h4>
                            
                            <?php if(!$resultados_encontrados): ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> Nenhum resultado encontrado para "<?php echo htmlspecialchars($termo_pesquisa); ?>". Tente outros termos de pesquisa.
                                </div>
                            <?php endif; ?>
                            
                            <!-- Resultados de Atletas -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Atletas</h5>
                                    <?php if(isset($result_atletas) && mysqli_num_rows($result_atletas) > 0): ?>
                                    <span class="badge badge-primary"><?php echo mysqli_num_rows($result_atletas); ?> encontrado(s)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if(isset($result_atletas) && mysqli_num_rows($result_atletas) > 0): ?>
                                        <div class="row">
                                            <?php while($atleta = mysqli_fetch_assoc($result_atletas)): ?>
                                            <div class="col-md-3 mb-4">
                                                <div class="card h-100">
                                                    <img src="<?php echo !empty($atleta['fotografia']) ? 'fotos_jogadores/' . $atleta['fotografia'] : 'imagens/sem_foto.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($atleta['nome']); ?>" style="height: 200px; object-fit: cover;">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($atleta['nome']); ?></h5>
                                                        <p class="card-text">
                                                            <strong>Escalão:</strong> <?php echo htmlspecialchars($atleta['escalao']); ?>
                                                            <?php if(!empty($atleta['sub_escalao'])): ?>
                                                            (<?php echo htmlspecialchars($atleta['sub_escalao']); ?>)
                                                            <?php endif; ?>
                                                            <br>
                                                            <strong>Posição:</strong> <?php echo htmlspecialchars($atleta['posicao']); ?>
                                                        </p>
                                                    </div>
                                                    <div class="card-footer">
                                                        <a href="editar_atleta.php?id=<?php echo $atleta['id_atleta']; ?>" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-user"></i> Ver Perfil
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Nenhum atleta encontrado.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Resultados de Treinos -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Treinos</h5>
                                    <?php if(isset($result_treinos) && mysqli_num_rows($result_treinos) > 0): ?>
                                    <span class="badge badge-primary"><?php echo mysqli_num_rows($result_treinos); ?> encontrado(s)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if(isset($result_treinos) && mysqli_num_rows($result_treinos) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Hora</th>
                                                        <th>Local</th>
                                                        <th>Escalão</th>
                                                        <th>Tipo</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while($treino = mysqli_fetch_assoc($result_treinos)): ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($treino['data'])); ?></td>
                                                        <td><?php echo date('H:i', strtotime($treino['hora'])); ?></td>
                                                        <td><?php echo htmlspecialchars($treino['local']); ?></td>
                                                        <td><?php echo htmlspecialchars($treino['escalao']); ?></td>
                                                        <td><?php echo htmlspecialchars($treino['tipo_treino']); ?></td>
                                                        <td>
                                                            <a href="editar_treino.php?id=<?php echo $treino['id_treino']; ?>" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-edit"></i> Editar
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Nenhum treino encontrado.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Resultados de Jogos -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Jogos</h5>
                                    <?php if(isset($result_jogos) && mysqli_num_rows($result_jogos) > 0): ?>
                                    <span class="badge badge-primary"><?php echo mysqli_num_rows($result_jogos); ?> encontrado(s)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if(isset($result_jogos) && mysqli_num_rows($result_jogos) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Hora</th>
                                                        <th>Local</th>
                                                        <th>Escalão</th>
                                                        <th>Adversário</th>
                                                        <th>Tipo</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while($jogo = mysqli_fetch_assoc($result_jogos)): ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($jogo['data'])); ?></td>
                                                        <td><?php echo date('H:i', strtotime($jogo['hora'])); ?></td>
                                                        <td><?php echo htmlspecialchars($jogo['local']); ?></td>
                                                        <td><?php echo htmlspecialchars($jogo['escalao']); ?></td>
                                                        <td><?php echo htmlspecialchars($jogo['adversario']); ?></td>
                                                        <td><?php echo htmlspecialchars($jogo['tipo_jogo']); ?></td>
                                                        <td>
                                                            <a href="editar_jogo.php?id=<?php echo $jogo['id_jogo']; ?>" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-edit"></i> Editar
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Nenhum jogo encontrado.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Resultados de Documentos -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Documentos</h5>
                                    <?php if(isset($result_documentos) && mysqli_num_rows($result_documentos) > 0): ?>
                                    <span class="badge badge-primary"><?php echo mysqli_num_rows($result_documentos); ?> encontrado(s)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if(isset($result_documentos) && mysqli_num_rows($result_documentos) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Nome do Documento</th>
                                                        <th>Tipo</th>
                                                        <th>Atleta</th>
                                                        <th>Data de Upload</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while($documento = mysqli_fetch_assoc($result_documentos)): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($documento['nome_documento']); ?></td>
                                                        <td><?php echo htmlspecialchars($documento['tipo_documento']); ?></td>
                                                        <td><?php echo htmlspecialchars($documento['nome_atleta']); ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($documento['data_upload'])); ?></td>
                                                        <td>
                                                            <a href="<?php echo htmlspecialchars($documento['caminho_arquivo']); ?>" class="btn btn-primary btn-sm" target="_blank">
                                                                <i class="fa fa-download"></i> Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Nenhum documento encontrado.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Digite um termo de pesquisa para encontrar atletas, treinos, jogos ou documentos.
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
</body>

</html>
