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

// Função para obter eventos do calendário (treinos ou jogos)
if(isset($_GET['tipo']) && $_GET['tipo'] == 'treinos') {
    // Buscar treinos
    $sql = "SELECT id_treino as id, data as start, CONCAT(escalao, ' - ', local) as title, 'treino' as tipo FROM treinos WHERE codigo_clube = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    $eventos = array();
    while($row = mysqli_fetch_assoc($result)) {
        $row['url'] = 'calendario_treinos.php?editar=' . $row['id'];
        $row['className'] = 'bg-success'; // Cor verde para treinos
        $eventos[] = $row;
    }
    
    echo json_encode($eventos);
    exit();
} elseif(isset($_GET['tipo']) && $_GET['tipo'] == 'jogos') {
    // Buscar jogos
    $sql = "SELECT id_jogo as id, data as start, CONCAT(escalao, ' vs ', adversario) as title, 'jogo' as tipo FROM jogos WHERE codigo_clube = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    $eventos = array();
    while($row = mysqli_fetch_assoc($result)) {
        $row['url'] = 'calendario_jogos.php?editar=' . $row['id'];
        $row['className'] = 'bg-primary'; // Cor azul para jogos
        $eventos[] = $row;
    }
    
    echo json_encode($eventos);
    exit();
}
?>
