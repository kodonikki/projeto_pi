<?php
// get_reservas.php
include 'config.php';

$data = $_GET['data'] ?? '';

if (!$data) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT * FROM reservas WHERE data_reserva = :data ORDER BY hora_inicio ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['data' => $data]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reservas);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>