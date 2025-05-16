document.addEventListener('DOMContentLoaded', () => {
    // ====== Common Elements ======
    const totalPriceDisplay = document.getElementById('total-price');
    const payBtn = document.getElementById('proceed-payment');
    const grid = document.getElementById('seats-grid');               // For events with numbered seats
    const spinnerContainer = document.querySelector('.custom-spinner'); // For events without numbered seats

    // ====== Global Variables (shared across files) ======
    window.selectedSeats = [];
    window.ticketQuantity = 1;
    window.totalPrice = 0;

    // ====== Check if Event Uses Numbered Seats ======
    const usesSeats = !!grid;

    // ====== Function to Update Total Price Display ======
    function updateTotalPrice(price) {
        totalPriceDisplay.textContent = price.toFixed(2) + ' MXN';
        window.totalPrice = price; // Update global total price
    }

    // ====== Code for Events with Numbered Seats ======
    if (usesSeats) {
        const seats = document.querySelectorAll('.seat:not(.sold)');
        const selectedSeatsList = document.getElementById('selected-seats-list');
        const seatPrice = parseFloat(grid.dataset.price || 0);

        // ====== Function to Update Selection Summary ======
        function updateSelectionInfo() {
            selectedSeatsList.textContent = window.selectedSeats.length
                ? window.selectedSeats.join(', ')
                : '—';
            const total = window.selectedSeats.length * seatPrice;
            updateTotalPrice(total);
            payBtn.disabled = window.selectedSeats.length === 0;
        }

        // ====== Handle Seat Selection/Deselection ======
        seats.forEach(seat => {
            seat.addEventListener('click', () => {
                const seatLabel = seat.getAttribute('data-seat');
                if (window.selectedSeats.includes(seatLabel)) {
                    // ====== Deselect Seat ======
                    window.selectedSeats = window.selectedSeats.filter(s => s !== seatLabel);
                    seat.classList.remove('selected');
                } else {
                    // ====== Select Seat ======
                    window.selectedSeats.push(seatLabel);
                    seat.classList.add('selected');
                }
                updateSelectionInfo();
            });
        });

        // ====== Initialize Selection Summary ======
        updateSelectionInfo();
    }

    // ====== Code for Events without Numbered Seats ======
    if (!usesSeats) {
        const decreaseBtn = document.getElementById('decrease-btn');
        const increaseBtn = document.getElementById('increase-btn');
        const ticketQuantityDisplay = document.getElementById('ticket-quantity-display');
        const seatPrice = parseFloat(spinnerContainer?.dataset.price || 0);
        const totalSeats = parseInt(spinnerContainer?.dataset.totalSeats || 0, 10); // Maximum available tickets

        // ====== Function to Update Ticket Summary ======
        function updateTicketInfo() {
            ticketQuantityDisplay.textContent = window.ticketQuantity;
            const total = window.ticketQuantity * seatPrice;
            updateTotalPrice(total);
            payBtn.disabled = window.ticketQuantity === 0;
        }

        // ====== Decrease Ticket Quantity ======
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', () => {
                if (window.ticketQuantity > 1) {
                    window.ticketQuantity--;
                    updateTicketInfo();
                }
            });
        }

        // ====== Increase Ticket Quantity ======
        if (increaseBtn) {
            increaseBtn.addEventListener('click', () => {
                if (window.ticketQuantity < totalSeats) {
                    window.ticketQuantity++;
                    updateTicketInfo();
                }
            });
        }

        // ====== Initialize Ticket Summary ======
        updateTicketInfo();
    }
});