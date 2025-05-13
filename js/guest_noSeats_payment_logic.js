document.addEventListener('DOMContentLoaded', () => {
    const payBtn = document.getElementById('proceed-payment');
    const guestEmailInput = document.getElementById('guest-email');
    const guestEmailConfirmInput = document.getElementById('guest-email-confirm');
    const eventId = new URLSearchParams(window.location.search).get('id'); // Obtener el ID del evento desde la URL

    async function handlePayment() {
        const email = guestEmailInput.value.trim();
        const confirmEmail = guestEmailConfirmInput.value.trim();

        // Validar los datos ingresados
        if (!email || email !== confirmEmail || window.ticketQuantity <= 0) {
            alert('Por favor, verifica los datos ingresados.');
            return;
        }

        try {
            // Enviar los datos al servidor
            const response = await fetch('../app/controllers/guest_noSeats_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email,
                    ticketQuantity: window.ticketQuantity, // Usar la variable global
                    totalPrice: window.totalPrice, // Usar la variable global
                    eventId,
                }),
            });

            const result = await response.json();

            if (response.ok) {
                alert('Compra realizada con éxito. Revisa tu correo para más detalles.');
                window.location.href = `/confirmation.php?order_id=${result.orderId}`;
            } else {
                alert(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error al procesar el pago:', error);
            alert('Ocurrió un error al procesar el pago. Inténtalo de nuevo más tarde.');
        }
    }

    // Asociar el evento de clic al botón de pago
    payBtn.addEventListener('click', handlePayment);
});