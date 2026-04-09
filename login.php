<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: index.php");
        exit;
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <h2>Entrar no Sistema</h2>
        <?php if(isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <p>Não tem conta? <a href="register.php">Registre-se</a></p>
    </div>
</body>
</html>