<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

// Lógica de Deletar Reserva (Existente)
if (isset($_POST['deletar_id'])) {
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
    $stmt->execute([$_POST['deletar_id']]);
    $mensagem = "Reserva excluída!";
}

$labs = $pdo->query("SELECT * FROM laboratorios ORDER BY nome ASC")->fetchAll();

$stmt = $pdo->query("
    SELECT r.id, r.data_reserva, r.hora_inicio, r.hora_fim, r.descricao, 
           u.nome AS usuario_nome, l.nome AS lab_nome 
    FROM reservas r
    JOIN usuarios u ON r.usuario_id = u.id
    JOIN laboratorios l ON r.laboratorio_id = l.id
    ORDER BY r.data_reserva DESC, r.hora_inicio ASC
");
$todas_reservas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - ETEC</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .admin-card { 
            background: white; padding: 20px; border-radius: 12px; 
            border-top: 5px solid var(--vermelho-etec); box-shadow: var(--shadow);
        }
        .lab-list { margin-top: 15px; max-height: 200px; overflow-y: auto; }
        .lab-item { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 8px; border-bottom: 1px solid #eee; font-size: 0.9em;
        }
        .btn-small-del { color: #d32f2f; text-decoration: none; font-weight: bold; padding: 2px 5px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #eee; text-align: left; font-size: 0.85em; }
        th { background: var(--azul-escuro); color: white; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="main-container" style="flex-direction: column; display: block;">
        
        <h2 style="color: var(--azul-escuro); margin-bottom: 20px;">Gerenciamento do Sistema</h2>

        <?php 
            if(isset($_GET['msg'])){
                if($_GET['msg'] == 'lab_add') echo "<p style='color:green'>Laboratório adicionado!</p>";
                if($_GET['msg'] == 'lab_del') echo "<p style='color:green'>Laboratório removido!</p>";
                if($_GET['msg'] == 'erro_lab_vinculado') echo "<p style='color:red'>Erro: Este lab possui reservas e não pode ser excluído!</p>";
            }
        ?>

        <div class="admin-grid">
            <div class="admin-card">
                <h3>Laboratórios</h3>
                <form action="processar_admin.php" method="POST" style="display: flex; gap: 5px;">
                    <input type="hidden" name="acao" value="add_lab">
                    <input type="text" name="nome_lab" placeholder="Novo Lab..." required style="flex: 1;">
                    <button type="submit" class="btn" style="padding: 5px 10px;">+</button>
                </form>

                <div class="lab-list">
                    <?php foreach($labs as $l): ?>
                        <div class="lab-item">
                            <span><?= htmlspecialchars($l['nome']) ?></span>
                            <a href="processar_admin.php?remover_lab=<?= $l['id'] ?>" 
                               class="btn-small-del" 
                               onclick="return confirm('Excluir este laboratório?')">×</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="admin-card">
                <h3>Adicionar Lista</h3>
                <form action="processar_admin.php" method="POST">
                    <input type="hidden" name="acao" value="bulk_insert">
                    <div id="corpoBulk">
                        <div class="linha-reserva" style="display: flex; gap: 5px; margin-bottom: 5px;">
                            <select name="lab_id[]" required style="width: 40%;">
                                <?php foreach($labs as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= $l['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="date" name="data[]" required style="width: 35%;">
                            <input type="time" name="hora_inicio[]" required style="width: 25%;">
                        </div>
                    </div>
                    <button type="button" class="btn" style="background:#666; font-size: 0.7em; margin: 5px 0;" onclick="adicionarLinha()">+ Linha</button>
                    <button type="submit" class="btn" style="width: 100%;">Salvar</button>
                </form>
            </div>

            <div class="admin-card">
                <h3>Copiar Semana</h3>
                <form action="processar_admin.php" method="POST">
                    <input type="hidden" name="acao" value="clonar_semana">
                    <input type="date" name="origem_inicio" title="De Segunda-feira" required>
                    <div style="text-align: center; margin: 5px 0;">⬇ copiar para ⬇</div>
                    <input type="date" name="destino_inicio" title="Para Segunda-feira" required>
                    <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Clonar</button>
                </form>
            </div>
        </div>

        <div class="content-box">
            <h3>Todas as Reservas</h3>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Lab</th>
                        <th>Horário</th>
                        <th>Professor</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todas_reservas as $res): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($res['data_reserva'])) ?></td>
                            <td><strong><?= htmlspecialchars($res['lab_nome']) ?></strong></td>
                            <td><?= substr($res['hora_inicio'],0,5) ?> - <?= substr($res['hora_fim'],0,5) ?></td>
                            <td><?= htmlspecialchars($res['usuario_nome']) ?></td>
                            <td>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="deletar_id" value="<?= $res['id'] ?>">
                                    <button type="submit" class="btn-delete">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function adicionarLinha() {
            const corpo = document.getElementById('corpoBulk');
            const novaLinha = corpo.children[0].cloneNode(true);
            novaLinha.querySelectorAll('input').forEach(i => i.value = "");
            corpo.appendChild(novaLinha);
        }
    </script>
</body>
</html>