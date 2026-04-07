<?php
session_start();
require 'config.php';

// BUSCAR RESERVAS (GET)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['data'])) {
    $data = $_GET['data'];
    
    // Fazemos um JOIN para pegar o nome do usuário que fez a reserva
    $stmt = $pdo->prepare("
        SELECT r.horario, r.descricao, u.nome AS nome_usuario 
        FROM reservas r
        JOIN usuarios u ON r.usuario_id = u.id
        WHERE r.data_reserva = ?
        ORDER BY r.horario ASC
    ");
    $stmt->execute([$data]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($reservas);
    exit;
}

// CADASTRAR NOVA RESERVA (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $desc = $_POST['desc'];

    $stmt = $pdo->prepare("INSERT INTO reservas (usuario_id, data_reserva, horario, descricao) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$usuario_id, $data, $hora, $desc])) {
        echo "Reserva salva com sucesso!";
    } else {
        echo "Erro ao salvar reserva.";
    }
    exit;
}
?>