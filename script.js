document.addEventListener("DOMContentLoaded", () => {
    const calendar = document.getElementById("calendar");
    const mesAno = document.getElementById("mes-ano");
    const dataSelecionadaSpan = document.getElementById("dataSelecionada");
    const listaReservas = document.getElementById("listaReservas");

    // Modal elements
    const modal = document.getElementById("modalReserva");
    const btnNovo = document.getElementById("btnNovaReserva");
    const spanClose = document.getElementsByClassName("close")[0];

    // Data atual
    let hoje = new Date();
    let mesAtual = hoje.getMonth();
    let anoAtual = hoje.getFullYear();

    const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

    function renderizarCalendario(mes, ano) {
        calendar.innerHTML = "";
        mesAno.innerText = `${meses[mes]} ${ano}`;

        let primeiroDia = new Date(ano, mes, 1).getDay();
        let diasNoMes = new Date(ano, mes + 1, 0).getDate();

        // Dias vazios do mês anterior
        for (let i = 0; i < primeiroDia; i++) {
            let emptyDiv = document.createElement("div");
            calendar.appendChild(emptyDiv);
        }

        // Preencher os dias do mês
        for (let dia = 1; dia <= diasNoMes; dia++) {
            let dayDiv = document.createElement("div");
            dayDiv.classList.add("day");
            dayDiv.innerText = dia;
            
            // Formatando data para YYYY-MM-DD
            let dataFormatada = `${ano}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
            
            dayDiv.onclick = () => carregarReservas(dataFormatada);
            calendar.appendChild(dayDiv);
        }
    }

    // Carregar Reservas do Dia Clicado via AJAX (Fetch API)
    function carregarReservas(data) {
        dataSelecionadaSpan.innerText = data;
        listaReservas.innerHTML = "Carregando...";

        fetch(`api_reservas.php?data=${data}`)
            .then(response => response.json())
            .then(dados => {
                listaReservas.innerHTML = "";
                if (dados.length === 0) {
                    listaReservas.innerHTML = "<p>Nenhuma reserva para este dia.</p>";
                    return;
                }
                dados.forEach(reserva => {
                    listaReservas.innerHTML += `
                        <div class="reserva-item">
                            <strong>${reserva.horario}</strong> - ${reserva.nome_usuario}<br>
                            <small>${reserva.descricao}</small>
                        </div>
                    `;
                });
            });
    }

    // Eventos do Modal
    btnNovo.onclick = () => modal.style.display = "block";
    spanClose.onclick = () => modal.style.display = "none";
    window.onclick = (event) => { if (event.target == modal) modal.style.display = "none"; }

    // Enviar formulário de nova reserva via AJAX
    document.getElementById("formReserva").addEventListener("submit", function(e) {
        e.preventDefault();
        
        let formData = new FormData();
        formData.append("data", document.getElementById("reservaData").value);
        formData.append("hora", document.getElementById("reservaHora").value);
        formData.append("desc", document.getElementById("reservaDesc").value);

        fetch("api_reservas.php", {
            method: "POST",
            body: formData
        }).then(response => response.text())
        .then(msg => {
            alert(msg);
            modal.style.display = "none";
            // Recarrega as reservas se o dia selecionado for o mesmo que acabou de receber a reserva
            if (dataSelecionadaSpan.innerText === document.getElementById("reservaData").value) {
                carregarReservas(document.getElementById("reservaData").value);
            }
        });
    });

    renderizarCalendario(mesAtual, anoAtual);
});