<?php
// Arquivo para buscar atletas convocados para um jogo e suas presenças
session_start();

// Ligar à base de dados
include 'ligarbd.php';

// Validar sessão
if (!isset($_SESSION['clube'])) {
    echo json_encode(['error' => 'Sessão inválida']);
    exit();
}

$codigo_clube = $_SESSION['clube'];

// Verificar se foi enviado o ID do jogo
if (!isset($_POST['id_jogo'])) {
    echo json_encode(['error' => 'ID do jogo não fornecido']);
    exit();
}

$id_jogo = $_POST['id_jogo'];

// Verificar se o jogo pertence ao clube atual
$sql_check_jogo = "SELECT * FROM jogos WHERE id_jogo = '$id_jogo' AND codigo_clube = '$codigo_clube'";
$result_check_jogo = mysqli_query($conn, $sql_check_jogo);
if (mysqli_num_rows($result_check_jogo) == 0) {
    echo json_encode(['error' => 'Jogo não encontrado ou não pertence ao seu clube']);
    exit();
}

// Buscar atletas convocados para o jogo
$sql_atletas = "SELECT a.id_atleta, a.nome, a.escalao 
                FROM atletas a 
                JOIN convocatorias_jogo cj ON a.id_atleta = cj.id_atleta 
                WHERE cj.id_jogo = '$id_jogo' AND a.codigo_clube = '$codigo_clube'";
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

// Buscar presenças já registradas para este jogo
$sql_presencas = "SELECT id_atleta, presente, justificacao FROM presencas_jogo WHERE id_jogo = '$id_jogo'";
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
