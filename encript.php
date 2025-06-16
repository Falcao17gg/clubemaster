<?php
$password="12345678";
$passwordencript=trim($password);
$password = password_hash($passwordencript, PASSWORD_BCRYPT);
echo $password;
?>