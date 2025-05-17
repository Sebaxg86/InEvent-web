document.addEventListener('DOMContentLoaded', () => {
    const payBtn = document.getElementById('proceed-payment');
    const guestEmailInput = document.getElementById('guest-email');
    const guestEmailConfirmInput = document.getElementById('guest-email-confirm');
    const eventId = new URLSearchParams(window.location.search).get('id'); // Obtener el ID del evento desde la URL

    // Expresión regular para validar el correo electrónico
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // Función para mostrar el modal de error
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

    // Cerrar el popup cuando se hace clic en la "X"
    document.querySelector('.error-popup-close').addEventListener('click', function () {
        document.getElementById('error-popup').style.display = 'none';
    });
    
    // Cerrar el popup si se hace clic fuera del contenido
    window.addEventListener('click', function (event) {
        const popup = document.getElementById('error-popup');
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    async function handlePayment() {
        const email = guestEmailInput.value.trim();
        const confirmEmail = guestEmailConfirmInput.value.trim();

        // Validar el correo mediante la expresión regular
        if (!emailRegex.test(email)) {
            showError('Por favor, ingresa un correo electrónico válido.');
            return;
        }
        
        // Validar que el correo de confirmación coincida
        if (email !== confirmEmail) {
            showError('Los correos electrónicos no coinciden.');
            return;
        }

        // Validar que se haya seleccionado al menos un asiento
        if (window.selectedSeats.length === 0) {
            showError('Por favor, selecciona al menos un asiento.');
            return;
        }

        try {
            // Enviar los datos al servidor
            const response = await fetch('app/controllers/guest_seated_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email,
                    selectedSeats: window.selectedSeats, // Usar la variable global para los asientos seleccionados
                    totalPrice: window.totalPrice, // Usar la variable global para el precio total
                    eventId,
                }),
            });

            const result = await response.json();

            if (response.ok) {
                // Mostrar la pantalla de espera
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
                // Redirigir a PayPal
                window.location.href = result.approvalUrl;
            } else {
                showError(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error al procesar el pago:', error);
            showError('Ocurrió un error al procesar el pago. Inténtalo de nuevo más tarde.');
        }
    }

    // Asociar el evento de clic al botón de pago
    payBtn.addEventListener('click', handlePayment);
});