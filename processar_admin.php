<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    die("Acesso negado.");
}

// --- AÇÃO: INSERÇÃO EM MASSA ---
if (isset($_POST['acao']) && $_POST['acao'] == 'bulk_insert') {
    $datas = $_POST['data'];
    $horas = $_POST['hora'];
    $descs = $_POST['desc'];
    $usuario_id = $_SESSION['usuario_id']; // Admin reserva em seu nome ou pode-se fixar um ID

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO reservas (usuario_id, data_reserva, horario, descricao) VALUES (?, ?, ?, ?)");
        foreach ($datas as $i => $data) {
            if (!empty($data) && !empty($horas[$i])) {
                $stmt->execute([$usuario_id, $data, $horas[$i], $descs[$i]]);
            }
        }
        $pdo->commit();
        header("Location: admin.php?msg=sucesso_bulk");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: admin.php?msg=erro");
    }
}

// --- AÇÃO: CLONAR SEMANA (COPIAR E COLAR) ---
if (isset($_POST['acao']) && $_POST['acao'] == 'clonar_semana') {
    $inicio_origem = $_POST['origem_inicio']; // Data da segunda-feira original
    $inicio_destino = $_POST['destino_inicio']; // Data da segunda-feira de destino

    // Calcula a diferença em dias entre as semanas
    $date1 = new DateTime($inicio_origem);
    $date2 = new DateTime($inicio_destino);
    $intervalo = $date1->diff($date2);
    $dias_offset = $intervalo->days * ($intervalo->invert ? -1 : 1);

    // Busca todas as reservas da semana de origem (7 dias)
    $fim_origem = date('Y-m-d', strtotime($inicio_origem . ' + 6 days'));
    
    $stmt = $pdo->prepare("SELECT * FROM reservas WHERE data_reserva BETWEEN ? AND ?");
    $stmt->execute([$inicio_origem, $fim_origem]);
    $reservas_origem = $stmt->fetchAll();

    $pdo->beginTransaction();
    try {
        $stmt_ins = $pdo->prepare("INSERT INTO reservas (usuario_id, data_reserva, horario, descricao) VALUES (?, ?, ?, ?)");
        foreach ($reservas_origem as $res) {
            $nova_data = date('Y-m-d', strtotime($res['data_reserva'] . " + $dias_offset days"));
            $stmt_ins->execute([$res['usuario_id'], $nova_data, $res['horario'], $res['descricao']]);
        }
        $pdo->commit();
        header("Location: admin.php?msg=sucesso_clone");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: admin.php?msg=erro");
    }
}

// --- AÇÃO: ADICIONAR LABORATÓRIO ---
if (isset($_POST['acao']) && $_POST['acao'] == 'add_lab') {
    $nome_lab = trim($_POST['nome_lab']);
    if (!empty($nome_lab)) {
        $stmt = $pdo->prepare("INSERT INTO laboratorios (nome) VALUES (?)");
        $stmt->execute([$nome_lab]);
        header("Location: admin.php?msg=lab_add");
    }
}

// --- AÇÃO: REMOVER LABORATÓRIO ---
if (isset($_GET['remover_lab'])) {
    $id_lab = $_GET['remover_lab'];
    
    // Opcional: Verificar se existem reservas vinculadas antes de deletar
    $check = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE laboratorio_id = ?");
    $check->execute([$id_lab]);
    
    if ($check->fetchColumn() > 0) {
        header("Location: admin.php?msg=erro_lab_vinculado");
    } else {
        $stmt = $pdo->prepare("DELETE FROM laboratorios WHERE id = ?");
        $stmt->execute([$id_lab]);
        header("Location: admin.php?msg=lab_del");
    }
}