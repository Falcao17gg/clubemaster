<?php
// Ligar à base de dados
include 'ligarbd.php';

// Validar sessão
if (isset($_SESSION['clube'])) {
    $codigo_clube = $_SESSION['clube'];
} else {
    // Se não houver sessão, retornar erro ou redirecionar
    // Aqui, vamos retornar um array vazio ou uma mensagem de erro
    echo json_encode([]); // Retorna um array JSON vazio em caso de erro
    exit();
}

// Obter escalão do pedido GET
$escalao = isset($_GET['escalao']) ? mysqli_real_escape_string($conn, $_GET['escalao']) : '';

// Inicializar array de atletas
$atletas = [];

// Se o escalão foi fornecido, buscar atletas
if (!empty($escalao)) {
    $sql = "SELECT id_atleta, nome FROM atletas WHERE codigo_clube = '$codigo_clube' AND escalao = '$escalao' ORDER BY nome ASC";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $atletas[] = $row;
        }
    }
}

// Retornar atletas como JSON
header('Content-Type: application/json');
echo json_encode($atletas);
?>
