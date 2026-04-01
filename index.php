<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CITEC - Sistema de Reservas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
        <section class="calendar-section">
            <div class="calendar-wrap">
                
                <div id="calendar-main">
                    <div class="calendar-header">
                        <h2 id="month-year">Março 2026</h2>
                    </div>

                    <div class="calendar-grid" id="calendar-days">
                        <div class="day-name">Dom</div>
                        <div class="day-name">Seg</div>
                        <div class="day-name">Ter</div>
                        <div class="day-name">Qua</div>
                        <div class="day-name">Qui</div>
                        <div class="day-name">Sex</div>
                        <div class="day-name">Sáb</div>

                        <?php 
                        for($d=1; $d<=31; $d++): 
                            // Formata o dia com zero à esquerda
                            $diaFormatado = str_pad($d, 2, "0", STR_PAD_LEFT);
                            $dataCompleta = "2026-03-" . $diaFormatado;
                        ?>
                            <div class="day" onclick="showSchedule('<?php echo $dataCompleta; ?>')">
                                <?php echo $d; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <aside id="schedule-sidebar" class="sidebar">
                    <button class="close-btn" onclick="closeSidebar()">&times;</button>
                    <h3 id="selected-date" style="margin-bottom: 20px;">Reservas</h3>
                    <div id="schedule-list">
                        <p>Selecione um dia para ver os horários.</p>
                    </div>
                </aside>

            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>