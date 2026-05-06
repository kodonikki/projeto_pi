document.addEventListener("DOMContentLoaded", () => {
    // Elementos
    const calendar = document.getElementById("calendar");
    const mesAnoTexto = document.getElementById("mes-ano");
    const dataSelecionadaSpan = document.getElementById("dataSelecionada");
    const listaReservas = document.getElementById("listaReservas");
    const btnPrev = document.getElementById("btnPrevMes");
    const btnNext = document.getElementById("btnNextMes");
    const modal = document.getElementById("modalReserva");
    const btnNovo = document.getElementById("btnNovaReserva");
    const spanClose = document.querySelector(".close");
    const formReserva = document.getElementById("formReserva");

    // --- NOVOS ELEMENTOS PARA VISÃO DE LABS ---
    const btnTrocarVisao = document.getElementById("btnTrocarVisao");
    const labsView = document.getElementById("labs-view");
    let visaoAtual = 'calendario'; 

    // Controle de Data
    let hoje = new Date();
    hoje.setHours(0, 0, 0, 0);
    let mesExibicao = hoje.getMonth();
    let anoExibicao = hoje.getFullYear();

    const dataLimite = new Date();
    dataLimite.setMonth(hoje.getMonth() + 4);

    const nomesMeses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

    // Renderizar Calendário
    function renderizar(mes, ano) {
        calendar.innerHTML = "";
        mesAnoTexto.innerText = `${nomesMeses[mes]} ${ano}`;

        const primeiroDia = new Date(ano, mes, 1).getDay();
        const ultimoDia = new Date(ano, mes + 1, 0).getDate();

        btnPrev.disabled = (ano === hoje.getFullYear() && mes === hoje.getMonth());
        const dataRef = new Date(ano, mes, 1);
        btnNext.disabled = (dataRef.getTime() >= new Date(dataLimite.getFullYear(), dataLimite.getMonth(), 1).getTime());

        for (let i = 0; i < primeiroDia; i++) calendar.appendChild(document.createElement("div"));

        for (let d = 1; d <= ultimoDia; d++) {
            const dayDiv = document.createElement("div");
            dayDiv.classList.add("day");
            dayDiv.innerText = d;

            const dataStr = `${ano}-${String(mes + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const dataObj = new Date(ano, mes, d);

            if (dataObj < hoje) {
                dayDiv.classList.add("dia-passado");
                dayDiv.style.opacity = "0.3";
            } else {
                dayDiv.onclick = () => carregarDia(dataStr);
            }
            calendar.appendChild(dayDiv);
        }
    }

    // --- LOGICA DE ALTERNÂNCIA DE VISÃO ---
    btnTrocarVisao.onclick = () => {
        if (visaoAtual === 'calendario') {
            visaoAtual = 'laboratorios';
            calendar.style.display = "none";
            document.querySelector(".calendar-grid-header").style.display = "none";
            labsView.style.display = "block";
            btnTrocarVisao.innerText = "📅"; 
            renderizarVisaoLabs();
        } else {
            visaoAtual = 'calendario';
            calendar.style.display = "grid";
            document.querySelector(".calendar-grid-header").style.display = "grid";
            labsView.style.display = "none";
            btnTrocarVisao.innerText = "📊";
        }
    };

    // --- FUNÇÃO PARA RENDERIZAR OCUPAÇÃO POR LAB ---
    function renderizarVisaoLabs() {
        const dataAtual = dataSelecionadaSpan.innerText.split('/').reverse().join('-');
        labsView.innerHTML = "<h3>Ocupação dos Laboratórios</h3><p>Carregando...</p>";

        fetch(`api_reservas.php?data=${dataAtual}`)
            .then(r => r.json())
            .then(reservas => {
                labsView.innerHTML = "";
                const labsSelect = document.getElementById("reservaLab");
                const opcoes = Array.from(labsSelect.options).filter(opt => opt.value !== "");

                if (opcoes.length === 0) {
                    labsView.innerHTML = "<p>Nenhum laboratório cadastrado.</p>";
                    return;
                }

                opcoes.forEach(lab => {
                    const reservasDoLab = reservas.filter(r => r.laboratorio_id == lab.value);
                    let htmlReservas = "";
                    
                    if (reservasDoLab.length === 0) {
                        htmlReservas = `<span style="color: #999; font-style: italic;">Disponível o dia todo</span>`;
                    } else {
                        reservasDoLab.forEach(res => {
                            htmlReservas += `
                                <div class="reserva-item-lab" style="background: #fdf2f2; border-left: 3px solid var(--vermelho-etec); padding: 5px 10px; margin-bottom: 5px; font-size: 0.9em;">
                                    <strong>${res.hora_inicio.substring(0,5)} - ${res.hora_fim.substring(0,5)}</strong> 
                                    | Prof. ${res.nome_usuario}
                                </div>
                            `;
                        });
                    }

                    labsView.innerHTML += `
                        <div class="lab-card" style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                            <h4 style="margin: 0 0 10px 0; color: var(--azul-escuro); border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                ${lab.text}
                            </h4>
                            ${htmlReservas}
                        </div>
                    `;
                });
            });
    }

    // Buscar reservas do dia via AJAX
    window.carregarDia = (data) => {
        dataSelecionadaSpan.innerText = data.split('-').reverse().join('/');
        
        // Se estiver na visão de labs, atualiza ela ao clicar no dia
        if (visaoAtual === 'laboratorios') renderizarVisaoLabs();

        listaReservas.innerHTML = "Carregando...";

        fetch(`api_reservas.php?data=${data}`)
            .then(r => r.json())
            .then(reservas => {
                listaReservas.innerHTML = "";
                if (reservas.length === 0) {
                    listaReservas.innerHTML = "<p>Sem reservas para este dia.</p>";
                    return;
                }
                reservas.forEach(res => {
                    let botaoExcluir = "";
                    if (res.usuario_id == USUARIO_LOGADO_ID) {
                        botaoExcluir = `<button onclick="excluirMinhaReserva(${res.id})" class="btn-delete-small">Apagar</button>`;
                    }

                    listaReservas.innerHTML += `
                        <div class="reserva-item">
                            <strong style="color:var(--vermelho-etec)">${res.nome_lab}</strong> ${botaoExcluir}<br>
                            <span>${res.hora_inicio.substring(0,5)} - ${res.hora_fim.substring(0,5)}</span><br>
                            <small>Prof: ${res.nome_usuario}</small>
                        </div>
                    `;
                });
            });
    };

    // Função para disparar a exclusão
    window.excluirMinhaReserva = (id) => {
        if (confirm("Deseja realmente apagar sua reserva?")) {
            const fd = new FormData();
            fd.append('acao', 'deletar');
            fd.append('id', id);

            fetch("api_reservas.php", { method: "POST", body: fd })
                .then(r => r.text())
                .then(res => {
                    alert(res);
                    const dataAtual = dataSelecionadaSpan.innerText.split('/').reverse().join('-');
                    carregarDia(dataAtual); 
                });
        }
    };

    // Navegação
    btnPrev.onclick = () => { mesExibicao--; if(mesExibicao < 0){ mesExibicao=11; anoExibicao--; } renderizar(mesExibicao, anoExibicao); };
    btnNext.onclick = () => { mesExibicao++; if(mesExibicao > 11){ mesExibicao=0; anoExibicao++; } renderizar(mesExibicao, anoExibicao); };

    // Modal
    btnNovo.onclick = () => modal.style.display = "block";
    spanClose.onclick = () => modal.style.display = "none";

    // Envio de Reserva
    formReserva.onsubmit = (e) => {
        e.preventDefault();
        const fd = new FormData(formReserva);

        fetch("api_reservas.php", { method: "POST", body: fd })
            .then(r => r.text())
            .then(res => {
                alert(res);
                if (!res.includes("Erro")) {
                    modal.style.display = "none";
                    formReserva.reset();
                    renderizar(mesExibicao, anoExibicao); 
                    const dataAtual = dataSelecionadaSpan.innerText.split('/').reverse().join('-');
                    carregarDia(dataAtual);
                }
            });
    };

    renderizar(mesExibicao, anoExibicao);
});