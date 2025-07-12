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
$atleta = null;

// Obter ID do atleta da URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_atleta = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM atletas WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $atleta = mysqli_fetch_assoc($result);
    } else {
        $msg = "Atleta não encontrado ou não pertence a este clube.";
        $msg_type = "danger";
    }
} else {
    $msg = "ID do atleta não fornecido.";
    $msg_type = "danger";
}

// Processar formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_salvar'])) {
    if ($atleta) { // Só processa se o atleta foi encontrado
        // Sanitizar dados
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $data_nascimento = mysqli_real_escape_string($conn, $_POST['data_nascimento']);
        $cc = mysqli_real_escape_string($conn, $_POST['cc']);
        $nif = mysqli_real_escape_string($conn, $_POST['nif']);
        $morada = mysqli_real_escape_string($conn, $_POST['morada']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $escalao = mysqli_real_escape_string($conn, $_POST['escalao']);
        $sub_escalao = isset($_POST['sub_escalao']) ? mysqli_real_escape_string($conn, $_POST['sub_escalao']) : '';
        $posicao = mysqli_real_escape_string($conn, $_POST['posicao']);
        $pe_preferido = isset($_POST['pe_preferido']) ? mysqli_real_escape_string($conn, $_POST['pe_preferido']) : '';
        $altura = !empty($_POST['altura']) ? mysqli_real_escape_string($conn, $_POST['altura']) : 0;
        $peso = !empty($_POST['peso']) ? mysqli_real_escape_string($conn, $_POST['peso']) : 0;
        $ativo = isset($_POST['ativo']) ? 1 : 0; // Checkbox para ativo/inativo
        
        // Manter fotografia existente se não for enviada uma nova
        $fotografia = $atleta['fotografia'];
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $diretorio_destino = "fotos_jogadores/";
            if (!file_exists($diretorio_destino)) {
                mkdir($diretorio_destino, 0777, true);
            }
            $nome_arquivo = uniqid() . "_" . basename($_FILES['foto']['name']);
            $caminho_completo = $diretorio_destino . $nome_arquivo;
            $tipo_arquivo = strtolower(pathinfo($caminho_completo, PATHINFO_EXTENSION));
            $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
            
            if (in_array($tipo_arquivo, $tipos_permitidos)) {
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
                    $fotografia = $nome_arquivo;
                    // Remover foto antiga se não for a padrão
                    if ($atleta['fotografia'] != 'no_image.png' && file_exists($diretorio_destino . $atleta['fotografia'])) {
                        unlink($diretorio_destino . $atleta['fotografia']);
                    }
                } else {
                    $msg = "Erro ao fazer upload da nova foto.";
                    $msg_type = "danger";
                }
            } else {
                $msg = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos para a foto.";
                $msg_type = "danger";
            }
        }
        
        // Atualizar atleta no banco de dados
        $sql_update = "UPDATE atletas SET 
                        nome = '$nome', 
                        data_nascimento = '$data_nascimento', 
                        cc = '$cc', 
                        nif = '$nif', 
                        morada = '$morada', 
                        email = '$email', 
                        escalao = '$escalao', 
                        sub_escalao = '$sub_escalao', 
                        posicao = '$posicao', 
                        pe_preferido = '$pe_preferido', 
                        altura = '$altura', 
                        peso = '$peso', 
                        fotografia = '$fotografia', 
                        ativo = '$ativo' 
                        WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
        
        if (mysqli_query($conn, $sql_update)) {
            $_SESSION['msg'] = "Atleta atualizado com sucesso!";
            $_SESSION['msg_type'] = "success";
            echo "<script>window.location.href = 'atletas.php';</script>";
            exit();
        } else {
            $msg = "Erro ao atualizar atleta: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}

// Processar exclusão de atleta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_excluir'])) {
    if ($atleta) {
        $id_atleta_excluir = mysqli_real_escape_string($conn, $_POST['id_atleta_excluir']);
        
        // Remover fotografia do servidor se não for a padrão
        if ($atleta['fotografia'] != 'no_image.png' && file_exists('fotos_jogadores/' . $atleta['fotografia'])) {
            unlink('fotos_jogadores/' . $atleta['fotografia']);
        }

        // Excluir atleta do banco de dados
        $sql_delete = "DELETE FROM atletas WHERE id_atleta = '$id_atleta_excluir' AND codigo_clube = '$codigo_clube'";
        
        if (mysqli_query($conn, $sql_delete)) {
            $_SESSION['msg'] = "Atleta excluído com sucesso!";
            $_SESSION['msg_type'] = "success";
            echo "<script>window.location.href = 'atletas.php';</script>";
            exit();
        } else {
            $msg = "Erro ao excluir atleta: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}

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
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Editar Atleta</title>

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
                        <h2>Editar Atleta</h2>
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
                        
                        <?php if ($atleta): ?>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4>Editar Atleta: <?php echo htmlspecialchars($atleta['nome']); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="atletas.php" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Voltar para Atletas
                                </a>
                            </div>
                        </div>
                        
                        <form action="editar_atleta.php?id=<?php echo $atleta['id_atleta']; ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_atleta" value="<?php echo $atleta['id_atleta']; ?>">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Dados Pessoais</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nome">Nome Completo *</label>
                                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($atleta['nome']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_nascimento">Data de Nascimento *</label>
                                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($atleta['data_nascimento']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cc">Cartão de Cidadão</label>
                                                <input type="text" class="form-control" id="cc" name="cc" value="<?php echo htmlspecialchars($atleta['cc']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nif">NIF</label>
                                                <input type="text" class="form-control" id="nif" name="nif" value="<?php echo htmlspecialchars(isset($atleta['nif']) ? $atleta['nif'] : ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($atleta['email']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="morada">Morada</label>
                                                <input type="text" class="form-control" id="morada" name="morada" value="<?php echo htmlspecialchars($atleta['morada']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="escalao">Escalão *</label>
                                                <select class="form-control" id="escalao" name="escalao" required>
                                                    <option value="">Selecione um escalão</option>
                                                    <?php foreach ($todos_escaloes as $escalao_nome => $sub_escaloes_arr): ?>
                                                        <option value="<?php echo htmlspecialchars($escalao_nome); ?>" <?php echo ($atleta['escalao'] == $escalao_nome) ? 'selected' : ''; ?>><?php echo htmlspecialchars($escalao_nome); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sub_escalao">Sub-Escalão</label>
                                                <select class="form-control" id="sub_escalao" name="sub_escalao">
                                                    <option value="">Selecione um sub-escalão</option>
                                                    <?php 
                                                        $selected_escalao_subs = isset($todos_escaloes[$atleta['escalao']]) ? $todos_escaloes[$atleta['escalao']] : [];
                                                        foreach ($selected_escalao_subs as $sub_escalao_nome): 
                                                    ?>
                                                        <option value="<?php echo htmlspecialchars($sub_escalao_nome); ?>" <?php echo ($atleta['sub_escalao'] == $sub_escalao_nome) ? 'selected' : ''; ?>><?php echo htmlspecialchars($sub_escalao_nome); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="posicao">Posição *</label>
                                                <select class="form-control" id="posicao" name="posicao" required autocomplete="off" spellcheck="false">
                                                    <option value="">Selecione...</option>
                                                    <option value="Guarda-Redes" <?php echo ($atleta['posicao'] == 'Guarda-Redes') ? 'selected' : ''; ?>>Guarda-Redes</option>
                                                    <option value="Defesa-Central" <?php echo ($atleta['posicao'] == 'Defesa-Central') ? 'selected' : ''; ?>>Defesa-Central</option>
                                                    <option value="Defesa-Direito" <?php echo ($atleta['posicao'] == 'Defesa-Direito') ? 'selected' : ''; ?>>Defesa-Direito</option>
                                                    <option value="Defesa-Esquerdo" <?php echo ($atleta['posicao'] == 'Defesa-Esquerdo') ? 'selected' : ''; ?>>Defesa-Esquerdo</option>
                                                    <option value="Médio-Defensivo" <?php echo ($atleta['posicao'] == 'Médio-Defensivo') ? 'selected' : ''; ?>>Médio-Defensivo</option>
                                                    <option value="Médio-Centro" <?php echo ($atleta['posicao'] == 'Médio-Centro') ? 'selected' : ''; ?>>Médio-Centro</option>
                                                    <option value="Médio-Ofensivo" <?php echo ($atleta['posicao'] == 'Médio-Ofensivo') ? 'selected' : ''; ?>>Médio-Ofensivo</option>
                                                    <option value="Extremo-Direito" <?php echo ($atleta['posicao'] == 'Extremo-Direito') ? 'selected' : ''; ?>>Extremo-Direito</option>
                                                    <option value="Extremo-Esquerdo" <?php echo ($atleta['posicao'] == 'Extremo-Esquerdo') ? 'selected' : ''; ?>>Extremo-Esquerdo</option>
                                                    <option value="Ponta-de-Lança" <?php echo ($atleta['posicao'] == 'Ponta-de-Lança') ? 'selected' : ''; ?>>Ponta-de-Lança</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pe_preferido">Pé Preferido</label>
                                                <select class="form-control" id="pe_preferido" name="pe_preferido">
                                                    <option value="">Selecione</option>
                                                    <option value="Direito" <?php echo ($atleta['pe_preferido'] == 'Direito') ? 'selected' : ''; ?>>Direito</option>
                                                    <option value="Esquerdo" <?php echo ($atleta['pe_preferido'] == 'Esquerdo') ? 'selected' : ''; ?>>Esquerdo</option>
                                                    <option value="Ambos" <?php echo ($atleta['pe_preferido'] == 'Ambos') ? 'selected' : ''; ?>>Ambos</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="altura">Altura (m)</label>
                                                <input type="number" step="0.01" class="form-control" id="altura" name="altura" value="<?php echo htmlspecialchars($atleta['altura']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="peso">Peso (kg)</label>
                                                <input type="number" step="0.01" class="form-control" id="peso" name="peso" value="<?php echo htmlspecialchars($atleta['peso']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="foto">Fotografia</label>
                                        <?php if (!empty($atleta['fotografia']) && $atleta['fotografia'] != 'no_image.png'): ?>
                                            <div class="mb-2">
                                                <img src="fotos_jogadores/<?php echo htmlspecialchars($atleta['fotografia']); ?>" alt="Fotografia do Atleta" style="max-width: 150px; height: auto;">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*">
                                        <small class="form-text text-muted">Deixe em branco para manter a foto atual.</small>
                                    </div>
                                    
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="ativo" name="ativo" <?php echo ($atleta['ativo'] == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ativo">Atleta Ativo</label>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="btn_salvar" class="btn btn-primary mt-3">Salvar Alterações</button>
                            <button type="button" class="btn btn-danger mt-3" data-toggle="modal" data-target="#confirmDeleteModal">Excluir Atleta</button>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <?php echo $msg; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir este atleta? Esta ação não pode ser desfeita.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form action="editar_atleta.php?id=<?php echo $atleta['id_atleta']; ?>" method="post" style="display: inline;">
                        <input type="hidden" name="id_atleta_excluir" value="<?php echo $atleta['id_atleta']; ?>">
                        <button type="submit" name="btn_excluir" class="btn btn-danger">Excluir</button>
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

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Lógica para atualizar sub-escalões com base no escalão selecionado
        document.getElementById('escalao').addEventListener('change', function() {
            var escalaoSelecionado = this.value;
            var subEscalaoSelect = document.getElementById('sub_escalao');
            subEscalaoSelect.innerHTML = '<option value="">Selecione um sub-escalão</option>'; // Limpa opções

            var todosEscaloes = <?php echo json_encode($todos_escaloes); ?>;
            if (todosEscaloes[escalaoSelecionado]) {
                todosEscaloes[escalaoSelecionado].forEach(function(sub) {
                    var option = document.createElement('option');
                    option.value = sub;
                    option.textContent = sub;
                    subEscalaoSelect.appendChild(option);
                });
            }
        });
    </script>
</body>

</html>


