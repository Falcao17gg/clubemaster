<?php
$servername = "localhost";
$database = "clubemasterbd";
$username = "root";
$password = "";

// Tratamento de erros e mensagens amigáveis
$connection_error = false;
$error_message = "";

// Criar ligação à BD
try {
    $conn = mysqli_connect($servername, $username, $password, $database);

    // Verificar conexão
    if (!$conn) {
        $connection_error = true;
        $error_message = "Falha na conexão com a base de dados: " . mysqli_connect_error();
    }
    
    // Verificar se a tabela 'treinos' existe (tabela essencial)
    if (!$connection_error) {
        $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'treinos'");
        if (mysqli_num_rows($check_table) == 0) {
            $connection_error = true;
            $error_message = "A tabela 'treinos' não existe na base de dados. Por favor, importe o ficheiro SQL completo.";
        }
    }
    
} catch (Exception $e) {
    $connection_error = true;
    $error_message = "Erro ao conectar à base de dados: " . $e->getMessage();
}

// Se houver erro de conexão e não estivermos na página de erro, redirecionar
if ($connection_error && basename($_SERVER['PHP_SELF']) != 'erro_bd.php') {
    // Salvar mensagem de erro em sessão
    session_start();
    $_SESSION['db_error'] = $error_message;
    
    // Criar página de erro se não existir
    $error_page = dirname(__FILE__) . '/erro_bd.php';
    if (!file_exists($error_page)) {
        $error_content = '<?php
        session_start();
        $error_message = isset($_SESSION["db_error"]) ? $_SESSION["db_error"] : "Erro desconhecido na base de dados";
        ?>
        <!DOCTYPE html>
        <html lang="pt-PT">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erro de Conexão - ClubeMaster</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    margin: 0;
                    padding: 20px;
                    background-color: #f8f9fa;
                }
                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }
                h1 {
                    color: #dc3545;
                    margin-bottom: 20px;
                }
                .error-box {
                    background-color: #f8d7da;
                    color: #721c24;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                    border: 1px solid #f5c6cb;
                }
                .steps {
                    background-color: #e9ecef;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .steps h3 {
                    margin-top: 0;
                }
                .steps ol {
                    margin-bottom: 0;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 14px;
                    color: #6c757d;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Erro de Conexão com a Base de Dados</h1>
                
                <div class="error-box">
                    <strong>Mensagem de erro:</strong> <?php echo $error_message; ?>
                </div>
                
                <div class="steps">
                    <h3>Como resolver:</h3>
                    <ol>
                        <li>Verifique se o servidor MySQL está em execução</li>
                        <li>Confirme se a base de dados "clubemasterbd" foi criada</li>
                        <li>Importe o ficheiro SQL completo para criar todas as tabelas necessárias</li>
                        <li>Verifique as credenciais de acesso no ficheiro ligarbd.php</li>
                        <li>Reinicie o servidor web após as alterações</li>
                    </ol>
                </div>
                
                <p>Se o problema persistir, entre em contato com o suporte técnico.</p>
                
                <div class="footer">
                    ClubeMaster - Sistema de Gestão de Clubes Desportivos
                </div>
            </div>
        </body>
        </html>';
        
        file_put_contents($error_page, $error_content);
    }
    
    // Redirecionar para página de erro
    header("Location: erro_bd.php");
    exit();
}
?>
