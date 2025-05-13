document.addEventListener('DOMContentLoaded', () => {
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsList = document.getElementById('selected-seats-list');
    const totalPriceDisplay = document.getElementById('total-price');
    const grid = document.getElementById('seats-grid');
    const seatPrice = grid ? parseFloat(grid.dataset.price) : 0;
    const payBtn = document.getElementById('proceed-payment');
    let selectedSeats = [];

    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            if (seat.classList.contains('sold')) {
                return;
            }
            const seatCode = seat.getAttribute('data-seat');
            if (selectedSeats.includes(seatCode)) {
                selectedSeats = selectedSeats.filter(seat => seat !== seatCode);
                seat.classList.remove('selected');
            } else {
                selectedSeats.push(seatCode);
                seat.classList.add('selected');
            }
            updateSelectionInfo();
        });
    });

    function updateSelectionInfo() {
        // Actualiza la lista de asientos seleccionados
        selectedSeatsList.innerHTML = selectedSeats.length
            ? selectedSeats.map(seat => `<li>${seat}</li>`).join('')
            : '<li>—</li>';

        // Actualiza el precio total
        totalPriceDisplay.textContent = (selectedSeats.length * seatPrice).toFixed(2) + ' MXN';

        // Habilita o deshabilita el botón de pago
        if (payBtn) {
            payBtn.disabled = selectedSeats.length === 0;
        }
    }
});
