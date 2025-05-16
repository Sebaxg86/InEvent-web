document.addEventListener('DOMContentLoaded', () => {
    // ====== Retrieve Payment Button and Event ID ======
    const payBtn = document.getElementById('proceed-payment');
    const eventId = new URLSearchParams(window.location.search).get('id'); // Obtener el ID del evento desde la URL

    // ====== Function to Display Error Popup ======
    function showError(message) {
        const errorPopup = document.getElementById('error-popup');
        const errorMessage = document.getElementById('error-message');
        if (errorPopup && errorMessage) {
            errorMessage.innerText = message;
            errorPopup.style.display = 'block';
        } else {
            console.error('Error popup elements not found.');
        }
    }

    // ====== Close Error Popup on "X" Click ======
    document.querySelector('.error-popup-close').addEventListener('click', function () {
        document.getElementById('error-popup').style.display = 'none';
    });

    // ====== Close Error Popup When Clicking Outside the Popup ======
    window.addEventListener('click', function (event) {
        const popup = document.getElementById('error-popup');
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    // ====== Handle Payment Process Using Fetch API ======
    async function handlePayment() {
        // ====== Validate that at Least One Seat is Selected ======
        if (window.selectedSeats.length === 0) {
            showError('Por favor, selecciona al menos un asiento.');
            return;
        }

        try {
            // ====== Send Payment Data to Server ======
            const response = await fetch('../app/controllers/client_seated_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    selectedSeats: window.selectedSeats,  // Global variable: selected seats
                    totalPrice: window.totalPrice,          // Global variable: total price
                    eventId,
                }),
            });

            // ====== Process Server Response ======
            const result = await response.json();
            if (response.ok) {
                // ====== Display Waiting Screen and Redirect to PayPal ======
                document.body.innerHTML = `
                    <div style="text-align: center; margin-top: 20%;">
                        <h2>Redirecting to PayPal</h2>
                        <div style="margin: 20px auto; width: 50px; height: 50px; border: 5px solid #ccc; border-top: 5px solid #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    </div>
                    <style>
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                `;
                window.location.href = result.approvalUrl;
            } else {
                showError(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error al procesar el pago:', error);
            showError('Ocurrió un error al procesar el pago. Inténtalo de nuevo más tarde.');
        }
    }

    // ====== Attach Click Event to Payment Button ======
    payBtn.addEventListener('click', handlePayment);
});