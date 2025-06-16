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
    echo json_encode(['error' => 'Sessão inválida']);
    exit();
}

// Verificar parâmetros
if (!isset($_GET['id_atleta']) || empty($_GET['id_atleta'])) {
    echo json_encode(['error' => 'ID do atleta não fornecido']);
    exit();
}

$id_atleta = mysqli_real_escape_string($conn, $_GET['id_atleta']);
$periodo = isset($_GET['periodo']) ? mysqli_real_escape_string($conn, $_GET['periodo']) : 'mes';
$data_inicio = "";
$data_fim = "";

// Definir período de análise
switch($periodo) {
    case 'semana':
        $data_inicio = date('Y-m-d', strtotime('-7 days'));
        $data_fim = date('Y-m-d');
        break;
    case 'mes':
        $data_inicio = date('Y-m-d', strtotime('-30 days'));
        $data_fim = date('Y-m-d');
        break;
    case 'trimestre':
        $data_inicio = date('Y-m-d', strtotime('-90 days'));
        $data_fim = date('Y-m-d');
        break;
    case 'ano':
        $data_inicio = date('Y-m-d', strtotime('-365 days'));
        $data_fim = date('Y-m-d');
        break;
    case 'personalizado':
        $data_inicio = isset($_GET['data_inicio']) ? mysqli_real_escape_string($conn, $_GET['data_inicio']) : date('Y-m-d', strtotime('-30 days'));
        $data_fim = isset($_GET['data_fim']) ? mysqli_real_escape_string($conn, $_GET['data_fim']) : date('Y-m-d');
        break;
    default:
        $data_inicio = date('Y-m-d', strtotime('-30 days'));
        $data_fim = date('Y-m-d');
}

// Buscar dados do atleta
$sql_atleta = "SELECT nome, escalao FROM atletas WHERE id_atleta = '$id_atleta' AND codigo_clube = '$codigo_clube'";
$result_atleta = mysqli_query($conn, $sql_atleta);

if (mysqli_num_rows($result_atleta) == 0) {
    echo json_encode(['error' => 'Atleta não encontrado']);
    exit();
}

$row_atleta = mysqli_fetch_assoc($result_atleta);
$nome_atleta = $row_atleta['nome'];
$escalao_atleta = $row_atleta['escalao'];

// Buscar estatísticas de presenças do atleta
$sql_presencas = "SELECT 
                    COUNT(pt.id_presenca) as total_registros,
                    SUM(CASE WHEN pt.presente = 1 THEN 1 ELSE 0 END) as presentes,
                    SUM(CASE WHEN pt.presente = 0 THEN 1 ELSE 0 END) as ausentes
                  FROM presencas_treino pt
                  JOIN treinos t ON pt.id_treino = t.id_treino
                  WHERE pt.id_atleta = '$id_atleta' 
                    AND pt.codigo_clube = '$codigo_clube' 
                    AND t.data_treino BETWEEN '$data_inicio' AND '$data_fim'";
$result_presencas = mysqli_query($conn, $sql_presencas);
$row_presencas = mysqli_fetch_assoc($result_presencas);

$total_registros = $row_presencas['total_registros'];
$presentes = $row_presencas['presentes'];
$ausentes = $row_presencas['ausentes'];
$percentual_presenca = ($total_registros > 0) ? round(($presentes / $total_registros) * 100, 2) : 0;

// Buscar detalhes dos treinos
$sql_treinos = "SELECT 
                  t.id_treino,
                  t.data_treino,
                  t.hora_inicio,
                  t.hora_fim,
                  t.local,
                  pt.presente,
                  pt.justificacao
                FROM treinos t
                LEFT JOIN presencas_treino pt ON t.id_treino = pt.id_treino AND pt.id_atleta = '$id_atleta'
                WHERE t.codigo_clube = '$codigo_clube' 
                  AND t.data_treino BETWEEN '$data_inicio' AND '$data_fim'
                  AND t.escalao = '$escalao_atleta'
                ORDER BY t.data_treino DESC";
$result_treinos = mysqli_query($conn, $sql_treinos);

// Preparar resposta HTML
$html = '
<div class="card">
    <div class="card-header">
        <h5>Estatísticas de ' . htmlspecialchars($nome_atleta) . ' - ' . htmlspecialchars($escalao_atleta) . '</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total de Treinos</h5>
                        <p class="card-text display-4">' . $total_registros . '</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Presenças</h5>
                        <p class="card-text display-4">' . $presentes . '</p>
                        <p class="card-text">' . $percentual_presenca . '%</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Ausências</h5>
                        <p class="card-text display-4">' . $ausentes . '</p>
                        <p class="card-text">' . (100 - $percentual_presenca) . '%</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="progress mb-4" style="height: 30px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: ' . $percentual_presenca . '%;" 
                aria-valuenow="' . $percentual_presenca . '" aria-valuemin="0" aria-valuemax="100">
                ' . $percentual_presenca . '% Presenças
            </div>
            <div class="progress-bar bg-danger" role="progressbar" style="width: ' . (100 - $percentual_presenca) . '%;" 
                aria-valuenow="' . (100 - $percentual_presenca) . '" aria-valuemin="0" aria-valuemax="100">
                ' . (100 - $percentual_presenca) . '% Ausências
            </div>
        </div>
        
        <h6 class="mt-4">Detalhes dos Treinos</h6>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Local</th>
                        <th>Presença</th>
                        <th>Justificação</th>
                    </tr>
                </thead>
                <tbody>';

if (mysqli_num_rows($result_treinos) > 0) {
    while ($row = mysqli_fetch_assoc($result_treinos)) {
        $data_formatada = date('d/m/Y', strtotime($row['data_treino']));
        $horario = date('H:i', strtotime($row['hora_inicio'])) . ' - ' . date('H:i', strtotime($row['hora_fim']));
        $presente = isset($row['presente']) ? ($row['presente'] == 1 ? 'Presente' : 'Ausente') : 'Não registrado';
        $justificacao = isset($row['justificacao']) && !empty($row['justificacao']) ? $row['justificacao'] : '-';
        
        $classe_presenca = '';
        if ($presente == 'Presente') {
            $classe_presenca = 'text-success';
        } elseif ($presente == 'Ausente') {
            $classe_presenca = 'text-danger';
        }
        
        $html .= '
                    <tr>
                        <td>' . $data_formatada . '</td>
                        <td>' . $horario . '</td>
                        <td>' . htmlspecialchars($row['local']) . '</td>
                        <td class="' . $classe_presenca . '">' . $presente . '</td>
                        <td>' . htmlspecialchars($justificacao) . '</td>
                    </tr>';
    }
} else {
    $html .= '
                    <tr>
                        <td colspan="5" class="text-center">Nenhum treino encontrado para o período selecionado.</td>
                    </tr>';
}

$html .= '
                </tbody>
            </table>
        </div>
    </div>
</div>';

echo $html;
?>
