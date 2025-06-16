<?php
// Arquivo para buscar atletas convocados para um treino e suas presenças
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

// Buscar atletas convocados para o treino
$sql_atletas = "SELECT a.id_atleta, a.nome, a.escalao 
                FROM atletas a 
                JOIN convocatorias_treino ct ON a.id_atleta = ct.id_atleta 
                WHERE ct.id_treino = '$id_treino' AND a.codigo_clube = '$codigo_clube'";
$result_atletas = mysqli_query($conn, $sql_atletas);

$atletas = [];
if ($result_atletas && mysqli_num_rows($result_atletas) > 0) {
    while ($row = mysqli_fetch_assoc($result_atletas)) {
        $atletas[] = $row;
    }
}

// Se não houver atletas convocados, buscar todos os atletas do clube
if (empty($atletas)) {
    $sql_todos_atletas = "SELECT id_atleta, nome, escalao FROM atletas WHERE codigo_clube = '$codigo_clube' ORDER BY nome";
    $result_todos_atletas = mysqli_query($conn, $sql_todos_atletas);
    
    if ($result_todos_atletas && mysqli_num_rows($result_todos_atletas) > 0) {
        while ($row = mysqli_fetch_assoc($result_todos_atletas)) {
            $atletas[] = $row;
        }
    }
}

// Buscar presenças já registradas para este treino
$sql_presencas = "SELECT id_atleta, presente, justificacao FROM presencas_treino WHERE id_treino = '$id_treino'";
$result_presencas = mysqli_query($conn, $sql_presencas);

$presencas = [];
if ($result_presencas && mysqli_num_rows($result_presencas) > 0) {
    while ($row = mysqli_fetch_assoc($result_presencas)) {
        $presencas[$row['id_atleta']] = [
            'presente' => $row['presente'],
            'observacao' => $row['justificacao']
        ];
    }
}

// Retornar dados em formato JSON
echo json_encode([
    'atletas' => $atletas,
    'presencas' => $presencas
]);
