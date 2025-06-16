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

// Processar formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se é uma ação de salvar atleta
    if (isset($_POST['nome'])) {
        // Sanitizar dados
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $data_nascimento = mysqli_real_escape_string($conn, $_POST['data_nascimento']);
        $cc = mysqli_real_escape_string($conn, $_POST['cc']);
        $nif = mysqli_real_escape_string($conn, $_POST['nif']);
        $morada = mysqli_real_escape_string($conn, $_POST['morada']);
        $codigo_postal = mysqli_real_escape_string($conn, $_POST['codigo_postal']);
        $localidade = mysqli_real_escape_string($conn, $_POST['localidade']);
        $telemovel = mysqli_real_escape_string($conn, $_POST['telemovel']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $escalao = mysqli_real_escape_string($conn, $_POST['escalao']);
        $sub_escalao = isset($_POST['sub_escalao']) ? mysqli_real_escape_string($conn, $_POST['sub_escalao']) : '';
        $posicao = mysqli_real_escape_string($conn, $_POST['posicao']);
        $pe_preferido = isset($_POST['pe_preferido']) ? mysqli_real_escape_string($conn, $_POST['pe_preferido']) : '';
        $altura = !empty($_POST['altura']) ? mysqli_real_escape_string($conn, $_POST['altura']) : 0;
        $peso = !empty($_POST['peso']) ? mysqli_real_escape_string($conn, $_POST['peso']) : 0;
        $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : 'Ativo';
        
        // Processar upload de foto
        $fotografia = 'no_image.png'; // Valor padrão
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $diretorio_destino = "fotos_jogadores/";
            
            // Criar diretório se não existir
            if (!file_exists($diretorio_destino)) {
                mkdir($diretorio_destino, 0777, true);
            }
            
            $nome_arquivo = uniqid() . "_" . basename($_FILES['foto']['name']);
            $caminho_completo = $diretorio_destino . $nome_arquivo;
            
            // Verificar tipo de arquivo
            $tipo_arquivo = strtolower(pathinfo($caminho_completo, PATHINFO_EXTENSION));
            $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
            
            if (in_array($tipo_arquivo, $tipos_permitidos)) {
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
                    $fotografia = $nome_arquivo;
                } else {
                    $msg = "Erro ao fazer upload da foto.";
                    $msg_type = "danger";
                }
            } else {
                $msg = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
                $msg_type = "danger";
            }
        }
        
        // Inserir atleta no banco de dados
        $sql = "INSERT INTO atletas (codigo_clube, nome, data_nascimento, cc, nif, morada, codigo_postal, localidade, telemovel, email, escalao, sub_escalao, posicao, pe_preferido, altura, peso, fotografia, status) 
                VALUES ('$codigo_clube', '$nome', '$data_nascimento', '$cc', '$nif', '$morada', '$codigo_postal', '$localidade', '$telemovel', '$email', '$escalao', '$sub_escalao', '$posicao', '$pe_preferido', '$altura', '$peso', '$fotografia', '$status')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg'] = "Atleta adicionado com sucesso!";
            $_SESSION['msg_type'] = "success";
            
            // Redirecionar para a página de atletas após sucesso
            echo "<script>window.location.href = 'atletas.php';</script>";
            exit();
        } else {
            $msg = "Erro ao adicionar atleta: " . mysqli_error($conn);
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
    <title>ClubeMaster - Novo Atleta</title>

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
                        <h2>Novo Atleta</h2>
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
                                <h4>Adicionar Novo Atleta</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="atletas.php" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Voltar para Atletas
                                </a>
                            </div>
                        </div>
                        
                        <form action="novoatleta.php" method="post" enctype="multipart/form-data">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Dados Pessoais</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nome">Nome Completo *</label>
                                                <input type="text" class="form-control" id="nome" name="nome" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_nascimento">Data de Nascimento *</label>
                                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cc">Cartão de Cidadão</label>
                                                <input type="text" class="form-control" id="cc" name="cc">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nif">NIF</label>
                                                <input type="text" class="form-control" id="nif" name="nif">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="morada">Morada</label>
                                                <input type="text" class="form-control" id="morada" name="morada">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="codigo_postal">Código Postal</label>
                                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="localidade">Localidade</label>
                                                <input type="text" class="form-control" id="localidade" name="localidade">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telemovel">Telemóvel</label>
                                                <input type="text" class="form-control" id="telemovel" name="telemovel">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5>Dados Desportivos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="escalao">Escalão *</label>
                                                <select class="form-control" id="escalao" name="escalao" required onchange="carregarSubEscaloes()">
                                                    <option value="">Selecione...</option>
                                                    <?php foreach($todos_escaloes as $escalao => $sub_escaloes): ?>
                                                    <option value="<?php echo $escalao; ?>"><?php echo $escalao; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sub_escalao">Sub-Escalão</label>
                                                <select class="form-control" id="sub_escalao" name="sub_escalao">
                                                    <option value="">Selecione primeiro o escalão</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="posicao">Posição *</label>
                                                <select class="form-control" id="posicao" name="posicao" required>
                                                    <option value="">Selecione...</option>
                                                    <option value="Guarda-Redes">Guarda-Redes</option>
                                                    <option value="Defesa">Defesa</option>
                                                    <option value="Médio">Médio</option>
                                                    <option value="Avançado">Avançado</option>
                                                    <option value="Defesa-Direito">Defesa-Direito</option>
                                                    <option value="Defesa-Esquerdo">Defesa-Esquerdo</option>
                                                    <option value="Defesa-Central">Defesa-Central</option>
                                                    <option value="Médio-Defensivo">Médio-Defensivo</option>
                                                    <option value="Médio-Centro">Médio-Centro</option>
                                                    <option value="Médio-Ofensivo">Médio-Ofensivo</option>
                                                    <option value="Extremo-Direito">Extremo-Direito</option>
                                                    <option value="Extremo-Esquerdo">Extremo-Esquerdo</option>
                                                    <option value="Ponta-de-Lança">Ponta-de-Lança</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pe_preferido">Pé Preferido</label>
                                                <select class="form-control" id="pe_preferido" name="pe_preferido">
                                                    <option value="">Selecione...</option>
                                                    <option value="Direito">Direito</option>
                                                    <option value="Esquerdo">Esquerdo</option>
                                                    <option value="Ambos">Ambos</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="altura">Altura (cm)</label>
                                                <input type="number" class="form-control" id="altura" name="altura" min="0" max="250">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="peso">Peso (kg)</label>
                                                <input type="number" class="form-control" id="peso" name="peso" min="0" max="150" step="0.1">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="foto">Foto</label>
                                                <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*">
                                                <small class="form-text text-muted">Formatos aceitos: JPG, JPEG, PNG, GIF. Tamanho máximo: 2MB.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="Ativo">Ativo</option>
                                                    <option value="Inativo">Inativo</option>
                                                    <option value="Lesionado">Lesionado</option>
                                                    <option value="Suspenso">Suspenso</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Salvar Atleta
                                </button>
                                <a href="atletas.php" class="btn btn-secondary ml-2">
                                    <i class="fa fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
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
        // Objeto com todos os escalões e sub-escalões
        const todosEscaloes = <?php echo json_encode($todos_escaloes); ?>;
        
        // Função para carregar sub-escalões com base no escalão selecionado
        function carregarSubEscaloes() {
            const escalaoSelect = document.getElementById('escalao');
            const subEscalaoSelect = document.getElementById('sub_escalao');
            const escalaoSelecionado = escalaoSelect.value;
            
            // Limpar opções atuais
            subEscalaoSelect.innerHTML = '';
            
            // Adicionar opção vazia
            const optionVazia = document.createElement('option');
            optionVazia.value = '';
            optionVazia.textContent = 'Selecione...';
            subEscalaoSelect.appendChild(optionVazia);
            
            // Se não houver escalão selecionado ou não houver sub-escalões, retornar
            if (!escalaoSelecionado || !todosEscaloes[escalaoSelecionado] || todosEscaloes[escalaoSelecionado].length === 0) {
                return;
            }
            
            // Adicionar sub-escalões
            todosEscaloes[escalaoSelecionado].forEach(subEscalao => {
                if (subEscalao) {  // Verificar se não é vazio
                    const option = document.createElement('option');
                    option.value = subEscalao;
                    option.textContent = subEscalao;
                    subEscalaoSelect.appendChild(option);
                }
            });
        }
        
        // Inicializar ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            carregarSubEscaloes();
        });
    </script>
</body>

</html>
