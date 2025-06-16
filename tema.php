<?php
// Funções para gerenciamento de temas personalizáveis

// Obter as cores do tema atual do clube
function obter_cores_tema($codigo_clube) {
    global $conn;
    
    $cores = array(
        'primaria' => '#dc3545',  // Vermelho padrão
        'secundaria' => '#343a40', // Cinza escuro padrão
        'texto' => '#ffffff',      // Branco padrão
        'tema_ativo' => 'padrao'   // Tema padrão
    );
    
    $sql = "SELECT tema_cor_primaria, tema_cor_secundaria, tema_cor_texto, tema_ativo 
            FROM clube WHERE codigo = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $cores['primaria'] = $row['tema_cor_primaria'];
        $cores['secundaria'] = $row['tema_cor_secundaria'];
        $cores['texto'] = $row['tema_cor_texto'];
        $cores['tema_ativo'] = $row['tema_ativo'];
    }
    
    return $cores;
}

// Atualizar as cores do tema do clube
function atualizar_cores_tema($codigo_clube, $cor_primaria, $cor_secundaria, $cor_texto, $tema_ativo) {
    global $conn;
    
    $sql = "UPDATE clube SET 
            tema_cor_primaria = '$cor_primaria',
            tema_cor_secundaria = '$cor_secundaria',
            tema_cor_texto = '$cor_texto',
            tema_ativo = '$tema_ativo'
            WHERE codigo = '$codigo_clube'";
    
    return mysqli_query($conn, $sql);
}

// Gerar CSS dinâmico com base nas cores do tema
function gerar_css_tema($cores) {
    $css = "
    /* CSS dinâmico gerado para o tema personalizado */
    :root {
        --cor-primaria: {$cores['primaria']};
        --cor-secundaria: {$cores['secundaria']};
        --cor-texto: {$cores['texto']};
    }
    
    /* Cabeçalhos e elementos principais */
    .header__nav {
        background-color: var(--cor-primaria);
    }
    
    .primary-btn {
        background-color: var(--cor-primaria);
        color: var(--cor-texto);
    }
    
    .primary-btn:hover {
        background-color: var(--cor-secundaria);
    }
    
    /* Menus e navegação */
    .nav-menu .main-menu li.active a {
        background-color: var(--cor-secundaria);
    }
    
    .nav-menu .main-menu li:hover > a {
        background-color: var(--cor-secundaria);
    }
    
    /* Cartões e cabeçalhos */
    .card-header.bg-danger {
        background-color: var(--cor-primaria) !important;
        color: var(--cor-texto) !important;
    }
    
    /* Botões */
    .btn-primary {
        background-color: var(--cor-primaria);
        border-color: var(--cor-primaria);
    }
    
    .btn-primary:hover {
        background-color: var(--cor-secundaria);
        border-color: var(--cor-secundaria);
    }
    
    /* Rodapé */
    .footer-section {
        background-color: var(--cor-secundaria);
    }
    ";
    
    return $css;
}

// Obter temas predefinidos
function obter_temas_predefinidos() {
    $temas = array(
        'padrao' => array(
            'nome' => 'Padrão (Vermelho)',
            'primaria' => '#dc3545',
            'secundaria' => '#343a40',
            'texto' => '#ffffff'
        ),
        'azul' => array(
            'nome' => 'Azul',
            'primaria' => '#007bff',
            'secundaria' => '#0056b3',
            'texto' => '#ffffff'
        ),
        'verde' => array(
            'nome' => 'Verde',
            'primaria' => '#28a745',
            'secundaria' => '#1e7e34',
            'texto' => '#ffffff'
        ),
        'laranja' => array(
            'nome' => 'Laranja',
            'primaria' => '#fd7e14',
            'secundaria' => '#c96500',
            'texto' => '#ffffff'
        ),
        'roxo' => array(
            'nome' => 'Roxo',
            'primaria' => '#6f42c1',
            'secundaria' => '#4e2d89',
            'texto' => '#ffffff'
        ),
        'escuro' => array(
            'nome' => 'Modo Escuro',
            'primaria' => '#343a40',
            'secundaria' => '#1d2124',
            'texto' => '#ffffff'
        )
    );
    
    return $temas;
}

// Aplicar tema predefinido
function aplicar_tema_predefinido($codigo_clube, $tema_id) {
    $temas = obter_temas_predefinidos();
    
    if (isset($temas[$tema_id])) {
        $tema = $temas[$tema_id];
        return atualizar_cores_tema(
            $codigo_clube,
            $tema['primaria'],
            $tema['secundaria'],
            $tema['texto'],
            $tema_id
        );
    }
    
    return false;
}
?>
