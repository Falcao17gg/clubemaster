<?php
// Arquivo para buscar atletas convocados para um treino
session_start();

// Ligar à base de dados
include 'ligarbd.php';

// Validar sessão
if (!isset($_SESSION['clube'])) {
    echo json_encode(['error' => 'Sessão inválida']);
    exit();
}

$codigo_clube = $_SESSION['clube'];

// Verificar se foi enviado o ID do treino
if (!isset($_POST['id_treino'])) {
    echo json_encode(['error' => 'ID do treino não fornecido']);
    exit();
}

$id_treino = $_POST['id_treino'];

// Verificar se o treino pertence ao clube atual
$sql_check_treino = "SELECT * FROM treinos WHERE id_treino = '$id_treino' AND codigo_clube = '$codigo_clube'";
$result_check_treino = mysqli_query($conn, $sql_check_treino);
if (mysqli_num_rows($result_check_treino) == 0) {
    echo json_encode(['error' => 'Treino não encontrado ou não pertence ao seu clube']);
    exit();
}

// Buscar atletas convocados para este treino
$sql = "SELECT id_atleta FROM convocatorias_treino WHERE id_treino = '$id_treino'";
$result = mysqli_query($conn, $sql);

$atletas = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $atletas[] = $row['id_atleta'];
    }
}

// Retornar dados em formato JSON
echo json_encode(['atletas' => $atletas]);
