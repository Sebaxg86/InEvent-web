document.addEventListener('DOMContentLoaded', () => {
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsDisplay = document.getElementById('selected-seats');
    const totalPriceDisplay = document.getElementById('total-price');
    let selectedSeats = [];
    const seatPrice = 100; // Example price, you can change this.

    seats.forEach(seat => {
        seat.addEventListener('click', () => {
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
        selectedSeatsDisplay.textContent = selectedSeats.join(', ');
        totalPriceDisplay.textContent = (selectedSeats.length * seatPrice).toFixed(2) + ' MXN';
    }
});
