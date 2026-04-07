<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        if ($stmt->execute([$nome, $email, $senha])) {
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao registrar. O e-mail pode já estar em uso.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Registro</title>
</head>
<body>
    <div class="login-container">
        <h2>Criar Conta</h2>
        <?php if(isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
        <form method="POST">
            <input type="text" name="nome" placeholder="Nome Completo" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Registrar</button>
        </form>
        <p>Já tem conta? <a href="login.php">Faça login</a></p>
    </div>
</body>
</html>