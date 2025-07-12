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
$filtro_funcao = isset($_GET['funcao']) ? $_GET['funcao'] : '';

// Processar formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['enviar_mensagem'])) {
        // Validar e sanitizar dados
        $destinatario = mysqli_real_escape_string($conn, $_POST['destinatario']);
        $assunto = mysqli_real_escape_string($conn, $_POST['assunto']);
        $mensagem = mysqli_real_escape_string($conn, $_POST['mensagem']);
        
        // Inserir mensagem no banco de dados
        $sql_insert = "INSERT INTO mensagens_internas (codigo_clube, remetente, destinatario, assunto, mensagem) VALUES ('$codigo_clube', '" . (isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Desconhecido') . "', '$destinatario', '$assunto', '$mensagem')";
        
        if (mysqli_query($conn, $sql_insert)) {
            $msg = "Mensagem interna enviada com sucesso!";
            $msg_type = "success";
        } else {
            $msg = "Erro ao enviar mensagem: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
    
    // Adicionar novo contacto
    if (isset($_POST['adicionar_contacto'])) {
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $funcao = mysqli_real_escape_string($conn, $_POST['funcao']);
        $telemovel = mysqli_real_escape_string($conn, $_POST['telemovel']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        
        $sql_insert = "INSERT INTO contactos_internos (codigo_clube, nome, funcao, telemovel, email) 
                      VALUES ('$codigo_clube', '$nome', '$funcao', '$telemovel', '$email')";
        
        if (mysqli_query($conn, $sql_insert)) {
            $msg = "Contacto adicionado com sucesso!";
            $msg_type = "success";
        } else {
            $msg = "Erro ao adicionar contacto: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}

// Verificar se a tabela existe, se não, criar
$sql_check_table = "SHOW TABLES LIKE 'contactos_internos'";
$result_check = mysqli_query($conn, $sql_check_table);

if (mysqli_num_rows($result_check) == 0) {
    // Criar tabela de contactos internos
    $sql_create_table = "CREATE TABLE contactos_internos (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        codigo_clube VARCHAR(50) NOT NULL,
        nome VARCHAR(100) NOT NULL,
        funcao VARCHAR(50) NOT NULL,
        telemovel VARCHAR(20) NOT NULL,
        email VARCHAR(100) NOT NULL,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    mysqli_query($conn, $sql_create_table);
    
    // Inserir alguns contactos de exemplo
    $sql_insert_examples = "INSERT INTO contactos_internos (codigo_clube, nome, funcao, telemovel, email) VALUES
        ('$codigo_clube', 'António Silva', 'Presidente', '912345678', 'presidente@clube.pt'),
        ('$codigo_clube', 'Manuel Santos', 'Diretor Desportivo', '923456789', 'diretor@clube.pt'),
        ('$codigo_clube', 'João Ferreira', 'Treinador Principal', '934567890', 'treinador@clube.pt'),
        ('$codigo_clube', 'Ana Pereira', 'Secretaria', '945678901', 'secretaria@clube.pt'),
        ('$codigo_clube', 'Carlos Oliveira', 'Médico', '956789012', 'medico@clube.pt')";
    
    mysqli_query($conn, $sql_insert_examples);
}

// Verificar se a tabela de mensagens existe, se não, criar
$sql_check_msg_table = "SHOW TABLES LIKE 'mensagens_internas'";
$result_check_msg = mysqli_query($conn, $sql_check_msg_table);

if (mysqli_num_rows($result_check_msg) == 0) {
    // Criar tabela de mensagens internas
    $sql_create_msg_table = "CREATE TABLE mensagens_internas (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        codigo_clube VARCHAR(50) NOT NULL,
        remetente VARCHAR(100) NOT NULL,
        destinatario VARCHAR(100) NOT NULL,
        assunto VARCHAR(200) NOT NULL,
        mensagem TEXT NOT NULL,
        lida TINYINT(1) DEFAULT 0,
        data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    mysqli_query($conn, $sql_create_msg_table);
}

// Obter contactos internos
$sql_contactos = "SELECT * FROM contactos_internos WHERE codigo_clube = '$codigo_clube'";
if (!empty($filtro_funcao)) {
    $sql_contactos .= " AND funcao = '$filtro_funcao'";
}
$sql_contactos .= " ORDER BY funcao, nome";
$result_contactos = mysqli_query($conn, $sql_contactos);

// Obter funções únicas para o filtro
$sql_funcoes = "SELECT DISTINCT funcao FROM contactos_internos WHERE codigo_clube = '$codigo_clube' ORDER BY funcao";
$result_funcoes = mysqli_query($conn, $sql_funcoes);

// Obter mensagens recebidas
$sql_mensagens = "SELECT * FROM mensagens_internas WHERE codigo_clube = '$codigo_clube' ORDER BY data_envio DESC LIMIT 10";
$result_mensagens = mysqli_query($conn, $sql_mensagens);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Contactos Internos</title>

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
            <li class="active"><a href="./contacto.php">Contacto</a></li>
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
                                <li class="active"><a href="./contacto.php">Contacto</a></li>
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
                        <h2>Contactos Internos</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Contact Section Begin -->
    <section class="contact-section spad">
        <div class="container">
            <?php if(!empty($msg)): ?>
            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $msg; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="section-title">
                        <h3>Contactos da Equipa Técnica e Administrativa</h3>
                        <p>Lista de contactos internos do clube para facilitar a comunicação entre os membros da equipa.</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Lista de Contactos</h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#novoContactoModal">
                                    <i class="fa fa-plus"></i> Novo Contacto
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <form action="contacto.php" method="get" class="form-inline">
                                    <label for="funcao" class="mr-2">Filtrar por função:</label>
                                    <select class="form-control mr-2" id="funcao" name="funcao" onchange="this.form.submit()">
                                        <option value="">Todas as funções</option>
                                        <?php while($row_funcao = mysqli_fetch_assoc($result_funcoes)): ?>
                                        <option value="<?php echo $row_funcao['funcao']; ?>" <?php echo ($filtro_funcao == $row_funcao['funcao']) ? 'selected' : ''; ?>>
                                            <?php echo $row_funcao['funcao']; ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </form>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Função</th>
                                            <th>Telemóvel</th>
                                            <th>Email</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(mysqli_num_rows($result_contactos) > 0): ?>
                                            <?php while($row = mysqli_fetch_assoc($result_contactos)): ?>
                                            <tr>
                                                <td><?php echo $row['nome']; ?></td>
                                                <td><span class="badge badge-info"><?php echo $row['funcao']; ?></span></td>
                                                <td><?php echo $row['telemovel']; ?></td>
                                                <td><?php echo $row['email']; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="prepararMensagem('<?php echo $row['nome']; ?>')">
                                                        <i class="fa fa-envelope"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Nenhum contacto encontrado.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Mensagens Recentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php if(mysqli_num_rows($result_mensagens) > 0): ?>
                                    <?php while($row_msg = mysqli_fetch_assoc($result_mensagens)): ?>
                                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo $row_msg['assunto']; ?></h6>
                                            <small><?php echo date('d/m/Y H:i', strtotime($row_msg['data_envio'])); ?></small>
                                        </div>
                                        <p class="mb-1">De: <?php echo $row_msg['remetente']; ?> | Para: <?php echo $row_msg['destinatario']; ?></p>
                                        <small class="text-muted"><?php echo substr($row_msg['mensagem'], 0, 50) . (strlen($row_msg['mensagem']) > 50 ? '...' : ''); ?></small>
                                    </a>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center p-3">
                                        <p>Nenhuma mensagem recente.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#novaMensagemModal">
                                <i class="fa fa-paper-plane"></i> Nova Mensagem
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

    <!-- Modal Novo Contacto -->
    <div class="modal fade" id="novoContactoModal" tabindex="-1" role="dialog" aria-labelledby="novoContactoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoContactoModalLabel">Adicionar Novo Contacto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="contacto.php" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="funcao">Função</label>
                            <select class="form-control" id="funcao" name="funcao" required>
                                <option value="">Selecione...</option>
                                <option value="Presidente">Presidente</option>
                                <option value="Diretor Desportivo">Diretor Desportivo</option>
                                <option value="Treinador Principal">Treinador Principal</option>
                                <option value="Treinador Adjunto">Treinador Adjunto</option>
                                <option value="Preparador Físico">Preparador Físico</option>
                                <option value="Médico">Médico</option>
                                <option value="Fisioterapeuta">Fisioterapeuta</option>
                                <option value="Secretaria">Secretaria</option>
                                <option value="Tesoureiro">Tesoureiro</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="telemovel">Telemóvel</label>
                            <input type="text" class="form-control" id="telemovel" name="telemovel" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="adicionar_contacto" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Nova Mensagem -->
    <div class="modal fade" id="novaMensagemModal" tabindex="-1" role="dialog" aria-labelledby="novaMensagemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novaMensagemModalLabel">Enviar Nova Mensagem</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="contacto.php" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="destinatario">Destinatário</label>
                            <select class="form-control" id="destinatario" name="destinatario" required>
                                <option value="">Selecione...</option>
                                <?php 
                                mysqli_data_seek($result_contactos, 0);
                                while($row = mysqli_fetch_assoc($result_contactos)): 
                                ?>
                                <option value="<?php echo $row['nome']; ?>"><?php echo $row['nome'] . ' (' . $row['funcao'] . ')'; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="assunto">Assunto</label>
                            <input type="text" class="form-control" id="assunto" name="assunto" required>
                        </div>
                        <div class="form-group">
                            <label for="mensagem">Mensagem</label>
                            <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="enviar_mensagem" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
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
        function prepararMensagem(destinatario) {
            $('#destinatario').val(destinatario);
            $('#novaMensagemModal').modal('show');
        }
    </script>
</body>

</html>
