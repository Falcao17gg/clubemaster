<?php
// Arquivo para obter sub-escalões com base no escalão selecionado
session_start();
include 'ligarbd.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['clube'])) {
    echo json_encode([]);
    exit;
}

$codigo_clube = $_SESSION['clube'];

// Verificar se o escalão foi enviado
if (isset($_POST['escalao'])) {
    $escalao = $_POST['escalao'];
    
    // Buscar o ID do escalão
    $sql_escalao = "SELECT id_escalao FROM escaloes WHERE nome = '$escalao' AND codigo_clube = '$codigo_clube'";
    $result_escalao = mysqli_query($conn, $sql_escalao);
    
    if ($row_escalao = mysqli_fetch_assoc($result_escalao)) {
        $id_escalao = $row_escalao['id_escalao'];
        
        // Buscar os sub-escalões
        $sql_sub_escaloes = "SELECT * FROM sub_escaloes WHERE id_escalao = '$id_escalao' AND codigo_clube = '$codigo_clube' AND ativo = 1 ORDER BY nome";
        $result_sub_escaloes = mysqli_query($conn, $sql_sub_escaloes);
        
        $sub_escaloes = [];
        while ($row = mysqli_fetch_assoc($result_sub_escaloes)) {
            $sub_escaloes[] = $row;
        }
        
        echo json_encode($sub_escaloes);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
