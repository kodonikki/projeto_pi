<?php
session_start();
require 'config.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Busca laboratórios para o select do modal
try {
    $stmt_labs = $pdo->query("SELECT * FROM laboratorios ORDER BY nome ASC");
    $laboratorios = $stmt_labs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar laboratórios: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reservas - ETEC</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="main-container">
        <section class="calendar-section">
            <div class="calendar-header">
                <button id="btnPrevMes" class="btn">&lt;</button>
                <h2 id="mes-ano"></h2>
                <button id="btnNextMes" class="btn">&gt;</button>
                <button id="btnNovaReserva" class="btn">Nova Reserva</button>
            </div>
            
            <div class="calendar-grid-header" style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: bold; margin-bottom: 10px;">
                <div>Dom</div><div>Seg</div><div>Ter</div><div>Qua</div><div>Qui</div><div>Sex</div><div>Sáb</div>
            </div>

            <div id="calendar" class="calendar-grid"></div>
        </section>

        <aside class="side-panel">
            <h3>Reservas para: <span id="dataSelecionada">--/--/----</span></h3>
            <hr>
            <div id="listaReservas" class="reservas-container">
                <p>Selecione um dia para ver as reservas.</p>
            </div>
        </aside>
    </div>

    <div id="modalReserva" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Nova Reserva</h2>
            <form id="formReserva">
                <label>Laboratório:</label>
                <select name="lab_id" id="reservaLab" required>
                    <option value="">Selecione um laboratório</option>
                    <?php foreach ($laboratorios as $lab): ?>
                        <option value="<?= $lab['id'] ?>"><?= htmlspecialchars($lab['nome']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Data:</label>
                <input type="date" name="data" id="reservaData" required>

                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label>Início:</label>
                        <input type="time" name="hora_inicio" id="reservaInicio" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Fim:</label>
                        <input type="time" name="hora_fim" id="reservaFim" required>
                    </div>
                </div>

                <label>Descrição / Disciplina:</label>
                <textarea name="desc" id="reservaDesc" placeholder="Ex: Aula de Banco de Dados"></textarea>

                <button type="submit" class="btn" style="width: 100%; margin-top: 15px;">Confirmar Reserva</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>