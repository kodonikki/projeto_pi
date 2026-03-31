// script.js

async function showSchedule(date) {
    const sidebar = document.getElementById('schedule-sidebar');
    const list = document.getElementById('schedule-list');
    const dateTitle = document.getElementById('selected-date');

    // 1. Formata a exibição da data no título (ex: 31/03/2026)
    const dataBr = date.split('-').reverse().join('/');
    dateTitle.innerText = "Reservas: " + dataBr;

    // 2. Limpa a lista e mostra que está carregando
    list.innerHTML = "<p style='color: #8892b0;'>Buscando reservas...</p>";
    
    // 3. Adiciona a classe active para a barra lateral deslizar para dentro
    sidebar.classList.add('active');

    try {
        // 4. Faz a requisição ao PHP passando a data
        const response = await fetch(`get_reservas.php?data=${date}`);
        const reservas = await response.json();

        // 5. Se não houver nada no banco para esse dia
        if (reservas.length === 0) {
            list.innerHTML = "<p>Nenhuma reserva para este dia.</p>";
            return;
        }

        // 6. Se houver, monta o HTML dinamicamente
        list.innerHTML = reservas.map(res => `
            <div class="event-item">
                <div style="font-size: 0.85rem; color: var(--primary); margin-bottom: 5px;">
                    <strong>${res.hora_inicio.substring(0,5)} - ${res.hora_fim.substring(0,5)}</strong>
                </div>
                <div style="font-weight: bold; margin-bottom: 3px;">${res.ambiente}</div>
                <div style="font-size: 0.9rem; opacity: 0.8;">Prof. ${res.professor}</div>
            </div>
        `).join('');

    } catch (error) {
        console.error("Erro:", error);
        list.innerHTML = "<p>Erro ao conectar com o servidor.</p>";
    }
}

function closeSidebar() {
    document.getElementById('schedule-sidebar').classList.remove('active');
}