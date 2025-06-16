<?php
// Função para adicionar o logo do clube no cabeçalho
function adicionar_logo_clube($codigo_clube) {
    global $conn;
    
    // Buscar informações do clube
    $sql = "SELECT imagem FROM clube WHERE codigo = '$codigo_clube'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $clube = mysqli_fetch_assoc($result);
        $imagem = $clube['imagem'];
        
        if(!empty($imagem) && $imagem != 'no_image.png') {
            return "imagens/{$imagem}";
        }
    }
    
    return "imagens/ClubeMaster_pequeno.png"; // Logo padrão se não houver logo do clube
}

// Função para gerar o HTML do cabeçalho com logo do clube
function gerar_cabecalho_com_logo($codigo_clube) {
    $logo_path = adicionar_logo_clube($codigo_clube);
    
    $html = '
    <div class="header__top" style="padding: 20px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="ht-info d-flex align-items-center">
                        <a href="home.php" class="mr-4">
                            <img src="' . $logo_path . '" alt="Logo do Clube" style="max-height: 60px; max-width: 100%;">
                        </a>
                        <a href="home.php"><img src="imagens/ClubeMaster_pequeno.png" alt="ClubeMaster" style="max-height: 40px;"></a>
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
    </div>';
    
    return $html;
}
?>
