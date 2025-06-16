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

// Processar exclusão de atleta se solicitado
if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $id_atleta = mysqli_real_escape_string($conn, $_GET['excluir']);
    
    // Verificar se o atleta pertence ao clube
    $check_sql = "SELECT id_atleta FROM atletas WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Excluir o atleta
        $delete_sql = "DELETE FROM atletas WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
        if (mysqli_query($conn, $delete_sql)) {
            $msg = "Atleta excluído com sucesso!";
            $msg_type = "success";
        } else {
            $msg = "Erro ao excluir atleta: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    } else {
        $msg = "Atleta não encontrado ou não pertence ao seu clube.";
        $msg_type = "warning";
    }
}

// Processar filtros
$filtro_escalao = isset($_GET['escalao']) ? $_GET['escalao'] : '';
$filtro_sub_escalao = isset($_GET['sub_escalao']) ? $_GET['sub_escalao'] : '';
$filtro_posicao = isset($_GET['posicao']) ? $_GET['posicao'] : '';
$filtro_status = isset($_GET['status']) ? $_GET['status'] : '';
$filtro_pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// Construir consulta SQL com filtros
$sql = "SELECT * FROM atletas WHERE codigo_clube = '$codigo_clube'";

if (!empty($filtro_escalao) && $filtro_escalao != 'Todos') {
    $sql .= " AND escalao = '" . mysqli_real_escape_string($conn, $filtro_escalao) . "'";
}

if (!empty($filtro_sub_escalao) && $filtro_sub_escalao != 'Todos') {
    $sql .= " AND sub_escalao = '" . mysqli_real_escape_string($conn, $filtro_sub_escalao) . "'";
}

if (!empty($filtro_posicao) && $filtro_posicao != 'Todas') {
    $sql .= " AND posicao = '" . mysqli_real_escape_string($conn, $filtro_posicao) . "'";
}

if (!empty($filtro_status) && $filtro_status != 'Todos') {
    $sql .= " AND status = '" . mysqli_real_escape_string($conn, $filtro_status) . "'";
}

if (!empty($filtro_pesquisa)) {
    $filtro_pesquisa = mysqli_real_escape_string($conn, $filtro_pesquisa);
    $sql .= " AND (nome LIKE '%$filtro_pesquisa%' OR cc LIKE '%$filtro_pesquisa%' OR nif LIKE '%$filtro_pesquisa%')";
}

$sql .= " ORDER BY nome ASC";
$result = mysqli_query($conn, $sql);

// Verificar se há mensagem na sessão
if (isset($_SESSION['msg']) && isset($_SESSION['msg_type'])) {
    $msg = $_SESSION['msg'];
    $msg_type = $_SESSION['msg_type'];
    unset($_SESSION['msg']);
    unset($_SESSION['msg_type']);
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
    <title>ClubeMaster - Atletas</title>

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
            <li class="active"><a href="./atletas.php">Atletas</a></li>
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
                                <li class="active"><a href="./atletas.php">Atletas</a></li>
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
                        <h2>Atletas</h2>
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
                                <h4>Lista de Atletas</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="novoatleta.php" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Novo Atleta
                                </a>
                            </div>
                        </div>
                        
                        <!-- Barra de Pesquisa -->
                        <div class="row mb-4">
                            <div class="col-lg-12">
                                <form action="atletas.php" method="get" class="mb-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Pesquisar por nome, CC ou NIF..." name="pesquisa" value="<?php echo htmlspecialchars($filtro_pesquisa); ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-search"></i> PESQUISAR
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Filtros -->
                        <div class="row mb-4">
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
                                    <label for="sub_escalao">Sub-Escalão</label>
                                    <select class="form-control" id="sub_escalao" name="sub_escalao">
                                        <option value="Todos">Todos</option>
                                        <!-- Opções serão carregadas via JavaScript -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="posicao">Posição</label>
                                    <select class="form-control" id="posicao" name="posicao">
                                        <option value="Todas" <?php echo ($filtro_posicao == 'Todas') ? 'selected' : ''; ?>>Todas</option>
                                        <option value="Guarda-Redes" <?php echo ($filtro_posicao == 'Guarda-Redes') ? 'selected' : ''; ?>>Guarda-Redes</option>
                                        <option value="Defesa" <?php echo ($filtro_posicao == 'Defesa') ? 'selected' : ''; ?>>Defesa</option>
                                        <option value="Médio" <?php echo ($filtro_posicao == 'Médio') ? 'selected' : ''; ?>>Médio</option>
                                        <option value="Avançado" <?php echo ($filtro_posicao == 'Avançado') ? 'selected' : ''; ?>>Avançado</option>
                                        <option value="Defesa-Central" <?php echo ($filtro_posicao == 'Defesa-Central') ? 'selected' : ''; ?>>Defesa-Central</option>
                                        <option value="Defesa-Direito" <?php echo ($filtro_posicao == 'Defesa-Direito') ? 'selected' : ''; ?>>Defesa-Direito</option>
                                        <option value="Defesa-Esquerdo" <?php echo ($filtro_posicao == 'Defesa-Esquerdo') ? 'selected' : ''; ?>>Defesa-Esquerdo</option>
                                        <option value="Médio-Defensivo" <?php echo ($filtro_posicao == 'Médio-Defensivo') ? 'selected' : ''; ?>>Médio-Defensivo</option>
                                        <option value="Médio-Centro" <?php echo ($filtro_posicao == 'Médio-Centro') ? 'selected' : ''; ?>>Médio-Centro</option>
                                        <option value="Médio-Ofensivo" <?php echo ($filtro_posicao == 'Médio-Ofensivo') ? 'selected' : ''; ?>>Médio-Ofensivo</option>
                                        <option value="Extremo-Direito" <?php echo ($filtro_posicao == 'Extremo-Direito') ? 'selected' : ''; ?>>Extremo-Direito</option>
                                        <option value="Extremo-Esquerdo" <?php echo ($filtro_posicao == 'Extremo-Esquerdo') ? 'selected' : ''; ?>>Extremo-Esquerdo</option>
                                        <option value="Ponta-de-Lança" <?php echo ($filtro_posicao == 'Ponta-de-Lança') ? 'selected' : ''; ?>>Ponta-de-Lança</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Todos" <?php echo ($filtro_status == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Ativo" <?php echo ($filtro_status == 'Ativo') ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="Inativo" <?php echo ($filtro_status == 'Inativo') ? 'selected' : ''; ?>>Inativo</option>
                                        <option value="Lesionado" <?php echo ($filtro_status == 'Lesionado') ? 'selected' : ''; ?>>Lesionado</option>
                                        <option value="Suspenso" <?php echo ($filtro_status == 'Suspenso') ? 'selected' : ''; ?>>Suspenso</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-danger" id="aplicarFiltros">
                                    <i class="fa fa-filter"></i> APLICAR FILTROS
                                </button>
                                <button type="button" class="btn btn-secondary ml-2" id="limparFiltros">
                                    <i class="fa fa-eraser"></i> Limpar Filtros
                                </button>
                            </div>
                        </div>
                        
                        <!-- Lista de Atletas -->
                        <div class="row">
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($atleta = mysqli_fetch_assoc($result)): ?>
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100">
                                        <img src="<?php echo !empty($atleta['fotografia']) && $atleta['fotografia'] != 'no_image.png' ? 'fotos_jogadores/'.$atleta['fotografia'] : 'img/default-player.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($atleta['nome']); ?>" style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($atleta['nome']); ?></h5>
                                            <p class="card-text">
                                                <strong>Escalão:</strong> <?php echo htmlspecialchars($atleta['escalao']); ?> 
                                                <?php if(!empty($atleta['sub_escalao'])): ?>
                                                (<?php echo htmlspecialchars($atleta['sub_escalao']); ?>)
                                                <?php endif; ?>
                                                <br>
                                                <strong>Posição:</strong> <?php echo htmlspecialchars($atleta['posicao']); ?><br>
                                                <strong>Status:</strong> 
                                                <span class="badge <?php 
                                                    $status = isset($atleta['status']) && !empty($atleta['status']) ? $atleta['status'] : 'Ativo';
                                                    switch($status) {
                                                        case 'Ativo': echo 'badge-success'; break;
                                                        case 'Inativo': echo 'badge-secondary'; break;
                                                        case 'Lesionado': echo 'badge-danger'; break;
                                                        case 'Suspenso': echo 'badge-warning'; break;
                                                        default: echo 'badge-info';
                                                    }
                                                ?>">
                                                    <?php echo isset($atleta['status']) && !empty($atleta['status']) ? htmlspecialchars($atleta['status']) : 'Ativo'; ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="card-footer text-center">
                                            <div class="btn-group" role="group">
                                                <a href="editar_atleta.php?id=<?php echo $atleta['id_atleta']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i> Editar
                                                </a>
                                                <a href="#" class="btn btn-danger btn-sm" onclick="confirmarExclusao(<?php echo $atleta['id_atleta']; ?>, '<?php echo htmlspecialchars($atleta['nome']); ?>')">
                                                    <i class="fa fa-trash"></i> Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="col-lg-12">
                                    <div class="alert alert-info">
                                        <h5>Nenhum atleta encontrado</h5>
                                        <p>Não foram encontrados atletas com os critérios selecionados.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
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

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="modalConfirmacao" tabindex="-1" role="dialog" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmacaoLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir o atleta <strong id="nomeAtleta"></strong>?
                    <p class="text-danger mt-2">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <a href="#" id="btnConfirmarExclusao" class="btn btn-danger">Confirmar Exclusão</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
    
    <script>
        // Definir sub-escalões para cada escalão
        var subEscaloes = {
            "Pré-Petizes": ["Sub-5"],
            "Petizes": ["Sub-6", "Sub-7"],
            "Traquinas": ["Sub-8", "Sub-9"],
            "Benjamins": ["Sub-10", "Sub-11"],
            "Infantis": ["Sub-12", "Sub-13"],
            "Iniciados": ["Sub-14", "Sub-15"],
            "Juvenis": ["Sub-16", "Sub-17"],
            "Juniores": ["Sub-18", "Sub-19"],
            "Sub-23": ["B"],
            "Seniores": [""],
            "Veteranos": [""]
        };
        
        // Carregar sub-escalões com base no escalão selecionado
        function carregarSubEscaloes() {
            var escalao = document.getElementById('escalao').value;
            var subEscalaoSelect = document.getElementById('sub_escalao');
            
            // Limpar opções atuais
            subEscalaoSelect.innerHTML = '<option value="Todos">Todos</option>';
            
            // Se nenhum escalão selecionado ou "Todos", retornar
            if (!escalao || escalao === 'Todos') return;
            
            // Adicionar opções de sub-escalão
            if (subEscaloes[escalao]) {
                subEscaloes[escalao].forEach(function(subEscalao) {
                    if (subEscalao) {
                        var option = document.createElement('option');
                        option.value = subEscalao;
                        option.textContent = subEscalao;
                        subEscalaoSelect.appendChild(option);
                    }
                });
            }
        }
        
        // Atualizar filtros quando o escalão mudar
        document.getElementById('escalao').addEventListener('change', carregarSubEscaloes);
        
        // Aplicar filtros
        document.getElementById('aplicarFiltros').addEventListener('click', function() {
            atualizarFiltros();
        });
        
        // Limpar filtros
        document.getElementById('limparFiltros').addEventListener('click', function() {
            window.location.href = 'atletas.php';
        });
        
        // Função para atualizar filtros
        function atualizarFiltros() {
            var escalao = document.getElementById('escalao').value;
            var subEscalao = document.getElementById('sub_escalao').value;
            var posicao = document.getElementById('posicao').value;
            var status = document.getElementById('status').value;
            var pesquisa = document.querySelector('input[name="pesquisa"]').value;
            
            var url = 'atletas.php?';
            if (escalao !== 'Todos') url += 'escalao=' + encodeURIComponent(escalao) + '&';
            if (subEscalao !== 'Todos') url += 'sub_escalao=' + encodeURIComponent(subEscalao) + '&';
            if (posicao !== 'Todas') url += 'posicao=' + encodeURIComponent(posicao) + '&';
            if (status !== 'Todos') url += 'status=' + encodeURIComponent(status) + '&';
            if (pesquisa) url += 'pesquisa=' + encodeURIComponent(pesquisa) + '&';
            
            // Remover o último '&' se existir
            if (url.endsWith('&')) {
                url = url.slice(0, -1);
            }
            
            window.location.href = url;
        }
        
        // Função para confirmar exclusão
        function confirmarExclusao(id, nome) {
            document.getElementById('nomeAtleta').textContent = nome;
            document.getElementById('btnConfirmarExclusao').href = 'atletas.php?excluir=' + id;
            $('#modalConfirmacao').modal('show');
        }
        
        // Carregar sub-escalões ao iniciar a página
        document.addEventListener('DOMContentLoaded', function() {
            carregarSubEscaloes();
            
            // Selecionar o sub-escalão atual se existir
            var currentSubEscalao = '<?php echo $filtro_sub_escalao; ?>';
            if (currentSubEscalao) {
                var subEscalaoSelect = document.getElementById('sub_escalao');
                for (var i = 0; i < subEscalaoSelect.options.length; i++) {
                    if (subEscalaoSelect.options[i].value === currentSubEscalao) {
                        subEscalaoSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        });
    </script>
</body>

</html>
