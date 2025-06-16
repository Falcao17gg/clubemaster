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

// Processar formulário de ficha clínica
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['btn_salvar'])) {
        $grupo_sanguineo = mysqli_real_escape_string($conn, $_POST['grupo_sanguineo']);
        $alergias = mysqli_real_escape_string($conn, $_POST['alergias']);
        $doencas_cronicas = mysqli_real_escape_string($conn, $_POST['doencas_cronicas']);
        $medicacao_regular = mysqli_real_escape_string($conn, $_POST['medicacao_regular']);
        $cirurgias_anteriores = mysqli_real_escape_string($conn, $_POST['cirurgias_anteriores']);
        $lesoes_anteriores = mysqli_real_escape_string($conn, $_POST['lesoes_anteriores']);
        $restricoes_alimentares = mysqli_real_escape_string($conn, $_POST['restricoes_alimentares']);
        $contato_emergencia_nome = mysqli_real_escape_string($conn, $_POST['contato_emergencia_nome']);
        $contato_emergencia_telefone = mysqli_real_escape_string($conn, $_POST['contato_emergencia_telefone']);
        $contato_emergencia_relacao = mysqli_real_escape_string($conn, $_POST['contato_emergencia_relacao']);
        $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes']);
        
        // Verificar se já existe ficha clínica para este atleta
        $sql_check_ficha = "SELECT * FROM ficha_clinica WHERE id_atleta = '$id_atleta'";
        $result_check_ficha = mysqli_query($conn, $sql_check_ficha);
        
        if(mysqli_num_rows($result_check_ficha) > 0) {
            // Atualizar ficha clínica existente
            $sql = "UPDATE ficha_clinica SET 
                    grupo_sanguineo = '$grupo_sanguineo',
                    alergias = '$alergias',
                    doencas_cronicas = '$doencas_cronicas',
                    medicacao_regular = '$medicacao_regular',
                    cirurgias_anteriores = '$cirurgias_anteriores',
                    lesoes_anteriores = '$lesoes_anteriores',
                    restricoes_alimentares = '$restricoes_alimentares',
                    contato_emergencia_nome = '$contato_emergencia_nome',
                    contato_emergencia_telefone = '$contato_emergencia_telefone',
                    contato_emergencia_relacao = '$contato_emergencia_relacao',
                    observacoes = '$observacoes',
                    data_atualizacao = NOW()
                    WHERE id_atleta = '$id_atleta'";
        } else {
            // Inserir nova ficha clínica
            $sql = "INSERT INTO ficha_clinica (id_atleta, codigo_clube, grupo_sanguineo, alergias, doencas_cronicas, medicacao_regular, cirurgias_anteriores, lesoes_anteriores, restricoes_alimentares, contato_emergencia_nome, contato_emergencia_telefone, contato_emergencia_relacao, observacoes, data_atualizacao) 
                    VALUES ('$id_atleta', '$codigo_clube', '$grupo_sanguineo', '$alergias', '$doencas_cronicas', '$medicacao_regular', '$cirurgias_anteriores', '$lesoes_anteriores', '$restricoes_alimentares', '$contato_emergencia_nome', '$contato_emergencia_telefone', '$contato_emergencia_relacao', '$observacoes', NOW())";
        }
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Ficha clínica salva com sucesso!";
            $msg_type = "success";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Ficha clínica salva com sucesso!');
                window.location.href='ficha_clinica.php?id=$id_atleta';
            </script>";
            exit();
        } else {
            $msg = "Erro ao salvar ficha clínica: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
    
    // Processar adição de consulta médica
    if(isset($_POST['btn_adicionar_consulta'])) {
        $data_consulta = mysqli_real_escape_string($conn, $_POST['data_consulta']);
        $tipo_consulta = mysqli_real_escape_string($conn, $_POST['tipo_consulta']);
        $medico = mysqli_real_escape_string($conn, $_POST['medico']);
        $diagnostico = mysqli_real_escape_string($conn, $_POST['diagnostico']);
        $tratamento = mysqli_real_escape_string($conn, $_POST['tratamento']);
        $recomendacoes = mysqli_real_escape_string($conn, $_POST['recomendacoes']);
        $data_proxima_consulta = mysqli_real_escape_string($conn, $_POST['data_proxima_consulta']);
        $apto_treinar = isset($_POST['apto_treinar']) ? 1 : 0;
        $apto_competir = isset($_POST['apto_competir']) ? 1 : 0;
        $observacoes_consulta = mysqli_real_escape_string($conn, $_POST['observacoes_consulta']);
        
        // Inserir consulta médica
        $sql = "INSERT INTO consultas_medicas (id_atleta, codigo_clube, data_consulta, medico, tipo_consulta, diagnostico, tratamento, recomendacoes, data_proxima_consulta, apto_treinar, apto_competir, observacoes) 
                VALUES ('$id_atleta', '$codigo_clube', '$data_consulta', '$medico', '$tipo_consulta', '$diagnostico', '$tratamento', '$recomendacoes', " . ($data_proxima_consulta ? "'$data_proxima_consulta'" : "NULL") . ", '$apto_treinar', '$apto_competir', '$observacoes_consulta')";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Consulta médica adicionada com sucesso!";
            $msg_type = "success";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Consulta médica adicionada com sucesso!');
                window.location.href='ficha_clinica.php?id=$id_atleta';
            </script>";
            exit();
        } else {
            $msg = "Erro ao adicionar consulta médica: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
    
    // Processar exclusão de consulta médica
    if(isset($_POST['btn_excluir_consulta'])) {
        $id_consulta = $_POST['id_consulta'];
        
        // Excluir consulta médica
        $sql = "DELETE FROM consultas_medicas WHERE id_consulta = '$id_consulta' AND codigo_clube = '$codigo_clube'";
        
        if(mysqli_query($conn, $sql)) {
            $msg = "Consulta médica excluída com sucesso!";
            $msg_type = "success";
            
            // Redirecionar após sucesso
            echo "<script>
                alert('Consulta médica excluída com sucesso!');
                window.location.href='ficha_clinica.php?id=$id_atleta';
            </script>";
            exit();
        } else {
            $msg = "Erro ao excluir consulta médica: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}

// Buscar ficha clínica do atleta
$sql_ficha = "SELECT * FROM ficha_clinica WHERE id_atleta = '$id_atleta'";
$result_ficha = mysqli_query($conn, $sql_ficha);
$ficha = mysqli_fetch_assoc($result_ficha);

// Inicializar variáveis da ficha clínica
$grupo_sanguineo = isset($ficha['grupo_sanguineo']) ? $ficha['grupo_sanguineo'] : '';
$alergias = isset($ficha['alergias']) ? $ficha['alergias'] : '';
$doencas_cronicas = isset($ficha['doencas_cronicas']) ? $ficha['doencas_cronicas'] : '';
$medicacao_regular = isset($ficha['medicacao_regular']) ? $ficha['medicacao_regular'] : '';
$cirurgias_anteriores = isset($ficha['cirurgias_anteriores']) ? $ficha['cirurgias_anteriores'] : '';
$lesoes_anteriores = isset($ficha['lesoes_anteriores']) ? $ficha['lesoes_anteriores'] : '';
$restricoes_alimentares = isset($ficha['restricoes_alimentares']) ? $ficha['restricoes_alimentares'] : '';
$contato_emergencia_nome = isset($ficha['contato_emergencia_nome']) ? $ficha['contato_emergencia_nome'] : '';
$contato_emergencia_telefone = isset($ficha['contato_emergencia_telefone']) ? $ficha['contato_emergencia_telefone'] : '';
$contato_emergencia_relacao = isset($ficha['contato_emergencia_relacao']) ? $ficha['contato_emergencia_relacao'] : '';
$observacoes = isset($ficha['observacoes']) ? $ficha['observacoes'] : '';

// Buscar consultas médicas do atleta
$sql_consultas = "SELECT * FROM consultas_medicas WHERE id_atleta = '$id_atleta' ORDER BY data_consulta DESC";
$result_consultas = mysqli_query($conn, $sql_consultas);
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="ClubeMaster - Sistema de Gestão de Clubes Desportivos">
    <meta name="keywords" content="clube, desporto, gestão, atletas, treinos, jogos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClubeMaster - Ficha Clínica</title>

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
                <ul class="dropdown">
                    <li><a href="./perfil.php?id=<?php echo $id_atleta; ?>">Perfil</a></li>
                    <li class="active"><a href="./ficha_clinica.php?id=<?php echo $id_atleta; ?>">Ficha Clínica</a></li>
                    <li><a href="./documentos.php?id=<?php echo $id_atleta; ?>">Upload de Documentos</a></li>
                    <li><a href="./historico.php?id=<?php echo $id_atleta; ?>">Histórico</a></li>
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
                                    <ul class="dropdown">
                                        <li><a href="./perfil.php?id=<?php echo $id_atleta; ?>">Perfil</a></li>
                                        <li class="active"><a href="./ficha_clinica.php?id=<?php echo $id_atleta; ?>">Ficha Clínica</a></li>
                                        <li><a href="./documentos.php?id=<?php echo $id_atleta; ?>">Upload de Documentos</a></li>
                                        <li><a href="./historico.php?id=<?php echo $id_atleta; ?>">Histórico</a></li>
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
                        <h2>Ficha Clínica - <?php echo $atleta['nome']; ?></h2>
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
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="ficha-tab" data-toggle="tab" href="#ficha" role="tab" aria-controls="ficha" aria-selected="true">Ficha Clínica</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="consultas-tab" data-toggle="tab" href="#consultas" role="tab" aria-controls="consultas" aria-selected="false">Consultas Médicas</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <!-- Ficha Clínica Tab -->
                                    <div class="tab-pane fade show active" id="ficha" role="tabpanel" aria-labelledby="ficha-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Ficha Clínica</h4>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_atleta);?>">
                                                    <div class="form-group row">
                                                        <label for="grupo_sanguineo" class="col-sm-3 col-form-label">Grupo Sanguíneo</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" id="grupo_sanguineo" name="grupo_sanguineo">
                                                                <option value="">Selecione...</option>
                                                                <option value="A+" <?php if($grupo_sanguineo == "A+") echo "selected"; ?>>A+</option>
                                                                <option value="A-" <?php if($grupo_sanguineo == "A-") echo "selected"; ?>>A-</option>
                                                                <option value="B+" <?php if($grupo_sanguineo == "B+") echo "selected"; ?>>B+</option>
                                                                <option value="B-" <?php if($grupo_sanguineo == "B-") echo "selected"; ?>>B-</option>
                                                                <option value="AB+" <?php if($grupo_sanguineo == "AB+") echo "selected"; ?>>AB+</option>
                                                                <option value="AB-" <?php if($grupo_sanguineo == "AB-") echo "selected"; ?>>AB-</option>
                                                                <option value="O+" <?php if($grupo_sanguineo == "O+") echo "selected"; ?>>O+</option>
                                                                <option value="O-" <?php if($grupo_sanguineo == "O-") echo "selected"; ?>>O-</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="alergias" class="col-sm-3 col-form-label">Alergias</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="alergias" name="alergias" rows="2"><?php echo $alergias; ?></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="doencas_cronicas" class="col-sm-3 col-form-label">Doenças Crónicas</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="doencas_cronicas" name="doencas_cronicas" rows="2"><?php echo $doencas_cronicas; ?></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="medicacao_regular" class="col-sm-3 col-form-label">Medicação Regular</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="medicacao_regular" name="medicacao_regular" rows="2"><?php echo $medicacao_regular; ?></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="cirurgias_anteriores" class="col-sm-3 col-form-label">Cirurgias Anteriores</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="cirurgias_anteriores" name="cirurgias_anteriores" rows="2"><?php echo $cirurgias_anteriores; ?></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="lesoes_anteriores" class="col-sm-3 col-form-label">Lesões Anteriores</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="lesoes_anteriores" name="lesoes_anteriores" rows="2"><?php echo $lesoes_anteriores; ?></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="restricoes_alimentares" class="col-sm-3 col-form-label">Restrições Alimentares</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="restricoes_alimentares" name="restricoes_alimentares" rows="2"><?php echo $restricoes_alimentares; ?></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="contato_emergencia_nome" class="col-sm-3 col-form-label">Nome do Contacto de Emergência</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="contato_emergencia_nome" name="contato_emergencia_nome" value="<?php echo $contato_emergencia_nome; ?>">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="contato_emergencia_telefone" class="col-sm-3 col-form-label">Telefone do Contacto de Emergência</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="contato_emergencia_telefone" name="contato_emergencia_telefone" value="<?php echo $contato_emergencia_telefone; ?>">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="contato_emergencia_relacao" class="col-sm-3 col-form-label">Relação com o Atleta</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="contato_emergencia_relacao" name="contato_emergencia_relacao" value="<?php echo $contato_emergencia_relacao; ?>">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label for="observacoes" class="col-sm-3 col-form-label">Observações</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo $observacoes; ?></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <div class="col-sm-12 text-center">
                                                            <button type="submit" name="btn_salvar" class="btn btn-primary">Salvar Ficha Clínica</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Consultas Médicas Tab -->
                                    <div class="tab-pane fade" id="consultas" role="tabpanel" aria-labelledby="consultas-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Consultas Médicas</h4>
                                            </div>
                                            <div class="card-body">
                                                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalNovaConsulta">
                                                    <i class="fa fa-plus"></i> Nova Consulta
                                                </button>
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Data</th>
                                                                <th>Tipo</th>
                                                                <th>Médico</th>
                                                                <th>Diagnóstico</th>
                                                                <th>Apto Treinar</th>
                                                                <th>Apto Competir</th>
                                                                <th>Ações</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if(mysqli_num_rows($result_consultas) > 0): ?>
                                                                <?php while($consulta = mysqli_fetch_assoc($result_consultas)): ?>
                                                                <tr>
                                                                    <td><?php echo date('d/m/Y', strtotime($consulta['data_consulta'])); ?></td>
                                                                    <td><?php echo $consulta['tipo_consulta']; ?></td>
                                                                    <td><?php echo $consulta['medico']; ?></td>
                                                                    <td><?php echo $consulta['diagnostico']; ?></td>
                                                                    <td>
                                                                        <?php if($consulta['apto_treinar'] == 1): ?>
                                                                            <span class="badge badge-success">Sim</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-danger">Não</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if($consulta['apto_competir'] == 1): ?>
                                                                            <span class="badge badge-success">Sim</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-danger">Não</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalDetalhesConsulta<?php echo $consulta['id_consulta']; ?>">
                                                                            <i class="fa fa-eye"></i>
                                                                        </button>
                                                                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_atleta);?>" style="display: inline;">
                                                                            <input type="hidden" name="id_consulta" value="<?php echo $consulta['id_consulta']; ?>">
                                                                            <button type="submit" name="btn_excluir_consulta" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                                
                                                                <!-- Modal Detalhes Consulta -->
                                                                <div class="modal fade" id="modalDetalhesConsulta<?php echo $consulta['id_consulta']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetalhesConsultaLabel<?php echo $consulta['id_consulta']; ?>" aria-hidden="true">
                                                                    <div class="modal-dialog modal-lg" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="modalDetalhesConsultaLabel<?php echo $consulta['id_consulta']; ?>">Detalhes da Consulta</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($consulta['data_consulta'])); ?></p>
                                                                                        <p><strong>Tipo:</strong> <?php echo $consulta['tipo_consulta']; ?></p>
                                                                                        <p><strong>Médico:</strong> <?php echo $consulta['medico']; ?></p>
                                                                                        <p><strong>Diagnóstico:</strong> <?php echo $consulta['diagnostico']; ?></p>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Tratamento:</strong> <?php echo $consulta['tratamento']; ?></p>
                                                                                        <p><strong>Recomendações:</strong> <?php echo $consulta['recomendacoes']; ?></p>
                                                                                        <p><strong>Próxima Consulta:</strong> <?php echo $consulta['data_proxima_consulta'] ? date('d/m/Y', strtotime($consulta['data_proxima_consulta'])) : 'Não agendada'; ?></p>
                                                                                        <p><strong>Apto para Treinar:</strong> <?php echo $consulta['apto_treinar'] == 1 ? 'Sim' : 'Não'; ?></p>
                                                                                        <p><strong>Apto para Competir:</strong> <?php echo $consulta['apto_competir'] == 1 ? 'Sim' : 'Não'; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <p><strong>Observações:</strong> <?php echo $consulta['observacoes']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php endwhile; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="7" class="text-center">Nenhuma consulta médica registrada.</td>
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
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

    <!-- Modal Nova Consulta -->
    <div class="modal fade" id="modalNovaConsulta" tabindex="-1" role="dialog" aria-labelledby="modalNovaConsultaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaConsultaLabel">Nova Consulta Médica</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_atleta);?>">
                        <div class="form-group row">
                            <label for="data_consulta" class="col-sm-3 col-form-label">Data da Consulta</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="data_consulta" name="data_consulta" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="tipo_consulta" class="col-sm-3 col-form-label">Tipo de Consulta</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="tipo_consulta" name="tipo_consulta" required>
                                    <option value="">Selecione...</option>
                                    <option value="Avaliação Física">Avaliação Física</option>
                                    <option value="Lesão">Lesão</option>
                                    <option value="Rotina">Rotina</option>
                                    <option value="Emergência">Emergência</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="medico" class="col-sm-3 col-form-label">Médico</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="medico" name="medico" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="diagnostico" class="col-sm-3 col-form-label">Diagnóstico</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="diagnostico" name="diagnostico" rows="2"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="tratamento" class="col-sm-3 col-form-label">Tratamento</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="tratamento" name="tratamento" rows="2"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="recomendacoes" class="col-sm-3 col-form-label">Recomendações</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="recomendacoes" name="recomendacoes" rows="2"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="data_proxima_consulta" class="col-sm-3 col-form-label">Próxima Consulta</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="data_proxima_consulta" name="data_proxima_consulta">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-3">Aptidão</div>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="apto_treinar" name="apto_treinar" checked>
                                    <label class="form-check-label" for="apto_treinar">
                                        Apto para Treinar
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="apto_competir" name="apto_competir" checked>
                                    <label class="form-check-label" for="apto_competir">
                                        Apto para Competir
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="observacoes_consulta" class="col-sm-3 col-form-label">Observações</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="observacoes_consulta" name="observacoes_consulta" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-12 text-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" name="btn_adicionar_consulta" class="btn btn-primary">Adicionar Consulta</button>
                            </div>
                        </div>
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
