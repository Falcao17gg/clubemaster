<?php
include 'ligarbd.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'];
    $senha = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verificar se o código existe na BD
    $sql = "SELECT * FROM clube WHERE codigo = '$codigo'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Atualizar a senha na BD
        $sql = "UPDATE clube SET password = '$senha' WHERE codigo = '$codigo'";
        if (mysqli_query($conn, $sql)) {
            echo "Senha criada com sucesso! Agora pode fazer login.";
        } else {
            echo "Erro ao definir a senha!";
        }
    } else {
        echo "Código inválido!";
    }
}
?>
<form method="post">
    <input type="text" name="codigo" placeholder="Código recebido" required>
    <input type="password" name="password" placeholder="Nova senha" required>
    <button type="submit">Criar Senha</button>
</form>
