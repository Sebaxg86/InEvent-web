document.addEventListener('DOMContentLoaded', () => {
    // ====== Retrieve Essential Elements and Event ID ======
    const payBtn = document.getElementById('proceed-payment');
    const guestEmailInput = document.getElementById('guest-email');
    const guestEmailConfirmInput = document.getElementById('guest-email-confirm');
    const eventId = new URLSearchParams(window.location.search).get('id'); // Obtener el ID del evento desde la URL

    // ====== Define Email Validation Regex ======
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // ====== Function: Show Error Popup ======
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

    // ====== Event: Close Error Popup on "X" Click ======
    document.querySelector('.error-popup-close').addEventListener('click', function () {
        document.getElementById('error-popup').style.display = 'none';
    });

    // ====== Event: Close Error Popup When Clicking Outside ======
    window.addEventListener('click', function (event) {
        const popup = document.getElementById('error-popup');
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    // ====== Function: Handle Payment Process ======
    async function handlePayment() {
        // ====== Retrieve and Sanitize Email Inputs ======
        const email = guestEmailInput.value.trim();
        const confirmEmail = guestEmailConfirmInput.value.trim();

        // ====== Validate Email Format ======
        if (!emailRegex.test(email)) {
            showError('Por favor, ingresa un correo electrónico válido.');
            return;
        }

        // ====== Validate Email Confirmation ======
        if (email !== confirmEmail) {
            showError('Los correos electrónicos no coinciden.');
            return;
        }

        // ====== Validate that at Least One Ticket is Selected ======
        if (window.ticketQuantity <= 0) {
            showError('Por favor, selecciona al menos un boleto.');
            return;
        }

        try {
            // ====== Send Payment Data to Server Using Fetch API ======
            const response = await fetch('../app/controllers/guest_noSeats_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email,
                    ticketQuantity: window.ticketQuantity,  // Global variable: ticket quantity
                    totalPrice: window.totalPrice,            // Global variable: total price
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
            // ====== Handle Fetch/Network Errors ======
            console.error('Error al procesar el pago:', error);
            showError('Ocurrió un error al procesar el pago. Inténtalo de nuevo más tarde.');
        }
    }

    // ====== Attach Click Event to Payment Button ======
    payBtn.addEventListener('click', handlePayment);
});