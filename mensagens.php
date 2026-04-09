<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$meu_id = $_SESSION['usuario_id'];

// Enviar Mensagem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destinatario']) && isset($_POST['mensagem'])) {
    $destinatario = $_POST['destinatario'];
    $mensagem = $_POST['mensagem'];

    $stmt = $pdo->prepare("INSERT INTO mensagens (remetente_id, destinatario_id, mensagem) VALUES (?, ?, ?)");
    $stmt->execute([$meu_id, $destinatario, $mensagem]);
    $sucesso = "Mensagem enviada!";
}

// Buscar todos os usuários (exceto o logado) para o select de envio
$stmt_users = $pdo->prepare("SELECT id, nome FROM usuarios WHERE id != ?");
$stmt_users->execute([$meu_id]);
$usuarios = $stmt_users->fetchAll();

// Buscar mensagens recebidas
$stmt_msg = $pdo->prepare("
    SELECT m.mensagem, m.data_envio, u.nome AS remetente 
    FROM mensagens m 
    JOIN usuarios u ON m.remetente_id = u.id 
    WHERE m.destinatario_id = ? 
    ORDER BY m.data_envio DESC
");
$stmt_msg->execute([$meu_id]);
$mensagens_recebidas = $stmt_msg->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mensagens</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="main-container" style="display: block;">
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h2>Enviar Nova Mensagem</h2>
            <?php if(isset($sucesso)) echo "<p style='color: green;'>$sucesso</p>"; ?>
            <form method="POST" style="max-width: 500px;">
                <label>Para:</label>
                <select name="destinatario" required style="padding: 8px;">
                    <option value="">Selecione um usuário...</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Mensagem:</label>
                <textarea name="mensagem" required rows="4"></textarea>
                <button type="submit" style="background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer;">Enviar</button>
            </form>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px;">
            <h2>Caixa de Entrada</h2>
            <?php if (count($mensagens_recebidas) === 0): ?>
                <p>Você não tem novas mensagens.</p>
            <?php else: ?>
                <?php foreach ($mensagens_recebidas as $msg): ?>
                    <div style="border-bottom: 1px solid #ccc; padding: 10px 0;">
                        <strong>De: <?= htmlspecialchars($msg['remetente']) ?></strong> 
                        <span style="color: #888; font-size: 0.8em;">(<?= date('d/m/Y H:i', strtotime($msg['data_envio'])) ?>)</span>
                        <p style="margin-top: 5px;"><?= nl2br(htmlspecialchars($msg['mensagem'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>