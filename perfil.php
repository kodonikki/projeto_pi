<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$meu_id = $_SESSION['usuario_id'];

// Atualizar Perfil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_nome = trim($_POST['nome']);
    $nova_senha = $_POST['senha'];

    if (!empty($nova_senha)) {
        // Atualiza nome e senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?");
        $stmt->execute([$novo_nome, $senha_hash, $meu_id]);
    } else {
        // Atualiza apenas o nome
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
        $stmt->execute([$novo_nome, $meu_id]);
    }
    
    $_SESSION['nome'] = $novo_nome; // Atualiza a sessão
    $sucesso = "Perfil atualizado com sucesso!";
}

// Buscar dados atuais
$stmt = $pdo->prepare("SELECT nome, email FROM usuarios WHERE id = ?");
$stmt->execute([$meu_id]);
$usuario_atual = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="main-container" style="display: flex; justify-content: center; align-items: flex-start;">
        <div style="background: white; padding: 30px; border-radius: 8px; width: 400px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <h2>Editar Perfil</h2>
            <?php if(isset($sucesso)) echo "<p style='color: green;'>$sucesso</p>"; ?>
            <form method="POST">
                <label>E-mail (Não alterável):</label>
                <input type="email" value="<?= htmlspecialchars($usuario_atual['email']) ?>" disabled style="background: #eee;">
                
                <label>Nome:</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($usuario_atual['nome']) ?>" required>
                
                <label>Nova Senha (deixe em branco para manter a atual):</label>
                <input type="password" name="senha" placeholder="Digite a nova senha">
                
                <button type="submit" style="background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; margin-top: 10px;">Salvar Alterações</button>
            </form>
        </div>
    </main>
</body>
</html>