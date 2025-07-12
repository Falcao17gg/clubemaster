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
$nome = "";
$cc = "";
$data_nascimento = "";
$posicao = "";
$pe_preferido = "";
$escalao = "";
$fotografia = "no_image.png";

// Verificar se foi passado um ID de atleta
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id_atleta = $_GET['id'];
    
    // Buscar dados do atleta
    $sql = "SELECT * FROM atletas WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $atleta = mysqli_fetch_assoc($result);
        $nome = $atleta['nome'];
        $cc = $atleta['cc'];
        $data_nascimento = $atleta['data_nascimento'];
        $posicao = $atleta['posicao'];
        $pe_preferido = $atleta['pe_preferido'];
        $escalao = $atleta['escalao'];
        $fotografia = $atleta['fotografia'];
    } else {
        // Atleta não encontrado ou não pertence ao clube
        echo "<script>
            alert('Atleta não encontrado ou não pertence ao seu clube.');
            window.location.href='atletas.php';
        </script>";
        exit();
    }
}

// Processar formulário de edição
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_editar'])) {
    $id_atleta = $_POST['id_atleta'];
    $nome = $_POST['nome'];
    $cc = $_POST['cc'];
    $data_nascimento = $_POST['data_nascimento'];
    $posicao = $_POST['posicao'];
    $pe_preferido = $_POST['pe_preferido'];
    $escalao = $_POST['escalao'];
    $fotografia_atual = $_POST['fotografia_atual'];
    
    // Verificar se foi enviada uma nova foto
    if(isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $nomefoto = time() . basename($_FILES["foto"]["name"]);
        $fotografia = $nomefoto;
        
        // Upload da nova foto
        $target_dir = "fotos_jogadores/";
        if(move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $nomefoto)) {
            // Foto carregada com sucesso
        } else {
            $msg = "Erro ao carregar a nova fotografia.";
            $fotografia = $fotografia_atual; // Manter a foto atual em caso de erro
        }
    } else {
        $fotografia = $fotografia_atual; // Manter a foto atual se não foi enviada uma nova
    }
    
    // Atualizar dados do atleta
    $sql_update = "UPDATE atletas SET 
                    nome = '$nome',
                    cc = '$cc',
                    data_nascimento = '$data_nascimento',
                    posicao = '$posicao',
                    pe_preferido = '$pe_preferido',
                    escalao = '$escalao',
                    fotografia = '$fotografia'
                  WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
    
    if(mysqli_query($conn, $sql_update)) {
        $msg = "Dados do atleta atualizados com sucesso!";
        
        // Redirecionar para a página de perfil após sucesso
        echo "<script>
            alert('Dados do atleta atualizados com sucesso!');
            window.location.href='perfil.php?id=$id_atleta';
        </script>";
        exit();
    } else {
        $msg = "Erro ao atualizar dados: " . mysqli_error($conn);
    }
}

// Buscar estatísticas do atleta (jogos, treinos, etc.)
$total_jogos = 0;
$total_treinos = 0;
$total_gols = 0;

if(!empty($id_atleta)) {
    // Contar presenças em jogos
    $sql_jogos = "SELECT COUNT(*) as total FROM presencas_jogo WHERE id_atleta = '$id_atleta' AND presente = 1";
    $result_jogos = mysqli_query($conn, $sql_jogos);
    if($result_jogos && mysqli_num_rows($result_jogos) > 0) {
        $row_jogos = mysqli_fetch_assoc($result_jogos);
        $total_jogos = $row_jogos['total'];
    }
    
    // Contar presenças em treinos
    $sql_treinos = "SELECT COUNT(*) as total FROM presencas_treino WHERE id_atleta = '$id_atleta' AND presente = 1";
    $result_treinos = mysqli_query($conn, $sql_treinos);
    if($result_treinos && mysqli_num_rows($result_treinos) > 0) {
        $row_treinos = mysqli_fetch_assoc($result_treinos);
        $total_treinos = $row_treinos['total'];
    }
    
    // Contar gols
    $sql_gols = "SELECT SUM(gols) as total FROM estatisticas_jogo WHERE id_atleta = '$id_atleta'";
    $result_gols = mysqli_query($conn, $sql_gols);
    if($result_gols && mysqli_num_rows($result_gols) > 0) {
        $row_gols = mysqli_fetch_assoc($result_gols);
        $total_gols = $row_gols['total'] ? $row_gols['total'] : 0;
    }
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
    <title>ClubeMaster - Perfil do Atleta</title>

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
        <div class="canvas-toggle">
            <i class="fa fa-bars"></i>
        </div>
        <ul class="main-menu mobile-menu">
            <li><a href="./home.php">Home</a></li>
            <li class="active"><a href="./atletas.php">Atletas</a>
             
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
                        <h2>Perfil do Atleta</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Player Section Begin -->
    <section class="player-section spad">
        <div class="container">
            <?php if(!empty($id_atleta)): ?>
            <!-- Visualização de Perfil -->
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="player-profile">
                    <div class="player-pic">
                        <img src="fotos_jogadores/<?php echo $fotografia; ?>" alt="<?php echo $nome; ?>" class="img-fluid">
                    </div>
                    <div class="player-info">
                        <h4><?php echo $nome; ?></h4>
                        <div class="player-table table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Data de Nascimento:</td>
                                        <td><?php echo date('d/m/Y', strtotime($data_nascimento)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Posição:</td>
                                        <td><?php echo $posicao; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Escalão:</td>
                                        <td><?php echo $escalao ; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Pé Preferido:</td>
                                        <td><?php echo $pe_preferido; ?></td>
                                    </tr>
                                    <tr>
                                        <td>CC:</td>
                                        <td><?php echo $cc; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 col-sm-12">
                <div class="player-info-text">
                    <h4>Estatísticas</h4>
                    <div class="row">
                        <div class="col-md-4 col-sm-4 text-center mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h1 class="display-4"><?php echo $total_jogos; ?></h1>
                                    <p class="card-text">Jogos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 text-center mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h1 class="display-4"><?php echo $total_treinos; ?></h1>
                                    <p class="card-text">Treinos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 text-center mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h1 class="display-4"><?php echo $total_gols; ?></h1>
                                    <p class="card-text">Golos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Ações</h5>
                                </div>
                                <div class="card-body">
                                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#editarModal">Editar Perfil</a>
                                    <a href="ficha_clinica.php?id=<?php echo $id_atleta; ?>" class="btn btn-info">Ficha Clínica</a>
                                    <a href="documentos.php?id=<?php echo $id_atleta; ?>" class="btn btn-secondary">Documentos</a>
                                    <a href="historico.php?id=<?php echo $id_atleta; ?>" class="btn btn-warning">Histórico</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
<?php
// Fetch all main escaloes for the current club, ordered by name
$escalao_options = [];
$sql_escalao_options = "SELECT nome FROM escaloes WHERE codigo_clube = '$codigo_clube' AND ativo = 1 ORDER BY FIELD(nome, 'Pré-Petizes', 'Petizes', 'Traquinas', 'Benjamins', 'Infantis', 'Iniciados', 'Juvenis', 'Juniores', 'Sub-23', 'Seniores', 'Veteranos')";
$result_escalao_options = mysqli_query($conn, $sql_escalao_options);
if ($result_escalao_options) {
    while ($row = mysqli_fetch_assoc($result_escalao_options)) {
        $escalao_options[] = $row['nome'];
    }
}
?>

<!-- Modal de Edição -->
<div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarModalLabel">Editar Perfil do Atleta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <input type="hidden" name="id_atleta" value="<?php echo $id_atleta; ?>">
                    <input type="hidden" name="fotografia_atual" value="<?php echo $fotografia; ?>">
                    
                    <div class="form-group row">
                        <div class="col-md-12 text-center mb-3">
                            <img src="fotos_jogadores/<?php echo $fotografia; ?>" alt="<?php echo $nome; ?>" style="max-height: 200px;">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="nome" class="col-sm-3 col-form-label">Nome</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $nome; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="cc" class="col-sm-3 col-form-label">CC</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="cc" name="cc" value="<?php echo $cc; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="data_nascimento" class="col-sm-3 col-form-label">Data de Nascimento</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?php echo $data_nascimento; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="posicao" class="col-sm-3 col-form-label">Posição</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="posicao" name="posicao" required>
                                <option value="">Selecione...</option>
                                <option value="Guarda-Redes" <?php if($posicao == "Guarda-Redes") echo "selected"; ?>>Guarda-Redes</option>                                      
                                <option value="Defesa-Central" <?php if($posicao == "Defesa-Central") echo "selected"; ?>>Defesa-Central</option>
                                <option value="Defesa-Direito" <?php if($posicao == "Defesa-Direito") echo "selected"; ?>>Defesa-Direito</option>
                                <option value="Defesa-Esquerdo" <?php if($posicao == "Defesa-Esquerdo") echo "selected"; ?>>Defesa-Esquerdo</option>
                                <option value="Médio-Defensivo" <?php if($posicao == "Médio-Defensivo") echo "selected"; ?>>Médio-Defensivo</option>
                                <option value="Médio-Centro" <?php if($posicao == "Médio-Centro") echo "selected"; ?>>Médio-Centro</option>
                                <option value="Médio-Ofensivo" <?php if($posicao == "Médio-Ofensivo") echo "selected"; ?>>Médio-Ofensivo</option>
                                <option value="Extremo-Direito" <?php if($posicao == "Extremo-Direito") echo "selected"; ?>>Extremo-Direito</option>
                                <option value="Extremo-Esquerdo" <?php if($posicao == "Extremo-Esquerdo") echo "selected"; ?>>Extremo-Esquerdo</option>
                                <option value="Ponta-de-Lança" <?php if($posicao == "Ponta-de-Lança") echo "selected"; ?>>Ponta-de-Lança</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="pe_preferido" class="col-sm-3 col-form-label">Pé Preferido</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="pe_preferido" name="pe_preferido" required>
                                <option value="">Selecione...</option>
                                <option value="Direito" <?php if($pe_preferido == "Direito") echo "selected"; ?>>Direito</option>
                                <option value="Esquerdo" <?php if($pe_preferido == "Esquerdo") echo "selected"; ?>>Esquerdo</option>
                                <option value="Ambos" <?php if($pe_preferido == "Ambos") echo "selected"; ?>>Ambos</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="escalao" class="col-sm-3 col-form-label">Escalão</label>
                        <div class="col-sm-9">
<select class="form-control" id="escalao" name="escalao" required>
    <option value="">Selecione...</option>
    <?php foreach ($escalao_options as $option): ?>
        <option value="<?php echo htmlspecialchars($option); ?>" <?php if($escalao == $option) echo "selected"; ?>>
            <?php echo htmlspecialchars($option); ?>
        </option>
    <?php endforeach; ?>
</select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="foto" class="col-sm-3 col-form-label">Nova Fotografia</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control-file" id="foto" name="foto">
                            <small class="form-text text-muted">Deixe em branco para manter a fotografia atual.</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" name="btn_editar" class="btn btn-primary">Salvar Alterações</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
<?php if (isset($_GET['edit']) && $_GET['edit'] == '1'): ?>
    $(document).ready(function() {
        console.log("Opening editarModal modal because edit=1");
        $('#editarModal').modal('show');
    });
<?php endif; ?>
</script>
            <?php else: ?>
            <!-- Mensagem para selecionar um atleta -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-info">
                        <h4>Nenhum atleta selecionado</h4>
                        <p>Por favor, selecione um atleta na <a href="atletas.php">lista de atletas</a> para visualizar seu perfil.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <!-- Player Section End -->

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
</body>

</html>
