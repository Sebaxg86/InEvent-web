document.addEventListener('DOMContentLoaded', () => {
    // Elementos comunes
    const totalPriceDisplay = document.getElementById('total-price');
    const payBtn = document.getElementById('proceed-payment');
    const grid = document.getElementById('seats-grid'); // Solo para eventos con asientos numerados
    const spinnerContainer = document.querySelector('.custom-spinner'); // Solo para eventos sin asientos numerados

    // Variables globales para compartir datos con otros archivos
    window.selectedSeats = [];
    window.ticketQuantity = 1;
    window.totalPrice = 0;

    // Verificar si el evento tiene asientos numerados
    const usesSeats = !!grid;

    // Función para actualizar el total del precio
    function updateTotalPrice(price) {
        totalPriceDisplay.textContent = price.toFixed(2) + ' MXN';
        window.totalPrice = price; // Actualizar el total global
    }

    // Variables para eventos con asientos numerados
    if (usesSeats) {
        const seats = document.querySelectorAll('.seat:not(.sold)');
        const selectedSeatsList = document.getElementById('selected-seats-list');
        const seatPrice = parseFloat(grid.dataset.price || 0);

        // Función para actualizar el panel de resumen en eventos con asientos numerados
        function updateSelectionInfo() {
            selectedSeatsList.textContent = window.selectedSeats.length
                ? window.selectedSeats.join(', ')
                : '—';
            const total = window.selectedSeats.length * seatPrice;
            updateTotalPrice(total);
            payBtn.disabled = window.selectedSeats.length === 0;
        }

        // Manejo de selección de asientos
        seats.forEach(seat => {
            seat.addEventListener('click', () => {
                const seatLabel = seat.getAttribute('data-seat');
                if (window.selectedSeats.includes(seatLabel)) {
                    // Deseleccionar asiento
                    window.selectedSeats = window.selectedSeats.filter(s => s !== seatLabel);
                    seat.classList.remove('selected');
                } else {
                    // Seleccionar asiento
                    window.selectedSeats.push(seatLabel);
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

        // Función para actualizar el panel de resumen en eventos sin asientos numerados
        function updateTicketInfo() {
            ticketQuantityDisplay.textContent = window.ticketQuantity;
            const total = window.ticketQuantity * seatPrice;
            updateTotalPrice(total);
            payBtn.disabled = window.ticketQuantity === 0;
        }

        // Disminuir cantidad
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', () => {
                if (window.ticketQuantity > 1) {
                    window.ticketQuantity--;
                    updateTicketInfo();
                }
            });
        }

        // Aumentar cantidad
        if (increaseBtn) {
            increaseBtn.addEventListener('click', () => {
                if (window.ticketQuantity < totalSeats) {
                    window.ticketQuantity++;
                    updateTicketInfo();
                }
            });
        }

        // Inicializar el resumen
        updateTicketInfo();
    }
});