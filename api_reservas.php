<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) { die("Acesso negado"); }

// BUSCAR RESERVAS (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['data'])) {
    $data = $_GET['data'];
    $stmt = $pdo->prepare("
        SELECT r.*, u.nome as nome_usuario, l.nome as nome_lab 
        FROM reservas r 
        JOIN usuarios u ON r.usuario_id = u.id 
        JOIN laboratorios l ON r.laboratorio_id = l.id
        WHERE r.data_reserva = ? 
        ORDER BY r.hora_inicio ASC
    ");
    $stmt->execute([$data]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// CRIAR RESERVA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['usuario_id'];
    $lab_id = $_POST['lab_id'];
    $data = $_POST['data'];
    $inicio = $_POST['hora_inicio'];
    $fim = $_POST['hora_fim'];
    $desc = $_POST['desc'];

    if ($inicio >= $fim) {
        die("Erro: O horário de início deve ser menor que o de fim.");
    }

    // LÓGICA DE CONFLITO:
    // Verifica se existe reserva no mesmo lab e data onde:
    // (NovoInicio < ReservaFim) E (NovoFim > ReservaInicio)
    $stmt_check = $pdo->prepare("
        SELECT COUNT(*) FROM reservas 
        WHERE laboratorio_id = ? 
        AND data_reserva = ? 
        AND (hora_inicio < ? AND hora_fim > ?)
    ");
    $stmt_check->execute([$lab_id, $data, $fim, $inicio]);

    if ($stmt_check->fetchColumn() > 0) {
        die("Erro: Este laboratório já está reservado para este horário.");
    }

    $stmt = $pdo->prepare("INSERT INTO reservas (usuario_id, laboratorio_id, data_reserva, hora_inicio, hora_fim, descricao) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $lab_id, $data, $inicio, $fim, $desc])) {
        echo "Reserva realizada com sucesso!";
    } else {
        echo "Erro ao salvar reserva.";
    }
}