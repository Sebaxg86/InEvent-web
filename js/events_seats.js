document.addEventListener('DOMContentLoaded', () => {
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsDisplay = document.getElementById('selected-seats');
    const totalPriceDisplay = document.getElementById('total-price');
    const grid      = document.getElementById('seats-grid');
    const seatPrice = grid ? parseFloat(grid.dataset.price) : 0;
    const payBtn    = document.getElementById('proceed-payment');
    let selectedSeats = [];

    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            if (seat.classList.contains('sold')) {
                return; // already taken
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
        selectedSeatsDisplay.textContent = selectedSeats.length ? selectedSeats.join(', ') : '—';
        totalPriceDisplay.textContent = (selectedSeats.length * seatPrice).toFixed(2) + ' MXN';
        if (payBtn) {
            payBtn.disabled = selectedSeats.length === 0;
        }
    }
});
