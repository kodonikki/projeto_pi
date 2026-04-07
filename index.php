<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Reservas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">CIETEC</div>
        <nav>
            <ul>
                <li><a href="index.php" class="active">Calendário</a></li>
                <li><a href="mensagens.php">Mensagens</a></li>
                <li><a href="perfil.php">Perfil</a></li>
            </ul>
        </nav>
        <div class="user-info">Olá, <?= htmlspecialchars($_SESSION['nome']) ?>! <a href="logout.php">Sair</a></div>
    </header>

    <main class="main-container">
        <div class="calendar-section">
            <div class="calendar-header">
                <h2 id="mes-ano"></h2>
                <button id="btnNovaReserva">Nova Reserva</button>
            </div>
            <div class="calendar-grid" id="calendar"></div> 
        </div>

        <aside class="side-panel" id="sidePanel">
            <h3>Reservas do Dia: <span id="dataSelecionada"></span></h3>
            <div id="listaReservas">Clique em um dia para ver as reservas.</div>
        </aside>
    </main>

    <div id="modalReserva" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Fazer Nova Reserva</h2>
            <form id="formReserva">
                <input type="hidden" id="reservaUsuario" value="<?= $_SESSION['nome'] ?>">
                <label>Data:</label>
                <input type="date" id="reservaData" required>
                <label>Horário:</label>
                <input type="time" id="reservaHora" required>
                <label>Descrição:</label>
                <textarea id="reservaDesc" required></textarea>
                <button type="submit">Salvar Reserva</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>