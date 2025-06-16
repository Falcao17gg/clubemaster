<?php
// Funções de autenticação e controle de permissões

// Verificar se o utilizador está autenticado
function verificar_autenticacao() {
    if (!isset($_SESSION['clube'])) {
        header("Location: index.php");
        exit();
    }
    
    // Registrar último acesso se for um utilizador específico
    if (isset($_SESSION['id_utilizador'])) {
        global $conn;
        $id_utilizador = $_SESSION['id_utilizador'];
        $sql = "UPDATE utilizadores SET ultimo_acesso = NOW() WHERE id_utilizador = '$id_utilizador'";
        mysqli_query($conn, $sql);
    }
    
    return true;
}

// Carregar permissões do utilizador
function carregar_permissoes($id_funcao) {
    global $conn;
    $permissoes = array();
    
    $sql = "SELECT modulo, visualizar, adicionar, editar, excluir FROM permissoes WHERE id_funcao = '$id_funcao'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $modulo = $row['modulo'];
            $permissoes[$modulo] = array(
                'visualizar' => $row['visualizar'],
                'adicionar' => $row['adicionar'],
                'editar' => $row['editar'],
                'excluir' => $row['excluir']
            );
        }
    }
    
    return $permissoes;
}

// Verificar se o utilizador tem permissão para uma operação específica
function verificar_permissao($modulo, $operacao) {
    // Se não existir sistema de permissões (clube sem multi_utilizadores), permitir tudo
    if (!isset($_SESSION['permissoes'])) {
        return true;
    }
    
    // Se o utilizador for presidente, permitir tudo
    if (isset($_SESSION['id_funcao']) && $_SESSION['id_funcao'] == 1) {
        return true;
    }
    
    // Verificar permissão específica
    if (!isset($_SESSION['permissoes'][$modulo])) {
        return false;
    }
    
    return isset($_SESSION['permissoes'][$modulo][$operacao]) && $_SESSION['permissoes'][$modulo][$operacao] == 1;
}

// Registrar ação no log
function registrar_log($acao, $descricao = '') {
    global $conn;
    
    $codigo_clube = $_SESSION['clube'];
    $id_utilizador = isset($_SESSION['id_utilizador']) ? $_SESSION['id_utilizador'] : 'NULL';
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $sql = "INSERT INTO logs (codigo_clube, id_utilizador, data_hora, acao, descricao, ip) 
            VALUES ('$codigo_clube', $id_utilizador, NOW(), '$acao', '$descricao', '$ip')";
    
    mysqli_query($conn, $sql);
}

// Obter nome da função pelo ID
function obter_nome_funcao($id_funcao) {
    global $conn;
    
    $sql = "SELECT nome_funcao FROM funcoes WHERE id_funcao = '$id_funcao'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['nome_funcao'];
    }
    
    return 'Desconhecido';
}

// Verificar se o clube tem sistema de múltiplos utilizadores ativado
function tem_multi_utilizadores($codigo_clube) {
    global $conn;
    
    $sql = "SELECT multi_utilizadores FROM clube WHERE codigo = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['multi_utilizadores'] == 1;
    }
    
    return false;
}

// Migrar clube para sistema de múltiplos utilizadores
function migrar_para_multi_utilizadores($codigo_clube, $nome_utilizador, $email) {
    global $conn;
    
    // Obter dados do clube
    $sql = "SELECT * FROM clube WHERE codigo = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $clube = mysqli_fetch_assoc($result);
        
        // Criar utilizador presidente
        $password = $clube['password']; // Manter a mesma senha
        $data_registo = date('Y-m-d');
        
        $sql = "INSERT INTO utilizadores (codigo_clube, nome, email, password, id_funcao, data_registo) 
                VALUES ('$codigo_clube', '$nome_utilizador', '$email', '$password', 1, '$data_registo')";
        
        if (mysqli_query($conn, $sql)) {
            // Atualizar clube para multi_utilizadores
            $sql = "UPDATE clube SET multi_utilizadores = 1 WHERE codigo = '$codigo_clube'";
            mysqli_query($conn, $sql);
            
            return true;
        }
    }
    
    return false;
}
?>
