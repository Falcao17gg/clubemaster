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
$msg_type = ""; // Para controlar a cor da mensagem (success/danger)
$id_atleta = "";

// Verificar se foi passado um ID de atleta
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id_atleta = $_GET['id'];
    
    // Verificar se o atleta pertence ao clube
    $sql_check = "SELECT * FROM atletas WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if(mysqli_num_rows($result_check) == 0) {
        echo "<script>
            alert('Atleta não encontrado ou não pertence ao seu clube.');
            window.location.href='atletas.php';
        </script>";
        exit();
    }
    
    $atleta = mysqli_fetch_assoc($result_check);
} else {
    echo "<script>
        alert('Nenhum atleta selecionado.');
        window.location.href='atletas.php';
    </script>";
    exit();
}

// Processar formulário de upload de documentos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_upload'])) {
        $nome_documento = mysqli_real_escape_string($conn, $_POST['nome_documento']);
        $tipo_documento = mysqli_real_escape_string($conn, $_POST['tipo_documento']);
        $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes']);
        
        // Verificar se foi enviado um arquivo
        if(isset($_FILES["arquivo"]) && $_FILES["arquivo"]["error"] == 0) {
            $nome_arquivo = time() . "_" . basename($_FILES["arquivo"]["name"]);
            $caminho_arquivo = "documentos/" . $nome_arquivo;
            
            // Criar diretório se não existir
            if (!file_exists("documentos")) {
                mkdir("documentos", 0777, true);
            }
            
            // Validar o tamanho do arquivo
            if ($_FILES["arquivo"]["size"] > 5000000) { // 5MB
                $msg = "Lamentamos, o arquivo é demasiado grande.";
                $msg_type = "danger";
            } else {
                // Inserir documento na base de dados
                $sql = "INSERT INTO documentos (id_atleta, codigo_clube, nome_documento, tipo_documento, caminho_arquivo, data_upload, observacoes) 
                        VALUES ('$id_atleta', '$codigo_clube', '$nome_documento', '$tipo_documento', '$caminho_arquivo', NOW(), '$observacoes')";
                
                if(mysqli_query($conn, $sql)) {
                    // Upload do arquivo
                    if (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $caminho_arquivo)) {
                        $msg = "Documento enviado com sucesso!";
                        $msg_type = "success";
                    } else {
                        $msg = "Erro ao fazer upload do arquivo.";
                        $msg_type = "danger";
                        
                        // Remover entrada do banco de dados se o upload falhar
                        $id_documento = mysqli_insert_id($conn);
                        mysqli_query($conn, "DELETE FROM documentos WHERE id_documento = '$id_documento'");
                    }
                } else {
                    $msg = "Erro ao registrar documento: " . mysqli_error($conn);
                    $msg_type = "danger";
                }
            }
        } else {
            $msg = "Por favor, selecione um arquivo para upload.";
            $msg_type = "danger";
        }
    }
    
    // Processar exclusão de documento
    if(isset($_POST['btn_excluir'])) {
        $id_documento = $_POST['id_documento'];
        
        // Buscar informações do documento
        $sql_doc = "SELECT * FROM documentos WHERE id_documento = '$id_documento' AND codigo_clube = '$codigo_clube'";
        $result_doc = mysqli_query($conn, $sql_doc);
        
        if(mysqli_num_rows($result_doc) > 0) {
            $documento = mysqli_fetch_assoc($result_doc);
            $caminho_arquivo = $documento['caminho_arquivo'];
            
            // Excluir documento da base de dados
            $sql = "DELETE FROM documentos WHERE id_documento = '$id_documento' AND codigo_clube = '$codigo_clube'";
            
            if(mysqli_query($conn, $sql)) {
                // Excluir arquivo físico
                if(file_exists($caminho_arquivo)) {
                    unlink($caminho_arquivo);
                }
                
                $msg = "Documento excluído com sucesso!";
                $msg_type = "success";
            } else {
                $msg = "Erro ao excluir documento: " . mysqli_error($conn);
                $msg_type = "danger";
            }
        } else {
            $msg = "Documento não encontrado ou não pertence ao seu clube.";
            $msg_type = "danger";
        }
    }
}

// Buscar documentos do atleta
$sql_documentos = "SELECT * FROM documentos WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube' ORDER BY data_upload DESC";
$result_documentos = mysqli_query($conn, $sql_documentos);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Upload de Documentos</title>

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
    
    <style>
        .card-header {
            background-color: #e60000;
            color: white;
        }
        
        .btn-primary {
            background-color: #e60000;
            border-color: #cc0000;
        }
        
        .btn-primary:hover {
            background-color: #cc0000;
            border-color: #b30000;
        }
    </style>
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
                        <h2>Upload de Documentos - <?php echo $atleta['nome']; ?></h2>
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
                   <div class="d-flex justify-content-end mb-3">
                        <a href="perfil.php?id=<?php echo $id_atleta; ?>" class="btn btn-secondary">
                           <i class="fa fa-arrow-left"></i> Voltar ao Perfil
                        </a>
                    </div>
                    <div class="contact-form">
                        <?php if(!empty($msg)): ?>
                        <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo $msg; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Upload de Novo Documento</h4>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_atleta);?>" enctype="multipart/form-data">
                                            <div class="form-group row">
                                                <label for="nome_documento" class="col-sm-3 col-form-label">Nome do Documento</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="nome_documento" name="nome_documento" required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="tipo_documento" class="col-sm-3 col-form-label">Tipo de Documento</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                                                        <option value="">Selecione...</option>
                                                        <option value="Identificação">Identificação</option>
                                                        <option value="Médico">Médico</option>
                                                        <option value="Seguro">Seguro</option>
                                                        <option value="Escolar">Escolar</option>
                                                        <option value="Autorização">Autorização</option>
                                                        <option value="Outro">Outro</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="arquivo" class="col-sm-3 col-form-label">Arquivo</label>
                                                <div class="col-sm-9">
                                                    <input type="file" class="form-control-file" id="arquivo" name="arquivo" required>
                                                    <small class="form-text text-muted">Formatos aceitos: PDF, DOC, DOCX, JPG, PNG, GIF. Tamanho máximo: 5MB.</small>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="observacoes" class="col-sm-3 col-form-label">Observações</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-sm-12 text-center">
                                                    <button type="submit" name="btn_upload" class="btn btn-primary">Enviar Documento</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h4>Documentos do Atleta</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nome</th>
                                                        <th>Tipo</th>
                                                        <th>Data de Upload</th>
                                                        <th>Observações</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(mysqli_num_rows($result_documentos) > 0): ?>
                                                        <?php while($documento = mysqli_fetch_assoc($result_documentos)): ?>
                                                        <tr>
                                                            <td><?php echo $documento['nome_documento']; ?></td>
                                                            <td><?php echo $documento['tipo_documento']; ?></td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($documento['data_upload'])); ?></td>
                                                            <td><?php echo $documento['observacoes']; ?></td>
                                                            <td>
                                                                <a href="<?php echo $documento['caminho_arquivo']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="fa fa-eye"></i> Ver
                                                                </a>
                                                                <a href="<?php echo $documento['caminho_arquivo']; ?>" download class="btn btn-sm btn-success">
                                                                    <i class="fa fa-download"></i> Baixar
                                                                </a>
                                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_atleta);?>" style="display: inline;">
                                                                    <input type="hidden" name="id_documento" value="<?php echo $documento['id_documento']; ?>">
                                                                    <button type="submit" name="btn_excluir" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este documento?');">
                                                                        <i class="fa fa-trash"></i> Excluir
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">Nenhum documento registrado.</td>
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
