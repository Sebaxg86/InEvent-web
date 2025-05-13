document.addEventListener('DOMContentLoaded', () => {
    // Elementos comunes
    const totalPriceDisplay = document.getElementById('total-price');
    const payBtn = document.getElementById('proceed-payment');
    const grid = document.getElementById('seats-grid'); // Solo para eventos con asientos numerados
    const spinnerContainer = document.querySelector('.custom-spinner'); // Solo para eventos sin asientos numerados

    // Verificar si el evento tiene asientos numerados
    const usesSeats = !!grid;

    // Variables para eventos con asientos numerados
    if (usesSeats) {
        const seats = document.querySelectorAll('.seat:not(.sold)');
        const selectedSeatsList = document.getElementById('selected-seats-list');
        const seatPrice = parseFloat(grid.dataset.price || 0);
        let selectedSeats = [];

        // Función para actualizar el panel de resumen en eventos con asientos numerados
        function updateSelectionInfo() {
            selectedSeatsList.innerHTML = selectedSeats.length
                ? selectedSeats.map(seat => `<li>${seat}</li>`).join('')
                : '<li>—</li>';
            totalPriceDisplay.textContent = (selectedSeats.length * seatPrice).toFixed(2) + ' MXN';
            payBtn.disabled = selectedSeats.length === 0;
        }

        // Manejo de selección de asientos
        seats.forEach(seat => {
            seat.addEventListener('click', () => {
                const seatLabel = seat.getAttribute('data-seat');
                if (selectedSeats.includes(seatLabel)) {
                    selectedSeats = selectedSeats.filter(s => s !== seatLabel);
                    seat.classList.remove('selected');
                } else {
                    selectedSeats.push(seatLabel);
                    seat.classList.add('selected');
                }
                updateSelectionInfo();
            });
        });

        // Inicializar el resumen
        updateSelectionInfo();
    }

    // Variables para eventos sin asientos numerados
    if (!usesSeats) {
        const decreaseBtn = document.getElementById('decrease-btn');
        const increaseBtn = document.getElementById('increase-btn');
        const ticketQuantityDisplay = document.getElementById('ticket-quantity-display');
        const seatPrice = parseFloat(spinnerContainer?.dataset.price || 0);
        const totalSeats = parseInt(spinnerContainer?.dataset.totalSeats || 0, 10); // Límite máximo de boletos
        let ticketQuantity = 1;

        // Función para actualizar el panel de resumen en eventos sin asientos numerados
        function updateTicketInfo() {
            ticketQuantityDisplay.textContent = ticketQuantity;
            totalPriceDisplay.textContent = (ticketQuantity * seatPrice).toFixed(2) + ' MXN';
            payBtn.disabled = ticketQuantity === 0;
        }

        // Disminuir cantidad
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', () => {
                if (ticketQuantity > 1) {
                    ticketQuantity--;
                    updateTicketInfo();
                }
            });
        }

        // Aumentar cantidad
        if (increaseBtn) {
            increaseBtn.addEventListener('click', () => {
                if (ticketQuantity < totalSeats) {
                    ticketQuantity++;
                    updateTicketInfo();
                }
            });
        }

        // Inicializar el resumen
        updateTicketInfo();
    }
});